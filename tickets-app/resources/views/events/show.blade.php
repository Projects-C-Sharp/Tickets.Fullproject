@extends('layouts.app')
@section('title', $event['name'])
@section('content')
<div class="container" style="padding:3rem 1.5rem;max-width:1100px">
  <a href="{{ route('home') }}" class="btn btn-ghost btn-sm" style="margin-bottom:2rem">← Volver a cartelera</a>
  <div style="display:grid;grid-template-columns:1fr 2fr;gap:3rem;align-items:start">
    <div>
      @if($event['posterUrl'])
        <img src="{{ $event['posterUrl'] }}" alt="{{ $event['name'] }}" style="width:100%;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg)">
      @else
        <div style="aspect-ratio:3/4;background:var(--c-dark);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;font-size:6rem">🎭</div>
      @endif
    </div>
    <div>
      <span style="display:inline-block;background:var(--c-orange);color:#fff;padding:.3rem .9rem;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:1rem">{{ $event['type'] }}</span>
      <h1 style="font-size:clamp(2rem,4vw,3rem);margin-bottom:1rem">{{ $event['name'] }}</h1>
      <div style="display:flex;flex-wrap:wrap;gap:1.5rem;margin-bottom:1.5rem">
        <div style="display:flex;align-items:center;gap:.5rem;color:var(--text-muted)">📍 <span>{{ $event['venueName'] }}, {{ $event['venueCity'] }}</span></div>
        <div style="display:flex;align-items:center;gap:.5rem;color:var(--text-muted)">⏱ <span>{{ $event['durationMinutes'] }} minutos</span></div>
      </div>
      <p style="color:var(--text-muted);line-height:1.8;margin-bottom:2rem">{{ $event['description'] }}</p>
      <h2 style="font-size:1.4rem;margin-bottom:1.5rem">Selecciona una funcion</h2>
      @if(count($showtimes) > 0)
        <div style="display:flex;flex-direction:column;gap:1rem">
          @foreach($showtimes as $showtime)
            @php $available = $showtime['availableSeats'] > 0 && $showtime['status'] === 'Active'; $dateObj = \Carbon\Carbon::parse($showtime['startTime']); @endphp
            <div style="background:#fff;border-radius:var(--radius-md);padding:1.25rem 1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
              <div>
                <div style="font-weight:700;font-size:1.05rem">{{ $dateObj->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}</div>
                <div style="color:var(--text-muted);font-size:.9rem;margin-top:.25rem">🕐 {{ $dateObj->format('g:i A') }} &nbsp;·&nbsp; 💺 {{ $showtime['availableSeats'] }} de {{ $showtime['totalSeats'] }} disponibles</div>
              </div>
              <div style="display:flex;align-items:center;gap:1rem">
                <div style="text-align:right">
                  <div style="font-size:1.4rem;font-weight:800;color:var(--c-orange)">\${{ number_format($showtime['basePrice'], 0, ',', '.') }}</div>
                  <div style="font-size:.75rem;color:var(--text-muted)">por silla</div>
                </div>
                @if($available)
                  @auth
                    <a href="{{ route('checkout.seats', ['eventId'=>$event['id'],'showtimeId'=>$showtime['id']]) }}" class="btn btn-primary">Comprar →</a>
                  @else
                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}" class="btn btn-primary">Comprar →</a>
                  @endauth
                @else
                  <button class="btn" style="background:var(--c-smoke);color:var(--text-muted);cursor:not-allowed" disabled>Agotado</button>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="alert alert-info">No hay funciones programadas aun para este evento.</div>
      @endif
      @auth
        <div style="margin-top:2rem">
          <form method="POST" action="{{ route('favorites.toggle', $event['id']) }}">@csrf
            <button type="submit" class="btn btn-outline">
              {{ in_array($event['id'], session('favorites', [])) ? 'En Favoritos' : 'Agregar a Favoritos' }}
            </button>
          </form>
        </div>
      @endauth
    </div>
  </div>
</div>
@endsection
