import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  standalone: true,
  imports: [FormsModule]
})
export class RegisterComponent {
  user = { id: 0, username: '', email: '', password: '' };

  constructor(private authService: AuthService, private router: Router) {}
  register() {
    this.authService.register(this.user).subscribe(
      response => {
        if (response.success) {
          alert('Usuario registrado correctamente');
          this.router.navigate(['/login']);
        } else {
          alert(response.message);
        }
      },
      error => {
        console.error('Error en registro:', error);
        alert('Error al registrar el usuario.');
      }
    );
  }
}