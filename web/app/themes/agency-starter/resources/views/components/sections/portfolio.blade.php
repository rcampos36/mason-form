@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $projects = \App\Acf\Content::projects($section);
  $viewAll = \App\Acf\Content::link($section['view_all'] ?? null);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';

  if ($viewAll !== null) {
      $viewAll['style'] = 'secondary';
  }
@endphp

@if ($heading !== '' || $text !== '' || $projects)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :text="$text" :align="$align" />

      @if (\App\Acf\Content::flag($section, 'show_filters'))
        @php($current = \App\Acf\Content::requestedTerm('project_category'))
        @php($terms = get_terms(['taxonomy' => 'project_category', 'hide_empty' => true]))
        @if (! is_wp_error($terms) && $terms)
          <nav aria-label="{{ __('Project categories', 'sage') }}">
            <ul class="flex flex-wrap gap-x-6 gap-y-3">
              <li>
                <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ get_permalink() }}" @if ($current === '') aria-current="page" @endif>{{ __('All', 'sage') }}</a>
              </li>
              @foreach ($terms as $term)
                <li>
                  <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ add_query_arg('project_category', $term->slug, get_permalink()) }}" @if ($current === $term->slug) aria-current="page" @endif>{{ $term->name }}</a>
                </li>
              @endforeach
            </ul>
          </nav>
        @endif
      @endif

      @if ($projects)
        <ul class="{{ \App\Acf\Content::variant($section['layout'] ?? null, 'portfolio') }} list-none">
          @foreach ($projects as $project)
            <li>
              <article class="grid gap-6">
                @php($image = \App\Acf\Content::featuredImage($project->ID, 'portrait'))
                @if ($image !== '')
                  <a href="{{ get_permalink($project) }}" class="{{ \App\Design::classes('image', 'frame') }}">{!! $image !!}</a>
                @endif

                <x-heading level="3">
                  <a href="{{ get_permalink($project) }}">{{ $project->post_title }}</a>
                </x-heading>

                @php($client = \App\Acf\Content::text('project_client', $project->ID))
                @php($year = \App\Acf\Content::text('project_year', $project->ID))
                @php($categories = \App\Acf\Content::termNames($project->ID, 'project_category'))
                @if ($client !== '' || $year !== '' || $categories)
                  <p class="{{ \App\Design::classes('tag', 'default') }}">{{ collect([$client, $year, implode(', ', $categories)])->filter()->implode(' / ') }}</p>
                @endif

                @if (trim(get_the_excerpt($project)) !== '')
                  <p class="text-body text-muted">{{ get_the_excerpt($project) }}</p>
                @endif
              </article>
            </li>
          @endforeach
        </ul>
      @endif

      <x-actions :links="$viewAll ? [$viewAll] : []" :align="$align" />
    </div>
  </x-section>
@endif
