{{--
  Template Name: Work
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.flexible-page')
  @endwhile
@endsection
