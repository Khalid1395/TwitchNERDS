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
            </nav>
        </div>
    </header>

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
                    <div class="search-suggestions">
                        <span class="suggestion-tag" data-search="streaming">Streaming</span>
                        <span class="suggestion-tag" data-search="obs">OBS</span>
                        <span class="suggestion-tag" data-search="chat">Chat</span>
                        <span class="suggestion-tag" data-search="monetisation">Monétisation</span>
                        <span class="suggestion-tag" data-search="partenariat">Partenariat</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="faq-section">
            <div class="container">
                <h3>Questions Fréquemment Posées</h3>
                <div class="faq-categories">
                    <button class="category-btn active" data-category="all">Toutes</button>
                    <button class="category-btn" data-category="streaming">Streaming</button>
                    <button class="category-btn" data-category="technical">Technique</button>
                    <button class="category-btn" data-category="monetisation">Monétisation</button>
                    <button class="category-btn" data-category="community">Communauté</button>
                </div>

                <div class="faq-container">
                    <div class="faq-item" data-category="streaming">
                        <div class="faq-question">
                            <h4>Comment commencer à streamer sur Twitch ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Pour commencer à streamer sur Twitch :</p>
                            <ol>
                                <li>Créez un compte Twitch</li>
                                <li>Téléchargez OBS Studio (gratuit)</li>
                                <li>Configurez votre stream key dans OBS</li>
                                <li>Choisissez votre jeu ou contenu</li>
                                <li>Lancez votre premier stream !</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item" data-category="technical">
                        <div class="faq-question">
                            <h4>Quels sont les meilleurs paramètres OBS pour débuter ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Paramètres recommandés pour débuter :</p>
                            <ul>
                                <li><strong>Résolution :</strong> 1920x1080 ou 1280x720</li>
                                <li><strong>FPS :</strong> 30 ou 60 selon votre connexion</li>
                                <li><strong>Bitrate :</strong> 2500-6000 kbps</li>
                                <li><strong>Encoder :</strong> x264 ou NVENC si vous avez une carte NVIDIA</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item" data-category="monetisation">
                        <div class="faq-question">
                            <h4>Comment devenir partenaire Twitch ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Critères pour devenir partenaire :</p>
                            <ul>
                                <li>Streamer au moins 25 heures sur 30 jours</li>
                                <li>Streamer sur au moins 12 jours différents</li>
                                <li>Avoir une moyenne de 75 viewers</li>
                                <li>Respecter les conditions d'utilisation</li>
                                <li>Être en conformité avec les directives communautaires</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item" data-category="streaming">
                        <div class="faq-question">
                            <h4>Comment améliorer la qualité de mon stream ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Conseils pour améliorer votre stream :</p>
                            <ul>
                                <li>Investissez dans un bon microphone</li>
                                <li>Éclairez bien votre visage</li>
                                <li>Créez des overlays attrayants</li>
                                <li>Interagissez avec votre chat</li>
                                <li>Streamer régulièrement</li>
                                <li>Partagez vos streams sur les réseaux sociaux</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item" data-category="technical">
                        <div class="faq-question">
                            <h4>Mon stream lag, que faire ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Solutions pour réduire le lag :</p>
                            <ul>
                                <li>Vérifiez votre connexion internet (upload minimum 3 Mbps)</li>
                                <li>Fermez les applications inutiles</li>
                                <li>Réduisez la résolution ou le FPS</li>
                                <li>Changez de serveur Twitch</li>
                                <li>Utilisez un encodeur matériel (NVENC/QuickSync)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item" data-category="community">
                        <div class="faq-question">
                            <h4>Comment créer une communauté engagée ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Stratégies pour développer votre communauté :</p>
                            <ul>
                                <li>Soyez authentique et vous-même</li>
                                <li>Répondez aux messages du chat</li>
                                <li>Créez des événements réguliers</li>
                                <li>Utilisez Discord pour rester connecté</li>
                                <li>Collaborez avec d'autres streamers</li>
                                <li>Créez du contenu unique</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item" data-category="monetisation">
                        <div class="faq-question">
                            <h4>Comment gagner de l'argent en streamant ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Moyens de monétiser votre stream :</p>
                            <ul>
                                <li><strong>Abonnements :</strong> Revenus mensuels récurrents</li>
                                <li><strong>Bits :</strong> Pourboires virtuels</li>
                                <li><strong>Donations :</strong> Via PayPal ou autres plateformes</li>
                                <li><strong>Partenariats :</strong> Sponsors et collaborations</li>
                                <li><strong>Ventes :</strong> Merchandising et produits</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item" data-category="technical">
                        <div class="faq-question">
                            <h4>Quel équipement recommandez-vous pour débuter ?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Équipement essentiel pour débuter :</p>
                            <ul>
                                <li><strong>Microphone :</strong> Blue Yeti ou Audio-Technica AT2020</li>
                                <li><strong>Webcam :</strong> Logitech C920 ou C922</li>
                                <li><strong>Éclairage :</strong> Anneau lumineux LED</li>
                                <li><strong>PC :</strong> Processeur quad-core minimum</li>
                                <li><strong>Internet :</strong> Connexion stable avec bon upload</li>
                            </ul>
                        </div>
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
