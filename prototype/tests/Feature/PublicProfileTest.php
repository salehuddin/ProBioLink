<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    private function publishedProfile(array $overrides = []): Profile
    {
        $profile = Profile::create(array_merge([
            'username' => 'amir',
            'display_name' => 'Amiruddin',
            'headline' => 'Product engineer',
            'location' => 'Kuala Lumpur',
            'template' => 'minimal',
            'is_published' => true,
        ], $overrides));

        $profile->blocks()->create([
            'type' => 'bio',
            'position' => 0,
            'is_visible' => true,
            'data' => ['heading' => 'About', 'body' => 'I build calm interfaces.'],
        ]);

        return $profile;
    }

    public function test_it_renders_profile_content_without_javascript(): void
    {
        $this->publishedProfile();

        $response = $this->get('/u/amir');

        $response->assertOk()
            ->assertSee('Amiruddin')
            ->assertSee('Product engineer')
            ->assertSee('I build calm interfaces.');

        // The content must be in the served HTML, not hydrated later.
        $this->assertStringContainsString('I build calm interfaces.', $response->getContent());
    }

    public function test_it_exposes_meta_tags_for_unfurling(): void
    {
        $this->publishedProfile();

        $response = $this->get('/u/amir');

        $response->assertSee('<meta name="description" content="Product engineer">', false)
            ->assertSee('<meta property="og:title" content="Amiruddin">', false)
            ->assertSee(
                '<link rel="canonical" href="'.route('profile.show', 'amir').'">',
                false,
            );
    }

    public function test_it_emits_person_structured_data(): void
    {
        $this->publishedProfile();

        $response = $this->get('/u/amir');

        $response->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Person"', false);
    }

    public function test_hidden_blocks_are_not_rendered(): void
    {
        $profile = $this->publishedProfile();
        $profile->blocks()->create([
            'type' => 'bio',
            'position' => 1,
            'is_visible' => false,
            'data' => ['heading' => 'Secret', 'body' => 'Hidden copy'],
        ]);

        $this->get('/u/amir')->assertDontSee('Hidden copy');
    }

    public function test_unpublished_profiles_are_not_found(): void
    {
        $this->publishedProfile(['is_published' => false]);

        $this->get('/u/amir')->assertNotFound();
    }

    public function test_it_downloads_the_profile_as_a_pdf_cv(): void
    {
        $this->publishedProfile();

        $response = $this->get('/u/amir/cv.pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_the_cv_of_an_unpublished_profile_is_not_found(): void
    {
        $this->publishedProfile(['is_published' => false]);

        $this->get('/u/amir/cv.pdf')->assertNotFound();
    }

    public function test_profile_content_is_escaped(): void
    {
        $profile = $this->publishedProfile();
        $profile->blocks()->create([
            'type' => 'bio',
            'position' => 1,
            'is_visible' => true,
            'data' => ['heading' => 'About', 'body' => '<script>alert(1)</script>'],
        ]);

        $this->get('/u/amir')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_unsafe_link_schemes_are_neutralised(): void
    {
        $profile = $this->publishedProfile();
        $profile->blocks()->create([
            'type' => 'links',
            'position' => 1,
            'is_visible' => true,
            'data' => [
                'heading' => 'Find me',
                'links' => [
                    ['label' => 'Bad', 'url' => 'javascript:alert(1)'],
                    ['label' => 'Good', 'url' => 'https://example.com'],
                ],
            ],
        ]);

        $html = $this->get('/u/amir')->getContent();

        $this->assertStringNotContainsString('href="javascript:', $html);
        $this->assertStringContainsString('href="https://example.com"', $html);
    }
}
