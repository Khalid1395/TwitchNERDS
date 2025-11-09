<?php
/**
 * Configuration de la base de données pour TwitchNERDS
 * Configuration pour MAMP (localhost:8888)
 */

// Configuration de la base de données
$host = 'localhost';
$port = '80';
$dbname = 'twitchnerd';
$username = 'root';
$password = 'root';

try {
    // Connexion à la base de données MySQL via PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Connexion établie avec succès
    
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die("Erreur de connexion à la base de données : " . $e->getMessage());
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