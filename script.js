// ===== VARIABLES GLOBALES =====
let currentCategory = 'all';
let searchResults = [];
let currentSearchTerm = '';
let isSearchActive = false;
let currentForm = 'login';
let isSubmitting = false;

// ===== DONNÉES FAQ =====
const faqData = [
    {
        id: 1,
        category: 'streaming',
        question: 'Comment commencer à streamer sur Twitch ?',
        answer: 'Pour commencer à streamer sur Twitch : Créez un compte Twitch, Téléchargez OBS Studio (gratuit), Configurez votre stream key dans OBS, Choisissez votre jeu ou contenu, Lancez votre premier stream !',
        keywords: ['commencer', 'streamer', 'twitch', 'obs', 'premier', 'stream']
    },
    {
        id: 2,
        category: 'technical',
        question: 'Quels sont les meilleurs paramètres OBS pour débuter ?',
        answer: 'Paramètres recommandés pour débuter : Résolution 1920x1080 ou 1280x720, FPS 30 ou 60 selon votre connexion, Bitrate 2500-6000 kbps, Encoder x264 ou NVENC si vous avez une carte NVIDIA',
        keywords: ['obs', 'paramètres', 'résolution', 'fps', 'bitrate', 'encoder', 'nvenc']
    },
    {
        id: 3,
        category: 'monetisation',
        question: 'Comment devenir partenaire Twitch ?',
        answer: 'Critères pour devenir partenaire : Streamer au moins 25 heures sur 30 jours, Streamer sur au moins 12 jours différents, Avoir une moyenne de 75 viewers, Respecter les conditions d\'utilisation, Être en conformité avec les directives communautaires',
        keywords: ['partenaire', 'twitch', 'critères', 'viewers', 'heures', 'stream']
    },
    {
        id: 4,
        category: 'streaming',
        question: 'Comment améliorer la qualité de mon stream ?',
        answer: 'Conseils pour améliorer votre stream : Investissez dans un bon microphone, Éclairez bien votre visage, Créez des overlays attrayants, Interagissez avec votre chat, Streamer régulièrement, Partagez vos streams sur les réseaux sociaux',
        keywords: ['qualité', 'stream', 'microphone', 'éclairage', 'overlay', 'chat', 'réseaux sociaux']
    },
    {
        id: 5,
        category: 'technical',
        question: 'Mon stream lag, que faire ?',
        answer: 'Solutions pour réduire le lag : Vérifiez votre connexion internet (upload minimum 3 Mbps), Fermez les applications inutiles, Réduisez la résolution ou le FPS, Changez de serveur Twitch, Utilisez un encodeur matériel (NVENC/QuickSync)',
        keywords: ['lag', 'stream', 'connexion', 'internet', 'résolution', 'fps', 'serveur', 'encodeur']
    },
    {
        id: 6,
        category: 'community',
        question: 'Comment créer une communauté engagée ?',
        answer: 'Stratégies pour développer votre communauté : Soyez authentique et vous-même, Répondez aux messages du chat, Créez des événements réguliers, Utilisez Discord pour rester connecté, Collaborez avec d\'autres streamers, Créez du contenu unique',
        keywords: ['communauté', 'engagée', 'authentique', 'chat', 'discord', 'collaboration', 'contenu']
    },
    {
        id: 7,
        category: 'monetisation',
        question: 'Comment gagner de l\'argent en streamant ?',
        answer: 'Moyens de monétiser votre stream : Abonnements revenus mensuels récurrents, Bits pourboires virtuels, Donations via PayPal ou autres plateformes, Partenariats sponsors et collaborations, Ventes merchandising et produits',
        keywords: ['argent', 'streamer', 'abonnements', 'bits', 'donations', 'partenariats', 'merchandising']
    },
    {
        id: 8,
        category: 'technical',
        question: 'Quel équipement recommandez-vous pour débuter ?',
        answer: 'Équipement essentiel pour débuter : Microphone Blue Yeti ou Audio-Technica AT2020, Webcam Logitech C920 ou C922, Éclairage Anneau lumineux LED, PC Processeur quad-core minimum, Internet Connexion stable avec bon upload',
        keywords: ['équipement', 'microphone', 'webcam', 'éclairage', 'pc', 'internet', 'débuter']
    }
];

// ===== INITIALISATION =====
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeFAQ();
    initializeSearch();
    setupRealTimeValidation();
    checkUrlParams();
    preloadResources();
});

