import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Videojuego } from '../models/videojuego';

@Injectable({
  providedIn: 'root'
})
export class FavoritoService {

  private ruta_rest_services = "http://localhost/Angular/tiendaAngular/server/rest/";

  private favoritos: Videojuego[] = [];

  constructor(private http: HttpClient) {}

  agregarAFavoritos(videojuego: Videojuego): void {
    if (!this.favoritos.find(juego => juego.id === videojuego.id)) {
      this.favoritos.push(videojuego);
    }
  }

  eliminarDeFavoritos(id: number): void {
    this.favoritos = this.favoritos.filter(juego => juego.id !== id);
  }

  esFavorito(id: number): boolean {
    return !!this.favoritos.find(juego => juego.id === id);
  }

  obtenerFavoritos(): Videojuego[] {
    return this.favoritos;
  }

}
