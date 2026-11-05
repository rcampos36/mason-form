@props([
    'variant' => 'default',
])

<span {{ $attributes->merge(['class' => \App\Design::classes('tag', $variant)]) }}>
  {{ $slot }}
</span>
