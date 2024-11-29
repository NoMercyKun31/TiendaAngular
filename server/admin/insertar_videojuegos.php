<?php
require "../librerias_php/setup_red_bean.php";

// Definir las categorías disponibles
$categorias = [
    'Acción',
    'Aventura',
    'RPG',
    'Estrategia',
    'Simulación',
    'Carreras',
    'Plataformas',
    'Arcade',
    'Lucha'
];

// Array de videojuegos retro con datos predefinidos
$videojuegos = [
    // Acción
    [
        'nombre' => 'Contra',
        'categoria' => 'Acción',
        'compania' => 'Konami',
        'anyolanzamiento' => 1987,
        'precio' => 29.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Ghosts \'n Goblins',
        'categoria' => 'Acción',
        'compania' => 'Capcom',
        'anyolanzamiento' => 1985,
        'precio' => 24.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Ninja Gaiden',
        'categoria' => 'Acción',
        'compania' => 'Tecmo',
        'anyolanzamiento' => 1988,
        'precio' => 27.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Golden Axe',
        'categoria' => 'Acción',
        'compania' => 'Sega',
        'anyolanzamiento' => 1989,
        'precio' => 26.99,
        'stock' => rand(0, 100)
    ],

    // Aventura
    [
        'nombre' => 'The Legend of Zelda',
        'categoria' => 'Aventura',
        'compania' => 'Nintendo',
        'anyolanzamiento' => 1986,
        'precio' => 34.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Maniac Mansion',
        'categoria' => 'Aventura',
        'compania' => 'LucasArts',
        'anyolanzamiento' => 1987,
        'precio' => 22.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Zak McKracken',
        'categoria' => 'Aventura',
        'compania' => 'LucasArts',
        'anyolanzamiento' => 1988,
        'precio' => 23.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Shadowgate',
        'categoria' => 'Aventura',
        'compania' => 'ICOM Simulations',
        'anyolanzamiento' => 1987,
        'precio' => 21.99,
        'stock' => rand(0, 100)
    ],

    // RPG
    [
        'nombre' => 'Final Fantasy',
        'categoria' => 'RPG',
        'compania' => 'Square',
        'anyolanzamiento' => 1987,
        'precio' => 39.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Dragon Quest',
        'categoria' => 'RPG',
        'compania' => 'Enix',
        'anyolanzamiento' => 1986,
        'precio' => 34.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Phantasy Star',
        'categoria' => 'RPG',
        'compania' => 'Sega',
        'anyolanzamiento' => 1987,
        'precio' => 36.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Ultima III: Exodus',
        'categoria' => 'RPG',
        'compania' => 'Origin Systems',
        'anyolanzamiento' => 1983,
        'precio' => 29.99,
        'stock' => rand(0, 100)
    ],

    // Estrategia
    [
        'nombre' => 'Populous',
        'categoria' => 'Estrategia',
        'compania' => 'Bullfrog Productions',
        'anyolanzamiento' => 1989,
        'precio' => 24.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'SimCity',
        'categoria' => 'Estrategia',
        'compania' => 'Maxis',
        'anyolanzamiento' => 1989,
        'precio' => 29.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Herzog Zwei',
        'categoria' => 'Estrategia',
        'compania' => 'Technosoft',
        'anyolanzamiento' => 1989,
        'precio' => 26.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Military Madness',
        'categoria' => 'Estrategia',
        'compania' => 'Hudson Soft',
        'anyolanzamiento' => 1989,
        'precio' => 23.99,
        'stock' => rand(0, 100)
    ],
    // Simulación
    [
        'nombre' => 'Elite',
        'categoria' => 'Simulación',
        'compania' => 'Acornsoft',
        'anyolanzamiento' => 1984,
        'precio' => 27.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'F-19 Stealth Fighter',
        'categoria' => 'Simulación',
        'compania' => 'MicroProse',
        'anyolanzamiento' => 1988,
        'precio' => 29.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Silent Service',
        'categoria' => 'Simulación',
        'compania' => 'MicroProse',
        'anyolanzamiento' => 1985,
        'precio' => 24.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'M.U.L.E.',
        'categoria' => 'Simulación',
        'compania' => 'Electronic Arts',
        'anyolanzamiento' => 1983,
        'precio' => 22.99,
        'stock' => rand(0, 100)
    ],
    // Carreras
    [
        'nombre' => 'Out Run',
        'categoria' => 'Carreras',
        'compania' => 'Sega',
        'anyolanzamiento' => 1986,
        'precio' => 24.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Pole Position',
        'categoria' => 'Carreras',
        'compania' => 'Namco',
        'anyolanzamiento' => 1982,
        'precio' => 19.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'F-Zero',
        'categoria' => 'Carreras',
        'compania' => 'Nintendo',
        'anyolanzamiento' => 1990,
        'precio' => 26.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Road Rash',
        'categoria' => 'Carreras',
        'compania' => 'Electronic Arts',
        'anyolanzamiento' => 1991,
        'precio' => 23.99,
        'stock' => rand(0, 100)
    ],
    // Plataformas
    [
        'nombre' => 'Super Mario Bros.',
        'categoria' => 'Plataformas',
        'compania' => 'Nintendo',
        'anyolanzamiento' => 1985,
        'precio' => 29.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Sonic the Hedgehog',
        'categoria' => 'Plataformas',
        'compania' => 'Sega',
        'anyolanzamiento' => 1991,
        'precio' => 29.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Castlevania',
        'categoria' => 'Plataformas',
        'compania' => 'Konami',
        'anyolanzamiento' => 1986,
        'precio' => 27.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Mega Man 2',
        'categoria' => 'Plataformas',
        'compania' => 'Capcom',
        'anyolanzamiento' => 1988,
        'precio' => 24.99,
        'stock' => rand(0, 100)
    ],

    // Arcade
    [
        'nombre' => 'Pac-Man',
        'categoria' => 'Arcade',
        'compania' => 'Namco',
        'anyolanzamiento' => 1980,
        'precio' => 19.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Donkey Kong',
        'categoria' => 'Arcade',
        'compania' => 'Nintendo',
        'anyolanzamiento' => 1981,
        'precio' => 24.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Frogger',
        'categoria' => 'Arcade',
        'compania' => 'Konami',
        'anyolanzamiento' => 1981,
        'precio' => 17.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Q*bert',
        'categoria' => 'Arcade',
        'compania' => 'Gottlieb',
        'anyolanzamiento' => 1982,
        'precio' => 18.99,
        'stock' => rand(0, 100)
    ],

    // Lucha
    [
        'nombre' => 'Street Fighter II',
        'categoria' => 'Lucha',
        'compania' => 'Capcom',
        'anyolanzamiento' => 1991,
        'precio' => 24.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Mortal Kombat',
        'categoria' => 'Lucha',
        'compania' => 'Midway',
        'anyolanzamiento' => 1992,
        'precio' => 26.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Fatal Fury',
        'categoria' => 'Lucha',
        'compania' => 'SNK',
        'anyolanzamiento' => 1991,
        'precio' => 22.99,
        'stock' => rand(0, 100)
    ],
    [
        'nombre' => 'Samurai Shodown',
        'categoria' => 'Lucha',
        'compania' => 'SNK',
        'anyolanzamiento' => 1993,
        'precio' => 23.99,
        'stock' => rand(0, 100)
    ],
];

// Insertar los videojuegos
foreach ($videojuegos as $datos) {
    $videojuego = R::dispense('videojuego');
    $videojuego->nombre = $datos['nombre'];
    $videojuego->categoria = $datos['categoria'];
    $videojuego->compania = $datos['compania'];
    $videojuego->anyolanzamiento = $datos['anyolanzamiento'];
    $videojuego->precio = $datos['precio'];
    $videojuego->descuento = 0;
    $videojuego->en_descuento = false;
    $videojuego->stock = $datos['stock'];
    
    $id_generada = R::store($videojuego);
    
    // Simular carga de imagen (necesitarás tener estas imágenes en tu carpeta de imágenes)
    $imagen_generica = "../imagenes/placeholder.jpg";
    $ruta_destino = "../imagenes/$id_generada.jpg";
    copy($imagen_generica, $ruta_destino);
}

echo "36 videojuegos retro arcade insertados correctamente.";
?>