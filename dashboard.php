<?php
// Inclure la configuration pour gérer les sessions
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getCurrentUser();
// Rafraîchir le rôle depuis la base pour garantir sa justesse
try {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch();
    if ($row && isset($row['role'])) {
        $_SESSION['role'] = $row['role'];
        $user['role'] = $row['role'];
    }
} catch (PDOException $e) {
    error_log('Erreur récupération rôle sur dashboard: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Forum Twitch</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header" style="background: black;">
        <div class="container">
            <div class="logo">
                <img src="assets/upload/logo_b.png" alt="Twitch Nerd" class="logo-img logo-transparent">
                <img src="assets/upload/logo.png" alt="Twitch Nerd" class="logo-img logo-scrolled">
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Accueil</a>
                <a href="index.php#faq" class="nav-link">FAQ</a>
                <?php
                // L'utilisateur est forcément connecté ici, mais on vérifie quand même
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    // Utilisateur connecté - afficher profil
                    echo '<div class="nav-profile">';
                    echo '<a href="dashboard.php" class="nav-link profile-link active">';
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
                }
                ?>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-message">
                <div class="welcome-text">
                    <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?> !</h1>
                    <p>Gérez votre compte et explorez toutes les fonctionnalités</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="dashboard-card">
                <div class="card-title">
                    <i class="fas fa-user"></i>
                    Informations du compte
                </div>
                <div class="card-content">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 20px 0;" class="account-info-grid">
                        <div>
                            <h3 style="color: #333; margin-bottom: 15px; font-size: 1.2rem;">Informations personnelles</h3>
                            <p style="margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #e1e5e9;">
                                <strong>Nom d'utilisateur :</strong><br>
                                <span style="color: #666; font-size: 1.1rem;"><?php echo htmlspecialchars($user['username']); ?></span>
                            </p>
                            <p style="margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #e1e5e9;">
                                <strong>Email :</strong><br>
                                <span style="color: #666; font-size: 1.1rem;"><?php echo htmlspecialchars($user['email']); ?></span>
                            </p>
                            <p style="margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #e1e5e9;">
                                <strong>Rôle :</strong><br>
                                <span style="color: #666; font-size: 1.1rem; text-transform: capitalize;"><?php echo ucfirst($_SESSION['role'] ?? 'user'); ?></span>
                            </p>
                        </div>
                        
                        <div>
                            <h3 style="color: #333; margin-bottom: 15px; font-size: 1.2rem;">Informations du compte</h3>
                            <p style="margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #e1e5e9;">
                                <strong>Membre depuis :</strong><br>
                                <span style="color: #666; font-size: 1.1rem;"><?php echo date('d/m/Y', strtotime($_SESSION['created_at'] ?? 'now')); ?></span>
                            </p>
                            <p style="margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #e1e5e9;">
                                <strong>Dernière connexion :</strong><br>
                                <span style="color: #666; font-size: 1.1rem;"><?php echo date('d/m/Y à H:i', strtotime($_SESSION['updated_at'] ?? 'now')); ?></span>
                            </p>
                            <p style="margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #e1e5e9;">
                                <strong>Statut :</strong><br>
                                <span style="color: #48bb78; font-size: 1.1rem; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Actif
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 12px; border-left: 4px solid #9146ff;">
                        <h4 style="color: #333; margin-bottom: 10px; font-size: 1.1rem;">
                            <i class="fas fa-info-circle" style="color: #9146ff; margin-right: 8px;"></i>
                            À propos de votre compte
                        </h4>
                        <p style="color: #666; margin: 0; line-height: 1.6;">
                            Votre compte est entièrement configuré et prêt à utiliser. Vous pouvez accéder à toutes les fonctionnalités 
                            du forum Twitch Nerd avec ce compte. Si vous souhaitez modifier vos informations, contactez l'administrateur.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>