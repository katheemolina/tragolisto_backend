<?php

namespace App\Exceptions\Tragos;


use Exception;

class TragosPorIngredientesException extends Exception
{
    protected $codeError = 1002;
    protected $message = 'No se encontraron tragos con el o los ingredientes seleccionados.';
    public function getCodeError()
    {
        return $this->codeError;
    }
}
