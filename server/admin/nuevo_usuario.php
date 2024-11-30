<?php
$pageTitle = "Registro de Usuario";
ob_start();

require "../librerias_php/setup_red_bean.php";

$mensaje = '';
$tipo_alerta = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $usuario = R::dispense('usuario');
        $usuario->username = $_POST['username'];
        $usuario->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $usuario->email = $_POST['email'];
        
        $id_generada = R::store($usuario);
        
        $mensaje = "El usuario se ha registrado correctamente.";
        $tipo_alerta = "success";
    } catch (Exception $e) {
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
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="registroUsuarioForm" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required minlength="2" maxlength="10">
                            <div class="invalid-feedback" id="usernameFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="2" maxlength="10">
                            <div class="invalid-feedback" id="passwordFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="emailFeedback"></div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroUsuarioForm');

    function validateField(input) {
        const field = document.getElementById(input);
        const feedback = document.getElementById(input + 'Feedback');
        let isValid = true;
        let message = '';

        if (input === 'username' || input === 'password') {
            field.value = field.value.trim();
        }

        if (!field.value) {
            isValid = false;
            message = 'Este campo es obligatorio';
        } else if (input === 'username') {
            if (field.value.length < 2 || field.value.length > 10) {
                isValid = false;
                message = 'El nombre de usuario debe tener entre 2 y 10 caracteres';
            } else if (/\s{4,}/.test(field.value)) {
                isValid = false;
                message = 'No se permiten más de 3 espacios consecutivos';
            }
        } else if (input === 'password') {
            if (field.value.length < 2 || field.value.length > 10) {
                isValid = false;
                message = 'La contraseña debe tener entre 2 y 10 caracteres';
            } else if (/\s/.test(field.value)) {
                isValid = false;
                message = 'La contraseña no puede contener espacios';
            } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).*$/.test(field.value)) {
                isValid = false;
                message = 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial';
            }
        } else if (input === 'email') {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                isValid = false;
                message = 'Ingrese un correo electrónico válido';
            }
        }

        if (!isValid) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            feedback.textContent = message;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            feedback.textContent = '';
        }

        return isValid;
    }

    const inputs = ['username', 'password', 'email'];
    inputs.forEach(input => {
        const field = document.getElementById(input);
        field.addEventListener('input', () => validateField(input));
        field.addEventListener('blur', () => validateField(input));
    });

    form.addEventListener('submit', function(event) {
        let isValid = true;

        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>