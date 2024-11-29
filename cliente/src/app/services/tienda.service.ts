import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Videojuego } from "../models/videojuego";
import { Observable, map, catchError, throwError } from "rxjs";
import { Pedido } from "../models/pedido";

@Injectable({providedIn: 'root'})
export class TiendaService {
    ruta_rest_services = "http://localhost/Angular/tiendaAngular/server/";
    
    constructor(private http: HttpClient) {}
    
    obtenerProductos(): Observable<Videojuego[]> {
        return this.http.get<Videojuego[]>(this.ruta_rest_services + "rest/obtener_videojuegos.php")
            .pipe(
                catchError(this.handleError<Videojuego[]>('obtenerProductos', []))
            );
    }

    obtenerProductosPorCategoria(categoria: string): Observable<Videojuego[]> {
        return this.http.get<Videojuego[]>(this.ruta_rest_services + "rest/obtener_videojuegos_por_categoria.php", {
            params: { categoria }
        }).pipe(
            catchError(this.handleError<Videojuego[]>('obtenerProductosPorCategoria', []))
        );
    }

    buscarVideojuegos(termino: string): Observable<Videojuego[]> {
        console.log('Service: Searching for:', termino);
        return this.http.get<{data: Videojuego[]}>(this.ruta_rest_services + "rest/buscar_videojuegos.php", {
          params: { termino }
        }).pipe(
          map(response => {
            console.log('Service: Raw server response:', response);
            return response.data;
          }),
          catchError(error => {
            console.error('Service: Error in buscarVideojuegos:', error);
            return throwError(() => new Error('Error en la búsqueda de videojuegos'));
          })
        );
    }

    obtenerVideojuegoPorId(id: number): Observable<Videojuego> {
        return this.http.get<Videojuego>(this.ruta_rest_services + "rest/obtener_videojuego_por_id.php", {
            params: { id: id.toString() }
        }).pipe(
            catchError(this.handleError<Videojuego>('obtenerVideojuegoPorId'))
        );
    }

    registrarPedido(pedido: Pedido): Observable<any> {
        const headers = new HttpHeaders({
            'Content-Type': 'application/json'
        });

        return this.http.post(
            this.ruta_rest_services + "rest/registrar_pedido.php", 
            pedido, 
            { 
                headers: headers,
                withCredentials: false 
            }
        ).pipe(
            catchError(error => {
                console.error('Error en registrarPedido:', error);
                if (error.error instanceof ErrorEvent) {
                    // Error del cliente
                    return throwError(() => new Error('Error de conexión'));
                } else {
                    // Error del servidor
                    const message = error.error?.message || 'Error del servidor';
                    return throwError(() => new Error(message));
                }
            })
        );
    }

    private handleError<T>(operation = 'operation', result?: T) {
        return (error: any): Observable<T> => {
            console.error(error);
            console.log(`${operation} failed: ${error.message}`);
            return throwError(() => error);
        };
    }
}
