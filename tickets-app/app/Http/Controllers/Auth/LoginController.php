<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\JwtHelper;
use App\Http\Services\ApiService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $response = $this->api->login($request->email, $request->password);

        // La API devuelve 401 con un string plano en caso de error
        if (!$response->successful()) {
            return back()
                ->withErrors(['email' => 'Correo o contrasena incorrectos.'])
                ->withInput();
        }

        // La API devuelve: { "accessToken": "...", "refreshToken": "..." }
        $body = $response->json();

        $accessToken  = $body['accessToken']  ?? null;
        $refreshToken = $body['refreshToken'] ?? null;

        if (!$accessToken) {
            return back()
                ->withErrors(['email' => 'Error inesperado al iniciar sesion. Intenta de nuevo.'])
                ->withInput();
        }

        // Extraer info del usuario desde el JWT
        $user = JwtHelper::extractUserInfo($accessToken, $request->email);

        session([
            'api_token'     => $accessToken,
            'refresh_token' => $refreshToken,
            'user_name'     => $user['name'],
            'user_email'    => $user['email'],
            'user_photo'    => null,
            'user_roles'    => $user['roles'],
        ]);

        $redirect = $request->input('redirect');
        return redirect($redirect ?: route('orders.index'))
            ->with('success', '¡Bienvenido de vuelta!');
    }

    public function logout(Request $request)
    {
        $this->api->logout();
        $request->session()->flush();
        return redirect()->route('home')
            ->with('success', 'Has cerrado sesion correctamente.');
    }
}
