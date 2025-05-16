-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 06 mai 2025 à 14:40
-- Version du serveur : 11.4.5-MariaDB
-- Version de PHP : 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `delo5366_AtlanStream`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(6, 'Aventure', 'Films d\'aventure dans les profondeurs marines'),
(7, 'Documentaire', 'Documentaires sur l\'Atlantide et les civilisations perdues'),
(8, 'Science-Fiction', 'Films de science-fiction inspirés par l\'Atlantide'),
(9, 'Fantastique', 'Films fantastiques sur les mythes et légendes atlantes'),
(10, 'Action', 'Films d\'action avec des batailles sous-marines et des quêtes atlantes'),
(11, 'Thriller', NULL),
(12, 'Thriller/Documentaire', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `poster_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `poster_url`, `category_id`, `created_at`) VALUES
(1, 'Le Chant du Loup', 'Le Chant du loup est un thriller militaire français où un expert en acoustique sous-marine doit empêcher une guerre nucléaire imminente. Réaliste et intense, le film explore les coulisses de la dissuasion à bord d’un sous-marin.', '1746455338_6818cb2a6b3f8.jpg', 11, '2025-05-05 14:28:58');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_admin`, `created_at`) VALUES
(1, 'admin', 'admin@atlanstream.com', '$2y$10$SZFbpYMWC3aNGKuK5iok5O2i9ryrWfKHUA5MKA4iXEH0WB2A7JIt2', 1, '2025-05-05 13:01:14'),
(3, 'Byirwi', 'depaiva.louka@gmail.com', '$2y$10$xEVcO2FkVeSu2mB9.ZiSPuT1O32OPeG.V4T0a3cxp75y69jBYK0.u', 1, '2025-05-05 13:20:38'),
(4, 'louka_test_no1', NULL, '$2y$10$mWl/2vvyKiQ5qtnpIitEzOATavOMBa2ahQrx46ds4enIL8kmpenU6', 0, '2025-05-05 14:45:13'),
(5, 'Lolan', 'lolan.flr.perso@gmail.com', '$2y$10$K231TmWFTIRn6VD22BZFiu7x7OPDSmW159MYgp2orfYHaE6dlSTfy', 1, '2025-05-05 19:31:09');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
