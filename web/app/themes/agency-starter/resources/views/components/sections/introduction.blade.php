@php
  $heading = \App\Acf\Content::text('heading', $section);
  $html = \App\Acf\Content::rich($section['text'] ?? null);
  $image = \App\Acf\Content::image($section['image'] ?? null, 'large', 'portrait', '(min-width: 768px) 40vw, 100vw');
  $layout = is_string($section['layout'] ?? null) ? $section['layout'] : 'stacked';
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
@endphp

@if ($heading !== '' || $html !== '' || $image !== '')
  <x-section layout="contained" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="{{ \App\Acf\Content::variant($layout, 'intro') }}">
      <x-section-intro :heading="$heading" :html="$html" :align="$align" />

      @if ($image !== '')
        <x-image @class([\App\Acf\Content::variant($layout, 'position')])>{!! $image !!}</x-image>
      @endif
    </div>
  </x-section>
@endif
