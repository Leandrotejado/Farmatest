/**
 * JavaScript principal de la aplicación FarmaXpress
 * Funcionalidades comunes y utilidades
 */

// Configuración global
const App = {
    config: {
        apiUrl: '/api',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    },
    
    // Inicialización
    init() {
        this.setupEventListeners();
        this.setupAjaxDefaults();
        this.setupNotifications();
    },
    
    // Configurar event listeners globales
    setupEventListeners() {
        // Confirmar eliminaciones
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-confirm]')) {
                const message = e.target.getAttribute('data-confirm');
                if (!confirm(message)) {
                    e.preventDefault();
                }
            }
        });
        
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('.alert[data-auto-hide]');
            alerts.forEach(alert => {
                const delay = parseInt(alert.getAttribute('data-auto-hide')) || 5000;
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, delay);
            });
        });
        
        // Formularios con validación
        document.addEventListener('submit', (e) => {
            if (e.target.matches('[data-validate]')) {
                if (!this.validateForm(e.target)) {
                    e.preventDefault();
                }
            }
        });
    },
    
    // Configurar AJAX por defecto
    setupAjaxDefaults() {
        // Configurar fetch con headers por defecto
        this.originalFetch = window.fetch;
        window.fetch = (url, options = {}) => {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...options.headers
                }
            };
            
            if (this.config.csrfToken) {
                defaultOptions.headers['X-CSRF-Token'] = this.config.csrfToken;
            }
            
            return this.originalFetch(url, { ...defaultOptions, ...options });
        };
    },
    
    // Sistema de notificaciones
    setupNotifications() {
        this.notificationContainer = document.createElement('div');
        this.notificationContainer.className = 'notification-container';
        this.notificationContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(this.notificationContainer);
    },
    
    // Mostrar notificación
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.style.cssText = `
            margin-bottom: 10px;
            animation: slideIn 0.3s ease;
            cursor: pointer;
        `;
        notification.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
            </div>
        `;
        
        this.notificationContainer.appendChild(notification);
        
        // Auto-remove
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
        
        // Click to dismiss
        notification.addEventListener('click', () => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        });
    },
    
    // Validar formulario
    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo es requerido');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });
        
        // Validar emails
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !this.isValidEmail(field.value)) {
                this.showFieldError(field, 'Email inválido');
                isValid = false;
            }
        });
        
        return isValid;
    },
    
    // Mostrar error en campo
    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.cssText = 'color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
        field.style.borderColor = '#ef4444';
    },
    
    // Limpiar error de campo
    clearFieldError(field) {
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        field.style.borderColor = '';
    },
    
    // Validar email
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    // Utilidades AJAX
    async post(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: JSON.stringify(data)
            });
            
            return await response.json();
        } catch (error) {
            this.showNotification('Error de conexión', 'danger');
            throw error;
        }
    },
    
    async get(url) {
        try {
            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            this.showNotification('Error de conexión', 'danger');
            throw error;
        }
    },
    
    // Utilidades de formato
    formatCurrency(amount) {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(amount);
    },
    
    formatDate(date) {
        return new Intl.DateTimeFormat('es-AR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(new Date(date));
    },
    
    // Debounce para búsquedas
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// Utilidades de búsqueda
const SearchUtils = {
    // Búsqueda en tiempo real
    setupRealTimeSearch(inputSelector, resultsSelector, searchUrl) {
        const input = document.querySelector(inputSelector);
        const results = document.querySelector(resultsSelector);
        
        if (!input || !results) return;
        
        const debouncedSearch = App.debounce(async (query) => {
            if (query.length < 2) {
                results.innerHTML = '';
                return;
            }
            
            try {
                const response = await App.get(`${searchUrl}?q=${encodeURIComponent(query)}`);
                this.displaySearchResults(results, response.data || response);
            } catch (error) {
                console.error('Error en búsqueda:', error);
            }
        }, 300);
        
        input.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    },
    
    displaySearchResults(container, results) {
        if (!results || results.length === 0) {
            container.innerHTML = '<div class="p-4 text-gray-500">No se encontraron resultados</div>';
            return;
        }
        
        const html = results.map(item => `
            <div class="search-result-item p-3 border-b hover:bg-gray-50 cursor-pointer">
                <div class="font-medium">${item.nombre || item.title}</div>
                <div class="text-sm text-gray-600">${item.descripcion || item.description || ''}</div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }
};

// Utilidades de formularios
const FormUtils = {
    // Enviar formulario con AJAX
    async submitForm(form, options = {}) {
        const formData = new FormData(form);
        const url = form.action || window.location.href;
        
        try {
            const response = await fetch(url, {
                method: form.method || 'POST',
                body: formData,
                ...options
            });
            
            const result = await response.json();
            
            if (result.success) {
                App.showNotification(result.message || 'Operación exitosa', 'success');
                
                if (result.redirect) {
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                }
            } else {
                App.showNotification(result.message || 'Error en la operación', 'danger');
            }
            
            return result;
        } catch (error) {
            App.showNotification('Error de conexión', 'danger');
            throw error;
        }
    },
    
    // Resetear formulario
    resetForm(form) {
        form.reset();
        const errors = form.querySelectorAll('.field-error');
        errors.forEach(error => error.remove());
        
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => input.style.borderColor = '');
    }
};

// Inicializar aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

// Exportar para uso global
window.App = App;
window.SearchUtils = SearchUtils;
window.FormUtils = FormUtils;

