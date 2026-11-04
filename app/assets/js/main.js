/**
 * ==========================================================================
 * ParkControl · app/assets/js/main.js
 * ==========================================================================
 * Comportamientos compartidos con Bootstrap 5:
 *   1. Cierre del menú móvil al navegar.
 *   2. Modales de alta/edición reutilizables (data-form-modal).
 *   3. Modal de confirmación para acciones destructivas (data-confirmar).
 *   4. Reapertura automática de un modal tras un error de validación.
 *   5. Cierre automático de alertas y mostrar/ocultar contraseñas.
 * ==========================================================================
 */

(function () {
    'use strict';

    function listo(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    listo(function () {
        // 1. Menú móvil: se cierra al elegir una sección
        var menuMovil = document.getElementById('pcMenuMovil');
        if (menuMovil) {
            var offcanvas = bootstrap.Offcanvas.getInstance(menuMovil) || new bootstrap.Offcanvas(menuMovil);
            menuMovil.querySelectorAll('.pc-nav-link').forEach(function (enlace) {
                enlace.addEventListener('click', function () {
                    offcanvas.hide();
                });
            });
        }

        // 2. Modal de formulario reutilizable: crear / editar
        document.querySelectorAll('[data-form-modal]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                var modal = document.querySelector(boton.dataset.formModal);
                if (!modal) {
                    return;
                }

                var formulario = modal.querySelector('form');
                var titulo = modal.querySelector('[data-modal-titulo]');
                var modo = boton.dataset.modo || 'crear';

                formulario.reset();
                formulario.action = boton.dataset.formAction || formulario.dataset.actionCrear || formulario.action;

                if (titulo) {
                    titulo.textContent = boton.dataset.formTitulo || titulo.dataset.tituloCrear || titulo.textContent;
                }

                formulario.querySelectorAll('[data-solo-crear]').forEach(function (contenedor) {
                    contenedor.classList.toggle('d-none', modo === 'editar');
                });

                var campos = {};
                if (boton.dataset.campos) {
                    try {
                        campos = JSON.parse(boton.dataset.campos);
                    } catch (error) {
                        campos = {};
                    }
                }

                Object.keys(campos).forEach(function (nombre) {
                    var campo = formulario.elements[nombre];
                    if (!campo) {
                        return;
                    }
                    if (campo.type === 'checkbox') {
                        campo.checked = String(campos[nombre]) === '1';
                    } else {
                        campo.value = campos[nombre];
                    }
                });

                var primero = formulario.querySelector('input:not([type=hidden]), select, textarea');
                if (primero) {
                    setTimeout(function () { primero.focus(); }, 250);
                }
            });
        });

        // 3. Confirmación genérica para activar, desactivar, restablecer o eliminar
        document.querySelectorAll('[data-confirmar]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                var modal = document.querySelector('#modalConfirmar');
                if (!modal) {
                    return;
                }

                var formulario = modal.querySelector('form');
                formulario.action = boton.dataset.confirmAction || '';

                var campo = modal.querySelector('[data-confirmar-campo]');
                campo.name = boton.dataset.confirmCampo || 'id';
                campo.value = boton.dataset.confirmValor || '';

                var mensaje = modal.querySelector('[data-confirmar-mensaje]');
                if (mensaje) {
                    mensaje.textContent = boton.dataset.confirmMensaje || '¿Confirmas esta acción?';
                }

                var titulo = modal.querySelector('[data-confirmar-titulo]');
                if (titulo && boton.dataset.confirmTitulo) {
                    titulo.textContent = boton.dataset.confirmTitulo;
                }

                var aceptar = modal.querySelector('[data-confirmar-aceptar]');
                if (aceptar && boton.dataset.confirmAceptar) {
                    aceptar.textContent = boton.dataset.confirmAceptar;
                    aceptar.className = 'btn ' + (boton.dataset.confirmClase || 'btn-danger');
                }

                bootstrap.Modal.getOrCreateInstance(modal).show();
            });
        });

        // 4. Reabrir modal cuando el servidor devuelve un error de validación
        var auto = document.body.dataset.abrirModal;
        if (auto) {
            var objetivo = document.querySelector(auto);
            if (objetivo) {
                bootstrap.Modal.getOrCreateInstance(objetivo).show();
            }
        }

        // 5. Alertas que se cierran solas y visibilidad de contraseñas
        document.querySelectorAll('.alert-auto').forEach(function (alerta) {
            setTimeout(function () {
                bootstrap.Alert.getOrCreateInstance(alerta).close();
            }, 6000);
        });

        document.querySelectorAll('[data-ver-clave]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                var campo = document.getElementById(boton.dataset.verClave);
                if (!campo) {
                    return;
                }
                var oculta = campo.type === 'password';
                campo.type = oculta ? 'text' : 'password';
                var icono = boton.querySelector('i');
                if (icono) {
                    icono.className = oculta ? 'bi bi-eye-slash' : 'bi bi-eye';
                }
            });
        });
    });
})();
