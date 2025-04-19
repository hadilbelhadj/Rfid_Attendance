<?php
// URL de la base Firebase
$passagesUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/passages.json";
$usersUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/users.json";

// Récupérer les passages
$passagesResponse = file_get_contents($passagesUrl);
if ($passagesResponse === FALSE) {
    echo "<p>Impossible de récupérer les données des passages depuis Firebase.</p>";
    exit;
}

// Récupérer les utilisateurs
$usersResponse = file_get_contents($usersUrl);
if ($usersResponse === FALSE) {
    echo "<p>Impossible de récupérer les données des utilisateurs depuis Firebase.</p>";
    exit;
}

// Décoder les réponses JSON
$passagesData = json_decode($passagesResponse, true);
$usersData = json_decode($usersResponse, true);

// Vérifier si des passages existent
if ($passagesData) {
    // Trouver le passage avec la plus grande heure (maximum)
    $lastPassage = null;
    foreach ($passagesData as $uid => $passages) {
        foreach ($passages as $passage) {
            if ($lastPassage === null || $passage['time'] > $lastPassage['time']) {
                $lastPassage = $passage;
                $lastUid = $uid;
            }
        }
    }

    // Récupérer les valeurs avec des valeurs par défaut
    $uid = $lastPassage['uid'] ?? "UID inconnu";
    $time = date('Y-m-d H:i:s', $lastPassage['time']) ?? "Heure non spécifiée"; // Formater l'heure au besoin

    // Vérifier si l'UID existe dans la table "users"
    if (isset($usersData[$lastUid])) {
        $name = $usersData[$lastUid]['name'] ?? "Nom inconnu";
        $gender = $usersData[$lastUid]['gender'] ?? "-";
        $cin = $usersData[$lastUid]['cin'] ?? "-";
    } else {
        $name = "Inconnu";
        $gender = "-";
        $cin = "-";
    }

    // Vérifier si l'image du CIN existe
    $imagePath = "photo_user/{$cin}.jpg";
    $imageHTML = '';
    if (file_exists($imagePath)) {
        $imageHTML = "<img src='{$imagePath}' alt='Photo de l'utilisateur' style='width: 200px; height: 200px; border-radius: 10px;'>";
    } else {
        $imageHTML = "<span>Aucune photo</span>";
    }

    // Générer l'affichage du dernier utilisateur
    $output = "<div style='text-align: center;'>
                    <div style='margin-bottom: 20px;'>{$imageHTML}</div>
                    <p><strong>Nom:</strong> {$name}</p>
                    <p><strong>Genre:</strong> {$gender}</p>
                    <p><strong>CIN:</strong> {$cin}</p>
                    <p><strong>Heure de passage:</strong> {$time}</p>
                </div>";
} else {
    $output = "<p>Aucun passage trouvé.</p>";
}

// Afficher l'output
echo $output;
?>
