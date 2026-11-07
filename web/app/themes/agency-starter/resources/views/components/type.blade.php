@props([
    'size' => 'body',
    'as' => 'p',
])

@php
  $tag = in_array($as, ['p', 'span', 'div', 'li'], true) ? $as : 'p';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => \App\Design::classes('type', $size)]) }}>
  {!! $slot !!}
</{{ $tag }}>
