# ParkControl

Sistema de control de estacionamiento desarrollado en **PHP 8.2 clásico con
arquitectura MVC**, **MySQL/MariaDB** y **Bootstrap 5.3**. La interfaz está en
español y usa los estilos propios `pc-` del proyecto.

---

## Requisitos

| Componente | Versión usada |
|---|---|
| PHP | 8.2.12 (XAMPP) con `pdo_mysql` |
| MySQL / MariaDB | MariaDB 10.4.32 (XAMPP) |
| Apache | XAMPP (mod_rewrite no es necesario) |
| Navegador | Cualquiera moderno |

No requiere Composer ni Laravel.

---

## Instalación

1. Copia la plantilla de configuración y ajusta las credenciales de MySQL:

   ```text
   app/config/config.local.example.php  →  app/config/config.local.php
   ```

   `config.local.php` **no se versiona** (está en `.gitignore`).

2. Aplica el esquema y los datos iniciales:

   ```powershell
   C:\xampp\php\php.exe database\migrate.php
   ```

   El comando es idempotente: registra lo aplicado en `schema_migrations` y no
   borra datos. Para ver el estado: `database\migrate.php --estado`.

3. Abre la aplicación:

   ```text
   http://localhost/ParkControl/
   ```

   La raíz redirige al front controller `app/index.php`.

### Credenciales iniciales

El seeder restablece la contraseña del primer usuario con rol **Administrador**
y la imprime en consola (o usa `PARKCONTROL_ADMIN_PASS` para fijarla). Cámbiala
después del primer ingreso.

---

## Estructura

```text
ParkControl/
├── index.php                    # Redirige a app/index.php
├── app/
│   ├── index.php                # Front controller (único punto de entrada)
│   ├── bootstrap.php            # Configuración, sesión, autocarga, helpers
│   ├── routes.php               # Tabla de rutas
│   ├── config/
│   │   ├── config.php           # Configuración base (sin secretos)
│   │   ├── config.local.php     # Credenciales locales (ignorado por git)
│   │   ├── config.local.example.php
│   │   └── conexion.php         # PDO singleton (clase Database)
│   ├── core/                    # Router, Auth, Csrf, Flash, Validador, Rut, helpers
│   ├── controllers/             # Controladores por módulo
│   ├── models/                  # Acceso a datos con consultas preparadas
│   ├── views/
│   │   ├── layout/              # header.php y footer.php compartidos
│   │   ├── auth/                # login
│   │   ├── dashboard/           # panel
│   │   └── errores/             # 403, 404, 500
│   └── assets/                  # css, js e imágenes
├── database/
│   ├── migrate.php              # Aplicador de migraciones y seeds
│   ├── migrations/              # Cambios de esquema versionados (.sql)
│   └── seeds/                   # Datos iniciales (.php)
└── docs/                        # Documentación adicional
```

### Flujo de una petición

```text
index.php?ruta=usuarios
  → app/index.php (front controller)
    → Router (método + ruta + permisos + CSRF)
      → Controlador
        → Modelo (PDO, consultas preparadas)
          → Vista (escapa con e())
```

Las rutas se declaran en [app/routes.php](app/routes.php) con
`['auth' => false]` para el login y `['rol' => 1]` para secciones de
administrador.

---

## Roles

| idRol | Rol | Alcance |
|---|---|---|
| 1 | Administrador | Todos los módulos, usuarios, tarifas, reportes y configuración |
| 2 | Operador | Operación diaria: ingresos, plazas, clientes y vehículos |

El estado de cada usuario (`usuario.estado`) permite desactivar cuentas sin
borrarlas.

---

## Estado de los módulos

| Módulo | Estado |
|---|---|
| Autenticación, sesión y CSRF | ✅ Implementado |
| Panel con métricas reales | ✅ Implementado |
| Usuarios (CRUD) | ✅ Implementado |
| Clientes y vehículos | ✅ Implementado |
| Plazas y tarifas | ✅ Implementado |
| Ingresos, cobro y pagos | ✅ Implementado |
| Reportes (con exportación CSV) | ✅ Implementado |
| Configuración y cambio de contraseña | ✅ Implementado |

---

## Base de datos

Tablas del negocio: `cliente`, `vehiculo`, `marca`, `modelo`, `tipovehiculo`,
`fila`, `ingreso`, `servicioextra`, `pago`, `usuario`, `rol`, `configuracion`,
`auditoria` y `schema_migrations`.

### Filas y ocupación

Las filas disponibles son **A, E, F, G, L y Segundo Patio**, cada una con
**16 espacios**. No hay plazas numeradas: al registrar un ingreso el operador
solo indica la fila. La ocupación se calcula contando los ingresos con
`fechaTermino IS NULL`; la fila se considera llena al alcanzar su capacidad.

### Tarifas

Los precios siguen las reglas del repositorio
`Bd_Eva01_Base_De_Datos` (`REGLAS_NEGOCIO.md`):

| Días | Valor |
|---|---|
| 1 a 2 | $10.000 por día |
| 3 a 5 | $7.000 por día |
| 6 a 7 | $40.000 fijo |
| 8 o más | $50.000 fijo |

Una estadía de menos de un día se cobra como 1 día; después de cada día
completo hay 5 horas de prórroga. El servicio extra se cobra según el tipo de
vehículo (SEDAN, HATCHBACK, CAMIONETA, SUV), con la matriz de precios de
Lavado, Lavado y aspirado, Aspirado y Sin servicio. El monto se calcula al
registrar la salida y queda en `ingreso.montoTotal` y `pago`.

Las tablas `plaza` y `tarifa` quedaron como legado del primer prototipo y ya
no se usan; el modelo vigente está en `tipovehiculo`, `fila` e `ingreso`.

Los cambios de esquema se hacen **siempre** con un archivo nuevo en
`database/migrations/` (nunca editando la base a mano) y se aplican con
`database/migrate.php`. Las migraciones son aditivas y conservan los datos.

### Incidencia conocida del entorno local

Las tablas de privilegios de este MySQL (`mysql.db` y `mysql.proxies_priv`)
estaban corruptas y se reconstruyeron; por eso la conexión local usa `root`
sin contraseña (ver `app/config/config.local.php`). En un servidor real:

1. Crear un usuario dedicado:
   ```sql
   CREATE USER 'parkcontrol_app'@'localhost' IDENTIFIED BY '...';
   GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, REFERENCES
     ON parkcontrol.* TO 'parkcontrol_app'@'localhost';
   ```
2. Actualizar `config.local.php` con esas credenciales.
3. Poner `app.depuracion` en `false`.

Los respaldos de las tablas dañadas están en la carpeta temporal del sistema
(`%TEMP%\parkcontrol-mysql-backup`).

---

## Seguridad

- Consultas preparadas en todos los modelos; sin SQL en las vistas.
- Escape de salida con `e()` y token CSRF en cada formulario POST.
- Contraseñas con `password_hash()` / `password_verify()` y rehash automático.
- Sesión con cookie `HttpOnly` + `SameSite=Lax`, expiración por inactividad y
  bloqueo temporal tras 5 intentos fallidos.
- Los errores internos no se muestran con `app.depuracion = false`.
- `app/config/` está bloqueado por `.htaccess` y las credenciales viven fuera
  del repositorio.

---

## Convenciones

- Textos e interfaz en **español**.
- Cada archivo inicia con un comentario de cabecera que explica su propósito.
- Estilos propios con prefijo `pc-`; se reutilizan los tokens de
  `app/assets/css/styles.css`.
- Controladores delgados, modelos con PDO, vistas sin SQL.
