
// Ajouter la fonctionnalité de recherche AJAX pour les discussions
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les fonctions d'administration
    initializeAdminFunctions();
    
    // Initialiser la recherche AJAX pour les discussions
    initializeDiscussionSearch();
});

// Fonctions pour la recherche AJAX des discussions
function initializeDiscussionSearch() {
    // Gestion de la recherche d'utilisateur pour les discussions
    const usernameSearch = document.getElementById('username_search');
    const usernameSuggestions = document.getElementById('username-suggestions');
    const commentsList = document.getElementById('comments-list');
    const selectedCommentIdInput = document.getElementById('selected_comment_id');
    const deleteCommentBtn = document.querySelector('.btn-delete-comment');
    
    if (usernameSearch && usernameSuggestions && commentsList) {
        let searchTimeout;
        let selectedCommentId = null;
        
        // Recherche d'utilisateur avec suggestions
        usernameSearch.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                usernameSuggestions.style.display = 'none';
                usernameSuggestions.classList.remove('show');
                clearCommentsList();
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch('process_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=search_username&query=${encodeURIComponent(query)}`
                })
                .then(response => response.json())
                .then(data => {
                    displayUsernameSuggestions(data);
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                    usernameSuggestions.style.display = 'none';
                    usernameSuggestions.classList.remove('show');
                });
            }, 300);
        });
        
        // Affichage des suggestions d'utilisateur
        function displayUsernameSuggestions(users) {
            if (users.length === 0) {
                usernameSuggestions.innerHTML = '<div class="suggestion-item"><i class="fas fa-user-slash"></i>Aucun utilisateur trouvé</div>';
                usernameSuggestions.style.display = 'block';
                usernameSuggestions.classList.add('show');
                return;
            }
            
            const suggestionsHTML = users.map(user => 
                `<div class="suggestion-item" data-user-id="${user.id}" data-username="${user.username}">
                    <i class="fas fa-user"></i>
                    <span>${user.username}</span>
                    <small style="color: #a0aec0; margin-left: auto;">(ID: ${user.id})</small>
                </div>`
            ).join('');
            
            usernameSuggestions.innerHTML = suggestionsHTML;
            usernameSuggestions.style.display = 'block';
            usernameSuggestions.classList.add('show');
            
            // Gestion du clic sur une suggestion
            usernameSuggestions.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    
                    if (userId && username) {
                        usernameSearch.value = username;
                        usernameSuggestions.style.display = 'none';
                        usernameSuggestions.classList.remove('show');
                        loadUserComments(userId);
                    }
                });
            });
        }
        
        // Chargement des commentaires d'un utilisateur
        function loadUserComments(userId) {
            console.log('Chargement des commentaires pour l\'utilisateur ID:', userId);
            
            fetch('process_admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_user_comments&user_id=${userId}`
            })
            .then(response => {
                console.log('Réponse reçue:', response);
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data);
                displayCommentsList(data);
            })
            .catch(error => {
                console.error('Erreur lors du chargement des commentaires:', error);
                clearCommentsList();
            });
        }
        
        // Affichage de la liste des commentaires
        function displayCommentsList(comments) {
            if (comments.length === 0) {
                commentsList.innerHTML = `
                    <div class="no-comments-message">
                        <i class="fas fa-comment-slash"></i>
                        <p>Aucun commentaire trouvé pour cet utilisateur.</p>
                    </div>
                `;
                updateDeleteButton(false);
                return;
            }
            
            const commentsHTML = comments.map(comment => {
                const truncatedText = comment.commentary.length > 150 
                    ? comment.commentary.substring(0, 150) + '...' 
                    : comment.commentary;
                    
                const publishedDate = new Date(comment.published_at).toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                return `
                    <div class="comment-item" data-comment-id="${comment.id}">
                        <div class="comment-header">
                            <span class="comment-id">ID: ${comment.id}</span>
                            <span class="comment-date">${publishedDate}</span>
                        </div>
                        <div class="comment-author">
                            <i class="fas fa-user"></i>
                            ${comment.username}
                        </div>
                        <div class="comment-text">${truncatedText}</div>
                        <div class="comment-actions">
                            <button type="button" class="btn-secondary btn-edit-comment" 
                                    data-id="${comment.id}" 
                                    data-text="${comment.commentary.replace(/\"/g, '&quot;')}">
                                <i class="fas fa-edit"></i> Éditer
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            
            commentsList.innerHTML = commentsHTML;
            
            // Gestion de la sélection des commentaires
            commentsList.querySelectorAll('.comment-item').forEach(item => {
                item.addEventListener('click', function() {
                    // Retirer la sélection précédente
                    commentsList.querySelectorAll('.comment-item').forEach(el => {
                        el.classList.remove('selected');
                    });
                    
                    // Ajouter la sélection à l'élément cliqué
                    this.classList.add('selected');
                    
                    // Mettre à jour l'ID du commentaire sélectionné
                    selectedCommentId = this.dataset.commentId;
                    selectedCommentIdInput.value = selectedCommentId;
                    
                    // Activer le bouton de suppression
                    updateDeleteButton(true);
                });
            });
            
            updateDeleteButton(false);
            
            // Préremplissage du formulaire d'édition de commentaire
            const editFormIdInput = document.getElementById('edit_comment_id');
            const editFormTextInput = document.getElementById('edit_comment_text');
            const editFormSubmit = document.getElementById('edit-comment-submit');
            commentsList.querySelectorAll('.btn-edit-comment').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const cid = this.dataset.id;
                    const ctext = this.dataset.text || '';
                    if (editFormIdInput && editFormTextInput && editFormSubmit) {
                        editFormIdInput.value = cid;
                        editFormTextInput.value = ctext;
                        editFormSubmit.disabled = false;
                        const editCard = document.getElementById('edit-comment-card');
                        if (editCard) {
                            editCard.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            });
        }
        
        // Vider la liste des commentaires
        function clearCommentsList() {
            commentsList.innerHTML = `
                <div class="no-comments-message">
                    <i class="fas fa-info-circle"></i>
                    <p>Recherchez un utilisateur ou saisissez un ID de commentaire pour afficher les commentaires disponibles.</p>
                </div>
            `;
            selectedCommentId = null;
            selectedCommentIdInput.value = '';
            updateDeleteButton(false);
        }
        
        // Mise à jour de l'état du bouton de suppression
        function updateDeleteButton(enabled) {
            if (deleteCommentBtn) {
                deleteCommentBtn.disabled = !enabled;
                if (enabled) {
                    deleteCommentBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Supprimer le commentaire sélectionné';
                } else {
                    deleteCommentBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Sélectionnez un commentaire';
                }
            }
        }
        
        // Gestion de la recherche par ID de commentaire
        const commentIdInput = document.getElementById('comment_id');
        if (commentIdInput) {
            commentIdInput.addEventListener('input', function() {
                const commentId = this.value.trim();
                
                if (commentId) {
                    // Vider la recherche par utilisateur
                    usernameSearch.value = '';
                    usernameSuggestions.style.display = 'none';
                    usernameSuggestions.classList.remove('show');
                    
                    // Simuler la sélection d'un commentaire par ID
                    selectedCommentId = commentId;
                    selectedCommentIdInput.value = commentId;
                    
                    // Afficher un message dans la liste des commentaires
                    commentsList.innerHTML = `
                        <div class="comment-item selected" data-comment-id="${commentId}">
                            <div class="comment-header">
                                <span class="comment-id">ID: ${commentId}</span>
                                <span class="comment-date">Recherche par ID</span>
                            </div>
                            <div class="comment-author">
                                <i class="fas fa-search"></i>
                                Commentaire recherché par ID
                            </div>
                            <div class="comment-text">Le commentaire avec l'ID ${commentId} sera supprimé si il existe.</div>
                        </div>
                    `;
                    
                    updateDeleteButton(true);
                } else {
                    clearCommentsList();
                }
            });
        }
        
        // Fermer les suggestions quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!usernameSearch.contains(e.target) && !usernameSuggestions.contains(e.target)) {
                usernameSuggestions.style.display = 'none';
                usernameSuggestions.classList.remove('show');
            }
        });
    }
}

// Fonction pour charger les commentaires d'un utilisateur (version alternative pour select)
function loadUserCommentsForSelect(userId) {
    const commentSelectGroup = document.getElementById('user-comments-group');
    const commentSelect = document.getElementById('comment_select');
    
    fetch('process_admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_user_comments&user_id=${userId}`
    })
        .then(response => response.json())
        .then(data => {
            commentSelect.innerHTML = '<option value="">-- Sélectionnez un commentaire --</option>';
            
            if (data && data.length > 0) {
                data.forEach(comment => {
                    const option = document.createElement('option');
                    option.value = comment.id;
                    option.textContent = `#${comment.id}: ${comment.commentary} (${comment.published_at})`;
                    commentSelect.appendChild(option);
                });
                
                commentSelectGroup.style.display = 'block';
            } else {
                commentSelectGroup.style.display = 'none';
                alert('Aucun commentaire trouvé pour cet utilisateur.');
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des commentaires:', error);
            commentSelectGroup.style.display = 'none';
        });
}

// Fonctions pour la gestion des membres
function initializeAdminFunctions() {
    // Fonctions utilitaires pour la modal d'édition de membre
    function openMemberModal() {
        const modal = document.getElementById('memberEditModal');
        if (!modal) return;
        modal.style.display = 'flex';
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMemberModal() {
        const modal = document.getElementById('memberEditModal');
        if (!modal) return;
        modal.classList.remove('open');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    // Gestion des boutons d'édition de membres
    const editButtons = document.querySelectorAll('.edit-btn');
    if (editButtons) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.id;
                const username = this.dataset.username;
                const email = this.dataset.email;
                
                // Remplir le formulaire d'édition
                document.getElementById('edit_member_id').value = userId;
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_password').value = '';
                
                // Ouvrir la modal d'édition de membre
                openMemberModal();
            });
        });
    }
    
    // Gestion des boutons de suppression de membres
    const deleteButtons = document.querySelectorAll('.delete-btn');
    if (deleteButtons) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.id;
                const username = this.dataset.username;
                
                if (confirm(`Êtes-vous sûr de vouloir supprimer le membre "${username}" ?`)) {
                    // Créer un formulaire pour la suppression
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'process_admin.php';
                    form.style.display = 'none';
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete_member';
                    
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'member_id';
                    idInput.value = userId;
                    
                    form.appendChild(actionInput);
                    form.appendChild(idInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    }
    
    // Gestion du bouton d'annulation d'édition (modal)
    const cancelEditButton = document.getElementById('cancel-member-edit');
    if (cancelEditButton) {
        cancelEditButton.addEventListener('click', function() {
            closeMemberModal();
        });
    }

    // Gestion de la fermeture via overlay et bouton close pour la modal membre
    const memberEditModal = document.getElementById('memberEditModal');
    if (memberEditModal) {
        const memberModalCloseBtn = memberEditModal.querySelector('.modal-close');
        const memberModalOverlay = memberEditModal.querySelector('.modal-overlay');

        if (memberModalCloseBtn) {
            memberModalCloseBtn.addEventListener('click', closeMemberModal);
        }
        if (memberModalOverlay) {
            memberModalOverlay.addEventListener('click', function(e) {
                if (e.target && e.target.dataset && e.target.dataset.close === 'true') {
                    closeMemberModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMemberModal();
            }
        });
    }

    // Gestion des boutons d'édition de FAQ (préremplissage du formulaire)
    const faqEditButtons = document.querySelectorAll('.btn-edit-faq');
    if (faqEditButtons && faqEditButtons.length > 0) {
        const faqEditModal = document.getElementById('faqEditModal');
        const modalCloseBtn = faqEditModal ? faqEditModal.querySelector('.modal-close') : null;
        const modalOverlay = faqEditModal ? faqEditModal.querySelector('.modal-overlay') : null;
        const cancelEditBtn = document.getElementById('cancel-faq-edit');

        function openFaqModal() {
            if (!faqEditModal) return;
            faqEditModal.style.display = 'flex';
            faqEditModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeFaqModal() {
            if (!faqEditModal) return;
            faqEditModal.classList.remove('open');
            faqEditModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        const idInput = document.getElementById('edit_faq_id');
        const catSelect = document.getElementById('edit_faq_category');
        const questionInput = document.getElementById('edit_faq_question');
        const answerInput = document.getElementById('edit_faq_answer');
        const keywordsInput = document.getElementById('edit_faq_keywords');
        const orderInput = document.getElementById('edit_faq_display_order');
        const activeCheckbox = document.getElementById('edit_faq_is_active');
        faqEditButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const category = this.dataset.category;
                const question = this.dataset.question;
                const displayOrder = this.dataset.display_order;
                const isActive = parseInt(this.dataset.is_active, 10) === 1;
                if (idInput) idInput.value = id;
                if (catSelect) catSelect.value = category;
                if (questionInput) questionInput.value = question;
                if (orderInput) orderInput.value = displayOrder || 0;
                if (activeCheckbox) activeCheckbox.checked = isActive;
                // La réponse et les mots-clés ne sont pas inclus dans le tableau; laisser l'admin les ajuster.
                openFaqModal();
            });
        });

        // Gestion de fermeture du modal
        if (modalCloseBtn) {
            modalCloseBtn.addEventListener('click', closeFaqModal);
        }
        if (modalOverlay) {
            modalOverlay.addEventListener('click', function(e) {
                if (e.target && e.target.dataset && e.target.dataset.close === 'true') {
                    closeFaqModal();
                }
            });
        }
        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', function() {
                closeFaqModal();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFaqModal();
            }
        });
    }
}

// Gestion du défilement et de la navigation
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Ajouter la classe 'scrolled' au header lors du défilement
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        // Mettre à jour le lien actif en fonction de la position de défilement
        updateActiveNavLink();
    });
    
    // Défilement fluide vers les sections lors du clic sur les liens de navigation
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Ne pas traiter les liens externes
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    scrollToSection(targetElement);
                }
            }
        });
    });
    
    // Initialiser le lien actif au chargement de la page
    updateActiveNavLink();
});

// Fonction pour faire défiler vers une section
function scrollToSection(element) {
    const headerHeight = document.querySelector('header').offsetHeight;
    const elementPosition = element.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
    
    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
    });
}

// Fonction pour mettre à jour le lien actif dans la navigation
function updateActiveNavLink() {
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link');
    
    let currentSectionId = '';
    const scrollPosition = window.scrollY + 100; // Ajouter un décalage pour une meilleure détection
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            currentSectionId = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        
        if (href === `#${currentSectionId}`) {
            link.classList.add('active');
        }
    });
}