// ===== GESTION DES ÉVÉNEMENTS =====
function initializeEventListeners() {
    // Navigation
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const target = this.getAttribute('href');
            
            // Si c'est un lien externe (comme login.php), laisser le comportement par défaut
            if (target.startsWith('http') || target.includes('.php') || target.includes('.html')) {
                return; // Laisser le comportement par défaut
            }
            
            e.preventDefault();
            scrollToSection(target);
            updateActiveNavLink(this);
        });
    });

    // Boutons de catégorie FAQ
    const categoryBtns = document.querySelectorAll('.category-btn');
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            filterFAQByCategory(category);
            updateActiveCategoryBtn(this);
        });
    });

    // Questions FAQ
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', function() {
            toggleFAQItem(item);
        });
    });

    // Suggestions de recherche
    const suggestionTags = document.querySelectorAll('.suggestion-tag');
    suggestionTags.forEach(tag => {
        tag.addEventListener('click', function() {
            const searchTerm = this.getAttribute('data-search');
            document.getElementById('searchInput').value = searchTerm;
            performSearch(searchTerm);
        });
    });

    // Boutons de la section hero
    const btnPrimary = document.querySelector('.btn-primary');
    const btnSecondary = document.querySelector('.btn-secondary');
    
    if (btnPrimary) {
        btnPrimary.addEventListener('click', function() {
            scrollToSection('#recherche');
            document.getElementById('searchInput').focus();
        });
    }
    
    if (btnSecondary) {
        btnSecondary.addEventListener('click', function() {
            scrollToSection('#faq');
        });
    }

    // Écouteurs pour les formulaires d'authentification
    const loginForm = document.querySelector('#loginForm .auth-form');
    const registerForm = document.querySelector('#registerForm .auth-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegisterSubmit);
    }
    
    // Gestion des touches
    document.addEventListener('keydown', handleKeyPress);
}

// ===== FONCTIONS FAQ =====
function initializeFAQ() {
    // Animation d'apparition des éléments FAQ
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.faq-item').forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(item);
    });
}

function filterFAQByCategory(category) {
    currentCategory = category;
    
    // Si une recherche est active, on ne filtre pas par catégorie
    if (isSearchActive && currentSearchTerm) {
        performSearch(currentSearchTerm);
        return;
    }
    
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        
        if (category === 'all' || itemCategory === category) {
            item.style.display = 'block';
            item.style.animation = 'fadeInUp 0.6s ease forwards';
        } else {
            item.style.display = 'none';
        }
    });
}

function updateActiveCategoryBtn(activeBtn) {
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    activeBtn.classList.add('active');
}

function toggleFAQItem(item) {
    const isActive = item.classList.contains('active');
    
    // Fermer tous les autres éléments
    document.querySelectorAll('.faq-item').forEach(otherItem => {
        if (otherItem !== item) {
            otherItem.classList.remove('active');
        }
    });
    
    // Toggle l'élément actuel
    item.classList.toggle('active');
}

// ===== FONCTIONS DE RECHERCHE =====
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');

    if (!searchInput) return;

    // Recherche en temps réel
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        if (searchTerm.length > 2) {
            debouncedSearch(searchTerm);
        } else if (searchTerm.length === 0) {
            showAllFAQ();
        }
    });

    // Recherche au clic
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                performSearch(searchTerm);
            }
        });
    }

    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
                performSearch(searchTerm);
            }
        }
    });
}

function performSearch(searchTerm) {
    currentSearchTerm = searchTerm;
    isSearchActive = true;
    
    const results = faqData.filter(item => {
        const searchLower = searchTerm.toLowerCase();
        return item.question.toLowerCase().includes(searchLower) ||
               item.answer.toLowerCase().includes(searchLower) ||
               item.keywords.some(keyword => keyword.toLowerCase().includes(searchLower));
    });

    displaySearchResults(results, searchTerm);
}

function displaySearchResults(results, searchTerm) {
    const faqContainer = document.querySelector('.faq-container');
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (!faqContainer) return;
    
    // Masquer tous les éléments
    faqItems.forEach(item => {
        item.style.display = 'none';
    });
    
    if (results.length === 0) {
        // Afficher message "Aucun résultat"
        showNoResultsMessage(searchTerm);
    } else {
        // Afficher les résultats
        results.forEach(result => {
            // Trouver l'élément FAQ correspondant par son contenu
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const questionText = item.querySelector('.faq-question h4').textContent.toLowerCase();
                if (questionText.includes(result.question.toLowerCase())) {
                    item.style.display = 'block';
                    highlightSearchTerm(item, searchTerm);
                }
            });
        });
        
        // Scroll vers la section FAQ
        scrollToSection('#faq');
    }
    
    // Mettre à jour l'indicateur de recherche active
    updateSearchIndicator(searchTerm);
}

