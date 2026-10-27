<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function profileFor(User $user, string $username = 'amir'): Profile
    {
        return Profile::create([
            'user_id' => $user->id,
            'username' => $username,
            'display_name' => 'Amiruddin',
            'is_published' => true,
        ]);
    }

    private function asBrowser(): static
    {
        // Sanctum only treats a request as stateful when it comes from a frontend domain.
        return $this->withHeader('Referer', config('app.url'));
    }

    public function test_guests_cannot_write(): void
    {
        $profile = $this->profileFor(User::factory()->create());

        $this->patchJson("/api/profiles/{$profile->username}", [
            'display_name' => 'Hijacked',
        ])->assertUnauthorized();

        $this->postJson("/api/profiles/{$profile->username}/blocks", [
            'type' => 'bio',
        ])->assertUnauthorized();
    }

    public function test_guests_can_read_public_profiles(): void
    {
        $profile = $this->profileFor(User::factory()->create());

        $this->getJson("/api/profiles/{$profile->username}")->assertOk();
    }

    public function test_a_non_owner_cannot_edit_a_profile(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $profile = $this->profileFor($owner);

        $this->actingAs($stranger, 'sanctum')
            ->patchJson("/api/profiles/{$profile->username}", [
                'display_name' => 'Hijacked',
            ])
            ->assertForbidden();

        $this->assertSame('Amiruddin', $profile->fresh()->display_name);
    }

    public function test_the_owner_can_edit_their_profile(): void
    {
        $owner = User::factory()->create();
        $profile = $this->profileFor($owner);

        $this->actingAs($owner, 'sanctum')
            ->patchJson("/api/profiles/{$profile->username}", [
                'display_name' => 'Amir U.',
            ])
            ->assertOk()
            ->assertJsonPath('data.display_name', 'Amir U.');
    }

    public function test_a_user_can_sign_in_and_read_their_session(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->asBrowser()
            ->postJson('/login', ['email' => $user->email, 'password' => 'secret-password'])
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->assertAuthenticatedAs($user);
        $this->asBrowser()->getJson('/api/me')->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_bad_credentials_are_rejected(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->asBrowser()
            ->postJson('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_a_user_can_sign_out(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->asBrowser()->postJson('/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertOk();

        $this->asBrowser()->postJson('/logout')->assertOk();

        // The session is gone, so the API no longer recognises the caller.
        $this->asBrowser()->getJson('/api/me')->assertUnauthorized();
    }

    public function test_a_visitor_can_register_and_gets_a_started_profile(): void
    {
        $response = $this->asBrowser()->postJson('/register', [
            'name' => 'Nadia Rahman',
            'username' => 'Nadia-R',
            'email' => 'nadia@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'preset' => 'freelancer',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.profile.username', 'nadia-r')
            ->assertJsonPath('data.profile.preset', 'freelancer');

        $profile = Profile::where('username', 'nadia-r')->firstOrFail();

        $this->assertSame('Nadia Rahman', $profile->display_name);
        $this->assertSame(6, $profile->blocks()->count());
        $this->assertAuthenticated();
    }

    public function test_registration_requires_a_unique_username(): void
    {
        $this->profileFor(User::factory()->create());

        $this->asBrowser()->postJson('/register', [
            'name' => 'Someone Else',
            'username' => 'amir',
            'email' => 'other@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertStatus(422)->assertJsonValidationErrors('username');
    }

    public function test_registration_requires_a_long_enough_password(): void
    {
        $this->asBrowser()->postJson('/register', [
            'name' => 'Short Pass',
            'username' => 'shorty',
            'email' => 'short@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    }

    public function test_the_owner_is_the_only_one_who_can_apply_a_preset(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $profile = $this->profileFor($owner);

        $this->actingAs($stranger, 'sanctum')
            ->postJson("/api/profiles/{$profile->username}/presets", ['preset' => 'freelancer'])
            ->assertForbidden();

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/profiles/{$profile->username}/presets", ['preset' => 'freelancer'])
            ->assertOk();
    }
}
