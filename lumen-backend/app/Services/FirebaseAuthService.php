<?php

namespace App\Services;

use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseAuthService
{
    protected Auth $auth;

    public function __construct()
    {
        $this->auth = (new Factory)
            ->withServiceAccount(base_path('firebase-credentials.json'))
            ->createAuth();
    }

    public function verifyGoogleToken(string $idToken): array
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);

            $uid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $name = $verifiedIdToken->claims()->get('name'); // puede ser null

            return [
                'uid' => $uid,
                'email' => $email,
                'name' => $name,
            ];
        } catch (InvalidToken $e) {
            throw new \Exception('Token inválido: ' . $e->getMessage());
        } catch (\Throwable $e) {
            throw new \Exception('Error al verificar token: ' . $e->getMessage());
        }
    }
}