function showNoResultsMessage(searchTerm) {
    const faqContainer = document.querySelector('.faq-container');
    let noResultsMsg = faqContainer.querySelector('.no-results');
    
    if (!noResultsMsg) {
        noResultsMsg = document.createElement('div');
        noResultsMsg.className = 'no-results';
        noResultsMsg.style.cssText = `
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 15px;
            margin: 2rem 0;
            color: #666;
        `;
        faqContainer.appendChild(noResultsMsg);
    }
    
    noResultsMsg.innerHTML = `
        <i class="fas fa-search" style="font-size: 3rem; color: #9146ff; margin-bottom: 1rem;"></i>
        <h3>Aucun résultat trouvé</h3>
        <p>Aucune question ne correspond à votre recherche "<strong>${searchTerm}</strong>"</p>
        <p>Essayez avec d'autres mots-clés ou parcourez nos catégories.</p>
    `;
    noResultsMsg.style.display = 'block';
}

function highlightSearchTerm(item, searchTerm) {
    const question = item.querySelector('.faq-question h4');
    const answer = item.querySelector('.faq-answer');
    
    if (question) {
        highlightText(question, searchTerm);
    }
    if (answer) {
        highlightText(answer, searchTerm);
    }
}

function highlightText(element, searchTerm) {
    const text = element.innerHTML;
    const regex = new RegExp(`(${searchTerm})`, 'gi');
    const highlightedText = text.replace(regex, '<mark style="background: #ffd700; padding: 2px 4px; border-radius: 3px;">$1</mark>');
    element.innerHTML = highlightedText;
}

function showAllFAQ() {
    isSearchActive = false;
    currentSearchTerm = '';
    
    const faqItems = document.querySelectorAll('.faq-item');
    const noResultsMsg = document.querySelector('.no-results');
    
    if (noResultsMsg) {
        noResultsMsg.style.display = 'none';
    }
    
    // Appliquer le filtre de catégorie actuel
    filterFAQByCategory(currentCategory);
    
    // Retirer le surlignage
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question h4');
        const answer = item.querySelector('.faq-answer');
        
        if (question) {
            question.innerHTML = question.textContent;
        }
        if (answer) {
            answer.innerHTML = answer.innerHTML.replace(/<mark[^>]*>(.*?)<\/mark>/gi, '$1');
        }
    });
    
    // Mettre à jour l'indicateur de recherche
    updateSearchIndicator('');
}

function updateSearchIndicator(searchTerm) {
    const searchContainer = document.querySelector('.search-container');
    if (!searchContainer) return;
    
    let searchIndicator = searchContainer.querySelector('.search-indicator');
    
    if (searchTerm && searchTerm.length > 0) {
        if (!searchIndicator) {
            searchIndicator = document.createElement('div');
            searchIndicator.className = 'search-indicator';
            searchIndicator.style.cssText = `
                background: #9146ff;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 25px;
                margin: 1rem auto;
                display: inline-block;
                font-size: 0.9rem;
            `;
            searchContainer.appendChild(searchIndicator);
        }
        
        searchIndicator.innerHTML = `
            <i class="fas fa-search"></i>
            Résultats pour "${searchTerm}" 
            <button onclick="clearSearch()" style="background: none; border: none; color: white; margin-left: 0.5rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        `;
        searchIndicator.style.display = 'block';
    } else if (searchIndicator) {
        searchIndicator.style.display = 'none';
    }
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    showAllFAQ();
}

// ===== FONCTIONS D'AUTHENTIFICATION =====
function showLoginForm() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (registerForm) {
        registerForm.classList.add('hidden');
    }
    
    if (loginForm) {
        loginForm.classList.remove('hidden');
        currentForm = 'login';
        
        // Focus sur le premier champ après l'animation
        setTimeout(() => {
            const firstInput = loginForm.querySelector('input[type="text"], input[type="email"]');
            if (firstInput) firstInput.focus();
        }, 300);
    }
}

