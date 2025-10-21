<?php 
$title = 'Email Verificado - FarmaXpress';
include '../layout/header.php'; 
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">¡Email Verificado!</h2>
    </div>
    
    <div class="text-center space-y-4">
        <p class="text-gray-600">
            Tu cuenta ha sido verificada exitosamente. ¡Ya puedes usar todos los servicios de FarmaXpress!
        </p>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="font-semibold text-green-900 mb-2">¿Qué puedes hacer ahora?</h3>
            <ul class="text-sm text-green-800 text-left space-y-1">
                <li>• Buscar medicamentos disponibles</li>
                <li>• Encontrar farmacias de turno</li>
                <li>• Recibir recomendaciones personalizadas</li>
                <li>• Acceder a tu dashboard personal</li>
            </ul>
        </div>
        
        <div class="pt-4">
            <a href="/login" class="btn btn-primary w-full">
                Iniciar Sesión
            </a>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>

