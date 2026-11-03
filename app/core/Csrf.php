<?php
/**
 * ==========================================================================
 * ParkControl · app/core/Csrf.php
 * ==========================================================================
 * Token CSRF por sesión para todos los formularios POST.
 * ==========================================================================
 */

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /** Campo oculto listo para insertar dentro de un <form>. */
    public static function campo(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function validar(): bool
    {
        $enviado = (string) ($_POST['_csrf'] ?? '');
        return $enviado !== '' && hash_equals(self::token(), $enviado);
    }
}
