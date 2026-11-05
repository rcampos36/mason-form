@php
  $heading = \App\Acf\Content::text('heading', $section);
  $postsPage = (int) get_option('page_for_posts');
  $heading = $heading !== '' ? $heading : (is_home() && $postsPage > 0 ? get_the_title($postsPage) : get_the_title());
  $text = \App\Acf\Content::text('text', $section);
  $eyebrow = \App\Acf\Content::text('eyebrow', $section);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
  $video = \App\Acf\Content::fileUrl($section['video'] ?? null);
  $poster = \App\Acf\Content::imageUrl($section['image'] ?? null, 'full');
  $image = \App\Acf\Content::image($section['image'] ?? null, 'full', $video !== '' ? 'content' : 'cover', '100vw', 'eager');
  $links = \App\Acf\Content::pairedLinks($section);
@endphp

<section @class([
    \App\Design::classes('hero', 'shell'),
    \App\Design::classes('tone', $section['section_background'] ?? null),
    \App\Design::classes('height', $section['min_height'] ?? null),
    \App\Design::classes('align', $align),
])>
  @if ($video !== '')
    <video
      class="{{ \App\Design::classes('hero', 'video') }}"
      autoplay
      muted
      loop
      playsinline
      @if ($poster !== '') poster="{{ $poster }}" @endif
    >
      <source src="{{ $video }}" type="{{ \App\Acf\Content::fileMime($section['video'] ?? null) }}">
    </video>
  @endif

  @if ($image !== '')
    @if ($video !== '')
      <div class="{{ \App\Design::classes('hero', 'imageFallback') }}">{!! $image !!}</div>
    @else
      {!! $image !!}
    @endif
  @endif

  @if ($image !== '' || $video !== '')
    <div class="{{ \App\Design::classes('hero', 'shade') }}"></div>
  @endif

  <div class="{{ \App\Design::classes('hero', 'content') }}">
    <x-section-intro :eyebrow="$eyebrow" :heading="$heading" :text="$text" :align="$align" :level="1" />
    <x-actions :links="$links" :align="$align" />
  </div>
</section>
