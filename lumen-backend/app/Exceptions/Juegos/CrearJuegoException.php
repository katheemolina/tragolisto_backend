<?php
namespace App\Exceptions\Juegos;

use Exception;

class CrearJuegoException extends Exception
{
    protected $codeError = 3003;
    public function __construct($message = 'No se pudo crear el juego.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 