@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <x-section layout="contained">
      <header class="grid gap-4">
        <x-heading level="1">{{ get_the_title() }}</x-heading>

        @php($client = \App\Acf\Content::text('project_client', get_the_ID()))
        @php($year = \App\Acf\Content::text('project_year', get_the_ID()))
        @php($categories = \App\Acf\Content::termNames(get_the_ID(), 'project_category'))
        @if ($client !== '' || $year !== '' || $categories)
          <p class="{{ \App\Design::classes('tag', 'default') }}">
            {{ collect([$client, $year, implode(', ', $categories)])->filter()->implode(' / ') }}
          </p>
        @endif
      </header>

      @php($image = \App\Acf\Content::featuredImage(get_the_ID(), 'content', 'eager'))
      @if ($image !== '')
        <x-image class="mt-10">{!! $image !!}</x-image>
      @endif

      @if (trim(get_the_content()) !== '')
        <div class="entry-content mt-10 max-w-content">
          @php(the_content())
        </div>
      @endif

      @php($link = \App\Acf\Content::link(function_exists('get_field') ? get_field('project_url') : null))
      @if ($link)
        <div class="mt-10">
          <x-button :href="$link['url']" variant="secondary" :target="$link['target'] ?: null">
            {{ $link['title'] }}
          </x-button>
        </div>
      @endif
    </x-section>

    @php($galleryValue = function_exists('get_field') ? get_field('project_gallery') : null)
    @php($gallery = \App\Acf\Content::ids(is_array($galleryValue) && $galleryValue !== [] ? $galleryValue : get_post_meta(get_the_ID(), 'project_gallery', true)))
    @if ($gallery)
      <x-section layout="images">
        @foreach ($gallery as $id)
          <x-image>{!! \App\Acf\Content::image($id, 'large', 'content', '(min-width: 1024px) 30vw, 100vw') !!}</x-image>
        @endforeach
      </x-section>
    @endif

    @php($services = \App\Acf\Content::posts(function_exists('get_field') ? get_field('project_services') : null))
    @if ($services)
      <x-section layout="contained">
        <x-heading level="2">{{ __('Services', 'sage') }}</x-heading>
        <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-3">
          @foreach ($services as $service)
            <li>
              <a class="{{ \App\Design::classes('link', 'inline') }}" href="{{ get_permalink($service) }}">{{ $service->post_title }}</a>
            </li>
          @endforeach
        </ul>
      </x-section>
    @endif

    @include('partials.page-sections', ['sections' => \App\Acf\Content::sections()])
  @endwhile
@endsection
