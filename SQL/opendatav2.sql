-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 05 déc. 2025 à 07:12
-- Version du serveur : 8.0.43
-- Version de PHP : 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `opendatav2`
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
-- DOIT être créée en premier car les autres tables y font référence
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `created_at`, `updated_at`) VALUES
(1, 'kilian.spitaels@gmail.com', '$2y$10$2SWXUnhopqBiMo.pTfSeKeV04NtNAV6CVtiB.fXODZYzjqxuIUOx2', 'Kilian Spitaels', '2025-11-21 13:46:24', '2025-11-21 13:46:24');

-- --------------------------------------------------------

--
-- Structure de la table `user_settings`
-- Référence users
--

CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `participation_visibility` enum('public','friends_only') COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `view_participations` enum('public','friends_only') COLLATE utf8mb4_unicode_ci DEFAULT 'friends_only',
  `notifications_enabled` tinyint(1) DEFAULT '1',
  `email_notifications` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `participation_visibility`, `view_participations`, `notifications_enabled`, `email_notifications`, `created_at`, `updated_at`) VALUES
(1, 1, 'public', 'friends_only', 1, 1, '2025-11-23 14:51:08', '2025-12-04 18:55:26');

-- --------------------------------------------------------

--
-- Structure de la table `friends`
-- Référence users (2 fois)
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `friend_id` int NOT NULL,
  `status` enum('pending','accepted','blocked') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_friend` (`friend_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `aires_jeux`
-- Table indépendante (pas de contraintes de clé étrangère)
--

CREATE TABLE IF NOT EXISTS `aires_jeux` (
  `id` int NOT NULL AUTO_INCREMENT,
  `famille_eqpt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_autorise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tranches_age` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acces_entree_pmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acces_sol_pmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acces_modules_pmr` text COLLATE utf8mb4_unicode_ci,
  `adresse` text COLLATE utf8mb4_unicode_ci,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codeinsee` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `photo` text COLLATE utf8mb4_unicode_ci,
  `data_json` json DEFAULT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_location` (`latitude`,`longitude`),
  KEY `idx_commune` (`commune`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `equipements_sportifs`
-- Table indépendante (pas de contraintes de clé étrangère)
--

CREATE TABLE IF NOT EXISTS `equipements_sportifs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equip_theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equip_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equip_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_codepostal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adr_commune` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adr_code_insee_com` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adr_num_et_rue` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `data_json` json DEFAULT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_location` (`latitude`,`longitude`),
  KEY `idx_commune` (`adr_commune`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `manifestations_sportives`
-- Table indépendante (pas de contraintes de clé étrangère)
--

CREATE TABLE IF NOT EXISTS `manifestations_sportives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `association_ou_service` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manifestation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_de_fin` date DEFAULT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `data_json` json DEFAULT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_location` (`latitude`,`longitude`),
  KEY `idx_date_fin` (`date_de_fin`),
  KEY `idx_commune` (`commune`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `agenda_culturel`
-- Table indépendante (pas de contraintes de clé étrangère)
--

CREATE TABLE IF NOT EXISTS `agenda_culturel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `horaire` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thematique` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_du_spectacle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lieu_de_representation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `data_json` json DEFAULT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_location` (`latitude`,`longitude`),
  KEY `idx_date` (`date`),
  KEY `idx_commune` (`commune`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `points_interets`
-- Table indépendante (pas de contraintes de clé étrangère)
--

CREATE TABLE IF NOT EXISTS `points_interets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thematique` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descriptio` text COLLATE utf8mb4_unicode_ci,
  `liens_vers` text COLLATE utf8mb4_unicode_ci,
  `photo` text COLLATE utf8mb4_unicode_ci,
  `credit_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_insee` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `data_json` json DEFAULT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_location` (`latitude`,`longitude`),
  KEY `idx_commune` (`commune`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participations`
-- Utilise activity_type et activity_id pour référencer n'importe quelle table d'activités
-- Structure générique extensible pour toutes les futures tables d'activités
-- Référence users
--

CREATE TABLE IF NOT EXISTS `participations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `activity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type d''activité: aires_jeux, equipements_sportifs, etc.',
  `activity_id` int NOT NULL COMMENT 'ID de l''activité dans la table correspondante',
  `date_presence` date DEFAULT NULL,
  `heure_presence` time DEFAULT NULL,
  `activity_description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_participation` (`user_id`,`activity_type`,`activity_id`,`date_presence`),
  KEY `idx_user` (`user_id`),
  KEY `idx_activity` (`activity_type`,`activity_id`),
  CONSTRAINT `participations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invitations`
-- Utilise activity_type et activity_id pour référencer n'importe quelle table d'activités
-- Référence users
--

CREATE TABLE IF NOT EXISTS `invitations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `activity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type d''activité: aires_jeux, equipements_sportifs, etc.',
  `activity_id` int NOT NULL COMMENT 'ID de l''activité dans la table correspondante',
  `date_presence` date DEFAULT NULL,
  `heure_presence` time DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','accepted','declined') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_from_user` (`from_user_id`),
  KEY `idx_to_user` (`to_user_id`),
  KEY `idx_activity` (`activity_type`,`activity_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invitations_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
