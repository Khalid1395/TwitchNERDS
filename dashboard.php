<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('index.html');
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Forum Twitch</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: var(--twitch-gray);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid var(--twitch-gray-light);
        }
        
        .welcome-message {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .welcome-text h1 {
            color: var(--twitch-white);
            font-size: 2rem;
            margin-bottom: 8px;
        }
        
        .welcome-text p {
            color: var(--twitch-text-alt);
            font-size: 1.1rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--twitch-purple) 0%, var(--twitch-purple-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
        
        .logout-btn {
            background: var(--twitch-error);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .logout-btn:hover {
            background: #d32f2f;
        }
        
        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .dashboard-card {
            background: var(--twitch-gray);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid var(--twitch-gray-light);
            transition: var(--transition);
        }
        
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .card-title {
            color: var(--twitch-white);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-content {
            color: var(--twitch-text-alt);
            line-height: 1.6;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid var(--twitch-gray-light);
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .coming-soon {
            background: var(--twitch-purple);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="welcome-message">
                <div class="welcome-text">
                    <h1>Bienvenue, <?= escape($user['username']) ?> !</h1>
                    <p>Vous êtes connecté au Forum Twitch</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                    <button class="logout-btn" onclick="logout()">Déconnexion</button>
                </div>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="dashboard-card">
                <h2 class="card-title">
                    🎮 Profil Utilisateur
                </h2>
                <div class="card-content">
                    <p><strong>Nom d'utilisateur:</strong> <?= escape($user['username']) ?></p>
                    <p><strong>Email:</strong> <?= escape($user['email']) ?></p>
                    <p><strong>Statut:</strong> <span style="color: var(--twitch-success);">Actif</span></p>
                    <p><strong>Membre depuis:</strong> Aujourd'hui</p>
                </div>
            </div>

            <div class="dashboard-card">
                <h2 class="card-title">
                    💬 Forum
                </h2>
                <div class="card-content">
                    <p>Accédez aux discussions de la communauté Twitch</p>
                    <ul class="feature-list">
                        <li>📝 Créer des sujets <span class="coming-soon">Bientôt</span></li>
                        <li>💭 Répondre aux discussions <span class="coming-soon">Bientôt</span></li>
                        <li>👍 Système de votes <span class="coming-soon">Bientôt</span></li>
                        <li>🏷️ Catégories par jeux <span class="coming-soon">Bientôt</span></li>
                    </ul>
                </div>
            </div>

            <div class="dashboard-card">
                <h2 class="card-title">
                    📺 Intégration Twitch
                </h2>
                <div class="card-content">
                    <p>Connectez votre compte Twitch pour plus de fonctionnalités</p>
                    <ul class="feature-list">
                        <li>🔗 Lier compte Twitch <span class="coming-soon">Bientôt</span></li>
                        <li>📊 Statistiques de stream <span class="coming-soon">Bientôt</span></li>
                        <li>🎯 Notifications de live <span class="coming-soon">Bientôt</span></li>
                        <li>👥 Communauté streamers <span class="coming-soon">Bientôt</span></li>
                    </ul>
                </div>
            </div>

            <div class="dashboard-card">
                <h2 class="card-title">
                    ⚙️ Paramètres
                </h2>
                <div class="card-content">
                    <p>Personnalisez votre expérience sur le forum</p>
                    <ul class="feature-list">
                        <li>🖼️ Changer d'avatar <span class="coming-soon">Bientôt</span></li>
                        <li>🔔 Préférences notifications <span class="coming-soon">Bientôt</span></li>
                        <li>🎨 Thème personnalisé <span class="coming-soon">Bientôt</span></li>
                        <li>🔒 Sécurité du compte <span class="coming-soon">Bientôt</span></li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script>
        function logout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                fetch('logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.html';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Redirection forcée en cas d'erreur
                    window.location.href = 'index.html';
                });
            }
        }
    </script>
</body>
</html>