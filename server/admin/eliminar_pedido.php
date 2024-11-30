<?php
require "../librerias_php/setup_red_bean.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'])) {
    $pedido_id = $_POST['pedido_id'];
    
    try {
        // Iniciar transacción
        R::begin();
        
        // Eliminar los detalles del pedido primero
        $detalles = R::find('detallepedido', 'pedido_id = ?', [$pedido_id]);
        foreach ($detalles as $detalle) {
            R::trash($detalle);
        }
        
        // Eliminar el pedido
        $pedido = R::load('pedido', $pedido_id);
        if ($pedido->id) {
            R::trash($pedido);
            
            // Confirmar transacción
            R::commit();
            
            // Redirigir con mensaje de éxito
            header('Location: gestionar_pedidos.php?mensaje=Pedido eliminado correctamente');
            exit;
        } else {
            throw new Exception('Pedido no encontrado');
        }
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        R::rollback();
        
        // Redirigir con mensaje de error
        header('Location: gestionar_pedidos.php?error=Error al eliminar el pedido: ' . $e->getMessage());
        exit;
    }
} else {
    // Redirigir si no se proporcionó un ID de pedido
    header('Location: gestionar_pedidos.php?error=ID de pedido no proporcionado');
    exit;
}
?>