import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { Router, RouterModule } from '@angular/router';
import { Usuario } from '../../models/usuario';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  imports: [RouterModule, CommonModule],
  standalone: true
})
export class ProfileComponent implements OnInit {
  currentUser: Usuario | null = null;

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {
    this.authService.currentUser.subscribe(
      user => {
        if (user) {
          this.currentUser = user;
        } else {
          this.authService.getCurrentUser().subscribe(
            fetchedUser => {
              this.currentUser = fetchedUser;
            },
            error => {
              console.error('Error al obtener el usuario actual:', error);
              this.router.navigate(['/login']);
            }
          );
        }
      }
    );
  }

  logout() {
    this.authService.logout().subscribe(() => {
      this.router.navigate(['/login']);
    });
  }

  goToHistorial() {
    console.log('Current user:', this.authService.currentUserValue);
    if (this.authService.currentUserValue) {
      console.log('Navigating to historial-pedidos');
      this.router.navigate(['/historial-pedidos'])
        .then(() => console.log('Navigation complete'))
        .catch(err => console.error('Navigation error:', err));
    } else {
      console.log('User not logged in');
      this.router.navigate(['/login']);
    }
  }
}