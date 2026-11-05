<form role="search" method="get" class="grid max-w-content gap-6" action="{{ home_url('/') }}">
  <x-field
    :label="_x('Search for:', 'label', 'sage')"
    type="search"
    name="s"
    value="{{ get_search_query() }}"
    placeholder="{!! esc_attr_x('Search &hellip;', 'placeholder', 'sage') !!}"
  />

  <div>
    <x-button type="submit" variant="primary">
      {{ _x('Search', 'submit button', 'sage') }}
    </x-button>
  </div>
</form>
