@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
    'target' => null,
])

@php($classes = \App\Design::classes('button', $variant))

@if ($href)
  <a
    href="{{ $href }}"
    @if ($target === '_blank') target="_blank" rel="noopener noreferrer" @endif
    {{ $attributes->merge(['class' => $classes]) }}
  >
    {{ $slot }}
  </a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </button>
@endif
