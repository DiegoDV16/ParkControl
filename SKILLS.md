# Habilidades requeridas para desarrollar ParkControl

Este archivo describe las capacidades y el flujo de trabajo que DeepSeek debe
aplicar al trabajar en el proyecto.

## PHP 8.2+

- Dominar PHP 8.2 o superior, incluyendo tipos, manejo de excepciones,
  sesiones, organización por responsabilidades y prácticas compatibles con
  versiones actuales.
- Mantener compatibilidad con la versión de PHP configurada en XAMPP. Verificar
  esa versión antes de proponer sintaxis o ejecutar pruebas.
- Seguir el patrón MVC actual. Separar presentación, lógica de control y acceso
  a datos; no añadir lógica SQL directamente a las vistas.
- Usar `password_hash()` y `password_verify()` para contraseñas y evitar
  funciones obsoletas o inseguras.

## Laravel

- Conocer rutas, controladores, modelos Eloquent, Blade, validación,
  autenticación, middleware, migraciones, seeders, configuración y Artisan.
- Aplicar estas prácticas cuando Laravel esté efectivamente instalado o cuando
  la tarea solicite incorporarlo.
- Antes de implementar una función Laravel, comprobar `composer.json`,
  dependencias y estructura del proyecto. Si no existe Laravel, no mezclar
  fragmentos Laravel con la aplicación PHP clásica: señalar el requisito y
  realizar únicamente los cambios solicitados.
- Al incorporar Laravel, respetar el flujo de instalación y la configuración
  para XAMPP/Apache; mantener secretos en `.env` y nunca publicar ese archivo.

## MySQL, phpMyAdmin y XAMPP

- Diseñar tablas y relaciones con claves primarias y foráneas, restricciones,
  índices adecuados y tipos de datos coherentes.
- Conocer SQL para MySQL, transacciones, integridad referencial y consultas
  parametrizadas.
- Tratar phpMyAdmin como interfaz para inspeccionar y administrar MySQL, no
  como parte de la aplicación ni como mecanismo único para distribuir cambios
  de esquema.
- Versionar cambios de estructura como migraciones de Laravel si está
  disponible; para la aplicación actual, entregar instrucciones SQL
  reproducibles y explicar cómo aplicarlas en el entorno local de XAMPP.
- No asumir nombres de tablas, columnas, credenciales ni datos de conexión:
  inspeccionar el código y el esquema disponible. No incluir secretos en
  respuestas ni archivos.

## Seguridad web

- Prevenir inyección SQL con consultas preparadas o Eloquent.
- Prevenir XSS escapando datos al generar HTML; prevenir CSRF y proteger
  sesiones y autorización en los formularios y acciones correspondientes.
- Validar datos en el servidor aunque también exista validación en el
  navegador.
- Al gestionar archivos, comprobar tipo, tamaño y nombre, y evitar que las
  cargas puedan ejecutarse como código.

## Bootstrap 5 y JavaScript

- Usar Bootstrap 5 (servido localmente en `app/assets/vendor/`) para modales,
  alertas, dropdowns, tablas y barras de progreso.
- Implementar los formularios de alta y edición de usuarios y clientes como
  modales sobre el listado, reutilizando el mismo markup y el JS compartido.
- Escribir JavaScript progresivo en `app/assets/js/main.js`, sin manejadores
  inline, usando atributos `data-*` y APIs de Bootstrap
  (`bootstrap.Modal`, `bootstrap.Alert`).
- Confirmar acciones destructivas con un modal de confirmación reutilizable.
- Validar siempre en el servidor; el JavaScript es una ayuda de interfaz.

## Flujo de trabajo para cada cambio

1. Identificar los archivos y el flujo MVC implicados; revisar el README y los
   patrones existentes antes de editar.
2. Confirmar si el cambio pertenece a PHP clásico o a Laravel. No cambiar de
   arquitectura por iniciativa propia.
3. Implementar la solución más pequeña que complete el requisito, cuidando
   seguridad, compatibilidad PHP 8.2+ y funcionamiento con MySQL.
4. Si hay cambios de base de datos, incluir pasos reproducibles y proteger los
   datos existentes.
5. Ejecutar las comprobaciones pertinentes y resumir qué se verificó y qué no
   pudo probarse en el entorno local.
6. Actualizar la documentación relacionada cuando cambien la configuración o
   los pasos de uso.
