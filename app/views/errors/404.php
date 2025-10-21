<?php
/**
 * Página de Error 404 - FarmaXpress
 */

// Obtener la URL solicitada para mostrar en el error
$requested_url = $_SERVER['REQUEST_URI'] ?? 'URL desconocida';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada - FarmaXpress</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
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
            background: #f7fafc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .error-details h3 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .error-details code {
            background: #e2e8f0;
            padding: 8px 12px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            color: #e53e3e;
            word-break: break-all;
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
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
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
            content: "💡";
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
        <div class="error-code">404</div>
        <h1 class="error-title">¡Página No Encontrada!</h1>
        <p class="error-message">
            Lo sentimos, la página que estás buscando no existe o ha sido movida.
        </p>
        
        <div class="error-details">
            <h3>URL solicitada:</h3>
            <code><?php echo htmlspecialchars($requested_url); ?></code>
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
                <li>Verificar que la URL esté escrita correctamente</li>
                <li>Usar el menú de navegación para encontrar lo que buscas</li>
                <li>Ir a la página principal y explorar desde ahí</li>
                <li>Contactar al administrador si crees que es un error</li>
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
