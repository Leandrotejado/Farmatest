<?php include '../layout/header.php'; ?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h2>
    
    <form id="loginForm" class="space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" required 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" id="password" name="password" required 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Iniciar Sesión
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <p class="text-sm text-gray-600">
            ¿No tienes cuenta? <a href="/register" class="text-blue-600 hover:text-blue-500">Regístrate aquí</a>
        </p>
    </div>
    
    <div id="message" class="mt-4 hidden"></div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('message');
    
    try {
        const response = await fetch('/auth/login', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageDiv.className = 'mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded';
            messageDiv.textContent = data.message;
            messageDiv.classList.remove('hidden');
            
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            messageDiv.className = 'mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded';
            messageDiv.textContent = data.message;
            messageDiv.classList.remove('hidden');
        }
    } catch (error) {
        messageDiv.className = 'mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded';
        messageDiv.textContent = 'Error de conexión. Inténtalo de nuevo.';
        messageDiv.classList.remove('hidden');
    }
});
</script>

<?php include '../layout/footer.php'; ?>

