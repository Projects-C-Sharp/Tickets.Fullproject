@extends('layouts.app')
@section('title', '¡Pago exitoso!')

@section('content')
<div class="container" style="max-width:720px;padding:3rem 1.5rem">

  {{-- HERO SUCCESS --}}
  <div style="text-align:center;margin-bottom:3rem">
    <div style="
      width:90px;height:90px;border-radius:50%;
      background:linear-gradient(135deg,#4caf50,#66bb6a);
      display:flex;align-items:center;justify-content:center;
      margin:0 auto 1.5rem;
      box-shadow:0 8px 32px rgba(76,175,80,.4);
      font-size:2.5rem
    ">✓</div>
    <h1 style="font-size:2.2rem;margin-bottom:.5rem">¡Pago <span class="text-orange">exitoso!</span></h1>
    <p style="color:var(--text-muted);font-size:1.05rem">
      Tus entradas están listas. Guárdalas o descárgalas para el día del evento.
    </p>
    @if($order)
      <div style="display:inline-block;background:var(--c-smoke);border-radius:999px;padding:.4rem 1.2rem;margin-top:.75rem;font-size:.85rem;color:var(--text-muted)">
        Orden #{{ $order['id'] }} · {{ \Carbon\Carbon::parse($order['createdAt'])->locale('es')->isoFormat('D MMM YYYY, h:mm A') }}
      </div>
    @endif
  </div>

  @if($order && !empty($order['items']))
    {{-- TICKETS CON QR --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem;margin-bottom:2.5rem">
      @foreach($order['items'] as $item)
        @php
          $isPast  = \Carbon\Carbon::parse($item['showtimeStart'])->isPast();
          $qr      = $item['qrCode'] ?? null;
          // El QR de la API es base64 del string "TS:{id}:{ticks}"
          // Lo mostramos como imagen QR usando una API externa de generación
          $qrText  = $qr ? base64_decode($qr) : '';
          $qrImgUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrText ?: 'Ticket-'.$item['id']);
        @endphp

        {{-- TICKET CARD --}}
        <div style="
          background:#fff;border-radius:var(--radius-lg);
          box-shadow:var(--shadow-md);overflow:hidden;
          display:grid;grid-template-columns:8px 1fr;
        ">
          <div style="background:var(--c-orange)"></div>
          <div style="padding:1.5rem">
            <div style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:flex-start">

              {{-- INFO --}}
              <div style="flex:1;min-width:200px">
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem">
                  <span style="
                    background:{{ $isPast ? 'var(--c-smoke)' : '#e8f5e9' }};
                    color:{{ $isPast ? 'var(--text-muted)' : '#2e7d32' }};
                    padding:.25rem .75rem;border-radius:999px;font-size:.72rem;font-weight:700;text-transform:uppercase
                  ">{{ $isPast ? 'Asistido' : '✓ Próximo' }}</span>
                  <span style="background:#e8f5e9;color:#2e7d32;padding:.25rem .75rem;border-radius:999px;font-size:.72rem;font-weight:700;text-transform:uppercase">
                    Pagado
                  </span>
                </div>

                <h3 style="font-family:var(--font-display);font-size:1.2rem;margin-bottom:.5rem">
                  {{ $item['eventName'] }}
                </h3>

                <div style="color:var(--text-muted);font-size:.85rem;line-height:1.9">
                  📅 {{ \Carbon\Carbon::parse($item['showtimeStart'])->locale('es')->isoFormat('dddd D [de] MMMM YYYY') }}<br>
                  🕐 {{ \Carbon\Carbon::parse($item['showtimeStart'])->format('g:i A') }}<br>
                  💺 Asiento: <strong style="color:var(--c-dark)">{{ $item['seatLabel'] }}</strong><br>
                  💳 Valor: <strong style="color:var(--c-dark)">${{ number_format($item['pricePaid'],0,',','.') }}</strong>
                </div>

                {{-- Código de referencia --}}
                @if($qr)
                  <div style="margin-top:.75rem;background:var(--bg);border-radius:var(--radius-sm);padding:.6rem .9rem;font-size:.78rem;color:var(--text-muted);font-family:monospace;border:1px dashed var(--border)">
                    Ref: {{ strtoupper(substr(base64_encode($qr), 0, 16)) }}
                  </div>
                @endif
              </div>

              {{-- QR CODE --}}
              @if($qr)
                <div style="flex-shrink:0;text-align:center">
                  <div style="
                    background:#fff;border:2px solid var(--c-smoke);
                    border-radius:var(--radius-md);padding:.75rem;
                    display:inline-block;
                  ">
                    <img
                      src="{{ $qrImgUrl }}"
                      alt="Código QR Ticket"
                      width="130" height="130"
                      style="display:block;border-radius:4px"
                      loading="lazy"
                    >
                  </div>
                  <div style="font-size:.72rem;color:var(--text-muted);margin-top:.4rem;max-width:130px">
                    Presenta este QR en la entrada
                  </div>
                  <a
                    href="{{ $qrImgUrl }}&format=png"
                    download="ticket-{{ $item['seatLabel'] }}.png"
                    class="btn btn-outline btn-sm"
                    style="margin-top:.5rem;font-size:.75rem;padding:.35rem .8rem"
                    target="_blank"
                  >⬇ Descargar</a>
                </div>
              @endif

            </div>
          </div>
        </div>

      @endforeach
    </div>

    {{-- RESUMEN TOTAL --}}
    <div class="card" style="padding:1.5rem;margin-bottom:2rem">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
        <div>
          <div style="font-size:.85rem;color:var(--text-muted)">Total pagado</div>
          <div style="font-size:1.8rem;font-weight:900;color:var(--c-orange)">${{ number_format($order['total'],0,',','.') }}</div>
        </div>
        <div style="text-align:right">
          <div style="font-size:.85rem;color:var(--text-muted)">{{ count($order['items']) }} entrada(s)</div>
          <div style="font-size:.9rem;color:#4caf50;font-weight:600">✓ Pago confirmado</div>
        </div>
      </div>
    </div>

  @else
    <div class="alert alert-info">No se encontraron detalles de la orden.</div>
  @endif

  {{-- ACCIONES --}}
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center">
    <a href="{{ route('orders.index') }}" class="btn btn-primary btn-lg">
      🎟 Ver todas mis entradas
    </a>
    <a href="{{ route('home') }}" class="btn btn-outline btn-lg">
      Volver a la cartelera
    </a>
  </div>
</div>
@endsection
