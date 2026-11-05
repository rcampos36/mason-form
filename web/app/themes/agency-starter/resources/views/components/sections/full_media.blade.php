@php
  $type = ($section['media_type'] ?? '') === 'video' ? 'video' : 'image';
  $image = $type === 'image' ? \App\Acf\Content::image($section['image'] ?? null, 'full', 'content', '100vw') : '';
  $video = $type === 'video' ? \App\Acf\Content::fileUrl($section['video'] ?? null) : '';
  $caption = \App\Acf\Content::text('caption', $section);
@endphp

@if ($image !== '' || $video !== '' || $caption !== '')
  <section class="{{ \App\Design::classes('layout', 'bleed') }} {{ \App\Design::classes('tone', 'default') }}">
    <figure>
      <div class="relative overflow-hidden bg-surface {{ \App\Acf\Content::variant($section['aspect'] ?? null, 'aspect') }}">
        @if ($video !== '')
          <video class="absolute inset-0 {{ \App\Design::classes('image', 'content') }}" controls playsinline>
            <source src="{{ $video }}" type="{{ \App\Acf\Content::fileMime($section['video'] ?? null) }}">
          </video>
        @elseif ($image !== '')
          <div class="absolute inset-0">{!! $image !!}</div>
        @endif
      </div>

      @if ($caption !== '')
        <figcaption class="mx-auto w-full max-w-wide px-6 py-4 text-caption text-muted md:px-10">{{ $caption }}</figcaption>
      @endif
    </figure>
  </section>
@endif
