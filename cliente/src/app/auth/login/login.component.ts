import { Component } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  standalone: true,
  imports: [FormsModule]
})
export class LoginComponent {
  credentials = { username: '', password: '' };

  constructor(private authService: AuthService, private router: Router) {}

  login() {
    this.authService.login(this.credentials.username, this.credentials.password).subscribe(
      response => {
        if (response.success) {
          alert('Inicio de sesión exitoso');
          this.router.navigate(['/profile']);
        } else {
          alert(response.message);
        }
      },
      error => {
        console.error('Error en inicio de sesión:', error);
        alert('Error al iniciar sesión.');
      }
    );
  }

  goToRegister() {
    this.router.navigate(['/register']);
  }
}
