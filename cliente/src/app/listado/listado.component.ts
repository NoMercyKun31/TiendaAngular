import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TiendaService } from '../services/tienda.service';
import { Videojuego } from '../models/videojuego';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-listado',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './listado.component.html',
  styleUrls: ['./listado.component.css'],
})
export class ListadoComponent implements OnInit {
  videojuegos: Videojuego[] = [];
  categories: string[] = [];
  selectedCategory: string = '';
  currentPage: number = 1;
  itemsPerPage: number = 8;
  totalItems: number = 0;
  searchTerm: string = '';

  constructor(private tiendaService: TiendaService, private router: Router, private route: ActivatedRoute) {}

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      const searchTerm = params['search'];
      console.log('Received search term:', searchTerm);
      if (searchTerm) {
        this.searchVideojuegos(searchTerm);
      } else {
        this.loadVideojuegos();
      }
    });
  }

  loadVideojuegos(): void {
    this.tiendaService.obtenerProductos().subscribe(
      (data) => {
        console.log('Loaded videojuegos:', data);
        this.videojuegos = data;
        this.totalItems = data.length;
        this.categories = [...new Set(data.map((v) => v.categoria))];
      },
      (error) => console.error('Error loading videojuegos:', error)
    );
  }

  searchVideojuegos(term: string): void {
    console.log('Searching videojuegos with term:', term);
    this.tiendaService.buscarVideojuegos(term).subscribe(
      (data) => {
        console.log('Search results:', data);
        this.videojuegos = data;
        this.totalItems = data.length;
        this.currentPage = 1;
        if (data.length === 0) {
          console.log('No results found');
        }
      },
      (error) => {
        console.error('Error searching videojuegos:', error);
      }
    );
  }

  onCategorySelect(category: string): void {
    this.selectedCategory = category;
    this.currentPage = 1;
    if (category) {
      this.tiendaService.obtenerProductosPorCategoria(category).subscribe(
        (data) => {
          console.log('Category results:', data);
          this.videojuegos = data;
          this.totalItems = data.length;
        },
        (error) => console.error('Error loading category:', error)
      );
    } else {
      this.loadVideojuegos();
    }
  }

  get pagedVideojuegos(): Videojuego[] {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    return this.videojuegos.slice(startIndex, startIndex + this.itemsPerPage);
  }

  get totalPages(): number {
    return Math.ceil(this.totalItems / this.itemsPerPage);
  }

  changePage(page: number | string): void {
    if (typeof page === 'number' && page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }

  getPageNumbers(): (number | string)[] {
    const pageNumbers: (number | string)[] = [];
    if (this.totalPages <= 5) {
      for (let i = 1; i <= this.totalPages; i++) {
        pageNumbers.push(i);
      }
    } else {
      pageNumbers.push(1);
      if (this.currentPage > 3) {
        pageNumbers.push('...');
      }
      for (let i = Math.max(2, this.currentPage - 1); i <= Math.min(this.totalPages - 1, this.currentPage + 1); i++) {
        pageNumbers.push(i);
      }
      if (this.currentPage < this.totalPages - 2) {
        pageNumbers.push('...');
      }
      pageNumbers.push(this.totalPages);
    }
    return pageNumbers;
  }

  getStockText(stock: number): string {
    if (stock > 10) return 'En Stock';
    if (stock > 0) return 'Pocas Unidades';
    return 'Agotado';
  }

  getStockClass(stock: number): string {
    if (stock > 10) return 'text-success';
    if (stock > 0) return 'text-warning';
    return 'text-danger';
  }

  verDetalles(videojuego: Videojuego) {
    this.router.navigate(['/detalles', videojuego.id]);
  }

  calculateDiscountedPrice(videojuego: Videojuego): number {
    return videojuego.precio * (1 - videojuego.descuento / 100);
  }

  getCategoryIcon(category: string): string {
    const icons: { [key: string]: string } = {
      'Acción': 'fas fa-fist-raised',
      'RPG': 'fas fa-hat-wizard',
      'Carreras': 'fas fa-car',
      'Shooter' : 'fas fa-crosshairs',
      'Plataformas' : 'fas fa-dice',
      'Lucha' : 'fas fa-fist-raised',
      'Arcade': 'fas fa-gamepad',
      'Estrategia': 'fas fa-chess',
      'Aventura': 'fas fa-compass',
      'Simulación': 'fas fa-sim-card',
      'Puzzle': 'fas fa-puzzle-piece',
    };
    return icons[category] || 'fas fa-gamepad'; // Icono por defecto
  }

  getCategoryCount(category: string): number {
    return this.videojuegos.filter(v => v.categoria === category).length;
  }
}

