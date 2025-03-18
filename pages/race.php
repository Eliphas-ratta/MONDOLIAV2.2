<?php
require_once '../includes/header.php';

// Vérifier si l'ID est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: races.php");
    exit();
}

$race_id = $_GET['id'];

// Récupération des infos de la race
$stmt = $pdo->prepare("SELECT * FROM races WHERE id = ?");
$stmt->execute([$race_id]);
$race = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$race) {
    header("Location: races.php");
    exit();
}

// Récupération des héros associés
$stmt = $pdo->prepare("SELECT id, name, image, fonction FROM heros WHERE race_id = ?");
$stmt->execute([$race_id]);
$heros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto py-10">
    <!-- Infos de la race -->
    <div class="flex flex-col md:flex-row items-start gap-6">
        <img src="<?= $race['image'] ?>" alt="<?= $race['name'] ?>" class="w-full md:w-80 md:h-80 object-cover rounded-lg">
        <div>
            <h1 class="text-3xl font-bold text-red-500"><?= $race['name'] ?></h1>
            <p class="text-white"><strong>Taille moyenne :</strong> <?= $race['taille'] ?></p>
            <p class="text-white"><strong>Description :</strong> <?= $race['description'] ?></p>
        </div>
    </div>

    <!-- Héros associés -->
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
