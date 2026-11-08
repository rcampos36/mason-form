@php
  $eyebrow = \App\Acf\Content::text('eyebrow', $section);
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $projects = \App\Acf\Content::projects($section);
  $viewAll = \App\Acf\Content::link($section['view_all'] ?? null);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
  $layout = ($section['layout'] ?? '') === 'list' ? 'list' : 'grid';

  if ($viewAll !== null) {
      $viewAll['style'] = 'secondary';
  }
@endphp

@if (is_front_page() && ($eyebrow !== '' || $heading !== '' || $projects))
  <section class="home-work site-section" data-reveal="line">
    @if ($eyebrow !== '' || $heading !== '' || ($sectionNumber ?? '') !== '' || $viewAll)
      <div class="home-work-intro">
        @if ($eyebrow !== '')
          <p class="home-work-kicker">{{ $eyebrow }}</p>
        @endif
        @if (($sectionNumber ?? '') !== '')
          <p class="home-work-index">[{{ $sectionNumber }}]</p>
        @endif
        <div class="home-work-lead">
          @if ($heading !== '')
            <h2 class="home-work-heading">{{ $heading }}</h2>
          @endif
          @if ($viewAll)
            <a class="home-work-all" href="{{ $viewAll['url'] }}" @if ($viewAll['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>
              <span class="home-work-all-label">{{ $viewAll['title'] }} <span aria-hidden="true">→</span></span>
            </a>
          @endif
        </div>
      </div>
    @endif

    @if ($projects)
      <ul class="home-work-list">
        @foreach ($projects as $project)
          @php($projectId = $project->ID)
          @php($caption = trim((string) $project->post_excerpt))
          @php($type = \App\Acf\Content::text('project_type', $projectId))
          @php($facts = array_filter([
              __('Location', 'sage') => \App\Acf\Content::text('project_location', $projectId),
              __('Year', 'sage') => \App\Acf\Content::text('project_year', $projectId),
              __('Area', 'sage') => \App\Acf\Content::text('project_area', $projectId),
              __('Type', 'sage') => $type !== '' ? $type : (\App\Acf\Content::termNames($projectId, 'project_category')[0] ?? ''),
          ]))
          @php($image = \App\Acf\Content::image((int) get_post_thumbnail_id($projectId), 'large', 'content', '(min-width: 64rem) 52vw, 100vw'))
          <li>
            <article class="home-work-project" data-reveal="rise">
              @if ($image !== '')
                <a class="home-work-media" href="{{ get_permalink($project) }}">
                  {!! $image !!}
                  <span class="home-work-caption">
                    <span class="home-work-caption-name">{{ $project->post_title }}</span>
                    @if ($caption !== '')
                      <span class="home-work-caption-text">{{ $caption }}</span>
                    @endif
                  </span>
                </a>
              @endif
              <div class="home-work-copy">
                <h3 class="home-work-title"><a href="{{ get_permalink($project) }}">{{ $project->post_title }}</a></h3>
                <div class="home-work-foot">
                  @if ($facts !== [])
                    <dl class="home-work-facts">
                      @foreach (array_chunk($facts, 2, true) as $row)
                        <div class="home-work-fact-row">
                          @foreach ($row as $label => $value)
                            <div class="home-work-fact">
                              <dt>{{ $label }}</dt>
                              <dd>{{ $value }}</dd>
                            </div>
                          @endforeach
                        </div>
                      @endforeach
                    </dl>
                  @endif
                  <a class="home-work-more" href="{{ get_permalink($project) }}">{{ __('View project', 'sage') }}</a>
                </div>
              </div>
            </article>
          </li>
        @endforeach
      </ul>
    @endif
  </section>
@elseif (is_page_template('template-work.blade.php') || $heading !== '' || $text !== '' || $projects)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      @unless (is_page_template('template-work.blade.php'))
      <x-section-intro :heading="$heading" :text="$text" :align="$align" :number="$sectionNumber ?? ''" />

      @if (\App\Acf\Content::flag($section, 'show_filters'))
        @php($current = \App\Acf\Content::requestedTerm('project_category'))
        @php($terms = get_terms(['taxonomy' => 'project_category', 'hide_empty' => true]))
        @if (! is_wp_error($terms) && $terms)
          <nav aria-label="{{ __('Project categories', 'sage') }}">
            <ul class="flex flex-wrap gap-x-8 gap-y-3">
              <li>
                <a class="{{ \App\Design::classes('link', $current === '' ? 'filterCurrent' : 'filter') }}" href="{{ get_permalink() }}" @if ($current === '') aria-current="page" @endif>{{ __('All', 'sage') }}</a>
              </li>
              @foreach ($terms as $term)
                <li>
                  <a class="{{ \App\Design::classes('link', $current === $term->slug ? 'filterCurrent' : 'filter') }}" href="{{ add_query_arg('project_category', $term->slug, get_permalink()) }}" @if ($current === $term->slug) aria-current="page" @endif>{{ $term->name }}</a>
                </li>
              @endforeach
            </ul>
          </nav>
        @endif
      @endif
      @endunless

      @if ($projects)
        <ul class="{{ \App\Design::classes('portfolio', $layout) }} list-none">
          @foreach ($projects as $project)
            <li @class(['border-b border-border py-10' => $layout === 'list'])>
              <article>
                <a href="{{ get_permalink($project) }}" @class([
                    \App\Design::classes('link', 'card'),
                    'gap-6',
                    'items-start lg:grid-cols-12 lg:gap-10' => $layout === 'list',
                ])>
                  @php($image = \App\Acf\Content::featuredImage($project->ID, 'landscape'))
                  @if ($image !== '')
                    <div @class(['lg:col-span-7' => $layout === 'list'])>
                      <x-image ratio="landscape">{!! $image !!}</x-image>
                    </div>
                  @endif

                  <div @class(['grid content-start gap-4', 'lg:col-span-5 lg:pt-2' => $layout === 'list'])>
                    <x-heading class="transition-colors duration-300 ease-out group-hover:text-accent group-focus-visible:text-accent motion-reduce:transition-none" level="3">{{ $project->post_title }}</x-heading>

                    @include('partials.project-meta', ['id' => $project->ID])

                    @if (trim(get_the_excerpt($project)) !== '')
                      <p class="max-w-content text-body text-muted">{{ get_the_excerpt($project) }}</p>
                    @endif
                  </div>
                </a>
              </article>
            </li>
          @endforeach
        </ul>
      @endif

      @unless (is_page_template('template-work.blade.php'))
        <x-actions :links="$viewAll ? [$viewAll] : []" :align="$align" />
      @else
        @unless ($projects)
          <p class="work-empty">{{ __('No projects in this category.', 'sage') }}</p>
        @endunless
      @endunless
    </div>
  </x-section>
@endif
