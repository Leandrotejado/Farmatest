<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Direcciones - FarmaXpress</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f9fafb;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .farmacia-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            background: white;
        }
        .button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
            margin: 5px;
        }
        .button:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🗺️ Test de Funcionalidad "Ir a esta Farmacia"</h1>
        <p>Esta página te permite probar la funcionalidad de direcciones con farmacias de ejemplo.</p>
        
        <div class="farmacia-card">
            <h3>🏥 Farmacia Central</h3>
            <p><strong>Dirección:</strong> Av. Corrientes 1234, Buenos Aires</p>
            <p><strong>Teléfono:</strong> (011) 1234-5678</p>
            <p><strong>Estado:</strong> <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 4px;">Abierta 24hs</span></p>
            <button class="button" onclick="testDirections(-34.6037, -58.3816, 'Farmacia Central')">
                🗺️ Ir a esta Farmacia
            </button>
        </div>
        
        <div class="farmacia-card">
            <h3>💊 Farmacia del Pueblo</h3>
            <p><strong>Dirección:</strong> Av. Santa Fe 5678, Buenos Aires</p>
            <p><strong>Teléfono:</strong> (011) 9876-5432</p>
            <p><strong>Estado:</strong> <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 4px;">Abierta 24hs</span></p>
            <button class="button" onclick="testDirections(-34.5875, -58.3974, 'Farmacia del Pueblo')">
                🗺️ Ir a esta Farmacia
            </button>
        </div>
        
        <div class="farmacia-card">
            <h3>🏪 Farmacia San Martín</h3>
            <p><strong>Dirección:</strong> Av. 9 de Julio 999, Buenos Aires</p>
            <p><strong>Teléfono:</strong> (011) 5555-1234</p>
            <p><strong>Estado:</strong> <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 4px;">Cerrada</span></p>
            <button class="button" onclick="testDirections(-34.6097, -58.3731, 'Farmacia San Martín')">
                🗺️ Ir a esta Farmacia
            </button>
        </div>
        
        <div id="result" style="margin-top: 20px;"></div>
    </div>

    <script>
        function testDirections(lat, lon, name) {
            console.log(`Probando direcciones para: ${name} (${lat}, ${lon})`);
            
            try {
                // Verificar que las coordenadas sean válidas
                if (!lat || !lon || isNaN(lat) || isNaN(lon)) {
                    showResult('error', 'Error: Coordenadas de la farmacia no válidas');
                    return;
                }
                
                // Crear URL de Google Maps simplificada y más compatible
                const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lon}&travelmode=driving`;
                
                // Intentar abrir Google Maps
                try {
                    const newWindow = window.open(googleMapsUrl, '_blank', 'noopener,noreferrer');
                    
                    // Verificar si se pudo abrir la ventana
                    if (!newWindow) {
                        throw new Error('No se pudo abrir la ventana');
                    }
                    
                    showResult('success', `✅ Google Maps abierto para ${name}`);
                    
                    // Verificar si la ventana se cerró inmediatamente (bloqueador de popups)
                    setTimeout(() => {
                        if (newWindow.closed) {
                            showResult('error', '⚠️ La ventana se cerró. Posible bloqueador de popups activo.');
                            showFallbackOptions(lat, lon, name);
                        }
                    }, 1000);
                    
                } catch (error) {
                    console.log('Error al abrir ventana, mostrando opciones alternativas:', error);
                    showFallbackOptions(lat, lon, name);
                }
                
            } catch (error) {
                console.error('Error al abrir direcciones:', error);
                showResult('error', '❌ Error al abrir direcciones: ' + error.message);
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
            resultDiv.innerHTML = `<div class="${type}">${message}</div>`;
        }
        
        // Mostrar información del navegador
        console.log('Información del navegador:');
        console.log('User Agent:', navigator.userAgent);
        console.log('Clipboard API disponible:', !!navigator.clipboard);
        console.log('Contexto seguro:', window.isSecureContext);
    </script>
</body>
</html>

