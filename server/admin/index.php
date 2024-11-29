<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Noto Sans', sans-serif;
        }
        .welcome-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .cursor {
            animation: blink 0.7s infinite;
        }
        @keyframes blink {
            0% { opacity: 0; }
            50% { opacity: 1; }
            100% { opacity: 0; }
        }
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include("menu.html") ?>
    
    <div class="container mt-5">
        <h1 class="welcome-title display-4 text-center" id="welcome-message"></h1>
        <span class="cursor text-center d-block" aria-hidden="true">|</span>

        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Gestión de Usuarios</h5>
                        <p class="card-text">Administra usuarios</p>
                        <a href="gestionar_usuarios.php" class="btn btn-outline-primary">Ir a Usuarios</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x mb-3 text-success"></i>
                        <h5 class="card-title">Gestionar Productos</h5>
                        <p class="card-text">Administra tus productos</p>
                        <a href="gestionar_productos.php" class="btn btn-outline-success">Ir a Productos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-truck fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title">Gestionar Pedidos</h5>
                        <p class="card-text">Administrar tus pedidos</p>
                        <a href="gestionar_pedidos.php" class="btn btn-outline-warning">Ir a Pedidos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>  

    <!-- Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const text = "Bienvenido a la parte de Administración";
            const welcomeMessage = document.getElementById('welcome-message');
            let index = 0;

            function typeWriter() {
                if (index < text.length) {
                    welcomeMessage.innerHTML += text.charAt(index);
                    index++;
                    setTimeout(typeWriter, 100);
                }
            }
            typeWriter();
        });
    </script>
</body>
</html>