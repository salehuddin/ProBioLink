<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    private function profile(array $overrides = []): Profile
    {
        return Profile::create(array_merge([
            'username' => 'amir',
            'display_name' => 'Amiruddin',
            'is_published' => true,
        ], $overrides));
    }

    public function test_a_visitor_can_send_an_inquiry(): void
    {
        $profile = $this->profile();

        $response = $this->post('/u/amir/inquire', [
            'name' => 'Dana Ruiz',
            'email' => 'dana@example.com',
            'service' => 'Product audit',
            'message' => 'We need help with onboarding.',
        ]);

        $response->assertRedirect()->assertSessionHas('inquiry_sent', true);

        $this->assertDatabaseHas('inquiries', [
            'profile_id' => $profile->id,
            'name' => 'Dana Ruiz',
            'email' => 'dana@example.com',
            'service' => 'Product audit',
        ]);
    }

    public function test_an_inquiry_requires_a_message_and_valid_email(): void
    {
        $this->profile();

        $this->post('/u/amir/inquire', [
            'name' => 'Dana',
            'email' => 'not-an-email',
        ])->assertSessionHasErrors(['email', 'message']);
    }

    public function test_the_honeypot_is_dropped_silently(): void
    {
        $this->profile();

        $this->post('/u/amir/inquire', [
            'name' => 'Spam Bot',
            'email' => 'bot@example.com',
            'message' => 'Buy my service',
            'website' => 'http://spam.example',
        ])->assertRedirect()->assertSessionHas('inquiry_sent', true);

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_unpublished_profiles_cannot_receive_inquiries(): void
    {
        $this->profile(['is_published' => false]);

        $this->post('/u/amir/inquire', [
            'name' => 'Dana',
            'email' => 'dana@example.com',
            'message' => 'Hello',
        ])->assertNotFound();
    }

    public function test_the_owner_can_read_their_inbox(): void
    {
        $owner = User::factory()->create();
        $profile = $this->profile(['user_id' => $owner->id]);
        $profile->inquiries()->create([
            'name' => 'Dana Ruiz',
            'email' => 'dana@example.com',
            'message' => 'We need help.',
        ]);

        $this->actingAs($owner, 'sanctum')
            ->getJson('/api/profiles/amir/inquiries')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Dana Ruiz');
    }

    public function test_a_stranger_cannot_read_the_inbox(): void
    {
        $profile = $this->profile(['user_id' => User::factory()->create()->id]);
        $profile->inquiries()->create([
            'name' => 'Dana',
            'email' => 'dana@example.com',
            'message' => 'Private.',
        ]);

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/profiles/amir/inquiries')
            ->assertForbidden();
    }
}
