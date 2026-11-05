@php
  $heading = \App\Acf\Content::text('heading', $section);
  $logos = array_values(array_filter(\App\Acf\Content::rows($section['logos'] ?? null), function ($logo) {
      return \App\Acf\Content::attachmentId($logo['image'] ?? null) > 0;
  }));
@endphp

@if ($heading !== '' || $logos)
  <x-section layout="wide" :tone="$section['section_background'] ?? null">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" />

      @if ($logos)
        <ul class="{{ \App\Design::classes('layout', 'logos') }} list-none">
          @foreach ($logos as $logo)
            @php($image = \App\Acf\Content::image($logo['image'], 'medium', 'mark', '160px'))
            @php($link = \App\Acf\Content::link($logo['link'] ?? null))
            <li>
              @if ($link)
                <a href="{{ $link['url'] }}" @if ($link['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>
                  {!! $image !!}
                </a>
              @else
                {!! $image !!}
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
