<?php
require_once '../includes/header.php';

// Récupération des héros avec leur fonction et leurs relations
$stmt = $pdo->query("SELECT h.id, h.name, h.image, h.fonction, 
                            f.name AS faction, g.name AS guilde, r.name AS race 
                     FROM heros h
                     LEFT JOIN factions f ON h.faction_id = f.id
                     LEFT JOIN guildes g ON h.guilde_id = g.id
                     LEFT JOIN races r ON h.race_id = r.id
                     ORDER BY h.id DESC"); // Tri par ID décroissant (plus récent en premier)

$heros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des options pour les filtres
$factions = $pdo->query("SELECT id, name FROM factions")->fetchAll(PDO::FETCH_ASSOC);
$guildes = $pdo->query("SELECT id, name FROM guildes")->fetchAll(PDO::FETCH_ASSOC);
$races = $pdo->query("SELECT id, name FROM races")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-white mb-6">HÉROS</h1>

    <!-- Barre de recherche et filtres -->
    <div class="flex flex-wrap justify-center mb-6 gap-4">
        <div class="flex">
            <input type="text" id="searchInput" class="p-2 w-80 rounded-l-md bg-neutral-800 text-white border border-neutral-600" placeholder="Rechercher...">
            <button class="p-2 bg-red-500 text-white rounded-r-md" onclick="filterHeroes()">🔍</button>
        </div>
        <select id="filterFaction" class="p-2 bg-neutral-800 text-white border border-neutral-600 rounded-md" onchange="filterHeroes()">
            <option value="">Trier par faction</option>
            <?php foreach ($factions as $faction) : ?>
                <option value="<?= $faction['name'] ?>"><?= $faction['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <select id="filterGuilde" class="p-2 bg-neutral-800 text-white border border-neutral-600 rounded-md" onchange="filterHeroes()">
            <option value="">Trier par guilde</option>
            <?php foreach ($guildes as $guilde) : ?>
                <option value="<?= $guilde['name'] ?>"><?= $guilde['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <select id="filterRace" class="p-2 bg-neutral-800 text-white border border-neutral-600 rounded-md" onchange="filterHeroes()">
            <option value="">Trier par race</option>
            <?php foreach ($races as $race) : ?>
                <option value="<?= $race['name'] ?>"><?= $race['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <select id="sortOrder" class="p-2 bg-neutral-800 text-white border border-neutral-600 rounded-md" onchange="sortHeroes()">
    <option value="recent">Plus récent</option>
    <option value="oldest">Plus ancien</option>
</select>

    </div>

    <!-- Liste des héros -->
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 px-4 md:px-10">
        <?php foreach ($heros as $hero) : ?>
            <a href="hero.php?id=<?= $hero['id'] ?>" 
               class="hero-card transform transition duration-300 hover:scale-105"
               data-faction="<?= $hero['faction'] ?: 'Aucune' ?>" 
               data-guilde="<?= $hero['guilde'] ?: 'Aucune' ?>" 
               data-race="<?= $hero['race'] ?: 'Aucune' ?>">
               
                <div class="bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 flex flex-col h-full">
                    <img src="<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>" class="w-full h-40 object-cover rounded-lg mb-2">
                    <p class="text-white font-bold text-lg flex-grow"><?= $hero['name'] ?></p>
                    <p class="text-gray-400 text-sm"><?= $hero['fonction'] ?: 'Fonction inconnue' ?></p> <!-- Affichage de la fonction -->
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function filterHeroes() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let factionFilter = document.getElementById('filterFaction').value.toLowerCase();
        let guildeFilter = document.getElementById('filterGuilde').value.toLowerCase();
        let raceFilter = document.getElementById('filterRace').value.toLowerCase();
        let cards = document.querySelectorAll('.hero-card');

        cards.forEach(card => {
            let name = card.querySelector('.text-lg').innerText.toLowerCase();
            let faction = card.getAttribute('data-faction').toLowerCase();
            let guilde = card.getAttribute('data-guilde').toLowerCase();
            let race = card.getAttribute('data-race').toLowerCase();

            let matchesName = name.includes(input);
            let matchesFaction = !factionFilter || faction.includes(factionFilter);
            let matchesGuilde = !guildeFilter || guilde.includes(guildeFilter);
            let matchesRace = !raceFilter || race.includes(raceFilter);

            card.style.display = (matchesName && matchesFaction && matchesGuilde && matchesRace) ? 'block' : 'none';
        });
    }
    function sortHeroes() {
    let order = document.getElementById('sortOrder').value;
    let cards = Array.from(document.querySelectorAll('.hero-card'));

    cards.sort((a, b) => {
        let idA = parseInt(a.getAttribute('href').split('=')[1]);
        let idB = parseInt(b.getAttribute('href').split('=')[1]);

        if (order === 'recent') {
            return idB - idA; // Plus récent en premier
        } else {
            return idA - idB; // Plus ancien en premier
        }
    });

    const container = document.querySelector('.grid');
    container.innerHTML = ''; // Vider le conteneur
    cards.forEach(card => container.appendChild(card)); // Réinsérer les cartes triées
}

</script>

<?php require_once '../includes/footer.php'; ?>
