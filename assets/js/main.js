// JavaScript principal pour TwitchNerd

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des fonctionnalités
    initFAQ();
    initSearch();
    initUserMenu();
    initAnimations();
});

// Gestion des FAQ (accordéon)
function initFAQ() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        const toggle = item.querySelector('.faq-toggle');
        
        if (question && answer && toggle) {
            question.addEventListener('click', function() {
                const isActive = answer.classList.contains('active');
                
                // Fermer toutes les autres FAQ
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.querySelector('.faq-answer').classList.remove('active');
                        otherItem.querySelector('.faq-toggle').style.transform = 'rotate(0deg)';
                    }
                });
                
                // Toggle la FAQ actuelle
                if (isActive) {
                    answer.classList.remove('active');
                    toggle.style.transform = 'rotate(0deg)';
                } else {
                    answer.classList.add('active');
                    toggle.style.transform = 'rotate(180deg)';
                }
            });
        }
    });
}

// Gestion de la recherche
function initSearch() {
    const searchForms = document.querySelectorAll('.search-form');
    
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const searchInput = form.querySelector('input[type="text"]');
            if (searchInput && searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    });
}

// Gestion du menu utilisateur
function initUserMenu() {
    const userMenu = document.querySelector('.user-menu');
    
    if (userMenu) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.querySelector('.dropdown');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }
        });
        
        // Fermer le menu en cliquant ailleurs
        document.addEventListener('click', function() {
            const dropdown = userMenu.querySelector('.dropdown');
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        });
    }
}

// Animations et effets
function initAnimations() {
    // Animation des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observer les éléments animables
    const animatedElements = document.querySelectorAll('.article-card, .discussion-item, .faq-item');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Fonctions utilitaires
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Suppression automatique
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Fonctions pour les interactions avec la base de données
function likeFAQ(faqId) {
    fetch('ajax/like-faq.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ faq_id: faqId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Merci pour votre retour !', 'success');
        } else {
            showNotification('Erreur lors de l\'enregistrement', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    });
}

function reportFAQ(faqId) {
    const reason = prompt('Raison du signalement (optionnel):');
    if (reason !== null) {
        fetch('ajax/report-faq.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                faq_id: faqId, 
                reason: reason 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Signalement envoyé, merci !', 'success');
            } else {
                showNotification('Erreur lors de l\'envoi', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
        });
    }
}

function likeArticle(articleId) {
    fetch('ajax/like-article.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ article_id: articleId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likeButton = document.querySelector(`[onclick="likeArticle(${articleId})"]`);
            if (likeButton) {
                const count = likeButton.querySelector('.like-count');
                if (count) {
                    count.textContent = parseInt(count.textContent) + 1;
                }
            }
            showNotification('Article liké !', 'success');
        } else {
            showNotification('Erreur lors de l\'enregistrement', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    });
}

function likeReply(replyId) {
    fetch('ajax/like-reply.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ reply_id: replyId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likeButton = document.querySelector(`[onclick="likeReply(${replyId})"]`);
            if (likeButton) {
                const count = likeButton.querySelector('.like-count');
                if (count) {
                    count.textContent = parseInt(count.textContent) + 1;
                }
            }
        } else {
            showNotification('Erreur lors de l\'enregistrement', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    });
}

function markAsSolution(replyId, discussionId) {
    confirmAction('Marquer cette réponse comme solution ?', function() {
        fetch('ajax/mark-solution.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                reply_id: replyId, 
                discussion_id: discussionId 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Réponse marquée comme solution !', 'success');
                location.reload();
            } else {
                showNotification('Erreur lors de l\'enregistrement', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
        });
    });
}

// Gestion des formulaires
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Veuillez remplir tous les champs obligatoires', 'error');
            }
        });
    });
}

// Initialisation des validations de formulaire
document.addEventListener('DOMContentLoaded', function() {
    initFormValidation();
});

// Gestion du mode sombre (optionnel)
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Charger le mode sombre depuis le localStorage
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
    }
});

// Gestion des filtres
function initFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Retirer la classe active de tous les boutons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Ajouter la classe active au bouton cliqué
            this.classList.add('active');
            
            // Rediriger vers la nouvelle URL
            window.location.href = this.href;
        });
    });
}

// Initialisation des filtres
document.addEventListener('DOMContentLoaded', function() {
    initFilters();
});
