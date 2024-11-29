<?php
$pageTitle = "Gestión de Usuarios";
ob_start();

require "../librerias_php/setup_red_bean.php";

// Procesar la eliminación de usuario
if (isset($_POST['eliminar_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    $usuario = R::load('usuario', $id_usuario);
    if ($usuario->id) {
        R::trash($usuario);
        $mensaje = "Usuario eliminado exitosamente.";
        $tipo = "success";
    }
}

// Obtener todos los usuarios
$usuarios = R::findAll('usuario');
?>

<div class="container-fluid py-4">
    <?php
    if (isset($mensaje) && isset($tipo)) {
        echo "<div class='alert alert-$tipo alert-dismissible fade show' role='alert'>
                $mensaje
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Cerrar'></button>
              </div>";
    }
    ?>
    <h1 class="mb-4">Gestión de Usuarios</h1>

    <div class="mb-4">
        <a href="nuevo_usuario.php" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario
        </a>
    </div>

    <?php if (count($usuarios) > 0): ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario->id; ?></td>
                        <td><?php echo htmlspecialchars($usuario->username); ?></td>
                        <td><?php echo htmlspecialchars($usuario->email); ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $usuario->id; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar<?php echo $usuario->id; ?>">
                                <i class="fas fa-trash-alt me-1"></i>Eliminar
                            </button>
                        </td>
                    </tr>
                    <!-- Modal de Confirmación de Eliminación -->
                    <div class="modal fade" id="modalEliminar<?php echo $usuario->id; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Eliminación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de que quieres eliminar al usuario "<?php echo htmlspecialchars($usuario->username); ?>"?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form method="POST">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario->id; ?>">
                                        <button type="submit" name="eliminar_usuario" class="btn btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            No hay usuarios registrados en este momento. ¡Comienza añadiendo un nuevo usuario!
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>