import { Component, OnInit } from '@angular/core';

import { Videojuego } from '../models/videojuego';
import { FavoritoService } from '../services/favorito.service';
import { CommonModule } from '@angular/common';
import { CarritoService } from '../services/carrito.service';

@Component({
  selector: 'app-favoritos',
  standalone: true,
  imports: [CommonModule], 
  templateUrl: './favoritos.component.html',
  styleUrls: ['./favoritos.component.css']
})
export class FavoritosComponent implements OnInit {
  favoritos: Videojuego[] = [];
  videojuego: Videojuego = {} as Videojuego;

  constructor(
    private favoritosService: FavoritoService,
    private carritoService: CarritoService, 
  ) {}

  ngOnInit(): void {
    this.cargarFavoritos();
  }

  cargarFavoritos(): void {
    this.favoritos = this.favoritosService.obtenerFavoritos();
  }

  agregarAlCarrito(videojuego: Videojuego): void {
    if (videojuego && videojuego.id) {
      this.carritoService.agregarAlCarrito(videojuego.id, 1).subscribe(
        response => {
          if (response.success) {
            console.log(`Producto ${videojuego.nombre} agregado al carrito.`);
          } else {
            console.error('Error al agregar al carrito:', response.message);
          }
        },
        error => console.error('Error al agregar al carrito:', error)
      );
    } else {
      console.error('El videojuego no tiene un ID válido.');
    }
  }
  

  eliminarDeFavoritos(id: number): void {
    this.favoritosService.eliminarDeFavoritos(id);
    this.cargarFavoritos();
  }
}
