<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\JwtHelper;
use App\Http\Services\ApiService;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email      = $googleUser->getEmail();
            // Contrasena determinista para usuarios OAuth
            $password   = 'Google@' . substr(md5($googleUser->getId()), 0, 12);

            // Intentar login
            $response = $this->api->login($email, $password);

            // Si falla, registrar primero
            if (!$response->successful()) {
                $reg = $this->api->registerCustomer([
                    'fullName' => $googleUser->getName(),
                    'email'    => $email,
                    'password' => $password,
                ]);
                if (!$reg->successful()) {
                    return redirect()->route('login')
                        ->with('error', 'No se pudo iniciar sesion con Google.');
                }
                $response = $this->api->login($email, $password);
            }

            if (!$response->successful()) {
                return redirect()->route('login')
                    ->with('error', 'Error al autenticar con Google.');
            }

            $body        = $response->json();
            $accessToken = $body['accessToken'] ?? null;

            if (!$accessToken) {
                return redirect()->route('login')
                    ->with('error', 'Token invalido al iniciar con Google.');
            }

            $user = JwtHelper::extractUserInfo($accessToken, $email);

            session([
                'api_token'     => $accessToken,
                'refresh_token' => $body['refreshToken'] ?? null,
                'user_name'     => $googleUser->getName() ?: $user['name'],
                'user_email'    => $email,
                'user_photo'    => $googleUser->getAvatar(),
                'user_roles'    => $user['roles'],
            ]);

            return redirect()->route('orders.index')
                ->with('success', '¡Bienvenido con Google!');

        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Error al conectar con Google: ' . $e->getMessage());
        }
    }
}
