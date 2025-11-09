<?php
// Centralise sessions, utilitaires et $pdo
require_once __DIR__ . '/config.php';

// Vérification de connexion
if (!isLoggedIn()) {
    redirect('login.php?error=admin_required');
}

// Vérification du rôle (doit être admin)
try {
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    if (!$row || $row['role'] !== 'admin') {
        redirect('index.php?error=admin_required');
    }
    // Synchroniser la session
    $_SESSION['role'] = $row['role'];
} catch (PDOException $e) {
    error_log('Erreur vérification rôle admin: ' . $e->getMessage());
    redirect('index.php?error=admin_required');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Twitch Nerd | Panel d'administration</title>
    <meta name="description" content="Panel d'administration Twitch Nerd. Gérez les FAQ, les commentaires, les membres et le contenu du forum depuis l'interface d'administration.">
    <meta name="keywords" content="administration twitch, panel admin, gestion forum, modération, administration site">
    <meta name="author" content="Twitch Nerd">
    <meta name="robots" content="noindex, nofollow">
    <meta name="language" content="French">
    <link rel="canonical" href="https://wafd.agency/administration.php">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://wafd.agency/administration.php">
    <meta property="og:title" content="Administration - Twitch Nerd">
    <meta property="og:description" content="Panel d'administration du forum Twitch Nerd.">
    <meta property="og:image" content="https://wafd.agency/assets/upload/logo.png">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Administration - Twitch Nerd">
    <meta name="twitter:description" content="Panel d'administration">
    
    <link rel="icon" type="image/png" href="assets/upload/logo.png">
    <link rel="stylesheet" href="styles-administration.css">
    <link rel="stylesheet" href="styles-administration-dashboard.css">
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
                <a href="#comments_admin" class="nav-link">Gestion des commentaires</a>
                <a href="#faq_admin" class="nav-link">Gestion FAQ</a>
                <a href="#membres" class="nav-link">Gestion des membres</a>
                <a href="index.php" class="nav-link">Retour au site</a>
                <?php
                // Menu profil similaire à index.php, avec lien Administration si admin
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
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
                }
                ?>
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

        <section id="comments_admin" class="section">
            <div class="container">
                <h2>Gestion des commentaires</h2>
                <?php
                // Seuil de signalements configurable par l'admin
                $threshold = isset($_SESSION['report_threshold']) ? (int)$_SESSION['report_threshold'] : 3;
                if ($threshold < 1) { $threshold = 1; }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'set_report_threshold') {
                    $newThreshold = isset($_POST['report_threshold']) ? (int)$_POST['report_threshold'] : $threshold;
                    if ($newThreshold < 1) { $newThreshold = 1; }
                    $_SESSION['report_threshold'] = $newThreshold;
                    $threshold = $newThreshold;
                }

                // Récupérer les commentaires signalés (>= seuil de signalements)
                $reportedComments = [];
                $dbError = null;
                try {
                    $sql = "SELECT d.id, d.comment, d.created_at, u.username, COUNT(r.id) AS reports
                            FROM discussion d
                            JOIN users u ON d.user_id = u.id
                            JOIN comment_reports r ON r.comment_id = d.id
                            GROUP BY d.id, d.comment, d.created_at, u.username
                            HAVING reports >= :threshold
                            ORDER BY reports DESC, d.created_at DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
                    $stmt->execute();
                    $reportedComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    // Si la table n'existe pas encore, on ignore et affiche une note
                    $dbError = $e->getMessage();
                    $reportedComments = [];
                }
                ?>

                <div class="admin-card dashboard-card">
                    <h4><i class="fas fa-flag"></i> Commentaires signalés (≥ <?= (int)$threshold ?>)</h4>
                    <form method="post" action="administration.php" class="admin-form inline">
                        <input type="hidden" name="action" value="set_report_threshold">
                        <div class="form-group">
                            <label for="report_threshold">Seuil de signalements</label>
                            <input type="number" id="report_threshold" name="report_threshold" value="<?= (int)$threshold ?>" min="1">
                        </div>
                        <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Appliquer</button>
                        <p class="help-text">Affiche les commentaires ayant au moins <?= (int)$threshold ?> signalements.</p>
                    </form>
                    <?php if ($dbError): ?>
                        <div class="no-results">
                            <p>Note: aucune donnée de signalement disponible (<?= htmlspecialchars($dbError) ?>).</p>
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="members-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Auteur</th>
                                    <th>Commentaire</th>
                                    <th>Signalements</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($reportedComments)): ?>
                                <?php foreach ($reportedComments as $rc): ?>
                                    <tr>
                                        <td><?= (int)$rc['id'] ?></td>
                                        <td><?= htmlspecialchars($rc['username']) ?></td>
                                        <td><?= htmlspecialchars($rc['comment']) ?></td>
                                        <td><span class="badge badge-warning"><?= (int)$rc['reports'] ?></span></td>
                                        <td><?= htmlspecialchars($rc['created_at']) ?></td>
                                        <td>
                                            <form method="post" action="process_admin.php" style="display:inline-block;">
                                                <input type="hidden" name="action" value="delete_comment">
                                                <input type="hidden" name="comment_id" value="<?= (int)$rc['id'] ?>">
                                                <button type="submit" class="delete-btn" onclick="return confirm('Supprimer ce commentaire signalé ?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center;">Aucun commentaire signalé pour le moment</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq_admin" class="section">
            <div class="container">
                <h2>Gestion FAQ</h2>
                <?php
                try {
                    $stmtFaq = $pdo->query("SELECT id, category, question, display_order, is_active FROM faq ORDER BY display_order ASC, id ASC");
                    $faqs = $stmtFaq->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $faqs = [];
                }
                $categories = ['streaming','technical','monetisation','community'];
                ?>

                <div class="admin-card dashboard-card">
                    <h4><i class="fas fa-plus-circle"></i> Ajouter une FAQ</h4>
                    <form method="post" action="process_admin.php" class="admin-form">
                        <input type="hidden" name="action" value="add_faq">
                        <div class="form-group">
                            <label for="add_faq_category">Catégorie</label>
                            <select id="add_faq_category" name="category" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars(ucfirst($cat)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_faq_question">Question</label>
                            <input type="text" id="add_faq_question" name="question" required>
                        </div>
                        <div class="form-group">
                            <label for="add_faq_answer">Réponse</label>
                            <textarea id="add_faq_answer" name="answer" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="add_faq_keywords">Mots-clés (séparés par des virgules)</label>
                            <input type="text" id="add_faq_keywords" name="keywords" placeholder="ex: streaming, bitrate, OBS">
                        </div>
                        <div class="form-group">
                            <label for="add_faq_display_order">Ordre d’affichage</label>
                            <input type="number" id="add_faq_display_order" name="display_order" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" checked> Activer
                            </label>
                        </div>
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Créer</button>
                    </form>
                </div>

                <div class="admin-card dashboard-card">
                    <h4><i class="fas fa-list"></i> Liste des FAQ</h4>
                    <div class="table-responsive">
                        <table class="members-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Catégorie</th>
                                    <th>Question</th>
                                    <th>Ordre</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($faqs)): ?>
                                <?php foreach ($faqs as $f): ?>
                                    <tr>
                                        <td><?= (int)$f['id'] ?></td>
                                        <td><?= htmlspecialchars($f['category']) ?></td>
                                        <td><?= htmlspecialchars($f['question']) ?></td>
                                        <td><?= (int)$f['display_order'] ?></td>
                                        <td>
                                            <?php if ((int)$f['is_active'] === 1): ?>
                                                <span class="badge badge-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="edit-btn btn-edit-faq"
                                                    data-id="<?= (int)$f['id'] ?>"
                                                    data-category="<?= htmlspecialchars($f['category']) ?>"
                                                    data-question="<?= htmlspecialchars($f['question']) ?>"
                                                    data-display_order="<?= (int)$f['display_order'] ?>"
                                                    data-is_active="<?= (int)$f['is_active'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="post" action="process_admin.php" style="display:inline-block; margin-left: .25rem;">
                                                <input type="hidden" name="action" value="delete_faq">
                                                <input type="hidden" name="faq_id" value="<?= (int)$f['id'] ?>">
                                                <button type="submit" class="delete-btn" onclick="return confirm('Supprimer cette FAQ ?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <form method="post" action="process_admin.php" style="display:inline-block; margin-left: .25rem;">
                                                <input type="hidden" name="action" value="toggle_faq_status">
                                                <input type="hidden" name="faq_id" value="<?= (int)$f['id'] ?>">
                                                <input type="hidden" name="is_active" value="<?= (int)$f['is_active'] === 1 ? 0 : 1 ?>">
                                                <button type="submit" class="toggle-btn">
                                                    <?= (int)$f['is_active'] === 1 ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>' ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center;">Aucune FAQ trouvée</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal d'édition FAQ -->
                <div id="faqEditModal" class="modal" aria-hidden="true" role="dialog" aria-modal="true" style="display:none;">
                    <div class="modal-overlay" data-close="true"></div>
                    <div class="modal-dialog dashboard-card" role="document">
                        <div class="modal-header">
                            <h4><i class="fas fa-edit"></i> Modifier une FAQ</h4>
                            <button type="button" class="modal-close" aria-label="Fermer">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="process_admin.php" class="admin-form" id="faq-edit-form">
                                <input type="hidden" name="action" value="edit_faq">
                                <input type="hidden" id="edit_faq_id" name="faq_id">
                                <div class="form-group">
                                    <label for="edit_faq_category">Catégorie</label>
                                    <select id="edit_faq_category" name="category" required>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars(ucfirst($cat)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_faq_question">Question</label>
                                    <input type="text" id="edit_faq_question" name="question" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_faq_answer">Réponse</label>
                                    <textarea id="edit_faq_answer" name="answer" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="edit_faq_keywords">Mots-clés</label>
                                    <input type="text" id="edit_faq_keywords" name="keywords">
                                </div>
                                <div class="form-group">
                                    <label for="edit_faq_display_order">Ordre d’affichage</label>
                                    <input type="number" id="edit_faq_display_order" name="display_order" value="0" min="0">
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="edit_faq_is_active" name="is_active"> Activer
                                    </label>
                                </div>
                                <div class="modal-actions">
                                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                                    <button type="button" class="btn-secondary" id="cancel-faq-edit">Annuler</button>
                                </div>
                            </form>
                            <p class="help-text">Cliquez sur "Modifier" dans la liste des FAQ pour ouvrir ce formulaire.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="membres" class="section">
            <div class="container">
                <h2>Gestion des membres</h2>
                <div class="admin-form-container">
                    <div class="admin-card dashboard-card">
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

                    <!-- Modal d'édition de membre -->
                    <div id="memberEditModal" class="modal" aria-hidden="true" role="dialog" aria-modal="true" style="display:none;">
                        <div class="modal-overlay" data-close="true"></div>
                        <div class="modal-dialog dashboard-card" role="document">
                            <div class="modal-header">
                                <h4><i class="fas fa-user-edit"></i> Modifier un membre</h4>
                                <button type="button" class="modal-close" aria-label="Fermer">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="process_admin.php" class="admin-form" id="member-edit-form">
                                    <input type="hidden" name="action" value="edit_member">
                                    <input type="hidden" id="edit_member_id" name="member_id">
                                    <div class="form-group">
                                        <label for="edit_username">Nom d'utilisateur</label>
                                        <input type="text" id="edit_username" name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_email">Email</label>
                                        <input type="email" id="edit_email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                        <input type="password" id="edit_password" name="password">
                                    </div>
                                    <div class="modal-actions">
                                        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                                        <button type="button" class="btn-secondary" id="cancel-member-edit">Annuler</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
                            $stmt = $pdo->prepare("SELECT d.*, u.username FROM discussion d JOIN users u ON d.user_id = u.id WHERE d.comment LIKE ? OR u.username LIKE ?");
                            $stmt->execute([$q, $q]);
                            $discussions = $stmt->fetchAll();
                            if ($discussions): ?>
                                <div class="result-section">
                                    <h4>Commentaires de discussions trouvés</h4>
                                    <ul class="result-list">
                                        <?php foreach ($discussions as $d): ?>
                                            <li class="result-item">
                                                <div class="result-header">
                                                    <strong>Commentaire #<?= $d['id'] ?>:</strong> <?= htmlspecialchars(substr($d['comment'], 0, 100)) ?>...
                                                    <br><small>Par: <?= htmlspecialchars($d['username']) ?> le <?= date('Y-m-d H:i', strtotime($d['created_at'])) ?></small>
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
                        <li><a href="#comments_admin">Gestion des commentaires</a></li>
                        <li><a href="#faq_admin">Gestion FAQ</a></li>
                        <li><a href="#membres">Gestion des membres</a></li>
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
    
    <!-- Bouton remonter en haut -->
    <button id="scrollToTop" class="scroll-to-top" aria-label="Remonter en haut de la page" title="Remonter en haut">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script src="script-administration.js"></script>
</body>
</html>

