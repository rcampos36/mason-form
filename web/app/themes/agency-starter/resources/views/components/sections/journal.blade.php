@php
  $heading = \App\Acf\Content::text('heading', $section);
  $text = \App\Acf\Content::text('text', $section);
  $posts = \App\Acf\Content::journal($section);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
@endphp

@if ($heading !== '' || $text !== '' || $posts)
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12">
      <x-section-intro :heading="$heading" :text="$text" :align="$align" />

      @if ($posts)
        <ul class="{{ \App\Acf\Content::variant($section['layout'] ?? null, 'portfolio') }} list-none">
          @foreach ($posts as $post)
            <li>
              <article class="grid gap-4">
                <p class="{{ \App\Design::classes('tag', 'default') }}">{{ get_the_date('', $post) }}</p>
                <x-heading level="3">
                  <a href="{{ get_permalink($post) }}">{{ $post->post_title }}</a>
                </x-heading>
                @if (trim(get_the_excerpt($post)) !== '')
                  <p class="text-body text-muted">{{ get_the_excerpt($post) }}</p>
                @endif
              </article>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </x-section>
@endif
