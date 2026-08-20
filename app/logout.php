<?php
/**
 * ==========================================================================
 * ParkControl · app/logout.php
 * ==========================================================================
 * Cierra la sesión del usuario y lo devuelve al login.
 * Se debe llamar desde el enlace "Cerrar sesión" del layout.
 * ==========================================================================
 */
session_start();
session_unset();
session_destroy();

header("Location: views/login.php");
exit;
