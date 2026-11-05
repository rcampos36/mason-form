@props([
    'variant' => 'inline',
    'href' => '#',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => \App\Design::classes('link', $variant)]) }}>
  {{ $slot }}
</a>
