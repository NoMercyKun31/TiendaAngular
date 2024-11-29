<?php
$pageTitle = "Editar Usuario";
ob_start();

require "../librerias_php/setup_red_bean.php";

$mensaje = '';
$tipo_alerta = '';

if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];
    $usuario = R::load('usuario', $id_usuario);

    if ($usuario->id == 0) {
        $mensaje = "Usuario no encontrado.";
        $tipo_alerta = "danger";
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $usuario->username = $_POST['username'];
            if (!empty($_POST['password'])) {
                $usuario->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            $usuario->email = $_POST['email'];
            
            R::store($usuario);
            
            $mensaje = "El usuario se ha actualizado correctamente.";
            $tipo_alerta = "success";
        } catch (Exception $e) {
            $mensaje = "Ha ocurrido un error al actualizar el usuario: " . $e->getMessage();
            $tipo_alerta = "danger";
        }
    }
} else {
    $mensaje = "ID de usuario no proporcionado.";
    $tipo_alerta = "danger";
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($usuario) && $usuario->id != 0): ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Editar Usuario</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $usuario->id; ?>" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($usuario->username); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña (Dejar en blanco para no cambiar)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario->email); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-3 text-center">
                <a href="gestionar_usuarios.php" class="btn btn-secondary">Volver a la lista de usuarios</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>