<?php
$pageTitle = "Registro de Videojuego";
ob_start();

require "../librerias_php/setup_red_bean.php";

$mensaje = '';
$tipo_alerta = '';

// Definir las categorías disponibles
$categorias = [
    'Acción',
    'Aventura',
    'RPG',
    'Estrategia',
    'Deportes',
    'Simulación',
    'Puzzle',
    'Carreras',
    'Shooter',
    'Plataformas'
];

$imagen_generica = "../imagenes/placeholder.jpg";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $videojuego = R::dispense('videojuego');
        $videojuego->nombre = $_POST['nombre'];
        $videojuego->categoria = $_POST['categoria'];
        $videojuego->compania = $_POST['compania'];
        $videojuego->anyolanzamiento = $_POST['anyolanzamiento'];
        $videojuego->precio = $_POST['precio'];
        $videojuego->stock = max(0, min(99, intval($_POST['stock'])));
        $videojuego->descuento = 0;
        $videojuego->en_descuento = false;
        
        $id_generada = R::store($videojuego);
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nuevo_nombre = $id_generada . '.' . $extension;
            $ruta_destino = "../imagenes/" . $nuevo_nombre;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
                $videojuego->foto = $nuevo_nombre;
                R::store($videojuego);
                $mensaje = "El videojuego se ha registrado correctamente y la imagen se ha subido.";
                $tipo_alerta = "success";
            } else {
                $mensaje = "El videojuego se ha registrado, pero hubo un problema al subir la imagen. Se usará la imagen genérica.";
                $tipo_alerta = "warning";
                copy($imagen_generica, "../imagenes/" . $id_generada . ".jpg");
            }
        } else {
            $mensaje = "El videojuego se ha registrado correctamente. Se usará la imagen genérica.";
            $tipo_alerta = "info";
            copy($imagen_generica, "../imagenes/" . $id_generada . ".jpg");
        }
    } catch (Exception $e) {
        $mensaje = "Ha ocurrido un error al registrar el videojuego: " . $e->getMessage();
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
                    <h2 class="mb-0">Registro de Videojuego</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="videojuegoForm" novalidate>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   minlength="2" maxlength="100" pattern="^(?!.*\s{4})[A-Za-zÀ-ÿ0-9\s']{2,100}$">
                            <div class="invalid-feedback" id="nombreFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo htmlspecialchars($categoria); ?>">
                                        <?php echo htmlspecialchars($categoria); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="categoriaFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="compania" class="form-label">Compañía</label>
                            <input type="text" class="form-control" id="compania" name="compania" required
                                   minlength="2" maxlength="100" pattern="^(?!.*\s{4})[A-Za-zÀ-ÿ0-9\s']{2,100}$">
                            <div class="invalid-feedback" id="companiaFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="anyolanzamiento" class="form-label">Año de Lanzamiento</label>
                            <input type="number" class="form-control" id="anyolanzamiento" name="anyolanzamiento" 
                                   required min="1950" max="2030">
                            <div class="invalid-feedback" id="anyolanzamientoFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" 
                                       step="0.01" min="0.01" max="999.99" required>
                                <div class="invalid-feedback" id="precioFeedback"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   min="0" max="99" required>
                            <div class="invalid-feedback" id="stockFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <small class="form-text text-muted">Si no se sube una imagen, se usará una imagen genérica.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar Videojuego</button>
                    </form>
                </div>
            </div>

            <div class="mt-3 text-center">
                <a href="gestionar_productos.php" class="btn btn-secondary">Volver a la lista de videojuegos</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('videojuegoForm');
    const inputs = ['nombre', 'compania'];
    
    // Función para validar un campo
    function validateField(input) {
        const field = document.getElementById(input);
        const feedback = document.getElementById(input + 'Feedback');
        let isValid = true;
        let message = '';

        // Validar que no esté vacío
        if (!field.value.trim()) {
            isValid = false;
            message = 'Este campo es obligatorio';
        }
        // Validar longitud mínima
        else if (field.value.length < 2) {
            isValid = false;
            message = 'Debe tener al menos 2 caracteres';
        }
        // Validar longitud máxima
        else if (field.value.length > 100) {
            isValid = false;
            message = 'No puede tener más de 100 caracteres';
        }
        // Validar espacios consecutivos
        else if (/\s{4,}/.test(field.value)) {
            isValid = false;
            message = 'No se permiten más de 3 espacios consecutivos';
        }
        // Validar caracteres permitidos
        else if (!/^[A-Za-zÀ-ÿ0-9\s']+$/.test(field.value)) {
            isValid = false;
            message = 'Solo se permiten letras, números, espacios y apóstrofes';
        }

        // Actualizar la UI
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

    // Validar categoría
    function validateCategoria() {
        const categoria = document.getElementById('categoria');
        const feedback = document.getElementById('categoriaFeedback');
        const isValid = categoria.value !== '';

        if (!isValid) {
            categoria.classList.add('is-invalid');
            categoria.classList.remove('is-valid');
            feedback.textContent = 'Debe seleccionar una categoría';
        } else {
            categoria.classList.remove('is-invalid');
            categoria.classList.add('is-valid');
            feedback.textContent = '';
        }

        return isValid;
    }

    // Función para validar campos numéricos
    function validateNumericField(input, min, max, isInteger = true) {
        const field = document.getElementById(input);
        const feedback = document.getElementById(input + 'Feedback');
        let isValid = true;
        let message = '';
        
        const value = isInteger ? parseInt(field.value) : parseFloat(field.value);

        if (!field.value) {
            isValid = false;
            message = 'Este campo es obligatorio';
        } else if (isNaN(value)) {
            isValid = false;
            message = 'Debe ser un número válido';
        } else if (value < min) {
            isValid = false;
            message = `El valor mínimo es ${min}`;
        } else if (value > max) {
            isValid = false;
            message = `El valor máximo es ${max}`;
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

    // Agregar validación en tiempo real para cada campo
    inputs.forEach(input => {
        const field = document.getElementById(input);
        field.addEventListener('input', () => validateField(input));
        field.addEventListener('blur', () => validateField(input));
    });

    // Validación para categoría
    const categoria = document.getElementById('categoria');
    categoria.addEventListener('change', validateCategoria);

    // Validar año de lanzamiento
    const anyolanzamiento = document.getElementById('anyolanzamiento');
    anyolanzamiento.addEventListener('input', () => validateNumericField('anyolanzamiento', 1950, 2030, true));
    anyolanzamiento.addEventListener('blur', () => validateNumericField('anyolanzamiento', 1950, 2030, true));

    // Validar precio
    const precio = document.getElementById('precio');
    precio.addEventListener('input', () => validateNumericField('precio', 0.01, 999.99, false));
    precio.addEventListener('blur', () => validateNumericField('precio', 0.01, 999.99, false));

    // Validar stock
    const stock = document.getElementById('stock');
    stock.addEventListener('input', () => validateNumericField('stock', 0, 99, true));
    stock.addEventListener('blur', () => validateNumericField('stock', 0, 99, true));

    // Validación del formulario completo antes de enviar
    form.addEventListener('submit', function(event) {
        let isValid = true;

        // Validar campos de texto
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        // Validar categoría
        if (!validateCategoria()) {
            isValid = false;
        }

        // Validar campos numéricos
        if (!validateNumericField('anyolanzamiento', 1950, 2030, true)) {
            isValid = false;
        }
        if (!validateNumericField('precio', 0.01, 999.99, false)) {
            isValid = false;
        }
        if (!validateNumericField('stock', 0, 99, true)) {
            isValid = false;
        }

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