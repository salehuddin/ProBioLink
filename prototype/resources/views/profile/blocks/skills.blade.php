<div class="space-y-3">
    <h3 class="text-xs font-medium tracking-[0.2em] uppercase {{ $theme['labelText'] }}">
        {{ $block->data['heading'] ?? 'Skills' }}
    </h3>
    <ul class="flex flex-wrap gap-2">
        @foreach ($block->data['tags'] ?? [] as $tag)
            <li class="{{ $theme['tag'] }}">{{ $tag }}</li>
        @endforeach
    </ul>
</div>
