// Variables globales
let currentForm = 'login';
let isSubmitting = false;

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    setupRealTimeValidation();
    checkUrlParams();
});

// Configuration des écouteurs d'événements
function initializeEventListeners() {
    // Écouteurs pour les formulaires
    const loginForm = document.querySelector('#loginForm .auth-form');
    const registerForm = document.querySelector('#registerForm .auth-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegisterSubmit);
    }
    
    // Validation en temps réel
    setupRealTimeValidation();
    
    // Gestion des touches
    document.addEventListener('keydown', handleKeyPress);
}

// Navigation entre les formulaires
function showLoginForm() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    registerForm.classList.add('hidden');
    loginForm.classList.remove('hidden');
    currentForm = 'login';
    
    // Animation
    loginForm.style.animation = 'fadeIn 0.5s ease-out';
    
    // Focus sur le premier champ
    setTimeout(() => {
        const firstInput = loginForm.querySelector('input[type="text"], input[type="email"]');
        if (firstInput) firstInput.focus();
    }, 100);
}

function showRegisterForm() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    loginForm.classList.add('hidden');
    registerForm.classList.remove('hidden');
    currentForm = 'register';
    
    // Animation
    registerForm.style.animation = 'fadeIn 0.5s ease-out';
    
    // Focus sur le premier champ
    setTimeout(() => {
        const firstInput = registerForm.querySelector('input[type="text"]');
        if (firstInput) firstInput.focus();
    }, 100);
}

// Gestion de la soumission du formulaire de connexion
async function handleLoginSubmit(event) {
    event.preventDefault();
    
    if (isSubmitting) return;
    
    const form = event.target;
    const formData = new FormData(form);
    const email = formData.get('email');
    const password = formData.get('password');
    
    // Validation côté client
    if (!validateLoginForm(email, password)) {
        return;
    }
    
    // Désactiver le bouton et afficher le chargement
    setSubmitState(form, true);
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Connexion réussie ! Redirection...', 'success');
            setTimeout(() => {
                window.location.href = result.redirect || 'dashboard.php';
            }, 1500);
        } else {
            showNotification(result.message || 'Erreur de connexion', 'error');
            setSubmitState(form, false);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion au serveur', 'error');
        setSubmitState(form, false);
    }
}

// Gestion de la soumission du formulaire d'inscription
async function handleRegisterSubmit(event) {
    event.preventDefault();
    
    if (isSubmitting) return;
    
    const form = event.target;
    const formData = new FormData(form);
    const username = formData.get('username');
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    
    // Validation côté client
    if (!validateRegisterForm(username, email, password, confirmPassword)) {
        return;
    }
    
    // Désactiver le bouton et afficher le chargement
    setSubmitState(form, true);
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Compte créé avec succès ! Vous pouvez maintenant vous connecter.', 'success');
            setTimeout(() => {
                showLoginForm();
                // Pré-remplir l'email
                document.getElementById('login-email').value = email;
            }, 2000);
        } else {
            showNotification(result.message || 'Erreur lors de la création du compte', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion au serveur', 'error');
    } finally {
        setSubmitState(form, false);
    }
}

// Validation du formulaire de connexion
function validateLoginForm(email, password) {
    let isValid = true;
    
    // Validation email
    if (!email || email.trim() === '') {
        showFieldError('login-email', 'L\'email ou nom d\'utilisateur est requis');
        isValid = false;
    } else {
        hideFieldError('login-email');
    }
    
    // Validation mot de passe
    if (!password || password.length < 6) {
        showFieldError('login-password', 'Le mot de passe doit contenir au moins 6 caractères');
        isValid = false;
    } else {
        hideFieldError('login-password');
    }
    
    return isValid;
}

// Validation du formulaire d'inscription
function validateRegisterForm(username, email, password, confirmPassword) {
    let isValid = true;
    
    // Validation nom d'utilisateur
    if (!username || username.trim().length < 3) {
        showFieldError('register-username', 'Le nom d\'utilisateur doit contenir au moins 3 caractères');
        isValid = false;
    } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        showFieldError('register-username', 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres et underscores');
        isValid = false;
    } else {
        hideFieldError('register-username');
    }
    
    // Validation email
    if (!email || !isValidEmail(email)) {
        showFieldError('register-email', 'Veuillez entrer une adresse email valide');
        isValid = false;
    } else {
        hideFieldError('register-email');
    }
    
    // Validation mot de passe
    if (!password || password.length < 8) {
        showFieldError('register-password', 'Le mot de passe doit contenir au moins 8 caractères');
        isValid = false;
    } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
        showFieldError('register-password', 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre');
        isValid = false;
    } else {
        hideFieldError('register-password');
    }
    
    // Validation confirmation mot de passe
    if (password !== confirmPassword) {
        showFieldError('register-confirm-password', 'Les mots de passe ne correspondent pas');
        isValid = false;
    } else {
        hideFieldError('register-confirm-password');
    }
    
    return isValid;
}

