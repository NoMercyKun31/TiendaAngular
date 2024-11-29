<?php
$pageTitle = "Gestión de Videojuegos";
ob_start();

require "../librerias_php/setup_red_bean.php";

// Procesar la aplicación de descuentos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aplicar descuento individual
    if (isset($_POST['aplicar_descuento'])) {
        $id = $_POST['videojuego_id'];
        $descuento = $_POST['descuento'];
        $videojuego = R::load('videojuego', $id);
        if ($videojuego->id) {
            $videojuego->descuento = $descuento;
            R::store($videojuego);
            $mensaje = "Descuento aplicado correctamente.";
            $tipo = "success";
        }
    }

    // Aplicar descuento en masa
    if (isset($_POST['aplicar_descuentos_masivos'])) {
        $descuento_seleccionado = $_POST['descuento_masivo'];
        $juegos_seleccionados = $_POST['juegos_seleccionados'] ?? [];

        if (!empty($juegos_seleccionados)) {
            $juegos_actualizados = 0;
            foreach ($juegos_seleccionados as $id) {
                $videojuego = R::load('videojuego', $id);
                if ($videojuego->id) {
                    $videojuego->descuento = $descuento_seleccionado;
                    $videojuego->en_descuento = true;
                    R::store($videojuego);
                    $juegos_actualizados++;
                }
            }
            $mensaje = "$juegos_actualizados juegos actualizados con descuento de {$descuento_seleccionado}%.";
            $tipo = "success";
        }
    }
}

?>

<div class="container-fluid py-4">
    <?php
    if (isset($mensaje) && isset($tipo)) {
        echo "<div class='alert alert-$tipo alert-dismissible fade show' role='alert'>
                $mensaje
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
    ?>
    <h1 class="mb-4">Gestión de Videojuegos</h1>

    <div class="mb-4 d-flex justify-content-between">
        <a href="nuevo_producto.php" class="btn btn-primary me-2">
            <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Videojuego
        </a>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#descuentosMasivosModal">
            <i class="fas fa-tags me-2"></i>Aplicar Descuentos Masivos
        </button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#eliminacionMasivaModal">
            <i class="fas fa-trash-alt me-2"></i>Eliminación Multiple
        </button>
    </div>

    <?php
    $videojuegos = R::findAll('videojuego');

    if (count($videojuegos) > 0):
    ?>
        <!-- Modal de descuentos masivos -->
        <div class="modal fade" id="descuentosMasivosModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Aplicar Descuentos Masivos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Descuento</label>
                                <select name="descuento_masivo" class="form-select" required>
                                    <option value="">Seleccionar Porcentaje</option>
                                    <option value="5">5%</option>
                                    <option value="10">10%</option>
                                    <option value="15">15%</option>
                                    <option value="20">20%</option>
                                    <option value="25">25%</option>
                                    <option value="50">50%</option>
                                    <option value="75">75%</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Videojuegos</label>
                                <?php foreach ($videojuegos as $videojuego): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="juegos_seleccionados[]"
                                            value="<?php echo $videojuego->id; ?>"
                                            id="juego<?php echo $videojuego->id; ?>">
                                        <label class="form-check-label" for="juego<?php echo $videojuego->id; ?>">
                                            <?php echo htmlspecialchars($videojuego->nombre); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="aplicar_descuentos_masivos" class="btn btn-primary">Aplicar Descuentos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($videojuegos as $videojuego): ?>
                <div class="col">
                    <div class="card h-100 <?php echo $videojuego->en_descuento ? 'border-success' : ''; ?>">
                        <img src='../imagenes/<?php echo $videojuego->id; ?>.jpg' class="card-img-top" style="height: 250px; object-fit: contain; width: auto; max-width: 100%; margin: 0 auto;" alt='Foto de <?php echo htmlspecialchars($videojuego->nombre); ?>'>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($videojuego->nombre); ?></h5>
                            <p class="card-text">
                                <strong>Categoría:</strong> <?php echo htmlspecialchars($videojuego->categoria); ?><br>
                                <strong>Compañía:</strong> <?php echo htmlspecialchars($videojuego->compania); ?><br>
                                <strong>Año de lanzamiento:</strong> <?php echo htmlspecialchars($videojuego->anyolanzamiento); ?><br>
                                <strong>Precio original:</strong> $<?php echo number_format($videojuego->precio, 2); ?><br>
                                <?php if ($videojuego->descuento > 0): ?>
                                    <strong>Descuento:</strong> <?php echo $videojuego->descuento; ?>%<br>
                                    <strong>Precio con descuento:</strong> $<?php echo number_format($videojuego->precio * (1 - $videojuego->descuento / 100), 2); ?><br>
                                <?php endif; ?>
                                <strong>Stock:</strong> <?php echo $videojuego->stock; ?><br>
                                <strong>Estado:</strong>
                                <?php
                                if ($videojuego->stock > 10) {
                                    echo '<span class="text-success">Disponible</span>';
                                } elseif ($videojuego->stock > 0) {
                                    echo '<span class="text-warning">Mínimo</span>';
                                } else {
                                    echo '<span class="text-danger">No disponible</span>';
                                }
                                ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between mb-2">
                                <a href="editar_producto.php?id=<?php echo $videojuego->id; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $videojuego->id; ?>">
                                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="modal fade" id="eliminacionMasivaModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminación Masiva de Videojuegos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Videojuegos para Eliminar</label>
                                <?php foreach ($videojuegos as $videojuego): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="juegos_eliminar[]"
                                            value="<?php echo $videojuego->id; ?>"
                                            id="eliminar<?php echo $videojuego->id; ?>">
                                        <label class="form-check-label" for="eliminar<?php echo $videojuego->id; ?>">
                                            <?php echo htmlspecialchars($videojuego->nombre); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="eliminar_masivo" class="btn btn-danger">Eliminar Seleccionados</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php
// ... (código anterior sin cambios)

// Procesar la eliminación masiva
if (isset($_POST['eliminar_masivo'])) {
    $juegos_eliminar = $_POST['juegos_eliminar'] ?? [];
    if (!empty($juegos_eliminar)) {
        $juegos_eliminados = 0;
        foreach ($juegos_eliminar as $id) {
            $videojuego = R::load('videojuego', $id);
            if ($videojuego->id) {
                // Eliminar la imagen asociada
                $imagen_path = "../imagenes/{$id}.jpg";
                if (file_exists($imagen_path)) {
                    unlink($imagen_path);
                }
                
                // Eliminar el videojuego de la base de datos
                R::trash($videojuego);
                $juegos_eliminados++;
            }
        }
        $mensaje = "$juegos_eliminados juegos y sus imágenes asociadas han sido eliminados.";
        $tipo = "success";
    } else {
        $mensaje = "No se seleccionaron juegos para eliminar.";
        $tipo = "warning";
    }
}
        ?>
        <!-- Modal de confirmación para eliminar -->
        <div class="modal fade" id="deleteModal<?php echo $videojuego->id; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar el videojuego "<?php echo htmlspecialchars($videojuego->nombre); ?>"?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a href="eliminar_producto.php?id=<?php echo $videojuego->id; ?>" class="btn btn-danger">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

</div>
<?php else: ?>
    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        No hay videojuegos registrados en este momento. ¡Comienza añadiendo uno nuevo!
    </div>
<?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>