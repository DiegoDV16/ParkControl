# Instrucciones para el agente de ParkControl

Estas instrucciones están dirigidas a DeepSeek y a cualquier agente de IA o desarrollador que desarrolle, refactorice o mantenga este proyecto.

---

## 1. Contexto y Arquitectura del Proyecto

- **ParkControl** es un sistema integral de gestión y control de estacionamientos en tiempo real, operado desde PC de escritorio y Tablets táctiles.
- **Idioma:** Todos los textos de la interfaz, validaciones, comentarios y documentación deben mantenerse exclusivamente en **español**.
- **Arquitectura:** Monolito desacoplado en dos capas principales:
  - **Backend (API REST):** PHP 8.2+ con **Laravel**. Expone endpoints JSON limpios, gestiona la autenticación con **Laravel Sanctum**, aplica reglas de negocio y maneja el control de concurrencia.
  - **Frontend (SPA):** **React** inicializado con **Vite**, **TypeScript**, **React Router** y estilizado con **Tailwind CSS**. Optimizado con áreas táctiles amplias para tablets en garita/terreno.
  - **Base de Datos:** **MySQL / MariaDB** ejecutado localmente mediante **XAMPP** y administrado vía **phpMyAdmin**.

---

## 2. Reglas del Modelo de Datos (Esquema Relacional Estricto)

Queda **estrictamente prohibido el uso de campos JSON** para almacenar información del negocio. Todo dato debe persistir en columnas atómicas y tablas normalizadas mediante migraciones de Laravel.

### Estructura de Tablas:
1. **`rol`**: `idRol` (PK), `rol`.
2. **`usuario`**: `idUsuario` (PK), `idRol` (FK), `rut`, `nombres`, `apellidoPaterno`, `apellidoMaterno`, `correo`, `contrasena`.
3. **`cliente`**: `idCliente` (PK), `rut`, `nombres`, `apellidoPaterno`, `apellidoMaterno`, `telefono`, `correo`.
4. **`marca`**: `idMarca` (PK), `marca`.
5. **`modelo`**: `idModelo` (PK), `idMarca` (FK), `modelo`.
6. **`vehiculo`**: `idVehiculo` (PK), `patente`, `anio`, `color`, `idModelo` (FK).
7. **`fila`**: `idFila` (PK), `fila`, `capacidadTotal`, `estado`. *(Nota: Los cupos no se numeran individualmente; se asignan por capacidad de fila, ej. Fila A: 16 cupos).*
8. **`servicioextra`**: `idServicioExtra` (PK), `servicioExtra`.
9. **`ingreso`**: `idIngreso` (PK), `idServicioExtra` (FK nullable), `idUsuario` (FK), `idVehiculo` (FK), `idFilaEstacionamiento` (FK -> `fila.idFila`), `idCliente` (FK), `fechaInicio` (date), `horaInicio` (time), `fechaTermino` (date nullable), `horaTermino` (time nullable), `numeroVuelo` (varchar nullable), `llaves` (boolean), `observacion` (text nullable).
10. **`danio_vehiculo`**: `idDanio` (PK), `idIngreso` (FK), `coordX` (decimal(5,2)), `coordY` (decimal(5,2)), `tipoDanio` (varchar), `detalle` (varchar nullable).
11. **`pago`**: `idPago` (PK), `idIngreso` (FK), `montoTotal` (int), `metodoPago` (varchar), `fechaHoraPago` (datetime).

---

## 3. Módulos y Control de Acceso (RBAC)

El sistema opera con tres perfiles delimitados mediante middlewares en Laravel y rutas protegidas en React Router:

1. **Operador (Optimizado para Tablet táctil):**
   - Registro ágil de entrada/salida de vehículos y clientes.
   - Selector visual de filas con barras de ocupación en tiempo real (`ocupados / capacidadTotal`).
   - Módulo de inspección gráfica sobre silueta vehicular: captura toques táctiles, calcula coordenadas relativas en porcentaje (`coordX`, `coordY`) y las asocia a `danio_vehiculo`.
2. **Jefatura (Dashboard de Supervisión - Solo Lectura):**
   - Monitoreo en vivo de capacidad y porcentaje de ocupación por fila.
   - Métrica en tiempo real de recaudación total diaria basada en la tabla `pago`.
   - Bloqueo total de acciones de edición o eliminación de registros.
3. **Administrador:**
   - CRUD integral de usuarios y asignación de roles.
   - Configuración de filas: alta, baja lógica y ajuste de capacidades máximas.

---

## 4. Reglas de Desarrollo Backend (Laravel & PHP)

- Escribe código compatible con **PHP 8.2+** utilizando tipado estricto, Form Requests para validación y Resources para la respuesta de la API.
- **Control de Concurrencia Obligatorio:** En la asignación de filas, nunca asumas disponibilidad con un simple conteo. Implementa transacciones con bloqueo pesimista (`DB::transaction` y `lockForUpdate()`) para impedir sobrecupo si dos operadores asignan en simultáneo:
  ```php
  $fila = Fila::where('idFila',$filaId)->lockForUpdate()->first();