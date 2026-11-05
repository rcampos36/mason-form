@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $features = \App\Acf\Content::rows($section['features'] ?? null);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
@endphp

@if ($heading !== '' || $text !== '' || $features)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :text="$text" :align="$align" />

      @if ($features)
        <ul class="{{ \App\Acf\Content::variant($section['columns'] ?? null, 'columns') }} list-none">
          @foreach ($features as $feature)
            @php($link = \App\Acf\Content::link($feature['link'] ?? null))
            <li>
              <x-card>
                @php($image = \App\Acf\Content::image($feature['image'] ?? null, 'thumbnail', 'icon', '48px'))
                @if ($image !== '')
                  {!! $image !!}
                @endif

                @if (\App\Acf\Content::text('heading', $feature) !== '')
                  <x-heading level="3">
                    @if ($link)
                      <a href="{{ $link['url'] }}" @if ($link['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>
                        {{ \App\Acf\Content::text('heading', $feature) }}
                      </a>
                    @else
                      {{ \App\Acf\Content::text('heading', $feature) }}
                    @endif
                  </x-heading>
                @endif

                @if (\App\Acf\Content::text('text', $feature) !== '')
                  <p class="text-body text-muted">{{ \App\Acf\Content::text('text', $feature) }}</p>
                @endif
              </x-card>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
