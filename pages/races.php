<?php
require_once '../includes/header.php';

// Récupération des races depuis la base de données
$stmt = $pdo->query("SELECT id, name, image FROM races");
$races = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-white mb-6">RACES</h1>

    <!-- Barre de recherche -->
    <div class="flex justify-center mb-6">
        <input type="text" id="searchInput" class="p-2 w-80 rounded-l-md bg-neutral-800 text-white border border-neutral-600" placeholder="Rechercher...">
        <button class="p-2 bg-red-500 text-white rounded-r-md" onclick="filterRaces()">
            🔍
        </button>
    </div>

    <!-- Liste des races -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 px-4 md:px-10">
        <?php foreach ($races as $race) : ?>
            <a href="race.php?id=<?= $race['id'] ?>" class="transform transition duration-300 hover:scale-105">
                <div class="race-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 flex flex-col h-full">
                    <img src="<?= $race['image'] ?>" alt="<?= $race['name'] ?>" class="w-full h-64 object-cover rounded-lg mb-2">
                    <p class="text-white font-bold text-lg flex-grow"><?= $race['name'] ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function filterRaces() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let cards = document.querySelectorAll('.race-card');

        cards.forEach(card => {
            let name = card.querySelector('p').innerText.toLowerCase();
            card.style.display = name.includes(input) ? 'block' : 'none';
        });
    }
</script>

<?php require_once '../includes/footer.php'; ?>
