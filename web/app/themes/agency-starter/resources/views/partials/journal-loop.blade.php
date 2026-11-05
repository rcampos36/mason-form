@if (! have_posts())
  <x-alert type="warning">
    {{ __('No articles yet.', 'sage') }}
  </x-alert>
@else
  <ul class="grid list-none gap-12 sm:grid-cols-2 lg:grid-cols-3">
    @while(have_posts()) @php(the_post())
      <li>
        @include('partials.content')
      </li>
    @endwhile
  </ul>

  <nav class="journal-pages mt-12" aria-label="{{ __('Journal pages', 'sage') }}">
    {!! get_the_posts_pagination([
        'mid_size' => 1,
        'prev_text' => __('Previous', 'sage'),
        'next_text' => __('Next', 'sage'),
    ]) !!}
  </nav>
@endif
