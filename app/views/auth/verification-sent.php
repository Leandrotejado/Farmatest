<?php 
$title = 'Verificación Enviada - FarmaXpress';
include '../layout/header.php'; 
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">¡Correo Enviado!</h2>
    </div>
    
    <div class="text-center space-y-4">
        <p class="text-gray-600">
            Hemos enviado un correo de verificación a tu dirección de email.
        </p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">¿Qué hacer ahora?</h3>
            <ol class="text-sm text-blue-800 text-left space-y-1">
                <li>1. Revisa tu bandeja de entrada</li>
                <li>2. Busca el correo de FarmaXpress</li>
                <li>3. Haz clic en el enlace de verificación</li>
                <li>4. ¡Tu cuenta estará lista!</li>
            </ol>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>¿No recibiste el correo?</strong><br>
                Revisa tu carpeta de spam o correo no deseado.
            </p>
        </div>
        
        <div class="pt-4">
            <button onclick="resendVerification()" class="btn btn-secondary mr-2">
                Reenviar Correo
            </button>
            <a href="/login" class="btn btn-primary">
                Ir al Login
            </a>
        </div>
    </div>
</div>

<!-- Modal para reenviar verificación -->
<div id="resendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Reenviar Correo de Verificación</h3>
        
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
                    Reenviar
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
    const email = formData.get('email');
    
    try {
        const response = await fetch('/auth/resend-verification', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            App.showNotification(data.message, 'success');
            closeResendModal();
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

