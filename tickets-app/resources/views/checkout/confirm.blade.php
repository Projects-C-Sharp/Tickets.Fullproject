@extends('layouts.app')
@section('title', 'Confirmar compra')

@section('content')
<div class="container" style="max-width:700px;padding:2rem 1.5rem">

  {{-- BREADCRUMB --}}
  <nav style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:var(--text-muted);margin-bottom:1.5rem">
    <a href="{{ route('home') }}" style="color:var(--text-muted)">Cartelera</a>
    <span>›</span>
    <a href="{{ route('events.show', $event['id']) }}" style="color:var(--text-muted)">{{ $event['name'] }}</a>
    <span>›</span>
    <span style="color:var(--c-orange);font-weight:600">Confirmar compra</span>
  </nav>

  <h1 style="font-size:2rem;margin-bottom:.25rem">Confirmar <span class="text-orange">compra</span></h1>
  <p class="text-muted" style="margin-bottom:2rem">Revisa los detalles antes de pagar</p>

  {{-- EVENTO --}}
  <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
    <div style="display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap">
      @if($event['posterUrl'])
        <img src="{{ $event['posterUrl'] }}" alt="{{ $event['name'] }}"
          style="width:80px;height:110px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0">
      @else
        <div style="width:80px;height:110px;background:var(--c-dark);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0">🎭</div>
      @endif
      <div>
        <div style="font-size:.75rem;background:var(--c-orange);color:#fff;padding:.2rem .7rem;border-radius:999px;display:inline-block;margin-bottom:.5rem;font-weight:700">
          {{ $event['type'] }}
        </div>
        <h2 style="font-size:1.3rem;margin-bottom:.4rem">{{ $event['name'] }}</h2>
        <div style="color:var(--text-muted);font-size:.88rem;line-height:1.8">
          📅 {{ \Carbon\Carbon::parse($showtime['startTime'])->locale('es')->isoFormat('dddd D [de] MMMM YYYY · h:mm A') }}<br>
          📍 {{ $event['venueName'] }}, {{ $event['venueCity'] }}<br>
          ⏱ {{ $event['durationMinutes'] }} minutos
        </div>
      </div>
    </div>
  </div>

  {{-- ASIENTOS --}}
  <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
    <h3 style="font-size:1.05rem;margin-bottom:1rem;color:var(--c-dark)">
      🪑 Asientos seleccionados ({{ count($selected) }})
    </h3>

    @foreach($selected as $seat)
      @php $label = $seat['label'] ?? ($seat['row'].''.$seat['number']); @endphp
      <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem 0;border-bottom:1px dashed var(--border)">
        <div style="display:flex;align-items:center;gap:.75rem">
          <div style="
            width:36px;height:36px;border-radius:var(--radius-sm);
            background:{{ in_array($seat['type'], ['Premium','VIP',1,2]) ? 'var(--c-sand)' : 'var(--c-smoke)' }};
            display:flex;align-items:center;justify-content:center;
            font-weight:800;font-size:.85rem;color:var(--c-dark);flex-shrink:0
          ">{{ $label }}</div>
          <div>
            <div style="font-weight:600;font-size:.95rem">Fila {{ $seat['row'] ?? substr($label,0,1) }}, Silla {{ $seat['number'] ?? substr($label,1) }}</div>
            <div style="font-size:.78rem;color:var(--text-muted)">
              {{ in_array($seat['type'], ['Premium','VIP',1,2]) ? 'Premium' : 'Estándar' }}
            </div>
          </div>
        </div>
        <span style="font-weight:700;font-size:1rem">${{ number_format($showtime['basePrice'],0,',','.') }}</span>
      </div>
    @endforeach

    {{-- Subtotales --}}
    <div style="margin-top:1rem">
      <div style="display:flex;justify-content:space-between;font-size:.9rem;color:var(--text-muted);margin-bottom:.3rem">
        <span>Subtotal ({{ count($selected) }} entrada{{ count($selected) > 1 ? 's' : '' }})</span>
        <span>${{ number_format(count($selected) * $showtime['basePrice'],0,',','.') }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:.9rem;color:var(--text-muted);margin-bottom:.75rem">
        <span>Cargos por servicio</span>
        <span>$0</span>
      </div>
      <div style="display:flex;justify-content:space-between;font-weight:900;font-size:1.3rem;color:var(--c-orange);border-top:2px solid var(--border);padding-top:.75rem">
        <span>Total</span>
        <span>${{ number_format(count($selected) * $showtime['basePrice'],0,',','.') }}</span>
      </div>
    </div>
  </div>

  {{-- PAGO SIMULADO --}}
  <div class="card" style="padding:1.5rem;margin-bottom:1.5rem" x-data="{ method: 'CreditCard' }">
    <h3 style="font-size:1.05rem;margin-bottom:1rem">💳 Método de pago</h3>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1rem">
      @foreach(['CreditCard' => '💳 Tarjeta de crédito', 'DebitCard' => '🏦 Tarjeta débito', 'PSE' => '🏛️ PSE', 'Cash' => '💵 Efectivo'] as $val => $label)
        <label style="display:flex;align-items:center;gap:.6rem;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius-md);cursor:pointer;transition:all .2s"
          :style="method === '{{ $val }}' ? 'border-color:var(--c-orange);background:rgba(253,123,65,.06)' : ''">
          <input type="radio" name="method_display" value="{{ $val }}" x-model="method" style="accent-color:var(--c-orange)">
          <span style="font-size:.9rem;font-weight:500">{{ $label }}</span>
        </label>
      @endforeach
    </div>

    <div style="background:#e3f2fd;border-radius:var(--radius-md);padding:.9rem 1rem;font-size:.82rem;color:#1565c0;display:flex;gap:.5rem;margin-bottom:1.5rem">
      <span>ℹ️</span>
      <span>El pago es <strong>simulado</strong>. No se realizarán cargos reales a tu cuenta.</span>
    </div>

    <form method="POST" action="{{ route('checkout.pay') }}" id="pay-form">
      @csrf
      <input type="hidden" name="payment_method" x-bind:value="method">

      <button type="submit" class="btn btn-primary btn-lg btn-full" id="pay-btn"
        onclick="document.getElementById('pay-btn').disabled=true; document.getElementById('pay-btn').textContent='Procesando...'; document.getElementById('pay-form').submit();">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Pagar ${{ number_format(count($selected) * $showtime['basePrice'],0,',','.') }} ahora
      </button>
    </form>
  </div>

  <a href="javascript:history.back()" class="btn btn-ghost btn-full" style="justify-content:center">
    ← Volver a seleccionar asientos
  </a>
</div>
@endsection
