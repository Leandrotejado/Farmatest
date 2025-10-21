<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Autocompletado - FarmaXpress</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Demo del Sistema de Autocompletado</h1>
            <p class="text-gray-600 mb-8">Prueba el sistema de autocompletado inteligente. Escribe las primeras letras y verás las sugerencias aparecer automáticamente.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Categorías -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">📋 Categorías de Medicamentos</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Categoría:</label>
                            <input type="text" id="categoria-input" 
                                   placeholder="Escribe 'analg' o 'anti'..." 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Prueba escribiendo: analg, anti, cardio, etc.</p>
                        </div>
                        
                        <div id="categoria-info" class="hidden p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-blue-800">Información de la Categoría:</h3>
                            <div id="categoria-details" class="text-sm text-blue-700 mt-2"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Proveedores -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">🚚 Proveedores</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Proveedor:</label>
                            <input type="text" id="proveedor-input" 
                                   placeholder="Escribe 'pfizer' o 'roche'..." 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Prueba escribiendo: pfizer, roche, novartis, etc.</p>
                        </div>
                        
                        <div id="proveedor-info" class="hidden p-4 bg-green-50 rounded-lg">
                            <h3 class="font-semibold text-green-800">Información del Proveedor:</h3>
                            <div id="proveedor-details" class="text-sm text-green-700 mt-2"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Medicamentos -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">💊 Medicamentos</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Medicamento:</label>
                            <input type="text" id="medicamento-input" 
                                   placeholder="Escribe 'ibuprofeno' o 'paracetamol'..." 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Prueba escribiendo nombres de medicamentos</p>
                        </div>
                        
                        <div id="medicamento-info" class="hidden p-4 bg-purple-50 rounded-lg">
                            <h3 class="font-semibold text-purple-800">Información del Medicamento:</h3>
                            <div id="medicamento-details" class="text-sm text-purple-700 mt-2"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario Completo -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">📝 Formulario Completo</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría:</label>
                            <input type="text" id="form-categoria" 
                                   placeholder="Selecciona una categoría..." 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor:</label>
                            <input type="text" id="form-proveedor" 
                                   placeholder="Selecciona un proveedor..." 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medicamento:</label>
                            <input type="text" id="form-medicamento" 
                                   placeholder="Selecciona un medicamento..." 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <button onclick="procesarFormulario()" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-save"></i> Procesar Formulario
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Resultados -->
            <div id="resultados" class="mt-8 hidden">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">📊 Resultados del Formulario:</h2>
                <div id="resultados-content" class="p-4 bg-gray-50 rounded-lg"></div>
            </div>
            
            <!-- Instrucciones -->
            <div class="mt-8 p-6 bg-yellow-50 rounded-lg">
                <h3 class="font-semibold text-yellow-800 mb-2">💡 Cómo usar el Autocompletado:</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• <strong>Escribe 2+ caracteres</strong> para ver sugerencias</li>
                    <li>• <strong>Usa las flechas ↑↓</strong> para navegar entre opciones</li>
                    <li>• <strong>Presiona Enter</strong> para seleccionar</li>
                    <li>• <strong>Presiona Escape</strong> para cerrar</li>
                    <li>• <strong>Haz clic</strong> en una sugerencia para seleccionarla</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Incluir el sistema de autocompletado -->
    <script src="../public/js/autocomplete.js"></script>
    
    <script>
        // Inicializar autocompletados
        let categoriaAutocomplete, proveedorAutocomplete, medicamentoAutocomplete;
        let formCategoriaAutocomplete, formProveedorAutocomplete, formMedicamentoAutocomplete;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Autocompletados de demostración
            categoriaAutocomplete = initAutocomplete('categoria-input', '../admin/api_autocomplete_categorias.php', {
                showDescription: true,
                showIcon: true
            });
            
            proveedorAutocomplete = initAutocomplete('proveedor-input', '../admin/api_autocomplete_proveedores.php', {
                showDescription: true
            });
            
            medicamentoAutocomplete = initAutocomplete('medicamento-input', '../admin/api_autocomplete_medicamentos.php', {
                showDescription: true
            });
            
            // Autocompletados del formulario
            formCategoriaAutocomplete = initAutocomplete('form-categoria', '../admin/api_autocomplete_categorias.php', {
                showDescription: true,
                showIcon: true
            });
            
            formProveedorAutocomplete = initAutocomplete('form-proveedor', '../admin/api_autocomplete_proveedores.php', {
                showDescription: true
            });
            
            formMedicamentoAutocomplete = initAutocomplete('form-medicamento', '../admin/api_autocomplete_medicamentos.php', {
                showDescription: true
            });
            
            // Event listeners para mostrar información
            document.getElementById('categoria-input').addEventListener('autocomplete-select', function(e) {
                mostrarInfoCategoria(e.detail.data);
            });
            
            document.getElementById('proveedor-input').addEventListener('autocomplete-select', function(e) {
                mostrarInfoProveedor(e.detail.data);
            });
            
            document.getElementById('medicamento-input').addEventListener('autocomplete-select', function(e) {
                mostrarInfoMedicamento(e.detail.data);
            });
        });
        
        function mostrarInfoCategoria(data) {
            const infoDiv = document.getElementById('categoria-info');
            const detailsDiv = document.getElementById('categoria-details');
            
            if (data) {
                detailsDiv.innerHTML = `
                    <strong>ID:</strong> ${data.id}<br>
                    <strong>Nombre:</strong> ${data.nombre}<br>
                    <strong>Descripción:</strong> ${data.descripcion}<br>
                    <strong>Tipo de Venta:</strong> ${data.tipo_venta}<br>
                    <strong>Icono:</strong> ${data.tipo_icon}
                `;
                infoDiv.classList.remove('hidden');
            }
        }
        
        function mostrarInfoProveedor(data) {
            const infoDiv = document.getElementById('proveedor-info');
            const detailsDiv = document.getElementById('proveedor-details');
            
            if (data) {
                detailsDiv.innerHTML = `
                    <strong>ID:</strong> ${data.id}<br>
                    <strong>Nombre:</strong> ${data.nombre}<br>
                    <strong>Contacto:</strong> ${data.contacto}<br>
                    <strong>Teléfono:</strong> ${data.telefono}<br>
                    <strong>Email:</strong> ${data.email}<br>
                    <strong>Ciudad:</strong> ${data.ciudad}, ${data.provincia}
                `;
                infoDiv.classList.remove('hidden');
            }
        }
        
        function mostrarInfoMedicamento(data) {
            const infoDiv = document.getElementById('medicamento-info');
            const detailsDiv = document.getElementById('medicamento-details');
            
            if (data) {
                detailsDiv.innerHTML = `
                    <strong>ID:</strong> ${data.id}<br>
                    <strong>Nombre:</strong> ${data.nombre}<br>
                    <strong>Precio:</strong> $${data.precio}<br>
                    <strong>Stock:</strong> ${data.stock}<br>
                    <strong>Categoría:</strong> ${data.categoria}<br>
                    <strong>Proveedor:</strong> ${data.proveedor}
                `;
                infoDiv.classList.remove('hidden');
            }
        }
        
        function procesarFormulario() {
            const categoria = document.getElementById('form-categoria').value;
            const proveedor = document.getElementById('form-proveedor').value;
            const medicamento = document.getElementById('form-medicamento').value;
            
            if (!categoria || !proveedor || !medicamento) {
                alert('Por favor, completa todos los campos usando el autocompletado');
                return;
            }
            
            const resultadosDiv = document.getElementById('resultados');
            const contentDiv = document.getElementById('resultados-content');
            
            contentDiv.innerHTML = `
                <div class="space-y-2">
                    <p><strong>✅ Categoría seleccionada:</strong> ${categoria}</p>
                    <p><strong>✅ Proveedor seleccionado:</strong> ${proveedor}</p>
                    <p><strong>✅ Medicamento seleccionado:</strong> ${medicamento}</p>
                </div>
                <div class="mt-4 p-3 bg-green-100 rounded">
                    <p class="text-green-800"><strong>🎉 ¡Formulario procesado exitosamente!</strong></p>
                    <p class="text-sm text-green-700">Todos los campos fueron completados usando el sistema de autocompletado inteligente.</p>
                </div>
            `;
            
            resultadosDiv.classList.remove('hidden');
        }
    </script>
</body>
</html>
