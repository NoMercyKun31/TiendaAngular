import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PedidoService {
  private apiUrl = 'http://localhost/Angular/TiendaAngular/server/rest/';

  constructor(private http: HttpClient) { }

  obtenerHistorialPedidos(usuarioId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}obtener_historial_pedidos.php?usuario_id=${usuarioId}`, {
      withCredentials: true
    });
  }

  // Método para crear un nuevo pedido
  crearPedido(pedido: any): Observable<any> {
    return this.http.post(`${this.apiUrl}crear_pedido.php`, pedido, {
      withCredentials: true
    });
  }

  // Método para obtener un pedido específico
  obtenerPedido(pedidoId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}obtener_pedido.php?id=${pedidoId}`, {
      withCredentials: true
    });
  }
}