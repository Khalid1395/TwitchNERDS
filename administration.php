<?php
session_start();
require_once __DIR__ . "/Config/database.php"; // ton fichier de connexion

// Vérification de l'authentification + rôle admin
/* if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier le rôle en BDD
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    echo "❌ Accès refusé : vous n'êtes pas administrateur.";
    exit();
}*/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitch Nerd - Administration</title>
    <link rel="stylesheet" href="styles-administration.css">
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
                <a href="#accueil" class="nav-link active">Tableau de bord</a>
                <a href="#discussions" class="nav-link">Gestion des discussions</a>
                <a href="#membres" class="nav-link">Gestion des membres</a>
                <a href="#recherche" class="nav-link">Recherche</a>
                <a href="index.php" class="nav-link">Retour au site</a>
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
                        <i class="fas fa-lock"></i>
                        <span>Espace Administration</span>
                    </div>
                    <h1 class="hero-title">
                        <span class="title-line">Panneau</span>
                        <span class="title-highlight">d'Administration</span>
                    </h1>
                    <p class="hero-description">
                        Gérez le contenu de votre site Twitch Nerd facilement.
                        <br>Supprimez des commentaires de discussions et gérez les membres.
                    </p>
                </div>
                <div class="hero-visual">
                    <div class="floating-card card-1">
                        <i class="fas fa-comments"></i>
                        <span>Discussions</span>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-users"></i>
                        <span>Membres</span>
                    </div>
                    <div class="floating-card card-3">
                        <i class="fas fa-search"></i>
                        <span>Recherche</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="discussions" class="discussions-section">
            <div class="container">
                <h3>Gestion des discussions</h3>
                <div class="discussions-management-container">
                    <!-- Colonne de gauche : Formulaire de suppression -->
                    <div class="discussions-left-panel">
                        <div class="admin-card">
                            <h4><i class="fas fa-trash-alt"></i> Supprimer un commentaire</h4>
                            <form method="post" action="process_admin.php" class="admin-form">
                                <input type="hidden" name="action" value="delete_comment">
                                
                                <div class="form-group">
                                    <label for="username_search">Rechercher par nom d'utilisateur:</label>
                                    <div class="search-input-container">
                                        <input type="text" id="username_search" name="username_search" placeholder="Commencez à taper le nom d'utilisateur..." autocomplete="off">
                                        <div id="username-suggestions" class="username-suggestions-dropdown"></div>
                                    </div>
                                </div>
                                
                                <div class="form-separator">
                                    <span>OU</span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="comment_id">Rechercher par ID du commentaire:</label>
                                    <input type="number" id="comment_id" name="comment_id" placeholder="ID du commentaire à supprimer">
                                </div>
                                
                                <input type="hidden" id="selected_comment_id" name="selected_comment_id">
                                <button type="submit" class="btn-delete-comment" disabled>
                                    <i class="fas fa-trash-alt"></i> Supprimer le commentaire
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Colonne de droite : Liste des commentaires -->
                    <div class="discussions-right-panel">
                        <div class="admin-card">
                            <h4><i class="fas fa-comments"></i> Commentaires disponibles</h4>
                            <div class="comments-list-container">
                                <div id="comments-list" class="comments-list">
                                    <div class="no-comments-message">
                                        <i class="fas fa-info-circle"></i>
                                        <p>Recherchez un utilisateur ou saisissez un ID de commentaire pour afficher les commentaires disponibles.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="membres" class="section">
            <div class="container">
                <h2>Gestion des membres</h2>
                <div class="admin-form-container">
                    <div class="admin-card">
                        <h4><i class="fas fa-users"></i> Liste des membres</h4>
                        <div class="table-responsive">
                            <table class="members-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Créé le</th>
                                        <th>Mis à jour le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
                                    $users = $stmt->fetchAll();
                                    foreach ($users as $user):
                                    ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['role']) ?></td>
                                        <td><?= $user['created_at'] ?></td>
                                        <td><?= $user['updated_at'] ?></td>
                                        <td class="actions">
                                            <button class="edit-btn" data-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>" data-email="<?= htmlspecialchars($user['email']) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="delete-btn" data-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="admin-card" id="edit-member-form" style="display: none;">
                        <h4><i class="fas fa-edit"></i> Modifier un membre</h4>
                        <form method="post" action="process_admin.php" class="admin-form">
                            <input type="hidden" name="action" value="edit_member">
                            <input type="hidden" id="edit_member_id" name="member_id">
                            <div class="form-group">
                                <label for="edit_username">Nom d'utilisateur:</label>
                                <input type="text" id="edit_username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_email">Email:</label>
                                <input type="email" id="edit_email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_password">Nouveau mot de passe (laisser vide pour ne pas changer):</label>
                                <input type="password" id="edit_password" name="password">
                            </div>
                            <button type="submit" class="btn-primary">Mettre à jour</button>
                            <button type="button" class="btn-secondary" id="cancel-edit">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section id="recherche" class="search-section">
            <div class="container">
                <div class="search-container">
                    <h3>Recherche dans la base de données</h3>
                    <div class="search-box">
                        <form method="get" action="administration.php" style="display: flex; width: 100%;">
                            <input type="text" name="q" id="searchInput" placeholder="Rechercher dans les discussions..." required>
                            <button type="submit" id="searchBtn"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <?php if (isset($_GET['q'])): ?>
                        <div class="search-indicator">
                            <i class="fas fa-search"></i>
                            Résultats pour "<?= htmlspecialchars($_GET['q']) ?>"
                        </div>
                        
                        <div class="search-results">
                            <?php
                            $q = "%" . $_GET['q'] . "%";

                            // Recherche dans discussions
                            $stmt = $pdo->prepare("SELECT d.*, u.username FROM discussions d JOIN users u ON d.users_id = u.id WHERE d.commentary LIKE ? OR u.username LIKE ?");
                            $stmt->execute([$q, $q]);
                            $discussions = $stmt->fetchAll();
                            if ($discussions): ?>
                                <div class="result-section">
                                    <h4>Commentaires de discussions trouvés</h4>
                                    <ul class="result-list">
                                        <?php foreach ($discussions as $d): ?>
                                            <li class="result-item">
                                                <div class="result-header">
                                                    <strong>Commentaire #<?= $d['id'] ?>:</strong> <?= htmlspecialchars(substr($d['commentary'], 0, 100)) ?>...
                                                    <br><small>Par: <?= htmlspecialchars($d['username']) ?> le <?= $d['published_at'] ?></small>
                                                </div>
                                                <div class="result-actions">
                                                    <a href="#" class="btn-action delete" data-id="<?= $d['id'] ?>"><i class="fas fa-trash"></i></a>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($discussions)): ?>
                                <div class="no-results">
                                    <i class="fas fa-search" style="font-size: 3rem; color: #9146ff; margin-bottom: 1rem;"></i>
                                    <h3>Aucun résultat trouvé</h3>
                                    <p>Aucun élément ne correspond à votre recherche "<strong><?= htmlspecialchars($_GET['q']) ?></strong>"</p>
                                    <p>Essayez avec d'autres mots-clés.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Twitch Nerd</h4>
                    <p>Espace administration</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="#discussions">Gestion des discussions</a></li>
                        <li><a href="#membres">Gestion des membres</a></li>
                        <li><a href="#recherche">Recherche</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Retour</h4>
                    <ul>
                        <li><a href="index.php">Retour au site</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> TwitchNerd. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    <script src="script-administration.js"></script>
</body>
</html>

