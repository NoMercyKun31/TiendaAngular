<?php
$pageTitle = "Insertar Usuarios de Ejemplo";
ob_start();

require "../librerias_php/setup_red_bean.php";

$mensaje = '';
$tipo_alerta = '';

function insertarUsuariosEjemplo() {
    $usuarios = [
        ['username' => 'alicia.mendez', 'email' => 'alicia.mendez@ejemplo.com', 'password' => 'segura123'],
        ['username' => 'carlos.garcia', 'email' => 'carlos.garcia@ejemplo.com', 'password' => 'clave987'],
        ['username' => 'isabel.ruiz', 'email' => 'isabel.ruiz@ejemplo.com', 'password' => '123isabel'],
        ['username' => 'roberto.molina', 'email' => 'roberto.molina@ejemplo.com', 'password' => 'roberto456'],
        ['username' => 'valeria.lopez', 'email' => 'valeria.lopez@ejemplo.com', 'password' => 'valeria#2023'],
        ['username' => 'andres.villalba', 'email' => 'andres.villalba@ejemplo.com', 'password' => 'villalba*123'],
        ['username' => 'sofia.rojas', 'email' => 'sofia.rojas@ejemplo.com', 'password' => 'sofiapass'],
        ['username' => 'david.fernandez', 'email' => 'david.fernandez@ejemplo.com', 'password' => 'david1987'],
        ['username' => 'emilia.martinez', 'email' => 'emilia.martinez@ejemplo.com', 'password' => 'emilia1234'],
        ['username' => 'martin.torres', 'email' => 'martin.torres@ejemplo.com', 'password' => 'torrespass']
    ];

    $insertados = 0;

    foreach ($usuarios as $datos_usuario) {
        $usuario = R::dispense('usuario');
        $usuario->username = $datos_usuario['username'];
        $usuario->email = $datos_usuario['email'];
        $usuario->password = password_hash($datos_usuario['password'], PASSWORD_DEFAULT);

        try {
            R::store($usuario);
            $insertados++;
        } catch (Exception $e) {
            continue;
        }
    }

    return $insertados;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarios_insertados = insertarUsuariosEjemplo();
    $mensaje = "Se han insertado $usuarios_insertados usuarios de ejemplo.";
    $tipo_alerta = "success";
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

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Insertar Usuarios de Ejemplo</h2>
                </div>
                <div class="card-body">
                    <p>Este proceso insertará 10 usuarios de ejemplo en la base de datos.</p>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <button type="submit" class="btn btn-primary">Insertar Usuarios de Ejemplo</button>
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
