<?php
session_start();
require_once 'config.php';

// Fonction pour supprimer le token "Se souvenir de moi"
function removeRememberToken() {
    if (isset($_COOKIE['remember_token'])) {
        $pdo = getDbConnection();
        
        try {
            // Supprimer le token de la base de données
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt->execute([hash('sha256', $_COOKIE['remember_token'])]);
            
            // Supprimer le cookie
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        } catch (PDOException $e) {
            // Log silencieux en cas d'erreur
            error_log("Erreur lors de la suppression du token: " . $e->getMessage());
        }
    }
}

// Fonction pour logger la déconnexion
function logLogout($userId) {
    if (!$userId) return;
    
    $pdo = getDbConnection();
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) 
            VALUES (?, 'logout', 'Déconnexion utilisateur', ?, ?, NOW())
        ");
        
        $stmt->execute([
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (PDOException $e) {
        error_log("Erreur lors du logging de déconnexion: " . $e->getMessage());
    }
}

// Traitement de la déconnexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    
    // Logger la déconnexion avant de détruire la session
    if ($userId) {
        logLogout($userId);
    }
    
    // Supprimer le token "Se souvenir de moi"
    removeRememberToken();
    
    // Détruire la session
    session_unset();
    session_destroy();
    
    // Supprimer le cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Réponse JSON pour les requêtes AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Déconnexion réussie'
    ]);
    exit;
} else {
    // Redirection directe si accès via GET
    $userId = $_SESSION['user_id'] ?? null;
    
    if ($userId) {
        logLogout($userId);
    }
    
    removeRememberToken();
    session_unset();
    session_destroy();
    
    redirect('index.html');
}
?>