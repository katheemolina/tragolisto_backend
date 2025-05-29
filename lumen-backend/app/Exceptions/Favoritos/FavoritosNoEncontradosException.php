<?php

namespace App\Exceptions\Favoritos;

use Exception;

class FavoritosNoEncontradosException extends Exception
{
    protected $code = 2001;
    protected $message = 'No se encontraron tragos favoritos para este usuario.';
    public function getCodeError()
    {
        return $this->codeError;
    }
}
