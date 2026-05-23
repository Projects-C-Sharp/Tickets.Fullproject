<?php

namespace App\Http\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiService
{
    private string $base;
    private int    $timeout;

    public function __construct()
    {
        $this->base    = rtrim(config('services.api.base_url', env('API_BASE_URL', 'http://localhost:5201/api')), '/');
        $this->timeout = (int) config('services.api.timeout', 30);
    }

    // ── HTTP client (inyecta Bearer si hay sesión) ──────────────────
    private function http(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::baseUrl($this->base)
                      ->timeout($this->timeout)
                      ->acceptJson();

        $token = Session::get('api_token');
        if ($token) {
            $client = $client->withToken($token);
        }

        return $client;
    }

    // ── Auth ────────────────────────────────────────────────────────
    // POST /auth/login  → { accessToken, refreshToken }
    public function login(string $email, string $password): Response
    {
        return $this->http()->post('/auth/login', [
            'email'    => $email,
            'password' => $password,
        ]);
    }

    // POST /auth/register-customer  → { message }
    public function registerCustomer(array $data): Response
    {
        return $this->http()->post('/auth/register-customer', $data);
    }

    // POST /auth/logout  (requiere Bearer)
    public function logout(): void
    {
        try { $this->http()->post('/auth/logout'); } catch (\Throwable) {}
    }

    // POST /auth/upload-photo
    public function uploadPhoto($file): Response
    {
        return $this->http()
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post('/auth/upload-photo');
    }

    // ── Events ──────────────────────────────────────────────────────
    // GET /events → ApiResponse<PagedResult<EventDto>>
    public function getEvents(int $page = 1, int $pageSize = 12, bool $isActive = true): Response
    {
        return $this->http()->get('/events', [
            'page'     => $page,
            'pageSize' => $pageSize,
            'isActive' => $isActive ? 'true' : 'false',
        ]);
    }

    // GET /events/{id} → ApiResponse<EventDto>
    public function getEvent(int $id): Response
    {
        return $this->http()->get("/events/{$id}");
    }

    // ── Showtimes ───────────────────────────────────────────────────
    // GET /showtimes?eventId= → ApiResponse<PagedResult<ShowtimeDto>>
    public function getShowtimes(int $eventId): Response
    {
        return $this->http()->get('/showtimes', [
            'eventId'  => $eventId,
            'pageSize' => 100,
        ]);
    }

    // GET /showtimes/{id}/seats → ApiResponse<List<SeatDto>>
    public function getSeats(int $showtimeId): Response
    {
        return $this->http()->get("/showtimes/{$showtimeId}/seats");
    }

    // ── Seats ───────────────────────────────────────────────────────
    // POST /seats/reserve → ApiResponse<ReservationResult>
    public function reserveSeats(int $showtimeId, array $seatIds): Response
    {
        return $this->http()->post('/seats/reserve', [
            'showtimeId' => $showtimeId,
            'seatIds'    => $seatIds,
        ]);
    }

    // POST /seats/release
    public function releaseSeats(array $seatIds): void
    {
        try { $this->http()->post('/seats/release', $seatIds); } catch (\Throwable) {}
    }

    // ── Orders ──────────────────────────────────────────────────────
    // POST /orders  body: { seatIds: [] } → ApiResponse<OrderDto>
    public function createOrder(array $seatIds): Response
    {
        return $this->http()->post('/orders', ['seatIds' => $seatIds]);
    }

    // POST /orders/pay  body: { orderId, paymentMethod } → ApiResponse<PaymentResultDto>
    public function payOrder(int $orderId, string $method = 'CreditCard'): Response
    {
        return $this->http()->post('/orders/pay', [
            'orderId'       => $orderId,
            'paymentMethod' => $method,
        ]);
    }

    // GET /orders → ApiResponse<PagedResult<OrderDto>>
    public function getOrders(int $page = 1): Response
    {
        return $this->http()->get('/orders', ['page' => $page, 'pageSize' => 50]);
    }

    // GET /orders/{id} → ApiResponse<OrderDto>
    public function getOrder(int $id): Response
    {
        return $this->http()->get("/orders/{$id}");
    }
}
