@extends('layouts.app')
@section('title', 'Seleccionar asientos — ' . $event['name'])

@section('content')
<div x-data="seatPicker()" x-init="init()" class="container" style="max-width:1100px;padding:2rem 1.5rem">

  {{-- BREADCRUMB --}}
  <nav style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:var(--text-muted);margin-bottom:1.5rem">
    <a href="{{ route('home') }}" style="color:var(--text-muted)">Cartelera</a>
    <span>›</span>
    <a href="{{ route('events.show', $event['id']) }}" style="color:var(--text-muted)">{{ $event['name'] }}</a>
    <span>›</span>
    <span style="color:var(--c-orange);font-weight:600">Seleccionar asientos</span>
  </nav>

  {{-- HEADER --}}
  <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-start;margin-bottom:2rem">
    <div style="flex:1;min-width:240px">
      <h1 style="font-size:1.8rem;margin-bottom:.3rem">{{ $event['name'] }}</h1>
      <div style="display:flex;flex-wrap:wrap;gap:1rem;color:var(--text-muted);font-size:.9rem">
        <span>📅 {{ \Carbon\Carbon::parse($showtime['startTime'])->locale('es')->isoFormat('ddd D MMM YYYY · h:mm A') }}</span>
        <span>📍 {{ $event['venueName'] }}, {{ $event['venueCity'] }}</span>
        <span>⏱ {{ $event['durationMinutes'] }} min</span>
      </div>
    </div>
    <div style="background:var(--c-orange);color:#fff;padding:.75rem 1.5rem;border-radius:var(--radius-md);text-align:center;flex-shrink:0">
      <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Precio base</div>
      <div style="font-size:1.6rem;font-weight:900">${{ number_format($showtime['basePrice'],0,',','.') }}</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 320px;gap:2rem;align-items:start">

    {{-- ── MAPA DE ASIENTOS ── --}}
    <div class="card" style="padding:1.5rem">

      {{-- Pantalla --}}
      <div style="position:relative;margin-bottom:2rem">
        <div style="height:8px;background:linear-gradient(90deg,transparent,var(--c-smoke),transparent);border-radius:4px;margin-bottom:.4rem"></div>
        <div style="text-align:center;font-size:.75rem;font-weight:700;letter-spacing:.2em;color:var(--text-muted);text-transform:uppercase">PANTALLA / ESCENARIO</div>
      </div>

      {{-- Leyenda --}}
      <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:1.5rem;margin-bottom:2rem">
        <div style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--text-muted)">
          <div style="width:26px;height:26px;border-radius:6px 6px 0 0;background:var(--c-smoke);border-bottom:3px solid #bbb"></div> Disponible
        </div>
        <div style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--text-muted)">
          <div style="width:26px;height:26px;border-radius:6px 6px 0 0;background:#4caf50;border-bottom:3px solid #388e3c"></div> Tu butaca
        </div>
        <div style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--text-muted)">
          <div style="width:26px;height:26px;border-radius:6px 6px 0 0;background:var(--c-sand);border-bottom:3px solid #c8964a"></div> Premium
        </div>
        <div style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--text-muted)">
          <div style="width:26px;height:26px;border-radius:6px 6px 0 0;background:#d32f2f;border-bottom:3px solid #b71c1c;opacity:.5"></div> No disponible
        </div>
      </div>

      {{-- MAPA --}}
      <div style="overflow-x:auto;padding-bottom:1rem">
        <div style="min-width:360px">
          @php
            $byRow = collect($seats)->groupBy('row')->sortKeys();
          @endphp

          @foreach($byRow as $rowLabel => $rowSeats)
            <div style="display:flex;align-items:center;gap:.35rem;margin-bottom:.4rem;justify-content:center">
              {{-- Etiqueta fila izquierda --}}
              <span style="width:22px;font-size:.72rem;font-weight:700;color:var(--text-muted);text-align:right;flex-shrink:0">{{ $rowLabel }}</span>

              {{-- Asientos --}}
              @foreach($rowSeats->sortBy('number') as $seat)
                @php
                  $sold     = in_array($seat['status'], ['Sold', 'Occupied', 2, 1]);
                  $reserved = in_array($seat['status'], ['Reserved', 1]) && !$sold;
                  $premium  = in_array($seat['type'], ['Premium', 'VIP', 1, 2]);
                  $seatJson = json_encode(['id' => $seat['id'], 'label' => $seat['label'] ?? ($rowLabel.$seat['number']), 'type' => $premium ? 'Premium' : 'Standard', 'row' => $rowLabel, 'number' => $seat['number']]);
                @endphp
                <button
                  type="button"
                  data-seat="{{ $seatJson }}"
                  data-available="{{ $sold || $reserved ? 'false' : 'true' }}"
                  @click="toggle($el)"
                  :disabled="{{ $sold || $reserved ? 'true' : 'false' }}"
                  title="{{ ($sold ? 'No disponible' : ($reserved ? 'Reservado' : ($premium ? 'Premium · ' : 'Estándar · '))) }}Fila {{ $rowLabel }}, {{ $seat['number'] }}"
                  style="
                    width:28px;height:28px;
                    border-radius:6px 6px 2px 2px;
                    border:none;cursor:{{ $sold || $reserved ? 'not-allowed' : 'pointer' }};
                    font-size:.62rem;font-weight:700;
                    transition:transform .12s,box-shadow .12s;
                    position:relative;
                    background: {{ $sold ? '#d32f2f' : ($reserved ? '#e57373' : ($premium ? 'var(--c-sand)' : 'var(--c-smoke)')) }};
                    border-bottom: 3px solid {{ $sold ? '#b71c1c' : ($reserved ? '#c62828' : ($premium ? '#c8964a' : '#b0afad')) }};
                    opacity: {{ $sold || $reserved ? '.55' : '1' }};
                    color: {{ $sold || $reserved ? '#fff' : 'var(--c-dark)' }};
                  "
                  :style="isSelected({{ $seat['id'] }}) ? 'background:#4caf50;border-bottom-color:#388e3c;color:#fff;transform:scale(1.15);box-shadow:0 4px 12px rgba(76,175,80,.5)' : ''"
                  :class="!{{ $sold || $reserved ? 'true' : 'false' }} ? 'seat-btn' : ''"
                >{{ $seat['number'] }}</button>
              @endforeach

              {{-- Etiqueta fila derecha --}}
              <span style="width:22px;font-size:.72rem;font-weight:700;color:var(--text-muted);flex-shrink:0">{{ $rowLabel }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ── PANEL LATERAL: RESUMEN ── --}}
    <div style="position:sticky;top:80px">
      <div class="card" style="padding:1.5rem;margin-bottom:1rem">
        <h3 style="font-size:1.1rem;margin-bottom:1rem;border-bottom:1px solid var(--border);padding-bottom:.75rem">
          Tu selección
        </h3>

        {{-- Lista de asientos seleccionados --}}
        <div x-show="selected.length === 0" style="text-align:center;padding:1.5rem 0;color:var(--text-muted);font-size:.9rem">
          <div style="font-size:2rem;margin-bottom:.5rem">🪑</div>
          Toca un asiento disponible para seleccionarlo
        </div>

        <div x-show="selected.length > 0">
          <template x-for="s in selected" :key="s.id">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px dashed var(--border)">
              <div>
                <span style="font-weight:700;font-size:.95rem" x-text="'Fila ' + s.row + ' · Silla ' + s.number"></span>
                <span style="display:block;font-size:.75rem;color:var(--text-muted)" x-text="s.type"></span>
              </div>
              <div style="display:flex;align-items:center;gap:.5rem">
                <span style="font-weight:600;font-size:.9rem">${{ number_format($showtime['basePrice'],0,',','.') }}</span>
                <button type="button" @click="remove(s.id)" style="background:none;border:none;cursor:pointer;color:#e53935;font-size:1rem;padding:0;line-height:1">✕</button>
              </div>
            </div>
          </template>
        </div>

        {{-- Total --}}
        <div x-show="selected.length > 0" style="margin-top:1rem">
          <div style="display:flex;justify-content:space-between;margin-bottom:.25rem;font-size:.85rem;color:var(--text-muted)">
            <span x-text="selected.length + ' entrada(s)'"></span>
            <span x-text="'$' + (selected.length * {{ $showtime['basePrice'] }}).toLocaleString('es-CO')"></span>
          </div>
          <div style="display:flex;justify-content:space-between;font-weight:800;font-size:1.15rem;color:var(--c-orange);border-top:2px solid var(--border);padding-top:.5rem;margin-top:.5rem">
            <span>Total</span>
            <span x-text="'$' + (selected.length * {{ $showtime['basePrice'] }}).toLocaleString('es-CO')"></span>
          </div>
        </div>
      </div>

      {{-- BOTÓN CONTINUAR --}}
      <form method="POST" action="{{ route('checkout.reserve') }}" id="reserve-form">
        @csrf
        <input type="hidden" name="event_id"    value="{{ $event['id'] }}">
        <input type="hidden" name="showtime_id" value="{{ $showtime['id'] }}">
        <input type="hidden" name="seat_ids"    x-bind:value="JSON.stringify(selected.map(s => s.id))">

        <button
          type="submit"
          class="btn btn-primary btn-lg btn-full"
          :disabled="selected.length === 0"
          :style="selected.length === 0 ? 'opacity:.45;cursor:not-allowed' : ''"
          @click.prevent="submitForm()"
        >
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          Continuar al pago
        </button>
      </form>

      <a href="{{ route('events.show', $event['id']) }}" class="btn btn-ghost btn-full" style="margin-top:.5rem;justify-content:center">
        ← Cambiar función
      </a>

      {{-- Info reserva --}}
      <div style="background:#fff3e0;border-radius:var(--radius-md);padding:1rem;margin-top:1rem;font-size:.82rem;color:#e65100;display:flex;gap:.5rem;align-items:flex-start">
        <span>⏳</span>
        <span>Tus asientos se reservan por <strong>5 minutos</strong> mientras completas el pago.</span>
      </div>
    </div>
  </div>
</div>

<style>
.seat-btn:not([disabled]):hover { transform: scale(1.12) !important; box-shadow: 0 4px 12px rgba(0,0,0,.15) !important; }
</style>

<script>
function seatPicker() {
  return {
    selected: [],

    init() {
      // Restaurar selección de sesión si existe
    },

    isSelected(id) {
      return this.selected.some(s => s.id === id);
    },

    toggle(el) {
      if (el.dataset.available === 'false') return;
      const seat = JSON.parse(el.dataset.seat);
      const idx  = this.selected.findIndex(s => s.id === seat.id);
      if (idx === -1) {
        this.selected.push(seat);
      } else {
        this.selected.splice(idx, 1);
      }
    },

    remove(id) {
      this.selected = this.selected.filter(s => s.id !== id);
    },

    submitForm() {
      if (this.selected.length === 0) return;
      document.getElementById('reserve-form').submit();
    }
  }
}
</script>
@endsection
