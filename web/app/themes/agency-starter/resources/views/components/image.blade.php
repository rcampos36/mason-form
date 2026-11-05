@props([
    'src' => null,
    'alt' => '',
    'caption' => null,
])

<figure {{ $attributes->merge(['class' => \App\Design::classes('image', 'frame')]) }}>
  @if ($src)
    <img
      src="{{ $src }}"
      alt="{{ $alt }}"
      class="{{ \App\Design::classes('image', 'media') }}"
    >
  @else
    <div class="{{ \App\Design::classes('image', 'media') }} [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
      {{ $slot }}
    </div>
  @endif

  @if ($caption)
    <figcaption class="px-1 {{ \App\Design::classes('tag', 'caption') }}">
      {{ $caption }}
    </figcaption>
  @endif
</figure>
