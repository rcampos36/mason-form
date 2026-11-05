@props([
    'links' => [],
    'align' => 'left',
])

@if ($links)
  <div class="{{ \App\Design::classes('actions', is_string($align) ? $align : null) }}">
    @foreach ($links as $link)
      <x-button :href="$link['url']" :variant="$link['style'] ?? 'primary'" :target="($link['target'] ?? '') ?: null">
        {{ $link['title'] }}
      </x-button>
    @endforeach
  </div>
@endif
