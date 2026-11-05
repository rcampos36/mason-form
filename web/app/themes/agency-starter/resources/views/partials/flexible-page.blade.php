@php($sections = \App\Acf\Content::sections())

@unless (\App\Acf\Content::startsWithHero($sections))
  @include('partials.page-header')
@endunless

@include('partials.page-sections', ['sections' => $sections])

@if (trim(get_the_content()) !== '')
  @includeFirst(['partials.content-page', 'partials.content'])
@endif
