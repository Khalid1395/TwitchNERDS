<?php
// Inclure la configuration pour gérer les sessions
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion & Inscription - Twitch Nerd | Rejoignez la communauté</title>
    <meta name="description" content="Connectez-vous ou créez votre compte sur Twitch Nerd pour accéder au forum de la communauté Twitch. Partagez vos expériences, posez des questions et participez aux discussions sur le streaming.">
    <meta name="keywords" content="connexion twitch, inscription twitch, compte streamer, forum twitch, communauté streaming, créer compte">
    <meta name="author" content="Twitch Nerd">
    <meta name="robots" content="index, follow">
    <meta name="language" content="French">
    <link rel="canonical" href="https://wafd.agency/login.php">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://wafd.agency/login.php">
    <meta property="og:title" content="Connexion & Inscription - Twitch Nerd">
    <meta property="og:description" content="Rejoignez la communauté Twitch Nerd. Créez votre compte pour accéder au forum et participer aux discussions sur le streaming.">
    <meta property="og:image" content="https://wafd.agency/assets/upload/logo.png">
    <meta property="og:locale" content="fr_FR">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:url" content="https://wafd.agency/login.php">
    <meta name="twitter:title" content="Connexion & Inscription - Twitch Nerd">
    <meta name="twitter:description" content="Rejoignez la communauté Twitch Nerd et accédez au forum de référence pour streamers.">
    <meta name="twitter:image" content="https://wafd.agency/assets/upload/logo.png">
    
    <link rel="icon" type="image/png" href="assets/upload/logo.png">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="main-content">
        <header class="header">
            <div class="container">
                <div class="logo">
                    <img src="assets/upload/logo_b.png" alt="Twitch Nerd" class="logo-img logo-transparent">
                    <img src="assets/upload/logo.png" alt="Twitch Nerd" class="logo-img logo-scrolled">
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link">Accueil</a>
                    <a href="index.php#faq" class="nav-link">FAQ</a>
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
                        echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>';
                        echo '</div>';
                        echo '</div>';
                    } else {
                        // Utilisateur non connecté - afficher connexion
                        echo '<a href="login.php" class="nav-link">Connexion</a>';
                    }
                    ?>
                </nav>
            </div>
        </header>

        <main>
            <!-- Formulaire de connexion -->
            <div class="form-container" id="loginForm">
                <div class="form-header">
                    <h2>Connexion</h2>
                    <p>Connectez-vous à votre compte</p>
                </div>
                
                <form class="auth-form" method="POST" action="auth.php">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="input-group">
                        <label for="login-email">Email ou nom d'utilisateur</label>
                        <input type="text" id="login-email" name="email" placeholder="Votre email ou nom d'utilisateur" required>
                        <div class="error-message" id="login-email-error"></div>
                    </div>
                    
                    <div class="input-group">
                        <label for="login-password">Mot de passe</label>
                        <input type="password" id="login-password" name="password" placeholder="Votre mot de passe" required>
                        <div class="error-message" id="login-password-error"></div>
                    </div>
                    
                    <div class="form-options">
                        <div class="checkbox-container">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                    </div>
                    
                    <button type="submit" class="btn-primary">Se connecter</button>
                </form>
                
                <div class="form-footer">
                    <p>Pas encore de compte ? <a href="#" onclick="showRegisterForm()">S'inscrire</a></p>
                </div>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="form-container hidden" id="registerForm">
                <div class="form-header">
                    <h2>Inscription</h2>
                    <p>Créez votre compte gratuitement</p>
                </div>
                
                <form class="auth-form" method="POST" action="auth.php">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="input-group">
                        <label for="register-username">Nom d'utilisateur</label>
                        <input type="text" id="register-username" name="username" placeholder="Votre nom d'utilisateur" required>
                        <div class="error-message" id="register-username-error"></div>
                    </div>
                    
                    <div class="input-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" placeholder="Votre adresse email" required>
                        <div class="error-message" id="register-email-error"></div>
                    </div>
                    
                    <div class="input-group">
                        <label for="register-password">Mot de passe</label>
                        <input type="password" id="register-password" name="password" placeholder="Votre mot de passe" required>
                        <div class="error-message" id="register-password-error"></div>
                    </div>
                    
                    <div class="input-group">
                        <label for="register-confirm-password">Confirmer le mot de passe</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" placeholder="Confirmez votre mot de passe" required>
                        <div class="error-message" id="register-confirm-password-error"></div>
                    </div>
                    
                    <div class="form-options">
                        <div class="checkbox-container">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">J'accepte les <a href="#" style="color: #9146ff;">conditions d'utilisation</a></label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary">Créer mon compte</button>
                </form>
                
                <div class="form-footer">
                    <p>Déjà un compte ? <a href="#" onclick="showLoginForm()">Se connecter</a></p>
                </div>
            </div>
        </main>

        <!-- Messages de notification -->
        <div class="notification" id="notification">
            <span class="notification-text"></span>
            <button class="notification-close" onclick="hideNotification()">&times;</button>
        </div>
    </div>

    <!-- Bouton remonter en haut -->
    <button id="scrollToTop" class="scroll-to-top" aria-label="Remonter en haut de la page" title="Remonter en haut">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="script.js"></script>
</body>
</html>