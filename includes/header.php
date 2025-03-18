<?php
session_start();
require_once __DIR__ . '/config.php'; // Connexion à la base de données

// Vérifier si l'utilisateur est bien connecté
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Liste des pages accessibles sans connexion
$currentFile = basename($_SERVER['PHP_SELF']);
$allowedPages = ['login.php', 'register.php', 'index.php'];

// Redirection forcée vers login.php si non connecté
if (!$isLoggedIn && !in_array($currentFile, $allowedPages)) {
    header("Location: " . BASE_URL . "Security/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mondolia</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>img/default/favicon-32x32.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleMenu() {
            const menu = document.getElementById("mobile-menu");
            menu.classList.toggle("hidden");
            menu.classList.toggle("opacity-100");
            menu.classList.toggle("translate-y-0");
        }
    </script>
</head>
<body class="bg-neutral-800 text-white">

<header class="bg-neutral-900 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>index.php" class="text-white text-xl font-bold">
            <img src="<?= BASE_URL ?>img/default/logo_header.png" alt="Logo" width="80" height="80">
        </a>

        <!-- Menu burger (mobile) -->
        <button class="lg:hidden text-white focus:outline-none" onclick="toggleMenu()">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <!-- Navigation Desktop -->
        <nav class="hidden lg:flex space-x-6">
            <a href="<?= BASE_URL ?>pages/factions.php" class="hover:text-gray-300">Factions</a>
            <a href="<?= BASE_URL ?>pages/guildes.php" class="hover:text-gray-300">Guildes</a>
            <a href="<?= BASE_URL ?>pages/races.php" class="hover:text-gray-300">Races</a>
            <a href="<?= BASE_URL ?>pages/heros.php" class="hover:text-gray-300">Héros</a>
            <a href="<?= BASE_URL ?>pages/contextes.php" class="hover:text-gray-300">Contextes</a>
            <a href="<?= BASE_URL ?>pages/carte.php" class="hover:text-gray-300">Carte</a>
        </nav>

        <!-- Authentification -->
        <div class="hidden lg:flex items-center space-x-4">
            <?php if (!$isLoggedIn) : ?>
                <a href="<?= BASE_URL ?>Security/login.php" class="hover:text-gray-300">Connexion</a>
                <a href="<?= BASE_URL ?>Security/register.php" class="hover:text-gray-300">S'inscrire</a>
            <?php else : ?>
                <?php if ($isAdmin) : ?>
                    <a href="<?= BASE_URL ?>backoffice/dashboard.php" class="text-red-500 font-semibold">Backoffice</a>
                <?php endif; ?>
                <div class="flex items-center space-x-2">
                    <img src="<?= BASE_URL ?>img/default/pdp.png" alt="Profile Pic" class="w-10 h-10 rounded-full object-cover">
                    <span class="text-white font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="<?= BASE_URL ?>Security/logout.php" class="hover:text-gray-300">Déconnexion</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Menu mobile -->
    <div id="mobile-menu" class="hidden opacity-0 translate-y-[-10px] transition-all duration-300 bg-neutral-900 p-4 absolute top-16 left-0 w-full flex flex-col space-y-2">
        <a href="<?= BASE_URL ?>pages/factions.php" class="hover:text-gray-300">Factions</a>
        <a href="<?= BASE_URL ?>pages/guildes.php" class="hover:text-gray-300">Guildes</a>
        <a href="<?= BASE_URL ?>pages/races.php" class="hover:text-gray-300">Races</a>
        <a href="<?= BASE_URL ?>pages/heros.php" class="hover:text-gray-300">Héros</a>
        <a href="<?= BASE_URL ?>pages/contextes.php" class="hover:text-gray-300">Contextes</a>
        <a href="<?= BASE_URL ?>pages/carte.php" class="hover:text-gray-300">Carte</a>
        
        <div class="border-t border-gray-700 mt-2 pt-2">
            <?php if (!$isLoggedIn) : ?>
                <a href="<?= BASE_URL ?>Security/login.php" class="hover:text-gray-300">Connexion</a>
                <a href="<?= BASE_URL ?>Security/register.php" class="hover:text-gray-300">S'inscrire</a>
            <?php else : ?>
                <?php if ($isAdmin) : ?>
                    <a href="<?= BASE_URL ?>backoffice/dashboard.php" class="text-red-500 font-semibold">Backoffice</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>Security/logout.php" class="hover:text-gray-300">Déconnexion</a>
            <?php endif; ?>
        </div>
    </div>
</header>
