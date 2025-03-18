<?php
require_once '../includes/header.php';
?>



<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-white mb-6">Carte de Mondolia</h1>
    
    <!-- Conteneur de la carte -->
    <div id="map" class="w-full h-[80vh] rounded-lg shadow-lg border-2 border-neutral-700"></div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log('Leaflet est chargé :', typeof L !== 'undefined'); // Vérifie si Leaflet est bien chargé

        var w = 4000, h = 3990;
        var url = "/img/carte/Carte_mondolia.png"; // Vérifie aussi ton chemin

        var map = L.map('map', {
            minZoom: -10,
            maxZoom: 3,
            center: [0, 0],
            zoom: -4,
            crs: L.CRS.Simple,
            zoomSnap: 0.5,
        });

        var southWest = map.unproject([0, h], map.getMinZoom());
        var northEast = map.unproject([w, 0], map.getMinZoom());
        var bounds = new L.LatLngBounds(southWest, northEast);

        L.imageOverlay(url, bounds).addTo(map);

        map.fitBounds(bounds);
        map.setMaxBounds(null);
    });
</script>


<?php require_once '../includes/footer.php'; ?>
