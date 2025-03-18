<?php
require_once '../includes/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Récupération des héros, races, guildes et factions
$heroes = $pdo->query("SELECT * FROM heros")->fetchAll(PDO::FETCH_ASSOC);
$races = $pdo->query("SELECT * FROM races")->fetchAll(PDO::FETCH_ASSOC);
$guildes = $pdo->query("SELECT * FROM guildes")->fetchAll(PDO::FETCH_ASSOC);
$factions = $pdo->query("SELECT * FROM factions")->fetchAll(PDO::FETCH_ASSOC);


// Tri des héros par ID pour l'ordre
usort($heroes, function ($a, $b) {
    return $b['id'] <=> $a['id'];
});



// Gestion des actions (ajout/modification)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $age = trim($_POST['age']);
    $taille = trim($_POST['taille']);
    $fonction = trim($_POST['fonction']);
    $description = trim($_POST['description']);
    $race_id = !empty($_POST['race_id']) ? $_POST['race_id'] : null;
    $guilde_id = !empty($_POST['guilde_id']) ? $_POST['guilde_id'] : null;
    $faction_id = !empty($_POST['faction_id']) ? $_POST['faction_id'] : null;

    // Gestion de l'image
    $existingImage = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $imagePath = '../img/hero/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $existingImage;
    }

    if ($id) {
        // Modifier un héros
        $stmt = $pdo->prepare("UPDATE heros SET name = ?, age = ?, taille = ?, fonction = ?, description = ?, race_id = ?, guilde_id = ?, faction_id = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $age, $taille, $fonction, $description, $race_id, $guilde_id, $faction_id, $imagePath, $id]);
    } else {
        // Ajouter un nouveau héros
        $stmt = $pdo->prepare("INSERT INTO heros (name, age, taille, fonction, description, race_id, guilde_id, faction_id, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $age, $taille, $fonction, $description, $race_id, $guilde_id, $faction_id, $imagePath]);
    }

    header("Location: manage_heros.php");
    exit();
}

// Suppression d'un héros
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM heros WHERE id = ?")->execute([$id]);
    header("Location: manage_heros.php");
    exit();
}

// Récupération des détails d'un héros lors de la modification
$selectedHero = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $selectedHero = $pdo->prepare("SELECT * FROM heros WHERE id = ?");
    $selectedHero->execute([$id]);
    $selectedHero = $selectedHero->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-red-500 mb-8">Manage Héros</h1>

    <!-- Formulaire d'ajout/modification -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-center mb-4">Créer ou modifier un héros</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $selectedHero['id'] ?? '' ?>">
            <input type="hidden" name="existing_image" value="<?= $selectedHero['image'] ?? '' ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Nom</label>
                    <input type="text" name="name" value="<?= $selectedHero['name'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm">Âge</label>
                    <input type="text" name="age" value="<?= $selectedHero['age'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div>
                    <label class="block text-sm">Taille</label>
                    <input type="text" name="taille" value="<?= $selectedHero['taille'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div>
                    <label class="block text-sm">Fonction</label>
                    <input type="text" name="fonction" value="<?= $selectedHero['fonction'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm">Description</label>
                    <textarea name="description" class="w-full p-3 bg-neutral-800 text-white rounded-md"><?= $selectedHero['description'] ?? '' ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm">Image</label>
                <input type="file" name="image" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                <?php if (!empty($selectedHero['image'])) : ?>
                    <img src="<?= $selectedHero['image'] ?>" class="w-20 h-20 rounded-lg mt-2">
                <?php endif; ?>
            </div>

            <div class="mt-4">
                <label class="block text-sm">Race associée</label>
                <select name="race_id" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                    <option value="">Aucune</option>
                    <?php foreach ($races as $race) : ?>
                        <option value="<?= $race['id'] ?>" <?= isset($selectedHero['race_id']) && $selectedHero['race_id'] == $race['id'] ? 'selected' : '' ?>>
                            <?= $race['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-4">
                <label class="block text-sm">Guilde associée</label>
                <select name="guilde_id" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                    <option value="">Aucune</option>
                    <?php foreach ($guildes as $guilde) : ?>
                        <option value="<?= $guilde['id'] ?>" <?= isset($selectedHero['guilde_id']) && $selectedHero['guilde_id'] == $guilde['id'] ? 'selected' : '' ?>>
                            <?= $guilde['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-4">
                <label class="block text-sm">Faction associée</label>
                <select name="faction_id" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                    <option value="">Aucune</option>
                    <?php foreach ($factions as $faction) : ?>
                        <option value="<?= $faction['id'] ?>" <?= isset($selectedHero['faction_id']) && $selectedHero['faction_id'] == $faction['id'] ? 'selected' : '' ?>>
                            <?= $faction['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="mt-4 bg-green-500 text-white p-3 rounded-md w-full hover:bg-green-600">Sauvegarder</button>
        </form>
    </div>


    <!-- Tableau des héros -->
    <!-- Tableau des héros -->
<div class="bg-neutral-900 p-6 rounded-lg shadow-lg mt-8 w-full">
    <h2 class="text-xl font-bold text-center mb-4">Liste des héros</h2>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse border border-neutral-700">
            <thead>
                <tr class="bg-neutral-800 text-white">
                    <th class="p-3">Image</th>
                    <th class="p-3">Nom</th>
                    <th class="p-3">Race</th>
                    <th class="p-3">Guilde</th>
                    <th class="p-3">Faction</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($heroes as $hero) : ?>
                    <tr class="bg-neutral-800 hover:bg-neutral-700 transition">
                        <td class="p-3 text-center">
                            <img src="<?= $hero['image'] ?>" class="w-16 h-16 rounded-lg mx-auto">
                        </td>
                        <td class="p-3"><?= htmlspecialchars($hero['name']) ?></td>
                        <td class="p-3">
                            <?= $hero['race_id'] ? htmlspecialchars($races[array_search($hero['race_id'], array_column($races, 'id'))]['name']) : 'Aucune' ?>
                        </td>
                        <td class="p-3">
                            <?= $hero['guilde_id'] ? htmlspecialchars($guildes[array_search($hero['guilde_id'], array_column($guildes, 'id'))]['name']) : 'Aucune' ?>
                        </td>
                        <td class="p-3">
    <?= $hero['faction_id'] ? htmlspecialchars($factions[array_search($hero['faction_id'], array_column($factions, 'id'))]['name']) : 'Aucune' ?>
</td>

                        <td class="p-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="?edit=<?= $hero['id'] ?>" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Modifier</a>
                                <a href="?delete=<?= $hero['id'] ?>" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce héros ?');">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


