<?php
// Désactiver l'affichage des erreurs pour production
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_PARSE);

// Fonction pour récupérer l'UID avec le plus grand 'time'
function getLastUID($url) {
    // Récupérer les données JSON
    $json = file_get_contents($url);
    if (!$json) {
        return "Erreur lors de la récupération des données.";
    }

    $data = json_decode($json, true);
    if (!is_array($data)) {
        return "Données invalides.";
    }

    $latestUID = null;
    $latestTime = -1;

    // Parcourir chaque clé de l'objet associatif
    foreach ($data as $key => $entry) {
        if (isset($entry['time']) && $entry['time'] > $latestTime) {
            $latestTime = $entry['time'];
            $latestUID = isset($entry['uid']) ? $entry['uid'] : "UID manquant";
        }
    }

    // Retourner le dernier UID trouvé
    return $latestUID ? $latestUID : "Aucun UID trouvé.";
}

// URL de la base Firebase
$url = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/newuser.json";

// Retourner uniquement le dernier UID
header('Content-Type: text/plain');
echo getLastUID($url);
?>
