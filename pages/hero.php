<?php
require_once '../includes/header.php';

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: heros.php");
    exit();
}

$hero_id = $_GET['id'];

// Récupération des informations du héros
$stmt = $pdo->prepare("SELECT * FROM heros WHERE id = ?");
$stmt->execute([$hero_id]);
$hero = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hero) {
    header("Location: heros.php");
    exit();
}

// Récupération de la faction associée
$stmt = $pdo->prepare("SELECT id, name, image, regime FROM factions WHERE id = ?");
$stmt->execute([$hero['faction_id']]);
$faction = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupération des guildes associées
// Récupération de la guilde associée directement depuis `heros`
$stmt = $pdo->prepare("
    SELECT g.id, g.name, g.image, g.type 
    FROM guildes g 
    WHERE g.id = ?
");
$stmt->execute([$hero['guilde_id']]);
$guilde = $stmt->fetch(PDO::FETCH_ASSOC);


// Récupération de la race du héros
$stmt = $pdo->prepare("SELECT id, name, image FROM races WHERE id = ?");
$stmt->execute([$hero['race_id']]);
$race = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupération des héros liés via les relations
$stmt = $pdo->prepare("
    SELECT h.id, h.name, h.image, h.fonction 
    FROM hero_relations hr
    JOIN heros h ON hr.related_hero_id = h.id
    WHERE hr.hero_id = ?
");
$stmt->execute([$hero_id]);
$related_heroes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des contextes associés au héros
$stmt = $pdo->prepare("
    SELECT c.id, c.titre 
    FROM hero_contextes hc
    JOIN contextes c ON hc.contexte_id = c.id
    WHERE hc.hero_id = ?
");
$stmt->execute([$hero_id]);
$contextes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto py-10">
    <!-- Informations du héros -->
    <div class="flex flex-col lg:flex-row items-start gap-6">
        <div class="flex gap-6 flex-col lg:flex-row">
            <img src="<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>" class="w-full lg:w-80 lg:h-80 object-cover rounded-lg">
            <div>
                <h1 class="text-3xl font-bold text-red-500 pb-8"><?= $hero['name'] ?></h1>
                <p class="text-white"><strong>Âge :</strong> <?= $hero['age'] ?></p>
                <p class="text-white"><strong>Taille :</strong> <?= $hero['taille'] ?></p>
                <p class="text-white"><strong>Fonction :</strong> <?= $hero['fonction'] ?></p>
                <p class="text-white"><strong>Description :</strong> <?= $hero['description'] ?></p>
            </div>
        </div>

        <!-- Faction associée -->
        <?php if ($faction) : ?>
            <div class="text-center">
                <h2 class="text-lg font-bold text-white">Faction</h2>
                <a href="faction.php?id=<?= $faction['id'] ?>" class="transform transition duration-300 hover:scale-105">
                    <div class="faction-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 w-32">
                        <img src="<?= $faction['image'] ?>" alt="<?= $faction['name'] ?>" class="w-full h-24 object-cover rounded-lg mb-2">
                        <p class="text-white font-bold"><?= $faction['name'] ?></p>
                        <p class="text-gray-400 text-sm"><?= $faction['regime'] ?></p>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="flex flex-col lg:flex-row justify-around gap-6 mt-6">
        <!-- Guilde associée -->
        <div>
            <h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Guilde</h2>
            <?php if ($guilde) : ?>
                <div class="flex justify-center mt-6">
                    <a href="guilde.php?id=<?= $guilde['id'] ?>" class="transform transition duration-300 hover:scale-105">
                        <div class="guilde-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 w-full lg:w-50">
                            <img src="<?= $guilde['image'] ?>" alt="<?= $guilde['name'] ?>" class="w-full h-40 lg:h-60 object-cover rounded-lg mb-2">
                            <p class="text-white font-bold"><?= $guilde['name'] ?></p>
                            <p class="text-gray-400 text-sm"><?= $guilde['type'] ?></p>
                        </div>
                    </a>
                </div>
            <?php else : ?>
                <p class="text-center text-gray-400">Aucune guilde associée</p>
            <?php endif; ?>
        </div>

        <!-- Race du héros -->
        <div>
            <h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Race</h2>
            <?php if ($race) : ?>
                <div class="flex justify-center mt-6">
                    <a href="race.php?id=<?= $race['id'] ?>" class="transform transition duration-300 hover:scale-105">
                        <div class="race-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 w-full lg:w-50">
                            <img src="<?= $race['image'] ?>" alt="<?= $race['name'] ?>" class="w-full h-40 lg:h-60 object-cover rounded-lg mb-2">
                            <p class="text-white font-bold"><?= $race['name'] ?></p>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Héros liés -->
    <h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Héros liés</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 justify-center mt-6">
        <?php foreach ($related_heroes as $hero) : ?>
            <a href="hero.php?id=<?= $hero['id'] ?>" class="transform transition duration-300 hover:scale-105">
                <div class="hero-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 w-full lg:w-60">
                    <img src="<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>" class="w-full h-40 lg:h-full object-cover rounded-lg mb-2">
                    <p class="text-white font-bold"><?= $hero['name'] ?></p>
                    <p class="text-gray-400 text-sm"><?= $hero['fonction'] ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Contextes associés -->
    <h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Contextes</h2>
    <div class="flex flex-col md:flex-row justify-center gap-6 mt-6">
        <?php foreach ($contextes as $contexte) : ?>
            <a href="contexte.php?id=<?= $contexte['id'] ?>" class="bg-neutral-900 text-white px-6 py-3 rounded-md text-center hover:bg-neutral-700 transition">
                <?= $contexte['titre'] ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
