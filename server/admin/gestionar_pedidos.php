<?php
$pageTitle = "Gestión de Pedidos";
ob_start();

require "../librerias_php/setup_red_bean.php";

// Obtener todos los pedidos ordenados por fecha descendente
$pedidos = R::findAll('pedido', ' ORDER BY fecha ASC');
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">
        <i class="fas fa-shopping-cart me-2"></i>
        Gestión de Pedidos
    </h1>

    <?php if (count($pedidos) > 0): ?>
        <?php foreach ($pedidos as $pedido): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Pedido #<?php echo $pedido->id; ?>
                    </h5>
                    <span class="badge bg-light text-primary">
                        <?php echo date('d/m/Y H:i', strtotime($pedido->fecha)); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    Información del Cliente
                                </h6>
                                <p class="mb-2">
                                    <strong><i class="fas fa-user-circle me-2"></i>Nombre:</strong> 
                                    <?php echo htmlspecialchars($pedido->nombreCompleto); ?>
                                </p>
                                <p class="mb-2">
                                    <strong><i class="fas fa-envelope me-2"></i>Email:</strong> 
                                    <?php echo htmlspecialchars($pedido->email); ?>
                                </p>
                                <p class="mb-2">
                                    <strong><i class="fas fa-map-marker-alt me-2"></i>Dirección:</strong> 
                                    <?php echo htmlspecialchars($pedido->direccion); ?>
                                </p>
                                <p class="mb-2">
                                    <strong><i class="fas fa-city me-2"></i>Ciudad:</strong> 
                                    <?php echo htmlspecialchars($pedido->ciudad); ?>
                                </p>
                                <p class="mb-2">
                                    <strong><i class="fas fa-map me-2"></i>Provincia:</strong> 
                                    <?php echo htmlspecialchars($pedido->provincia); ?>
                                </p>
                                <p class="mb-0">
                                    <strong><i class="fas fa-mail-bulk me-2"></i>Código Postal:</strong> 
                                    <?php echo htmlspecialchars($pedido->codigoPostal); ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Detalles del Pedido
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 mb-0">Total del Pedido:</span>
                                    <span class="h4 mb-0 text-primary">$<?php echo number_format($pedido->total, 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-box-open me-2"></i>
                            Productos del Pedido
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio unitario</th>
                                        <th class="text-center">Descuento</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $detalles_pedido = R::find('pedidovideojuego', 'pedido_id = ?', [$pedido->id]);
                                    foreach ($detalles_pedido as $detalle):
                                    ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-gamepad me-2 text-primary"></i>
                                                <?php echo htmlspecialchars($detalle->nombre); ?>
                                            </td>
                                            <td class="text-center"><?php echo $detalle->cantidad; ?></td>
                                            <td class="text-end">$<?php echo number_format($detalle->precio, 2); ?></td>
                                            <td class="text-center">
                                                <?php if ($detalle->descuento > 0): ?>
                                                    <span class="badge bg-success">
                                                        <?php echo $detalle->descuento; ?>%
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">$<?php echo number_format($detalle->subtotal, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end">
                                            <strong>Total del Pedido:</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-primary">$<?php echo number_format($pedido->total, 2); ?></strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info shadow-sm" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            No hay pedidos registrados en este momento.
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>