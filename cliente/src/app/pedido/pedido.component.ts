import { Component, OnInit } from '@angular/core';
import { Pedido } from '../models/pedido';
import { FormsModule } from '@angular/forms';
import { TiendaService } from '../services/tienda.service';
import { Router } from '@angular/router';
import { CarritoService } from '../services/carrito.service';
import { PedidoItem } from '../models/pedido-item';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-pedido',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './pedido.component.html',
  styleUrls: ['./pedido.component.css']
})
export class PedidoComponent implements OnInit {
  pedido: Pedido = new Pedido();
  subtotal: number = 0;
  descuento: number = 0;
  iva: number = 0;
  total: number = 0;
  readonly IVA_RATE: number = 0.21; // 21% IVA

  constructor(
    private tiendaService: TiendaService,
    private carritoService: CarritoService,
    private authService: AuthService,
    private router: Router,
  ) {}

  ngOnInit() {
    // Obtener el ID del usuario actual
    this.authService.currentUser.subscribe(user => {
      if (user) {
        this.pedido.usuario_id = user.id;
        this.calcularTotales();
      } else {
        this.router.navigate(['/login']);
      }
    });
  }

  calcularTotales() {
    // Obtener los items y el descuento del carrito
    const items = this.carritoService.obtenerCarrito();
    this.descuento = this.carritoService.obtenerDescuento();
    
    // Calcular subtotal
    this.subtotal = items.reduce((total, item) => {
      return total + (item.precio * item.cantidad);
    }, 0);

    // Calcular base después de descuento
    const baseConDescuento = this.subtotal - this.descuento;

    // Calcular IVA
    this.iva = baseConDescuento * this.IVA_RATE;

    // Calcular total final
    this.total = baseConDescuento + this.iva;

    // Guardar el total en el pedido
    this.pedido.total = this.total;
  }

  finalizarPedido() {
    // Verificar si hay usuario logueado
    if (!this.pedido.usuario_id) {
      alert('Debe iniciar sesión para realizar un pedido');
      this.router.navigate(['/login']);
      return;
    }

    // Limpiar espacios en blanco de los campos críticos
    this.pedido.numeroTarjeta = this.pedido.numeroTarjeta?.replace(/\s/g, '') || '';
    this.pedido.cvv = this.pedido.cvv?.replace(/\s/g, '') || '';
    this.pedido.fechaVencimiento = this.pedido.fechaVencimiento?.replace(/\s/g, '') || '';
    this.pedido.codigoPostal = this.pedido.codigoPostal?.replace(/\s/g, '') || '';

    // Obtener los items del carrito
    this.pedido.items = this.carritoService.obtenerCarrito();
    
    // Recalcular totales antes de enviar
    this.calcularTotales();

    console.log('Enviando pedido:', this.pedido);
    
    this.tiendaService.registrarPedido(this.pedido).subscribe({
      next: (response) => {
        console.log('Respuesta del servidor:', response);
        if (response.status === 'ok') {
          // Limpiar el carrito después de un pedido exitoso
          this.carritoService.limpiarCarrito();
          alert('Pedido registrado correctamente. Total: €' + this.total.toFixed(2));
          this.router.navigate(['/listado']);
        } else {
          alert('Error al registrar el pedido: ' + response.message);
        }
      },
      error: (error) => {
        console.error('Error completo:', error);
        alert('Error al registrar el pedido: ' + error.message);
      }
    });
  }
}
