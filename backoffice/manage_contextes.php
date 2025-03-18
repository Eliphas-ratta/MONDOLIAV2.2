<?php
require_once '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Récupération des contextes et héros
$contextes = $pdo->query("SELECT * FROM contextes")->fetchAll(PDO::FETCH_ASSOC);
$heroes = $pdo->query("SELECT * FROM heros")->fetchAll(PDO::FETCH_ASSOC);

// Gestion des actions (ajout/modification)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $heroes_selected = $_POST['heroes'] ?? [];

    if ($id) {
        // Modifier un contexte
        $stmt = $pdo->prepare("UPDATE contextes SET titre = ?, description = ? WHERE id = ?");
        $stmt->execute([$titre, $description, $id]);

        // Supprimer les anciennes associations
        $pdo->prepare("DELETE FROM hero_contextes WHERE contexte_id = ?")->execute([$id]);
    } else {
        // Ajouter un nouveau contexte
        $stmt = $pdo->prepare("INSERT INTO contextes (titre, description) VALUES (?, ?)");
        $stmt->execute([$titre, $description]);
        $id = $pdo->lastInsertId();
    }

    // Associer les héros au contexte
    foreach ($heroes_selected as $hero_id) {
        $stmt = $pdo->prepare("INSERT INTO hero_contextes (hero_id, contexte_id) VALUES (?, ?)");
        $stmt->execute([$hero_id, $id]);
    }

    header("Location: manage_contextes.php");
    exit();
}

// Suppression d'un contexte
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM contextes WHERE id = ?")->execute([$id]);
    header("Location: manage_contextes.php");
    exit();
}

// Récupération des héros d'un contexte pour modification
$selectedContexte = null;
$heroes_ids = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $selectedContexte = $pdo->prepare("SELECT * FROM contextes WHERE id = ?");
    $selectedContexte->execute([$id]);
    $selectedContexte = $selectedContexte->fetch(PDO::FETCH_ASSOC);

    if ($selectedContexte) {
        $stmt = $pdo->prepare("SELECT hero_id FROM hero_contextes WHERE contexte_id = ?");
        $stmt->execute([$id]);
        $heroes_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-red-500 mb-8">Manage Contextes</h1>

    <!-- Formulaire d'ajout/modification -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-center mb-4">Créer ou modifier un contexte</h2>

        <form method="POST">
            <input type="hidden" name="id" value="<?= $selectedContexte['id'] ?? '' ?>">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm">Titre</label>
                    <input type="text" name="titre" value="<?= $selectedContexte['titre'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm">Description</label>
                    <textarea name="description" class="w-full p-3 bg-neutral-800 text-white rounded-md"><?= $selectedContexte['description'] ?? '' ?></textarea>
                </div>
            </div>

            <div class="mt-4">
    <label class="block text-sm">Associer des héros</label>
    <div class="w-full p-3 bg-neutral-800 text-white rounded-md overflow-y-auto max-h-64">
        <?php foreach ($heroes as $hero) : ?>
            <label class="flex items-center space-x-3 p-2 hover:bg-neutral-700 rounded-md cursor-pointer">
                <input type="checkbox" name="heroes[]" value="<?= $hero['id'] ?>" 
                       <?= in_array($hero['id'], $heroes_ids) ? 'checked' : '' ?> class="form-checkbox text-green-500">
                <img src="<?= $hero['image'] ?>" class="w-10 h-10 rounded-full object-cover">
                <span><?= $hero['name'] ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>


            <button type="submit" class="mt-4 bg-green-500 text-white p-3 rounded-md w-full hover:bg-green-600">Sauvegarder</button>
        </form>
    </div>

    <!-- Tableau des contextes -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg mt-8 w-full">
        <h2 class="text-xl font-bold text-center mb-4">Liste des contextes</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-neutral-700">
                <thead>
                    <tr class="bg-neutral-800 text-white">
                        <th class="p-3">Titre</th>
                        <th class="p-3">Description</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contextes as $contexte) : ?>
                        <tr class="bg-neutral-800 hover:bg-neutral-700 transition">
                            <td class="p-3"><?= $contexte['titre'] ?></td>
                            <td class="p-3"><?= $contexte['description'] ?></td>
                            <td class="p-3 text-center flex">
                                <a href="?edit=<?= $contexte['id'] ?>" class="bg-yellow-500 px-4 py-2  rounded">Modifier</a>
                                <a href="?delete=<?= $contexte['id'] ?>" class="bg-red-500 px-4 py-2  rounded">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
