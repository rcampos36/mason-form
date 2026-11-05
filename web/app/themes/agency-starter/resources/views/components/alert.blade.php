@props([
    'type' => 'default',
    'message' => null,
])

<div {{ $attributes->merge(['class' => \App\Design::classes('alert', $type)]) }} role="status">
  {!! $message ?? $slot !!}
</div>
