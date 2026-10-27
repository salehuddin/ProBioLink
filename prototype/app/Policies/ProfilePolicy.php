<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;

class ProfilePolicy
{
    /**
     * Only the owner may edit a profile or its blocks.
     * Profiles without an owner cannot be edited at all.
     */
    public function update(User $user, Profile $profile): bool
    {
        return $profile->user_id !== null && $profile->user_id === $user->id;
    }
}
