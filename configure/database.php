<?php
$host = "localhost";
$dbname = "twitchnerd";
$username = "nerd1";
$password = "mdpnerd1";

try {
    // Connexion à la base avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Activer les erreurs PDO en mode Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //echo "✅ Connexion réussie à la base '$dbname'";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage();
}