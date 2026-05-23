@extends('layouts.app')
@section('title','Cartelera')
@section('content')
<section class="hero">
  <div class="hero__bg" style="background-image:url('/images/hero-theater.jpg')"></div>
  <div class="hero__content">
    <span class="hero__eyebrow">Temporada {{ date('Y') }}</span>
    <h1 class="hero__title">El arte de vivir<br><em>cada funcion</em></h1>
    <p class="hero__sub">Desde conciertos intimos hasta grandes obras de teatro.<br>Tu proxima experiencia cultural te espera.</p>
    <div class="hero__cta">
      <a href="#cartelera" class="btn btn-primary btn-lg">Ver cartelera</a>
      @guest<a href="{{ route('register') }}" class="btn btn-lg" style="color:#fff;border:2px solid rgba(255,255,255,.5);background:transparent;border-radius:var(--radius-xl)">Crear cuenta gratis</a>@endguest
    </div>
  </div>
</section>

<section class="section" id="cartelera">
  <div class="container">
    <h2 class="section-title">Cartelera <span class="text-orange">actual</span></h2>
    <p class="section-sub">Descubre todos nuestros espectaculos disponibles</p>
    @if(count($events) > 0)
      <div class="grid-events">
        @foreach($events as $event)
          <a href="{{ route('events.show', $event['id']) }}" class="card event-card" style="display:block">
            <div style="position:relative;overflow:hidden;aspect-ratio:3/4">
              @if($event['posterUrl'])
                <img src="{{ $event['posterUrl'] }}" alt="{{ $event['name'] }}" class="event-card__poster" loading="lazy">
              @else
                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--c-dark) 0%,var(--c-dark-2) 100%);display:flex;align-items:center;justify-content:center;font-size:4rem">🎭</div>
              @endif
              <span class="event-card__badge">{{ $event['type'] }}</span>
              <div class="event-card__overlay">
                <div class="event-card__title">{{ $event['name'] }}</div>
                <div class="event-card__meta">📍 {{ $event['venueName'] }}, {{ $event['venueCity'] }}<br>⏱ {{ $event['durationMinutes'] }} min</div>
                <div class="btn btn-primary btn-sm" style="margin-top:1rem;align-self:flex-start">Ver mas →</div>
              </div>
            </div>
            <div class="card-body">
              <h3 style="font-size:1.1rem;margin-bottom:.25rem">{{ $event['name'] }}</h3>
              <p class="text-muted" style="font-size:.85rem;line-height:1.5">{{ Str::limit($event['description'], 80) }}</p>
              <div style="display:flex;align-items:center;gap:.5rem;margin-top:.75rem">
                <span style="font-size:.75rem;background:var(--c-sand-lt);color:var(--c-dark);padding:.2rem .6rem;border-radius:999px;font-weight:600">{{ $event['venueCity'] }}</span>
                <span style="font-size:.75rem;color:var(--text-muted)">{{ $event['durationMinutes'] }} min</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
      @if($totalPages > 1)
        <div style="display:flex;justify-content:center;gap:.5rem;margin-top:3rem;flex-wrap:wrap">
          @for($i=1;$i<=$totalPages;$i++)
            <a href="?page={{ $i }}" class="btn {{ $currentPage==$i ? 'btn-primary' : 'btn-outline' }} btn-sm">{{ $i }}</a>
          @endfor
        </div>
      @endif
    @else
      <div style="text-align:center;padding:5rem 1rem">
        <div style="font-size:4rem;margin-bottom:1rem">🎭</div>
        <h3 style="margin-bottom:.5rem">Sin eventos activos</h3>
        <p class="text-muted">Pronto anunciaremos nuestra nueva temporada. Regresa pronto!</p>
      </div>
    @endif
  </div>
</section>
@guest
<section style="background:var(--c-dark);padding:5rem 1.5rem;text-align:center">
  <div class="container" style="max-width:600px">
    <h2 style="color:#fff;font-size:2.2rem;margin-bottom:1rem">Listo para tu proxima<br><em style="color:var(--c-orange)">experiencia cultural?</em></h2>
    <p style="color:#888;margin-bottom:2rem">Crea tu cuenta gratis y accede a tus entradas digitales, historial de compras y mucho mas.</p>
    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Crear cuenta gratis</a>
  </div>
</section>
@endguest
@endsection
