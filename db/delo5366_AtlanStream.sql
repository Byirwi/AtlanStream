-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 02 juin 2025 à 14:50
-- Version du serveur : 11.4.7-MariaDB
-- Version de PHP : 8.3.21

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
(13, 'guerre', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favorites`
--

INSERT INTO `favorites` (`id`, `movie_id`, `user_id`, `created_at`) VALUES
(6, 4, 3, '2025-06-02 10:06:51');

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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `year` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `director` varchar(100) DEFAULT NULL,
  `actors` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `poster_url`, `category_id`, `created_at`, `year`, `duration`, `director`, `actors`) VALUES
(1, 'le chant du loup', 'Le Chant du loup est un thriller militaire français où un expert en acoustique sous-marine doit empêcher une guerre nucléaire imminente. Réaliste et intense, le film explore les coulisses de la dissuasion à bord d’un sous-marin.', '1748331862_68356d5663ba4.jpg', NULL, '2025-05-27 07:44:22', 2019, 180, 'Antonin Baudry', 'François Civil, Omar Sy'),
(2, 'Joker', 'Le deuxième volet intitulé Joker: Folie à Deux sortira le 2 octobre 2024 (prévu). Il mettra en scène Harley Quinn, jouée par Lady Gaga, et comportera des éléments de comédie musicale.', '1748332268_68356eec8416a.jpg', NULL, '2025-05-27 07:45:42', 2019, 122, 'Joaquin Phoenix', ''),
(3, 'The Batman', 'The Batman (2022) est un thriller sombre réalisé par Matt Reeves, où Bruce Wayne enquête sur une série de meurtres orchestrés par le Riddler. Plongé dans une Gotham corrompue, il découvre des secrets liés à sa propre famille.', '1748337329_683582b190f21.jpg', NULL, '2025-05-27 09:15:29', 2022, 176, 'Matt Reeves', ''),
(4, 'Baywatch', 'une équipe de sauveteurs déjantés menée par Mitch Buchannon enquête sur un trafic de drogue menaçant leur plage. Humour, action et rivalités s’enchaînent sous le soleil de Malibu.', '1748337479_68358347c59cd.jpg', NULL, '2025-05-27 09:17:59', 2017, 119, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `movie_categories`
--

CREATE TABLE `movie_categories` (
  `movie_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `movie_categories`
--

INSERT INTO `movie_categories` (`movie_id`, `category_id`) VALUES
(3, 10),
(4, 10),
(1, 11),
(2, 11),
(1, 13);

-- --------------------------------------------------------

--
-- Structure de la table `movie_ratings`
--

CREATE TABLE `movie_ratings` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `movie_ratings`
--

INSERT INTO `movie_ratings` (`id`, `movie_id`, `user_id`, `rating`, `created_at`, `updated_at`) VALUES
(1, 3, 8, 2, '2025-05-27 15:58:17', '2025-05-27 15:58:30'),
(2, 4, 3, 3, '2025-05-27 15:59:04', NULL),
(3, 3, 3, 4, '2025-05-27 16:03:44', '2025-05-27 16:50:47'),
(4, 2, 3, 4, '2025-05-27 16:05:23', NULL),
(5, 3, 12, 3, '2025-05-27 16:09:08', NULL),
(6, 1, 3, 4, '2025-05-27 17:00:06', NULL);

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
(3, 'Byirwi', 'depaiva.louka@gmail.com', '$2y$10$5AjgPu2izyTTb8lPEVaVKetwaCMEE61YtCiOkdsKkUhh9.vh.R29W', 1, '2025-05-05 13:20:38'),
(5, 'Lolan', 'lolan.flr.perso@gmail.com', '$2y$10$K231TmWFTIRn6VD22BZFiu7x7OPDSmW159MYgp2orfYHaE6dlSTfy', 1, '2025-05-05 19:31:09'),
(7, 'Thomas', 'bizarrepourquoituveuxmonmail@gmail.com', '$2y$10$m492yqh8sxpSmVXXFnMAguZ/E58PPZw9N3Paf7IpLSyfSfIPB5MuO', 1, '2025-05-14 19:12:17'),
(8, 'louka_test', NULL, '$2y$10$hjE.isp8FxhX45xc9lUH8uF4dhVY1BbLWSWAGu/1l0JbNsaHXtNb.', 0, '2025-05-27 07:29:05'),
(9, 'Hashtag 628', 'zogoobamenicolas@gmail.com', '$2y$10$qt2LRQ0R18JZepriHlB15.ErcKIbVYUzIhdLwLnwBm9T1d97iRRBK', 1, '2025-05-27 07:29:49'),
(10, 'saita08', NULL, '$2y$10$MC67LtrWu8i/t0HyLfde.um.gEmDPZFMMz805S8Zwoqy/wnoq01hC', 1, '2025-05-27 09:25:59'),
(11, 'Liam', 'mongroszob@bztmr.cum', '$2y$10$v.5NGo73K9Klhv4xKRtVD.TBJeqi2TOkVC3v/GL539i3eLaC5JG1e', 1, '2025-05-27 11:41:48'),
(12, 'louka_test_02', NULL, '$2y$10$0HkNyjhvp6YtbkSxdPhVRu/QNJdquQ3imNRakQx4V.44hTO9cQ2wG', 0, '2025-05-27 14:05:57');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_movie_favorite` (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Index pour la table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `movie_categories`
--
ALTER TABLE `movie_categories`
  ADD PRIMARY KEY (`movie_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `movie_ratings`
--
ALTER TABLE `movie_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_movie_rating` (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `movie_ratings`
--
ALTER TABLE `movie_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `movie_categories`
--
ALTER TABLE `movie_categories`
  ADD CONSTRAINT `movie_categories_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movie_ratings`
--
ALTER TABLE `movie_ratings`
  ADD CONSTRAINT `movie_ratings_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
