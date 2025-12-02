/**
 * PAGTREM - JavaScript Principal
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Tem certeza que deseja excluir?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Form validation feedback
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc2626';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });
    });
    
    // Toggle password visibility
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('data-target'));
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🙈';
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            }
        });
    });
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const headerNav = document.querySelector('.header-nav');
    if (menuToggle && headerNav) {
        menuToggle.addEventListener('click', function() {
            headerNav.classList.toggle('active');
        });
    }
    
});

/**
 * Função para fechar modal
 */
function closeModal() {
    const modal = document.querySelector('.modal-backdrop');
    if (modal) {
        modal.remove();
    }
}

/**
 * Função para formatar data BR
 */
function formatDateBR(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

