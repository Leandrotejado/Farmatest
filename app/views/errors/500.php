<?php
/**
 * Página de Error 500 - FarmaXpress
 */

// Obtener información del error si está disponible
$error_message = $_SESSION['error_message'] ?? 'Error interno del servidor';
$error_file = $_SESSION['error_file'] ?? 'Archivo desconocido';
$error_line = $_SESSION['error_line'] ?? 'Línea desconocida';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Servidor - FarmaXpress</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #e53e3e;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .error-title {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .error-message {
            font-size: 18px;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .error-details {
            background: #fef5e7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #e53e3e;
        }
        
        .error-details h3 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .error-details code {
            background: #fed7d7;
            padding: 8px 12px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            color: #c53030;
            word-break: break-all;
            display: block;
            margin: 5px 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #e53e3e;
            color: white;
        }
        
        .btn-primary:hover {
            background: #c53030;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }
        
        .icon {
            font-size: 20px;
        }
        
        .suggestions {
            margin-top: 40px;
            text-align: left;
        }
        
        .suggestions h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .suggestions ul {
            list-style: none;
            padding: 0;
        }
        
        .suggestions li {
            padding: 8px 0;
            color: #718096;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .suggestions li::before {
            content: "🔧";
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .error-container {
                padding: 40px 20px;
            }
            
            .error-code {
                font-size: 80px;
            }
            
            .error-title {
                font-size: 24px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-title">¡Error del Servidor!</h1>
        <p class="error-message">
            Ha ocurrido un error interno en el servidor. Nuestro equipo técnico ha sido notificado.
        </p>
        
        <div class="error-details">
            <h3>Detalles del Error:</h3>
            <code><?php echo htmlspecialchars($error_message); ?></code>
            <?php if ($error_file !== 'Archivo desconocido'): ?>
            <code>Archivo: <?php echo htmlspecialchars($error_file); ?></code>
            <?php endif; ?>
            <?php if ($error_line !== 'Línea desconocida'): ?>
            <code>Línea: <?php echo htmlspecialchars($error_line); ?></code>
            <?php endif; ?>
        </div>
        
        <div class="action-buttons">
            <a href="/farmatest/public/" class="btn btn-primary">
                <span class="icon">🏠</span>
                Ir al Inicio
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <span class="icon">⬅️</span>
                Volver Atrás
            </a>
        </div>
        
        <div class="suggestions">
            <h3>¿Qué puedes hacer?</h3>
            <ul>
                <li>Intentar recargar la página en unos minutos</li>
                <li>Verificar tu conexión a internet</li>
                <li>Contactar al administrador si el problema persiste</li>
                <li>Usar una funcionalidad diferente mientras tanto</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Auto-redireccionar después de 30 segundos si el usuario no hace nada
        setTimeout(function() {
            if (confirm('¿Te gustaría volver a la página principal?')) {
                window.location.href = '/farmatest/public/';
            }
        }, 30000);
    </script>
</body>
</html>
