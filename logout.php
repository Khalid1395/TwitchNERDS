<?php
require_once 'config.php';

// Vérifier si c'est une requête POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    
    // Logger la déconnexion si l'utilisateur est connecté
    if ($userId) {
        try {
            require_once 'configue/database.php';
            
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
    
    // Supprimer le token "Se souvenir de moi" si il existe
    if (isset($_COOKIE['remember_token'])) {
        try {
            require_once 'configue/database.php';
            
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt->execute([hash('sha256', $_COOKIE['remember_token'])]);
            
            // Supprimer le cookie
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du token: " . $e->getMessage());
        }
    }
    
    // Détruire la session
    session_unset();
    session_destroy();
    
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
    
    // Logger la déconnexion si l'utilisateur est connecté
    if ($userId) {
        try {
            require_once 'configue/database.php';
            
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
    
    // Supprimer le token "Se souvenir de moi" si il existe
    if (isset($_COOKIE['remember_token'])) {
        try {
            require_once 'configue/database.php';
            
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt->execute([hash('sha256', $_COOKIE['remember_token'])]);
            
            // Supprimer le cookie
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du token: " . $e->getMessage());
        }
    }
    
    // Détruire la session
    session_unset();
    session_destroy();
    
    // Rediriger vers la page d'accueil
    redirect('index.php');
}
?>