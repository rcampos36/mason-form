@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @php($sections = \App\Acf\Content::sections())
    @php($hasJournal = false)
    @foreach ($sections as $section)
      @if (($section['acf_fc_layout'] ?? '') === 'journal')
        @php($hasJournal = true)
      @endif
    @endforeach
    @php($closing = [])
    @if (! $hasJournal)
      @php($last = $sections === [] ? null : $sections[array_key_last($sections)])
      @if (is_array($last) && ($last['acf_fc_layout'] ?? '') === 'call_to_action')
        @php($closing = [array_pop($sections)])
      @endif
    @endif

    @unless (\App\Acf\Content::startsWithHero($sections))
      @include('partials.page-header')
    @endunless

    @include('partials.page-sections', ['sections' => $sections])

    @unless ($hasJournal)
      @php($journalId = (int) get_option('page_for_posts'))
      @include('components.sections.journal', [
          'section' => [
              'heading' => $journalId > 0 ? get_the_title($journalId) : '',
              'text' => '',
              'count' => 3,
          ],
          'sectionNumber' => \App\Acf\Content::sectionNumber('journal'),
          'sectionIndex' => count($sections),
      ])
    @endunless

    @include('partials.page-sections', ['sections' => $closing])

    @if (trim(get_the_content()) !== '')
      @includeFirst(['partials.content-page', 'partials.content'])
    @endif
  @endwhile
@endsection
