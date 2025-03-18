<?php
require_once '../includes/header.php';

// Récupérer l'ID du contexte sélectionné
$contexte_id = $_GET['id'] ?? null;
$contexte = null;
$related_heroes = [];

if ($contexte_id) {
    // Récupérer les informations du contexte
    $stmt = $pdo->prepare("SELECT * FROM contextes WHERE id = ?");
    $stmt->execute([$contexte_id]);
    $contexte = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les héros liés à ce contexte
    $stmt = $pdo->prepare("
        SELECT h.id, h.name, h.image, h.fonction 
        FROM hero_contextes hc
        JOIN heros h ON hc.hero_id = h.id
        WHERE hc.contexte_id = ?
    ");
    $stmt->execute([$contexte_id]);
    $related_heroes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mx-auto py-10">
    <?php if ($contexte) : ?>
        <div class="bg-neutral-900 p-6 rounded-lg">
            <h1 class="text-3xl font-bold text-red-500 mb-4"><?= htmlspecialchars($contexte['titre']) ?></h1>
            <p class="text-white"><strong>Description :</strong> <?= htmlspecialchars($contexte['description']) ?></p>
        </div>

        <?php if (!empty($related_heroes)) : ?>
            <div class="bg-neutral-800 mt-6 p-6 rounded-lg">
                <h2 class="text-2xl font-bold text-white mb-4 text-center">Héros liés</h2>

                <!-- Mise en page responsive -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <?php foreach ($related_heroes as $hero) : ?>
                        <a href="hero.php?id=<?= $hero['id'] ?>" class="block transform transition duration-300 hover:scale-105">
                            <div class="bg-neutral-700 p-4 rounded-lg text-center cursor-pointer hover:bg-neutral-600">
                                <img src="<?= htmlspecialchars($hero['image']) ?>" class="w-32 h-32 rounded-full mx-auto object-cover">
                                <p class="text-white font-bold mt-2"><?= htmlspecialchars($hero['name']) ?></p>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars($hero['fonction']) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <p class="√text-white mt-6">Aucun héros lié à ce contexte.</p>
        <?php endif; ?>
    <?php else : ?>
        <p class="text-white text-center">Contexte non trouvé.</p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>

