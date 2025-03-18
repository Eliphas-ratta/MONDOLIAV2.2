<?php
require_once '../includes/header.php'; // `header.php` contient déjà `config.php`

// Vérification : seul un admin peut accéder
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Définition des sections du backoffice
$sections = [
    ["title" => "Factions", "image" => BASE_URL . "img/default/Faction.jpeg", "link" => BASE_URL . "backoffice/manage_factions.php"],
    ["title" => "Guildes", "image" => BASE_URL . "img/default/Guilde.jpeg", "link" => BASE_URL . "backoffice/manage_guildes.php"],
    ["title" => "Héros", "image" => BASE_URL . "img/default/Hero.jpeg", "link" => BASE_URL . "backoffice/manage_heros.php"],
    ["title" => "Races", "image" => BASE_URL . "img/default/Races.jpeg", "link" => BASE_URL . "backoffice/manage_races.php"],
    ["title" => "Contextes", "image" => BASE_URL . "img/default/Contexte.jpeg", "link" => BASE_URL . "backoffice/manage_contextes.php"],
    ["title" => "Relations", "image" => BASE_URL . "img/default/Relation.jpeg", "link" => BASE_URL . "backoffice/manage_relations.php"],
];

?>

<div class="container mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-center mb-8">Tableau de Bord</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($sections as $section) : ?>
            <div class="bg-neutral-900 p-4 rounded-lg shadow-lg text-center">
                <!-- Vérification si l'image existe -->
                <?php if (file_exists(str_replace(BASE_URL, '../', $section['image']))) : ?>
                    <img src="<?= $section['image'] ?>" alt="<?= $section['title'] ?>" class="w-full  object-cover rounded-lg">
                <?php else : ?>
                    <div class="w-full h-32 bg-gray-700 rounded-lg flex items-center justify-center text-gray-300">
                        Aucune image
                    </div>
                <?php endif; ?>
                <h2 class="text-lg font-bold mt-2"><?= $section['title'] ?></h2>
                <a href="<?= $section['link'] ?>" class="inline-block mt-3 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition">
                    Gérer
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
