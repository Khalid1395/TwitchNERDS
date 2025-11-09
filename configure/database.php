<?php
/**
 * Configuration de la base de données pour TwitchNERDS
 * Configuration pour base de données en ligne
 */

// Configuration de la base de données en ligne IONOS
$host = 'db5018973790.hosting-data.io';
$port = '3306'; // Port MySQL standard (peut être omis si 3306)
$dbname = 'dbs14945231';
$username = 'dbu1451358';
$password = 'NevoProjet@13013';

try {
    // Connexion à la base de données MySQL via PDO
    // Pour IONOS, on peut omettre le port s'il est 3306
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Connexion établie avec succès
    
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    // Si localhost ne fonctionne pas, essayez avec le host distant
    // Vous pouvez trouver le host exact dans votre panneau IONOS > Bases de données
    die("Erreur de connexion à la base de données : " . $e->getMessage() . 
        "<br><br>Vérifiez dans votre panneau IONOS > Bases de données > Informations de connexion le host MySQL exact.");
}

// Fonction utilitaire pour obtenir la connexion PDO
function getDB() {
    global $pdo;
    return $pdo;
}

// Fonction pour tester la connexion
function testConnection() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>