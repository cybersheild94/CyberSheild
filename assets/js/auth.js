/**
 * CYBER SHIELD - Client-side Auth Validation
 * File: assets/js/auth.js
 * BCA Final Year Project
 */

document.addEventListener('DOMContentLoaded', () => {
    // Password Strength Meter for Signup
    const passwordInput = document.getElementById('signupPassword');
    const strengthBar = document.getElementById('passwordStrengthFill');
    const strengthText = document.getElementById('passwordStrengthText');

    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            let score = 0;

            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            switch (score) {
                case 0:
                case 1:
                    strengthBar.style.width = '25%';
                    strengthBar.style.backgroundColor = '#ef4444';
                    strengthText.textContent = 'Weak (Must be min 8 chars with mixed symbols)';
                    strengthText.className = 'text-danger small mt-1';
                    break;
                case 2:
                    strengthBar.style.width = '50%';
                    strengthBar.style.backgroundColor = '#f59e0b';
                    strengthText.textContent = 'Fair (Add uppercase letters and numbers)';
                    strengthText.className = 'text-warning small mt-1';
                    break;
                case 3:
                    strengthBar.style.width = '75%';
                    strengthBar.style.backgroundColor = '#3b82f6';
                    strengthText.textContent = 'Good (Add a special symbol like !@#)';
                    strengthText.className = 'text-info small mt-1';
                    break;
                case 4:
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = '#10b981';
                    strengthText.textContent = 'Strong (Excellent complexity)';
                    strengthText.className = 'text-success small mt-1';
                    break;
            }
        });
    }

    // Confirm Password Matching Validation
    const confirmInput = document.getElementById('signupConfirmPassword');
    if (confirmInput && passwordInput) {
        const form = confirmInput.closest('form');
        form.addEventListener('submit', (e) => {
            if (passwordInput.value !== confirmInput.value) {
                e.preventDefault();
                alert('Passwords do not match. Please re-enter your password.');
                confirmInput.focus();
            }
        });
    }
});
