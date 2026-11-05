@props([
    'heading' => '',
    'text' => '',
    'eyebrow' => '',
    'html' => '',
    'align' => 'left',
    'level' => 2,
])

@if ($eyebrow !== '' || $heading !== '' || $text !== '' || $html !== '')
  <div class="{{ \App\Design::classes('stack', is_string($align) ? $align : null) }}">
    @if ($eyebrow !== '')
      <p class="{{ \App\Design::classes('tag', 'default') }}">{{ $eyebrow }}</p>
    @endif

    @if ($heading !== '')
      <x-heading :level="$level" :size="$level === 1 ? 'display' : null">{{ $heading }}</x-heading>
    @endif

    @if ($html !== '')
      <div class="entry-content text-body text-muted">{!! $html !!}</div>
    @elseif ($text !== '')
      <p class="text-body text-muted">{{ $text }}</p>
    @endif
  </div>
@endif
