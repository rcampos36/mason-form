@extends('layouts.app')

@section('content')
  <x-section layout="contained">
    <header class="grid max-w-content gap-4">
      <x-heading level="1">{{ single_term_title('', false) !== '' ? single_term_title('', false) : wp_strip_all_tags(get_the_archive_title()) }}</x-heading>
      @if (get_the_archive_description() !== '')
        <div class="entry-content text-muted">{!! get_the_archive_description() !!}</div>
      @endif
    </header>
  </x-section>

  <x-section layout="wide">
    <div class="grid gap-12">
      @if (is_category())
        @include('partials.journal-filters')
      @endif
      @include('partials.journal-loop')
    </div>
  </x-section>
@endsection
