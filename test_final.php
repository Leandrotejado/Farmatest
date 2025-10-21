<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Final - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9fafb; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .button { background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 500; margin: 10px; }
        .button:hover { background: #2563eb; }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Test Final - FarmaXpress</h1>
        <p>Este es el test más simple posible. Si esto funciona, el problema está solucionado.</p>
        
        <div class="success">
            <h3>✅ Test 1: Farmacia Escobar</h3>
            <p><strong>Coordenadas:</strong> -34.3500, -58.7833</p>
            <button class="button" onclick="getDirections(-34.3500, -58.7833, 'Farmacia Escobar')">
                🗺️ Ir a Farmacia Escobar
            </button>
        </div>
        
        <div class="success">
            <h3>✅ Test 2: Farmacia Central</h3>
            <p><strong>Coordenadas:</strong> -34.6037, -58.3816</p>
            <button class="button" onclick="getDirections(-34.6037, -58.3816, 'Farmacia Central')">
                🗺️ Ir a Farmacia Central
            </button>
        </div>
        
        <div class="success">
            <h3>✅ Test 3: Con Strings</h3>
            <p><strong>Coordenadas:</strong> "-34.3500", "-58.7833"</p>
            <button class="button" onclick="getDirections('-34.3500', '-58.7833', 'Farmacia Test')">
                🗺️ Test con Strings
            </button>
        </div>
    </div>

    <script>
        function getDirections(lat, lon, name) {
            console.log('=== TEST FINAL ===');
            console.log('Parámetros recibidos:', { lat, lon, name });
            console.log('Tipos:', { latType: typeof lat, lonType: typeof lon, nameType: typeof name });
            
            // Verificar que tengamos los parámetros básicos
            if (!lat || !lon || !name) {
                alert('Error: Faltan datos de la farmacia');
                return;
            }
            
            // Convertir a números si vienen como strings
            const latNum = parseFloat(lat);
            const lonNum = parseFloat(lon);
            
            console.log('Después de parseFloat:', { latNum, lonNum });
            
            // Verificar que sean números válidos
            if (isNaN(latNum) || isNaN(lonNum)) {
                alert(`Error: Coordenadas inválidas\nLat: ${lat}\nLon: ${lon}`);
                return;
            }
            
            console.log('Coordenadas válidas, creando URL...');
            
            // Crear URL de Google Maps
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latNum},${lonNum}&travelmode=driving`;
            console.log('URL generada:', googleMapsUrl);
            
            // Intentar abrir Google Maps
            try {
                const newWindow = window.open(googleMapsUrl, '_blank', 'noopener,noreferrer');
                
                if (!newWindow) {
                    // Si no se puede abrir, mostrar opciones alternativas
                    const choice = confirm(`No se pudo abrir Google Maps automáticamente.\n\n¿Quieres abrir Google Maps manualmente?\n\nCoordenadas: ${latNum}, ${lonNum}`);
                    
                    if (choice) {
                        window.open(`https://maps.google.com/maps?q=${latNum},${lonNum}`, '_blank');
                    }
                } else {
                    console.log('✅ Google Maps abierto exitosamente');
                }
            } catch (error) {
                console.error('Error al abrir Google Maps:', error);
                // Si hay error, mostrar coordenadas para copiar
                alert(`Error al abrir Google Maps.\n\nCoordenadas de ${name}:\n${latNum}, ${lonNum}\n\nPuedes copiar estas coordenadas y pegarlas en Google Maps.`);
            }
        }
        
        console.log('🔧 Página cargada. Abre la consola (F12) para ver los logs detallados.');
    </script>
</body>
</html>

