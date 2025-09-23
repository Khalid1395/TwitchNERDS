// Variables globales
let currentCategory = 'all';
let searchResults = [];
let currentSearchTerm = '';
let isSearchActive = false;

// Données des questions FAQ
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

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeFAQ();
    initializeSearch();
});

// Initialisation des événements
function initializeEventListeners() {
    // Navigation
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('href');
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
}

// Initialisation de la FAQ
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

// Initialisation de la recherche
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');

    // Recherche en temps réel
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        if (searchTerm.length > 2) {
            performSearch(searchTerm);
        } else if (searchTerm.length === 0) {
            showAllFAQ();
        }
    });

    // Recherche au clic
    searchBtn.addEventListener('click', function() {
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            performSearch(searchTerm);
        }
    });

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

// Navigation fluide
function scrollToSection(target) {
    const section = document.querySelector(target);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Mise à jour du lien de navigation actif
function updateActiveNavLink(activeLink) {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    activeLink.classList.add('active');
}

// Filtrage des FAQ par catégorie
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

// Mise à jour du bouton de catégorie actif
function updateActiveCategoryBtn(activeBtn) {
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    activeBtn.classList.add('active');
}

// Toggle des éléments FAQ
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

// Recherche dans les FAQ
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

// Affichage des résultats de recherche
function displaySearchResults(results, searchTerm) {
    const faqContainer = document.querySelector('.faq-container');
    const faqItems = document.querySelectorAll('.faq-item');
    
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

// Message "Aucun résultat"
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

// Surlignage des termes de recherche
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

// Fonction de surlignage
function highlightText(element, searchTerm) {
    const text = element.innerHTML;
    const regex = new RegExp(`(${searchTerm})`, 'gi');
    const highlightedText = text.replace(regex, '<mark style="background: #ffd700; padding: 2px 4px; border-radius: 3px;">$1</mark>');
    element.innerHTML = highlightedText;
}

// Afficher toutes les FAQ
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

// Mettre à jour l'indicateur de recherche active
function updateSearchIndicator(searchTerm) {
    const searchContainer = document.querySelector('.search-container');
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

// Fonction pour effacer la recherche
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    showAllFAQ();
}

// Animation des statistiques
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

// Gestion du scroll pour la navigation et la navbar
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

// Fonction utilitaire pour le debounce
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
