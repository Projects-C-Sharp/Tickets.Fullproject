<?php

namespace App\Http\Controllers;

use App\Http\Services\ApiService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function index()
    {
        $favoriteIds = session('favorites', []);
        $events      = [];

        foreach ($favoriteIds as $id) {
            $res = $this->api->getEvent($id);
            if ($res->successful() && $res->json('data')) {
                $events[] = $res->json('data');
            }
        }

        return view('dashboard.favorites', compact('events'));
    }

    public function toggle(int $eventId)
    {
        $favorites = session('favorites', []);
        $key       = array_search($eventId, $favorites);

        if ($key !== false) {
            array_splice($favorites, $key, 1);
            $msg = 'Eliminado de favoritos.';
        } else {
            $favorites[] = $eventId;
            $msg = '¡Agregado a favoritos!';
        }

        session(['favorites' => array_values($favorites)]);
        return back()->with('success', $msg);
    }
}
