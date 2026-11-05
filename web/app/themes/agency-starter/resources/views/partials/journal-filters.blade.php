@php($terms = get_categories(['hide_empty' => true]))
@php($journal = (int) get_option('page_for_posts'))

@if ($terms)
  <nav aria-label="{{ __('Article categories', 'sage') }}">
    <ul class="flex flex-wrap gap-x-6 gap-y-3">
      @if ($journal > 0)
        <li>
          <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ get_permalink($journal) }}" @if (is_home()) aria-current="page" @endif>{{ __('All', 'sage') }}</a>
        </li>
      @endif

      @foreach ($terms as $term)
        <li>
          <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ get_category_link($term) }}" @if (is_category($term->term_id)) aria-current="page" @endif>{{ $term->name }}</a>
        </li>
      @endforeach
    </ul>
  </nav>
@endif
