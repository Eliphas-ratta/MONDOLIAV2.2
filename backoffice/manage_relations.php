<?php
require_once '../includes/header.php';

// Récupérer tous les héros
$stmt = $pdo->query("SELECT id, name, image FROM heros");
$heroes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un héros est sélectionné
$selected_hero_id = $_GET['hero_id'] ?? null;
$selected_hero = null;
$related_heroes = [];

if ($selected_hero_id) {
    // Récupérer les infos du héros sélectionné
    $stmt = $pdo->prepare("SELECT id, name, image FROM heros WHERE id = ?");
    $stmt->execute([$selected_hero_id]);
    $selected_hero = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les relations existantes du héros
    $stmt = $pdo->prepare("
        SELECT h.id, h.name, h.image 
        FROM hero_relations hr
        JOIN heros h ON hr.related_hero_id = h.id
        WHERE hr.hero_id = ?
    ");
    $stmt->execute([$selected_hero_id]);
    $related_heroes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Gérer la mise à jour des relations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hero_id'])) {
    $hero_id = $_POST['hero_id'];
    $new_relations = $_POST['related_heroes'] ?? [];

    // Récupérer les relations existantes
    $stmt = $pdo->prepare("SELECT related_hero_id FROM hero_relations WHERE hero_id = ?");
    $stmt->execute([$hero_id]);
    $existing_relations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Déterminer les relations à supprimer (présentes avant mais non sélectionnées)
    $to_remove = array_diff($existing_relations, $new_relations);

    // Supprimer les relations qui ne sont plus sélectionnées
    if (!empty($to_remove)) {
        foreach ($to_remove as $related_id) {
            $stmt = $pdo->prepare("DELETE FROM hero_relations WHERE (hero_id = ? AND related_hero_id = ?) OR (hero_id = ? AND related_hero_id = ?)");
            $stmt->execute([$hero_id, $related_id, $related_id, $hero_id]);
        }
    }

    // Insérer les nouvelles relations
    foreach ($new_relations as $related_id) {
        if ($hero_id != $related_id) { // Empêcher l'auto-relation
            // Vérifier si la relation existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM hero_relations WHERE (hero_id = ? AND related_hero_id = ?) OR (hero_id = ? AND related_hero_id = ?)");
            $stmt->execute([$hero_id, $related_id, $related_id, $hero_id]);
            $relation_exists = $stmt->fetchColumn();

            if (!$relation_exists) {
                // Ajouter relation bidirectionnelle
                $stmt = $pdo->prepare("INSERT INTO hero_relations (hero_id, related_hero_id) VALUES (?, ?)");
                $stmt->execute([$hero_id, $related_id]);

                $stmt = $pdo->prepare("INSERT INTO hero_relations (hero_id, related_hero_id) VALUES (?, ?)");
                $stmt->execute([$related_id, $hero_id]);
            }
        }
    }

    // Rediriger pour éviter le re-submit du formulaire
    header("Location: manage_relations.php?hero_id=$hero_id&success=1");
    exit();
}
?>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-white mb-6">Gérer les relations entre héros</h1>
    <?php if (isset($_GET['success'])) : ?>
        <p class="text-green-500 font-bold">✅ Les relations ont été mises à jour avec succès !</p>
    <?php endif; ?>

    <!-- Sélectionner un héros -->
    <form method="GET" class="mb-6">
        <label class="block text-sm text-white mb-2">Choisir un héros</label>
        <select name="hero_id" class="w-full p-3 bg-neutral-800 text-white rounded-md" onchange="this.form.submit()">
            <option value="">-- Sélectionner un héros --</option>
            <?php foreach ($heroes as $hero) : ?>
                <option value="<?= $hero['id'] ?>" <?= $selected_hero_id == $hero['id'] ? 'selected' : '' ?>>
                    <?= $hero['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($selected_hero) : ?>
        <div class="bg-neutral-900 p-6 rounded-lg">
            <h2 class="text-2xl font-bold text-white mb-4">Relations de <?= $selected_hero['name'] ?></h2>

            <img src="<?= $selected_hero['image'] ?>" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">

            <form method="POST">
                <input type="hidden" name="hero_id" value="<?= $selected_hero['id'] ?>">

                <div class="w-full p-3 bg-neutral-800 text-white rounded-md overflow-y-auto max-h-64">
                    <?php foreach ($heroes as $hero) : ?>
                        <?php if ($hero['id'] != $selected_hero['id']) : ?>
                            <label class="flex items-center space-x-3 p-2 hover:bg-neutral-700 rounded-md cursor-pointer">
                                <input type="checkbox" name="related_heroes[]" value="<?= $hero['id'] ?>" 
                                       <?= in_array($hero['id'], array_column($related_heroes, 'id')) ? 'checked' : '' ?> 
                                       class="form-checkbox text-green-500">
                                <img src="<?= $hero['image'] ?>" class="w-10 h-10 rounded-full object-cover">
                                <span><?= $hero['name'] ?></span>
                            </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Bouton de soumission -->
                <button type="submit" id="updateBtn" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition duration-300">
                    Mettre à jour les relations
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
