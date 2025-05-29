<?php

namespace App\Exceptions\Favoritos;

use Exception;

class UsuarioNoEncontradoException extends Exception
{
    protected $code = 2002;
    protected $message = 'No se encontró el usuario.';
    public function getCodeError()
    {
        return $this->codeError;
    }
}
