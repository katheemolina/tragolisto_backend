<?php
namespace App\Exceptions\Juegos;

use Exception;

class EliminarJuegoException extends Exception
{
    protected $codeError = 3005;
    public function __construct($message = 'No se pudo eliminar el juego.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 