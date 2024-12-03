# Tienda de Videojuegos Retro con Angular y PHP

## Índice

1. [Introducción](#introducci%C3%B3n)
2. [Características](#caracter%C3%ADsticas)
3. [Detalles a considerar](#detalles-a-considerar)

---

## Introducción

Este proyecto es una tienda de videojuegos retro desarrollada con **Angular** en el frontend y **PHP** en el backend. Su objetivo principal es demostrar los conceptos básicos de Angular y PHP a través de una aplicación funcional y atractiva.

---

## Características

- **Interfaz amigable y futurista**: Diseño visual moderno con estilos únicos, incluyendo paginación en el listado de productos.
- **Filtrado y búsqueda de productos**: Los usuarios pueden filtrar productos por categoría o buscarlos por nombre de manera eficiente.
- **Gestión de favoritos**: Posibilidad de agregar productos favoritos y posteriormente incluirlos en el carrito.
- **Carrito de compras**: Funcionalidad para añadir videojuegos al carrito y proceder con la compra, disponible tras iniciar sesión.
- **Autenticación de usuarios**: Registro e inicio de sesión con validaciones en tiempo real. Incluye una sección para visualizar datos en la pantalla de cuenta personal.
- **Acceso de administración**: Gestión avanzada de productos, usuarios y pedidos mediante un panel exclusivo.
- **Gestión de productos**: Crear, editar y eliminar productos con soporte para actualizaciones de stock y descuentos.
- **Gestión de usuarios**: Administración completa para añadir, editar y eliminar usuarios.
- **Gestión de pedidos**: Visualización de pedidos realizados junto con los productos adquiridos.

---

## Detalles a considerar

1. **Error en "Mi Cuenta"**: Actualmente, el historial de pedidos no se visualiza correctamente.
2. **Validación de tarjeta de crédito**:
   - Los números de tarjeta deben tener 16 dígitos sin espacios.
   - La validación utiliza el algoritmo de Luhn.
3. **Tarjetas de prueba válidas**:
   - 4532 7812 3456 7890
   - 4539 1488 0343 6467
   - 6011 1111 1111 1117
   - 4111 1111 1111 1111
   - 5555 5555 5555 4444
