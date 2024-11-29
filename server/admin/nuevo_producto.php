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
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
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
                        </div>
                        <div class="mb-3">
                            <label for="compania" class="form-label">Compañía</label>
                            <input type="text" class="form-control" id="compania" name="compania" required>
                        </div>
                        <div class="mb-3">
                            <label for="anyolanzamiento" class="form-label">Año de Lanzamiento</label>
                            <input type="number" class="form-control" id="anyolanzamiento" name="anyolanzamiento" required>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" max="99" required>
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

<?php
$content = ob_get_clean();
include 'layout.php';
?>

