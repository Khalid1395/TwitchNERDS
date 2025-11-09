<?php
require_once 'config.php';

// Classe pour la gestion de l'authentification
class AuthManager {
    private $pdo;
    
    public function __construct() {
        try {
            // Ancienne connexion locale (via $db_config) — commentée pour centraliser via database.php
            // $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['dbname']};charset={$db_config['charset']}";
            // $this->pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
            //     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            //     PDO::ATTR_EMULATE_PREPARES => false
            // ]);

            // Nouvelle connexion: utilise le PDO global exposé par configure/database.php
            global $pdo;
            $this->pdo = $pdo;
            if (!$this->pdo) {
                throw new PDOException('Connexion PDO non initialisée depuis configure/database.php');
            }
        } catch (PDOException $e) {
            $this->sendJsonResponse(false, 'Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }
    
    // Inscription d'un nouvel utilisateur
    public function register($username, $email, $password, $confirmPassword) {
        // Validation des données
        $validation = $this->validateRegistrationData($username, $email, $password, $confirmPassword);
        if (!$validation['valid']) {
            $this->sendJsonResponse(false, $validation['message']);
        }
        
        // Vérifier si l'utilisateur existe déjà
        if ($this->userExists($username, $email)) {
            $this->sendJsonResponse(false, 'Un compte avec ce nom d\'utilisateur ou cette adresse email existe déjà');
        }
        
        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insérer l'utilisateur en base
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password_hash, created_at, updated_at, role) 
                VALUES (?, ?, ?, NOW(), NOW(), 'user')
            ");
            
            $stmt->execute([$username, $email, $hashedPassword]);
            
            $userId = $this->pdo->lastInsertId();
            
            // Log de l'inscription
            $this->logActivity($userId, 'register', 'Inscription réussie');
            
            // Créer la session automatiquement après inscription
            $user = [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'role' => 'user'
            ];
            $this->createUserSession($user, false);
            
            $this->sendJsonResponse(true, 'Compte créé avec succès', ['redirect' => 'dashboard.php']);
            
        } catch (PDOException $e) {
            $this->sendJsonResponse(false, 'Erreur lors de la création du compte');
        }
    }
    
    // Connexion d'un utilisateur
    public function login($emailOrUsername, $password, $remember = false) {
        // Validation des données
        if (empty($emailOrUsername) || empty($password)) {
            $this->sendJsonResponse(false, 'Tous les champs sont requis');
        }
        
        // Rechercher l'utilisateur
        $user = $this->findUser($emailOrUsername);
        
        if (!$user) {
            $this->sendJsonResponse(false, 'Identifiants incorrects');
        }
        
        // Vérifier le mot de passe
        if (!password_verify($password, $user['password_hash'])) {
            // Log de tentative de connexion échouée
            $this->logActivity($user['id'], 'login_failed', 'Tentative de connexion avec mot de passe incorrect');
            $this->sendJsonResponse(false, 'Identifiants incorrects');
        }
        
        // Vérifier si le compte existe (pas de vérification is_active car colonne n'existe pas)
        
        // Créer la session
        $this->createUserSession($user, $remember);
        
        // Log de connexion réussie
        $this->logActivity($user['id'], 'login_success', 'Connexion réussie');
        
        $this->sendJsonResponse(true, 'Connexion réussie', ['redirect' => 'dashboard.php']);
    }
    
    // Validation des données d'inscription
    private function validateRegistrationData($username, $email, $password, $confirmPassword) {
        // Validation nom d'utilisateur
        if (empty($username) || strlen($username) < 3) {
            return ['valid' => false, 'message' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères'];
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return ['valid' => false, 'message' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres et underscores'];
        }
        
        // Validation email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Veuillez entrer une adresse email valide'];
        }
        
        // Validation mot de passe
        if (empty($password) || strlen($password) < 8) {
            return ['valid' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères'];
        }
        
        if (!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            return ['valid' => false, 'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre'];
        }
        
        // Validation confirmation mot de passe
        if ($password !== $confirmPassword) {
            return ['valid' => false, 'message' => 'Les mots de passe ne correspondent pas'];
        }
        
        return ['valid' => true];
    }
    
    // Vérifier si un utilisateur existe
    private function userExists($username, $email) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() !== false;
    }
    
    // Rechercher un utilisateur par email ou nom d'utilisateur
    private function findUser($emailOrUsername) {
        $stmt = $this->pdo->prepare("
            SELECT id, username, email, password_hash, role, created_at 
            FROM users 
            WHERE email = ? OR username = ?
        ");
        $stmt->execute([$emailOrUsername, $emailOrUsername]);
        return $stmt->fetch();
    }
    
    // Créer une session utilisateur
    private function createUserSession($user, $remember = false) {
        // Régénérer l'ID de session pour la sécurité
        session_regenerate_id(true);
        
        // Stocker les informations utilisateur en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Gestion du "Se souvenir de moi"
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (30 * 24 * 60 * 60); // 30 jours
            
            // Stocker le token en base
            $stmt = $this->pdo->prepare("
                INSERT INTO remember_tokens (user_id, token, expires_at) 
                VALUES (?, ?, FROM_UNIXTIME(?))
                ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)
            ");
            $stmt->execute([$user['id'], hash('sha256', $token), $expires]);
            
            // Créer le cookie
            setcookie('remember_token', $token, $expires, '/', '', true, true);
        }
        
        // Mettre à jour la dernière connexion (si la colonne existe)
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
        } catch (PDOException $e) {
            // Ignorer si la colonne n'existe pas
        }
    }
    
    // Logger les activités
    private function logActivity($userId, $action, $description) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (PDOException $e) {
            // Log silencieux en cas d'erreur
            error_log("Erreur lors du logging: " . $e->getMessage());
        }
    }
    
    // Envoyer une réponse JSON
    private function sendJsonResponse($success, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}

// Traitement des requêtes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF basique
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Ancienne instanciation avec configuration locale — commentée pour centraliser via database.php
    // $auth = new AuthManager($db_config);
    
    // Nouvelle instanciation: utilise le PDO fourni par configure/database.php
    $auth = new AuthManager();
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'register':
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            $auth->register($username, $email, $password, $confirmPassword);
            break;
            
        case 'login':
            $emailOrUsername = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            
            $auth->login($emailOrUsername, $password, $remember);
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Action non valide']);
            break;
    }
} else {
    // Redirection si accès direct
    header('Location: index.php');
    exit;
}
?>