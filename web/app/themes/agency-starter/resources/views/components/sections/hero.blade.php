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
  $primary = \App\Acf\Content::link($section['primary_link'] ?? null);
  $secondary = \App\Acf\Content::link($section['secondary_link'] ?? null);
  $name = \App\Acf\Content::nameParts();
  $imageId = \App\Acf\Content::attachmentId($section['image'] ?? null);
  $caption = \App\Acf\Content::text('image_caption', $section);
  $caption = $caption !== '' ? $caption : ($imageId > 0 ? trim((string) wp_get_attachment_caption($imageId)) : '');
  $note = $caption === '' ? '' : $eyebrow;
  $caption = $caption !== '' ? $caption : $eyebrow;
  $copy = $text !== '' ? $text : \App\Acf\Content::description('company_description', 'footer_description');
@endphp

@if (is_front_page() && $video === '')
  <section class="home-hero">
    <h1 class="home-hero-name">
      <span class="home-hero-lead"><span class="home-hero-mask"><span class="home-hero-drop">{{ $name['lead'] }}</span></span></span>
      @if ($name['mark'] !== '')
        <span class="home-hero-amp"><span class="home-hero-mask"><span class="home-hero-drop">{{ $name['mark'] }}</span></span></span>
        <span class="home-hero-rest"><span class="home-hero-mask"><span class="home-hero-drop">{{ $name['rest'] }}</span></span></span>
      @endif
    </h1>

    @if ($name['mark'] !== '')
      <span class="home-hero-amp-fall" aria-hidden="true"><span>{{ $name['mark'] }}</span></span>
    @endif

    @if ($image !== '')
      <figure class="home-hero-photo">
        {!! \App\Acf\Content::image($section['image'] ?? null, 'large', 'content', '(min-width: 64rem) 32vw, 100vw', 'eager') !!}
        @if ($caption !== '')
          <figcaption>{{ $caption }}</figcaption>
        @endif
      </figure>
    @endif

    @if ($copy !== '')
      <p class="home-hero-copy">{{ $copy }}</p>
    @endif

    <div class="home-hero-bar">
      @if ($primary)
        <div class="home-hero-action">
          <x-button class="gap-3" :href="$primary['url']" variant="primary" :target="$primary['target'] ?: null">
            {{ $primary['title'] }}
            <span aria-hidden="true">→</span>
          </x-button>
        </div>
      @endif

      @if ($secondary)
        <a class="home-hero-note no-underline transition-colors duration-300 ease-out hover:text-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent" href="{{ $secondary['url'] }}" @if ($secondary['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $secondary['title'] }}</a>
      @elseif ($note !== '')
        <p class="home-hero-note">{{ $note }}</p>
      @endif
    </div>
  </section>
@else
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
@endif
