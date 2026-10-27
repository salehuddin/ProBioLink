<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $profile->display_name }}{{ $profile->headline ? ' — '.$profile->headline : '' }}</title>
        <meta name="description" content="{{ \Illuminate\Support\Str::limit($description, 155) }}">
        <link rel="canonical" href="{{ route('profile.show', $profile->username) }}">

        <meta property="og:type" content="profile">
        <meta property="og:site_name" content="ProBioLink">
        <meta property="og:title" content="{{ $profile->display_name }}">
        <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($description, 155) }}">
        <meta property="og:url" content="{{ route('profile.show', $profile->username) }}">
        <meta name="twitter:card" content="summary">

        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-[#0b0b0f] text-zinc-100 antialiased">
        <main class="mx-auto w-full max-w-3xl px-5 py-10">
            <article class="mx-auto w-full max-w-xl space-y-4 {{ $theme['shell'] }}">
                <header class="flex flex-col items-center gap-4 pb-2 text-center">
                    @if ($profile->avatar_url)
                        <img
                            src="{{ $profile->avatar_url }}"
                            alt="{{ $profile->display_name }}"
                            class="size-20 rounded-full object-cover ring-2 ring-black/10"
                        >
                    @else
                        <div class="grid size-20 place-items-center rounded-full bg-gradient-to-br text-2xl font-semibold ring-2 ring-black/10 {{ $theme['avatar'] }}">
                            {{ \Illuminate\Support\Str::of($profile->display_name)->explode(' ')->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}
                        </div>
                    @endif

                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">{{ $profile->display_name }}</h1>
                        @if ($profile->headline)
                            <p class="mt-1 text-sm {{ $theme['mutedText'] }}">{{ $profile->headline }}</p>
                        @endif
                        @if ($profile->location)
                            <p class="mt-1 text-xs {{ $theme['mutedText'] }}">{{ $profile->location }}</p>
                        @endif
                    </div>
                </header>

                @foreach ($blocks as $block)
                    <section class="{{ $theme['card'] }}">
                        @includeIf('profile.blocks.'.$block->type)
                    </section>
                @endforeach

                @php
                    $input = 'w-full rounded-lg border bg-transparent px-3 py-2 text-sm outline-none '.$theme['hairline'];
                @endphp
                <section class="{{ $theme['card'] }}" id="contact">
                    <h3 class="text-xs font-medium tracking-[0.2em] uppercase {{ $theme['labelText'] }}">
                        Send an inquiry
                    </h3>

                    @if (session('inquiry_sent'))
                        <p class="mt-3 text-sm {{ $theme['accentText'] }}">
                            Thanks — your message is on its way. You'll get a reply by email.
                        </p>
                    @else
                        <form method="POST" action="{{ route('profile.inquire', $profile->username) }}" class="mt-3 space-y-3">
                            @csrf
                            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" style="display:none" aria-hidden="true">

                            <div class="grid gap-3 sm:grid-cols-2">
                                <input name="name" value="{{ old('name') }}" placeholder="Your name" required class="{{ $input }}" aria-label="Your name">
                                <input name="email" type="email" value="{{ old('email') }}" placeholder="you@email.com" required class="{{ $input }}" aria-label="Your email">
                            </div>
                            <input name="service" value="{{ old('service') }}" placeholder="What do you need? (optional)" class="{{ $input }}" aria-label="What do you need">
                            <textarea name="message" rows="3" placeholder="Tell them what you need" required class="{{ $input }}" aria-label="Message">{{ old('message') }}</textarea>

                            @error('message')
                                <p class="text-xs {{ $theme['accentText'] }}">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="rounded-lg px-4 py-2 text-sm font-medium {{ $theme['accentSurface'] }}">
                                Send inquiry
                            </button>
                        </form>
                    @endif
                </section>

                <footer class="pt-6 text-center text-xs {{ $theme['mutedText'] }}">
                    Made with <a href="{{ url('/') }}" class="underline-offset-4 hover:underline">ProBioLink</a>
                    &nbsp;·&nbsp;
                    <a href="{{ route('profile.cv', $profile->username) }}" class="underline-offset-4 hover:underline">Download CV (PDF)</a>
                </footer>
            </article>
        </main>
    </body>
</html>
