import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BehaviorSubject, Observable, of, forkJoin } from 'rxjs';
import { map, switchMap, tap, catchError } from 'rxjs/operators';
import { Carrito } from '../models/carrito';
import { Videojuego } from '../models/videojuego';
import { PedidoItem } from '../models/pedido-item';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})
export class CarritoService {
  private carritoItems = new BehaviorSubject<Carrito[]>([]);
  private ruta_rest_services = "http://localhost/Angular/tiendaAngular/server/rest/";

  constructor(private http: HttpClient, private authService: AuthService) {
    this.cargarCarritoLocal();
    this.sincronizarCarrito();
  }

  getCarritoItems(): Observable<Carrito[]> {
    return this.authService.currentUser.pipe(
      switchMap(user => {
        if (user) {
          return this.http.get<Carrito[]>(`${this.ruta_rest_services}carrito_service.php?action=get&usuario_id=${user.id}`, {
            withCredentials: true
          }).pipe(
            tap(items => console.log('Respuesta de carrito_service.php:', items)), // Aquí se añade el log de la respuesta
            switchMap(items => {
              const videojuegoRequests = items.map(item =>
                this.http.get<Videojuego>(`${this.ruta_rest_services}videojuego_service.php?action=get&id=${item.videojuego_id}`, {
                  withCredentials: true
                }).pipe(
                  map(videojuego => {
                    const fullItem = new Carrito(item.usuario_id, item.videojuego_id, item.cantidad, videojuego);
                    fullItem.id = item.id;
                    return fullItem;
                  })
                )
              );
              return videojuegoRequests.length > 0 ? forkJoin(videojuegoRequests) : of([]);
            }),
            tap(fullItems => {
              this.carritoItems.next(fullItems);
              this.guardarCarritoLocal();
            }),
            catchError(error => {
              console.error('Error fetching cart items:', error);
              return of(this.carritoItems.value);
            })
          );
        } else {
          return of(this.carritoItems.value);
        }
      })
    );
  }

  agregarAlCarrito(videojuegoId: number, cantidad: number = 1): Observable<{ success: boolean; message: string }> {
    return this.authService.currentUser.pipe(
      switchMap(user => {
        if (!user) {
          return of({ success: false, message: 'Debe iniciar sesión para agregar items al carrito' });
        }

        return this.http.get<Videojuego>(`${this.ruta_rest_services}videojuego_service.php?action=get&id=${videojuegoId}`, {
          withCredentials: true
        }).pipe(
          switchMap(videojuego => {
            if (!videojuego) {
              return of({ success: false, message: 'Videojuego no encontrado' });
            }

            // Crear el nuevo item para el carrito
            const nuevoItem: PedidoItem = {
              id: videojuego.id,
              nombre: videojuego.nombre,
              precio: videojuego.precio,
              cantidad: cantidad,
              en_descuento: videojuego.en_descuento,
              descuento: videojuego.descuento
            };

            // Actualizar el carrito local
            const carritoActual = this.carritoItems.value;
            const itemExistente = carritoActual.find(item => item.videojuego_id === videojuegoId);

            if (itemExistente) {
              itemExistente.cantidad += cantidad;
            } else {
              carritoActual.push(new Carrito(0, videojuegoId, cantidad, videojuego));
            }

            this.carritoItems.next(carritoActual);
            this.guardarCarritoLocal();

            // Sincronizar con el servidor
            return this.http.post<{success: boolean; message: string}>(`${this.ruta_rest_services}carrito_service.php`, {
              action: 'add',
              usuario_id: user.id,
              videojuego_id: videojuegoId,
              cantidad: cantidad
            }, { withCredentials: true }).pipe(
              tap(() => this.sincronizarCarrito())
            );
          })
        );
      })
    );
  }

  actualizarCantidad(videojuegoId: number, cantidad: number): Observable<{ success: boolean; message: string }> {
    return this.authService.currentUser.pipe(
      switchMap(user => {
        if (user) {
          return this.http.post<{ success: boolean; message: string }>(`${this.ruta_rest_services}carrito_service.php`, {
            action: 'update',
            usuario_id: user.id,
            videojuego_id: videojuegoId,
            cantidad: cantidad
          }, { withCredentials: true }).pipe(
            tap(() => this.sincronizarCarrito())
          );
        } else {
          const items = this.carritoItems.value;
          const item = items.find(item => item.videojuego_id === videojuegoId);
          if (item) {
            item.cantidad = cantidad;
            this.carritoItems.next(items);
            this.guardarCarritoLocal();
          }
          return of({ success: true, message: 'Quantity updated locally' });
        }
      })
    );
  }

