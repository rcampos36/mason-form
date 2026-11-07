@php
  $heading = \App\Acf\Content::text('heading', $section);
  $people = \App\Acf\Content::people($section);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
@endphp

@if ($heading !== '' || $people)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :align="$align" :number="$sectionNumber ?? ''" />

      @if ($people)
        <ul class="{{ \App\Design::classes('columns', '3') }} list-none">
          @foreach ($people as $person)
            <li>
              <article class="grid gap-6">
                @php($photo = \App\Acf\Content::image($person['image'], 'large', 'portrait', '(min-width: 1024px) 30vw, 100vw'))
                @if ($photo !== '')
                  <x-image ratio="portrait">{!! $photo !!}</x-image>
                @endif

                @if ($person['name'] !== '')
                  <x-heading level="3">{{ $person['name'] }}</x-heading>
                @endif

                @if ($person['role'] !== '')
                  <p class="{{ \App\Design::classes('tag', 'default') }}">{{ $person['role'] }}</p>
                @endif

                @if ($person['biography'] !== '')
                  <p class="text-body text-muted">{!! nl2br(e($person['biography'])) !!}</p>
                @endif

                @if ($person['links'])
                  <ul class="flex flex-wrap gap-x-6 gap-y-2">
                    @foreach ($person['links'] as $link)
                      <li>
                        <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </article>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
