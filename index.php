<?php
require_once 'includes/header.php'; // Inclure le header
?>

<main class="container mx-auto py-10">
    <h1 class="text-center text-3xl font-bold text-white mb-8">Bienvenue sur Mondolia</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <!-- Card Factions -->
        <div class="bg-neutral-900 rounded-lg overflow-hidden shadow-lg">
            <img src="img/default/Faction.jpeg" class="bg-gray-300  flex items-center justify-center text-black text-lg font-bold"></img>
            <div class="p-4 text-center">
                <h2 class="text-white text-xl font-semibold mb-2">Factions</h2>
                <a href="<?= BASE_URL ?>pages/factions.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Voir plus
                </a>
            </div>
        </div>

        <!-- Card Guildes -->
        <div class="bg-neutral-900 rounded-lg overflow-hidden shadow-lg">
            <img src="img/default/Guilde.jpeg" class="bg-gray-300 flex items-center justify-center text-black text-lg font-bold"></img>
            <div class="p-4 text-center">
                <h2 class="text-white text-xl font-semibold mb-2">Guildes</h2>
                <a href="<?= BASE_URL ?>pages/guildes.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Voir plus
                </a>
            </div>
        </div>

        <!-- Card Races -->
        <div class="bg-neutral-900 rounded-lg overflow-hidden shadow-lg">
            <img src="img/default/Races.jpeg" class="bg-gray-300 flex items-center justify-center text-black text-lg font-bold"></img>
            <div class="p-4 text-center">
                <h2 class="text-white text-xl font-semibold mb-2">Races</h2>
                <a href="<?= BASE_URL ?>pages/races.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Voir plus
                </a>
            </div>
        </div>

        <!-- Card Héros -->
        <div class="bg-neutral-900 rounded-lg overflow-hidden shadow-lg">
            <img src="img/default/Hero.jpeg" class="bg-gray-300 flex items-center justify-center text-black text-lg font-bold"></img>
            <div class="p-4 text-center">
                <h2 class="text-white text-xl font-semibold mb-2">Héros</h2>
                <a href="<?= BASE_URL ?>pages/heros.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Voir plus
                </a>
            </div>
        </div>

        <!-- Card Contextes -->
        <div class="bg-neutral-900 rounded-lg overflow-hidden shadow-lg">
            <img src="img/default/Contexte.jpeg" class="bg-gray-300 flex items-center justify-center text-black text-lg font-bold"></img>
            <div class="p-4 text-center">
                <h2 class="text-white text-xl font-semibold mb-2">Contextes</h2>
                <a href="<?= BASE_URL ?>pages/contextes.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Voir plus
                </a>
            </div>
        </div>

        <!-- Card Carte -->
        <div class="bg-neutral-900 rounded-lg overflow-hidden shadow-lg">
            <img src="img/default/Carte" class="bg-gray-300 flex items-center justify-center text-black text-lg font-bold"></img>
            <div class="p-4 text-center">
                <h2 class="text-white text-xl font-semibold mb-2">Carte</h2>
                <a href="<?= BASE_URL ?>pages/carte.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Voir plus
                </a>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; // Inclure le footer ?>