  eliminarDelCarrito(videojuegoId: number): Observable<{ success: boolean; message: string }> {
    return this.authService.currentUser.pipe(
      switchMap(user => {
        if (user) {
          return this.http.post<{ success: boolean; message: string }>(`${this.ruta_rest_services}carrito_service.php`, {
            action: 'remove',
            usuario_id: user.id,
            videojuego_id: videojuegoId
          }, { withCredentials: true }).pipe(
            tap(() => this.sincronizarCarrito())
          );
        } else {
          const items = this.carritoItems.value.filter(item => item.videojuego_id !== videojuegoId);
          this.carritoItems.next(items);
          this.guardarCarritoLocal();
          return of({ success: true, message: 'Item removed from cart locally' });
        }
      })
    );
  }

  obtenerCarritoDelServidor(): Observable<PedidoItem[]> {
    return this.authService.currentUser.pipe(
      switchMap(user => {
        if (!user) {
          return of([]);
        }
        return this.http.get<any[]>(`${this.ruta_rest_services}carrito_service.php?action=get&usuario_id=${user.id}`, {
          withCredentials: true
        }).pipe(
          map(response => response.map(item => ({
            id: item.videojuego_id,
            nombre: item.nombre,
            precio: parseFloat(item.precio),
            cantidad: parseInt(item.cantidad),
            en_descuento: item.en_descuento === '1',
            descuento: parseFloat(item.descuento)
          }))),
          catchError(() => of([]))
        );
      })
    );
  }

  obtenerCarrito(): PedidoItem[] {
    return this.carritoItems.value.map(item => ({
      id: item.videojuego_id,
      nombre: item.videojuego?.nombre || '',
      precio: item.videojuego?.precio || 0,
      cantidad: item.cantidad,
      en_descuento: item.videojuego?.en_descuento || false,
      descuento: item.videojuego?.descuento || 0
    }));
  }

  obtenerDescuento(): number {
    const carrito = this.obtenerCarrito();
    // Sumar todos los descuentos individuales
    return carrito.reduce((totalDescuento, item) => {
      // Si el juego está en descuento, calcular el descuento para la cantidad de items
      if (item.en_descuento) {
        const descuentoPorItem = (item.precio * item.descuento) / 100;
        return totalDescuento + (descuentoPorItem * item.cantidad);
      }
      return totalDescuento;
    }, 0);
  }

  limpiarCarrito(): void {
    this.carritoItems.next([]);
    this.guardarCarritoLocal();
    // Si el usuario está autenticado, también limpiamos en el servidor
    this.authService.currentUser.pipe(
      switchMap(user => {
        if (user) {
          return this.vaciarCarrito();
        }
        return of({ success: true, message: 'Cart cleared locally' });
      })
    ).subscribe();
  }

  vaciarCarrito(): Observable<{ success: boolean; message: string }> {
    return this.authService.currentUser.pipe(
      switchMap(user => {
        if (user) {
          return this.http.post<{ success: boolean; message: string }>(`${this.ruta_rest_services}carrito_service.php`, {
            action: 'clear',
            usuario_id: user.id
          }, { withCredentials: true }).pipe(
            tap(() => this.sincronizarCarrito())
          );
        } else {
          this.carritoItems.next([]);
          this.guardarCarritoLocal();
          return of({ success: true, message: 'Cart cleared locally' });
        }
      })
    );
  }

  // Método para obtener el observable del carrito en tiempo real
  getCarritoObservable(): Observable<Carrito[]> {
    return this.carritoItems.asObservable();
  }

  private sincronizarCarrito() {
    this.getCarritoItems().subscribe();
  }

  private cargarCarritoLocal() {
    const carritoGuardado = localStorage.getItem('carrito');
    if (carritoGuardado) {
      this.carritoItems.next(JSON.parse(carritoGuardado));
    }
  }

  private guardarCarritoLocal() {
    localStorage.setItem('carrito', JSON.stringify(this.carritoItems.value));
  }
}
