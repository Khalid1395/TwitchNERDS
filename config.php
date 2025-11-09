<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la configuration de base de données existante
require_once 'configure/database.php';

// Configuration pour auth.php (format attendu)
$db_config = [
    'host' => 'localhost',
    'port' => '8888',
    'dbname' => 'twitchnerd',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8mb4'
];

// Fonctions utilitaires
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role'] ?? 'user'
    ];
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Vérification du token "Se souvenir de moi"
if (!isLoggedIn() && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $hashedToken = hash('sha256', $token);
    
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.email, u.role 
            FROM users u 
            JOIN remember_tokens rt ON u.id = rt.user_id 
            WHERE rt.token = ? AND rt.expires_at > NOW()
        ");
        $stmt->execute([$hashedToken]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
        }
    } catch (PDOException $e) {
        // Erreur silencieuse
        error_log("Erreur lors de la vérification du token: " . $e->getMessage());
    }
}
?>
