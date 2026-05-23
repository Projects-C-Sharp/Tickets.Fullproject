@extends('layouts.app')
@section('title', 'Mis Entradas')

@section('content')
<div class="dashboard-layout">
  @include('partials.sidebar')

  <div class="dashboard-main">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem">
      <div>
        <h1 style="font-size:2rem;margin-bottom:.25rem">Mis <span class="text-orange">Entradas</span></h1>
        <p class="text-muted">Historial de compras y boletas digitales</p>
      </div>
      <a href="{{ route('home') }}" class="btn btn-primary btn-sm">+ Comprar más</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    @if(count($orders) > 0)

      {{-- Tabs: Próximas / Pasadas --}}
      <div x-data="{ tab: 'upcoming' }" style="margin-bottom:2rem">
        <div style="display:flex;gap:.5rem;background:var(--c-smoke);padding:.25rem;border-radius:var(--radius-xl);width:fit-content;margin-bottom:1.5rem">
          <button type="button" @click="tab='upcoming'"
            :class="tab==='upcoming' ? 'btn btn-primary btn-sm' : 'btn btn-ghost btn-sm'"
            style="border-radius:var(--radius-xl)">
            🎭 Próximas
          </button>
          <button type="button" @click="tab='past'"
            :class="tab==='past' ? 'btn btn-dark btn-sm' : 'btn btn-ghost btn-sm'"
            style="border-radius:var(--radius-xl)">
            📁 Pasadas
          </button>
        </div>

        @foreach($orders as $order)
          @foreach($order['items'] as $item)
            @php
              $isPast   = \Carbon\Carbon::parse($item['showtimeStart'])->isPast();
              $qr       = $item['qrCode'] ?? null;
              $qrText   = $qr ? base64_decode($qr) : '';
              $qrImgUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($qrText ?: 'Ticket-'.$item['id']);
              $ref      = $qr ? strtoupper(substr(base64_encode($qr), 0, 12)) : 'N/A';
            @endphp

            <div x-show="tab === '{{ $isPast ? 'past' : 'upcoming' }}'"
                style="display:grid;grid-template-columns:8px 1fr;background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:1rem;transition:box-shadow .2s"
                @mouseenter="$el.style.boxShadow='var(--shadow-md)'"
                @mouseleave="$el.style.boxShadow='var(--shadow-sm)'">

              {{-- Franja color --}}
              <div style="background:{{ $isPast ? 'var(--c-smoke)' : 'var(--c-orange)' }}"></div>

              <div style="padding:1.25rem 1.5rem">
                <div style="display:flex;flex-wrap:wrap;gap:1.25rem;align-items:center">

                  {{-- INFO PRINCIPAL --}}
                  <div style="flex:1;min-width:180px">
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:.6rem">
                      <span style="background:{{ $isPast ? 'var(--c-smoke)' : '#e8f5e9' }};color:{{ $isPast ? 'var(--text-muted)' : '#2e7d32' }};padding:.2rem .7rem;border-radius:999px;font-size:.7rem;font-weight:700;text-transform:uppercase">
                        {{ $isPast ? '✓ Asistido' : '⏳ Próximo' }}
                      </span>
                      @if($order['status'] === 'Paid' || $order['status'] == 1)
                        <span style="background:#e8f5e9;color:#2e7d32;padding:.2rem .7rem;border-radius:999px;font-size:.7rem;font-weight:700">Pagado</span>
                      @endif
                    </div>

                    <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;margin-bottom:.4rem">{{ $item['eventName'] }}</div>

                    <div style="font-size:.83rem;color:var(--text-muted);line-height:1.8">
                      📅 {{ \Carbon\Carbon::parse($item['showtimeStart'])->locale('es')->isoFormat('ddd D MMM YYYY · h:mm A') }}<br>
                      💺 <strong style="color:var(--c-dark)">Asiento {{ $item['seatLabel'] }}</strong>
                      &nbsp;·&nbsp;
                      💳 <strong style="color:var(--c-dark)">${{ number_format($item['pricePaid'],0,',','.') }}</strong>
                    </div>

                    <div style="margin-top:.5rem;font-size:.75rem;color:var(--text-muted);font-family:monospace;background:var(--bg);padding:.3rem .6rem;border-radius:4px;display:inline-block;border:1px dashed var(--border)">
                      REF: {{ $ref }}
                    </div>
                  </div>

                  {{-- QR CODE --}}
                  @if($qr && !$isPast)
                    <div style="flex-shrink:0;text-align:center" x-data="{ show: false }">
                      {{-- Preview pequeño --}}
                      <div @click="show=true" style="cursor:pointer;position:relative;display:inline-block">
                        <img src="{{ $qrImgUrl }}" alt="QR" width="80" height="80"
                          style="border-radius:var(--radius-sm);border:2px solid var(--c-smoke);display:block">
                        <div style="position:absolute;inset:0;background:rgba(0,0,0,.04);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;opacity:0;transition:.2s"
                          @mouseenter="$el.style.opacity=1" @mouseleave="$el.style.opacity=0">
                          <span style="background:rgba(0,0,0,.6);color:#fff;font-size:.7rem;padding:.2rem .5rem;border-radius:4px">Ver</span>
                        </div>
                      </div>
                      <div style="font-size:.7rem;color:var(--text-muted);margin-top:.3rem">Ver QR</div>

                      {{-- MODAL QR GRANDE --}}
                      <div x-show="show" @click.self="show=false"
                        style="position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem"
                        x-cloak>
                        <div style="background:#fff;border-radius:var(--radius-lg);padding:2rem;max-width:380px;width:100%;text-align:center;box-shadow:var(--shadow-lg)">
                          <h2 style="font-size:1.3rem;margin-bottom:.5rem">Tu entrada digital</h2>
                          <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:1.5rem">
                            {{ $item['eventName'] }} · Asiento <strong>{{ $item['seatLabel'] }}</strong>
                          </p>

                          <div style="background:var(--bg);border-radius:var(--radius-md);padding:1.25rem;display:inline-block;margin-bottom:1rem;border:2px solid var(--c-smoke)">
                            <img src="{{ str_replace('180x180','240x240',$qrImgUrl) }}" alt="QR Ticket" width="220" height="220" style="display:block;border-radius:4px">
                          </div>

                          <div style="background:#f5f5f5;border-radius:var(--radius-sm);padding:.6rem;font-family:monospace;font-size:.78rem;color:var(--text-muted);margin-bottom:1rem;word-break:break-all">
                            REF: {{ $ref }}
                          </div>

                          <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:1.5rem">
                            📅 {{ \Carbon\Carbon::parse($item['showtimeStart'])->locale('es')->isoFormat('D [de] MMMM YYYY · h:mm A') }}
                          </div>

                          <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap">
                            <a href="{{ $qrImgUrl }}&format=png" download="ticket-{{ $item['seatLabel'] }}.png"
                              class="btn btn-primary btn-sm" target="_blank">⬇ Descargar QR</a>
                            <button @click="show=false" class="btn btn-ghost btn-sm">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  @elseif($isPast)
                    <div style="width:80px;height:80px;background:var(--c-smoke);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;opacity:.5">
                      <span style="font-size:1.5rem">✓</span>
                    </div>
                  @endif

                </div>
              </div>
            </div>
          @endforeach
        @endforeach
      </div>

    @else
      <div style="text-align:center;padding:5rem 1rem;background:#fff;border-radius:var(--radius-lg)">
        <div style="font-size:4rem;margin-bottom:1rem">🎟️</div>
        <h3 style="margin-bottom:.5rem">Aún no tienes entradas</h3>
        <p class="text-muted" style="margin-bottom:2rem">Explora nuestra cartelera y compra tu primera entrada</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Ver cartelera</a>
      </div>
    @endif
  </div>
</div>
@endsection
