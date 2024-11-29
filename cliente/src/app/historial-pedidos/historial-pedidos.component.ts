import { Component, OnInit } from '@angular/core';
import { PedidoService } from '../services/pedido.service';
import { AuthService } from '../services/auth.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

interface PedidoItem {
  id: number;
  producto_id: number;
  cantidad: number;
  precio: number;
  precio_final: number;
  descuento: number;
  subtotal: number;
  nombre: string;
  fecha: string;
}

interface Pedido {
  id: number;
  fecha: string;
  total: number;
  descuento: number;
  nombreCompleto: string;
  email: string;
  direccion: string;
  ciudad: string;
  provincia: string;
  codigoPostal: string;
  items: PedidoItem[];
}

@Component({
  selector: 'app-historial-pedidos',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './historial-pedidos.component.html',
  styleUrls: ['./historial-pedidos.component.css']
})
export class HistorialPedidosComponent implements OnInit {
  pedidos: Pedido[] = [];
  loading = true;
  error = '';

  constructor(
    private pedidoService: PedidoService,
    private authService: AuthService
  ) { }

  ngOnInit(): void {
    this.authService.currentUser.subscribe(user => {
      if (user) {
        this.cargarHistorialPedidos(user.id);
      }
    });
  }

  private cargarHistorialPedidos(usuarioId: number): void {
    this.loading = true;
    this.pedidoService.obtenerHistorialPedidos(usuarioId).subscribe({
      next: (response) => {
        console.log('Respuesta del servidor:', response);
        if (response.success) {
          this.pedidos = response.pedidos;
          console.log('Pedidos cargados:', this.pedidos);
        } else {
          this.error = response.message || 'Error al cargar los pedidos';
        }
        this.loading = false;
      },
      error: (error) => {
        console.error('Error completo:', error);
        this.error = 'Error al cargar el historial de pedidos';
        this.loading = false;
      }
    });
  }
}