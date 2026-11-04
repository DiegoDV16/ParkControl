<?php
/**
 * ==========================================================================
 * ParkControl · app/core/Auth.php
 * ==========================================================================
 * Autenticación por sesión con protección de fuerza bruta y expiración por
 * inactividad. Los permisos se resuelven por rol: 1 = Administrador,
 * 2 = Operador.
 * ==========================================================================
 */

class Auth
{
    private const SESION = 'usuario';
    private const MAX_INTENTOS = 5;
    private const BLOQUEO_SEGUNDOS = 60;

    /** Valida credenciales y abre la sesión. */
    public static function intentar(string $correo, string $clave): bool
    {
        $usuario = Usuario::porCorreo($correo);

        if ($usuario === null || (int) $usuario['estado'] !== 1) {
            self::registrarFallo();
            return false;
        }

        if (!password_verify($clave, $usuario['contrasena'])) {
            self::registrarFallo();
            return false;
        }

        if (password_needs_rehash($usuario['contrasena'], PASSWORD_DEFAULT)) {
            Usuario::actualizarHash(
                (int) $usuario['idUsuario'],
                password_hash($clave, PASSWORD_DEFAULT)
            );
        }

        Usuario::marcarAcceso((int) $usuario['idUsuario']);

        session_regenerate_id(true);
        $_SESSION[self::SESION] = [
            'id'              => (int) $usuario['idUsuario'],
            'rut'             => $usuario['rut'],
            'nombres'         => $usuario['nombres'],
            'apellidoPaterno' => $usuario['apellidoPaterno'],
            'apellidoMaterno' => $usuario['apellidoMaterno'],
            'correo'          => $usuario['correo'],
            'rol'             => (int) $usuario['idRol'],
            'rolNombre'       => $usuario['rolNombre'],
        ];
        $_SESSION['_ultimaActividad'] = time();
        unset($_SESSION['_intentos'], $_SESSION['_bloqueo']);

        return true;
    }

    /** Datos del usuario autenticado (o null). */
    public static function usuario(): ?array
    {
        return $_SESSION[self::SESION] ?? null;
    }

    /** Comprueba sesión activa y expira por inactividad. */
    public static function verificar(): bool
    {
        if (!isset($_SESSION[self::SESION])) {
            return false;
        }

        $limite = max(5, (int) config('app.sesion_minutos', 30)) * 60;
        $ultima = (int) ($_SESSION['_ultimaActividad'] ?? 0);

        if ($ultima > 0 && (time() - $ultima) > $limite) {
            self::salir();
            return false;
        }

        $_SESSION['_ultimaActividad'] = time();
        return true;
    }

    public static function rol(): int
    {
        return (int) ($_SESSION[self::SESION]['rol'] ?? 0);
    }

    public static function esAdmin(): bool
    {
        return self::rol() === 1;
    }

    /** Cierra la sesión y destruye la cookie. */
    public static function salir(): void
    {
        unset($_SESSION[self::SESION], $_SESSION['_ultimaActividad']);
        session_regenerate_id(true);
    }

    /** ¿La cuenta está bloqueada temporalmente por intentos fallidos? */
    public static function bloqueado(): bool
    {
        $bloqueo = (int) ($_SESSION['_bloqueo'] ?? 0);
        if ($bloqueo > 0 && (time() - $bloqueo) < self::BLOQUEO_SEGUNDOS) {
            return true;
        }
        if ($bloqueo > 0) {
            unset($_SESSION['_bloqueo']);
        }
        return false;
    }

    public static function segundosBloqueo(): int
    {
        $bloqueo = (int) ($_SESSION['_bloqueo'] ?? 0);
        return max(0, self::BLOQUEO_SEGUNDOS - (time() - $bloqueo));
    }

    private static function registrarFallo(): void
    {
        $intentos = (int) ($_SESSION['_intentos'] ?? 0) + 1;
        if ($intentos >= self::MAX_INTENTOS) {
            $_SESSION['_bloqueo'] = time();
            $_SESSION['_intentos'] = 0;
            return;
        }
        $_SESSION['_intentos'] = $intentos;
    }
}
