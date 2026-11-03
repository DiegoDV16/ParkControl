<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/AuthController.php
 * ==========================================================================
 * Inicio y cierre de sesión.
 * ==========================================================================
 */

class AuthController
{
    public function mostrarLogin(): void
    {
        if (Auth::verificar()) {
            redirigir('dashboard');
        }
        require PC_APP . '/views/auth/login.php';
    }

    public function ingresar(): void
    {
        $correo = trim((string) ($_POST['correo'] ?? ''));
        $clave = (string) ($_POST['clave'] ?? '');

        if (Auth::bloqueado()) {
            Flash::error('Demasiados intentos fallidos. Espera ' . Auth::segundosBloqueo() . ' segundos antes de reintentar.');
            $this->recordarCorreo($correo);
            redirigir('login');
        }

        if ($correo === '' || $clave === '') {
            Flash::error('Ingresa tu correo y tu contraseña.');
            $this->recordarCorreo($correo);
            redirigir('login');
        }

        if (!Auth::intentar($correo, $clave)) {
            Flash::error('Correo o contraseña incorrectos.');
            $this->recordarCorreo($correo);
            redirigir('login');
        }

        redirigir('dashboard');
    }

    public function salir(): void
    {
        Auth::salir();
        Flash::info('Tu sesión se cerró correctamente.');
        redirigir('login');
    }

    private function recordarCorreo(string $correo): void
    {
        $_SESSION['_old']['correo'] = $correo;
    }
}
