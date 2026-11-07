@php
  $eyebrow = \App\Acf\Content::text('footer_eyebrow', null, __('Contact', 'sage'));
  $index = \App\Acf\Content::text('footer_index');
  $heading = \App\Acf\Content::text('footer_heading');
  $intro = \App\Acf\Content::text('footer_intro');
  $description = \App\Acf\Content::description('footer_description', 'company_description');
  $studioLabel = \App\Acf\Content::text('footer_studio_label', null, __('Studio', 'sage'));
  $pagesLabel = \App\Acf\Content::text('footer_pages_label', null, __('Pages', 'sage'));
  $officeLabel = \App\Acf\Content::text('footer_contact_label', null, __('Studio office', 'sage'));
  $followLabel = \App\Acf\Content::text('footer_follow_label', null, __('Follow', 'sage'));
  $email = \App\Acf\Content::footerContact('email');
  $phone = \App\Acf\Content::footerContact('phone');
  $address = \App\Acf\Content::footerContact('address');
  $cta = \App\Acf\Content::link(\App\Acf\Content::raw('footer_cta'));
  $social = \App\Acf\Content::socialLinks();
  $mailto = $email !== '' ? sanitize_email($email) : '';
  $index = trim($index, "[] \t");
  $company = \App\Acf\Content::companyName();
  $characters = preg_split('//u', $company, -1, PREG_SPLIT_NO_EMPTY) ?: [];
  $backToTop = \App\Acf\Content::text('back_to_top_label', null, __('Back to top', 'sage'));
@endphp

<footer class="site-footer">
  <div class="site-footer-intro">
    @if ($eyebrow !== '')
      <p class="site-footer-kicker">{{ $eyebrow }}</p>
    @endif

    <div class="site-footer-copy">
      @if ($index !== '')
        <p class="site-footer-index">[{{ $index }}]</p>
      @endif

      @if ($heading !== '')
        <h2 class="site-footer-heading">{{ $heading }}</h2>
      @endif

      @if ($intro !== '')
        <p class="site-footer-lead">{{ $intro }}</p>
      @endif

      @if ($cta || $mailto !== '')
        <div class="site-footer-actions">
          @if ($cta)
            <a class="site-footer-button" href="{{ $cta['url'] }}" @if (($cta['target'] ?? '') === '_blank') target="_blank" rel="noopener noreferrer" @endif>
              {{ $cta['title'] }} <span aria-hidden="true">→</span>
            </a>
          @endif

          @if ($mailto !== '')
            <a class="site-footer-email" href="mailto:{{ $mailto }}">{{ $mailto }}</a>
          @endif
        </div>
      @endif
    </div>
  </div>

  <div class="site-footer-columns">
    @if ($description !== '')
      <div class="site-footer-column">
        <p class="site-footer-label">{{ $studioLabel }}</p>
        <p class="site-footer-note">{{ $description }}</p>
      </div>
    @endif

    @if (has_nav_menu('footer_navigation'))
      <div class="site-footer-column">
        <p class="site-footer-label">{{ $pagesLabel }}</p>
        <nav aria-label="{{ $pagesLabel }}">
          {!! wp_nav_menu([
              'theme_location' => 'footer_navigation',
              'menu_id' => 'footer-menu',
              'menu_class' => 'site-footer-links',
              'container' => false,
              'echo' => false,
              'fallback_cb' => false,
          ]) !!}
        </nav>
      </div>
    @endif

    @if ($address !== '' || $phone !== '' || $mailto !== '')
      <div class="site-footer-column">
        <p class="site-footer-label">{{ $officeLabel }}</p>
        <div class="site-footer-note">
          @if ($address !== '')
            <p>{!! \App\Acf\Content::addressHtml($address) !!}</p>
          @endif

          @if ($phone !== '')
            <p><a href="{{ \App\Acf\Content::phoneHref($phone) }}">{{ $phone }}</a></p>
          @endif

          @if ($mailto !== '')
            <p><a href="mailto:{{ $mailto }}">{{ $mailto }}</a></p>
          @endif
        </div>
      </div>
    @endif

    @if ($social)
      <div class="site-footer-column">
        <p class="site-footer-label">{{ $followLabel }}</p>
        <ul class="site-footer-links">
          @foreach ($social as $link)
            <li>
              <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
            </li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>

  <div class="site-footer-close">
    <a class="site-footer-top" href="#app">
      {{ $backToTop }} <span aria-hidden="true">↑</span>
    </a>

    @if ($characters !== [])
      <p class="site-footer-word" data-reveal="word" aria-label="{{ $company }}">
        <span class="site-footer-word-line" aria-hidden="true">
          @foreach ($characters as $characterIndex => $character)
            <span class="site-footer-letter" style="--letter: {{ (int) $characterIndex }}">@if ($character === ' ')&nbsp;@else{{ $character }}@endif</span>
          @endforeach
        </span>
      </p>
    @endif

    <p class="site-footer-meta">{{ \App\Acf\Content::copyright() }}</p>
  </div>
</footer>
