<?php
require_once '../includes/header.php';

// Vérifier si l'ID est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: factions.php");
    exit();
}

$faction_id = $_GET['id'];

// Récupération des infos de la faction
$stmt = $pdo->prepare("SELECT * FROM factions WHERE id = ?");
$stmt->execute([$faction_id]);
$faction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faction) {
    header("Location: factions.php");
    exit();
}

// Récupération des dirigeantes
$stmt = $pdo->prepare("SELECT h.id, h.name, h.image FROM heros h 
                       JOIN faction_dirigeants fd ON h.id = fd.hero_id 
                       WHERE fd.faction_id = ?");
$stmt->execute([$faction_id]);
$dirigeantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des guildes associées à la faction
$stmt = $pdo->prepare("SELECT id, name, image, type FROM guildes WHERE faction_id = ?");
$stmt->execute([$faction_id]);
$guildes = $stmt->fetchAll(PDO::FETCH_ASSOC);




// Récupération des héros associés
$stmt = $pdo->prepare("SELECT id, name, image, fonction FROM heros WHERE faction_id = ?");
$stmt->execute([$faction_id]);
$heros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mx-auto py-10 ">
    <!-- Infos de la faction -->
    <div class="flex flex-col md:flex-row items-start gap-6">
    <img src="<?= $faction['image'] ?>" alt="<?= $faction['name'] ?>" class="w-full md:w-80 md:h-80 object-cover rounded-lg">

    <div class="flex-1">
        <h1 class="text-3xl font-bold text-red-500"><?= $faction['name'] ?></h1>
        <p class="text-white"><strong>Régime :</strong> <?= $faction['regime'] ?></p>
        <p class="text-white"><strong>Type :</strong> <?= $faction['type'] ?></p>
        <p class="text-white"><strong>Couleur :</strong> <?= $faction['couleur'] ?></p>
        <p class="text-white"><strong>Capitale :</strong> <?= $faction['capitale'] ?></p>
        <p class="text-white"><strong>Description :</strong> <?= $faction['description'] ?></p>
    </div>

    <!-- Drapeau (Flag) -->
    <?php if (!empty($faction['flag'])) : ?>
        <div class="hidden md:block w-40 h-40 ml-auto">
            <img src="<?= $faction['flag'] ?>" alt="Drapeau de <?= $faction['name'] ?>" class="w-full h-full object-cover rounded-lg border-4 border-red-500">
        </div>
    <?php endif; ?>
</div>

<!-- Drapeau visible uniquement sur mobile -->
<?php if (!empty($faction['flag'])) : ?>
    <div class="block md:hidden w-40 h-40 mx-auto my-4">
        <img src="<?= $faction['flag'] ?>" alt="Drapeau de <?= $faction['name'] ?>" class="w-full h-full object-cover rounded-lg border-4 border-red-500">
    </div>
<?php endif; ?>



    <!-- Dirigeantes -->
    <h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Dirigeante(s)</h2>
<div class="bg-neutral-900 p-6 rounded-lg mx-auto w-fit">
    <div class="flex justify-center gap-6">
        <?php foreach ($dirigeantes as $dirigeante) : ?>
            <div class="text-center">
                <img src="<?= $dirigeante['image'] ?>" alt="<?= $dirigeante['name'] ?>" class="w-40 h-40 object-cover rounded-lg mx-auto">
                <p class="text-white mt-2"><?= $dirigeante['name'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>


   <!-- Guildes -->
<h2 class="text-2xl font-bold text-center text-white mt-10 bg-neutral-800 p-3">Guildes</h2>
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 justify-center px-10 mt-6">
    <?php foreach ($guildes as $guilde) : ?>
        <a href="guilde.php?id=<?= $guilde['id'] ?>" class="transform transition duration-300 hover:scale-105">
            <div class="guilde-card bg-neutral-900 p-4 rounded-lg shadow-lg text-center border-neutral-700 w-full md:w-64">
                <img src="<?= $guilde['image'] ?>" alt="<?= $guilde['name'] ?>" class="w-full h-40 md:h-full object-contain rounded-lg mb-2">
                <p class="text-white font-bold text-lg"><?= $guilde['name'] ?></p>
                <p class="text-gray-400 text-sm"><?= $guilde['type'] ?></p>
            </div>
        </a>
    <?php endforeach; ?>
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
