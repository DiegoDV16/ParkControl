<?php
/**
 * ==========================================================================
 * ParkControl · app/config/config.local.example.php
 * ==========================================================================
 * Plantilla de configuración local. Copiar como `config.local.php` y ajustar
 * las credenciales reales. `config.local.php` NO se versiona.
 * ==========================================================================
 */

return [
    'app' => [
        // false en producción: oculta los detalles de los errores.
        'depuracion' => true,
    ],
    'db' => [
        'host'    => '127.0.0.1',
        'puerto'  => 3306,
        'nombre'  => 'parkcontrol',
        'usuario' => 'parkcontrol_app',
        'clave'   => 'CAMBIA_ESTA_CLAVE',
    ],
];
