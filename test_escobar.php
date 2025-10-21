<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Farmacia Escobar - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9fafb; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .farmacia-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 15px 0; background: white; }
        .button { background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3); margin: 5px; }
        .button:hover { background: #2563eb; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4); }
        .debug { background: #f3f4f6; padding: 10px; border-radius: 5px; margin: 10px 0; font-family: monospace; font-size: 12px; }
        .success { background: #d1fae5; color: #065f46; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏥 Test Específico - Farmacia Escobar</h1>
        <p>Esta página prueba específicamente la funcionalidad con "Farmacia Escobar".</p>
        
        <div class="farmacia-card">
            <h3>🏥 Farmacia Escobar</h3>
            <p><strong>Dirección:</strong> Av. Tapia de Cruz 1500, Escobar</p>
            <p><strong>Teléfono:</strong> (03488) 456-789</p>
            <p><strong>Estado:</strong> <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 4px;">Abierta 24hs</span></p>
            
            <div class="debug">
                <strong>Coordenadas de prueba:</strong><br>
                Lat: -34.3500<br>
                Lng: -58.7833<br>
                Nombre: "Farmacia Escobar"
            </div>
            
            <button class="button" onclick="testEscobar()">
                🗺️ Ir a Farmacia Escobar
            </button>
            
            <button class="button" onclick="testWithDifferentParams()">
                🧪 Test con Parámetros Diferentes
            </button>
        </div>
        
        <div id="result" style="margin-top: 20px;"></div>
    </div>

    <script>
        // Datos de Farmacia Escobar
        const farmaciaEscobar = {
            id: 26,
            name: "Farmacia Escobar",
            lat: -34.3500,
            lng: -58.7833,
            address: "Av. Tapia de Cruz 1500, Escobar",
            phone: "(03488) 456-789",
            status: "abierta",
            distrito: "Escobar"
        };
        
        function testEscobar() {
            console.log('=== TEST FARMACIA ESCOBAR ===');
            console.log('Datos originales:', farmaciaEscobar);
            
            // Simular la llamada exacta que se hace desde el botón
            const lat = farmaciaEscobar.lat;
            const lng = farmaciaEscobar.lng;
            const name = farmaciaEscobar.name;
            
            console.log('Parámetros a pasar:', { lat, lng, name });
            console.log('Tipos:', { 
                latType: typeof lat, 
                lngType: typeof lng, 
                nameType: typeof name 
            });
            
            // Llamar a la función getDirections
            getDirections(lat, lng, name);
        }
        
        function testWithDifferentParams() {
            console.log('=== TEST CON PARÁMETROS DIFERENTES ===');
            
            // Test 1: Con números como strings
            console.log('Test 1: Números como strings');
            getDirections("-34.3500", "-58.7833", "Farmacia Escobar");
            
            setTimeout(() => {
                // Test 2: Con parseFloat
                console.log('Test 2: Con parseFloat');
                getDirections(parseFloat(-34.3500), parseFloat(-58.7833), "Farmacia Escobar");
            }, 2000);
        }
        
        function getDirections(lat, lon, name) {
            try {
                // Debug: mostrar información recibida
                console.log('getDirections llamada con:', { 
                    lat: lat, 
                    lon: lon, 
                    name: name,
                    latType: typeof lat,
                    lonType: typeof lon,
                    latValid: !isNaN(lat),
                    lonValid: !isNaN(lon)
                });
                
                showResult('info', `🔍 Debug: getDirections(${lat}, ${lon}, "${name}")`);
                
                // Verificar que las coordenadas sean válidas
                if (lat === null || lat === undefined || lon === null || lon === undefined) {
                    console.error('Coordenadas nulas o indefinidas:', { lat, lon, name });
                    showResult('error', `❌ Error: No se encontraron coordenadas para ${name}`);
                    return;
                }
                
                if (isNaN(lat) || isNaN(lon)) {
                    console.error('Coordenadas no son números:', { lat, lon, name });
                    showResult('error', `❌ Error: Coordenadas inválidas para ${name}\nLat: ${lat} (${typeof lat})\nLon: ${lon} (${typeof lon})`);
                    return;
                }
                
                // Verificar rangos válidos de coordenadas
                if (lat < -90 || lat > 90 || lon < -180 || lon > 180) {
                    console.error('Coordenadas fuera de rango:', { lat, lon, name });
                    showResult('error', `❌ Error: Coordenadas fuera de rango para ${name}\nLat: ${lat}\nLon: ${lon}`);
                    return;
                }
                
                console.log('Coordenadas válidas, procediendo con Google Maps...');
                showResult('success', `✅ Coordenadas válidas para ${name}: ${lat}, ${lon}`);
                
                // Crear URL de Google Maps simplificada y más compatible
                const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lon}&travelmode=driving`;
                console.log('URL de Google Maps:', googleMapsUrl);
                
                // Intentar abrir Google Maps
                try {
                    const newWindow = window.open(googleMapsUrl, '_blank', 'noopener,noreferrer');
                    
                    // Verificar si se pudo abrir la ventana
                    if (!newWindow) {
                        throw new Error('No se pudo abrir la ventana - posible bloqueador de popups');
                    }
                    
                    console.log('Ventana abierta exitosamente');
                    showResult('success', `✅ Google Maps abierto para ${name}`);
                    
                    // Verificar si la ventana se cerró inmediatamente (bloqueador de popups)
                    setTimeout(() => {
                        if (newWindow.closed) {
                            console.log('Ventana se cerró, mostrando opciones alternativas');
                            showResult('error', '⚠️ La ventana se cerró. Posible bloqueador de popups activo.');
                            showFallbackOptions(lat, lon, name);
                        }
                    }, 1000);
                    
                } catch (error) {
                    console.log('Error al abrir ventana, mostrando opciones alternativas:', error);
                    showResult('error', `❌ Error al abrir ventana: ${error.message}`);
                    showFallbackOptions(lat, lon, name);
                }
                
            } catch (error) {
                console.error('Error general al abrir direcciones:', error);
                showResult('error', `❌ Error inesperado: ${error.message}\n\nFarmacia: ${name}\nCoordenadas: ${lat}, ${lon}`);
                showFallbackOptions(lat, lon, name);
            }
        }
        
        function showFallbackOptions(lat, lon, name) {
            const options = `
                🗺️ Opciones para llegar a ${name}:
                
                1. 📱 Google Maps: https://maps.google.com/maps?q=${lat},${lon}
                2. 🍎 Apple Maps: https://maps.apple.com/?q=${lat},${lon}
                3. 📋 Coordenadas: ${lat}, ${lon}
                
                ¿Qué opción prefieres?
            `;
            
            const choice = confirm(options + '\n\n¿Quieres abrir Google Maps en una nueva pestaña?');
            
            if (choice) {
                // Intentar abrir Google Maps directamente
                window.open(`https://maps.google.com/maps?q=${lat},${lon}`, '_blank');
                showResult('success', '✅ Google Maps abierto (método alternativo)');
            } else {
                // Mostrar coordenadas para copiar
                if (confirm('¿Quieres copiar las coordenadas al portapapeles?')) {
                    copyToClipboard(`${lat}, ${lon}`);
                }
            }
        }
        
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                // Usar la API moderna de clipboard
                navigator.clipboard.writeText(text).then(() => {
                    showResult('success', '✅ Coordenadas copiadas al portapapeles: ' + text);
                }).catch(() => {
                    fallbackCopyToClipboard(text);
                });
            } else {
                // Fallback para navegadores más antiguos
                fallbackCopyToClipboard(text);
            }
        }
        
        function fallbackCopyToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                showResult('success', '✅ Coordenadas copiadas al portapapeles: ' + text);
            } catch (err) {
                showResult('error', '❌ No se pudo copiar automáticamente. Coordenadas: ' + text);
            }
            
            document.body.removeChild(textArea);
        }
        
        function showResult(type, message) {
            const resultDiv = document.getElementById('result');
            const timestamp = new Date().toLocaleTimeString();
            resultDiv.innerHTML += `<div class="${type}">[${timestamp}] ${message}</div>`;
        }
        
        // Mostrar información del navegador al cargar
        window.onload = function() {
            console.log('Información del navegador:');
            console.log('User Agent:', navigator.userAgent);
            console.log('Clipboard API disponible:', !!navigator.clipboard);
            console.log('Contexto seguro:', window.isSecureContext);
            
            showResult('info', '🔧 Página cargada. Abre la consola del navegador (F12) para ver logs detallados.');
        };
    </script>
</body>
</html>

