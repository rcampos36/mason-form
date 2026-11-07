@props([
    'logo' => '',
    'mark' => '',
])

<a {{ $attributes->merge(['class' => \App\Design::classes('link', 'brand')]) }} href="{{ home_url('/') }}">
  @if ($logo !== '')
    {!! $logo !!}
  @elseif ($mark !== '')
    {{ $mark }}
  @else
    {{ \App\Acf\Content::companyName() }}
  @endif
</a>
