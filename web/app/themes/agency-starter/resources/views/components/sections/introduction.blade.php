@php
  $heading = \App\Acf\Content::text('heading', $section);
  $html = \App\Acf\Content::rich($section['text'] ?? null);
  $image = \App\Acf\Content::image($section['image'] ?? null, 'large', 'portrait', '(min-width: 768px) 40vw, 100vw');
  $layout = is_string($section['layout'] ?? null) ? $section['layout'] : 'stacked';
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
  $eyebrow = \App\Acf\Content::text('eyebrow', $section);
  $statement = \App\Acf\Content::text('statement', $section);
  $statementRest = \App\Acf\Content::text('statement_rest', $section);
  $link = \App\Acf\Content::link($section['link'] ?? null);
  $figures = array_values(array_filter(\App\Acf\Content::rows($section['figures'] ?? null), function ($row) {
      return \App\Acf\Content::text('number', $row) !== '' || \App\Acf\Content::text('label', $row) !== '';
  }));
  $tags = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', \App\Acf\Content::text('tags', $section)) ?: []), function ($tag) {
      return $tag !== '';
  }));
@endphp

@if (is_front_page() && ($heading !== '' || $html !== '' || $statement !== '' || $figures !== []))
  <section class="home-studio site-section" data-reveal="line">
    <div @class(['home-studio-grid', 'has-figures' => $figures !== []])>
      <div class="home-studio-title">
        @if ($eyebrow !== '')
          <p class="home-studio-kicker">{{ $eyebrow }}</p>
        @endif
        @if ($heading !== '')
          <h2 class="home-studio-heading">{{ $heading }}</h2>
        @endif
      </div>

      <div class="home-studio-intro">
        @if (($sectionNumber ?? '') !== '')
          <p class="home-studio-index">[{{ $sectionNumber }}]</p>
        @endif
        @if ($html !== '')
          <div class="home-studio-copy entry-content">{!! $html !!}</div>
        @endif
      </div>

      @if ($link)
        <a class="home-studio-link" href="{{ $link['url'] }}" @if ($link['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>
          <span class="home-studio-link-label">{{ $link['title'] }} <span aria-hidden="true">→</span></span>
        </a>
      @endif

      @if ($figures !== [])
        <dl class="home-studio-stats">
          @foreach ($figures as $figure)
            @php($figureNumber = \App\Acf\Content::text('number', $figure))
            <div class="home-studio-stat">
              @if (\App\Acf\Content::text('label', $figure) !== '')
                <dt>{{ \App\Acf\Content::text('label', $figure) }}</dt>
              @endif
              @if ($figureNumber !== '')
                <dd @if (preg_match('/\d/', $figureNumber)) data-count="{{ $figureNumber }}" @endif>{{ $figureNumber }}</dd>
              @endif
            </div>
          @endforeach
        </dl>
      @endif

      @if ($statement !== '' || $tags !== [])
        <div class="home-studio-aside">
          @if ($statement !== '')
            <p class="home-studio-statement">
              {{ $statement }}
              @if ($statementRest !== '')
                <span>{{ $statementRest }}</span>
              @endif
            </p>
          @endif
          @if ($tags !== [])
            <div class="home-studio-marquee">
              <div class="home-studio-marquee-track">
                <ul class="home-studio-tags">
                  @foreach ($tags as $tag)
                    <li>{{ $tag }}</li>
                  @endforeach
                </ul>
                <ul class="home-studio-tags" aria-hidden="true">
                  @foreach ($tags as $tag)
                    <li>{{ $tag }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif
        </div>
      @endif
    </div>
  </section>
@elseif ($heading !== '' || $html !== '' || $image !== '')
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="{{ \App\Acf\Content::variant($layout, 'intro') }}">
      <x-section-intro :heading="$heading" :html="$html" :align="$align" size="1" :number="$sectionNumber ?? ''" />

      @if ($image !== '')
        <x-image ratio="landscape" @class([\App\Acf\Content::variant($layout, 'position')])>{!! $image !!}</x-image>
      @endif
    </div>
  </x-section>
@endif
