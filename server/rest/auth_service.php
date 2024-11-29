<?php

$allowedOrigins = array('http://localhost:4200');
if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

require_once "../librerias_php/setup_red_bean.php";

class AuthService {
    public function login($username, $password) {
        $usuario = R::findOne('usuario', 'username = ?', [$username]);
        
        if ($usuario && password_verify($password, $usuario->password)) {
            $_SESSION['user_id'] = $usuario->id;
            $_SESSION['username'] = $usuario->username;
            return [
                'success' => true,
                'user' => [
                    'id' => $usuario->id,
                    'username' => $usuario->username,
                    'email' => $usuario->email
                ]
            ];
        } else {
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada correctamente'];
    }

    public function register($username, $email, $password) {
        $existingUser = R::findOne('usuario', 'username = ? OR email = ?', [$username, $email]);
        
        if ($existingUser) {
            return ['success' => false, 'message' => 'El usuario o email ya existe'];
        }

        $usuario = R::dispense('usuario');
        $usuario->username = $username;
        $usuario->email = $email;
        $usuario->password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $id = R::store($usuario);
            return ['success' => true, 'message' => 'Usuario registrado correctamente', 'user_id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al registrar el usuario: ' . $e->getMessage()];
        }
    }

    public function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            $usuario = R::load('usuario', $_SESSION['user_id']);
            if ($usuario->id) {
                return [
                    'id' => $usuario->id,
                    'username' => $usuario->username,
                    'email' => $usuario->email
                ];
            }
        }
        return null;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

// Iniciar la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Crear una instancia del servicio de autenticación
$authService = new AuthService();

// Manejar las solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'login':
                echo json_encode($authService->login($data['username'], $data['password']));
                break;
            case 'logout':
                echo json_encode($authService->logout());
                break;
            case 'register':
                echo json_encode($authService->register($data['username'], $data['email'], $data['password']));
                break;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'current_user') {
        echo json_encode($authService->getCurrentUser());
    } elseif (isset($_GET['action']) && $_GET['action'] === 'is_logged_in') {
        echo json_encode(['logged_in' => $authService->isLoggedIn()]);
    }
}
?>