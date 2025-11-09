<?php
// Initialisation de la session et connexion à la base de données
session_start();
// Centralisation: utiliser config.php pour $pdo et utilitaires
require_once __DIR__ . '/config.php';

// Vérification de l'authentification et des droits d'administration
/*if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté ou n'est pas admin
    header('Location: login.php?error=unauthorized');
    exit;
}*/

// Traitement des actions d'administration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Gestion des discussions
    if ($action === 'delete_comment') {
        // Récupération de l'ID du commentaire à supprimer
        $comment_id = null;
        
        // Vérifier si l'ID est fourni directement
        if (!empty($_POST['comment_id'])) {
            $comment_id = (int)$_POST['comment_id'];
        } 
        // Sinon, vérifier si un ID a été sélectionné via la recherche par username
        elseif (!empty($_POST['selected_comment_id'])) {
            $comment_id = (int)$_POST['selected_comment_id'];
        }
        // Ou via le select des commentaires
        elseif (!empty($_POST['comment_select'])) {
            $comment_id = (int)$_POST['comment_select'];
        }
        
        // Validation de l'ID
        if (!$comment_id) {
            header('Location: administration.php?error=missing_id&section=discussions');
            exit;
        }

        // Suppression du commentaire
        try {
            $stmt = $pdo->prepare("DELETE FROM discussion WHERE id = :comment_id");
            $stmt->bindParam(':comment_id', $comment_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                header('Location: administration.php?success=comment_deleted&section=discussions');
            } else {
                header('Location: administration.php?error=comment_not_found&section=discussions');
            }
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=discussions');
            exit;
        }
    }
    // Édition d'un commentaire (admin ou auteur)
    elseif ($action === 'edit_comment') {
        $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        $comment_text = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';

        if (!$comment_id || $comment_text === '') {
            header('Location: administration.php?error=missing_fields&section=discussions');
            exit;
        }

        if (strlen($comment_text) > 2000) {
            header('Location: administration.php?error=comment_too_long&section=discussions');
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE discussion SET comment = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$comment_text, $comment_id]);
            if ($stmt->rowCount() > 0) {
                header('Location: administration.php?success=comment_updated&section=discussions');
            } else {
                header('Location: administration.php?error=comment_not_found&section=discussions');
            }
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=discussions');
            exit;
        }
    }
    
    // Gestion des FAQ (conservé pour compatibilité)
    elseif ($action === 'add_faq') {
        // Schéma actuel: category, question, answer, keywords, display_order, is_active
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $keywords = trim($_POST['keywords'] ?? '');
        $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 1; // par défaut active

        if ($question === '' || $answer === '' || $category === '') {
            header('Location: administration.php?error=missing_fields&section=faq');
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO faq (category, question, answer, keywords, display_order, is_active, created_at, updated_at)
                                   VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$category, $question, $answer, $keywords, $display_order, $is_active]);
            header('Location: administration.php?success=faq_added&section=faq');
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=faq');
            exit;
        }
    } 
    elseif ($action === 'delete_faq') {
        $faq_id = isset($_POST['faq_id']) ? (int)$_POST['faq_id'] : 0;
        if (!$faq_id) {
            header('Location: administration.php?error=missing_id&section=faq');
            exit;
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM faq WHERE id = ?");
            $stmt->execute([$faq_id]);
            if ($stmt->rowCount() > 0) {
                header('Location: administration.php?success=faq_deleted&section=faq');
            } else {
                header('Location: administration.php?error=faq_not_found&section=faq');
            }
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=faq');
            exit;
        }
    }
    elseif ($action === 'edit_faq') {
        $faq_id = isset($_POST['faq_id']) ? (int)$_POST['faq_id'] : 0;
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $keywords = trim($_POST['keywords'] ?? '');
        $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (!$faq_id || $question === '' || $answer === '' || $category === '') {
            header('Location: administration.php?error=missing_fields&section=faq');
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE faq SET category = ?, question = ?, answer = ?, keywords = ?, display_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$category, $question, $answer, $keywords, $display_order, $is_active, $faq_id]);
            if ($stmt->rowCount() > 0) {
                header('Location: administration.php?success=faq_updated&section=faq');
            } else {
                header('Location: administration.php?error=faq_not_found&section=faq');
            }
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=faq');
            exit;
        }
    }
    elseif ($action === 'toggle_faq_status') {
        $faq_id = isset($_POST['faq_id']) ? (int)$_POST['faq_id'] : 0;
        $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
        if (!$faq_id) {
            header('Location: administration.php?error=missing_id&section=faq');
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE faq SET is_active = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$is_active, $faq_id]);
            header('Location: administration.php?success=faq_status_updated&section=faq');
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=faq');
            exit;
        }
    }
    
    // Gestion des membres
    elseif ($action === 'edit_member') {
        // Récupération des données du formulaire
        $member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        // Validation des données
        if (!$member_id || empty($username) || empty($email)) {
            header('Location: administration.php?error=missing_fields&section=membres');
            exit;
        }

        try {
            // Vérifier si le nom d'utilisateur existe déjà (sauf pour l'utilisateur actuel)
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $member_id]);
            if ($stmt->rowCount() > 0) {
                header('Location: administration.php?error=username_exists&section=membres');
                exit;
            }

            // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $member_id]);
            if ($stmt->rowCount() > 0) {
                header('Location: administration.php?error=email_exists&section=membres');
                exit;
            }

            // Mise à jour des informations de l'utilisateur
            if (!empty($password)) {
                // Si un nouveau mot de passe est fourni, le hacher et mettre à jour
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password_hash = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$username, $email, $hashed_password, $member_id]);
            } else {
                // Sinon, mettre à jour uniquement le nom d'utilisateur et l'email
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$username, $email, $member_id]);
            }
            
            header('Location: administration.php?success=member_updated&section=membres');
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=membres');
            exit;
        }
    }
    elseif ($action === 'delete_member') {
        // Récupération de l'ID du membre à supprimer
        $member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;

        // Validation de l'ID
        if (!$member_id) {
            header('Location: administration.php?error=missing_id&section=membres');
            exit;
        }

        // Empêcher la suppression de son propre compte
        if ($member_id === $_SESSION['user_id']) {
            header('Location: administration.php?error=cannot_delete_self&section=membres');
            exit;
        }

        // Suppression du membre
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$member_id]);
            
            if ($stmt->rowCount() > 0) {
                header('Location: administration.php?success=member_deleted&section=membres');
            } else {
                header('Location: administration.php?error=member_not_found&section=membres');
            }
            exit;
        } catch (PDOException $e) {
            header('Location: administration.php?error=database_error&section=membres');
            exit;
        }
    }
}

