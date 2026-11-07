@php
  $projectId = (int) ($id ?? 0);
  $parts = array_values(array_filter([
      implode(', ', \App\Acf\Content::termNames($projectId, 'project_category')),
      \App\Acf\Content::text('project_year', $projectId),
      \App\Acf\Content::text('project_location', $projectId),
  ]));
@endphp

@if ($parts)
  <p class="text-nav uppercase text-muted">{{ implode(' / ', $parts) }}</p>
@endif
