<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /editor',
            'Disallow: /login',
            '',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function index(): Response
    {
        $profiles = Profile::query()
            ->where('is_published', true)
            ->orderByDesc('updated_at')
            ->get(['username', 'updated_at']);

        return response()
            ->view('sitemap', ['profiles' => $profiles])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
