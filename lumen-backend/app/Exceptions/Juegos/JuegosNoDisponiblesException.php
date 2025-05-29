<?php
namespace App\Exceptions\Juegos;

use Exception;

class JuegosNoDisponiblesException extends Exception
{
    protected $codeError = 3001;
    protected $message = 'No hay juegos disponibles.';
    public function getCodeError() { return $this->codeError; }
}