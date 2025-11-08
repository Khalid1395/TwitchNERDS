<?php
/**
 * API pour récupérer les questions FAQ depuis la base de données
 */
require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'get_all';
$category = $_GET['category'] ?? null;

try {
    switch ($action) {
        case 'get_all':
            // Récupérer toutes les FAQ actives
            if ($category && $category !== 'all') {
                $stmt = $pdo->prepare("
                    SELECT id, category, question, answer, keywords, display_order
                    FROM faq
                    WHERE is_active = 1 AND category = ?
                    ORDER BY display_order ASC, id ASC
                ");
                $stmt->execute([$category]);
            } else {
                $stmt = $pdo->prepare("
                    SELECT id, category, question, answer, keywords, display_order
                    FROM faq
                    WHERE is_active = 1
                    ORDER BY display_order ASC, id ASC
                ");
                $stmt->execute();
            }
            
            $faqs = $stmt->fetchAll();
            
            // Convertir les keywords en tableau si c'est une chaîne
            foreach ($faqs as &$faq) {
                if (is_string($faq['keywords'])) {
                    $faq['keywords'] = explode(',', $faq['keywords']);
                }
            }
            
            echo json_encode([
                'success' => true,
                'faqs' => $faqs
            ]);
            break;
            
        case 'get_by_id':
            // Récupérer une FAQ par son ID
            $faq_id = $_GET['id'] ?? null;
            
            if (!$faq_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID FAQ requis'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("
                SELECT id, category, question, answer, keywords, display_order
                FROM faq
                WHERE id = ? AND is_active = 1
            ");
            $stmt->execute([$faq_id]);
            $faq = $stmt->fetch();
            
            if (!$faq) {
                echo json_encode([
                    'success' => false,
                    'message' => 'FAQ introuvable'
                ]);
                exit;
            }
            
            // Convertir les keywords en tableau si c'est une chaîne
            if (is_string($faq['keywords'])) {
                $faq['keywords'] = explode(',', $faq['keywords']);
            }
            
            echo json_encode([
                'success' => true,
                'faq' => $faq
            ]);
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