// Validation en temps réel
function setupRealTimeValidation() {
    // Validation email en temps réel
    const emailInputs = document.querySelectorAll('input[type="email"], #login-email');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isValidEmail(this.value)) {
                showFieldError(this.id, 'Format d\'email invalide');
            } else {
                hideFieldError(this.id);
            }
        });
    });
    
    // Validation mot de passe en temps réel
    const passwordInput = document.getElementById('register-password');
    const confirmPasswordInput = document.getElementById('register-confirm-password');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            if (password.length > 0 && password.length < 8) {
                showFieldError(this.id, 'Au moins 8 caractères requis');
            } else {
                hideFieldError(this.id);
            }
            
            // Vérifier la confirmation si elle existe
            if (confirmPasswordInput && confirmPasswordInput.value) {
                if (password !== confirmPasswordInput.value) {
                    showFieldError('register-confirm-password', 'Les mots de passe ne correspondent pas');
                } else {
                    hideFieldError('register-confirm-password');
                }
            }
        });
    }
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput ? passwordInput.value : '';
            if (this.value && this.value !== password) {
                showFieldError(this.id, 'Les mots de passe ne correspondent pas');
            } else {
                hideFieldError(this.id);
            }
        });
    }
    
    // Validation nom d'utilisateur
    const usernameInput = document.getElementById('register-username');
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const username = this.value;
            if (username.length > 0 && username.length < 3) {
                showFieldError(this.id, 'Au moins 3 caractères requis');
            } else if (username && !/^[a-zA-Z0-9_]+$/.test(username)) {
                showFieldError(this.id, 'Seuls les lettres, chiffres et _ sont autorisés');
            } else {
                hideFieldError(this.id);
            }
        });
    }
}

// Utilitaires de validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Gestion des erreurs de champs
function showFieldError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + '-error');
    const inputElement = document.getElementById(fieldId);
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
    
    if (inputElement) {
        inputElement.style.borderColor = 'var(--twitch-error)';
    }
}

function hideFieldError(fieldId) {
    const errorElement = document.getElementById(fieldId + '-error');
    const inputElement = document.getElementById(fieldId);
    
    if (errorElement) {
        errorElement.classList.remove('show');
    }
    
    if (inputElement) {
        inputElement.style.borderColor = '';
    }
}

// Gestion de l'état de soumission
function setSubmitState(form, isSubmitting) {
    const submitButton = form.querySelector('button[type="submit"]');
    const inputs = form.querySelectorAll('input');
    
    if (isSubmitting) {
        submitButton.disabled = true;
        submitButton.textContent = 'Chargement...';
        submitButton.classList.add('loading');
        inputs.forEach(input => input.disabled = true);
    } else {
        submitButton.disabled = false;
        submitButton.textContent = currentForm === 'login' ? 'Se connecter' : 'Créer mon compte';
        submitButton.classList.remove('loading');
        inputs.forEach(input => input.disabled = false);
    }
    
    window.isSubmitting = isSubmitting;
}

// Système de notifications
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    const notificationText = notification.querySelector('.notification-text');
    
    notificationText.textContent = message;
    notification.className = `notification ${type}`;
    notification.classList.add('show');
    
    // Auto-hide après 5 secondes
    setTimeout(() => {
        hideNotification();
    }, 5000);
}

function hideNotification() {
    const notification = document.getElementById('notification');
    notification.classList.remove('show');
}

// Gestion des touches clavier
function handleKeyPress(event) {
    // Échapper pour fermer les notifications
    if (event.key === 'Escape') {
        hideNotification();
    }
    
    // Entrée pour soumettre le formulaire visible
    if (event.key === 'Enter' && event.target.tagName !== 'BUTTON') {
        const visibleForm = document.querySelector('.form-container:not(.hidden) .auth-form');
        if (visibleForm && !isSubmitting) {
            const submitButton = visibleForm.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.disabled) {
                submitButton.click();
            }
        }
    }
}

// Vérification des paramètres URL
function checkUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    const message = urlParams.get('message');
    const type = urlParams.get('type');
    
    if (action === 'register') {
        showRegisterForm();
    }
    
    if (message) {
        showNotification(decodeURIComponent(message), type || 'info');
        // Nettoyer l'URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

// Fonctions utilitaires pour l'accessibilité
function setFocusToFirstError() {
    const firstError = document.querySelector('.error-message.show');
    if (firstError) {
        const fieldId = firstError.id.replace('-error', '');
        const field = document.getElementById(fieldId);
        if (field) {
            field.focus();
        }
    }
}

// Gestion de la connexion automatique (Remember me)
function handleRememberMe() {
    const rememberCheckbox = document.querySelector('input[name="remember"]');
    if (rememberCheckbox && rememberCheckbox.checked) {
        // Cette fonctionnalité sera gérée côté serveur
        console.log('Remember me activé');
    }
}

// Préchargement des ressources
function preloadResources() {
    // Précharger les images ou autres ressources si nécessaire
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = 'dashboard.php';
    document.head.appendChild(link);
}

// Initialiser le préchargement
setTimeout(preloadResources, 2000);