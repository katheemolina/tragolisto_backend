<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use App\Exceptions\Usuarios\UsuariosNoDisponiblesException;

class UserController extends Controller
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function verificarOnboarding(Request $request)
    {
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
            $payload = $this->decodeIdToken($idToken);

            $googleId = $payload['sub'] ?? null;

            if (!$googleId) {
                return response()->json(['error' => 'Token inválido, falta googleId'], 400);
            }

            $usuario = DB::table('usuarios')->where('google_id', $googleId)->first();

            if ($usuario) {
                // Calcular si es mayor de edad solo si tiene fecha_nacimiento
                $esMayor = false;
                if (!is_null($usuario->fecha_nacimiento)) {
                    $esMayor = \Carbon\Carbon::parse($usuario->fecha_nacimiento)->age >= 18;
                }

                return response()->json([
                    'existe' => true,
                    'id_usuario' => $usuario->id,
                    'fecha_nacimiento' => $usuario->fecha_nacimiento, // puede ser null
                    'requiere_onboarding' => is_null($usuario->fecha_nacimiento),
                    'es_mayor' => $esMayor,
                ]);
            }
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

    public function completarOnboarding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
            'fecha_nacimiento' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'messages' => $validator->errors(),
            ], 422);
        }

        try {
            $payload = $this->decodeIdToken($request->input('id_token'));
            $googleId = $payload['sub'] ?? null;

            if (!$googleId) {
                return response()->json(['error' => 'Token inválido'], 400);
            }

            DB::table('usuarios')
                ->where('google_id', $googleId)
                ->update(['fecha_nacimiento' => $request->input('fecha_nacimiento')]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error procesando token: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function obtenerUsuarios()
    {
        try {
            $usuarios = $this->userService->obtenerTodosLosUsuarios();
            return response()->json($usuarios, 200);
        } catch (UsuariosNoDisponiblesException $e) {
            return response()->json([
                'error' => [
                    'code' => $e->getCodeError(),
                    'message' => $e->getMessage(),
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 5000,
                    'message' => 'Error interno del servidor',
                ]
            ], 500);
        }
    }
}
