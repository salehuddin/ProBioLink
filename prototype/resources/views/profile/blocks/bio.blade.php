<div class="space-y-2">
    <h3 class="text-xs font-medium tracking-[0.2em] uppercase {{ $theme['labelText'] }}">
        {{ $block->data['heading'] ?? 'About' }}
    </h3>
    <p class="text-sm leading-relaxed {{ $theme['bodyText'] }}">{{ $block->data['body'] ?? '' }}</p>
</div>
