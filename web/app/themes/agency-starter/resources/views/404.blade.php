@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (! have_posts())
    <x-container width="page" class="px-6 pb-section md:px-10">
      <div class="grid max-w-content gap-8">
        <x-alert type="warning">
          {!! __('Sorry, but the page you are trying to view does not exist.', 'sage') !!}
        </x-alert>

        {!! get_search_form(false) !!}
      </div>
    </x-container>
  @endif
@endsection
