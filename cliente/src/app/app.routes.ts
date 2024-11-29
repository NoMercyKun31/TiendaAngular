import { RouterModule, Routes } from '@angular/router';
import { ListadoComponent } from './listado/listado.component';
import { CarritoComponent } from './carrito/carrito.component';
import { PedidoComponent } from './pedido/pedido.component';
import { DetallesComponent } from './detalles/detalles.component';
import { RegisterComponent } from './auth/register/register.component';
import { LoginComponent } from './auth/login/login.component';
import { ProfileComponent } from './auth/profile/profile.component';
import { NgModule } from '@angular/core';
import { AuthGuard } from './auth/auth.guard';
import { FavoritoService } from './services/favorito.service';
import { FavoritosComponent } from './favoritos/favoritos.component';
import { HistorialPedidosComponent } from './historial-pedidos/historial-pedidos.component';

export const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'listado', component: ListadoComponent },
    { path: 'carrito', component: CarritoComponent },
    { path: 'pedido', component: PedidoComponent },
    { path: 'detalles/:id', component: DetallesComponent },
    { path: 'register', component: RegisterComponent },
    { path: 'login', component: LoginComponent },
    { path: 'profile', component: ProfileComponent, canActivate: [AuthGuard] },
    { path: 'favoritos', component: FavoritosComponent },
    { path: 'historial-pedidos', component: HistorialPedidosComponent, canActivate: [AuthGuard] },
    { path: '**', redirectTo: 'login' }
];

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule]
  })
  export class AppRoutingModule { }