/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included in your base template
 * and will serve as the entry point for your JavaScript code.
 */

// Import any CSS you want (example)
// import './styles/app.css';

// start the Stimulus application
// import './bootstrap';

import UserFormValidator from './js/user-forms';

// Your custom JavaScript code will go here
console.log('Test 1 - Logging outside DOMContentLoaded');

document.addEventListener('DOMContentLoaded', () => {
    // Vérifier si nous sommes sur la page d'inscription
    const isRegistrationPage = document.querySelector('form[name="registration_form"]') !== null;

    if (!isRegistrationPage) {
        return; // Ne pas exécuter le code si ce n'est pas la page d'inscription
    }

    console.log('Test 2 - Logging inside DOMContentLoaded');

    // Test simple d'interaction DOM
    const body = document.querySelector('body');
    if (body) {
        console.log('Test 3 - Body element found');
    }

    // Initialiser le validateur de formulaire
    new UserFormValidator();

    // Ajouter des effets visuels aux formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            // Ajouter une classe pour l'animation de focus
            input.addEventListener('focus', () => {
                input.closest('.form-group')?.classList.add('focused');
            });

            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.closest('.form-group')?.classList.remove('focused');
                }
            });
        });
    });

    // Test simple de validation d'email
    const emailInput = document.querySelector('input[type="email"]');
    console.log('Email input found:', emailInput);

    if (emailInput) {
        emailInput.addEventListener('input', (e) => {
            const email = e.target.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = emailRegex.test(email);

            const feedbackDiv = emailInput.nextElementSibling || (() => {
                const div = document.createElement('div');
                emailInput.parentNode.insertBefore(div, emailInput.nextSibling);
                return div;
            })();

            if (isValid) {
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('is-valid');
                feedbackDiv.className = 'valid-feedback';
                feedbackDiv.textContent = 'Parfait !';
            } else {
                emailInput.classList.remove('is-valid');
                emailInput.classList.add('is-invalid');
                feedbackDiv.className = 'invalid-feedback';
                feedbackDiv.textContent = 'Veuillez entrer une adresse email valide';
            }
        });
    }

    // Validation mot de passe avec indicateur de force
    const passwordInput = document.querySelector('input[type="password"]');
    if (passwordInput) {
        // Créer l'indicateur de force
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength mt-2';
        strengthIndicator.style.height = '5px';
        strengthIndicator.style.transition = 'all 0.3s ease';
        strengthIndicator.style.borderRadius = '3px';

        // Créer le texte de force
        const strengthText = document.createElement('div');
        strengthText.className = 'password-strength-text small mt-1';

        // Insérer les éléments après l'input
        passwordInput.parentNode.insertBefore(strengthIndicator, passwordInput.nextSibling);
        passwordInput.parentNode.insertBefore(strengthText, strengthIndicator.nextSibling);

        passwordInput.addEventListener('input', (e) => {
            const password = e.target.value;
            const strength = checkPasswordStrength(password);

            // Mettre à jour l'indicateur visuel
            updateStrengthIndicator(strength, strengthIndicator, strengthText);

            // Feedback de validation
            const feedbackDiv = strengthText.nextElementSibling || (() => {
                const div = document.createElement('div');
                passwordInput.parentNode.insertBefore(div, strengthText.nextSibling);
                return div;
            })();

            if (password.length >= 8) {
                passwordInput.classList.remove('is-invalid');
                passwordInput.classList.add('is-valid');
                feedbackDiv.className = 'valid-feedback';
                feedbackDiv.textContent = strength.message;
            } else {
                passwordInput.classList.remove('is-valid');
                passwordInput.classList.add('is-invalid');
                feedbackDiv.className = 'invalid-feedback';
                feedbackDiv.textContent = 'Le mot de passe doit contenir au moins 8 caractères';
            }
        });
    }
});

function checkPasswordStrength(password) {
    // Critères de force
    const hasLowerCase = /[a-z]/.test(password);
    const hasUpperCase = /[A-Z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const length = password.length;

    // Calculer le score
    let score = 0;
    if (length >= 8) score++;
    if (length >= 12) score++;
    if (hasLowerCase) score++;
    if (hasUpperCase) score++;
    if (hasNumbers) score++;
    if (hasSpecialChars) score++;

    // Déterminer le niveau de force
    if (score === 0 || length < 8) {
        return {
            strength: 'weak',
            color: '#dc3545', // rouge
            width: '33%',
            message: 'Mot de passe faible'
        };
    } else if (score <= 3) {
        return {
            strength: 'medium',
            color: '#ffc107', // jaune
            width: '66%',
            message: 'Mot de passe moyen'
        };
    } else {
        return {
            strength: 'strong',
            color: '#28a745', // vert
            width: '100%',
            message: 'Mot de passe fort'
        };
    }
}

function updateStrengthIndicator(strength, indicator, textElement) {
    indicator.style.width = strength.width;
    indicator.style.backgroundColor = strength.color;
    textElement.textContent = strength.message;
    textElement.style.color = strength.color;
}
