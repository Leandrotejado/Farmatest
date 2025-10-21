<?php
/**
 * Script de configuración automática del sistema administrativo
 * Ejecuta todas las consultas SQL necesarias para configurar la base de datos
 */

require_once 'config/db.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuración del Sistema Administrativo</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
</head>
<body class='bg-gray-100'>
    <div class='min-h-screen flex items-center justify-center p-4'>
        <div class='bg-white rounded-lg shadow-lg p-8 max-w-4xl w-full'>
            <div class='text-center mb-8'>
                <i class='fas fa-cogs text-6xl text-blue-600 mb-4'></i>
                <h1 class='text-3xl font-bold text-gray-800'>Configuración del Sistema Administrativo</h1>
                <p class='text-gray-600'>Configurando base de datos y datos iniciales...</p>
            </div>";

try {
    // Leer el archivo SQL
    $sql_file = 'database/setup_sistema_admin.sql';
    if (!file_exists($sql_file)) {
        throw new Exception('Archivo SQL no encontrado: ' . $sql_file);
    }
    
    $sql_content = file_get_contents($sql_file);
    $queries = explode(';', $sql_content);
    
    $success_count = 0;
    $error_count = 0;
    $errors = [];
    
    echo "<div class='space-y-4'>";
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($query);
            $success_count++;
            
            // Mostrar progreso
            if (strpos($query, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $query, $matches);
                $table_name = $matches[1] ?? 'tabla';
                echo "<div class='flex items-center p-3 bg-green-50 rounded-lg'>
                        <i class='fas fa-check-circle text-green-600 mr-3'></i>
                        <span class='text-green-800'>Tabla '$table_name' creada exitosamente</span>
                      </div>";
            } elseif (strpos($query, 'INSERT') !== false) {
                echo "<div class='flex items-center p-3 bg-blue-50 rounded-lg'>
                        <i class='fas fa-database text-blue-600 mr-3'></i>
                        <span class='text-blue-800'>Datos insertados exitosamente</span>
                      </div>";
            } elseif (strpos($query, 'ALTER TABLE') !== false) {
                preg_match('/ALTER TABLE.*?`?(\w+)`?/i', $query, $matches);
                $table_name = $matches[1] ?? 'tabla';
                echo "<div class='flex items-center p-3 bg-yellow-50 rounded-lg'>
                        <i class='fas fa-edit text-yellow-600 mr-3'></i>
                        <span class='text-yellow-800'>Tabla '$table_name' actualizada exitosamente</span>
                      </div>";
            } elseif (strpos($query, 'CREATE INDEX') !== false) {
                echo "<div class='flex items-center p-3 bg-purple-50 rounded-lg'>
                        <i class='fas fa-search text-purple-600 mr-3'></i>
                        <span class='text-purple-800'>Índice creado exitosamente</span>
                      </div>";
            }
            
        } catch (PDOException $e) {
            $error_count++;
            $errors[] = $e->getMessage();
            echo "<div class='flex items-center p-3 bg-red-50 rounded-lg'>
                    <i class='fas fa-exclamation-triangle text-red-600 mr-3'></i>
                    <span class='text-red-800'>Error: " . htmlspecialchars($e->getMessage()) . "</span>
                  </div>";
        }
    }
    
    echo "</div>";
    
    // Resumen final
    echo "<div class='mt-8 p-6 bg-gray-50 rounded-lg'>
            <h2 class='text-xl font-semibold mb-4'>Resumen de la Configuración</h2>
            <div class='grid grid-cols-1 md:grid-cols-3 gap-4'>
                <div class='text-center p-4 bg-green-100 rounded-lg'>
                    <i class='fas fa-check-circle text-3xl text-green-600 mb-2'></i>
                    <p class='text-2xl font-bold text-green-800'>$success_count</p>
                    <p class='text-green-700'>Operaciones Exitosas</p>
                </div>
                <div class='text-center p-4 bg-red-100 rounded-lg'>
                    <i class='fas fa-exclamation-triangle text-3xl text-red-600 mb-2'></i>
                    <p class='text-2xl font-bold text-red-800'>$error_count</p>
                    <p class='text-red-700'>Errores</p>
                </div>
                <div class='text-center p-4 bg-blue-100 rounded-lg'>
                    <i class='fas fa-database text-3xl text-blue-600 mb-2'></i>
                    <p class='text-2xl font-bold text-blue-800'>" . ($success_count + $error_count) . "</p>
                    <p class='text-blue-700'>Total Operaciones</p>
                </div>
            </div>";
    
    if ($error_count > 0) {
        echo "<div class='mt-4 p-4 bg-red-50 rounded-lg'>
                <h3 class='font-semibold text-red-800 mb-2'>Errores encontrados:</h3>
                <ul class='list-disc list-inside text-red-700'>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div>";
    }
    
    echo "</div>";
    
    // Botones de acción
    echo "<div class='mt-8 flex justify-center space-x-4'>";
    
    if ($error_count == 0) {
        echo "<a href='admin/dashboard.php' class='bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 flex items-center'>
                <i class='fas fa-check mr-2'></i>
                Ir al Dashboard
              </a>";
    } else {
        echo "<button onclick='location.reload()' class='bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 flex items-center'>
                <i class='fas fa-redo mr-2'></i>
                Reintentar
              </button>";
    }
    
    echo "<a href='admin/login.php' class='bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center'>
            <i class='fas fa-sign-in-alt mr-2'></i>
            Ir al Login
          </a>";
    
    echo "</div>";
    
    // Información adicional
    echo "<div class='mt-8 p-4 bg-blue-50 rounded-lg'>
            <h3 class='font-semibold text-blue-800 mb-2'>Información del Sistema:</h3>
            <div class='text-blue-700 text-sm space-y-1'>
                <p><strong>Usuario por defecto:</strong> admin</p>
                <p><strong>Contraseña por defecto:</strong> password</p>
                <p><strong>Email:</strong> admin@farmacia.com</p>
                <p><strong>Rol:</strong> Administrador</p>
            </div>
            <div class='mt-3 p-3 bg-yellow-100 rounded'>
                <p class='text-yellow-800 text-sm'>
                    <i class='fas fa-exclamation-triangle mr-1'></i>
                    <strong>Importante:</strong> Cambia la contraseña por defecto después del primer login.
                </p>
            </div>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='p-6 bg-red-50 rounded-lg'>
            <div class='flex items-center mb-4'>
                <i class='fas fa-exclamation-triangle text-red-600 mr-3'></i>
                <h2 class='text-xl font-semibold text-red-800'>Error en la Configuración</h2>
            </div>
            <p class='text-red-700 mb-4'>" . htmlspecialchars($e->getMessage()) . "</p>
            <div class='flex space-x-4'>
                <button onclick='location.reload()' class='bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700'>
                    <i class='fas fa-redo mr-2'></i>Reintentar
                </button>
                <a href='admin/login.php' class='bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700'>
                    <i class='fas fa-sign-in-alt mr-2'></i>Ir al Login
                </a>
            </div>
          </div>";
}

echo "</div>
    </div>
</body>
</html>";
?>
