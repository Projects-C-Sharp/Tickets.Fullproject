<?php

namespace App\Http\Controllers;

use App\Http\Services\ApiService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function show()
    {
        return view('dashboard.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:100',
        ]);
        // Guardamos en sesion hasta que la API exponga endpoint de perfil
        session(['user_name' => $request->fullName]);
        return back()->with('success', 'Perfil actualizado.');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:4096']);

        $res = $this->api->uploadPhoto($request->file('photo'));

        if ($res->successful()) {
            $url = $res->json('photoUrl') ?? $res->json('data.photoUrl');
            if ($url) session(['user_photo' => $url]);
            return back()->with('success', '¡Foto actualizada!');
        }

        return back()->with('error', 'No se pudo subir la foto.');
    }
}
