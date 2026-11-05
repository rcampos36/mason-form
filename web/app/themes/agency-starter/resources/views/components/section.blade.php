@props([
    'layout' => 'contained',
    'tone' => 'default',
    'align' => 'left',
])

@php
  $classes = trim(implode(' ', array_filter([
      \App\Design::classes('tone', is_string($tone) ? $tone : null),
      \App\Design::classes('layout', is_string($layout) ? $layout : null),
      \App\Design::classes('align', is_string($align) ? $align : null),
  ])));
@endphp

<section {{ $attributes->merge(['class' => $classes]) }}>
  {{ $slot }}
</section>
