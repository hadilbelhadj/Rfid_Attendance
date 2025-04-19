<?php

// URL de la base Firebase
$url = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/passages.json";

// Initialiser le compteur
$count = 0;

// Récupérer les données JSON depuis l'URL
$json_data = file_get_contents($url);
if ($json_data === false) {
    die("Erreur : Impossible de récupérer les données depuis Firebase.");
}

// Décoder les données JSON
$data = json_decode($json_data, true);
if ($data === null) {
    die("Erreur : Impossible de décoder les données JSON.");
}

// Parcourir chaque UID
foreach ($data as $uid => $events) {
    // Compter le nombre d'événements pour cet UID
    $event_count = count($events);

    // Appliquer la logique : ajouter 1 si le nombre d'événements est impair
    if ($event_count % 2 != 0) {
        $count++;
    }
}

// Afficher le résultat final sous forme de JSON
header('Content-Type: application/json');
echo json_encode(["count" => $count]);

?>