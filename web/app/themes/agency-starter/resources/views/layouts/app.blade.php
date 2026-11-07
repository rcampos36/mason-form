<!doctype html>
<html @php(language_attributes()) @class(['motion-ready' => \App\Acf\Content::motionEnabled()]) @if ($accent = \App\Acf\Content::accent()) style="--color-accent: {{ $accent }}" @endif>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class(\App\Acf\Content::choice('default_background', 'tone').' font-sans text-body antialiased '.(\App\Acf\Content::headerOverlaysContent() ? 'has-header-overlay' : (\App\Acf\Content::enabled('sticky_header') ? 'has-fixed-header' : ''))))>
    @php(wp_body_open())

    <div id="app" class="flex min-h-screen flex-col">
      <a class="skip-link" href="#main">
        {{ __('Skip to content', 'sage') }}
      </a>

      @include('sections.header')

      <main id="main" class="flex-1">
        @yield('content')
      </main>

      @hasSection('sidebar')
        <aside class="sidebar">
          @yield('sidebar')
        </aside>
      @endif

      @include('sections.footer')
    </div>

    <dialog data-section-lightbox class="{{ \App\Design::classes('dialog', 'default') }}">
      <div class="grid gap-4">
        <button type="button" data-lightbox-close class="{{ \App\Design::classes('button', 'ghost') }} justify-self-end">
          {{ __('Close', 'sage') }}
        </button>
        <img alt="" aria-hidden="true" data-lightbox-image class="{{ \App\Design::classes('image', 'content') }}">
        <p data-caption class="{{ \App\Design::classes('tag', 'caption') }}"></p>
      </div>
    </dialog>

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
