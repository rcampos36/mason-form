@foreach ($sections as $index => $section)
  @if (in_array($section['acf_fc_layout'] ?? '', \App\Acf\Content::LAYOUTS, true))
    @include('components.sections.'.$section['acf_fc_layout'], ['section' => $section, 'sectionIndex' => $index])
  @endif
@endforeach
