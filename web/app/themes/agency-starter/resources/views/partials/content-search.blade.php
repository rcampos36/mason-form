<article @php(post_class('grid gap-6 border-b border-border py-10'))>
  <header class="grid gap-3">
    <x-heading level="2" size="3">
      <a href="{{ get_permalink() }}">{!! $title !!}</a>
    </x-heading>

    @includeWhen(get_post_type() === 'post', 'partials.entry-meta')
  </header>

  <div class="entry-content text-muted">
    @php(the_excerpt())
  </div>
</article>
