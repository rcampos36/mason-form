@php
  $eyebrow = \App\Acf\Content::text('eyebrow', $section);
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $quotes = array_values(array_filter(\App\Acf\Content::rows($section['testimonials'] ?? null), function ($quote) {
      return \App\Acf\Content::text('quote', $quote) !== '';
  }));
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
  $rail = ($section['style'] ?? '') === 'rail';
@endphp

@if ($rail && ($eyebrow !== '' || $heading !== '' || $text !== '' || $quotes))
  <section class="home-quotes site-section" data-quote-rail>
    <div class="home-quotes-pin">
      <div class="home-quotes-head">
        <div class="home-quotes-label">
          @if ($eyebrow !== '')
            <p class="home-quotes-kicker">{{ $eyebrow }}</p>
          @endif
          @if (($sectionNumber ?? '') !== '')
            <p class="home-quotes-index">[{{ $sectionNumber }}]</p>
          @endif
        </div>
        <div class="home-quotes-copy">
          @if ($heading !== '')
            <h2 class="home-quotes-heading">{{ $heading }}</h2>
          @endif
          @if ($text !== '')
            <p class="home-quotes-lead">{{ $text }}</p>
          @endif
        </div>
      </div>

      @if ($quotes)
        <div class="home-quotes-list">
          @foreach ($quotes as $index => $quote)
            @php($meta = collect([\App\Acf\Content::text('role', $quote), \App\Acf\Content::text('company', $quote)])->filter()->implode(', '))
            <article class="home-quotes-card" data-quote-card>
              <div class="home-quotes-card-inner">
                <p class="home-quotes-meta">
                  <span class="home-quotes-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                  @if (\App\Acf\Content::text('project', $quote) !== '')
                    <span class="home-quotes-project">{{ \App\Acf\Content::text('project', $quote) }}</span>
                  @endif
                </p>
                <blockquote class="home-quotes-quote">
                  <p>{{ \App\Acf\Content::text('quote', $quote) }}</p>
                </blockquote>
                <footer class="home-quotes-author">
                  @if (\App\Acf\Content::text('name', $quote) !== '')
                    <cite class="home-quotes-name">{{ \App\Acf\Content::text('name', $quote) }}</cite>
                  @endif
                  @if ($meta !== '')
                    <p class="home-quotes-role">{{ $meta }}</p>
                  @endif
                </footer>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  </section>
@elseif ($heading !== '' || $quotes)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :align="$align" :number="$sectionNumber ?? ''" />

      @if ($quotes)
        <ul class="{{ \App\Design::classes('columns', '2') }} list-none">
          @foreach ($quotes as $quote)
            <li>
              <figure class="{{ \App\Design::classes('card', 'default') }}">
                <blockquote>
                  <p class="text-h3 text-foreground">{{ \App\Acf\Content::text('quote', $quote) }}</p>
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
