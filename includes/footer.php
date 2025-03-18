<footer class="bg-neutral-900 text-white p-6 mt-10">
    <div class="container mx-auto text-center">
        <p class="text-gray-400">&copy; <?php echo date("Y"); ?> Mondolia. Tous droits réservés.</p>
        <nav class="mt-4">
            <a href="pages/factions.php" class="hover:text-gray-300 mx-2">Factions</a>
            <a href="pages/guildes.php" class="hover:text-gray-300 mx-2">Guildes</a>
            <a href="pages/races.php" class="hover:text-gray-300 mx-2">Races</a>
            <a href="pages/heros.php" class="hover:text-gray-300 mx-2">Héros</a>
            <a href="pages/contextes.php" class="hover:text-gray-300 mx-2">Contextes</a>
            <a href="pages/carte.php" class="hover:text-gray-300 mx-2">Carte</a>
        </nav>
    </div>
</footer>

 </body>
 </html>

 <?php
ob_end_flush(); // Envoie le contenu et libère le tampon
?>
