<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($profiles as $profile)
    <url>
        <loc>{{ route('profile.show', $profile->username) }}</loc>
        <lastmod>{{ $profile->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
    </url>
@endforeach
</urlset>
