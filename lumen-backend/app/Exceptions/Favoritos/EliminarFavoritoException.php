<?php

namespace App\Exceptions\Favoritos;

use Exception;

class EliminarFavoritoException extends Exception
{
    protected $code = 2004;
    protected $message = 'No se pudo eliminar el favorito.';
    public function getCodeError()
    {
        return $this->codeError;
    }
}
