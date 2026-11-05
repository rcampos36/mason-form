@php
  $variation = \App\Acf\Content::raw('default_footer_variation');
  $variation = is_string($variation) ? $variation : null;
  $logo = \App\Acf\Content::logoHtml('footer_logo', true);
  $description = \App\Acf\Content::description('footer_description', 'company_description');
  $email = \App\Acf\Content::contact('email');
  $phone = \App\Acf\Content::contact('phone');
  $address = \App\Acf\Content::contact('address');
  $cta = \App\Acf\Content::link(\App\Acf\Content::raw('footer_cta'));
  $social = \App\Acf\Content::socialLinks();
  $menuName = has_nav_menu('footer_navigation') ? wp_get_nav_menu_name('footer_navigation') : '';
@endphp

<footer class="{{ \App\Design::classes('footer', $variation) }}">
  <div class="{{ \App\Design::classes('footerInner', $variation) }}">
    <div class="grid content-start gap-6 lg:col-span-5">
      <x-brand :logo="$logo" />

      @if ($description !== '')
        <p class="max-w-content text-body text-muted">{{ $description }}</p>
      @endif
    </div>

    @if (has_nav_menu('footer_navigation'))
      <nav class="lg:col-span-3" aria-label="{{ $menuName }}">
        {!! wp_nav_menu([
            'theme_location' => 'footer_navigation',
            'menu_id' => 'footer-menu',
            'menu_class' => \App\Design::classes('nav', 'footer'),
            'container' => false,
            'echo' => false,
            'fallback_cb' => false,
            'hk_variant' => 'footer',
        ]) !!}
      </nav>
    @endif

    @if ($email !== '' || $phone !== '' || $address !== '' || $social || $cta)
      <div class="grid content-start gap-4 lg:col-span-4">
        @if ($email !== '' && ($mailto = sanitize_email($email)) !== '')
          <a class="{{ \App\Design::classes('link', 'nav') }}" href="mailto:{{ $mailto }}">{{ $mailto }}</a>
        @endif

        @if ($phone !== '')
          <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ \App\Acf\Content::phoneHref($phone) }}">{{ $phone }}</a>
        @endif

        @if ($address !== '')
          <p class="text-body text-foreground">{!! \App\Acf\Content::addressHtml($address) !!}</p>
        @endif

        @if ($social)
          <ul class="mt-2 flex flex-wrap gap-x-6 gap-y-3">
            @foreach ($social as $link)
              <li>
                <a class="{{ \App\Design::classes('link', 'nav') }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
              </li>
            @endforeach
          </ul>
        @endif

        @if ($cta)
          <div class="mt-4">
            <x-button :href="$cta['url']" variant="secondary" :target="$cta['target'] ?: null">
              {{ $cta['title'] }}
            </x-button>
          </div>
        @endif
      </div>
    @endif

    <p class="text-small text-muted md:col-span-2 lg:col-span-12">{{ \App\Acf\Content::copyright() }}</p>
  </div>
</footer>
