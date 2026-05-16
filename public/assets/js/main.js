// assets/js/main.js - Pure Hybrid Execution Engine
document.addEventListener('DOMContentLoaded', function () {

    // 1. Unified AJAX Event Middleware Form Submit Handler
    const handleFormSubmit = (formId, alertId) => {
        const form = document.getElementById(formId);
        const alertBox = document.getElementById(alertId);

        if (!form || !alertBox) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            alertBox.classList.add('d-none'); // Clear existing error state

            // Real-time local verification mapping for registration matching fields
            if (formId === 'ajaxRegisterForm') {
                const pass = form.querySelector('input[name="password"]').value;
                const confirm = form.querySelector('input[name="confirm_password"]').value;
                if (pass !== confirm) {
                    alertBox.textContent = "Validation mismatch: Passwords do not match!";
                    alertBox.classList.remove('d-none');
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
                    throw new Error('Server returned unsafe response boundaries.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Forward context directly using structural destination maps
                    window.location.href = data.redirect || window.BASE_URL || '/';
                } else {
                    alertBox.textContent = data.message || 'An unhandled authentication event failed.';
                    alertBox.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error Exception Pipeline:', error);
                alertBox.textContent = 'A transmission or validation structure error occurred.';
                alertBox.classList.remove('d-none');
            });
        });
    };

    // Instantiate form intercept trackers matching the target element structures
    handleFormSubmit('ajaxLoginForm', 'loginAlert');
    handleFormSubmit('ajaxRegisterForm', 'registerAlert');
    handleFormSubmit('ajaxEmployeeForm', 'employeeAlert');

    // 2. URL Query State Router Layer (Brought in from Group 2 parameters)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openModal') === '1') {
        const authModalEl = document.getElementById('authModal');
        if (authModalEl) {
            const bootstrapModal = new bootstrap.Modal(authModalEl);
            bootstrapModal.show();
            
            // Switch tab focus seamlessly if employee query string is appended
            if (urlParams.get('tab') === 'employee') {
                const empTabBtn = document.getElementById('emp-tab-btn');
                if (empTabBtn) {
                    empTabBtn.click();
                }
            }
        }
    }
});