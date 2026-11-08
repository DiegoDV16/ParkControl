# Habilidades requeridas para desarrollar ParkControl

Este archivo describe las capacidades y el flujo de trabajo que DeepSeek debe aplicar al trabajar en el proyecto.

## PHP 8.2+ y Laravel

- Dominar PHP 8.2 o superior, aprovechando tipado estricto, atributos nativos, enumeraciones y manejo robusto de excepciones.
- Conocer la arquitectura moderna de Laravel (10/11+): rutas API (`routes/api.php`), controladores tipo API Resource, Form Requests con reglas de validación exhaustivas y middlewares personalizados.
- Manejar autenticación mediante Laravel Sanctum emitiendo y revocando tokens de acceso según el rol del usuario autenticado.
- Construir lógica de negocio desacoplada: separar las reglas complejas (como cálculos de tarifas o asignación de cupos) en servicios dedicados (`app/Services/`), evitando sobrecargar los controladores.

## MySQL, Eloquent ORM y Control de Concurrencia

- Diseñar y mantener esquemas relacionales estrictos sin el uso de columnas JSON. Toda entidad y detalle debe normalizarse en tablas y columnas atómicas.
- Dominar Eloquent ORM, definiendo relaciones explícitas (`belongsTo`, `hasMany`) con claves foráneas e integridad referencial garantizada a nivel de base de datos.
- Aplicar transacciones atómicas con bloqueo pesimista (`DB::transaction` con `lockForUpdate()`) al validar la disponibilidad de filas antes de insertar un ticket, previniendo condiciones de carrera y sobrecupos por accesos simultáneos desde tablets.
- Versionar todo cambio de estructura exclusivamente a través de migraciones de Laravel con métodos `up()` y `down()` reversibles. Usar phpMyAdmin y XAMPP solo para inspección y administración local.

## React, Vite y TypeScript (Frontend)

- Desarrollar Single Page Applications (SPA) con React utilizando TypeScript, React Router (`createBrowserRouter` o `<Routes>`) y Vite como empaquetador rápido.
- Implementar rutas protegidas (`ProtectedRoute`) basadas en tokens Sanctum y validación estricta de roles (`OPERADOR`, `JEFATURA`, `ADMINISTRADOR`).
- Organizar el frontend mediante arquitectura basada en módulos o funcionalidades (`features/parking-entry`, `features/management`, `features/admin`, `shared/components`).
- Aplicar Code Splitting con `React.lazy()` y `Suspense` para optimizar la carga inicial en dispositivos móviles y tablets.

## Diseño Táctil y Tailwind CSS

- Diseñar interfaces 100% responsivas optimizadas con Tailwind CSS para pantallas táctiles de tablets (en garita/terreno) y monitores de PC (jefatura/administración).
- Garantizar accesibilidad táctil: áreas de pulsación mínimas de 44x44 píxeles (`min-h-[44px]`),