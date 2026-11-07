<span class="home-approach-num">{{ $number }}</span>
<span class="home-approach-stage">
  @if ($card['title'] !== '')
    <span class="home-approach-title">{{ $card['title'] }}</span>
  @endif
  @if ($card['text'] !== '')
    <span class="home-approach-sub">{{ $card['text'] }}</span>
  @endif
</span>
<span class="home-approach-meta home-approach-deliverables">{{ $card['deliverables'] }}</span>
<span class="home-approach-meta home-approach-duration">{{ $card['duration'] }}</span>
<span class="home-approach-meta home-approach-team">{{ $card['team'] }}</span>
<span class="home-approach-chevron" @if (! $openable) hidden @endif>
  <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false">
    <path d="M3 6.5 8 11.5 13 6.5" fill="none" stroke="currentColor" stroke-width="1.25" />
  </svg>
</span>
