<?php
/**
 * ==========================================================================
 * ParkControl · app/views/footer.php
 * ==========================================================================
 * PIE COMPARTIDO del sistema (cierre del documento).
 *
 * Contiene:
 *   · Cierre del <main> y de la estructura del layout (.pc-app).
 *   · Carga de los scripts CDN (bootstrap.bundle.min.js) y de los scripts
 *     propios del sistema (app/assets/js/main.js).
 *   · Cierre de <body> y </html>.
 *
 * Debe incluirse SIEMPRE DESPUÉS de header.php, al final de cada vista:
 *
 *     include 'header.php';
 *     // ... contenido de la vista ...
 *     include 'footer.php';
 *
 * ==========================================================================
 */

// Si el footer se incluye sin haber cargado header.php, se recalcula la
// ruta base para no romper las rutas de los scripts.
if (!isset($urlApp)) {
    $documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? getcwd()), '/');
    $dirApp       = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/');
    $urlApp       = str_replace($documentRoot, '', $dirApp);
    if ($urlApp === $dirApp) {
        $urlApp = '/ParkControl/app'; // respaldo por si la ruta no coincide
    }
}
?>
            </main><!-- /main.pc-contenido -->
        </div><!-- /.pc-app-main -->
    </div><!-- /.pc-app -->

    <!-- Bootstrap 5.3 (JavaScript bundle) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Helpers propios del sistema -->
    <script src="<?php echo $urlApp; ?>/assets/js/main.js"></script>
</body>
</html>
