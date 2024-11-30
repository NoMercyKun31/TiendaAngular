import { Component } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { Usuario } from '../../models/usuario'; // Ensure this import path is correct

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  standalone: true,
  imports: [FormsModule, CommonModule, RouterModule]
})
export class RegisterComponent {
  user: Usuario = {
    id: 0,
    username: '',
    email: '',
    password: ''
  };

  constructor(
    private authService: AuthService, 
    private router: Router
  ) {}

  register(form: NgForm) {
    if (form.valid) {
      this.authService.register(this.user).subscribe({
        next: (response) => {
          if (response.success) {
            // Show success message
            alert('Usuario registrado correctamente');
            // Navigate to login page
            this.router.navigate(['/login']);
          } else {
            // Show error message from server
            alert(response.message || 'Error al registrar el usuario');
          }
        },
        error: (error) => {
          // Handle network or unexpected errors
          console.error('Error en registro:', error);
          alert('Error al registrar el usuario. Por favor, intente nuevamente.');
        }
      });
    } else {
      // Mark all fields as touched to show validation errors
      Object.keys(form.controls).forEach(field => {
        const control = form.controls[field];
        control.markAsTouched({ onlySelf: true });
      });
    }
  }
}