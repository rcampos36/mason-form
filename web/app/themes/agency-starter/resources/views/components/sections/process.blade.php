@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $steps = array_values(array_filter(\App\Acf\Content::rows($section['steps'] ?? null), function ($step) {
      return \App\Acf\Content::text('heading', $step) !== '' || \App\Acf\Content::text('text', $step) !== '';
  }));
@endphp

@if ($heading !== '' || $text !== '' || $steps)
  <x-section layout="contained" :tone="$section['section_background'] ?? null">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :text="$text" />

      @if ($steps)
        <ol class="{{ \App\Design::classes('layout', 'steps') }}">
          @foreach ($steps as $index => $step)
            <li class="{{ \App\Design::classes('layout', 'step') }}">
              <p class="{{ \App\Design::classes('tag', 'default') }}">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
              <div class="grid gap-4">
                @if (\App\Acf\Content::text('heading', $step) !== '')
                  <x-heading level="3">{{ \App\Acf\Content::text('heading', $step) }}</x-heading>
                @endif
                @if (\App\Acf\Content::text('text', $step) !== '')
                  <p class="text-body text-muted">{{ \App\Acf\Content::text('text', $step) }}</p>
                @endif
              </div>
              @php($image = \App\Acf\Content::image($step['image'] ?? null, 'medium', 'content', '(min-width: 768px) 16rem, 100vw'))
              @if ($image !== '')
                <x-image>{!! $image !!}</x-image>
              @endif
            </li>
          @endforeach
        </ol>
      @endif
    </div>
  </x-section>
@endif
