# Forum Twitch - Système d'Authentification

Un système d'inscription et de connexion moderne avec la colorimétrie officielle de Twitch, développé en PHP, HTML, CSS et JavaScript.

## 🎨 Caractéristiques

- **Design Twitch** : Utilise la colorimétrie officielle de Twitch (#9146FF)
- **Interface moderne** : Design responsive et animations fluides
- **Sécurité renforcée** : Hachage des mots de passe, protection CSRF, logs d'activité
- **Validation complète** : Côté client (JavaScript) et serveur (PHP)
- **Fonctionnalité "Se souvenir de moi"** : Connexion persistante sécurisée

## 📋 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Extension PHP PDO MySQL

## 🚀 Installation

### 1. Configuration de la base de données

```sql
-- Créer la base de données
CREATE DATABASE twitch_forum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importer la structure
mysql -u root -p twitch_forum < database.sql
```

### 2. Configuration de l'application

Modifiez le fichier `config.php` avec vos paramètres :

```php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'twitch_forum');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

### 3. Permissions des fichiers

```bash
# Donner les bonnes permissions
chmod 755 *.php
chmod 644 *.html *.css *.js
```

### 4. Configuration du serveur web

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.html [QSA,L]

# Sécurité
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.html;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

## 📁 Structure des fichiers

```
TW/
├── index.html          # Page principale (connexion/inscription)
├── style.css           # Styles avec colorimétrie Twitch
├── script.js           # JavaScript pour validation et interactions
├── auth.php            # Traitement de l'authentification
├── config.php          # Configuration de l'application
├── dashboard.php       # Page d'accueil après connexion
├── logout.php          # Script de déconnexion
├── database.sql        # Structure de la base de données
└── README.md           # Documentation
```

## 🎯 Utilisation

### Inscription
1. Accédez à `index.html`
2. Cliquez sur "Créer un compte"
3. Remplissez le formulaire avec :
   - Nom d'utilisateur (3+ caractères, lettres/chiffres/underscore)
   - Email valide
   - Mot de passe (8+ caractères, majuscule + minuscule + chiffre)
   - Confirmation du mot de passe

### Connexion
1. Utilisez votre email ou nom d'utilisateur
2. Entrez votre mot de passe
3. Optionnel : cochez "Se souvenir de moi" pour rester connecté

### Fonctionnalités de sécurité
- **Hachage des mots de passe** : Utilise `password_hash()` de PHP
- **Protection CSRF** : Tokens pour les formulaires
- **Logs d'activité** : Traçabilité des connexions/déconnexions
- **Validation stricte** : Côté client et serveur
- **Sessions sécurisées** : Configuration optimisée

## 🎨 Colorimétrie Twitch

Le design utilise la palette officielle de Twitch :

```css
:root {
    --twitch-purple: #9146FF;        /* Violet principal */
    --twitch-purple-dark: #772CE8;   /* Violet foncé */
    --twitch-purple-light: #A970FF;  /* Violet clair */
    --twitch-dark: #0E0E10;          /* Arrière-plan principal */
    --twitch-dark-alt: #18181B;      /* Arrière-plan alternatif */
    --twitch-gray: #1F1F23;          /* Gris foncé */
    --twitch-gray-light: #2F2F35;    /* Gris clair */
    --twitch-white: #FFFFFF;         /* Blanc */
    --twitch-text: #EFEFF1;          /* Texte principal */
    --twitch-text-alt: #ADADB8;      /* Texte secondaire */
}
```

## 🔧 Personnalisation

### Modifier les couleurs
Éditez les variables CSS dans `style.css` :

```css
:root {
    --twitch-purple: #votre-couleur;
    /* ... autres variables */
}
```

### Ajouter des champs
1. Modifiez `index.html` pour ajouter les champs
2. Mettez à jour `script.js` pour la validation
3. Modifiez `auth.php` pour le traitement
4. Ajustez la base de données si nécessaire

### Personnaliser les messages
Les messages sont configurables dans `script.js` et `auth.php`.

## 🛡️ Sécurité

### Bonnes pratiques implémentées
- Hachage sécurisé des mots de passe
- Protection contre les injections SQL (requêtes préparées)
- Validation stricte des données
- Tokens CSRF
- Sessions sécurisées
- Logs d'activité pour audit

### Recommandations supplémentaires
- Utilisez HTTPS en production
- Configurez un pare-feu
- Limitez les tentatives de connexion
- Sauvegardez régulièrement la base de données
- Mettez à jour PHP et MySQL régulièrement

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifiez les paramètres dans `config.php`
- Assurez-vous que MySQL est démarré
- Vérifiez les permissions utilisateur

### Problèmes de session
- Vérifiez que `session.save_path` est accessible en écriture
- Configurez correctement les cookies de session

### Erreurs JavaScript
- Ouvrez la console du navigateur (F12)
- Vérifiez que tous les fichiers sont accessibles

## 📝 Licence

Ce projet est libre d'utilisation pour des projets personnels et éducatifs.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations
- Ajouter des fonctionnalités

## 📞 Support

Pour toute question ou problème, consultez la documentation ou créez une issue.

---

**Développé avec ❤️ pour la communauté Twitch**