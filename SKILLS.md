# Habilidades requeridas para desarrollar ParkControl

Este archivo describe las capacidades y el flujo de trabajo que DeepSeek debe
aplicar al trabajar en el proyecto.

## PHP 8.2+ y Laravel

- Dominar PHP 8.2 o superior con tipado estricto, atributos, manejo de
  excepciones y separación de responsabilidades.
- Desarrollar endpoints y controladores tipo API REST bajo las convenciones de
  Laravel, empleando Form Requests para validación y API Resources para la
  transformación de datos hacia el frontend.
- Implementar autenticación ligera mediante Laravel Sanctum, gestionando la
  emisión y expiración de tokens según el rol del usuario autenticado.
- Desacoplar la lógica de cálculo de estadía en días, prórrogas y tarifas de
  servicios adicionales en clases de servicio dedicadas (`app/Services/`),
  evitando acumular lógica compleja dentro de los controladores.

## MySQL, Eloquent y Control de Concurrencia

- Diseñar esquemas relacionales estrictos normalizados sin hacer uso de tipos de
  datos o atributos JSON.
- Modelar relaciones directas en Eloquent (`hasMany`, `belongsTo`) entre las
  entidades del sistema: usuarios, roles, clientes, vehículos, marcas, modelos,
  filas, ingresos, daños y servicios.
- Consultar de manera relacional los catálogos de marcas y modelos para proveer
  endpoints eficientes que alimenten los selectores dependientes en cascada.
- Aplicar transacciones de base de datos con bloqueo pesimista (`DB::transaction`
  con `lockForUpdate()`) para evaluar la capacidad de las filas y evitar que dos
  operadores asignen cupos por encima del límite permitido.
- Tratar phpMyAdmin como herramienta de inspección local y versionar todo cambio
  de esquema mediante migraciones reproducibles y reversibles de Laravel.

## React, TypeScript y Tailwind CSS

- Crear aplicaciones SPA estructuradas y tipadas con TypeScript, Vite y React
  Router, organizando el código por módulos o dominios funcionales.
- Implementar rutas protegidas que verifiquen el estado de autenticación y los
  permisos por rol (`OPERADOR`, `JEFATURA`, `ADMINISTRADOR`).
- Desarrollar selectores en cascada para marca y modelo que sincronicen el
  catálogo relacional recibido desde la API de Laravel y autodetecten el tipo de
  vehículo.
- Diseñar interfaces táctiles con Tailwind CSS adaptadas a tablets y monitores
  de PC, cuidando espaciados, tamaños táctiles mínimos y contraste visual.
- Construir componentes de canvas o interacción gráfica para inspección de
  vehículos, manejando eventos de puntero y táctiles (`touchstart`, `touchmove`)
  y calculando coordenadas relativas en porcentaje para el registro de daños.

## Seguridad y Almacenamiento

- Prevenir inyecciones SQL mediante el uso constante de consultas parametrizadas
  y Eloquent ORM.
- Validar exhaustivamente las entradas en el servidor, comprobando rangos de
  fechas, formatos de patente y consistencia entre horas de inicio y término.
- Asegurar contraseñas mediante `Hash::make()` y evitar exponer datos sensibles
  en respuestas públicas.
- Almacenar archivos multimedia o esquemas de daños en el almacenamiento local
  del servidor (`storage/app/public`), guardando únicamente su ruta relativa en la
  base de datos sin recurrir a campos BLOB.

## Flujo de trabajo para cada cambio

1. Inspeccionar los modelos, migraciones, rutas de API y componentes de React
   involucrados antes de realizar cambios de código.
2. Comprobar que las respuestas JSON de Laravel coincidan en nombres y tipos con
   las interfaces TypeScript del frontend.
3. Proteger la integridad de la base de datos en XAMPP y verificar que no existan
   operaciones destructivas no autorizadas.
4. Entregar soluciones modulares, documentadas en español y listas para ser
   probadas en el entorno local de desarrollo.