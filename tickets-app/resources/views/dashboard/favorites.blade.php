@extends('layouts.app')
@section('title','Favoritos')
@section('content')
<div class="dashboard-layout">
  @include('partials.sidebar')
  <div class="dashboard-main">
    <h1 style="font-size:2rem;margin-bottom:.25rem">Mis <span class="text-orange">Favoritos</span></h1>
    <p class="text-muted" style="margin-bottom:2rem">Eventos que marcaste para no perderte</p>
    @if(count($events ?? []) > 0)
      <div class="grid-events">
        @foreach($events as $event)
          <a href="{{ route('events.show',$event['id']) }}" class="card event-card" style="display:block">
            <div style="position:relative;overflow:hidden;aspect-ratio:3/4">
              @if($event['posterUrl'])
                <img src="{{ $event['posterUrl'] }}" alt="{{ $event['name'] }}" class="event-card__poster" loading="lazy">
              @else
                <div style="width:100%;height:100%;background:var(--c-dark);display:flex;align-items:center;justify-content:center;font-size:4rem">🎭</div>
              @endif
              <span class="event-card__badge">{{ $event['type'] }}</span>
            </div>
            <div class="card-body">
              <h3 style="font-size:1.1rem;margin-bottom:.25rem">{{ $event['name'] }}</h3>
              <p class="text-muted" style="font-size:.85rem">{{ Str::limit($event['description'],80) }}</p>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:1rem">
                <span style="font-size:.75rem;color:var(--text-muted)">{{ $event['venueCity'] }}</span>
                <form method="POST" action="{{ route('favorites.toggle',$event['id']) }}" style="display:inline">@csrf
                  <button type="submit" class="btn btn-ghost btn-sm" style="color:#e74c3c" onclick="event.preventDefault();this.closest('form').submit()">Quitar ❤️</button>
                </form>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    @else
      <div style="text-align:center;padding:4rem 1rem;background:#fff;border-radius:var(--radius-lg)">
        <div style="font-size:4rem;margin-bottom:1rem">🤍</div>
        <h3 style="margin-bottom:.5rem">Sin favoritos aun</h3>
        <p class="text-muted" style="margin-bottom:2rem">Marca eventos desde la cartelera para no perdertelos</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Explorar cartelera</a>
      </div>
    @endif
  </div>
</div>
@endsection
