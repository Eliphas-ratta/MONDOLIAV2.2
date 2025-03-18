<?php 
require_once '../includes/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare("SELECT id, password, role FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php");
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>img/default/favicon-32x32.png">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-900 flex items-center justify-center h-screen">

    <div class="bg-neutral-800 text-white rounded-lg shadow-lg p-8 w-96">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        <?php if (isset($error)) : ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <!-- Champ Username -->
            <div>
                <label class="block text-sm mb-1">Username</label>
                <input type="text" name="username" class="w-full p-3 bg-neutral-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-500" required>
            </div>

            <!-- Champ Password -->
            <div>
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password" class="w-full p-3 bg-neutral-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-500" required>
            </div>

            <!-- Bouton Login -->
            <button type="submit" class="w-full bg-red-500 text-white p-3 rounded-md hover:bg-red-600 transition">
                Login
            </button>
        </form>

        <!-- Lien vers Register -->
        <p class="text-center text-sm text-red-400 mt-4 italic">
            Pas encore de compte ? <a href="register.php" class="text-red-500 hover:underline">Register</a>
        </p>
    </div>

</body>
</html>
