<?php

namespace App\Exceptions\Tragos;

use Exception;

class TragoNoEncontradoException extends Exception
{
    protected $codeError = 1003;
    protected $message = 'No existe el trago buscado.';
    public function getCodeError()
    {
        return $this->codeError;
    }
}
