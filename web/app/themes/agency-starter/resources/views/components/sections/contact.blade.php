@php
  $heading = \App\Acf\Content::text('heading', $section);
  $html = \App\Acf\Content::rich($section['text'] ?? null);
  $align = is_string($section['section_align'] ?? null) ? $section['section_align'] : 'left';
  $showDetails = \App\Acf\Content::flag($section, 'show_details', true);
  $showSocial = \App\Acf\Content::flag($section, 'show_social', true);
  $showForm = \App\Acf\Content::flag($section, 'show_form');
  $email = $showDetails ? \App\Acf\Content::contact('email') : '';
  $phone = $showDetails ? \App\Acf\Content::contact('phone') : '';
  $address = $showDetails ? \App\Acf\Content::contact('address') : '';
  $social = $showSocial ? \App\Acf\Content::socialLinks() : [];
  $form = $showForm ? \App\Acf\Content::formHtml($section['form'] ?? null) : '';
  $map = \App\Acf\Content::embed($section['map'] ?? null);
@endphp

@if ($heading !== '' || $html !== '' || $email !== '' || $phone !== '' || $address !== '' || $social || $form !== '' || $map !== '')
  <x-section layout="wide" :tone="$section['section_background'] ?? null" :align="$align">
    <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
      <div class="grid content-start gap-8">
        <x-section-intro
          :heading="$heading"
          :html="$html"
          :align="$align"
          :level="(int) ($sectionIndex ?? 0) === 0 ? 1 : 2"
          :size="(int) ($sectionIndex ?? 0) === 0 ? 'display' : '1'"
          :number="$sectionNumber ?? ''"
        />

        @if ($email !== '' || $phone !== '' || $address !== '')
          <div class="grid gap-3 text-body">
            @if ($email !== '' && ($mailto = sanitize_email($email)) !== '')
              <a class="{{ \App\Design::classes('link', 'inline') }}" href="mailto:{{ $mailto }}">{{ $mailto }}</a>
            @endif
            @if ($phone !== '')
              <a class="{{ \App\Design::classes('link', 'inline') }}" href="{{ \App\Acf\Content::phoneHref($phone) }}">{{ $phone }}</a>
            @endif
            @if ($address !== '')
              <p>{!! \App\Acf\Content::addressHtml($address) !!}</p>
            @endif
          </div>
        @endif

        @if ($social)
          <ul class="flex flex-wrap gap-x-6 gap-y-2">
            @foreach ($social as $link)
              <li>
                <a class="{{ \App\Design::classes('link', 'filter') }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
              </li>
            @endforeach
          </ul>
        @endif
      </div>

      @if ($form !== '' || $map !== '')
        <div class="grid content-start gap-10">
          @if ($form !== '')
            {!! $form !!}
          @endif

          @if ($map !== '')
            <div class="aspect-video overflow-hidden bg-surface [&_iframe]:h-full [&_iframe]:w-full">
              {!! $map !!}
            </div>
          @endif
        </div>
      @endif
    </div>
  </x-section>
@endif
