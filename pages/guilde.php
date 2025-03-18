<?php
require_once '../includes/header.php';

// Vérifier si l'ID est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: guildes.php");
    exit();
}

$guilde_id = $_GET['id'];

// Récupération des infos de la guilde
$stmt = $pdo->prepare("SELECT * FROM guildes WHERE id = ?");
$stmt->execute([$guilde_id]);
$guilde = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guilde) {
    header("Location: guildes.php");
    exit();
}

// Récupération de la faction associée
$stmt = $pdo->prepare("SELECT id, name, image, regime FROM factions WHERE id = ?");
$stmt->execute([$guilde['faction_id']]);
$faction = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupération des dirigeantes
$stmt = $pdo->prepare("SELECT h.id, h.name, h.image FROM heros h 
                       JOIN guilde_dirigeants gd ON h.id = gd.hero_id 
                       WHERE gd.guilde_id = ?");
$stmt->execute([$guilde_id]);
$dirigeantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des héros associés
$stmt = $pdo->prepare("SELECT id, name, image, fonction FROM heros WHERE guilde_id = ?");
$stmt->execute([$guilde_id]);
$heros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto py-10">
    <!-- Infos de la guilde -->
    <div class="flex flex-col md:flex-row items-start gap-6">
        <img src="<?= $guilde['image'] ?>" alt="<?= $guilde['name'] ?>" class="w-full md:w-80 md:h-80 object-cover rounded-lg">
        <div>
            <h1 class="text-3xl font-bold text-red-500"><?= $guilde['name'] ?></h1>
            <p class="text-white"><strong>Type :</strong> <?= $guilde['type'] ?></p>
            <p class="text-white"><strong>Description :</strong> <?= $guilde['description'] ?></p>
            <p class="text-white"><strong>Visibilité :</strong> <?= $guilde['visibility'] ?></p>
        </div>
        <!-- Faction associée -->
        <?php if ($faction) : ?>
            <div class="text-center mt-6">
                <h2 class="text-lg font-bold text-white">Faction</h2>
                <a href="faction.php?id=<?= $faction['id'] ?>" class="transform transition duration-300 hover:scale-105">
                    <div class="faction-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 w-32 mx-auto">
                        <img src="<?= $faction['image'] ?>" alt="<?= $faction['name'] ?>" class="w-full h-24 object-cover rounded-lg mb-2">
                        <p class="text-white font-bold"><?= $faction['name'] ?></p>
                        <p class="text-gray-400 text-sm"><?= $faction['regime'] ?></p>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>


    <!-- Dirigeantes -->
    <h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Dirigeante(s)</h2>
    <div class="bg-neutral-900 p-6 rounded-lg mx-auto w-fit">
        <div class="flex flex-wrap justify-center gap-6">
            <?php foreach ($dirigeantes as $dirigeante) : ?>
                <div class="text-center">
                    <img src="<?= $dirigeante['image'] ?>" alt="<?= $dirigeante['name'] ?>" class="w-40 h-40 object-cover rounded-lg mx-auto">
                    <p class="text-white mt-2"><?= $dirigeante['name'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Héros -->
    <h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Héros</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 justify-center px-10 mt-6">
        <?php foreach ($heros as $hero) : ?>
            <a href="hero.php?id=<?= $hero['id'] ?>" class="transform transition duration-300 hover:scale-105">
                <div class="hero-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 w-full md:w-64">
                    <img src="<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>" class="w-full h-40 md:h-full object-contain rounded-lg mb-2">
                    <p class="text-white font-bold text-lg"><?= $hero['name'] ?></p>
                    <p class="text-gray-400 text-sm"><?= $hero['fonction'] ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
