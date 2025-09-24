-- Base de données pour le forum Twitch
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS twitch_forum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE twitch_forum;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    twitch_username VARCHAR(50) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_admin BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_active (is_active),
    INDEX idx_created (created_at)
);

-- Table des tokens de "Se souvenir de moi"
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_token (user_id),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
);

-- Table des logs d'activité
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);

-- Table des tentatives de connexion (sécurité)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email_or_username VARCHAR(255) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_ip (ip_address),
    INDEX idx_email (email_or_username),
    INDEX idx_created (created_at)
);

-- Table des sessions (optionnel, pour une gestion avancée des sessions)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);

-- Table des réinitialisations de mot de passe
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
);

-- Insertion d'un utilisateur admin par défaut (optionnel)
INSERT INTO users (username, email, password, is_admin, is_active, email_verified) 
VALUES (
    'admin', 
    'admin@twitchforum.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    TRUE, 
    TRUE, 
    TRUE
) ON DUPLICATE KEY UPDATE id=id;

-- Procédure pour nettoyer les anciens tokens et logs
DELIMITER //

CREATE PROCEDURE CleanupOldData()
BEGIN
    -- Supprimer les tokens expirés
    DELETE FROM remember_tokens WHERE expires_at < NOW();
    
    -- Supprimer les tentatives de connexion de plus de 30 jours
    DELETE FROM login_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    -- Supprimer les logs d'activité de plus de 90 jours
    DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
    
    -- Supprimer les tokens de réinitialisation expirés
    DELETE FROM password_resets WHERE expires_at < NOW() OR used = TRUE;
    
    -- Supprimer les sessions inactives de plus de 30 jours
    DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 30 DAY);
END //

DELIMITER ;

-- Événement pour exécuter le nettoyage automatiquement (si les événements sont activés)
-- SET GLOBAL event_scheduler = ON;
-- CREATE EVENT IF NOT EXISTS cleanup_old_data
-- ON SCHEDULE EVERY 1 DAY
-- STARTS CURRENT_TIMESTAMP
-- DO CALL CleanupOldData();

-- Vues utiles pour les statistiques
CREATE VIEW user_stats AS
SELECT 
    COUNT(*) as total_users,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_users,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_30d,
    COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_users_7d
FROM users;

-- Index pour optimiser les performances
CREATE INDEX idx_users_last_login ON users(last_login);
CREATE INDEX idx_activity_logs_composite ON activity_logs(user_id, action, created_at);

-- Commentaires pour la documentation
ALTER TABLE users COMMENT = 'Table principale des utilisateurs du forum';
ALTER TABLE remember_tokens COMMENT = 'Tokens pour la fonctionnalité "Se souvenir de moi"';
ALTER TABLE activity_logs COMMENT = 'Logs des activités utilisateurs pour audit et sécurité';
ALTER TABLE login_attempts COMMENT = 'Tentatives de connexion pour détecter les attaques par force brute';
ALTER TABLE user_sessions COMMENT = 'Gestion avancée des sessions utilisateurs';
ALTER TABLE password_resets COMMENT = 'Tokens pour la réinitialisation des mots de passe';