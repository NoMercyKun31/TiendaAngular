<?php
$pageTitle = "Registro de Usuario";
ob_start();

require "../librerias_php/setup_red_bean.php";

$mensaje = '';
$tipo_alerta = '';

// Verificar si se ha enviado un formulario por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Crear un nuevo objeto usuario
        $usuario = R::dispense('usuario');

        // Asignar los valores del formulario al objeto usuario
        $usuario->username = $_POST['username'];
        // Encriptar la contraseña antes de guardarla
        $usuario->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $usuario->email = $_POST['email'];

        // Guardar el usuario en la base de datos
        $id_generada = R::store($usuario);

        // Mostrar mensaje de éxito
        $mensaje = "El usuario se ha registrado correctamente.";
        $tipo_alerta = "success";
    } catch (Exception $e) {
        // Mostrar mensaje de error
        $mensaje = "Ha ocurrido un error al registrar el usuario: " . $e->getMessage();
        $tipo_alerta = "danger";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Registro de Usuario</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>

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