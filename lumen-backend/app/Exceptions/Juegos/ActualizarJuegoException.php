<?php
namespace App\Exceptions\Juegos;

use Exception;

class ActualizarJuegoException extends Exception
{
    protected $codeError = 3004;
    public function __construct($message = 'No se pudo actualizar el juego.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 