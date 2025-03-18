<?php
require_once '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Récupération des races
$races = $pdo->query("SELECT * FROM races")->fetchAll(PDO::FETCH_ASSOC);

usort($races, function ($a, $b) {
    return $b['id'] <=> $a['id'];
});


// Gestion des actions (ajout/modification)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $taille = trim($_POST['taille']);
    $description = trim($_POST['description']);

    // Gestion de l'image
    $existingImage = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $imagePath = '../img/race/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $existingImage;
    }

    if ($id) {
        // Modifier une race
        $stmt = $pdo->prepare("UPDATE races SET name = ?, taille = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $taille, $description, $imagePath, $id]);
    } else {
        // Ajouter une nouvelle race
        $stmt = $pdo->prepare("INSERT INTO races (name, taille, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $taille, $description, $imagePath]);
    }

    header("Location: manage_races.php");
    exit();
}

// Suppression d'une race
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM races WHERE id = ?")->execute([$id]);
    header("Location: manage_races.php");
    exit();
}

// Récupération d'une race pour modification
$selectedRace = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $selectedRace = $pdo->prepare("SELECT * FROM races WHERE id = ?");
    $selectedRace->execute([$id]);
    $selectedRace = $selectedRace->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-red-500 mb-8">Manage Races</h1>

    <!-- Formulaire d'ajout/modification -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-center mb-4">Créer ou modifier une race</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $selectedRace['id'] ?? '' ?>">
            <input type="hidden" name="existing_image" value="<?= $selectedRace['image'] ?? '' ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Nom</label>
                    <input type="text" name="name" value="<?= $selectedRace['name'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm">Taille</label>
                    <input type="text" name="taille" value="<?= $selectedRace['taille'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm">Description</label>
                    <textarea name="description" class="w-full p-3 bg-neutral-800 text-white rounded-md"><?= $selectedRace['description'] ?? '' ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm">Image</label>
                <input type="file" name="image" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                <?php if (!empty($selectedRace['image'])) : ?>
                    <img src="<?= $selectedRace['image'] ?>" class="w-20 h-20 rounded-lg mt-2">
                <?php endif; ?>
            </div>

            <button type="submit" class="mt-4 bg-green-500 text-white p-3 rounded-md w-full hover:bg-green-600">Sauvegarder</button>
        </form>
    </div>

    <!-- Tableau des races -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg mt-8 w-full">
        <h2 class="text-xl font-bold text-center mb-4">Liste des races</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-neutral-700">
                <thead>
                    <tr class="bg-neutral-800 text-white">
                        <th class="p-3">Image</th>
                        <th class="p-3">Nom</th>
                        <th class="p-3">Taille</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($races as $race) : ?>
                        <tr class="bg-neutral-800 hover:bg-neutral-700 transition">
                            <td class="p-3 text-center">
                                <?php if (!empty($race['image'])) : ?>
                                    <img src="<?= $race['image'] ?>" class="w-16 h-16 rounded-lg mx-auto">
                                <?php else : ?>
                                    <span class="text-gray-400">Aucune image</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3"><?= $race['name'] ?></td>
                            <td class="p-3"><?= $race['taille'] ?></td>
                            <td class="p-3 text-center">
                                <a href="?edit=<?= $race['id'] ?>" class="bg-yellow-500 px-4 py-2 rounded">Modifier</a>
                                <a href="?delete=<?= $race['id'] ?>" class="bg-red-500 px-4 py-2 rounded">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
