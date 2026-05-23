<?php

namespace App\Http\Controllers;

use App\Http\Services\ApiService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private ApiService $api) {}

    // GET /my-tickets
    public function index()
    {
        $res    = $this->api->getOrders();
        $orders = $res->successful() ? ($res->json('data.items') ?? []) : [];
        return view('dashboard.orders', compact('orders'));
    }

    // GET /my-tickets/{id}
    public function show(int $id)
    {
        $res   = $this->api->getOrder($id);
        $order = $res->successful() ? $res->json('data') : null;
        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Orden no encontrada.');
        }
        return view('dashboard.order-detail', compact('order'));
    }

    // GET /events/{eventId}/showtimes/{showtimeId}/seats
    public function seats(int $eventId, int $showtimeId)
    {
        $evRes = $this->api->getEvent($eventId);
        if (!$evRes->successful()) abort(404);
        $event = $evRes->json('data');
        if (!$event) abort(404);

        $stRes     = $this->api->getShowtimes($eventId);
        $showtimes = $stRes->successful() ? ($stRes->json('data.items') ?? []) : [];
        $showtime  = collect($showtimes)->firstWhere('id', $showtimeId);
        if (!$showtime) abort(404);

        $seatsRes = $this->api->getSeats($showtimeId);
        $seats    = $seatsRes->successful() ? ($seatsRes->json('data') ?? []) : [];

        return view('checkout.seats', compact('event', 'showtime', 'seats'));
    }

    // POST /checkout/reserve
    public function reserve(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|integer',
            'event_id'    => 'required|integer',
            'seat_ids'    => 'required|string',
        ]);

        $seatIds    = json_decode($request->seat_ids, true);
        $showtimeId = (int) $request->showtime_id;

        if (empty($seatIds) || !is_array($seatIds)) {
            return back()->with('error', 'Debes seleccionar al menos una silla.');
        }

        $res = $this->api->reserveSeats($showtimeId, $seatIds);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? $res->json('data.message') ?? 'No se pudo reservar los asientos.';
            return back()->with('error', $msg);
        }

        $result = $res->json('data') ?? [];
        if (!($result['success'] ?? true)) {
            return back()->with('error', $result['message'] ?? 'Asientos no disponibles. Intenta de nuevo.');
        }

        // Guardar en sesión para el confirm
        session([
            'checkout_showtime_id' => $showtimeId,
            'checkout_seat_ids'    => $seatIds,
            'checkout_event_id'    => (int) $request->event_id,
            'checkout_expires_at'  => $result['expiresAt'] ?? null,
        ]);

        return redirect()->route('checkout.confirm');
    }

    // GET /checkout/confirm
    public function confirmPage()
    {
        $showtimeId = session('checkout_showtime_id');
        $seatIds    = session('checkout_seat_ids', []);
        $eventId    = session('checkout_event_id');

        if (!$showtimeId || !$eventId || empty($seatIds)) {
            return redirect()->route('home')
                ->with('error', 'Tu reserva expiró. Selecciona tus asientos nuevamente.');
        }

        $event    = $this->api->getEvent($eventId)->json('data');
        $stItems  = $this->api->getShowtimes($eventId)->json('data.items') ?? [];
        $showtime = collect($stItems)->firstWhere('id', $showtimeId);
        $allSeats = $this->api->getSeats($showtimeId)->json('data') ?? [];
        $selected = collect($allSeats)->whereIn('id', $seatIds)->values()->all();

        if (!$event || !$showtime) {
            return redirect()->route('home')
                ->with('error', 'No se pudo cargar la información del evento.');
        }

        return view('checkout.confirm', compact('event', 'showtime', 'selected'));
    }

    // POST /checkout/pay
    public function pay(Request $request)
    {
        $seatIds       = session('checkout_seat_ids', []);
        $paymentMethod = $request->input('payment_method', 'CreditCard');

        if (empty($seatIds)) {
            return redirect()->route('home')
                ->with('error', 'Tu reserva expiró. Selecciona tus asientos nuevamente.');
        }

        // 1. Crear orden  →  POST /orders  { seatIds: [...] }
        $orderRes = $this->api->createOrder($seatIds);

        if (!$orderRes->successful()) {
            $msg = $orderRes->json('message') ?? 'No se pudo crear la orden. La reserva puede haber expirado.';
            return back()->with('error', $msg);
        }

        $order = $orderRes->json('data');
        if (!$order || empty($order['id'])) {
            return back()->with('error', 'Respuesta inválida al crear la orden.');
        }

        // 2. Pagar  →  POST /orders/pay  { orderId, paymentMethod }
        $payRes = $this->api->payOrder($order['id'], $paymentMethod);

        if (!$payRes->successful()) {
            $msg = $payRes->json('message') ?? 'Error al procesar el pago.';
            return back()->with('error', $msg);
        }

        // Limpiar sesión de checkout
        session()->forget(['checkout_showtime_id', 'checkout_seat_ids',
                           'checkout_event_id', 'checkout_expires_at']);

        return redirect()->route('checkout.success', $order['id'])
            ->with('success', '¡Pago exitoso! Tus entradas están listas.');
    }

    // GET /checkout/success/{orderId}
    public function success(int $orderId)
    {
        $res   = $this->api->getOrder($orderId);
        $order = $res->successful() ? $res->json('data') : null;
        return view('checkout.success', compact('order'));
    }
}
