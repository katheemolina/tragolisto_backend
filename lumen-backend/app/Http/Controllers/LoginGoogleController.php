<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginGoogleController extends Controller
{
    public function login(Request $request)
    {
        // Validar input
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'messages' => $validator->errors(),
            ], 422);
        }

        $idToken = $request->input('id_token');

        try {
            // Decodificar el token para obtener datos del usuario (opcional)
            $payload = $this->decodeIdToken($idToken);

            $googleId = $payload['sub'] ?? null;
            $email = $payload['email'] ?? null;
            $nombre = $payload['name'] ?? null;

            if (!$googleId || !$email) {
                return response()->json(['error' => 'Token inválido, falta googleId o email'], 400);
            }

            // Insertar o actualizar usuario en la base de datos (tabla users)
            DB::table('usuarios')->updateOrInsert(
                ['google_id' => $googleId],
                [
                    'email' => $email,
                    'nombre' => $nombre,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Usuario logueado correctamente',
                'google_id' => $googleId,
                'email' => $email,
                'nombre' => $nombre,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error procesando token: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function decodeIdToken(string $idToken): array
    {
        // Decodificar el payload JWT sin validar firma
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            throw new \Exception('Formato de token inválido');
        }

        $payload = $parts[1];
        $decoded = base64_decode(strtr($payload, '-_', '+/'));

        if (!$decoded) {
            throw new \Exception('No se pudo decodificar el token');
        }

        return json_decode($decoded, true);
    }
}
