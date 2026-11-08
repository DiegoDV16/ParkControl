# Instrucciones para el agente de ParkControl

Estas instrucciones están dirigidas a DeepSeek y a cualquier agente que
desarrolle o mantenga este proyecto.

## Contexto del proyecto

- ParkControl es un sistema de control de estacionamiento de larga estadía
  (asociado a viajes y custodia de vehículos) con interfaz y textos
  exclusivamente en español.
- La arquitectura del proyecto es un monolito desacoplado:
  - Backend: API REST construida en PHP 8.2 o superior con Laravel y
    autenticación de tokens vía Laravel Sanctum.
  - Frontend: Single Page Application (SPA) responsiva desarrollada en React
    con Vite, TypeScript, React Router y Tailwind CSS, optimizada para PC y
    tablets táctiles en garita.
  - Base de datos: MySQL / MariaDB ejecutada localmente con XAMPP y
    administrada mediante phpMyAdmin.
- No se manejan cajas de cobro tradicionales ni aperturas/cierres de turno en
  tiempo real. Los cobros corresponden a liquidaciones y estimaciones calculadas
  por tramos de días y servicios adicionales al generar reportes o liquidar la
  salida.

## Reglas de desarrollo

- Escribe código compatible con PHP 8.2 o superior en el backend. Aplica tipos
  estrictos, Form Requests para validación de entradas, API Resources para
  respuestas estructuradas y middlewares de rol.
- Respeta la estructura de API REST y no mezcles vistas Blade tradicionales con
  la interfaz de React.
- Accede a MySQL a través de migraciones y modelos de Eloquent ORM. No ejecutes
  consultas directas desprotegidas ni uses phpMyAdmin como sustituto de
  migraciones versionadas.
- Queda prohibido el uso de tipos de datos o columnas JSON para persistir
  información de negocio. Toda entidad (daños, pagos, vehículos, marcas,
  modelos y clientes) debe estar normalizada en columnas atómicas y tablas
  relacionales.
- Los catálogos de marcas y modelos provienen directamente de la base de datos
  mediante las tablas `marca` y `modelo`. En el formulario de ingreso, el
  selector de modelos debe ser dependiente en cascada del selector de marca.
- La capacidad de las filas se administra por volumen total y no por cajones
  numerados. Una fila con `estado = 0` (ocupada) debe ser bloqueada por el
  backend mediante transacciones con bloqueo pesimista (`lockForUpdate()`) para
  evitar condiciones de carrera y sobrecupos entre operadores.
- Las tarifas de los servicios extra provienen de la base de datos o parámetros
  configurables y están sujetas a modificaciones; no deben quedar quemadas de
  forma rígida en controladores o vistas.
- Lee los archivos relacionados antes de editarlos. Conserva las convenciones de
  nombres y mantén la consistencia entre los atributos de la base de datos y las
  interfaces de TypeScript del frontend.

## Seguridad y datos

- Nunca expongas, copies a la documentación ni confirmes credenciales. Mantén
  secretos en el archivo `.env` y fuera del control de versiones.
- Usa consultas preparadas y el ORM para prevenir inyección SQL. Valida y
  sanitiza las entradas tanto en el cliente como en el servidor.
- Encripta siempre las contraseñas de los usuarios mediante el algoritmo de hash
  nativo de Laravel (`Hash::make()`).
- No muestres trazas de error interno, credenciales ni consultas SQL en las
  respuestas JSON de la API.
- Todo cambio en el esquema de base de datos debe respaldarse mediante una
  migración de Laravel con métodos `up()` y `down()` reversibles. Conserva los
  datos locales y documenta cómo aplicar o revertir el cambio.
- Las imágenes de diagramas de inspección vehicular deben guardarse en el disco
  local (`storage/app/public`) y almacenar únicamente su ruta relativa en la base
  de datos; nunca almacenes binarios BLOB en tablas.

## Interfaz, React y Tailwind CSS

- El diseño debe ser responsivo y táctil, preparado para pantallas de tablets
  (mínimo 44px de área de contacto en botones y selectores) y monitores de PC.
- Implementa el módulo de inspección gráfica de daños mediante un canvas o
  esquema vectorial del vehículo que capture coordenadas relativas en porcentaje
  (`coordX`, `coordY`) para registrar múltiples daños normalizados en
  `danio_vehiculo`.
- En el área de inspección táctil en tablets, aplica la propiedad CSS
  `touch-action: none` para impedir el scroll o zoom accidental mientras se
  marcan daños.
- Las tres vistas principales deben respetar el control de acceso basado en
  roles:
  - Operador: registro ágil de ingresos con vuelo, llaves, selección de fila,
    marcas de daños y catálogos dependientes de marca/modelo.
  - Jefatura: dashboard de solo lectura con recaudación acumulada estimada,
    ocupación de filas en vivo y monitoreo de vuelos.
  - Administrador: gestión de usuarios, roles y configuración de capacidad
    de filas.
- Protege las rutas en el frontend mediante un componente de React Router que
  compruebe el token de Sanctum y el rol asignado.

## Verificación

- Usa el entorno local de Apache y MySQL de XAMPP junto con el servidor de Vite
  para validar el funcionamiento entre backend y frontend.
- Ejecuta las comprobaciones pertinentes y verifica manualmente las rutas y
  endpoints afectados antes de dar por cerrada una tarea.
- No ejecutes migraciones destructivas (`migrate:fresh`) ni elimines datos de
  prueba locales sin previa confirmación.
- Actualiza `README.md` cuando cambien variables de entorno, dependencias de
  Composer o dependencias de npm.