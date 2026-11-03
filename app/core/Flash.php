<?php
/**
 * ==========================================================================
 * ParkControl · app/core/Flash.php
 * ==========================================================================
 * Mensajes de una sola lectura entre redirecciones.
 * ==========================================================================
 */

class Flash
{
    public static function exito(string $mensaje): void
    {
        self::agregar('success', $mensaje);
    }

    public static function error(string $mensaje): void
    {
        self::agregar('danger', $mensaje);
    }

    public static function advertencia(string $mensaje): void
    {
        self::agregar('warning', $mensaje);
    }

    public static function info(string $mensaje): void
    {
        self::agregar('info', $mensaje);
    }

    /** Devuelve y limpia los mensajes pendientes. */
    public static function tomar(): array
    {
        $mensajes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $mensajes;
    }

    private static function agregar(string $tipo, string $mensaje): void
    {
        $_SESSION['_flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
    }
}
