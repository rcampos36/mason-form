@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <x-section layout="contained">
      <x-heading level="1">{{ get_the_title() }}</x-heading>

      @if (has_excerpt())
        <p class="mt-6 max-w-content text-body text-muted">{{ get_the_excerpt() }}</p>
      @endif

      @if (trim(get_the_content()) !== '')
        <div class="entry-content mt-10 max-w-content">
          @php(the_content())
        </div>
      @endif

      @php($page = \App\Acf\Content::post(function_exists('get_field') ? get_field('service_page') : null))
      @if ($page)
        <div class="mt-10">
          <x-button :href="get_permalink($page)" variant="secondary">
            {{ $page->post_title }}
          </x-button>
        </div>
      @endif
    </x-section>
  @endwhile
@endsection
