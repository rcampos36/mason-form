<x-container width="page" class="px-6 pb-section md:px-10">
  <div class="entry-content max-w-content">
    @php(the_content())
  </div>

  @if ($pagination())
    <nav class="page-nav mt-10 flex gap-6 text-small" aria-label="Page">
      {!! $pagination !!}
    </nav>
  @endif
</x-container>
