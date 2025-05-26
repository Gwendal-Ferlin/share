class UserFormValidator {
    constructor() {
        this.form = document.querySelector('form[name="registration_form"]');
        console.log('Form found:', this.form);

        // Trouver tous les champs du formulaire pour le débogage
        const allFormInputs = document.querySelectorAll('input');
        console.log('All form inputs:', allFormInputs);

        this.emailInput = document.querySelector('#registration_form_email');
        console.log('Email input found:', this.emailInput);

        this.passwordInput = document.querySelector('#registration_form_plainPassword');
        console.log('Password input found:', this.passwordInput);

        this.nameInput = document.querySelector('#registration_form_name');
        console.log('Name input found:', this.nameInput);

        this.prenomInput = document.querySelector('#registration_form_prenom');
        console.log('Prenom input found:', this.prenomInput);

        if (this.form) {
            this.initializeValidation();
        } else {
            console.log('No registration form found on this page');
        }
    }

    initializeValidation() {
        // Validation email en temps réel
        if (this.emailInput) {
            console.log('Adding email validation listener');
            this.emailInput.addEventListener('input', (e) => {
                console.log('Email input event triggered');
                this.validateEmail();
            });
        }

        // Validation mot de passe en temps réel
        if (this.passwordInput) {
            this.createPasswordStrengthDiv();
            this.passwordInput.addEventListener('input', () => this.validatePassword());
        }

        // Validation nom en temps réel
        if (this.nameInput) {
            this.nameInput.addEventListener('input', () => this.validateName());
        }

        // Validation prénom en temps réel
        if (this.prenomInput) {
            this.prenomInput.addEventListener('input', () => this.validatePrenom());
        }

        // Validation finale avant envoi
        this.form.addEventListener('submit', (e) => this.validateForm(e));
    }

    createPasswordStrengthDiv() {
        // Ajoute un div pour la force du mot de passe juste après le champ si pas déjà présent
        let next = this.passwordInput.nextElementSibling;
        let found = false;
        while (next) {
            if (next.classList && next.classList.contains('password-strength-text')) {
                found = true;
                break;
            }
            next = next.nextElementSibling;
        }
        if (!found) {
            const strengthDiv = document.createElement('div');
            strengthDiv.className = 'password-strength-text small mt-1';
            strengthDiv.style.display = 'none';
            this.passwordInput.parentNode.insertBefore(strengthDiv, this.passwordInput.nextSibling);
        }
    }

    validateEmail() {
        console.log('Validating email...');
        const email = this.emailInput.value;
        console.log('Email value:', email);

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(email);
        console.log('Email is valid:', isValid);

        this.updateFieldValidation(this.emailInput, isValid,
            'Veuillez entrer une adresse email valide');
    }

    validatePassword() {

        const password = this.passwordInput.value;
        const hasMinLength = password.length >= 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);

        let strength = '';
        let color = '';
        let isValid = false;

        if (!hasMinLength) {
            strength = 'Mot de passe trop court';
            color = 'red';
        } else if (hasMinLength && ((hasUpperCase && hasLowerCase) || (hasUpperCase && hasNumber) || (hasLowerCase && hasNumber))) {
            strength = 'Mot de passe moyen';
            color = 'rgb(255, 193, 7)';
        }
        if (hasMinLength && hasUpperCase && hasLowerCase && hasNumber) {
            strength = 'Mot de passe fort';
            color = 'green';
            isValid = true;
        }

        // Affichage force du mot de passe
        let strengthDiv = this.passwordInput.parentNode.querySelector('.password-strength-text');
        if (!strengthDiv) {
            strengthDiv = document.createElement('div');
            strengthDiv.className = 'password-strength-text small mt-1';
            this.passwordInput.parentNode.insertBefore(strengthDiv, this.passwordInput.nextSibling);
        }
        if (password.length > 0) {
            strengthDiv.textContent = strength;
            strengthDiv.style.color = color;
            strengthDiv.style.display = 'block';
        } else {
            strengthDiv.textContent = '';
            strengthDiv.style.display = 'none';
        }

        // Feedback général (Parfait ! uniquement si tout est OK)
        let message = '';
        if (!isValid) {
            message = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre';
        }
        this.updateFieldValidation(this.passwordInput, isValid, message);
    }

    validateName() {
        const name = this.nameInput.value;
        const isValid = name.length >= 2;

        this.updateFieldValidation(this.nameInput, isValid,
            'Le nom doit contenir au moins 2 caractères');
    }

    validatePrenom() {
        const prenom = this.prenomInput.value;
        const isValid = prenom.length >= 2;

        this.updateFieldValidation(this.prenomInput, isValid,
            'Le prénom doit contenir au moins 2 caractères');
    }

    updateFieldValidation(input, isValid, errorMessage) {
        console.log('Updating field validation:', input, isValid);

        if (!input) {
            console.error('Input element is null');
            return;
        }

        const feedbackDiv = this.getOrCreateFeedbackDiv(input);
        console.log('Feedback div:', feedbackDiv);

        const currentState = input.classList.contains('is-valid');
        console.log('Current state:', currentState, 'New state:', isValid);

        // Ne rien faire si l'état n'a pas changé
        if ((isValid && currentState) || (!isValid && !currentState)) {
            console.log('State unchanged, skipping update');
            return;
        }

        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedbackDiv.classList.remove('invalid-feedback');
            feedbackDiv.classList.add('valid-feedback');
            feedbackDiv.textContent = 'Parfait !';
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            feedbackDiv.classList.remove('valid-feedback');
            feedbackDiv.classList.add('invalid-feedback');
            feedbackDiv.textContent = errorMessage;
        }

        console.log('Field updated:', input.classList);
    }

    getOrCreateFeedbackDiv(input) {
        let feedbackDiv = input.nextElementSibling;
        console.log('Existing feedback div:', feedbackDiv);

        if (!feedbackDiv || (!feedbackDiv.classList.contains('invalid-feedback') && !feedbackDiv.classList.contains('valid-feedback'))) {
            console.log('Creating new feedback div');
            feedbackDiv = document.createElement('div');
            feedbackDiv.classList.add('invalid-feedback');
            input.parentNode.insertBefore(feedbackDiv, input.nextSibling);
        }
        return feedbackDiv;
    }

    validateForm(e) {
        this.validateEmail();
        this.validatePassword();
        this.validateName();
        this.validatePrenom();

        const hasErrors = this.form.querySelectorAll('.is-invalid').length > 0;
        if (hasErrors) {
            e.preventDefault();
        }
    }
}

export default UserFormValidator; 