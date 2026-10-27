<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    private function profile(array $overrides = []): Profile
    {
        $profile = Profile::create(array_merge([
            'user_id' => $this->user->id,
            'username' => 'amir',
            'display_name' => 'Amiruddin',
            'headline' => 'Product engineer',
            'is_published' => true,
        ], $overrides));

        $profile->blocks()->create([
            'type' => 'hero',
            'position' => 0,
            'is_visible' => true,
            'data' => ['heading' => 'Hello'],
        ]);

        return $profile;
    }

    public function test_directory_lists_published_profiles_only(): void
    {
        $this->profile();
        $this->profile(['username' => 'hidden', 'is_published' => false]);

        $response = $this->getJson('/api/profiles');

        $response->assertOk();
        $this->assertSame(['amir'], array_column($response->json('data'), 'username'));
    }

    public function test_it_returns_a_profile_with_ordered_blocks(): void
    {
        $profile = $this->profile();
        $profile->blocks()->create([
            'type' => 'bio',
            'position' => 1,
            'is_visible' => true,
            'data' => ['body' => 'About me'],
        ]);

        $response = $this->getJson('/api/profiles/amir');

        $response->assertOk()
            ->assertJsonPath('data.username', 'amir')
            ->assertJsonPath('data.blocks.0.type', 'hero')
            ->assertJsonPath('data.blocks.1.type', 'bio');
    }

    public function test_unpublished_profiles_are_not_found(): void
    {
        $this->profile(['is_published' => false]);

        $this->getJson('/api/profiles/amir')->assertNotFound();
    }

    public function test_it_saves_order_visibility_and_content_in_one_request(): void
    {
        $profile = $this->profile();
        $hero = $profile->blocks()->first();
        $bio = $profile->blocks()->create([
            'type' => 'bio',
            'position' => 1,
            'is_visible' => true,
            'data' => ['heading' => 'About'],
        ]);

        $response = $this->patchJson('/api/profiles/amir/blocks', [
            'blocks' => [
                [
                    'id' => $bio->id,
                    'position' => 0,
                    'is_visible' => true,
                    'data' => ['heading' => 'About', 'body' => 'Rewritten'],
                ],
                [
                    'id' => $hero->id,
                    'position' => 1,
                    'is_visible' => false,
                ],
            ],
        ]);

        $response->assertOk();

        $this->assertSame($bio->id, $response->json('data.blocks.0.id'));
        $this->assertSame('Rewritten', $response->json('data.blocks.0.data.body'));
        $this->assertFalse($response->json('data.blocks.1.is_visible'));
    }

    public function test_it_updates_profile_details(): void
    {
        $this->profile();

        $this->patchJson('/api/profiles/amir', [
            'display_name' => 'Amir U.',
            'headline' => 'Staff engineer',
            'location' => 'Remote',
        ])->assertOk()
            ->assertJsonPath('data.display_name', 'Amir U.')
            ->assertJsonPath('data.headline', 'Staff engineer');
    }

    public function test_display_name_is_required(): void
    {
        $this->profile();

        $this->patchJson('/api/profiles/amir', ['display_name' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors('display_name');
    }

    public function test_it_applies_a_preset_and_replaces_all_blocks(): void
    {
        $profile = $this->profile();
        $this->assertSame(1, $profile->blocks()->count());

        $response = $this->postJson('/api/profiles/amir/presets', [
            'preset' => 'freelancer',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.preset', 'freelancer')
            ->assertJsonPath('data.blocks.0.type', 'hero')
            ->assertJsonPath('data.blocks.2.type', 'services');

        $this->assertSame(6, $profile->blocks()->count());
        $this->assertSame('freelancer', $profile->fresh()->preset);
    }

    public function test_it_rejects_an_unknown_preset(): void
    {
        $this->profile();

        $this->postJson('/api/profiles/amir/presets', ['preset' => 'astronaut'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('preset');
    }

    public function test_it_switches_template(): void
    {
        $this->profile();

        $this->patchJson('/api/profiles/amir', [
            'display_name' => 'Amiruddin',
            'template' => 'bold',
        ])->assertOk()->assertJsonPath('data.template', 'bold');
    }

    public function test_it_rejects_an_unknown_template(): void
    {
        $this->profile();

        $this->patchJson('/api/profiles/amir', [
            'display_name' => 'Amiruddin',
            'template' => 'neon',
        ])->assertStatus(422)->assertJsonValidationErrors('template');
    }

    public function test_it_validates_block_content_during_a_layout_save(): void
    {
        $profile = $this->profile();
        $hero = $profile->blocks()->first();

        $this->patchJson('/api/profiles/amir/blocks', [
            'blocks' => [
                [
                    'id' => $hero->id,
                    'position' => 0,
                    'is_visible' => true,
                    'data' => ['heading' => str_repeat('x', 121)],
                ],
            ],
        ])->assertStatus(422)
            ->assertJsonValidationErrors('blocks.0.data.heading');
    }

    public function test_it_rejects_blocks_belonging_to_another_profile(): void
    {
        $this->profile();
        $other = $this->profile(['username' => 'someone-else']);
        $foreign = $other->blocks()->first();

        $this->patchJson('/api/profiles/amir/blocks', [
            'blocks' => [
                ['id' => $foreign->id, 'position' => 0, 'is_visible' => true],
            ],
        ])->assertStatus(422);
    }
}
