<?php

namespace App\Http\Helpers;

class JwtHelper
{
    /**
     * Decodifica el payload del JWT (sin verificar firma).
     * La API usa ClaimTypes de .NET que se mapean a URIs largas.
     */
    public static function decode(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return [];

        $payload = $parts[1];
        // Padding base64url → base64
        $payload = str_replace(['-', '_'], ['+', '/'], $payload);
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);

        $decoded = json_decode(base64_decode($payload), true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Extrae nombre, email y roles del payload JWT generado por la API .NET
     */
    public static function extractUserInfo(string $token, string $fallbackEmail = ''): array
    {
        $p = self::decode($token);

        // .NET ClaimTypes.Email
        $email = $p['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress']
              ?? $p['email']
              ?? $fallbackEmail;

        // .NET ClaimTypes.Name  (= UserName en la entidad)
        $name = $p['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name']
             ?? $p['name']
             ?? explode('@', $email)[0];

        // .NET ClaimTypes.Role (puede ser string o array)
        $roles = $p['http://schemas.microsoft.com/ws/2008/06/identity/claims/role']
              ?? $p['role']
              ?? [];
        if (is_string($roles)) $roles = [$roles];

        return compact('email', 'name', 'roles');
    }
}
