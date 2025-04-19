<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualisation des UID</title>
</head>
<body>
    <h2>Nombre d'UID avec un nombre impair d'événements</h2>
    <p id="uid-count">Chargement...</p>

    <script>
        // Fonction pour récupérer les données via AJAX
        function fetchUIDCount() {
            // Création de la requête
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "page.php", true); // Remplace 'ton_script_php.php' par ton fichier PHP

            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse la réponse JSON
                    var response = JSON.parse(xhr.responseText);
                    document.getElementById("uid-count").innerText = response.count;
                } else {
                    console.error("Erreur lors de la récupération des données.");
                }
            };

            xhr.onerror = function() {
                console.error("Erreur réseau.");
            };

            xhr.send();
        }

        // Actualiser toutes les 2 secondes
        setInterval(fetchUIDCount, 2000);

        // Appel initial pour afficher les données dès le chargement de la page
        fetchUIDCount();
    </script>
</body>
</html>