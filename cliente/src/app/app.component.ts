// app.component.ts
import { Component, OnInit, OnDestroy } from '@angular/core';
import { NavigationEnd, NavigationStart, NavigationCancel, NavigationError, Router, RouterLink, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from './services/auth.service';
import { TiendaService } from './services/tienda.service';
import { CarritoService } from './services/carrito.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, RouterLink, CommonModule, FormsModule],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit, OnDestroy {
  title = 'cliente';
  isAuthenticated: boolean = false;
  mostrarHeader: boolean = true;
  showDropdown = false;
  searchTerm: string = '';
  totalItems: number = 0;
  isLoading = false;
  private cartSubscription: Subscription;

  constructor(
    private router: Router, 
    private authService: AuthService, 
    private tiendaService: TiendaService,
    private carritoService: CarritoService
  ) {
    // Detecta el cambio de rutas
    this.router.events.subscribe(event => {
      if (event instanceof NavigationStart) {
        this.isLoading = true;
      } else if (event instanceof NavigationEnd) {
        this.mostrarHeader = !event.url.includes('/detalles') 
        && !event.url.includes('/register') 
        && !event.url.includes('/login') 
        && !event.url.includes('/profile') 
        && !event.url.includes('/carrito')
        && !event.url.includes("/pedido")
        && !event.url.includes('/historial-pedidos')
        && !event.url.includes('/favoritos');
        setTimeout(() => {
          this.isLoading = false;
        }, 1000);
        scrollTo(0, 0);
      } else if (
        event instanceof NavigationCancel ||
        event instanceof NavigationError
      ) {
        setTimeout(() => {
          this.isLoading = false;
        }, 2500);
      }
    });

    // Suscríbete a los cambios en la autenticación
    this.authService.currentUser.subscribe(user => {
      this.isAuthenticated = !!user;
    });

    // Suscribirse a los cambios del carrito en tiempo real
    this.cartSubscription = this.carritoService.getCarritoObservable().subscribe(items => {
      this.totalItems = items.length;
    });
  }

  ngOnInit(): void {
    // Cargar items iniciales del carrito
    this.carritoService.getCarritoItems().subscribe();
  }

  ngOnDestroy(): void {
    if (this.cartSubscription) {
      this.cartSubscription.unsubscribe();
    }
  }

  toggleDropdown() {
    this.showDropdown = !this.showDropdown;
  }

  closeDropdown() {
    this.showDropdown = false;
  }

  logout(): void {
    this.authService.logout().subscribe(() => {
      this.isAuthenticated = false;
    });
  }

  onSearch(): void {
    if (this.searchTerm.trim()) {
      console.log('Searching for:', this.searchTerm.trim());
      this.router.navigate(['/listado'], { queryParams: { search: this.searchTerm.trim() } })
        .then(() => {
          setTimeout(() => {
            const gamesSection = document.getElementById('games');
            if (gamesSection) {
              gamesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }, 100);
        });
    } else {
      console.log('Search term is empty');
    }
  }

  onSearchKeyUp(event: KeyboardEvent): void {
    if (event.key === 'Enter') {
      this.onSearch();
    }
  }
}
