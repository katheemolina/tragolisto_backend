<?php

namespace App\Exceptions\Favoritos;

use Exception;

class AgregarFavoritoException extends Exception
{
    protected $code = 2003;
    protected $message = 'No se pudo agregar a favoritos.';
    public function getCodeError()
    {
        return $this->codeError;
    }
}
