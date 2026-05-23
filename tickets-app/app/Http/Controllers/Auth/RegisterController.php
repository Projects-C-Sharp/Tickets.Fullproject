<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\ApiService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullName'              => 'required|string|max:100',
            'email'                 => 'required|email',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $response = $this->api->registerCustomer([
            'fullName' => $request->fullName,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if (!$response->successful()) {
            // La API puede devolver string plano o array de errores de Identity
            $raw = $response->body();
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                // Array de IdentityError: [{ code, description }]
                $msg = collect($decoded)->pluck('description')->filter()->implode(' ');
            } else {
                $msg = trim($raw, '"') ?: 'No se pudo crear la cuenta. Intenta con otro correo.';
            }

            return back()->withErrors(['email' => $msg])->withInput();
        }

        return redirect()->route('login')
            ->with('success', '¡Cuenta creada! Ya puedes iniciar sesion.');
    }
}
