@php
  $announcement = \App\Acf\Content::text('announcement_text');
  $announcementLink = \App\Acf\Content::link(\App\Acf\Content::raw('announcement_link'));
  $cta = \App\Acf\Content::link(\App\Acf\Content::raw('header_cta'));
  $logo = \App\Acf\Content::logoHtml('header_logo');
  $mark = '';

  if ($logo === '') {
      $mark = \App\Acf\Content::text('header_mark');
      $mark = $mark !== '' ? $mark : \App\Acf\Content::nameMark();
  }
  $menuName = has_nav_menu('primary_navigation') ? wp_get_nav_menu_name('primary_navigation') : __('Primary', 'sage');
  $overlay = \App\Acf\Content::headerOverlaysContent();
  $fixed = $overlay || \App\Acf\Content::enabled('sticky_header');
@endphp

<header
  class="site-header {{ $overlay ? 'is-overlay' : '' }} {{ $fixed ? 'is-fixed' : 'relative z-40' }}"
  data-site-header
  @if ($fixed) data-header-scroll @endif
>
  <div class="site-header-bar">
  @if (\App\Acf\Content::enabled('announcement_enabled') && $announcement !== '')
    <div class="{{ \App\Design::classes('announcement', 'default') }}">
      <p>
        {{ $announcement }}
        @if ($announcementLink)
          <a class="{{ \App\Design::classes('link', 'accent') }}" href="{{ $announcementLink['url'] }}" @if ($announcementLink['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $announcementLink['title'] }}</a>
        @endif
      </p>
    </div>
  @endif

  <div class="flex items-stretch">
    <div class="flex items-center px-5 md:px-8 lg:px-gutter">
      <x-brand class="site-mark" :logo="$logo" :mark="$mark" />
    </div>

    <div class="ml-auto hidden items-stretch border-r border-border lg:flex">
      @if (has_nav_menu('primary_navigation'))
        <nav class="flex" aria-label="{{ $menuName }}">
          {!! wp_nav_menu([
              'theme_location' => 'primary_navigation',
              'menu_id' => 'primary-menu',
              'menu_class' => \App\Design::classes('nav', 'desktop').' site-header-nav',
              'container' => false,
              'echo' => false,
              'fallback_cb' => false,
              'hk_variant' => 'desktop',
          ]) !!}
        </nav>
      @endif
    </div>

    <button
      type="button"
      class="ml-auto inline-flex items-center justify-center border-l border-border px-5 text-current lg:hidden focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
      data-menu-open
      aria-expanded="false"
      aria-controls="site-menu"
    >
      <span class="sr-only">{{ __('Open menu', 'sage') }}</span>
      <span class="grid gap-1.5" aria-hidden="true">
        <span class="block h-px w-6 bg-current"></span>
        <span class="block h-px w-6 bg-current"></span>
      </span>
    </button>
  </div>
  </div>

  <p class="sr-only" data-menu-status aria-live="polite" data-opened="{{ __('Menu opened', 'sage') }}" data-closed="{{ __('Menu closed', 'sage') }}"></p>

  <dialog id="site-menu" class="site-menu" data-site-menu aria-label="{{ $menuName }}">
    <div class="flex min-h-full flex-col bg-inverse px-5 py-8 text-inverse-foreground md:px-8 lg:px-gutter">
      <div class="flex items-center justify-between gap-6">
        <x-brand :logo="$logo" />

        <button
          type="button"
          class="inline-flex h-12 w-12 items-center justify-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-inverse-accent"
          data-menu-close
        >
          <span class="sr-only">{{ __('Close menu', 'sage') }}</span>
          <span class="relative block h-6 w-6" aria-hidden="true">
            <span class="absolute left-0 top-1/2 block h-px w-6 -translate-y-1/2 rotate-45 bg-current"></span>
            <span class="absolute left-0 top-1/2 block h-px w-6 -translate-y-1/2 -rotate-45 bg-current"></span>
          </span>
        </button>
      </div>

      @if (has_nav_menu('primary_navigation'))
        <nav class="mt-20" aria-label="{{ $menuName }}">
          {!! wp_nav_menu([
              'theme_location' => 'primary_navigation',
              'menu_id' => 'primary-menu-mobile',
              'menu_class' => \App\Design::classes('nav', 'mobile'),
              'container' => false,
              'echo' => false,
              'fallback_cb' => false,
              'hk_variant' => 'mobile',
          ]) !!}
        </nav>
      @endif

      @if ($cta)
        <div class="mt-16">
          <x-button :href="$cta['url']" variant="primary" :target="$cta['target'] ?: null">
            {{ $cta['title'] }}
          </x-button>
        </div>
      @endif
    </div>
  </dialog>
</header>
