# ParkControl

Sistema de control de estacionamiento. Este proyecto usa una arquitectura
MVC clásica en PHP. La rama actual (`feature/ui`) contiene la maqueta de la
interfaz (front-end) construida con **Bootstrap 5.3** + CSS propio.

---

## Cómo previsualizar

Usa el servidor de XAMPP (Apache) apuntando a esta carpeta:

| Pantalla      | URL                                         | Descripción                              |
|---------------|---------------------------------------------|------------------------------------------|
| Login         | `http://localhost/ParkControl/app/views/login.php` | Inicio de sesión (página completa) |
| Recuperar contraseña | `http://localhost/ParkControl/app/views/recuperar_contrasena.php` | Recuperación de acceso (página completa) |
| Inicio        | `http://localhost/ParkControl/app/index.php`        | Página de inicio con sidebar + bienvenida |

> Consejo: abre la vista responsive del navegador (F12 → modo dispositivo)
> para probar el comportamiento móvil.

---

## Estructura del proyecto

```
ParkControl/
├── app/
│   ├── .htaccess                 # Permite ver la UI (ver "Seguridad" abajo)
│   ├── index.php                 # Página de inicio (usa header/footer)
│   ├── config/
│   │   ├── .htaccess             # Bloquea el acceso a config
│   │   └── conexion.php          # Conexión PDO a la BD (credenciales)
│   ├── assets/
│   │   ├── css/styles.css        # Estilos propios + tokens de diseño
│   │   ├── js/main.js            # Helpers JS (cierre del menú móvil)
│   │   └── img/logo.svg          # Logo de ParkControl
│   └── views/
│       ├── header.php            # CABECERA compartida: <head>, CDN, sidebar, topbar
│       ├── footer.php            # PIE compartido: cierra la página y carga scripts
│       ├── login.php             # Pantalla de login (página completa, sin sidebar)
│       └── ...                   # (tus vistas: vehiculos.php, clientes.php, etc.)
└── README.md                     # Esta documentación
```

---

## Cómo crear una vista (patrón header/footer)

Cada vista con menú es un archivo PHP dentro de `app/views/` que **incluye**
la cabecera al inicio y el pie al final. La cabecera aporta el `<head>` con
todos los enlaces/CDN, el sidebar (fijo en desktop / *offcanvas* en móvil) y
el topbar; el pie cierra la página y carga los scripts (Bootstrap JS + main.js).

Esquema mínimo de una vista (ej. `app/views/administrador.php`):

```php
<?php
$titulo  = 'Administración';   // Título del topbar y de la pestaña
$seccion = 'inicio';           // Ítem del menú que quedará resaltado
include 'header.php';          // Abre <html>, carga CDN, dibuja sidebar/topbar
?>

<!-- Contenido propio de la vista (sin <html>, sin sidebar) -->
<h2>Hola, este es mi módulo</h2>

<?php include 'footer.php'; ?>
```

### Variables opcionales de la vista

| Variable   | Valores posibles (por defecto `inicio`)         | Para qué sirve                          |
|------------|--------------------------------------------------|------------------------------------------|
| `$titulo`  | cualquier texto (por defecto `'Inicio'`)        | Título del topbar y de la pestaña        |
| `$seccion` | `inicio`, `vehiculos`, `plazas`, `clientes`, `tarifas`, `reportes`, `configuracion` | Ítem del menú lateral resaltado |

### Reglas

1. El archivo va dentro de `app/views/` y comienza con un comentario de
   cabecera explicando su propósito (revisa `app/index.php` como ejemplo).
2. Define `$titulo` y `$seccion` **antes** de `include 'header.php';`.
3. Escribe el contenido entre `include 'header.php';` y `include 'footer.php';`.
4. Para activar su enlace en el menú: en `app/views/header.php` cambia el
   `href="#"` de ese ítem por la URL de la vista y quita la clase
   `deshabilitado`.
5. `login.php` es la excepción: no lleva sidebar, por eso es una página
   completa independiente y **no** usa header/footer.

---

## Paleta de colores (tokens)

Los colores se definen como variables CSS en `app/assets/css/styles.css` y se
reutilizan en todo el sistema. También se sobrescriben los tokens nativos de
Bootstrap (`--bs-primary`, `--bs-success`, etc.) para que los componentes
estándar hereden la identidad del sistema sin `!important`.

| Variable        | Valor       | Uso                                   |
|-----------------|-------------|---------------------------------------|
| `--pc-azul-900` | `#0b2545`   | Sidebar y fondo del panel de marca    |
| `--pc-azul-700` | `#0a6dbd`   | Azul primario (botones, enlaces)      |
| `--pc-azul-500` | `#0e8fd4`   | Azul claro (degradados, acentos)      |
| `--pc-verde`    | `#1e9e5a`   | Éxito / disponible                    |
| `--pc-rojo`     | `#d64545`   | Error / no disponible                 |
| `--pc-ambar`    | `#f5a623`   | Advertencia / pendiente               |
| `--pc-fondo`    | `#f4f7fb`   | Fondo del contenido                   |

---

## Clases y componentes reutilizables

| Clase                        | Para qué sirve                             |
|------------------------------|--------------------------------------------|
| `.pc-app`                    | Grid principal: sidebar + contenido        |
| `.pc-sidebar` / `.pc-nav-link` | Sidebar y sus enlaces (`.activo`, `.deshabilitado`) |
| `.pc-sidebar-offcanvas`      | Menú móvil (offcanvas)                     |
| `.pc-topbar`                 | Barra superior con título y acciones       |
| `.pc-contenido`              | Contenedor donde se incluyen las vistas    |
| `.pc-card`                   | Card con la sombra y radio del sistema     |
| `.pc-card-icono.azul/verde/rojo/ambar` | Icono circular de color para módulos/métricas |
| `.pc-avatar`                 | Avatar circular del usuario                |
| `.pc-badge`                  | Etiqueta de estado (usa `text-bg-*` de Bootstrap) |
| `.pc-login-*`                | Componentes de la pantalla de login        |

---

## Responsive

- **Desktop (≥ 992px):** sidebar fijo a la izquierda + topbar + contenido.
- **Tablet/Móvil (< 992px):** el sidebar se oculta y el menú se abre como
  *offcanvas* con el botón hamburguesa del topbar. Al pulsar un enlace se
  cierra solo (ver `app/assets/js/main.js`).
- **Móvil (< 576px):** en el login se oculta el panel de marca y solo se ve
  el formulario; en el layout, las cards se apilan.

---

## Seguridad

- `app/config/.htaccess` bloquea el acceso directo a `config/` (credenciales).
- `app/config/conexion.php` está en `.gitignore` (no se sube al repositorio).
- `app/.htaccess` fue ajustado a `Require all granted` **solo para poder
  previsualizar la UI durante el desarrollo**. Cuando exista el enrutador del
  MVC, vuelve a restringir el acceso a los directorios internos
  (controladores, modelos, config) y deja únicamente expuesto el punto de
  entrada público.

---

## Convenciones

- Textos e interfaces en **español**.
- Cada archivo PHP/HTML/CSS/JS inicia con un comentario de cabecera que
  explica qué es y para qué sirve.
- Los estilos propios usan el prefijo `pc-` para no chocar con Bootstrap.
- Se usa Bootstrap vía CDN (no requiere instalación local).
