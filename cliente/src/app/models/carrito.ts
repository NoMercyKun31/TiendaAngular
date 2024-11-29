import { Videojuego } from "./videojuego";

export class Carrito {
    id: number = 0;
    usuario_id: number = 0;
    videojuego_id: number = 0;
    cantidad: number = 0;
    videojuego?: Videojuego;
  
    constructor(usuario_id: number, videojuego_id: number, cantidad: number = 1, videojuego?: Videojuego) {
        this.usuario_id = usuario_id;
        this.videojuego_id = videojuego_id;
        this.cantidad = cantidad;
        this.videojuego = videojuego;
    }
}
