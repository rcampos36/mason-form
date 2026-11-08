@php
  $title = get_the_title();
  $kicker = $eyebrow ?? '';
  $sentence = $heading ?? '';
  $description = $text ?? '';
  $filters = \App\Acf\Content::projectFilters();
@endphp

<section class="work-hero">
  @if ($title !== '')
    <h1 class="work-hero-title">{{ $title }}</h1>
  @endif

  <div class="work-hero-rule"></div>

  @if ($kicker !== '')
    <div class="work-hero-meta">
      <p class="work-hero-kicker">{{ $kicker }}</p>
      <p class="work-hero-index">[01]</p>
    </div>
  @endif

  <div class="work-hero-body">
    @if ($sentence !== '')
      <h2 class="work-hero-heading">{{ $sentence }}</h2>
    @endif

    <div class="work-hero-row">
      @if ($description !== '')
        <p class="work-hero-text">{{ $description }}</p>
      @endif

      @if (count($filters) > 1)
        <nav class="work-filters" aria-label="{{ __('Project categories', 'sage') }}">
          @foreach ($filters as $filter)
            <a href="{{ $filter['url'] }}" @if ($filter['current']) aria-current="page" @endif>{{ $filter['name'] }}</a>
          @endforeach
        </nav>
      @endif
    </div>
  </div>
</section>
