<?php
$pageTitle = "Editar Videojuego";
ob_start();

require "../librerias_php/setup_red_bean.php";

$id = $_GET['id'] ?? null;
$mensaje = '';
$tipo_alerta = '';

if (!$id) {
    header("Location: gestion_producto.php");
    exit;
}

$videojuego = R::load('videojuego', $id);

if ($videojuego->id == 0) {
    header("Location: gestion_producto.php");
    exit;
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $videojuego->nombre = $_POST['nombre'];
        $videojuego->categoria = $_POST['categoria'];
        $videojuego->compania = $_POST['compania'];
        $videojuego->anyolanzamiento = $_POST['anyolanzamiento'];
        $videojuego->precio = $_POST['precio'];
        $videojuego->stock = max(0, min(99, intval($_POST['stock'])));
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            // Eliminar la imagen anterior si existe
            $imagen_anterior = "../imagenes/{$videojuego->id}.jpg";
            if (file_exists($imagen_anterior)) {
                unlink($imagen_anterior);
            }

            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nuevo_nombre = $videojuego->id . '.' . $extension;
            $ruta_destino = "../imagenes/" . $nuevo_nombre;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
                $videojuego->foto = $nuevo_nombre;
            } else {
                $mensaje = "El videojuego se ha actualizado, pero hubo un problema al subir la nueva imagen.";
                $tipo_alerta = "warning";
            }
        }
        
        R::store($videojuego);
        $mensaje = "El videojuego se ha actualizado correctamente.";
        $tipo_alerta = "success";
    } catch (Exception $e) {
        $mensaje = "Ha ocurrido un error al actualizar el videojuego: " . $e->getMessage();
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
                    <h2 class="mb-0">Editar Videojuego</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="post" enctype="multipart/form-data" id="editarVideojuegoForm" novalidate>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($videojuego->nombre); ?>" required maxlength="100">
                            <div class="invalid-feedback">El nombre es requerido y debe tener máximo 100 caracteres.</div>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo htmlspecialchars($categoria); ?>" <?php echo $videojuego->categoria == $categoria ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Seleccione una categoría.</div>
                        </div>
                        <div class="mb-3">
                            <label for="compania" class="form-label">Compañía</label>
                            <input type="text" class="form-control" id="compania" name="compania" value="<?php echo htmlspecialchars($videojuego->compania); ?>" required maxlength="100">
                            <div class="invalid-feedback">La compañía es requerida y debe tener máximo 100 caracteres.</div>
                        </div>
                        <div class="mb-3">
                            <label for="anyolanzamiento" class="form-label">Año de Lanzamiento</label>
                            <input type="number" class="form-control" id="anyolanzamiento" name="anyolanzamiento" value="<?php echo htmlspecialchars($videojuego->anyolanzamiento); ?>" required min="1900" max="2099">
                            <div class="invalid-feedback">Ingrese un año válido entre 1900 y 2099.</div>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($videojuego->precio); ?>" required min="0.01" max="9999.99">
                            </div>
                            <div class="invalid-feedback">Ingrese un precio válido entre 0.01 y 9999.99.</div>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" max="99" value="<?php echo htmlspecialchars($videojuego->stock); ?>" required>
                            <div class="invalid-feedback">Ingrese un stock válido entre 0 y 99.</div>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto actual</label> <br/>
                            <img src="../imagenes/<?php echo $videojuego->id; ?>.jpg" alt="Foto actual" class="img-thumbnail mb-2" style="max-width: 200px;">
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <small class="form-text text-muted">Deja este campo vacío si no quieres cambiar la imagen.</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Actualizar Videojuego</button>
                            <a href="gestionar_productos.php" class="btn btn-secondary">Volver a la lista</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editarVideojuegoForm');

    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    }, false);

    // Validación personalizada para campos de texto
    const textInputs = ['nombre', 'compania'];
    textInputs.forEach(function(inputId) {
        const input = document.getElementById(inputId);
        input.addEventListener('input', function() {
            this.value = this.value.trim();
            if (this.value.length > 100) {
                this.value = this.value.slice(0, 100);
            }
            this.setCustomValidity(this.value ? '' : 'Este campo es requerido.');
        });
    });

    // Validación para año de lanzamiento
    const anyoInput = document.getElementById('anyolanzamiento');
    anyoInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        if (isNaN(value) || value < 1900 || value > 2099) {
            this.setCustomValidity('Ingrese un año válido entre 1900 y 2099.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación para precio
    const precioInput = document.getElementById('precio');
    precioInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (isNaN(value) || value < 0.01 || value > 9999.99) {
            this.setCustomValidity('Ingrese un precio válido entre 0.01 y 9999.99.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación para stock
    const stockInput = document.getElementById('stock');
    stockInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        if (isNaN(value) || value < 0 || value > 99) {
            this.setCustomValidity('Ingrese un stock válido entre 0 y 99.');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>