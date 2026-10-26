<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use App\Support\BlockTemplates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockApiTest extends TestCase
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
        return Profile::create(array_merge([
            'user_id' => $this->user->id,
            'username' => 'amir',
            'display_name' => 'Amiruddin',
            'is_published' => true,
        ], $overrides));
    }

    public function test_it_adds_a_block_with_type_defaults(): void
    {
        $profile = $this->profile();

        $response = $this->postJson('/api/profiles/amir/blocks', ['type' => 'links']);

        $response->assertOk()
            ->assertJsonPath('data.blocks.0.type', 'links')
            ->assertJsonPath('data.blocks.0.position', 0);

        $this->assertSame(
            BlockTemplates::defaults('links'),
            $profile->blocks()->first()->data,
        );
    }

    public function test_it_appends_new_blocks_after_existing_ones(): void
    {
        $profile = $this->profile();
        $profile->blocks()->create([
            'type' => 'hero',
            'position' => 0,
            'is_visible' => true,
            'data' => [],
        ]);

        $this->postJson('/api/profiles/amir/blocks', ['type' => 'bio'])
            ->assertOk()
            ->assertJsonPath('data.blocks.1.type', 'bio')
            ->assertJsonPath('data.blocks.1.position', 1);
    }

    public function test_it_rejects_unsafe_link_urls(): void
    {
        $profile = $this->profile();
        $block = $profile->blocks()->create([
            'type' => 'links',
            'position' => 0,
            'is_visible' => true,
            'data' => ['heading' => 'Find me', 'links' => []],
        ]);

        $this->patchJson("/api/profiles/amir/blocks/{$block->id}", [
            'data' => [
                'heading' => 'Find me',
                'links' => [['label' => 'Bad', 'url' => 'javascript:alert(1)']],
            ],
        ])->assertStatus(422)
            ->assertJsonValidationErrors('data.links.0.url');
    }

    public function test_it_rejects_oversized_content(): void
    {
        $profile = $this->profile();
        $block = $profile->blocks()->create([
            'type' => 'bio',
            'position' => 0,
            'is_visible' => true,
            'data' => ['heading' => 'About'],
        ]);

        $this->patchJson("/api/profiles/amir/blocks/{$block->id}", [
            'data' => ['heading' => 'About', 'body' => str_repeat('a', 2001)],
        ])->assertStatus(422)
            ->assertJsonValidationErrors('data.body');
    }

    public function test_it_accepts_mailto_and_relative_links(): void
    {
        $profile = $this->profile();
        $block = $profile->blocks()->create([
            'type' => 'links',
            'position' => 0,
            'is_visible' => true,
            'data' => ['heading' => 'Find me', 'links' => []],
        ]);

        $this->patchJson("/api/profiles/amir/blocks/{$block->id}", [
            'data' => [
                'heading' => 'Find me',
                'links' => [
                    ['label' => 'Email', 'url' => 'mailto:amir@example.com'],
                    ['label' => 'CV', 'url' => '/amir-cv.pdf'],
                ],
            ],
        ])->assertOk();
    }

    public function test_it_rejects_unknown_block_types(): void
    {
        $this->profile();

        $this->postJson('/api/profiles/amir/blocks', ['type' => 'shopping-cart'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('type');
    }

    public function test_it_updates_block_content(): void
    {
        $profile = $this->profile();
        $block = $profile->blocks()->create([
            'type' => 'bio',
            'position' => 0,
            'is_visible' => true,
            'data' => ['heading' => 'About'],
        ]);

        $this->patchJson("/api/profiles/amir/blocks/{$block->id}", [
            'data' => ['heading' => 'About', 'body' => 'New copy'],
        ])->assertOk()
            ->assertJsonPath('data.blocks.0.data.body', 'New copy');

        $this->assertSame('New copy', $block->fresh()->data['body']);
    }

    public function test_it_deletes_a_block_and_reindexes_the_rest(): void
    {
        $profile = $this->profile();
        $first = $profile->blocks()->create(['type' => 'hero', 'position' => 0, 'is_visible' => true, 'data' => []]);
        $second = $profile->blocks()->create(['type' => 'bio', 'position' => 1, 'is_visible' => true, 'data' => []]);
        $third = $profile->blocks()->create(['type' => 'links', 'position' => 2, 'is_visible' => true, 'data' => []]);

        $response = $this->deleteJson("/api/profiles/amir/blocks/{$first->id}");

        $response->assertOk()
            ->assertJsonPath('data.blocks.0.id', $second->id)
            ->assertJsonPath('data.blocks.0.position', 0)
            ->assertJsonPath('data.blocks.1.position', 1);

        $this->assertDatabaseMissing('blocks', ['id' => $first->id]);
        $this->assertSame(1, $third->fresh()->position);
    }

    public function test_a_block_from_another_profile_cannot_be_deleted(): void
    {
        $this->profile();
        $other = $this->profile(['username' => 'other']);
        $block = $other->blocks()->create(['type' => 'bio', 'position' => 0, 'is_visible' => true, 'data' => []]);

        $this->deleteJson("/api/profiles/amir/blocks/{$block->id}")->assertNotFound();

        $this->assertDatabaseHas('blocks', ['id' => $block->id]);
    }
}
