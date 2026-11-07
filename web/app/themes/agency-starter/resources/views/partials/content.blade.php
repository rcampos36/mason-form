<article @php(post_class('grid gap-5'))>
  <a class="{{ \App\Design::classes('link', 'card') }} gap-5" href="{{ get_permalink() }}">
    @php($image = \App\Acf\Content::featuredImage(get_the_ID(), 'landscape'))
    @if ($image !== '')
      <x-image ratio="landscape">{!! $image !!}</x-image>
    @endif

    <header class="grid gap-3">
      @include('partials.entry-meta')
      <x-heading class="transition-colors duration-300 ease-out group-hover:text-accent group-focus-visible:text-accent motion-reduce:transition-none" level="2" size="3">{{ get_the_title() }}</x-heading>
    </header>

    @if (has_excerpt() || trim(get_the_content()) !== '')
      <p class="text-body text-muted">{{ wp_strip_all_tags(get_the_excerpt()) }}</p>
    @endif
  </a>

  @php($categories = get_the_category())
  @if ($categories)
    <ul class="flex flex-wrap gap-x-6 gap-y-2">
      @foreach ($categories as $category)
        <li>
          <a class="{{ \App\Design::classes('link', 'filter') }}" href="{{ get_category_link($category) }}">{{ $category->name }}</a>
        </li>
      @endforeach
    </ul>
  @endif
</article>
