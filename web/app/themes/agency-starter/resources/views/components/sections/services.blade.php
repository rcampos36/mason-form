@php
  $eyebrow = \App\Acf\Content::text('eyebrow', $section);
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $cards = \App\Acf\Content::serviceCards($section);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
@endphp

@if (is_front_page() && ($eyebrow !== '' || $heading !== '' || $text !== '' || $cards))
  <section class="home-approach site-section" data-reveal="line">
    <div class="home-approach-head">
      <div class="home-approach-label">
        @if ($eyebrow !== '')
          <p class="home-approach-kicker">{{ $eyebrow }}</p>
        @endif
        @if (($sectionNumber ?? '') !== '')
          <p class="home-approach-index">[{{ $sectionNumber }}]</p>
        @endif
      </div>
      <div class="home-approach-copy">
        @if ($heading !== '')
          <h2 class="home-approach-heading">{{ $heading }}</h2>
        @endif
        @if ($text !== '')
          <p class="home-approach-lead">{{ $text }}</p>
        @endif
      </div>
    </div>

    @if ($cards)
      <div class="home-approach-table">
        <div class="home-approach-labels">
          <span>{{ __('No.', 'sage') }}</span>
          <span>{{ __('Stage', 'sage') }}</span>
          <span>{{ __('Deliverables', 'sage') }}</span>
          <span>{{ __('Duration', 'sage') }}</span>
          <span>{{ __('Team', 'sage') }}</span>
          <span></span>
        </div>
        @foreach ($cards as $index => $card)
          @php($detail = trim((string) ($card['detail'] ?? '')))
          @php($number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT))
          @if ($detail !== '')
            <details class="home-approach-item" name="home-approach-{{ (int) ($sectionIndex ?? 0) }}">
              <summary class="home-approach-row">
                @include('partials.home-approach-row', ['card' => $card, 'number' => $number, 'openable' => true])
              </summary>
              <div class="home-approach-panel">
                <div class="home-approach-panel-inner">
                  <div class="home-approach-detail entry-content">{!! \App\Acf\Content::rich($detail) !!}</div>
                </div>
              </div>
            </details>
          @else
            <div class="home-approach-item">
              <div class="home-approach-row">
                @include('partials.home-approach-row', ['card' => $card, 'number' => $number, 'openable' => false])
              </div>
            </div>
          @endif
        @endforeach
      </div>
    @endif
  </section>
@elseif ($heading !== '' || $text !== '' || $cards)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :text="$text" :align="$align" :number="$sectionNumber ?? ''" />

      @if ($cards)
        <ol class="list-none border-t border-border">
          @foreach ($cards as $index => $card)
            <li class="grid gap-6 border-b border-border py-8 md:grid-cols-[4.5rem_minmax(0,1fr)] md:items-start">
              <p class="text-nav uppercase text-muted">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
              <div class="grid max-w-content gap-4">
                @php($image = \App\Acf\Content::image($card['image'], 'thumbnail', 'icon', '48px'))
                @if ($image !== '')
                  {!! $image !!}
                @endif

                @if ($card['title'] !== '')
                  <x-heading level="3">
                    @if ($card['url'] !== '')
                      <a href="{{ $card['url'] }}" @if ($card['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $card['title'] }}</a>
                    @else
                      {{ $card['title'] }}
                    @endif
                  </x-heading>
                @endif

                @if ($card['text'] !== '')
                  <p class="text-body text-muted">{{ $card['text'] }}</p>
                @endif
              </div>
            </li>
          @endforeach
        </ol>
      @endif
    </div>
  </x-section>
@endif
