import { Component, OnInit } from '@angular/core';
import { Pedido } from '../models/pedido';
import { FormBuilder, FormGroup, Validators, AbstractControl, ValidationErrors, ReactiveFormsModule, FormsModule } from '@angular/forms';
import { TiendaService } from '../services/tienda.service';
import { Router } from '@angular/router';
import { CarritoService } from '../services/carrito.service';
import { PedidoItem } from '../models/pedido-item';
import { AuthService } from '../services/auth.service';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-pedido',
  standalone: true,
  imports: [FormsModule, ReactiveFormsModule, NgIf],
  templateUrl: './pedido.component.html',
  styleUrls: ['./pedido.component.css']
})
export class PedidoComponent implements OnInit {
  pedidoForm!: FormGroup;
  pedido: Pedido = new Pedido();
  subtotal: number = 0;
  descuento: number = 0;
  iva: number = 0;
  total: number = 0;
  readonly IVA_RATE: number = 0.21; // 21% IVA

  constructor(
    private fb: FormBuilder,
    private tiendaService: TiendaService,
    private carritoService: CarritoService,
    private authService: AuthService,
    private router: Router,
  ) {
    this.createForm();
  }

  // Validador personalizado para espacios en blanco
  noExcessiveWhitespace(control: AbstractControl): ValidationErrors | null {
    if (control.value && typeof control.value === 'string') {
      if (/\s{4,}/.test(control.value)) {
        return { excessiveWhitespace: true };
      }
    }
    return null;
  }

  // Validador para número de tarjeta
  validateCreditCard(control: AbstractControl): ValidationErrors | null {
    if (!control.value) return null;
    
    const value = control.value.replace(/\s/g, '');
    if (!/^\d{16}$/.test(value)) {
      return { invalidCreditCard: true };
    }
    
    // Algoritmo de Luhn
    let sum = 0;
    let isEven = false;
    
    for (let i = value.length - 1; i >= 0; i--) {
      let digit = parseInt(value[i]);
      
      if (isEven) {
        digit *= 2;
        if (digit > 9) {
          digit -= 9;
        }
      }
      
      sum += digit;
      isEven = !isEven;
    }
    
    return (sum % 10 === 0) ? null : { invalidCreditCard: true };
  }

  // Validador para fecha de vencimiento
  validateExpiryDate(control: AbstractControl): ValidationErrors | null {
    if (!control.value) return null;

    const value = control.value.trim();
    if (!/^\d{2}\/\d{2}$/.test(value)) {
      return { invalidFormat: true };
    }

    const [month, year] = value.split('/');
    const expiry = new Date(2000 + parseInt(year), parseInt(month) - 1);
    const today = new Date();
    
    if (expiry < today) {
      return { expired: true };
    }

    if (parseInt(month) < 1 || parseInt(month) > 12) {
      return { invalidMonth: true };
    }

    return null;
  }

  // Validador para CVV
  validateCVV(control: AbstractControl): ValidationErrors | null {
    if (!control.value) return null;
    return /^\d{3,4}$/.test(control.value) ? null : { invalidCVV: true };
  }

  createForm() {
    this.pedidoForm = this.fb.group({
      nombreCompleto: ['', [Validators.required, this.noExcessiveWhitespace]],
      email: ['', [Validators.required, Validators.email]],
      direccion: ['', [Validators.required, this.noExcessiveWhitespace]],
      ciudad: ['', [Validators.required, this.noExcessiveWhitespace]],
      provincia: ['', [Validators.required, this.noExcessiveWhitespace]],
      codigoPostal: ['', [Validators.required, Validators.pattern(/^\d{5}$/)]],
      titularTarjeta: ['', [Validators.required, this.noExcessiveWhitespace]],
      numeroTarjeta: ['', [Validators.required, this.validateCreditCard]],
      fechaVencimiento: ['', [Validators.required, this.validateExpiryDate]],
      cvv: ['', [Validators.required, this.validateCVV]]
    });
  }

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
    if (this.pedidoForm.invalid) {
      Object.keys(this.pedidoForm.controls).forEach(key => {
        const control = this.pedidoForm.get(key);
        if (control?.invalid) {
          control.markAsTouched();
        }
      });
      return;
    }

    // Verificar si hay usuario logueado
    if (!this.pedido.usuario_id) {
      alert('Debe iniciar sesión para realizar un pedido');
      this.router.navigate(['/login']);
      return;
    }

    // Actualizar el objeto pedido con los valores del formulario
    const formValues = this.pedidoForm.value;
    Object.assign(this.pedido, formValues);

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
        console.error('Error:', error);
        alert('Error al procesar el pedido. Por favor, inténtelo de nuevo.');
      }
    });
  }
}
