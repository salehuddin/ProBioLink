<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $profile->display_name }} — CV</title>
    <style>
        @page { margin: 18mm 16mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1b2430;
        }
        .name { font-size: 24pt; font-weight: bold; letter-spacing: -0.5pt; color: #0d1620; }
        .headline { font-size: 11pt; color: #3d4c5c; margin-top: 2pt; }
        .meta { font-size: 8.5pt; color: #6b7a89; margin-top: 4pt; }
        .meta a { color: #1d4ed8; text-decoration: none; }
        .rule { border-top: 1px solid #d8dee6; margin: 12pt 0 10pt; }
        h2 {
            font-size: 8.5pt;
            letter-spacing: 1.4pt;
            text-transform: uppercase;
            color: #64748b;
            margin: 14pt 0 6pt;
        }
        p { margin-bottom: 4pt; }
        .muted { color: #5b6b7b; }
        .job { margin-bottom: 8pt; page-break-inside: avoid; }
        .job .role { font-weight: bold; font-size: 10.5pt; color: #0d1620; }
        .job .where { font-size: 9pt; color: #3d4c5c; }
        .job .period { font-size: 8.5pt; color: #6b7a89; }
        .job p { font-size: 9.5pt; margin-top: 2pt; }
        ul { margin: 0 0 0 12pt; }
        li { font-size: 9.5pt; margin-bottom: 2pt; }
        .tags span {
            display: inline-block;
            border: 1px solid #d8dee6;
            border-radius: 8pt;
            padding: 1pt 6pt;
            font-size: 8.5pt;
            margin: 0 4pt 4pt 0;
            color: #37475a;
        }
        .service { margin-bottom: 6pt; page-break-inside: avoid; }
        .service .title { font-weight: bold; font-size: 10pt; }
        .service .price { color: #1d4ed8; font-size: 9pt; }
        .service p { font-size: 9pt; color: #5b6b7b; }
        .quote { font-style: italic; font-size: 9.5pt; color: #37475a; }
        .attribution { font-size: 8.5pt; color: #6b7a89; margin-top: 2pt; }
        .footer {
            margin-top: 16pt;
            border-top: 1px solid #d8dee6;
            padding-top: 6pt;
            font-size: 8pt;
            color: #8a97a4;
        }
        a { color: #1d4ed8; text-decoration: none; }
    </style>
</head>
<body>
    <div class="name">{{ $profile->display_name }}</div>
    @if ($profile->headline)
        <div class="headline">{{ $profile->headline }}</div>
    @endif

    <div class="meta">
        @if ($profile->location){{ $profile->location }} &nbsp;·&nbsp; @endif
        <a href="{{ route('profile.show', $profile->username) }}">{{ route('profile.show', $profile->username) }}</a>
    </div>

    <div class="rule"></div>

    @foreach ($blocks as $block)
        @switch($block->type)
            @case('bio')
                <h2>{{ $block->data['heading'] ?? 'About' }}</h2>
                <p>{{ $block->data['body'] ?? '' }}</p>
                @break

            @case('experience')
                <h2>{{ $block->data['heading'] ?? 'Experience' }}</h2>
                @foreach ($block->data['items'] ?? [] as $item)
                    <div class="job">
                        <div class="role">{{ $item['role'] ?? '' }}</div>
                        <div class="where">{{ $item['company'] ?? '' }}</div>
                        <div class="period">{{ $item['period'] ?? '' }}</div>
                        @if (! empty($item['summary']))
                            <p>{{ $item['summary'] }}</p>
                        @endif
                    </div>
                @endforeach
                @break

            @case('services')
                <h2>{{ $block->data['heading'] ?? 'Services' }}</h2>
                @foreach ($block->data['services'] ?? [] as $service)
                    <div class="service">
                        <div class="title">
                            {{ $service['name'] ?? '' }}
                            @if (! empty($service['price']))
                                <span class="price"> — {{ $service['price'] }}</span>
                            @endif
                        </div>
                        @if (! empty($service['description']))
                            <p>{{ $service['description'] }}</p>
                        @endif
                    </div>
                @endforeach
                @break

            @case('skills')
                <h2>{{ $block->data['heading'] ?? 'Skills' }}</h2>
                <div class="tags">
                    @foreach ($block->data['tags'] ?? [] as $tag)
                        <span>{{ $tag }}</span>
                    @endforeach
                </div>
                @break

            @case('links')
                <h2>{{ $block->data['heading'] ?? 'Links' }}</h2>
                <ul>
                    @foreach ($block->data['links'] ?? [] as $link)
                        <li>{{ $link['label'] ?? '' }} — {{ $link['url'] ?? '' }}</li>
                    @endforeach
                </ul>
                @break

            @case('testimonial')
                <h2>Testimonial</h2>
                <div class="quote">“{{ $block->data['quote'] ?? '' }}”</div>
                <div class="attribution">
                    {{ $block->data['author'] ?? '' }}@if (! empty($block->data['role'])) — {{ $block->data['role'] }}@endif
                </div>
                @break
        @endswitch
    @endforeach

    <div class="footer">
        Exported from ProBioLink · {{ now()->format('j F Y') }}
    </div>
</body>
</html>
