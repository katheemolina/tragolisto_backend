<?php
namespace App\Exceptions\Juegos;

use Exception;

class JuegoNoEncontradoException extends Exception
{
    protected $codeError = 3002;
    protected $message = 'No se encontró el juego seleccionado.';
    public function getCodeError() { return $this->codeError; }
}