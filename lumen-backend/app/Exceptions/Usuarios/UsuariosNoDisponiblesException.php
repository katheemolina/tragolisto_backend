<?php
namespace App\Exceptions\Usuarios;

use Exception;

class UsuariosNoDisponiblesException extends Exception
{
    protected $codeError = 4001;
    protected $message = 'No hay usuarios registrados en la plataforma.';
    public function getCodeError() { return $this->codeError; }
} 