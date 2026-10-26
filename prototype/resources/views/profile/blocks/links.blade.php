<div class="space-y-3">
    <h3 class="text-xs font-medium tracking-[0.2em] uppercase {{ $theme['labelText'] }}">
        {{ $block->data['heading'] ?? 'Links' }}
    </h3>
    <ul class="space-y-2">
        @foreach ($block->data['links'] ?? [] as $link)
            @php
                $raw = $link['url'] ?? '#';
                $safe = preg_match('#^(https?:|mailto:|tel:)#i', $raw) ? $raw : '#';
            @endphp
            <li>
                <a
                    href="{{ $safe }}"
                    rel="noreferrer noopener"
                    class="block {{ $theme['linkCard'] }}"
                >{{ $link['label'] ?? $safe }}</a>
            </li>
        @endforeach
    </ul>
</div>
