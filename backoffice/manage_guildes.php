<?php
require_once '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Récupération des guildes, factions et héros
$guildes = $pdo->query("SELECT * FROM guildes")->fetchAll(PDO::FETCH_ASSOC);
$factions = $pdo->query("SELECT * FROM factions")->fetchAll(PDO::FETCH_ASSOC);
$heroes = $pdo->query("SELECT * FROM heros")->fetchAll(PDO::FETCH_ASSOC);

usort($guildes, function ($a, $b) {
    return $b['id'] <=> $a['id'];
});

// Gestion des actions (ajout/modification)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $visibility = $_POST['visibility'];
    $faction_id = !empty($_POST['faction_id']) ? $_POST['faction_id'] : null;
    $dirigeantes = $_POST['dirigeantes'] ?? []; // Liste des dirigeantes sélectionnées

    // Récupérer l'image actuelle si elle existe
    $existingImage = $_POST['existing_image'] ?? null;

    // Gestion de l'image (ne pas écraser si aucune nouvelle image)
    if (!empty($_FILES['image']['name'])) {
        $imagePath = '../img/guilde/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $existingImage; // Conserver l'image existante si aucune nouvelle image
    }

    if ($id) {
        // Modifier une guilde
        $stmt = $pdo->prepare("UPDATE guildes SET name = ?, type = ?, description = ?, visibility = ?, faction_id = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $type, $description, $visibility, $faction_id, $imagePath, $id]);

        // Supprimer les anciennes dirigeantes
        $pdo->prepare("DELETE FROM guilde_dirigeants WHERE guilde_id = ?")->execute([$id]);
    } else {
        // Ajouter une nouvelle guilde
        $stmt = $pdo->prepare("INSERT INTO guildes (name, type, description, visibility, faction_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $type, $description, $visibility, $faction_id, $imagePath]);
        $id = $pdo->lastInsertId();
    }

    // Ajouter les nouvelles dirigeantes
    foreach ($dirigeantes as $hero_id) {
        $stmt = $pdo->prepare("INSERT INTO guilde_dirigeants (guilde_id, hero_id) VALUES (?, ?)");
        $stmt->execute([$id, $hero_id]);
    }

    header("Location: manage_guildes.php");
    exit();
}

// Suppression d'une guilde
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM guildes WHERE id = ?")->execute([$id]);
    header("Location: manage_guildes.php");
    exit();
}

// Récupération des dirigeantes d'une guilde lors de la modification
$selectedGuilde = null;
$dirigeantes_ids = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $selectedGuilde = $pdo->prepare("SELECT * FROM guildes WHERE id = ?");
    $selectedGuilde->execute([$id]);
    $selectedGuilde = $selectedGuilde->fetch(PDO::FETCH_ASSOC);

    if ($selectedGuilde) {
        $stmt = $pdo->prepare("SELECT hero_id FROM guilde_dirigeants WHERE guilde_id = ?");
        $stmt->execute([$id]);
        $dirigeantes_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-red-500 mb-8">Manage Guildes</h1>

    <!-- Formulaire d'ajout/modification -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-center mb-4">Créer ou modifier une guilde</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $selectedGuilde['id'] ?? '' ?>">
            <input type="hidden" name="existing_image" value="<?= $selectedGuilde['image'] ?? '' ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Nom</label>
                    <input type="text" name="name" value="<?= $selectedGuilde['name'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm">Type</label>
                    <input type="text" name="type" value="<?= $selectedGuilde['type'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm">Description</label>
                    <textarea name="description" class="w-full p-3 bg-neutral-800 text-white rounded-md"><?= $selectedGuilde['description'] ?? '' ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm">Visibilité</label>
                <select name="visibility" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                    <option value="visible" <?= isset($selectedGuilde['visibility']) && $selectedGuilde['visibility'] == 'visible' ? 'selected' : '' ?>>Visible</option>
                    <option value="discrete" <?= isset($selectedGuilde['visibility']) && $selectedGuilde['visibility'] == 'discrete' ? 'selected' : '' ?>>Discrète</option>
                    <option value="secrete" <?= isset($selectedGuilde['visibility']) && $selectedGuilde['visibility'] == 'secrete' ? 'selected' : '' ?>>Secrète</option>
                </select>
            </div>

            <div class="mt-4">
    <label class="block text-sm">Dirigeantes</label>
    <div class="w-full p-3 bg-neutral-800 text-white rounded-md overflow-y-auto max-h-64">
        <?php foreach ($heroes as $hero) : ?>
            <label class="flex items-center space-x-3 p-2 hover:bg-neutral-700 rounded-md cursor-pointer">
                <input type="checkbox" name="dirigeantes[]" value="<?= $hero['id'] ?>" 
                       <?= in_array($hero['id'], $dirigeantes_ids) ? 'checked' : '' ?> class="form-checkbox text-green-500">
                <img src="<?= $hero['image'] ?>" class="w-10 h-10 rounded-full object-cover">
                <span><?= $hero['name'] ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>


            <div class="mt-4">
    <label class="block text-sm">Faction associée</label>
    <select name="faction_id" class="w-full p-3 bg-neutral-800 text-white rounded-md">
        <option value="">Aucune</option>
        <?php foreach ($factions as $faction) : ?>
            <option value="<?= $faction['id'] ?>" <?= isset($selectedGuilde['faction_id']) && $selectedGuilde['faction_id'] == $faction['id'] ? 'selected' : '' ?>>
                <?= $faction['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


            <div class="mt-4">
    <label class="block text-sm">Image</label>
    <input type="file" name="image" class="w-full p-3 bg-neutral-800 text-white rounded-md">
    <?php if (!empty($selectedGuilde['image'])) : ?>
        <img src="<?= $selectedGuilde['image'] ?>" class="w-20 h-20 rounded-lg mt-2">
        <input type="hidden" name="existing_image" value="<?= $selectedGuilde['image'] ?>">
    <?php endif; ?>
</div>


            <button type="submit" class="mt-4 bg-green-500 text-white p-3 rounded-md w-full hover:bg-green-600">Sauvegarder</button>
        </form>
    </div>








    <!-- Tableau des guildes -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg mt-8 w-full">
        <h2 class="text-xl font-bold text-center mb-4">Liste des guildes</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-neutral-700">
                <thead>
                    <tr class="bg-neutral-800 text-white">
                        <th class="p-3">Image</th>
                        <th class="p-3">Nom</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Visibilité</th>
                        <th class="p-3">Faction</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guildes as $guilde) : ?>
                        <tr class="bg-neutral-800 hover:bg-neutral-700 transition">
                            <td class="p-3 text-center">
                                <?php if (!empty($guilde['image'])) : ?>
                                    <img src="<?= $guilde['image'] ?>" class="w-16 h-16 rounded-lg mx-auto">
                                <?php else : ?>
                                    <span class="text-gray-400">Aucune image</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3"><?= $guilde['name'] ?></td>
                            <td class="p-3"><?= $guilde['type'] ?></td>
                            <td class="p-3"><?= ucfirst($guilde['visibility']) ?></td>
                            <td class="p-3"><?= $guilde['faction_id'] ? $factions[array_search($guilde['faction_id'], array_column($factions, 'id'))]['name'] : 'Aucune' ?></td>
                            <td class="p-3 text-center">
                                <a href="?edit=<?= $guilde['id'] ?>" class="bg-yellow-500 px-4 py-2 rounded">Modifier</a>
                                <a href="?delete=<?= $guilde['id'] ?>" class="bg-red-500 px-4 py-2 rounded">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
