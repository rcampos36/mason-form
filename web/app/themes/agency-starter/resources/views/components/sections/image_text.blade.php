@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $image = \App\Acf\Content::image($section['image'] ?? null, 'large', 'portrait', '(min-width: 768px) 40vw, 100vw');
  $button = \App\Acf\Content::link($section['button'] ?? null);
  $position = is_string($section['image_position'] ?? null) ? $section['image_position'] : 'right';

  if ($button !== null) {
      $button['style'] = 'primary';
  }
@endphp

@if ($heading !== '' || $text !== '' || $image !== '')
  <x-section layout="split" :tone="$section['section_background'] ?? null">
    <div class="grid content-start gap-8">
      <x-section-intro :heading="$heading" :text="$text" />
      <x-actions :links="$button ? [$button] : []" />
    </div>

    @if ($image !== '')
      <x-image @class([\App\Acf\Content::variant($position, 'position')])>{!! $image !!}</x-image>
    @endif
  </x-section>
@endif
