<?php
// Fonctions utilitaires pour le forum

// Fonction pour formater les dates
function formatDate($date) {
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 3600) {
        return 'Il y a ' . floor($diff / 60) . ' minutes';
    } elseif ($diff < 86400) {
        return 'Il y a ' . floor($diff / 3600) . ' heures';
    } elseif ($diff < 2592000) {
        return 'Il y a ' . floor($diff / 86400) . ' jours';
    } else {
        return date('d/m/Y', $timestamp);
    }
}

// Fonction pour nettoyer les données
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Fonction pour générer un slug
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Fonction pour vérifier si l'utilisateur est modérateur
function isModerator() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['moderator', 'admin']);
}

// Fonction pour récupérer les articles récents
function getRecentArticles($limit = 6) {
    global $pdo;
    
    $limit = (int)$limit; // S'assurer que c'est un entier
    $stmt = $pdo->prepare("
        SELECT a.*, u.username as author_name, u.id as author_id, u.avatar as author_avatar, c.name as category_name, c.color as category_color
        FROM articles a
        JOIN users u ON a.author_id = u.id
        JOIN categories c ON a.category_id = c.id
        WHERE a.is_published = 1
        ORDER BY a.created_at DESC
        LIMIT $limit
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Fonction pour récupérer les FAQ en vedette
function getFeaturedFAQs($limit = 5) {
    global $pdo;
    
    $limit = (int)$limit; // S'assurer que c'est un entier
    $stmt = $pdo->prepare("
        SELECT f.*, c.name as category_name, c.color as category_color
        FROM faqs f
        JOIN categories c ON f.category_id = c.id
        WHERE f.is_featured = 1
        ORDER BY f.views DESC
        LIMIT $limit
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Fonction pour récupérer les discussions populaires
function getPopularDiscussions($limit = 5) {
    global $pdo;
    
    $limit = (int)$limit; // S'assurer que c'est un entier
    $stmt = $pdo->prepare("
        SELECT d.*, u.username as author_name, u.id as author_id, u.avatar as author_avatar, c.name as category_name, c.color as category_color
        FROM discussions d
        JOIN users u ON d.author_id = u.id
        JOIN categories c ON d.category_id = c.id
        ORDER BY d.views DESC, d.created_at DESC
        LIMIT $limit
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Fonction pour récupérer toutes les catégories
function getAllCategories() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

// Fonction pour récupérer un article par ID
function getArticleById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT a.*, u.username as author_name, u.avatar as author_avatar, u.id as author_id, c.name as category_name, c.color as category_color
        FROM articles a
        JOIN users u ON a.author_id = u.id
        JOIN categories c ON a.category_id = c.id
        WHERE a.id = ? AND a.is_published = 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Fonction pour récupérer une discussion par ID
function getDiscussionById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT d.*, u.username as author_name, u.avatar as author_avatar, u.id as author_id, c.name as category_name, c.color as category_color
        FROM discussions d
        JOIN users u ON d.author_id = u.id
        JOIN categories c ON d.category_id = c.id
        WHERE d.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Fonction pour récupérer les réponses d'une discussion
function getDiscussionReplies($discussion_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT r.*, u.username as author_name, u.avatar as author_avatar, u.id as author_id
        FROM replies r
        JOIN users u ON r.author_id = u.id
        WHERE r.discussion_id = ?
        ORDER BY r.created_at ASC
    ");
    $stmt->execute([$discussion_id]);
    return $stmt->fetchAll();
}

// Fonction pour récupérer les commentaires d'un article
function getArticleComments($article_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT c.*, u.username as author_name, u.avatar as author_avatar, u.id as author_id
        FROM article_comments c
        JOIN users u ON c.author_id = u.id
        WHERE c.article_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$article_id]);
    return $stmt->fetchAll();
}

// Fonction pour incrémenter les vues
function incrementViews($table, $id) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE $table SET views = views + 1 WHERE id = ?");
    $stmt->execute([$id]);
}

// Fonction pour créer un nouvel article
function createArticle($title, $content, $excerpt, $author_id, $category_id, $featured_image = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO articles (title, content, excerpt, author_id, category_id, featured_image, is_published)
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");
    return $stmt->execute([$title, $content, $excerpt, $author_id, $category_id, $featured_image]);
}

// Fonction pour créer une nouvelle discussion
function createDiscussion($title, $content, $author_id, $category_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO discussions (title, content, author_id, category_id)
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$title, $content, $author_id, $category_id]);
}

// Fonction pour créer une nouvelle réponse
function createReply($content, $author_id, $discussion_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO replies (content, author_id, discussion_id)
        VALUES (?, ?, ?)
    ");
    return $stmt->execute([$content, $author_id, $discussion_id]);
}

// Fonction pour créer un nouveau commentaire
function createComment($content, $author_id, $article_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO article_comments (content, author_id, article_id)
        VALUES (?, ?, ?)
    ");
    return $stmt->execute([$content, $author_id, $article_id]);
}

// Fonction pour créer une nouvelle FAQ
function createFAQ($question, $answer, $category_id, $author_id, $is_featured = false) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO faqs (question, answer, category_id, author_id, is_featured)
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$question, $answer, $category_id, $author_id, $is_featured]);
}

