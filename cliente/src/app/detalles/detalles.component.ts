import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router, ParamMap } from '@angular/router';
import { TiendaService } from '../services/tienda.service';
import { Videojuego } from '../models/videojuego';
import { CommonModule } from '@angular/common';
import { switchMap, takeUntil } from 'rxjs/operators';
import { Subject } from 'rxjs';
import { FavoritoService } from '../services/favorito.service';
import { CarritoService } from '../services/carrito.service';

@Component({
  selector: 'app-detalles',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './detalles.component.html',
  styleUrls: ['./detalles.component.css']
})
export class DetallesComponent implements OnInit, OnDestroy {
  videojuego: Videojuego = {} as Videojuego;
  juegosRelacionados: Videojuego[] = [];
  private unsubscribe$ = new Subject<void>();

  constructor(
    private tiendaService: TiendaService,
    private carritoService: CarritoService, 
    private favoritosService: FavoritoService,
    private activatedRoute: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.activatedRoute.paramMap.pipe(
      takeUntil(this.unsubscribe$),
      switchMap((params: ParamMap) => {
        const id = Number(params.get('id'));
        return this.tiendaService.obtenerVideojuegoPorId(id);
      })
    ).subscribe(res => {
      this.videojuego = res;
      this.cargarJuegosRelacionados();
    });
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }

  cargarJuegosRelacionados(): void {
    this.tiendaService.obtenerProductosPorCategoria(this.videojuego.categoria).pipe(
      takeUntil(this.unsubscribe$)
    ).subscribe(res => {
      this.juegosRelacionados = res.filter(juego => juego.id !== this.videojuego.id).slice(0, 4);
    });
  }

  esFavorito(id: number): boolean {
    return this.favoritosService.esFavorito(id);
  }

  toggleFavorito(id: number): void {
    if (this.favoritosService.esFavorito(id)) {
      this.favoritosService.eliminarDeFavoritos(id);
    } else {
      this.favoritosService.agregarAFavoritos(this.videojuego);
    }
  }

  verMasJuegos(): void {
    this.router.navigate(['/listado']);
  }

  verDetalles(juego: Videojuego): void {
    this.router.navigate(['/detalles', juego.id]);
  }

  agregarAlCarrito(): void {
    if (this.videojuego && this.videojuego.id) {
      this.carritoService.agregarAlCarrito(this.videojuego.id, 1).subscribe(
        response => {
          if (response.success) {
            console.log('Producto agregado al carrito');
          } else {
            console.error('Error al agregar al carrito:', response.message);
          }
        },
        error => console.error('Error al agregar al carrito:', error)
      );
    }
  }
}
