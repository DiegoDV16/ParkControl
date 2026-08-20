<?php
session_start();

require_once __DIR__ . '/../../config/conexion.php';

// Sin sesión → login
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

// Solo rol 1 (administra) puede ver este panel
if ((int)$_SESSION['usuario']['rol'] !== 1) {
    header("Location: ../../index.php");
    exit;
}

$usuario = $_SESSION['usuario'];


include __DIR__ . '/../header.php';
?>

<!-- Bienvenida -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Hola, <?php echo htmlspecialchars($usuario['nombres']); ?></h2>
        <p class="text-muted mb-1 small">· Administrador del sistema.</p>
    </div>
</div>

<!-- Accesos rápidos (módulos en desarrollo) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <a href="usuarios.php" class="text-decoration-none">
            <div class="card pc-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="pc-card-icono azul"><i class="bi bi-person-plus"></i></span>
                    <div>
                        <div class="fw-semibold">Nuevo usuario</div>
                        <div class="text-muted small">Crear usuario del sistema</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>


<div class="row g-3 mb-4">
    <!-- Últimos usuarios registrados (se poblará con el CRUD) -->
    <div class="col-12">
        <div class="card pc-card overflow-hidden">
            <!-- Cabecera de la tarjeta -->
            <div class="card-header bg-white d-flex justify-content-between align-items-center gap-2 px-3 py-3 border-bottom-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="pc-card-icono azul" style="width:38px; height:38px; font-size:1.1rem;"><i class="bi bi-people"></i></span>
                    <div>
                        <h3 class="h6 fw-bold mb-0">Últimos usuarios registrados</h3>
                        <span class="small text-muted">3 usuarios en el sistema</span>
                    </div>
                </div>
                <a href="usuarios.php" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                    Ver todos <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <!-- Últimos 3 usuarios registrados (consulta a la BD) -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 pc-tabla w-100">
                    <thead>
                        <tr>
                            <th style="width:40%">Usuario</th>
                            <th style="width:35%">Correo</th>
                            <th style="width:25%">Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = Database::getConexion();
                        $query = $conn->prepare("
                            SELECT usuario.nombres, usuario.apellidoPaterno, usuario.apellidoMaterno,
                                   usuario.correo, rol.rol
                            FROM usuario
                            INNER JOIN rol ON usuario.idRol = rol.idRol
                            ORDER BY usuario.idUsuario DESC
                            LIMIT 3
                        ");
                        $query->execute();
                        $result = $query->fetchAll();
                        foreach ($result as $row) {
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-circle-user fs-3 text-secondary"></i>
                                    <div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($row['nombres'] . ' ' . $row['apellidoPaterno'] . ' ' . $row['apellidoMaterno']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['correo']); ?></td>
                            <td><span class="pc-badge text-bg-primary"><i class="bi bi-shield-check me-1"></i><?php echo htmlspecialchars($row['rol']); ?></span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
