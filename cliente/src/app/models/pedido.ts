import { Videojuego } from "./videojuego";
import { PedidoItem } from './pedido-item';

export class Pedido {
    id: number = -1;
    usuario_id: number = -1;
    items: PedidoItem[] = [];
    nombreCompleto: string = '';
    email: string = '';
    direccion: string = '';
    ciudad: string = '';
    provincia: string = '';
    codigoPostal: string = '';
    titularTarjeta: string = '';
    numeroTarjeta: string = '';
    fechaVencimiento: string = '';
    cvv: string = '';
    total: number = 0;

    constructor(
        id: number = -1,
        usuario_id: number = -1,
        items: PedidoItem[] = [],
        nombreCompleto: string = '',
        email: string = '',
        direccion: string = '',
        ciudad: string = '',
        provincia: string = '',
        codigoPostal: string = '',
        titularTarjeta: string = '',
        numeroTarjeta: string = '',
        fechaVencimiento: string = '',
        cvv: string = '',
        total: number = 0
    ) {
        this.id = id;
        this.usuario_id = usuario_id;
        this.items = items;
        this.nombreCompleto = nombreCompleto;
        this.email = email;
        this.direccion = direccion;
        this.ciudad = ciudad;
        this.provincia = provincia;
        this.codigoPostal = codigoPostal;
        this.titularTarjeta = titularTarjeta;
        this.numeroTarjeta = numeroTarjeta;
        this.fechaVencimiento = fechaVencimiento;
        this.cvv = cvv;
        this.total = total;
    }
}
