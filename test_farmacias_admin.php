<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Gestión de Farmacias - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9fafb; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .button:hover { background: #2563eb; }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; background: white; }
        .farmacia-item { border: 1px solid #e5e7eb; border-radius: 5px; padding: 10px; margin: 5px 0; background: #f9fafb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Gestión de Farmacias - FarmaXpress</h1>
        <p>Esta página te permite probar todas las funcionalidades del sistema de gestión de farmacias.</p>
        
        <div class="grid">
            <div class="card">
                <h3>🔧 Configuración</h3>
                <p>Configura la tabla de farmacias y agrega datos de ejemplo.</p>
                <a href="setup_farmacias.php" class="button">Configurar Farmacias</a>
            </div>
            
            <div class="card">
                <h3>🏥 Gestión de Farmacias</h3>
                <p>Panel administrativo para gestionar farmacias y turnos.</p>
                <a href="admin/farmacias.php" class="button">Gestionar Farmacias</a>
            </div>
            
            <div class="card">
                <h3>📊 Dashboard Admin</h3>
                <p>Panel de control administrativo completo.</p>
                <a href="admin/dashboard.php" class="button">Dashboard Admin</a>
            </div>
            
            <div class="card">
                <h3>🗺️ Mapa de Farmacias</h3>
                <p>Ver farmacias en el mapa público.</p>
                <a href="public/index.php" class="button">Ver Mapa</a>
            </div>
            
            <div class="card">
                <h3>🔌 API de Farmacias</h3>
                <p>Probar la API que obtiene farmacias de la base de datos.</p>
                <a href="public/api_farmacias.php" class="button">Ver API</a>
            </div>
        </div>
        
        <div class="info">
            <h3>📋 Funcionalidades del Sistema:</h3>
            <ul>
                <li><strong>✅ Crear farmacias:</strong> Agregar nuevas farmacias con coordenadas</li>
                <li><strong>✅ Editar farmacias:</strong> Modificar información existente</li>
                <li><strong>✅ Gestionar turnos:</strong> Marcar farmacias como de turno (24hs) o diurnas</li>
                <li><strong>✅ Eliminar farmacias:</strong> Remover farmacias del sistema</li>
                <li><strong>✅ Ver en mapa:</strong> Las farmacias aparecen automáticamente en el mapa público</li>
                <li><strong>✅ API integrada:</strong> El mapa obtiene datos de la base de datos</li>
            </ul>
        </div>
        
        <div class="success">
            <h3>🎯 Pasos para Probar:</h3>
            <ol>
                <li><strong>Configurar:</strong> Ejecuta "Configurar Farmacias" para crear la tabla</li>
                <li><strong>Gestionar:</strong> Ve a "Gestionar Farmacias" para administrar</li>
                <li><strong>Probar turnos:</strong> Cambia el estado de turno de algunas farmacias</li>
                <li><strong>Ver mapa:</strong> Ve al mapa público para ver las farmacias</li>
                <li><strong>Verificar API:</strong> Revisa que la API devuelva los datos correctos</li>
            </ol>
        </div>
        
        <div class="info">
            <h3>🔍 Casos de Prueba:</h3>
            <div class="grid">
                <div>
                    <h4>Crear Nueva Farmacia:</h4>
                    <ul>
                        <li>Nombre: "Farmacia Test"</li>
                        <li>Distrito: "Test District"</li>
                        <li>Dirección: "Calle Test 123"</li>
                        <li>Teléfono: "(011) 123-4567"</li>
                        <li>Latitud: -34.6037</li>
                        <li>Longitud: -58.3816</li>
                        <li>Horario: 08:00 - 20:00</li>
                        <li>Marcar como "de turno"</li>
                    </ul>
                </div>
                
                <div>
                    <h4>Gestionar Turnos:</h4>
                    <ul>
                        <li>Cambiar farmacias de "diurna" a "24hs"</li>
                        <li>Cambiar farmacias de "24hs" a "diurna"</li>
                        <li>Verificar que se actualicen en el mapa</li>
                    </ul>
                </div>
                
                <div>
                    <h4>Verificar Mapa:</h4>
                    <ul>
                        <li>Las farmacias deben aparecer en el mapa</li>
                        <li>Los marcadores deben mostrar información correcta</li>
                        <li>El botón "Ir a esta Farmacia" debe funcionar</li>
                        <li>Las farmacias de turno deben estar marcadas como "Abierta 24hs"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