// Fonction pour rechercher dans les articles
function searchArticles($query, $category_id = null) {
    global $pdo;
    
    $sql = "
        SELECT a.*, u.username as author_name, u.id as author_id, u.avatar as author_avatar, c.name as category_name, c.color as category_color
        FROM articles a
        JOIN users u ON a.author_id = u.id
        JOIN categories c ON a.category_id = c.id
        WHERE a.is_published = 1 AND (a.title LIKE ? OR a.content LIKE ? OR a.excerpt LIKE ?)
    ";
    
    $params = ["%$query%", "%$query%", "%$query%"];
    
    if ($category_id) {
        $sql .= " AND a.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY a.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Fonction pour rechercher dans les discussions
function searchDiscussions($query, $category_id = null) {
    global $pdo;
    
    $sql = "
        SELECT d.*, u.username as author_name, u.id as author_id, u.avatar as author_avatar, c.name as category_name, c.color as category_color
        FROM discussions d
        JOIN users u ON d.author_id = u.id
        JOIN categories c ON d.category_id = c.id
        WHERE d.title LIKE ? OR d.content LIKE ?
    ";
    
    $params = ["%$query%", "%$query%"];
    
    if ($category_id) {
        $sql .= " AND d.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY d.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Fonction pour générer un avatar aléatoire basé sur l'ID utilisateur
function generateRandomAvatar($user_id, $username = '') {
    // Liste de couleurs pour les avatars
    $colors = [
        '#9146FF', '#00D4AA', '#FF6B6B', '#4ECDC4', '#FFE66D', 
        '#A8E6CF', '#FFB3BA', '#FF9A9E', '#A8E6CF', '#DDA0DD',
        '#98FB98', '#F0E68C', '#FFB6C1', '#87CEEB', '#DDA0DD'
    ];
    
    // Liste d'icônes/emojis pour les avatars
    $icons = [
        '🎮', '📺', '💻', '👥', '❓', '🎯', '🚀', '⭐', '🔥', '💎',
        '🎨', '🎵', '🎪', '🎭', '🎲', '🎸', '🎺', '🎻', '🎹', '🎤'
    ];
    
    // Utiliser l'ID utilisateur pour générer des valeurs cohérentes
    $colorIndex = $user_id % count($colors);
    $iconIndex = $user_id % count($icons);
    
    // Si on a un nom d'utilisateur, utiliser la première lettre
    $displayText = $username ? strtoupper(substr($username, 0, 1)) : $icons[$iconIndex];
    
    return [
        'color' => $colors[$colorIndex],
        'text' => $displayText,
        'icon' => $icons[$iconIndex]
    ];
}

// Fonction pour obtenir l'avatar d'un utilisateur (aléatoire ou personnalisé)
function getUserAvatar($user_id, $username = '', $custom_avatar = null) {
    if ($custom_avatar && file_exists($custom_avatar)) {
        return $custom_avatar;
    }
    
    return generateRandomAvatar($user_id, $username);
}

// Fonction pour générer le HTML de l'avatar
function getAvatarHtml($user_id, $username = '', $custom_avatar = null, $size = 'medium', $class = '') {
    $avatar = getUserAvatar($user_id, $username, $custom_avatar);
    
    if (is_string($avatar)) {
        // Avatar personnalisé (image)
        return '<img src="' . htmlspecialchars($avatar) . '" alt="Avatar de ' . htmlspecialchars($username) . '" class="avatar avatar-' . $size . ' ' . $class . '">';
    } else {
        // Avatar aléatoire (couleur + texte/icône)
        $style = 'background-color: ' . $avatar['color'] . '; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;';
        return '<div class="avatar avatar-' . $size . ' ' . $class . '" style="' . $style . '">' . htmlspecialchars($avatar['text']) . '</div>';
    }
}
?>
