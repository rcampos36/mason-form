<div
  class="{{ trim(\App\Acf\Content::variant($section['spacing'] ?? null, 'space').' '.\App\Acf\Content::variant($section['divider'] ?? null, 'divider')) }}"
  @if (($section['divider'] ?? '') === 'line') role="separator" @endif
></div>
