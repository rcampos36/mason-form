{{--
  Template Name: Site Settings
--}}

@extends('layouts.app')

@section('content')
  <x-section layout="contained">
    <x-heading level="1">{{ __('Site Settings', 'sage') }}</x-heading>
    <p class="mt-6 max-w-content text-muted">{{ __('These details are edited in the dashboard and are not part of the public page list.', 'sage') }}</p>
  </x-section>
@endsection
