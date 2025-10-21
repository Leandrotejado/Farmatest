/**
 * Sistema de Autocompletado Inteligente para FarmaXpress
 */

class AutocompleteSystem {
    constructor(inputElement, apiUrl, options = {}) {
        this.input = inputElement;
        this.apiUrl = apiUrl;
        this.options = {
            minLength: 2,
            maxSuggestions: 10,
            delay: 300,
            showDescription: true,
            showIcon: true,
            ...options
        };
        
        this.suggestionsContainer = null;
        this.currentSuggestions = [];
        this.selectedIndex = -1;
        this.timeout = null;
        
        this.init();
    }
    
    init() {
        // Crear contenedor de sugerencias
        this.createSuggestionsContainer();
        
        // Event listeners
        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
        this.input.addEventListener('blur', (e) => this.handleBlur(e));
        this.input.addEventListener('focus', (e) => this.handleFocus(e));
        
        // Click fuera del input para cerrar sugerencias
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.suggestionsContainer.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }
    
    createSuggestionsContainer() {
        this.suggestionsContainer = document.createElement('div');
        this.suggestionsContainer.className = 'autocomplete-suggestions';
        this.suggestionsContainer.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-top: none;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        `;
        
        // Insertar después del input
        this.input.parentNode.style.position = 'relative';
        this.input.parentNode.appendChild(this.suggestionsContainer);
    }
    
    async handleInput(e) {
        const query = e.target.value.trim();
        
        // Limpiar timeout anterior
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
        
        // Si la consulta es muy corta, ocultar sugerencias
        if (query.length < this.options.minLength) {
            this.hideSuggestions();
            return;
        }
        
        // Debounce para evitar muchas consultas
        this.timeout = setTimeout(() => {
            this.fetchSuggestions(query);
        }, this.options.delay);
    }
    
    async fetchSuggestions(query) {
        try {
            const response = await fetch(`${this.apiUrl}?q=${encodeURIComponent(query)}&limit=${this.options.maxSuggestions}`);
            const data = await response.json();
            
            if (data.suggestions) {
                this.currentSuggestions = data.suggestions;
                this.showSuggestions();
            } else {
                this.hideSuggestions();
            }
        } catch (error) {
            console.error('Error fetching suggestions:', error);
            this.hideSuggestions();
        }
    }
    
    showSuggestions() {
        if (this.currentSuggestions.length === 0) {
            this.hideSuggestions();
            return;
        }
        
        this.suggestionsContainer.innerHTML = '';
        
        this.currentSuggestions.forEach((suggestion, index) => {
            const item = this.createSuggestionItem(suggestion, index);
            this.suggestionsContainer.appendChild(item);
        });
        
        this.suggestionsContainer.style.display = 'block';
        this.selectedIndex = -1;
    }
    
    createSuggestionItem(suggestion, index) {
        const item = document.createElement('div');
        item.className = 'autocomplete-item';
        item.style.cssText = `
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s;
        `;
        
        // Contenido principal
        let content = `<div style="font-weight: 500; color: #1f2937;">${suggestion.value}</div>`;
        
        // Información adicional si está disponible
        if (suggestion.data) {
            const data = suggestion.data;
            
            // Icono para categorías
            if (data.tipo_icon && this.options.showIcon) {
                content = `<div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">${data.tipo_icon}</span>
                    <div style="font-weight: 500; color: #1f2937;">${suggestion.value}</div>
                </div>`;
            }
            
            // Descripción adicional
            if (this.options.showDescription) {
                let description = '';
                
                if (data.descripcion) {
                    description = data.descripcion;
                } else if (data.contacto) {
                    description = `Contacto: ${data.contacto}`;
                } else if (data.precio) {
                    description = `Precio: $${data.precio} | Stock: ${data.stock}`;
                }
                
                if (description) {
                    content += `<div style="font-size: 12px; color: #6b7280; margin-top: 2px;">${description}</div>`;
                }
            }
        }
        
        item.innerHTML = content;
        
        // Event listeners para el item
        item.addEventListener('mouseenter', () => {
            this.selectedIndex = index;
            this.updateSelection();
        });
        
        item.addEventListener('click', () => {
            this.selectSuggestion(suggestion);
        });
        
        return item;
    }
    
    handleKeydown(e) {
        if (!this.suggestionsContainer.style.display || this.suggestionsContainer.style.display === 'none') {
            return;
        }
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.currentSuggestions.length - 1);
                this.updateSelection();
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                this.updateSelection();
                break;
                
            case 'Enter':
                e.preventDefault();
                if (this.selectedIndex >= 0) {
                    this.selectSuggestion(this.currentSuggestions[this.selectedIndex]);
                }
                break;
                
            case 'Escape':
                this.hideSuggestions();
                break;
        }
    }
    
    updateSelection() {
        const items = this.suggestionsContainer.querySelectorAll('.autocomplete-item');
        
        items.forEach((item, index) => {
            if (index === this.selectedIndex) {
                item.style.backgroundColor = '#3b82f6';
                item.style.color = 'white';
            } else {
                item.style.backgroundColor = '';
                item.style.color = '';
            }
        });
    }
    
    selectSuggestion(suggestion) {
        this.input.value = suggestion.value;
        
        // Disparar evento personalizado con los datos
        const event = new CustomEvent('autocomplete-select', {
            detail: {
                value: suggestion.value,
                data: suggestion.data
            }
        });
        this.input.dispatchEvent(event);
        
        this.hideSuggestions();
    }
    
    hideSuggestions() {
        this.suggestionsContainer.style.display = 'none';
        this.selectedIndex = -1;
    }
    
    handleBlur(e) {
        // Delay para permitir clicks en sugerencias
        setTimeout(() => {
            this.hideSuggestions();
        }, 150);
    }
    
    handleFocus(e) {
        if (this.input.value.length >= this.options.minLength && this.currentSuggestions.length > 0) {
            this.showSuggestions();
        }
    }
}

// Función helper para inicializar autocompletado fácilmente
function initAutocomplete(inputId, apiUrl, options = {}) {
    const input = document.getElementById(inputId);
    if (input) {
        return new AutocompleteSystem(input, apiUrl, options);
    }
    return null;
}

// CSS adicional para mejorar la apariencia
const autocompleteCSS = `
<style>
.autocomplete-suggestions {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.autocomplete-item:hover {
    background-color: #f3f4f6 !important;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-loading {
    padding: 12px;
    text-align: center;
    color: #6b7280;
    font-style: italic;
}

.autocomplete-no-results {
    padding: 12px;
    text-align: center;
    color: #6b7280;
    font-style: italic;
}
</style>
`;

// Agregar CSS al head si no existe
if (!document.querySelector('#autocomplete-css')) {
    const style = document.createElement('style');
    style.id = 'autocomplete-css';
    style.textContent = autocompleteCSS;
    document.head.appendChild(style);
}
