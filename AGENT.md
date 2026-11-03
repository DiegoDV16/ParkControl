# Instrucciones para el agente de ParkControl

Estas instrucciones están dirigidas a DeepSeek y a cualquier agente que
desarrolle o mantenga este proyecto.

## Contexto del proyecto

- ParkControl es un sistema de control de estacionamiento con interfaz y textos
  en español.
- El código actual está organizado como una aplicación PHP MVC clásica bajo
  `app/`, con vistas en `app/views/`, recursos en `app/assets/` y conexión PDO
  en `app/config/conexion.php`.
- La base de datos prevista es MySQL, ejecutada localmente con XAMPP y
  administrada con phpMyAdmin.
- La documentación actual describe PHP clásico, no una aplicación Laravel.
  Antes de usar Artisan, migraciones, Eloquent o componentes Laravel, verifica
  que Composer y Laravel estén instalados. No presentes esos comandos como
  disponibles si no lo están.

## Reglas de desarrollo

- Escribe código compatible con PHP 8.2 o superior. Usa sintaxis moderna con
  tipos cuando aporte claridad y comprueba la versión instalada antes de
  ejecutar validaciones.
- Respeta la estructura MVC y los patrones existentes. No migres el proyecto a
  Laravel ni cambies su arquitectura sin una solicitud explícita.
- Si se solicita desarrollar o migrar a Laravel, usa las convenciones del
  framework: rutas, controladores, modelos, validación, middleware, Blade,
  configuración por entorno y migraciones. Explica los cambios de estructura y
  dependencias necesarios.
- Accede a MySQL desde la aplicación mediante PDO o, si Laravel ya está
  instalado, mediante sus componentes de base de datos. phpMyAdmin es una
  herramienta administrativa; no lo uses como sustituto de validaciones,
  consultas parametrizadas o migraciones versionadas.
- Mantén textos, mensajes de interfaz y documentación para usuarios en español.
- Conserva Bootstrap y los estilos propios `pc-` existentes, salvo que la tarea
  pida un cambio de diseño.
- Lee los archivos relacionados antes de editarlos. Evita cambios no
  relacionados y conserva las convenciones de nombres y el comportamiento
  existente.

## Seguridad y datos

- Nunca expongas, copies a la documentación ni confirmes credenciales.
  Mantén secretos fuera del repositorio y usa configuración local o variables
  de entorno según el patrón disponible.
- Usa consultas preparadas; valida y normaliza entradas del usuario.
- Escapa datos al renderizarlos en HTML y usa las protecciones CSRF y de sesión
  apropiadas para el flujo existente o para Laravel.
- No muestres errores internos, consultas SQL ni credenciales al usuario.
- Para cambios en el esquema MySQL, conserva los datos y documenta la forma de
  aplicar y revertir el cambio. Si Laravel está instalado, crea migraciones;
  en caso contrario, proporciona SQL explícito y seguro para el entorno local.

## Verificación

- Usa Apache y MySQL de XAMPP para probar flujos que dependan del servidor o de
  la base de datos. No des por válida una prueba que solo comprueba que un
  archivo existe.
- Ejecuta las pruebas o comprobaciones disponibles en el repositorio. Si no
  hay pruebas automatizadas o herramientas configuradas, indícalo y valida
  manualmente las rutas afectadas cuando sea posible.
- No ejecutes migraciones destructivas ni borres datos locales.
- Actualiza `README.md` cuando cambien los pasos de instalación, configuración
  o ejecución.
