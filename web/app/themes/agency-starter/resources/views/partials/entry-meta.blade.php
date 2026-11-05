<p class="{{ \App\Design::classes('tag', 'caption') }} flex flex-wrap gap-x-4 uppercase">
  <time class="dt-published" datetime="{{ get_post_time('c', true) }}">
    {{ get_the_date() }}
  </time>

  @if (get_the_author() !== '')
    <span class="p-author h-card">
      {{ get_the_author() }}
    </span>
  @endif
</p>
