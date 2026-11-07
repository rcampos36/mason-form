@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
  $links = \App\Acf\Content::pairedLinks($section);
@endphp

@if ($heading !== '' || $text !== '' || $links)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-10 border-t border-border pt-12">
      <x-section-intro :heading="$heading" :text="$text" :align="$align" size="display" />
      <x-actions :links="$links" :align="$align" />
    </div>
  </x-section>
@endif
