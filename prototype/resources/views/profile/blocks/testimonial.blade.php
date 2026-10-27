<figure class="space-y-3">
    <blockquote class="text-sm leading-relaxed {{ $theme['bodyText'] }}">
        “{{ $block->data['quote'] ?? '' }}”
    </blockquote>
    <figcaption class="text-xs {{ $theme['mutedText'] }}">
        {{ $block->data['author'] ?? '' }}@if (! empty($block->data['role'])) — {{ $block->data['role'] }}@endif
    </figcaption>
</figure>
