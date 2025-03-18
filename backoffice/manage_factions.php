<?php
require_once '../includes/header.php'; 

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Récupération des factions et des héros
$factions = $pdo->query("SELECT * FROM factions")->fetchAll(PDO::FETCH_ASSOC);
$heroes = $pdo->query("SELECT * FROM heros")->fetchAll(PDO::FETCH_ASSOC);

// Gestion des actions (ajout/modification)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $regime = trim($_POST['regime']);
    $type = trim($_POST['type']);
    $couleur = trim($_POST['couleur']);
    $capitale = trim($_POST['capitale']);
    $description = trim($_POST['description']);
    $dirigeantes = $_POST['dirigeantes'] ?? []; // Liste des dirigeantes sélectionnées

    // Gestion de l'image uploadée
    $image = $_FILES['image']['name'] ?? null;
    if ($image) {
        $imagePath = '../img/Faction/' . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $_POST['existing_image'] ?? null;
    }

    // Gestion du drapeau (flag) uploadé
    $flag = $_FILES['flag']['name'] ?? null;
    if ($flag) {
        $flagPath = '../img/Faction/Flags/' . basename($flag);
        move_uploaded_file($_FILES['flag']['tmp_name'], $flagPath);
    } else {
        $flagPath = $_POST['existing_flag'] ?? null;
    }


    if ($id) {
        // Modifier une faction
        $stmt = $pdo->prepare("UPDATE factions SET name = ?, regime = ?, type = ?, couleur = ?, capitale = ?, description = ?, image = ?, flag = ? WHERE id = ?");
$stmt->execute([$name, $regime, $type, $couleur, $capitale, $description, $imagePath, $flagPath, $id]);


        // Supprimer les anciennes dirigeantes
        $pdo->prepare("DELETE FROM faction_dirigeants WHERE faction_id = ?")->execute([$id]);

    } else {
        // Ajouter une faction
        $stmt = $pdo->prepare("INSERT INTO factions (name, regime, type, couleur, capitale, description, image, flag) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$name, $regime, $type, $couleur, $capitale, $description, $imagePath, $flagPath]);

        $id = $pdo->lastInsertId();
    }

    // Ajouter les nouvelles dirigeantes
    foreach ($dirigeantes as $hero_id) {
        $stmt = $pdo->prepare("INSERT INTO faction_dirigeants (faction_id, hero_id) VALUES (?, ?)");
        $stmt->execute([$id, $hero_id]);
    }

    // Supprimer l'ancien drapeau si un nouveau est uploadé
if ($flag && !empty($_POST['existing_flag']) && file_exists(__DIR__ . '/../' . $_POST['existing_flag'])) {
    unlink(__DIR__ . '/../' . $_POST['existing_flag']);
}


    header("Location: manage_factions.php");
    exit();
}

// Suppression d'une faction
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM factions WHERE id = ?")->execute([$id]);
    header("Location: manage_factions.php");
    exit();
}

// Récupération des dirigeantes d'une faction lors de la modification
$selectedFaction = null;
$dirigeantes_ids = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $selectedFaction = $pdo->prepare("SELECT * FROM factions WHERE id = ?");
    $selectedFaction->execute([$id]);
    $selectedFaction = $selectedFaction->fetch(PDO::FETCH_ASSOC);

    if ($selectedFaction) {
        $stmt = $pdo->prepare("SELECT hero_id FROM faction_dirigeants WHERE faction_id = ?");
        $stmt->execute([$id]);
        $dirigeantes_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-red-500 mb-8">Manage Faction</h1>

    <!-- Formulaire d'ajout/modification -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-center mb-4">Créer ou modifier une faction</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $selectedFaction['id'] ?? '' ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Nom</label>
                    <input type="text" name="name" value="<?= $selectedFaction['name'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm">Régime</label>
                    <input type="text" name="regime" value="<?= $selectedFaction['regime'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div>
                    <label class="block text-sm">Type</label>
                    <input type="text" name="type" value="<?= $selectedFaction['type'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div>
                    <label class="block text-sm">Couleur</label>
                    <input type="text" name="couleur" value="<?= $selectedFaction['couleur'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div>
                    <label class="block text-sm">Capitale</label>
                    <input type="text" name="capitale" value="<?= $selectedFaction['capitale'] ?? '' ?>" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm">Description</label>
                    <textarea name="description" class="w-full p-3 bg-neutral-800 text-white rounded-md"><?= $selectedFaction['description'] ?? '' ?></textarea>
                </div>
            </div>

            <div class="mt-4">
    <label class="block text-sm">Ajouter des dirigeantes</label>
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
                <label class="block text-sm">Image</label>
                <input type="file" name="image" class="w-full p-3 bg-neutral-800 text-white rounded-md">
                <?php if (!empty($selectedFaction['image'])) : ?>
                    <img src="<?= $selectedFaction['image'] ?>" alt="Faction Image" class="mt-2 w-20 h-20 rounded-lg">
                    <input type="hidden" name="existing_image" value="<?= $selectedFaction['image'] ?>">
                <?php endif; ?>
            </div>

            <div class="mt-4">
    <label class="block text-sm">Drapeau (Flag)</label>
    <input type="file" name="flag" class="w-full p-3 bg-neutral-800 text-white rounded-md">
    <?php if (!empty($selectedFaction['flag'])) : ?>
        <img src="<?= $selectedFaction['flag'] ?>" alt="Faction Flag" class="mt-2 w-20 h-20 rounded-lg">
        <input type="hidden" name="existing_flag" value="<?= $selectedFaction['flag'] ?>">
    <?php endif; ?>
</div>


            <button type="submit" class="mt-4 bg-green-500 text-white p-3 rounded-md w-full hover:bg-green-600">Ajouter</button>
        </form>
    </div>

    <!-- Tableau des factions -->
    <div class="bg-neutral-900 p-6 rounded-lg shadow-lg mt-8 w-full">
        <h2 class="text-xl font-bold text-center mb-4">Liste des factions</h2>
        
        <table class="w-full text-left border-collapse border border-neutral-700">
            <thead>
                <tr class="bg-neutral-800 text-white">
                    <th>Image</th><th>Nom</th><th>Régime</th><th>Type</th><th>Couleur</th><th>Capitale</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($factions as $faction) : ?>
    <tr class="bg-neutral-800 hover:bg-neutral-700 transition">
        <td class="border border-neutral-700 p-3 text-center ">
            <img src="<?= $faction['image'] ?>" class="w-16 h-16 rounded-lg mx-auto">
        </td>
        <td class="border border-neutral-700 p-3"><?= $faction['name'] ?></td>
        <td class="border border-neutral-700 p-3"><?= $faction['regime'] ?></td>
        <td class="border border-neutral-700 p-3"><?= $faction['type'] ?></td>
        <td class="border border-neutral-700 p-3"><?= $faction['couleur'] ?></td>
        
        <td class="border border-neutral-700 p-3 text-center">
    <?php if (!empty($faction['flag'])) : ?>
        <img src="<?= $faction['flag'] ?>" class="w-16 h-16 rounded-lg mx-auto">
    <?php else : ?>
        <span class="text-gray-400">Aucun</span>
    <?php endif; ?>
</td>
        <td class="border border-neutral-700 p-3 text-center">
            <div class="flex items-center justify-center space-x-2">
                <a href="?edit=<?= $faction['id'] ?>" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Modifier</a>
                <a href="?delete=<?= $faction['id'] ?>" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Supprimer</a>
            </div>
        </td>

    </tr>
<?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>