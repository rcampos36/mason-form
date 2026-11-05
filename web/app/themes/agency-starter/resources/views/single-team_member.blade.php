@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <x-section layout="split">
      @php($image = \App\Acf\Content::featuredImage(get_the_ID()))
      @if ($image !== '')
        <x-image>{!! $image !!}</x-image>
      @endif

      <div class="grid content-start gap-6">
        <x-heading level="1">{{ get_the_title() }}</x-heading>

        @php($role = \App\Acf\Content::text('team_role', get_the_ID()))
        @if ($role !== '')
          <p class="{{ \App\Design::classes('tag', 'default') }}">{{ $role }}</p>
        @endif

        @if (trim(get_the_content()) !== '')
          <div class="entry-content">
            @php(the_content())
          </div>
        @endif

        @php($link = \App\Acf\Content::link(function_exists('get_field') ? get_field('team_link') : null))
        @if ($link)
          <div>
            <x-button :href="$link['url']" variant="secondary" :target="$link['target'] ?: null">
              {{ $link['title'] }}
            </x-button>
          </div>
        @endif
      </div>
    </x-section>
  @endwhile
@endsection
