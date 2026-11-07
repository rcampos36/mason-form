@php
  $heading = \App\Acf\Content::text('heading', $section);
  $stats = array_values(array_filter(\App\Acf\Content::rows($section['statistics'] ?? null), function ($stat) {
      return \App\Acf\Content::text('number', $stat) !== '' || \App\Acf\Content::text('label', $stat) !== '';
  }));
@endphp

@if ($heading !== '' || $stats)
  <x-section layout="wide" :tone="$section['section_background'] ?? null">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :number="$sectionNumber ?? ''" />

      @if ($stats)
        <dl class="{{ \App\Design::classes('layout', 'stats') }}">
          @foreach ($stats as $stat)
            <div class="grid gap-3 border-t border-border pt-6">
              @if (\App\Acf\Content::text('number', $stat) !== '')
                <dt class="{{ \App\Design::classes('heading', '1') }}">{{ \App\Acf\Content::text('number', $stat) }}</dt>
              @endif
              <dd class="grid gap-2">
                @if (\App\Acf\Content::text('label', $stat) !== '')
                  <p class="text-body text-foreground">{{ \App\Acf\Content::text('label', $stat) }}</p>
                @endif
                @if (\App\Acf\Content::text('text', $stat) !== '')
                  <p class="text-small text-muted">{{ \App\Acf\Content::text('text', $stat) }}</p>
                @endif
              </dd>
            </div>
          @endforeach
        </dl>
      @endif
    </div>
  </x-section>
@endif
