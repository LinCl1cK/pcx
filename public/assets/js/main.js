// assets/js/main.js - Unified Authentication Notification Pipeline
document.addEventListener('DOMContentLoaded', function () {

    /**
     * Helper function to spawn a dynamic Bootstrap Toast Notification on the fly
     * Matches the precise look and feel defined in customer_header.php
     */
    const showToastNotification = (type, message) => {
        // Create a unique wrapper container for toasts if it doesn't exist yet
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '1100';
            toastContainer.style.marginTop = '85px';
            document.body.appendChild(toastContainer);
        }

        const bgClass = type === 'danger' ? 'bg-danger' : (type === 'success' ? 'bg-success' : 'bg-dark');
        const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

        const toastHtml = `
            <div class="toast align-items-center text-white ${bgClass} border-0 shadow" role="alert" aria-live=\"assertive\" aria-atomic=\"true\" data-bs-delay=\"4000\">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="bi ${iconClass} fs-5"></i>
                        <div>${message}</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        // Turn the string markup into a DOM element node
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = toastHtml.trim();
        const toastElement = tempDiv.firstChild;

        toastContainer.appendChild(toastElement);

        // Initialize and fire the Bootstrap Toast Instance
        if (typeof bootstrap !== 'undefined') {
            const bsToast = new bootstrap.Toast(toastElement);
            bsToast.show();

            // Automatically clean up the element from the DOM after it hides
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        } else {
            // Fallback console log if bootstrap library hasn't loaded yet
            console.warn('Bootstrap is missing. Fallback fallback message:', message);
        }
    };

    // 1. AJAX Form Submission Middleware Interceptor
    const handleFormSubmit = (formId, fallbackAlertId) => {
        const form = document.getElementById(formId);
        const fallbackAlert = document.getElementById(fallbackAlertId);

        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (fallbackAlert) fallbackAlert.classList.add('d-none');

            // Registration specific client side password check validation loop
            if (formId === 'ajaxRegisterForm') {
                const pass = form.querySelector('input[name="password"]').value;
                const confirm = form.querySelector('input[name="confirm_password"]').value;
                if (pass !== confirm) {
                    showToastNotification('danger', 'Validation mismatch: Passwords do not match!');
                    return;
                }
            }

            const formData = new FormData(form);

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server returned an unstable response code boundary.');
                }
                return response.json();
            })
            .then(data => {
                // FIXED: Changed data.status === 'success' to data.success
                if (data.success) {
                    // Redirects directly to the path sent back dynamically by the controller logic
                    window.location.href = data.redirect || window.BASE_URL || '/';
                } else {
                    // Spawn a dynamic toast matching your notification styling engine
                    showToastNotification('danger', data.message || 'Authentication parameters mismatch.');
                }
            })
            .catch(error => {
                console.error('Submission processing error:', error);
                showToastNotification('danger', 'A system transmission or verification structure error occurred.');
            });
        });
    };

    // Instantiate form submission structural intercept hooks
    handleFormSubmit('ajaxLoginForm', 'loginAlert');
    handleFormSubmit('ajaxRegisterForm', 'registerAlert');

    // 2. URL Query State Router Layer
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openModal') === '1') {
        const authModalEl = document.getElementById('authModal');
        if (authModalEl) {
            const bootstrapModal = new bootstrap.Modal(authModalEl);
            bootstrapModal.show();
        }
    }
});