// Traitement des requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Recherche d'utilisateurs par nom d'utilisateur
    if ($_POST['action'] === 'search_username') {
        $search = isset($_POST['query']) ? '%' . trim($_POST['query']) . '%' : '';
        
        if (!empty($search)) {
            try {
                $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username LIKE :search LIMIT 10");
                $stmt->bindParam(':search', $search);
                $stmt->execute();
                
                $users = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $users[] = [
                        'id' => $row['id'],
                        'username' => $row['username'],
                        'value' => $row['username']
                    ];
                }
                
                header('Content-Type: application/json');
                echo json_encode($users);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erreur de recherche: ' . $e->getMessage()]);
            }
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }
    
    // Récupération des commentaires d'un utilisateur
    elseif ($_POST['action'] === 'get_user_comments') {
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        
        if ($user_id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT d.id, d.comment AS commentary, d.created_at AS published_at, u.username 
                                     FROM discussion d 
                                     JOIN users u ON d.user_id = u.id 
                                     WHERE d.user_id = :user_id 
                                     ORDER BY d.created_at DESC");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                
                $comments = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $comments[] = [
                        'id' => $row['id'],
                        'commentary' => $row['commentary'],
                        'published_at' => $row['published_at'],
                        'username' => $row['username']
                    ];
                }
                
                header('Content-Type: application/json');
                echo json_encode($comments);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erreur de récupération: ' . $e->getMessage()]);
            }
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }
}
    
    // Recherche FAQ (conservé pour compatibilité)
    elseif (isset($_GET['action']) && $_GET['action'] === 'search_faq') {
    $search = isset($_GET['term']) ? '%' . trim($_GET['term']) . '%' : '';
    
    if (!empty($search)) {
        try {
            $stmt = $pdo->prepare("SELECT id, question FROM faq WHERE question LIKE :search LIMIT 10");
            $stmt->bindParam(':search', $search);
            $stmt->execute();
            
            $faqs = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $faqs[] = [
                    'id' => $row['id'],
                    'value' => $row['question']
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode($faqs);
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erreur de recherche: ' . $e->getMessage()]);
        }
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// Redirection par défaut si aucune action valide n'est spécifiée
header('Location: administration.php');
exit;
?>