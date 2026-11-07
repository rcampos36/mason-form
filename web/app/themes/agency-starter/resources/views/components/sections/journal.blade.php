@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $eyebrow = \App\Acf\Content::text('eyebrow', $section);
  $posts = \App\Acf\Content::journal($section);
  $layout = is_string($section['layout'] ?? null) ? $section['layout'] : 'grid';
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
  $journalId = (int) get_option('page_for_posts');
  $journalUrl = $journalId > 0 ? (string) get_permalink($journalId) : '';
@endphp

@if ($layout === 'stack' && ($heading !== '' || $text !== '' || $posts))
  <section class="home-journal site-section" data-journal-stack>
    <div class="home-journal-frame">
      <div class="home-journal-aside">
        @if ($eyebrow !== '')
          <p class="home-journal-kicker">{{ $eyebrow }}</p>
        @endif
      </div>
      @if (! empty($sectionNumber))
        <p class="home-journal-index">[{{ $sectionNumber }}]</p>
      @endif
      <div class="home-journal-main">
        <div class="home-journal-copy">
          @if ($heading !== '')
            <h2 class="home-journal-heading">{{ $heading }}</h2>
          @endif
          @if ($text !== '')
            <p class="home-journal-lead">{{ $text }}</p>
          @endif
          @if ($journalUrl !== '')
            <a class="home-journal-all" href="{{ $journalUrl }}">
              <span class="home-journal-all-label">{{ __('All articles', 'sage') }} <span aria-hidden="true">→</span></span>
            </a>
          @endif
        </div>

        @if ($posts)
          <div class="home-journal-list">
            @foreach ($posts as $index => $post)
                @php
                  $categories = array_values(array_filter(
                      \App\Acf\Content::termNames($post->ID, 'category'),
                      static fn (string $name): bool => strcasecmp($name, 'Uncategorized') !== 0
                  ));
                  $category = $categories[0] ?? '';
                  $date = get_the_date('F j, Y', $post);
                  $excerpt = trim(get_the_excerpt($post));
                  $image = \App\Acf\Content::featuredImage($post->ID, 'landscape');
                @endphp
                <a class="home-journal-card" data-journal-card href="{{ get_permalink($post) }}" style="z-index: {{ (int) $index + 1 }}">
                  @if ($image !== '')
                    <div class="home-journal-media">{!! $image !!}</div>
                  @endif
                  <div class="home-journal-body">
                    @if ($category !== '' || $date !== '')
                      <p class="home-journal-meta">
                        @if ($category !== '')
                          <span class="home-journal-category">{{ $category }}</span>
                        @endif
                        @if ($date !== '')
                          <span class="home-journal-date">{{ $date }}</span>
                        @endif
                      </p>
                    @endif
                    <div class="home-journal-title-row">
                      <h3 class="home-journal-title">{{ $post->post_title }}</h3>
                      <span class="home-journal-read">{{ __('Read', 'sage') }} <span aria-hidden="true">→</span></span>
                    </div>
                    @if ($excerpt !== '')
                      <p class="home-journal-summary">{{ $excerpt }}</p>
                    @endif
                  </div>
                </a>
              @endforeach
          </div>
        @endif
      </div>
    </div>
  </section>
@elseif ($heading !== '' || $text !== '' || $posts)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :text="$text" :align="$align" :number="$sectionNumber ?? ''" />

      @if ($posts)
        <ul class="grid list-none gap-x-8 gap-y-16 border-t border-border pt-10 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($posts as $post)
            <li>
              <article>
                <a class="{{ \App\Design::classes('link', 'card') }} gap-5" href="{{ get_permalink($post) }}">
                  @php($image = \App\Acf\Content::featuredImage($post->ID, 'landscape'))
                  @if ($image !== '')
                    <x-image ratio="landscape">{!! $image !!}</x-image>
                  @endif
                  <p class="text-nav uppercase text-muted">{{ get_the_date('', $post) }}</p>
                  <x-heading class="transition-colors duration-300 ease-out group-hover:text-accent group-focus-visible:text-accent motion-reduce:transition-none" level="3">{{ $post->post_title }}</x-heading>
                  @if (trim(get_the_excerpt($post)) !== '')
                    <p class="text-body text-muted">{{ get_the_excerpt($post) }}</p>
                  @endif
                </a>
              </article>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
