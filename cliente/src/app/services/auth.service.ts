import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { map, tap } from 'rxjs/operators';
import { Usuario } from '../models/usuario';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject: BehaviorSubject<Usuario | null>;
  public currentUser: Observable<Usuario | null>;
  private ruta_rest_services = "http://localhost/Angular/tiendaAngular/server/";

  constructor(private http: HttpClient) {
    this.currentUserSubject = new BehaviorSubject<Usuario | null>(this.getUserFromStorage());
    this.currentUser = this.currentUserSubject.asObservable();
  }

  private getUserFromStorage(): Usuario | null {
    const storedUser = localStorage.getItem('currentUser');
    return storedUser ? JSON.parse(storedUser) : null;
  }

  public get currentUserValue(): Usuario | null {
    return this.currentUserSubject.value;
  }

  login(username: string, password: string): Observable<any> {
    return this.http.post<any>(this.ruta_rest_services + "rest/auth_service.php", { action: 'login', username, password })
      .pipe(
        tap(response => {
          if (response.success) {
            localStorage.setItem('currentUser', JSON.stringify(response.user));
            this.currentUserSubject.next(response.user);
          }
        })
      );
  }

  logout(): Observable<any> {
    return this.http.post<any>(this.ruta_rest_services + "rest/auth_service.php", { action: 'logout' })
      .pipe(
        tap(() => {
          localStorage.removeItem('currentUser');
          this.currentUserSubject.next(null);
        })
      );
  }

  register(user: Usuario): Observable<any> {
    return this.http.post<any>(this.ruta_rest_services + "rest/auth_service.php", { action: 'register', ...user });
  }

  isLoggedIn(): Observable<boolean> {
    return this.currentUser.pipe(map(user => !!user));
  }

  getCurrentUser(): Observable<Usuario | null> {
    return this.http.get<any>(this.ruta_rest_services + "rest/auth_service.php?action=current_user")
      .pipe(
        tap(user => {
          if (user) {
            localStorage.setItem('currentUser', JSON.stringify(user));
            this.currentUserSubject.next(user);
          }
        })
      );
    }
}