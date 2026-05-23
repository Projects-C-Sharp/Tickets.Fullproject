<?php

namespace App\Http\Controllers;

use App\Http\Services\ApiService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function index(Request $request)
    {
        $page     = max(1, (int) $request->get('page', 1));
        $response = $this->api->getEvents($page, 12);

        $events     = [];
        $totalPages = 1;

        if ($response->successful()) {
            // ApiResponse<PagedResult<EventDto>>
            $data       = $response->json('data') ?? [];
            $events     = $data['items']      ?? [];
            $totalPages = $data['totalPages'] ?? 1;
        }

        return view('events.index', compact('events', 'page', 'totalPages'));
    }

    public function show(int $id)
    {
        $evRes = $this->api->getEvent($id);
        if (!$evRes->successful()) abort(404);

        $event = $evRes->json('data');
        if (!$event) abort(404);

        $stRes     = $this->api->getShowtimes($id);
        $showtimes = $stRes->successful() ? ($stRes->json('data.items') ?? []) : [];

        return view('events.show', compact('event', 'showtimes'));
    }
}
