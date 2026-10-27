<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use App\Support\BlockPresets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Those credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return response()->json(['data' => $request->user()]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:profiles,username'],
            'preset' => ['nullable', Rule::in(BlockPresets::keys())],
        ]);

        $username = strtolower($validated['username']);
        $preset = $validated['preset'] ?? 'job_seeker';

        $profile = DB::transaction(function () use ($validated, $username, $preset) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $profile = Profile::create([
                'user_id' => $user->id,
                'username' => $username,
                'display_name' => $validated['name'],
                'template' => 'minimal',
                'preset' => $preset,
                'is_published' => true,
            ]);

            foreach (BlockPresets::blocks($preset) as $position => $block) {
                $profile->blocks()->create([
                    'type' => $block['type'],
                    'position' => $position,
                    'is_visible' => true,
                    'data' => $block['data'],
                ]);
            }

            return $profile;
        });

        Auth::login($profile->user);
        $request->session()->regenerate();

        return response()->json([
            'data' => [
                'user' => $profile->user,
                'profile' => new ProfileResource($profile->load('blocks')),
            ],
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['data' => null]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()]);
    }
}
