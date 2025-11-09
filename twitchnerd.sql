-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : sam. 08 nov. 2025 à 15:56
-- Version du serveur : 8.0.40
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `twitchnerd`
--

-- --------------------------------------------------------

--
-- Structure de la table `discussion`
--

CREATE TABLE `discussion` (
  `id` int NOT NULL,
  `faq_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `discussion`
--

INSERT INTO `discussion` (`id`, `faq_id`, `user_id`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'dlpkopejiojfpoz', '2025-11-05 12:56:40', '2025-11-05 12:56:40');

-- --------------------------------------------------------

--
-- Structure de la table `faq`
--

CREATE TABLE `faq` (
  `id` int NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `faq`
--

INSERT INTO `faq` (`id`, `category`, `question`, `answer`, `keywords`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'streaming', 'Comment commencer à streamer sur Twitch ?', 'Pour commencer à streamer sur Twitch : Créez un compte Twitch, Téléchargez OBS Studio (gratuit), Configurez votre stream key dans OBS, Choisissez votre jeu ou contenu, Lancez votre premier stream !', 'commencer,streamer,twitch,obs,premier,stream', 1, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05'),
(2, 'technical', 'Quels sont les meilleurs paramètres OBS pour débuter ?', 'Paramètres recommandés pour débuter : Résolution 1920x1080 ou 1280x720, FPS 30 ou 60 selon votre connexion, Bitrate 2500-6000 kbps, Encoder x264 ou NVENC si vous avez une carte NVIDIA', 'obs,paramètres,résolution,fps,bitrate,encoder,nvenc', 2, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05'),
(3, 'monetisation', 'Comment devenir partenaire Twitch ?', 'Critères pour devenir partenaire : Streamer au moins 25 heures sur 30 jours, Streamer sur au moins 12 jours différents, Avoir une moyenne de 75 viewers, Respecter les conditions d\'utilisation, Être en conformité avec les directives communautaires', 'partenaire,twitch,critères,viewers,heures,stream', 3, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05'),
(4, 'streaming', 'Comment améliorer la qualité de mon stream ?', 'Conseils pour améliorer votre stream : Investissez dans un bon microphone, Éclairez bien votre visage, Créez des overlays attrayants, Interagissez avec votre chat, Streamer régulièrement, Partagez vos streams sur les réseaux sociaux', 'qualité,stream,microphone,éclairage,overlay,chat,réseaux sociaux', 4, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05'),
(5, 'technical', 'Mon stream lag, que faire ?', 'Solutions pour réduire le lag : Vérifiez votre connexion internet (upload minimum 3 Mbps), Fermez les applications inutiles, Réduisez la résolution ou le FPS, Changez de serveur Twitch, Utilisez un encodeur matériel (NVENC/QuickSync)', 'lag,stream,connexion,internet,résolution,fps,serveur,encodeur', 5, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05'),
(6, 'community', 'Comment créer une communauté engagée ?', 'Stratégies pour développer votre communauté : Soyez authentique et vous-même, Répondez aux messages du chat, Créez des événements réguliers, Utilisez Discord pour rester connecté, Collaborez avec d\'autres streamers, Créez du contenu unique', 'communauté,engagée,authentique,chat,discord,collaboration,contenu', 6, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05'),
(7, 'monetisation', 'Comment gagner de l\'argent en streamant ?', 'Moyens de monétiser votre stream : Abonnements revenus mensuels récurrents, Bits pourboires virtuels, Donations via PayPal ou autres plateformes, Partenariats sponsors et collaborations, Ventes merchandising et produits', 'argent,streamer,abonnements,bits,donations,partenariats,merchandising', 7, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05'),
(8, 'technical', 'Quel équipement recommandez-vous pour débuter ?', 'Équipement essentiel pour débuter : Microphone Blue Yeti ou Audio-Technica AT2020, Webcam Logitech C920 ou C922, Éclairage Anneau lumineux LED, PC Processeur quad-core minimum, Internet Connexion stable avec bon upload', 'équipement,microphone,webcam,éclairage,pc,internet,débuter', 8, 1, '2025-11-05 12:53:05', '2025-11-05 12:53:05');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text,
  `twitch_channel` varchar(100) DEFAULT NULL,
  `role` enum('user','moderator','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `avatar`, `bio`, `twitch_channel`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@twitchnerd.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, 'admin', '2025-09-17 13:53:21', '2025-09-23 12:30:15'),
(2, 'giani', 'gianibonillo@gmail.com', '$2y$10$CPskkKs0ZFyZNVTTHHV8cO6w0x5LnkbV2Bq/qslXUpOqglLeDMSeO', NULL, NULL, 'nique oum', 'admin', '2025-09-17 14:09:15', '2025-09-17 14:45:54'),
(3, 'test', 'test@gmail.com', '$2y$10$IASDcX1BHV.dp7zki5oNH.Wmm5P.Pno.onRA68yp8NM5RXQv7QZXu', NULL, NULL, NULL, 'user', '2025-11-05 12:49:46', '2025-11-05 12:56:30');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `discussion`
--
ALTER TABLE `discussion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faq_id` (`faq_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `discussion`
--
ALTER TABLE `discussion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `discussion`
--
ALTER TABLE `discussion`
  ADD CONSTRAINT `discussion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_ibfk_2` FOREIGN KEY (`faq_id`) REFERENCES `faq` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
