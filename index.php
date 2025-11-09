<?php
// Inclure la configuration pour gérer les sessions
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitch Nerd - Forum FAQ</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="assets/upload/logo_b.png" alt="Twitch Nerd" class="logo-img logo-transparent">
                <img src="assets/upload/logo.png" alt="Twitch Nerd" class="logo-img logo-scrolled">
            </div>
            <nav class="nav">
                <a href="#accueil" class="nav-link active">Accueil</a>
                <a href="#faq" class="nav-link">FAQ</a>
                <?php
                // Vérifier si l'utilisateur est connecté
                // La session est déjà démarrée via config.php
                
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    // Utilisateur connecté - afficher profil
                    echo '<div class="nav-profile">';
                    echo '<a href="dashboard.php" class="nav-link profile-link">';
                    echo '<i class="fas fa-user-circle"></i>';
                    echo '<span>' . htmlspecialchars($_SESSION['username']) . '</span>';
                    echo '</a>';
                    echo '<div class="profile-dropdown">';
                    echo '<a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>';
                    if (($_SESSION['role'] ?? 'user') === 'admin') {
                        echo '<a href="administration.php"><i class="fas fa-tools"></i> Administration</a>';
                    }
                    echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>';
                    echo '</div>';
                    echo '</div>';
                    // Données utilisateur pour JavaScript
                    echo '<script>window.currentUser = { id: ' . $_SESSION['user_id'] . ', role: "' . htmlspecialchars($_SESSION['role'] ?? 'user') . '" };</script>';
                } else {
                    // Utilisateur non connecté - afficher connexion
                    echo '<a href="login.php" class="nav-link">Connexion</a>';
                }
                ?>
            </nav>
        </div>
    </header>

    <!-- Notification globale (utilisée par script.js) -->
    <div class="notification" id="notification">
        <span class="notification-text"></span>
        <button class="notification-close" onclick="hideNotification()">&times;</button>
    </div>

    <main class="main">
        <section id="accueil" class="hero">
            <div class="hero-background">
                <div class="hero-particles"></div>
                <div class="hero-gradient"></div>
            </div>
            <div class="container">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fab fa-twitch"></i>
                        <span>Communauté Twitch</span>
                    </div>
                    <h1 class="hero-title">
                        <span class="title-line">Bienvenue sur</span>
                        <span class="title-highlight">Twitch Nerd</span>
                    </h1>
                    <p class="hero-description">
                        Le forum de référence pour tous les streamers et viewers Twitch. 
                        <br>Trouvez des réponses à vos questions et partagez votre passion pour le streaming.
                    </p>
                    <div class="hero-actions">
                        <button class="btn-primary">
                            <i class="fas fa-rocket"></i>
                            Commencer maintenant
                        </button>
                        <button class="btn-secondary">
                            <i class="fas fa-play"></i>
                            Voir la FAQ
                        </button>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">1,250+</span>
                                <span class="stat-label">Questions répondues</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">500+</span>
                                <span class="stat-label">Membres actifs</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">24/7</span>
                                <span class="stat-label">Support communautaire</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="floating-card card-1">
                        <i class="fab fa-twitch"></i>
                        <span>Streaming</span>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-microphone"></i>
                        <span>Audio</span>
                    </div>
                    <div class="floating-card card-3">
                        <i class="fas fa-video"></i>
                        <span>Vidéo</span>
                    </div>
                    <div class="floating-card card-4">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="recherche" class="search-section">
            <div class="container">
                <div class="search-container">
                    <h3>Rechercher une question</h3>
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Tapez votre question ici...">
                        <button id="searchBtn"><i class="fas fa-search"></i></button>
            </div>
                </div>
            </div>
        </section>

        <section id="faq" class="faq-section">
            <div class="container">
                <div class="faq-categories">
                    <button class="category-btn active" data-category="all">Toutes</button>
                    <button class="category-btn" data-category="streaming">Streaming</button>
                    <button class="category-btn" data-category="technical">Technique</button>
                    <button class="category-btn" data-category="monetisation">Monétisation</button>
                    <button class="category-btn" data-category="community">Communauté</button>
                </div>

                <div class="faq-container">
                    <div class="faq-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Chargement des questions...</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Twitch Nerd</h4>
                    <p>La communauté Twitch de référence pour tous vos besoins de streaming.</p>
                </div>
                <div class="footer-section">
                    <h4>Liens utiles</h4>
                    <ul>
                        <li><a href="#">Guide du débutant</a></li>
                        <li><a href="#">Équipement recommandé</a></li>
                        <li><a href="#">Partenariat Twitch</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>Discord: TwitchNerd#1234</p>
                    <p>Email: contact@twitchnerds.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Twitch Nerd. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
