<?php
/**
 * API pour gérer les commentaires sur les questions FAQ
 */
require_once 'config.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté pour les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour commenter'
    ]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            // Récupérer les commentaires d'une question FAQ
            $faq_id = $_GET['faq_id'] ?? null;
            
            if (!$faq_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de question FAQ requis'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    d.id,
                    d.faq_id,
                    d.comment,
                    d.created_at,
                    d.updated_at,
                    u.id as user_id,
                    u.username,
                    u.email
                FROM discussion d
                JOIN users u ON d.user_id = u.id
                WHERE d.faq_id = ?
                ORDER BY d.created_at ASC
            ");
            
            $stmt->execute([$faq_id]);
            $comments = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'comments' => $comments
            ]);
            break;
            
        case 'add':
            // Ajouter un nouveau commentaire
            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour commenter'
                ]);
                exit;
            }
            
            $faq_id = $_POST['faq_id'] ?? null;
            $comment = trim($_POST['comment'] ?? '');
            
            if (!$faq_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de question FAQ requis'
                ]);
                exit;
            }
            
            if (empty($comment)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le commentaire ne peut pas être vide'
                ]);
                exit;
            }
            
            if (strlen($comment) > 2000) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le commentaire est trop long (maximum 2000 caractères)'
                ]);
                exit;
            }
            
            $user_id = $_SESSION['user_id'];
            
            $stmt = $pdo->prepare("
                INSERT INTO discussion (faq_id, user_id, comment, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            
            $stmt->execute([$faq_id, $user_id, $comment]);
            
            $comment_id = $pdo->lastInsertId();
            
            // Récupérer le commentaire créé avec les infos utilisateur
            $stmt = $pdo->prepare("
                SELECT 
                    d.id,
                    d.faq_id,
                    d.comment,
                    d.created_at,
                    d.updated_at,
                    u.id as user_id,
                    u.username,
                    u.email
                FROM discussion d
                JOIN users u ON d.user_id = u.id
                WHERE d.id = ?
            ");
            
            $stmt->execute([$comment_id]);
            $newComment = $stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'message' => 'Commentaire ajouté avec succès',
                'comment' => $newComment
            ]);
            break;
            
        case 'delete':
            // Supprimer un commentaire (seul l'auteur peut supprimer)
            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vous devez être connecté'
                ]);
                exit;
            }
            
            $comment_id = $_POST['comment_id'] ?? null;
            
            if (!$comment_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de commentaire requis'
                ]);
                exit;
            }
            
            $user_id = $_SESSION['user_id'];
            $user_role = $_SESSION['role'] ?? 'user';
            
            // Vérifier que l'utilisateur est l'auteur du commentaire ou un admin
            $stmt = $pdo->prepare("
                SELECT user_id FROM discussion WHERE id = ?
            ");
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch();
            
            if (!$comment) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Commentaire introuvable'
                ]);
                exit;
            }
            
            if ($comment['user_id'] != $user_id && $user_role !== 'admin') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM discussion WHERE id = ?");
            $stmt->execute([$comment_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Commentaire supprimé avec succès'
            ]);
            break;
        case 'report':
            // Signaler un commentaire (anti-duplication par utilisateur)
            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour signaler'
                ]);
                exit;
            }

            $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
            $user_id = $_SESSION['user_id'] ?? 0;
            if (!$comment_id || !$user_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Paramètres invalides'
                ]);
                exit;
            }

            try {
                // Vérifier duplication
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM comment_reports WHERE comment_id = ? AND user_id = ?");
                $stmt->execute([$comment_id, $user_id]);
                $exists = (int)$stmt->fetchColumn() > 0;
                if ($exists) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Vous avez déjà signalé ce commentaire'
                    ]);
                    exit;
                }

                // Insérer signalement
                $stmt = $pdo->prepare("INSERT INTO comment_reports (comment_id, user_id, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$comment_id, $user_id]);

                // Compter total des signalements
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM comment_reports WHERE comment_id = ?");
                $stmt->execute([$comment_id]);
                $count = (int)$stmt->fetchColumn();

                echo json_encode([
                    'success' => true,
                    'message' => 'Signalement enregistré',
                    'report_count' => $count
                ]);
            } catch (PDOException $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur serveur: ' . $e->getMessage()
                ]);
            }
            break;
        
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action non valide'
            ]);
            break;
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>

