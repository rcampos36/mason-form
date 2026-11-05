@props([
    'width' => 'page',
])

<div {{ $attributes->merge(['class' => \App\Design::classes('width', $width)]) }}>
  {{ $slot }}
</div>
