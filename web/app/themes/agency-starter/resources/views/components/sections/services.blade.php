@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $cards = \App\Acf\Content::serviceCards($section);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
@endphp

@if ($heading !== '' || $text !== '' || $cards)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :text="$text" :align="$align" />

      @if ($cards)
        <ul class="{{ \App\Acf\Content::variant($section['columns'] ?? null, 'columns') }} list-none">
          @foreach ($cards as $card)
            <li>
              <x-card>
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
              </x-card>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