function showRegisterForm() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.classList.add('hidden');
    }
    
    if (registerForm) {
        registerForm.classList.remove('hidden');
        currentForm = 'register';
        
        // Focus sur le premier champ après l'animation
        setTimeout(() => {
            const firstInput = registerForm.querySelector('input[type="text"]');
            if (firstInput) firstInput.focus();
        }, 300);
    }
}

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
            
            // Mettre à jour le menu de navigation
            updateNavigationMenu(result.data?.username || 'Utilisateur');
            
            // Garder l'état de chargement pendant la redirection
            setTimeout(() => {
                window.location.href = (result.data && result.data.redirect) || 'dashboard.php';
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
            showNotification('Compte créé avec succès ! Redirection en cours...', 'success');
            
            // Redirection si spécifiée
            if (result.data && result.data.redirect) {
                setTimeout(() => {
                    window.location.href = result.data.redirect;
                }, 1500);
            } else {
                // Sinon, basculer vers le formulaire de connexion
                setTimeout(() => {
                    showLoginForm();
                    // Pré-remplir l'email
                    const emailInput = document.getElementById('login-email');
                    if (emailInput) emailInput.value = email;
                }, 2000);
            }
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

// ===== VALIDATION DES FORMULAIRES =====
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

// ===== UTILITAIRES =====
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

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

function setSubmitState(form, isSubmitting) {
    const submitButton = form.querySelector('button[type="submit"]');
    const inputs = form.querySelectorAll('input');
    const originalText = submitButton.textContent;
    
    if (isSubmitting) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connexion en cours...';
        submitButton.classList.add('loading');
        
        // Ajouter un overlay de chargement sur le formulaire
        const formContainer = form.closest('.form-container');
        if (formContainer && !formContainer.querySelector('.loading-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="loading-content"><i class="fas fa-spinner fa-spin"></i><p>Vérification en cours...</p></div>';
            formContainer.appendChild(overlay);
        }
        
        inputs.forEach(input => {
            input.disabled = true;
            input.style.opacity = '0.6';
        });
        
        // Désactiver les liens de navigation
        const formFooter = form.querySelector('.form-footer');
        if (formFooter) {
            const links = formFooter.querySelectorAll('a');
            links.forEach(link => {
                link.style.pointerEvents = 'none';
                link.style.opacity = '0.5';
            });
        }
    } else {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
        submitButton.classList.remove('loading');
        
        // Supprimer l'overlay de chargement
        const formContainer = form.closest('.form-container');
        if (formContainer) {
            const overlay = formContainer.querySelector('.loading-overlay');
            if (overlay) {
                overlay.remove();
            }
        }
        
        inputs.forEach(input => {
            input.disabled = false;
            input.style.opacity = '1';
        });
        
        // Réactiver les liens de navigation
        const formFooter = form.querySelector('.form-footer');
        if (formFooter) {
            const links = formFooter.querySelectorAll('a');
            links.forEach(link => {
                link.style.pointerEvents = 'auto';
                link.style.opacity = '1';
            });
        }
    }
    
    window.isSubmitting = isSubmitting;
}

function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    if (!notification) return;
    
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
    if (notification) {
        notification.classList.remove('show');
    }
}

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

function updateNavigationMenu(username) {
    const nav = document.querySelector('.nav');
    if (!nav) return;
    
    // Chercher le lien de connexion existant
    const loginLink = nav.querySelector('a[href="login.php"]');
    if (loginLink) {
        // Remplacer par le menu profil
        const profileMenu = `
            <div class="nav-profile">
                <a href="dashboard.php" class="nav-link profile-link">
                    <i class="fas fa-user-circle"></i>
                    <span>${username}</span>
                </a>
                <div class="profile-dropdown">
                    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </div>
            </div>
        `;
        loginLink.outerHTML = profileMenu;
    }
}

function preloadResources() {
    // Précharger les images ou autres ressources si nécessaire
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = 'dashboard.php';
    document.head.appendChild(link);
}

// ===== FONCTIONS DE NAVIGATION =====
function scrollToSection(target) {
    const section = document.querySelector(target);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function updateActiveNavLink(activeLink) {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    activeLink.classList.add('active');
}

// ===== ANIMATIONS =====
function animateStats() {
    const stats = document.querySelectorAll('.stat-number');
    
    stats.forEach(stat => {
        const target = parseInt(stat.textContent.replace(/[^\d]/g, ''));
        const suffix = stat.textContent.replace(/[\d]/g, '');
        let current = 0;
        const increment = target / 50;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(current) + suffix;
        }, 30);
    });
}

// Observer pour l'animation des statistiques
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateStats();
            statsObserver.unobserve(entry.target);
        }
    });
});

// Observer la section hero pour les statistiques
document.addEventListener('DOMContentLoaded', function() {
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        statsObserver.observe(heroSection);
    }
});

// ===== GESTION DU SCROLL =====
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Gestion de la navbar transparente/blanche
    if (window.scrollY > 100) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
    
    // Gestion de la navigation active
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (scrollY >= (sectionTop - 200)) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});

// ===== FONCTIONS UTILITAIRES =====
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Recherche avec debounce pour améliorer les performances
const debouncedSearch = debounce(performSearch, 300);

// Mise à jour de l'event listener pour la recherche
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            if (searchTerm.length > 2) {
                debouncedSearch(searchTerm);
            } else if (searchTerm.length === 0) {
                showAllFAQ();
            }
        });
    }
});

// Fonction pour effacer la recherche (accessible globalement)
window.clearSearch = clearSearch;
