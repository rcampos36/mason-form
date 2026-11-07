@props([
    'heading' => '',
    'text' => '',
    'eyebrow' => '',
    'html' => '',
    'align' => 'left',
    'level' => 2,
    'size' => null,
    'number' => '',
])

@php($level = (int) $level)

@if ($number !== '' || $eyebrow !== '' || $heading !== '' || $text !== '' || $html !== '')
  <div @class(['grid gap-6', 'mx-auto max-w-content text-center' => $align === 'center'])>
    @if ($number !== '')
      <p class="text-nav uppercase text-muted">{{ $number }}</p>
    @endif

    @if ($eyebrow !== '')
      <p class="{{ \App\Design::classes('tag', 'default') }}">{{ $eyebrow }}</p>
    @endif

    @if ($heading !== '')
      <x-heading :level="$level" :size="$size ?? ($level === 1 ? 'display' : null)" :data-reveal="$level === 1 ? null : 'heading'">{{ $heading }}</x-heading>
    @endif

    @if ($html !== '')
      <div class="entry-content max-w-content {{ \App\Design::classes('type', 'lead') }} text-muted">{!! $html !!}</div>
    @elseif ($text !== '')
      <x-type class="max-w-content text-muted" size="lead">{{ $text }}</x-type>
    @endif
  </div>
@endif
