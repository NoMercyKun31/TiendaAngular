import { Videojuego } from "./videojuego";

export class VideojuegoCarrito {
    videojuego: Videojuego = {} as Videojuego;
    cantidad: number = 1;

    constructor(videojuego: Videojuego, cantidad: number) {
        this.videojuego = videojuego;
        this.cantidad = cantidad;
    }
}
