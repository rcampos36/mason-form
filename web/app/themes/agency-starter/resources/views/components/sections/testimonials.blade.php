@php
  $heading = \App\Acf\Content::text('heading', $section);
  $quotes = \App\Acf\Content::rows($section['testimonials'] ?? null);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
@endphp

@if ($heading !== '' || $quotes)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :align="$align" />

      @if ($quotes)
        <ul class="{{ \App\Design::classes('columns', '2') }} list-none">
          @foreach ($quotes as $quote)
            @continue(\App\Acf\Content::text('quote', $quote) === '')
            <li>
              <figure class="{{ \App\Design::classes('card', 'default') }}">
                <blockquote>
                  <p class="text-h4 text-foreground">{{ \App\Acf\Content::text('quote', $quote) }}</p>
                </blockquote>
                <figcaption class="flex items-center gap-4">
                  @php($photo = \App\Acf\Content::image($quote['image'] ?? null, 'thumbnail', 'avatar', '64px'))
                  @if ($photo !== '')
                    {!! $photo !!}
                  @endif
                  <div>
                    @if (\App\Acf\Content::text('name', $quote) !== '')
                      <cite class="text-body not-italic text-foreground">{{ \App\Acf\Content::text('name', $quote) }}</cite>
                    @endif
                    @php($meta = collect([\App\Acf\Content::text('role', $quote), \App\Acf\Content::text('company', $quote)])->filter()->implode(', '))
                    @if ($meta !== '')
                      <p class="text-small text-muted">{{ $meta }}</p>
                    @endif
                  </div>
                </figcaption>
              </figure>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
