<?php
/**
 * ==========================================================================
 * ParkControl · app/views/Administrador/usuarios.php
 * ==========================================================================
 * Gestión de usuarios del sistema (MAQUETA / front-end).
 *
 * El administrador (rol 1) puede crear usuarios, asignarles rol y
 * eliminarlos. La parte lógica (INSERT / UPDATE / DELETE) queda pendiente
 * en los lugares marcados con TODO para que la implementes tú.
 *
 * Protegida: solo accesible con sesión iniciada y rol = 1.
 * ==========================================================================
 */
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

if ((int)$_SESSION['usuario']['rol'] !== 1) {
    header("Location: ../../index.php");
    exit;
}

// ==========================================================================
// TODO (lógica): procesar aquí las acciones POST del formulario.
//   · accion = "crear"        → INSERT INTO usuario ...
//   · accion = "cambiar_rol"  → UPDATE usuario SET idRol = :rol WHERE idUsuario = :id
//   · accion = "eliminar"     → DELETE FROM usuario WHERE idUsuario = :id
//   Antes de guardar: validar, aplicar password_hash() y reutilizar Database::getConexion().
// ==========================================================================

// Rótulos de los roles para la maqueta.
// Ajusta según el catálogo de la BD (actualmente: 1=administra, 2=Administrador).
$roles = [1 => 'Administrador', 2 => 'Operador'];

// Datos de ejemplo para la maqueta (reemplaza por tu consulta a la BD).
$usuariosMaqueta = [
    ['idUsuario' => 1, 'rut' => '12.345.678-9', 'nombres' => 'Diego Andrés',
     'apellidoPaterno' => 'Soto', 'apellidoMaterno' => 'Vargas',
     'correo' => 'diegosoto.vd@gmail.com', 'idRol' => 1],
    ['idUsuario' => 2, 'rut' => '22.111.222-3', 'nombres' => 'María',
     'apellidoPaterno' => 'López', 'apellidoMaterno' => 'Pérez',
     'correo' => 'maria.lopez@correo.cl', 'idRol' => 2],
    ['idUsuario' => 3, 'rut' => '18.444.555-6', 'nombres' => 'Juan',
     'apellidoPaterno' => 'Pérez', 'apellidoMaterno' => 'González',
     'correo' => 'juan.perez@correo.cl', 'idRol' => 2],
];

$titulo  = 'Gestión de Usuarios';
$seccion = 'usuarios';

include __DIR__ . '/../header.php';
?>

<!-- Cabecera de la sección -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Gestión de Usuarios</h2>
        <p class="text-muted mb-0 small">Crea usuarios, asígnales su rol o elimínalos del sistema.</p>
    </div>
    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
        <i class="bi bi-person-plus"></i> Nuevo usuario
    </button>
</div>

<!-- Tabla de usuarios -->
<div class="card pc-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre completo</th>
                        <th>RUT</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuariosMaqueta as $u): ?>
                    <tr>
                        <td><?php echo (int)$u['idUsuario']; ?></td>
                        <td class="fw-semibold">
                            <?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellidoPaterno'] . ' ' . $u['apellidoMaterno']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($u['rut']); ?></td>
                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                        <td>
                            <span class="pc-badge <?php echo (int)$u['idRol'] === 1 ? 'text-bg-primary' : 'text-bg-secondary'; ?>">
                                <?php echo htmlspecialchars($roles[$u['idRol']] ?? 'Rol ' . $u['idRol']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <!-- Editar rol -->
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#modalEditarRol"
                                    data-id="<?php echo (int)$u['idUsuario']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellidoPaterno']); ?>"
                                    data-rol="<?php echo (int)$u['idRol']; ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <!-- Eliminar -->
                            <form action="usuarios.php" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar al usuario <?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellidoPaterno']); ?>?' );">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idUsuario" value="<?php echo (int)$u['idUsuario']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== Modal: Nuevo usuario ===== -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="usuarios.php" method="POST">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoUsuarioTitulo">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nuevoNombres">Nombres</label>
                            <input type="text" class="form-control" id="nuevoNombres" name="nombres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nuevoRut">RUT</label>
                            <input type="text" class="form-control" id="nuevoRut" name="rut" placeholder="12.345.678-9" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nuevoApellidoP">Apellido paterno</label>
                            <input type="text" class="form-control" id="nuevoApellidoP" name="apellidoPaterno" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nuevoApellidoM">Apellido materno</label>
                            <input type="text" class="form-control" id="nuevoApellidoM" name="apellidoMaterno">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nuevoCorreo">Correo electrónico</label>
                            <input type="email" class="form-control" id="nuevoCorreo" name="correo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nuevoContrasena">Contraseña</label>
                            <input type="password" class="form-control" id="nuevoContrasena" name="contrasena" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nuevoRol">Rol</label>
                            <select class="form-select" id="nuevoRol" name="idRol" required>
                                <?php foreach ($roles as $id => $nombre): ?>
                                    <option value="<?php echo (int)$id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <!-- TODO: al enviar, procesa el INSERT en la parte superior del archivo -->
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Modal: Editar rol ===== -->
<div class="modal fade" id="modalEditarRol" tabindex="-1" aria-labelledby="modalEditarRolTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="usuarios.php" method="POST">
                <input type="hidden" name="accion" value="cambiar_rol">
                <input type="hidden" name="idUsuario" id="editarId" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarRolTitulo">Cambiar rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Usuario: <strong id="editarNombre"></strong></p>
                    <label class="form-label" for="editarRol">Rol</label>
                    <select class="form-select" id="editarRol" name="idRol">
                        <?php foreach ($roles as $id => $nombre): ?>
                            <option value="<?php echo (int)$id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <!-- TODO: al enviar, procesa el UPDATE del rol -->
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar rol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Carga datos del usuario en el modal "Editar rol" -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEditar = document.getElementById('modalEditarRol');
        modalEditar.addEventListener('show.bs.modal', function (event) {
            const boton = event.relatedTarget;
            document.getElementById('editarId').value = boton.getAttribute('data-id');
            document.getElementById('editarNombre').textContent = boton.getAttribute('data-nombre');
            document.getElementById('editarRol').value = boton.getAttribute('data-rol');
        });
    });
</script>

<?php include __DIR__ . '/../footer.php'; ?>
