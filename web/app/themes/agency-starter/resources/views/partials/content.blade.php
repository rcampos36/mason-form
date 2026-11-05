<article @php(post_class('grid gap-6'))>
  @php($image = \App\Acf\Content::featuredImage(get_the_ID(), 'portrait'))
  @if ($image !== '')
    <a href="{{ get_permalink() }}" class="{{ \App\Design::classes('image', 'frame') }}">{!! $image !!}</a>
  @endif

  <header class="grid gap-3">
    @include('partials.entry-meta')
    <x-heading level="2" size="3">
      <a href="{{ get_permalink() }}">{{ get_the_title() }}</a>
    </x-heading>
  </header>

  @if (has_excerpt() || trim(get_the_content()) !== '')
    <p class="text-body text-muted">{{ wp_strip_all_tags(get_the_excerpt()) }}</p>
  @endif

  @php($categories = get_the_category())
  @if ($categories)
    <ul class="flex flex-wrap gap-x-4 gap-y-2">
      @foreach ($categories as $category)
        <li>
          <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ get_category_link($category) }}">{{ $category->name }}</a>
        </li>
      @endforeach
    </ul>
  @endif
</article>
