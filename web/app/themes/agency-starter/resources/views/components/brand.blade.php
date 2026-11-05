@props([
    'logo' => '',
])

<a {{ $attributes->merge(['class' => \App\Design::classes('link', 'brand')]) }} href="{{ home_url('/') }}">
  @if ($logo !== '')
    {!! $logo !!}
  @else
    {{ \App\Acf\Content::companyName() }}
  @endif
</a>
