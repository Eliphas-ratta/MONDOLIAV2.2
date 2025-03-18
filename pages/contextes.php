<?php
require_once '../includes/header.php';

// Récupération des contextes avec les héros associés
$stmt = $pdo->query("
    SELECT c.id, c.titre, 
           GROUP_CONCAT(h.name SEPARATOR ', ') AS heros_associes
    FROM contextes c
    LEFT JOIN hero_contextes hc ON c.id = hc.contexte_id
    LEFT JOIN heros h ON hc.hero_id = h.id
    GROUP BY c.id, c.titre
");
$contextes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des héros pour le filtre
$heros = $pdo->query("SELECT id, name FROM heros")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-white mb-6">Contextes</h1>

    <!-- Barre de recherche et filtres -->
    <div class="flex flex-wrap justify-center mb-6 gap-4">
        <div class="flex">
            <input type="text" id="searchInput" class="p-2 w-80 rounded-l-md bg-neutral-800 text-white border border-neutral-600" placeholder="Rechercher...">
            <button class="p-2 bg-red-500 text-white rounded-r-md" onclick="filterContextes()">🔍</button>
        </div>
        <select id="filterHero" class="p-2 bg-neutral-800 text-white border border-neutral-600 rounded-md" onchange="filterContextes()">
            <option value="">Trier par héros</option>
            <?php foreach ($heros as $hero) : ?>
                <option value="<?= $hero['name'] ?>"><?= $hero['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Liste des contextes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-4 md:px-10">

        <?php foreach ($contextes as $contexte) : ?>
            <a href="contexte.php?id=<?= $contexte['id'] ?>" 
               class="contexte-card transform transition duration-300 hover:scale-105"
               data-hero="<?= strtolower($contexte['heros_associes'] ?? '') ?>">
               
                <div class="bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 flex flex-col justify-center h-20">
                    <p class="text-white font-bold text-lg"><?= $contexte['titre'] ?></p>
                    <p class="text-gray-400 text-sm"><?= $contexte['heros_associes'] ?: 'Aucun héros associé' ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function filterContextes() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let heroFilter = document.getElementById('filterHero').value.toLowerCase();
        let cards = document.querySelectorAll('.contexte-card');

        cards.forEach(card => {
            let title = card.querySelector('p').innerText.toLowerCase();
            let hero = card.getAttribute('data-hero').toLowerCase();

            let matchesTitle = title.includes(input);
            let matchesHero = !heroFilter || hero.includes(heroFilter);

            card.style.display = (matchesTitle && matchesHero) ? 'block' : 'none';
        });
    }
</script>

<?php require_once '../includes/footer.php'; ?>
