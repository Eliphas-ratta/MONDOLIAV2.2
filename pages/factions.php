<?php
require_once '../includes/header.php';

// Récupération des factions depuis la base de données
$stmt = $pdo->query("SELECT id, name, image, regime FROM factions");
$factions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-white mb-6">FACTIONS</h1>

    <!-- Barre de recherche -->
    <div class="flex justify-center mb-6">
        <input type="text" id="searchInput" class="p-2 w-80 rounded-l-md bg-neutral-800 text-white border border-neutral-600" placeholder="Rechercher...">
        <button class="p-2 bg-red-500 text-white rounded-r-md" onclick="filterFactions()">
            🔍
        </button>
    </div>

    <!-- Liste des factions -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 px-4 md:px-10">

        <?php foreach ($factions as $faction) : ?>
            <a href="faction.php?id=<?= $faction['id'] ?>" class="transform transition duration-300 hover:scale-105">
                <div class="faction-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 flex flex-col h-full">
                    <img src="<?= $faction['image'] ?>" alt="<?= $faction['name'] ?>" class="w-full h-64 object-cover rounded-lg mb-2">
                    <p class="text-white font-bold text-lg flex-grow"><?= $faction['name'] ?></p>
                    <p class="text-gray-400 text-sm"><?= $faction['regime'] ?></p> <!-- Affichage du régime -->
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function filterFactions() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let cards = document.querySelectorAll('.faction-card');

        cards.forEach(card => {
            let name = card.querySelector('p').innerText.toLowerCase();
            card.style.display = name.includes(input) ? 'block' : 'none';
        });
    }
</script>

<?php require_once '../includes/footer.php'; ?>
