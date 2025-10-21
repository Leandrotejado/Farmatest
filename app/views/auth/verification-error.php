<?php 
$title = 'Error de Verificación - FarmaXpress';
include '../layout/header.php'; 
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Error de Verificación</h2>
    </div>
    
    <div class="text-center space-y-4">
        <p class="text-gray-600">
            <?= htmlspecialchars($message ?? 'Hubo un problema al verificar tu email.') ?>
        </p>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-900 mb-2">Posibles causas:</h3>
            <ul class="text-sm text-red-800 text-left space-y-1">
                <li>• El enlace ha expirado (válido por 24 horas)</li>
                <li>• El enlace ya fue utilizado</li>
                <li>• El enlace es inválido o corrupto</li>
                <li>• La cuenta ya está verificada</li>
            </ul>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">¿Qué puedes hacer?</h3>
            <ul class="text-sm text-blue-800 text-left space-y-1">
                <li>• Solicitar un nuevo correo de verificación</li>
                <li>• Intentar iniciar sesión si ya verificaste</li>
                <li>• Contactar soporte si el problema persiste</li>
            </ul>
        </div>
        
        <div class="pt-4 space-y-2">
            <button onclick="resendVerification()" class="btn btn-primary w-full">
                Solicitar Nuevo Correo
            </button>
            <a href="/login" class="btn btn-secondary w-full">
                Intentar Iniciar Sesión
            </a>
        </div>
    </div>
</div>

<!-- Modal para reenviar verificación -->
<div id="resendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Solicitar Nuevo Correo de Verificación</h3>
        
        <form id="resendForm">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" id="email" name="email" required
                       class="form-input" placeholder="tu@email.com">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeResendModal()" class="btn btn-secondary">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    Enviar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function resendVerification() {
    document.getElementById('resendModal').classList.remove('hidden');
    document.getElementById('resendModal').classList.add('flex');
}

function closeResendModal() {
    document.getElementById('resendModal').classList.add('hidden');
    document.getElementById('resendModal').classList.remove('flex');
}

document.getElementById('resendForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/auth/resend-verification', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            App.showNotification(data.message, 'success');
            closeResendModal();
            setTimeout(() => {
                window.location.href = '/verification-sent';
            }, 2000);
        } else {
            App.showNotification(data.message, 'danger');
        }
    } catch (error) {
        App.showNotification('Error de conexión', 'danger');
    }
});

// Cerrar modal al hacer clic fuera
document.getElementById('resendModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeResendModal();
    }
});
</script>

<?php include '../layout/footer.php'; ?>

