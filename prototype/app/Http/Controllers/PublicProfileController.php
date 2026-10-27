<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Support\Templates;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function show(string $username): View
    {
        $profile = Profile::query()
            ->where('username', $username)
            ->where('is_published', true)
            ->with('blocks')
            ->firstOrFail();

        $blocks = $profile->blocks->where('is_visible', true);
        $theme = Templates::theme($profile->template);
        $description = $profile->headline
            ?: trim(strip_tags((string) $blocks->firstWhere('type', 'bio')?->data['body'] ?? ''));

        $jsonLd = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $profile->display_name,
            'jobTitle' => $profile->headline,
            'description' => $description ?: null,
            'url' => route('profile.show', $profile->username),
            'address' => $profile->location,
        ]);

        return view('profile.show', [
            'profile' => $profile,
            'blocks' => $blocks,
            'theme' => $theme,
            'description' => $description,
            'jsonLd' => $jsonLd,
        ]);
    }

    public function cv(string $username): Response
    {
        $profile = Profile::query()
            ->where('username', $username)
            ->where('is_published', true)
            ->with('blocks')
            ->firstOrFail();

        $pdf = Pdf::loadView('profile.cv', [
            'profile' => $profile,
            'blocks' => $profile->blocks->where('is_visible', true),
        ])->setPaper('a4');

        return $pdf->download("{$profile->username}-cv.pdf");
    }
}
