@foreach ($sections as $index => $section)
  @php($layout = (string) ($section['acf_fc_layout'] ?? ''))
  @if (in_array($layout, \App\Acf\Content::LAYOUTS, true))
    @include('components.sections.'.$layout, [
        'section' => $section,
        'sectionIndex' => $index,
        'sectionNumber' => \App\Acf\Content::sectionNumber($layout),
    ])
  @endif
@endforeach
