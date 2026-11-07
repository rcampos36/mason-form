<article @php(post_class())>
  <x-section layout="wide">
    <header class="grid gap-4">
      <x-heading level="1" size="display">{!! $title !!}</x-heading>

      @include('partials.entry-meta')

      @php($categories = get_post_type() === 'post' ? get_the_category() : [])
      @if ($categories)
        <ul class="flex flex-wrap gap-x-4 gap-y-2">
          @foreach ($categories as $category)
            <li>
              <a class="{{ \App\Design::classes('link', 'filter') }}" href="{{ get_category_link($category) }}">{{ $category->name }}</a>
            </li>
          @endforeach
        </ul>
      @endif
    </header>

    @php($image = \App\Acf\Content::featuredImage(get_the_ID(), 'content', 'eager'))
    @if ($image !== '')
      <x-image class="mt-10" ratio="landscape">{!! $image !!}</x-image>
    @endif

    <div class="entry-content mt-10">
      @php(the_content())
    </div>

    @if ($pagination())
      <footer>
        <nav class="page-nav mt-10 flex gap-6 text-small" aria-label="Page">
          {!! $pagination !!}
        </nav>
      </footer>
    @endif

    @php($related = get_post_type() === 'post' ? \App\Acf\Content::relatedPosts(get_the_ID()) : [])
    @if ($related)
      <div class="mt-16 grid gap-8">
        <x-heading level="2">{{ __('Related', 'sage') }}</x-heading>
        <ul class="grid list-none gap-12 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($related as $relatedPost)
            <li>
              @php(\App\Acf\Content::preparePost($relatedPost))
              @include('partials.content')
            </li>
          @endforeach
        </ul>
        @php(wp_reset_postdata())
      </div>
    @endif

    @php(comments_template())
  </x-section>
</article>
