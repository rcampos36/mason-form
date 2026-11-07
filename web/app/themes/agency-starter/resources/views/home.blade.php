@extends('layouts.app')

@section('content')
  @php($journalId = (int) get_option('page_for_posts'))
  @php($journalSections = array_values(array_filter(
      \App\Acf\Content::sections($journalId),
      fn ($section) => ($section['acf_fc_layout'] ?? '') !== 'journal'
  )))
  @php($sections = array_values(array_filter(
      $journalSections,
      fn ($section) => ($section['acf_fc_layout'] ?? '') !== 'call_to_action'
  )))
  @php($closing = array_values(array_filter(
      $journalSections,
      fn ($section) => ($section['acf_fc_layout'] ?? '') === 'call_to_action'
  )))

  @unless (\App\Acf\Content::startsWithHero($sections))
    <x-section layout="wide">
      <x-heading level="1" size="display">{{ $journalId > 0 ? get_the_title($journalId) : __('Journal', 'sage') }}</x-heading>
    </x-section>
  @endunless

  @include('partials.page-sections', ['sections' => $sections])

  <x-section layout="wide">
    <div class="grid gap-12">
      @php($featuredHeading = $journalId > 0 ? \App\Acf\Content::text('featured_heading', $journalId) : '')
      @php($featured = $journalId > 0 ? \App\Acf\Content::posts(function_exists('get_field') ? get_field('featured_posts', $journalId) : null) : [])
      @php($featuredIsNumbered = false)

      @if (! is_paged() && $featured)
        <div class="grid gap-16">
          @if ($featuredHeading !== '')
            @php($featuredIsNumbered = true)
            <x-section-intro :heading="$featuredHeading" :number="\App\Acf\Content::sectionNumber('journal')" />
          @endif

          @php($lead = array_shift($featured))
          @if ($lead instanceof \WP_Post)
            @php(\App\Acf\Content::preparePost($lead))
            <article class="border-t border-border pt-10">
              <a class="{{ \App\Design::classes('link', 'card') }} items-start gap-8 lg:grid-cols-12" href="{{ get_permalink($lead) }}">
                @php($leadImage = \App\Acf\Content::featuredImage($lead->ID, 'landscape'))
                @if ($leadImage !== '')
                  <div class="lg:col-span-7">
                    <x-image ratio="landscape">{!! $leadImage !!}</x-image>
                  </div>
                @endif
                <div class="grid content-start gap-4 lg:col-span-5">
                  @include('partials.entry-meta')
                  <x-heading class="transition-colors duration-300 ease-out group-hover:text-accent group-focus-visible:text-accent motion-reduce:transition-none" level="2" size="1">{{ get_the_title($lead) }}</x-heading>
                  @if (has_excerpt($lead) || trim($lead->post_content) !== '')
                    <p class="max-w-content text-body text-muted">{{ wp_strip_all_tags(get_the_excerpt($lead)) }}</p>
                  @endif
                </div>
              </a>
            </article>
            @php(wp_reset_postdata())
          @endif

          @if ($featured)
            <ul class="grid list-none gap-12 sm:grid-cols-2">
              @foreach ($featured as $feature)
                <li>
                  @php(\App\Acf\Content::preparePost($feature))
                  @include('partials.content')
                </li>
              @endforeach
            </ul>
            @php(wp_reset_postdata())
          @endif
        </div>
      @endif

      @unless ($featuredIsNumbered)
        <p class="text-nav uppercase text-muted">{{ \App\Acf\Content::sectionNumber('journal') }}</p>
      @endunless

      @include('partials.journal-filters')
      @include('partials.journal-loop')
    </div>
  </x-section>

  @include('partials.page-sections', ['sections' => $closing])
@endsection
