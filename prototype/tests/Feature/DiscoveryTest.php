<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscoveryTest extends TestCase
{
    use RefreshDatabase;

    private function profile(string $username, bool $published = true): Profile
    {
        return Profile::create([
            'username' => $username,
            'display_name' => 'Amiruddin',
            'is_published' => $published,
        ]);
    }

    public function test_robots_points_at_the_sitemap_and_keeps_the_editor_out(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /editor', false)
            ->assertSee(url('/sitemap.xml'), false);
    }

    public function test_sitemap_lists_only_published_profiles(): void
    {
        $this->profile('amir');
        $this->profile('draft', published: false);

        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('profile.show', 'amir').'</loc>', false)
            ->assertDontSee('draft');
    }

    public function test_sitemap_is_valid_xml(): void
    {
        $this->profile('amir');

        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());

        $this->assertNotFalse($xml);
        $this->assertSame('urlset', $xml->getName());
    }
}
