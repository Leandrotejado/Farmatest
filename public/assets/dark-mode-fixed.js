/**
 * Modo Oscuro - FarmaXpress
 * Script simple y efectivo para toggle de modo oscuro
 */

(function() {
    'use strict';

    // Configuración
    const STORAGE_KEY = 'farmaXpress_theme';
    const DARK_CLASS = 'dark';
    
    // Elementos del DOM
    let toggleButton = null;
    let body = null;

    /**
     * Crear botón de toggle
     */
    function createToggleButton() {
        toggleButton = document.createElement('button');
        toggleButton.id = 'dark-mode-toggle';
        toggleButton.innerHTML = '🌙';
        toggleButton.title = 'Cambiar modo oscuro/claro';
        toggleButton.setAttribute('aria-label', 'Toggle dark mode');
        
        // Estilos del botón
        toggleButton.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            border: none;
            font-size: 24px;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

        // Event listener
        toggleButton.addEventListener('click', toggleDarkMode);
        
        // Agregar al DOM
        document.body.appendChild(toggleButton);
        
        return toggleButton;
    }

    /**
     * Toggle del modo oscuro
     */
    function toggleDarkMode() {
        const isDark = body.classList.toggle(DARK_CLASS);
        
        // Guardar preferencia
        localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
        
        // Actualizar botón
        updateToggleButton(isDark);
        
        // Log para debugging
        console.log('Modo oscuro:', isDark ? 'activado' : 'desactivado');
    }

    /**
     * Actualizar icono del botón
     */
    function updateToggleButton(isDark) {
        if (toggleButton) {
            toggleButton.innerHTML = isDark ? '☀️' : '🌙';
            toggleButton.title = isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
        }
    }

    /**
     * Cargar tema guardado
     */
    function loadSavedTheme() {
        const savedTheme = localStorage.getItem(STORAGE_KEY);
        
        if (savedTheme === 'dark') {
            body.classList.add(DARK_CLASS);
            updateToggleButton(true);
        } else {
            body.classList.remove(DARK_CLASS);
            updateToggleButton(false);
        }
    }

    /**
     * Detectar preferencia del sistema
     */
    function detectSystemPreference() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }

    /**
     * Aplicar tema inicial
     */
    function applyInitialTheme() {
        const savedTheme = localStorage.getItem(STORAGE_KEY);
        const systemTheme = detectSystemPreference();
        const themeToApply = savedTheme || systemTheme;
        
        if (themeToApply === 'dark') {
            body.classList.add(DARK_CLASS);
            updateToggleButton(true);
        } else {
            body.classList.remove(DARK_CLASS);
            updateToggleButton(false);
        }
    }

    /**
     * Inicializar modo oscuro
     */
    function init() {
        body = document.body;
        
        // Crear botón
        createToggleButton();
        
        // Aplicar tema inicial
        applyInitialTheme();
        
        // Escuchar cambios en preferencia del sistema
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem(STORAGE_KEY)) {
                    if (e.matches) {
                        body.classList.add(DARK_CLASS);
                        updateToggleButton(true);
                    } else {
                        body.classList.remove(DARK_CLASS);
                        updateToggleButton(false);
                    }
                }
            });
        }
        
        console.log('Modo oscuro inicializado correctamente');
    }

    /**
     * API pública
     */
    window.DarkMode = {
        toggle: toggleDarkMode,
        isDark: function() {
            return body && body.classList.contains(DARK_CLASS);
        },
        setTheme: function(theme) {
            if (theme === 'dark') {
                body.classList.add(DARK_CLASS);
                updateToggleButton(true);
                localStorage.setItem(STORAGE_KEY, 'dark');
            } else {
                body.classList.remove(DARK_CLASS);
                updateToggleButton(false);
                localStorage.setItem(STORAGE_KEY, 'light');
            }
        }
    };

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
