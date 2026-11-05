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
    <x-section layout="contained">
      <x-heading level="1">{{ $journalId > 0 ? get_the_title($journalId) : __('Journal', 'sage') }}</x-heading>
    </x-section>
  @endunless

  @include('partials.page-sections', ['sections' => $sections])

  <x-section layout="wide">
    <div class="grid gap-12">
      @php($featuredHeading = $journalId > 0 ? \App\Acf\Content::text('featured_heading', $journalId) : '')
      @php($featured = $journalId > 0 ? \App\Acf\Content::posts(function_exists('get_field') ? get_field('featured_posts', $journalId) : null) : [])

      @if (! is_paged() && $featured)
        <div class="grid gap-8">
          @if ($featuredHeading !== '')
            <x-heading level="2">{{ $featuredHeading }}</x-heading>
          @endif
          <ul class="grid list-none gap-12 lg:grid-cols-2">
            @foreach ($featured as $feature)
              <li>
                @php(\App\Acf\Content::preparePost($feature))
                @include('partials.content')
              </li>
            @endforeach
          </ul>
          @php(wp_reset_postdata())
        </div>
      @endif

      @include('partials.journal-filters')
      @include('partials.journal-loop')
    </div>
  </x-section>

  @include('partials.page-sections', ['sections' => $closing])
@endsection
