@php
  $heading = \App\Acf\Content::text('heading', $section);
  $questions = array_values(array_filter(\App\Acf\Content::rows($section['questions'] ?? null), function ($item) {
      return \App\Acf\Content::text('question', $item) !== '';
  }));
@endphp

@if ($heading !== '' || $questions)
  <x-section layout="contained" :tone="$section['section_background'] ?? null">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" />

      @if ($questions)
        <div class="border-t border-border">
          @foreach ($questions as $item)
            <details class="group border-b border-border" name="faq-{{ (int) ($sectionIndex ?? 0) }}">
              <summary class="flex cursor-pointer list-none items-center justify-between gap-6 py-6 text-h4 text-foreground marker:content-none [&::-webkit-details-marker]:hidden">
                <span>{{ \App\Acf\Content::text('question', $item) }}</span>
                <span class="text-muted group-open:hidden" aria-hidden="true">+</span>
                <span class="hidden text-muted group-open:inline" aria-hidden="true">–</span>
              </summary>
              @php($answer = \App\Acf\Content::rich($item['answer'] ?? null))
              @if ($answer !== '')
                <div class="entry-content max-w-content pb-6 text-body text-muted">{!! $answer !!}</div>
              @endif
            </details>
          @endforeach
        </div>
      @endif
    </div>
  </x-section>
@endif
