document.addEventListener('DOMContentLoaded', function() {
    const authForm = document.querySelector('.auth-form');
    const submitBtn = authForm.querySelector('button[type="submit"]');
    
    authForm.addEventListener('submit', function() {
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Please wait...';
    });
    
    // Clear error when user starts typing
    const inputs = authForm.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                const errorMessage = this.parentNode.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        });
    });
});