<?php 
$title = 'Dashboard de IA - FarmaXpress';
$additionalCSS = ['/css/ai-dashboard.css'];
include '../layout/header.php'; 
?>

<div class="ai-dashboard">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard de Inteligencia Artificial</h1>
        <p class="text-gray-600 mt-2">Análisis predictivo y recomendaciones inteligentes</p>
    </div>
    
    <!-- Estado del servicio -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Python</h3>
                        <p class="text-sm text-gray-500">
                            <?= $status['python_available'] ? 'Disponible' : 'No disponible' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Dependencias</h3>
                        <p class="text-sm text-gray-500">
                            <?= $status['dependencies_ok'] ? 'Instaladas' : 'Faltantes' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 <?= $status['ai_service_ready'] ? 'bg-green-100' : 'bg-red-100' ?> rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 <?= $status['ai_service_ready'] ? 'text-green-600' : 'text-red-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Servicio IA</h3>
                        <p class="text-sm text-gray-500">
                            <?= $status['ai_service_ready'] ? 'Activo' : 'Inactivo' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Insights del negocio -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Insights del Negocio</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($insights as $insight): ?>
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($insight['titulo']) ?></h3>
                    <p class="text-3xl font-bold text-blue-600 mb-2"><?= htmlspecialchars($insight['valor']) ?></p>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($insight['descripcion']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Anomalías detectadas -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Anomalías Detectadas</h2>
        <?php if (empty($anomalies)): ?>
        <div class="card">
            <div class="card-body text-center py-8">
                <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">¡Todo en orden!</h3>
                <p class="text-gray-600">No se detectaron anomalías en el inventario.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($anomalies as $anomaly): ?>
            <div class="card border-l-4 <?= $anomaly['severidad'] === 'alta' ? 'border-red-500' : 'border-yellow-500' ?>">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <?= htmlspecialchars($anomaly['medicamento_nombre']) ?>
                            </h3>
                            <p class="text-gray-600 mt-1"><?= htmlspecialchars($anomaly['mensaje']) ?></p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $anomaly['severidad'] === 'alta' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst($anomaly['severidad']) ?>
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                    <?= ucfirst(str_replace('_', ' ', $anomaly['tipo'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <button onclick="handleAnomaly(<?= $anomaly['medicamento_id'] ?>, '<?= $anomaly['tipo'] ?>')" 
                                    class="btn btn-primary btn-sm">
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Herramientas de IA -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Herramientas de IA</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Predicción de Demanda</h3>
                    <p class="text-gray-600 mb-4">Analiza patrones de ventas para predecir demanda futura.</p>
                    <div class="flex space-x-2">
                        <input type="number" id="medicamentoId" placeholder="ID Medicamento" 
                               class="form-input flex-1">
                        <button onclick="predictDemand()" class="btn btn-primary">
                            Predecir
                        </button>
                    </div>
                    <div id="demandResult" class="mt-4 hidden"></div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Recomendaciones</h3>
                    <p class="text-gray-600 mb-4">Encuentra medicamentos similares basados en IA.</p>
                    <div class="flex space-x-2">
                        <input type="number" id="recommendationId" placeholder="ID Medicamento" 
                               class="form-input flex-1">
                        <button onclick="getRecommendations()" class="btn btn-primary">
                            Recomendar
                        </button>
                    </div>
                    <div id="recommendationResult" class="mt-4 hidden"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function predictDemand() {
    const medicamentoId = document.getElementById('medicamentoId').value;
    const resultDiv = document.getElementById('demandResult');
    
    if (!medicamentoId) {
        App.showNotification('Por favor ingresa un ID de medicamento', 'warning');
        return;
    }
    
    try {
        const response = await fetch(`/ai/predict-demand?medicamento_id=${medicamentoId}`);
        const data = await response.json();
        
        if (data.success) {
            const prediction = data.data;
            resultDiv.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">Predicción de Demanda</h4>
                    <p class="text-blue-800">
                        <strong>Demanda esperada:</strong> ${prediction.prediccion_demanda} unidades<br>
                        <strong>Promedio diario:</strong> ${prediction.demanda_promedio_diaria} unidades<br>
                        <strong>Confianza:</strong> ${(prediction.confianza * 100).toFixed(1)}%<br>
                        <strong>Recomendación:</strong> ${prediction.recomendacion}
                    </p>
                </div>
            `;
            resultDiv.classList.remove('hidden');
        } else {
            App.showNotification(data.message, 'danger');
        }
    } catch (error) {
        App.showNotification('Error al obtener predicción', 'danger');
    }
}

async function getRecommendations() {
    const medicamentoId = document.getElementById('recommendationId').value;
    const resultDiv = document.getElementById('recommendationResult');
    
    if (!medicamentoId) {
        App.showNotification('Por favor ingresa un ID de medicamento', 'warning');
        return;
    }
    
    try {
        const response = await fetch(`/ai/recommendations?medicamento_id=${medicamentoId}`);
        const data = await response.json();
        
        if (data.success) {
            const recommendations = data.data;
            if (recommendations.length === 0) {
                resultDiv.innerHTML = '<p class="text-gray-600">No se encontraron recomendaciones.</p>';
            } else {
                const html = recommendations.map(rec => `
                    <div class="border-b pb-2 mb-2">
                        <h5 class="font-semibold">${rec.nombre}</h5>
                        <p class="text-sm text-gray-600">
                            ${rec.categoria} - $${rec.precio} 
                            <span class="text-blue-600">(${(rec.similitud * 100).toFixed(1)}% similar)</span>
                        </p>
                    </div>
                `).join('');
                
                resultDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-semibold text-green-900 mb-2">Medicamentos Similares</h4>
                        ${html}
                    </div>
                `;
            }
            resultDiv.classList.remove('hidden');
        } else {
            App.showNotification(data.message, 'danger');
        }
    } catch (error) {
        App.showNotification('Error al obtener recomendaciones', 'danger');
    }
}

function handleAnomaly(medicamentoId, tipo) {
    // Redirigir a la página de detalles del medicamento
    window.location.href = `/medicamentos/${medicamentoId}`;
}
</script>

<?php include '../layout/footer.php'; ?>

