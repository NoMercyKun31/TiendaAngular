import { Component, OnInit, OnDestroy } from '@angular/core';
import { Carrito } from '../models/carrito';
import { CommonModule } from '@angular/common';
import { CarritoService } from '../services/carrito.service';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-carrito',
  templateUrl: './carrito.component.html',
  styleUrls: ['./carrito.component.css'],
  standalone: true,
  imports: [CommonModule]
})
export class CarritoComponent implements OnInit, OnDestroy {
  carritoItems: Carrito[] = [];
  private subscription: Subscription = new Subscription();

  constructor(
    private carritoService: CarritoService,
    private authService: AuthService,
    private router: Router
  ) { }

  ngOnInit(): void {
    this.verificarSesionYCargarCarrito();
  }

  ngOnDestroy(): void {
    this.subscription.unsubscribe();
  }

  verificarSesionYCargarCarrito(): void {
    this.subscription.add(
      this.authService.currentUser.subscribe(user => {
        if (user) {
          this.cargarCarrito();
        } else {
          this.router.navigate(['/login']);
        }
      })
    );
  }

  cargarCarrito(): void {
    this.subscription.add(
      this.carritoService.getCarritoItems().subscribe(
        items => {
          this.carritoItems = items;
        },
        error => console.error('Error al cargar los items del carrito:', error)
      )
    );
  }

  actualizarCantidad(item: Carrito, cambio: number): void {
    const cantidadActual = Number(item.cantidad) || 0;
    const nuevaCantidad = cantidadActual + cambio;
  
    if (nuevaCantidad > 0) {
      item.cantidad = nuevaCantidad;
  
      this.subscription.add(
        this.carritoService.actualizarCantidad(item.videojuego_id, nuevaCantidad).subscribe(
          response => {
            if (!response.success) {
              item.cantidad = cantidadActual;
            }
          },
          error => {
            item.cantidad = cantidadActual;
          }
        )
      );
    }
  }

  eliminarItem(videojuegoId: number): void {
    this.subscription.add(
      this.carritoService.eliminarDelCarrito(videojuegoId).subscribe(
        response => {
          if (response.success) {
            this.carritoItems = this.carritoItems.filter(item => item.videojuego_id !== videojuegoId);
          }
        }
      )
    );
  }

  vaciarCarrito(): void {
    this.subscription.add(
      this.carritoService.vaciarCarrito().subscribe(
        response => {
          if (response.success) {
            this.carritoItems = [];
          }
        }
      )
    );
  }

  calcularSubtotal(): number {
    return this.carritoItems.reduce((total, item) => {
      if (item.videojuego) {
        return total + (item.videojuego.precio * item.cantidad);
      }
      return total;
    }, 0);
  }

  calcularDescuentoTotal(): number {
    return this.carritoItems.reduce((total, item) => {
      if (item.videojuego?.en_descuento && item.videojuego?.descuento > 0) {
        const descuento = (item.videojuego.precio * item.videojuego.descuento / 100) * item.cantidad;
        return total + descuento;
      }
      return total;
    }, 0);
  }

  calcularIVA(): number {
    const subtotalConDescuento = this.calcularSubtotal() - this.calcularDescuentoTotal();
    return subtotalConDescuento * 0.21;
  }

  calcularTotal(): number {
    const subtotal = this.calcularSubtotal();
    const descuentoTotal = this.calcularDescuentoTotal();
    const iva = this.calcularIVA();
    return subtotal - descuentoTotal + iva;
  }

  procesarCompra() {
    if (this.carritoItems.length === 0) {
      alert("Agregar por los menos un producto en el carrito para realizar un pedido.")
      return
    } else {
      this.router.navigate(["/pedido"]);
    }
  }


}