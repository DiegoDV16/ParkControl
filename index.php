<?php
/**
 * Redirige la raíz del proyecto al front controller de la aplicación.
 * Permite abrir http://localhost/ParkControl/ en lugar de la ruta larga.
 */
header('Location: app/index.php');
exit;
