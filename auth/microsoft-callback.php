<?php
/**
 * Callback de Microsoft OAuth
 */

session_start();
require_once '../config/db.php';
require_once '../services/MultiOAuthService.php';

$error = '';
$success = '';

try {
    if (!isset($_GET['code'])) {
        throw new Exception('Código de autorización no recibido');
    }
    
    $code = $_GET['code'];
    $state = $_GET['state'] ?? '';
    
    // Verificar estado CSRF si existe
    if ($state && isset($_SESSION['oauth_state'])) {
        $oauthService = new MultiOAuthService();
        if (!$oauthService->verifyState($state, $_SESSION['oauth_state'])) {
            throw new Exception('Estado CSRF inválido');
        }
    }
    
    // Procesar login con Microsoft
    $oauthService = new MultiOAuthService();
    $result = $oauthService->processCallback('microsoft', $code, $pdo);
    
    if ($result['success']) {
        // Iniciar sesión
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['usuario_nombre'] = $result['user']['nombre'];
        $_SESSION['email'] = $result['user']['email'];
        $_SESSION['rol'] = $result['user']['rol'];
        
        // Redirigir según el rol
        if ($result['user']['rol'] == 'admin') {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../public/dashboard.php');
        }
        exit();
    } else {
        $error = $result['message'];
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login con Microsoft - FarmaXpress</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #0078d4 0%, #106ebe 100%);
            margin: 0; 
            padding: 0; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { 
            max-width: 500px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .success { 
            background: #d1fae5; 
            color: #065f46; 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0;
            border-left: 4px solid #059669;
        }
        .error { 
            background: #fee2e2; 
            color: #991b1b; 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0;
            border-left: 4px solid #dc2626;
        }
        .btn { 
            background: #0078d4; 
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 8px; 
            text-decoration: none; 
            display: inline-block; 
            margin: 10px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn:hover { 
            background: #106ebe; 
            transform: translateY(-2px);
        }
        .icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        .success .icon { color: #059669; }
        .error .icon { color: #dc2626; }
        h1 { color: #1f2937; margin-bottom: 10px; }
        p { color: #6b7280; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <div class="success">
                <div class="icon">✅</div>
                <h1>¡Login Exitoso!</h1>
                <p><?php echo $success; ?></p>
            </div>
        <?php elseif ($error): ?>
            <div class="error">
                <div class="icon">❌</div>
                <h1>Error en Login</h1>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="../public/login.php" class="btn">🔐 Volver al Login</a>
            <a href="../public/index.php" class="btn">🏠 Ir al Inicio</a>
        </div>
    </div>
</body>
</html>
