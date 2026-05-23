@extends('layouts.app')
@section('title','Soporte')
@section('content')
<div class="dashboard-layout" x-data="{ modal: false }">
  @include('partials.sidebar')
  <div class="dashboard-main">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem">
      <div>
        <h1 style="font-size:2rem;margin-bottom:.25rem">Servicio al <span class="text-orange">Cliente</span></h1>
        <p class="text-muted">Escribenos, te respondemos pronto</p>
      </div>
      <button class="btn btn-primary" @click="modal = true">+ Nueva solicitud</button>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(count($pqrs ?? []) > 0)
      @foreach($pqrs as $item)
        <div class="pqrs-item {{ strtolower($item['status'] ?? 'open') }}">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem">
            <span class="pqrs-type {{ strtolower($item['type'] ?? 'pregunta') }}">{{ $item['type'] ?? 'Pregunta' }}</span>
            <span style="font-size:.82rem;color:var(--text-muted)">{{ isset($item['createdAt']) ? \Carbon\Carbon::parse($item['createdAt'])->locale('es')->diffForHumans() : '' }}</span>
          </div>
          <div style="font-weight:600;margin-bottom:.5rem">{{ $item['subject'] ?? '' }}</div>
          <div class="text-muted" style="font-size:.9rem;line-height:1.6">{{ $item['message'] ?? '' }}</div>
          @if(!empty($item['response']))
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:1rem;margin-top:1rem;border-left:3px solid var(--c-orange)">
              <div style="font-size:.78rem;color:var(--text-muted);margin-bottom:.3rem">Respuesta del teatro:</div>
              <div style="font-size:.9rem">{{ $item['response'] }}</div>
            </div>
          @endif
        </div>
      @endforeach
    @else
      <div style="text-align:center;padding:4rem 1rem;background:#fff;border-radius:var(--radius-lg)">
        <div style="font-size:4rem;margin-bottom:1rem">💬</div>
        <h3 style="margin-bottom:.5rem">Sin solicitudes</h3>
        <p class="text-muted">Tienes alguna pregunta o reclamo? Escribenos!</p>
      </div>
    @endif
    <div x-show="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:1rem" :style="modal ? 'display:flex' : 'display:none'" @click.self="modal=false">
      <div class="card" style="width:100%;max-width:520px;padding:2rem">
        <h2 style="font-size:1.4rem;margin-bottom:1.5rem">Nueva solicitud</h2>
        <form method="POST" action="{{ route('pqrs.store') }}">@csrf
          <div class="form-group">
            <label class="form-label">Tipo</label>
            <select name="type" class="form-input"><option value="Pregunta">Pregunta</option><option value="Queja">Queja</option><option value="Reclamo">Reclamo</option><option value="Sugerencia">Sugerencia</option></select>
          </div>
          <div class="form-group"><label class="form-label">Asunto</label><input type="text" name="subject" class="form-input" placeholder="En que te podemos ayudar?" required></div>
          <div class="form-group"><label class="form-label">Mensaje</label><textarea name="message" class="form-input" rows="5" placeholder="Cuentanos con detalle..." required style="resize:vertical"></textarea></div>
          <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary">Enviar solicitud</button>
            <button type="button" class="btn btn-ghost" @click="modal=false">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
