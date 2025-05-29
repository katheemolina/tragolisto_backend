<?php

namespace App\Exceptions\Tragos;


use Exception;

class TragosVaciosException extends Exception
{
    protected $codeError = 1001;
    protected $message = 'No hay tragos disponibles en la base de datos.';

    public function getCodeError()
    {
        return $this->codeError;
    }
}
