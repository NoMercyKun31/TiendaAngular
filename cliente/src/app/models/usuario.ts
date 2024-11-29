export class Usuario {
    id: number = 0;
    username: string = '';
    password: string = '';
    email: string  = '';

    constructor(id: number | undefined, username: string | undefined, password: string | undefined, email: string | undefined) {
        this.id = id || 0;
        this.username = username || '';
        this.password = password || '';
        this.email = email || '';
    }
}
