@php
  $heading = \App\Acf\Content::text('heading', $section);
  $ids = \App\Acf\Content::ids($section['images'] ?? null);
  $lightbox = \App\Acf\Content::flag($section, 'lightbox');
@endphp

@if ($heading !== '' || $ids)
  <x-section layout="wide" :tone="$section['section_background'] ?? null">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" />

      @if ($ids)
        <ul class="{{ \App\Acf\Content::variant($section['columns'] ?? null, 'gallery') }} list-none">
          @foreach ($ids as $id)
            @php($caption = \App\Acf\Content::caption($id))
            <li class="grid gap-3">
              @if ($lightbox)
                <button
                  type="button"
                  class="{{ \App\Design::classes('image', 'frame') }} w-full cursor-pointer"
                  data-lightbox
                  data-src="{{ \App\Acf\Content::imageUrl($id, 'large') }}"
                  data-alt="{{ get_post_meta($id, '_wp_attachment_image_alt', true) }}"
                  data-caption="{{ $caption }}"
                >
                  {!! \App\Acf\Content::image($id, 'large', 'content', '(min-width: 1024px) 30vw, 100vw') !!}
                </button>
              @else
                <x-image>{!! \App\Acf\Content::image($id, 'large', 'content', '(min-width: 1024px) 30vw, 100vw') !!}</x-image>
              @endif

              @if ($caption !== '')
                <p class="{{ \App\Design::classes('tag', 'caption') }}">{{ $caption }}</p>
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
