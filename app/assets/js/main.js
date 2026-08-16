/* ==========================================================================
   ParkControl · main.js
   --------------------------------------------------------------------------
   Helpers JavaScript del sistema.

   NOTA: El offcanvas, dropdowns y demás componentes de Bootstrap funcionan
   con el bundle que se carga en cada página. Este archivo solo añade
   comportamientos propios del sistema.

   TABLA DE CONTENIDO
   ==========================================================================
   1. Cerrar el menú (offcanvas) al pulsar un enlace de navegación.
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. MENÚ MÓVIL: cierre automático al seleccionar un enlace
   En móvil, tras tocar un ítem del menú lateral se cierra el offcanvas
   para no tapar el contenido.
   -------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    var menuMovil = document.getElementById('pcMenuMovil');
    if (menuMovil) {
        var instanciaOffcanvas = bootstrap.Offcanvas.getInstance(menuMovil) || new bootstrap.Offcanvas(menuMovil);
        menuMovil.querySelectorAll('.pc-nav-link').forEach(function (enlace) {
            enlace.addEventListener('click', function () {
                instanciaOffcanvas.hide();
            });
        });
    }
});
