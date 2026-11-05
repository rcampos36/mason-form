@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'as' => 'input',
    'value' => null,
    'hiddenLabel' => false,
])

<label class="{{ \App\Design::classes('field', 'label') }}">
  @if ($label)
    <span @class([
        \App\Design::classes('field', 'labelText'),
        'sr-only' => $hiddenLabel,
    ])>
      {{ $label }}
    </span>
  @endif

  @if ($as === 'textarea')
    <textarea
      name="{{ $name }}"
      {{ $attributes->merge(['class' => \App\Design::classes('field', 'control').' min-h-32']) }}
    >{{ $slot }}</textarea>
  @elseif ($as === 'select')
    <select
      name="{{ $name }}"
      {{ $attributes->merge(['class' => \App\Design::classes('field', 'control')]) }}
    >{{ $slot }}</select>
  @else
    <input
      type="{{ $type }}"
      name="{{ $name }}"
      value="{{ $value }}"
      {{ $attributes->merge(['class' => \App\Design::classes('field', 'control')]) }}
    >
  @endif
</label>
