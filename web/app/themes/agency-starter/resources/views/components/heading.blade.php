@props([
    'level' => 2,
    'size' => null,
])

@php
  $level = (int) $level;
  $tag = in_array($level, [1, 2, 3, 4, 5, 6], true) ? 'h'.$level : 'h2';
  $size = $size ?? (string) $level;
  $classes = \App\Design::classes('heading', (string) $size);

  if ($level === 1) {
      $classes .= ' motion-rise';
  }
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>
  {{ $slot }}
</{{ $tag }}>
