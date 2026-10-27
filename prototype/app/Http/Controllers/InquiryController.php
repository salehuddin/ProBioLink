<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request, string $username): RedirectResponse
    {
        $profile = Profile::query()
            ->where('username', $username)
            ->where('is_published', true)
            ->firstOrFail();

        // Honeypot: real people never fill this in. Pretend it worked.
        if (filled($request->input('website'))) {
            return back()->with('inquiry_sent', true);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'service' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ], [], [
            'name' => 'your name',
            'email' => 'your email',
            'message' => 'message',
        ]);

        $profile->inquiries()->create($validated);

        return back()->with('inquiry_sent', true);
    }
}
