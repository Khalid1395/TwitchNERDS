<?php
/**
 * Script de test de connexion à la base de données
 */
require_once 'configure/database.php';

echo "<h2>Test de connexion à la base de données</h2>";

try {
    // Test de la connexion
    $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as db_version");
    $result = $stmt->fetch();
    
    echo "<p style='color: green;'>✅ Connexion réussie !</p>";
    echo "<ul>";
    echo "<li><strong>Base de données :</strong> " . htmlspecialchars($result['db_name']) . "</li>";
    echo "<li><strong>Version MySQL :</strong> " . htmlspecialchars($result['db_version']) . "</li>";
    echo "</ul>";
    
    // Lister les tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tables disponibles :</h3>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Aucune table trouvée. Vous devrez peut-être importer le fichier twitchnerd.sql</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Vérifiez :</strong></p>";
    echo "<ul>";
    echo "<li>Que la base de données en ligne est accessible</li>";
    echo "<li>Que le nom d'utilisateur et le mot de passe sont corrects</li>";
    echo "<li>Que la base de données 'dbs14945231' existe</li>";
    echo "<li>Les identifiants dans configure/database.php</li>";
    echo "<li>Que le serveur MySQL est accessible depuis votre hébergeur</li>";
    echo "</ul>";
}
?>

