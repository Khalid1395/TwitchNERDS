<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Récupération des articles récents
$recent_articles = getRecentArticles(6);
$featured_faqs = getFeaturedFAQs(5);
$popular_discussions = getPopularDiscussions(5);
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TwitchNerd - Forum Communauté Twitch</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img class="logo-img-size" src="assets/images/logo.png" alt="TwitchNerd">
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link active">Accueil</a>
                    <a href="articles.php" class="nav-link">Articles</a>
                    <a href="discussions.php" class="nav-link">Discussions</a>
                    <a href="faq.php" class="nav-link">FAQ</a>
                </nav>
                <div class="user-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-menu">
                            <?php echo getAvatarHtml($_SESSION['user_id'], $_SESSION['username'], $_SESSION['avatar'] ?? null, 'small', 'user-avatar'); ?>
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <div class="dropdown">
                                <a href="profile.php">Profil</a>
                                <a href="dashboard.php">Tableau de bord</a>
                                <a href="logout.php">Déconnexion</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">Connexion</a>
                        <a href="register.php" class="btn btn-primary">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Bienvenue sur TwitchNerd</h2>
                <p>La communauté francophone pour tous les passionnés de Twitch, streaming et gaming</p>
                <div class="hero-actions">
                    <a href="discussions.php" class="btn btn-primary btn-large">Rejoindre la discussion</a>
                    <a href="articles.php" class="btn btn-outline btn-large">Lire les articles</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="content-grid">
                <!-- Articles récents -->
                <section class="section">
                    <div class="section-header">
                        <h3><i class="fas fa-newspaper"></i> Articles récents</h3>
                        <a href="articles.php" class="btn btn-outline">Voir tout</a>
                    </div>
                    <div class="articles-grid">
                        <?php foreach ($recent_articles as $article): ?>
                        <article class="article-card">
                            <div class="article-image">
                                <img src="<?php echo $article['featured_image'] ?? 'assets/images/default-article.jpg'; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <div class="article-category" style="background-color: <?php echo $article['category_color']; ?>">
                                    <?php echo htmlspecialchars($article['category_name']); ?>
                                </div>
                            </div>
                            <div class="article-content">
                                <h4><a href="article.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h4>
                                <p><?php echo htmlspecialchars($article['excerpt']); ?></p>
                                <div class="article-meta">
                                    <span class="author-info">
                                        <?php echo getAvatarHtml($article['author_id'], $article['author_name'], $article['author_avatar'] ?? null, 'small', 'author-avatar'); ?>
                                        <span><?php echo htmlspecialchars($article['author_name']); ?></span>
                                    </span>
                                    <span><i class="fas fa-calendar"></i> <?php echo formatDate($article['created_at']); ?></span>
                                    <span><i class="fas fa-eye"></i> <?php echo $article['views']; ?></span>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- FAQ en vedette -->
                <section class="section">
                    <div class="section-header">
                        <h3><i class="fas fa-question-circle"></i> FAQ en vedette</h3>
                        <a href="faq.php" class="btn btn-outline">Voir tout</a>
                    </div>
                    <div class="faq-list">
                        <?php foreach ($featured_faqs as $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4><?php echo htmlspecialchars($faq['question']); ?></h4>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Discussions populaires -->
                <section class="section">
                    <div class="section-header">
                        <h3><i class="fas fa-comments"></i> Discussions populaires</h3>
                        <a href="discussions.php" class="btn btn-outline">Voir tout</a>
                    </div>
                    <div class="discussions-list">
                        <?php foreach ($popular_discussions as $discussion): ?>
                        <div class="discussion-item">
                            <div class="discussion-content">
                                <h4><a href="discussion.php?id=<?php echo $discussion['id']; ?>"><?php echo htmlspecialchars($discussion['title']); ?></a></h4>
                                <div class="discussion-meta">
                                    <span class="category" style="background-color: <?php echo $discussion['category_color']; ?>">
                                        <?php echo htmlspecialchars($discussion['category_name']); ?>
                                    </span>
                                    <span class="author-info">
                                        <?php echo getAvatarHtml($discussion['author_id'], $discussion['author_name'], $discussion['author_avatar'] ?? null, 'small', 'author-avatar'); ?>
                                        <span><?php echo htmlspecialchars($discussion['author_name']); ?></span>
                                    </span>
                                    <span><i class="fas fa-eye"></i> <?php echo $discussion['views']; ?></span>
                                </div>
                            </div>
                            <div class="discussion-status">
                                <?php if ($discussion['is_solved']): ?>
                                    <span class="solved"><i class="fas fa-check"></i> Résolu</span>
                                <?php else: ?>
                                    <span class="unsolved"><i class="fas fa-clock"></i> En cours</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>TwitchNerd</h4>
                    <p>La communauté francophone pour les passionnés de Twitch et de streaming.</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="articles.php">Articles</a></li>
                        <li><a href="discussions.php">Discussions</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Communauté</h4>
                    <ul>
                        <li><a href="https://twitch.tv" target="_blank">Twitch</a></li>
                        <li><a href="https://discord.gg" target="_blank">Discord</a></li>
                        <li><a href="https://twitter.com" target="_blank">Twitter</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 TwitchNerd. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
