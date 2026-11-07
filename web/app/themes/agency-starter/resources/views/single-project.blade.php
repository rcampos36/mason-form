@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @php($cover = \App\Acf\Content::featuredImage(get_the_ID(), 'cover', 'eager'))
    @php($client = \App\Acf\Content::text('project_client', get_the_ID()))
    @php($year = \App\Acf\Content::text('project_year', get_the_ID()))
    @php($location = \App\Acf\Content::text('project_location', get_the_ID()))
    @php($area = \App\Acf\Content::text('project_area', get_the_ID()))
    @php($type = \App\Acf\Content::text('project_type', get_the_ID()))
    @php($categories = \App\Acf\Content::termNames(get_the_ID(), 'project_category'))
    @php($facts = array_filter([
        __('Category', 'sage') => implode(', ', $categories),
        __('Year', 'sage') => $year,
        __('Location', 'sage') => $location,
        __('Area', 'sage') => $area,
        __('Type', 'sage') => $type,
        __('Client', 'sage') => $client,
    ]))

    @if ($cover !== '')
      <section class="relative isolate flex min-h-screen items-end overflow-hidden">
        {!! $cover !!}
        <div class="{{ \App\Design::classes('hero', 'shade') }}"></div>
        <div class="relative mx-auto w-full max-w-wide px-5 py-section md:px-8 lg:px-gutter">
          <x-heading level="1" size="display">{{ get_the_title() }}</x-heading>
        </div>
      </section>
    @else
      <x-section layout="wide">
        <x-heading level="1" size="display">{{ get_the_title() }}</x-heading>
      </x-section>
    @endif

    @if ($facts)
      <x-section layout="compact">
        <dl class="grid border-t border-border sm:grid-cols-2 lg:grid-cols-4">
          @foreach ($facts as $label => $value)
            <div class="grid content-start gap-2 border-b border-border py-6 pr-6">
              <dt class="text-nav uppercase text-muted">{{ $label }}</dt>
              <dd class="text-body text-foreground">{{ $value }}</dd>
            </div>
          @endforeach
        </dl>
      </x-section>
    @endif

    @if (trim(get_the_content()) !== '')
      <x-section layout="compact">
        <div class="entry-content max-w-content text-lead text-muted">
          @php(the_content())
        </div>
      </x-section>
    @endif

    @php($link = \App\Acf\Content::link(function_exists('get_field') ? get_field('project_url') : null))
    @if ($link)
      <x-section layout="compact">
        <x-button :href="$link['url']" variant="secondary" :target="$link['target'] ?: null">
          {{ $link['title'] }}
        </x-button>
      </x-section>
    @endif

    @php($galleryValue = function_exists('get_field') ? get_field('project_gallery') : null)
    @php($gallery = \App\Acf\Content::ids(is_array($galleryValue) && $galleryValue !== [] ? $galleryValue : get_post_meta(get_the_ID(), 'project_gallery', true)))
    @if ($gallery)
      <x-section layout="images">
        @foreach ($gallery as $id)
          <x-image ratio="landscape">{!! \App\Acf\Content::image($id, 'large', 'landscape', '(min-width: 1024px) 45vw, 100vw') !!}</x-image>
        @endforeach
      </x-section>
    @endif

    @php($plans = \App\Acf\Content::floorPlans(get_the_ID()))
    @if ($plans)
      <x-section layout="wide">
        <x-heading level="2">{{ __('Floor plans', 'sage') }}</x-heading>
        <div class="mt-10 grid gap-8 sm:grid-cols-2">
          @foreach ($plans as $id)
            <x-image ratio="landscape">{!! \App\Acf\Content::image($id, 'large', 'landscape', '(min-width: 1024px) 45vw, 100vw') !!}</x-image>
          @endforeach
        </div>
      </x-section>
    @endif

    @php($services = \App\Acf\Content::posts(function_exists('get_field') ? get_field('project_services') : null))
    @if ($services)
      <x-section layout="compact">
        <x-heading level="2">{{ __('Services', 'sage') }}</x-heading>
        <ul class="mt-6 flex flex-wrap gap-x-8 gap-y-3">
          @foreach ($services as $service)
            <li>
              <a class="{{ \App\Design::classes('link', 'filter') }}" href="{{ get_permalink($service) }}">{{ $service->post_title }}</a>
            </li>
          @endforeach
        </ul>
      </x-section>
    @endif

    @include('partials.page-sections', ['sections' => \App\Acf\Content::sections()])

    @php($next = \App\Acf\Content::nextProject(get_the_ID()))
    @if ($next instanceof \WP_Post)
      <x-section layout="wide">
        <a class="{{ \App\Design::classes('link', 'card') }} items-end gap-8 border-t border-border pt-10 lg:grid-cols-12" href="{{ get_permalink($next) }}">
          <div class="grid content-start gap-4 lg:col-span-5">
            <p class="text-nav uppercase text-muted">{{ __('Next project', 'sage') }}</p>
            <x-heading class="transition-colors duration-300 ease-out group-hover:text-accent group-focus-visible:text-accent motion-reduce:transition-none" level="2" size="1">{{ $next->post_title }}</x-heading>
            @include('partials.project-meta', ['id' => $next->ID])
          </div>
          @php($nextImage = \App\Acf\Content::featuredImage($next->ID, 'landscape'))
          @if ($nextImage !== '')
            <div class="lg:col-span-7">
              <x-image ratio="landscape">{!! $nextImage !!}</x-image>
            </div>
          @endif
        </a>
      </x-section>
    @endif
  @endwhile
@endsection
