<?php
// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // URL de la base de données Firebase (avec clé basée sur UID)
    $uid = $_POST["uid"] ?? ""; // Récupère la valeur de UID depuis le formulaire
    if (empty($uid)) {
        die("Erreur : UID est requis.");
    }

    $url = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/users/$uid.json";

    // Données à envoyer
    $data = [
        "uid" => $uid,
        "name" => $_POST["name"] ?? "",
        "cin" => $_POST["cin"] ?? "",
        "date" => $_POST["date"] ?? "",
        "gender" => $_POST["gender"] ?? ""
    ];

    // Encodage des données en JSON
    $jsonData = json_encode($data);

    // Initialiser cURL
    $ch = curl_init();
    if (!$ch) {
        die("Erreur : Impossible d'initialiser cURL");
    }

    // Configuration de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Utilisez PUT pour définir une clé spécifique
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactive temporairement la vérification SSL

    // Exécuter la requête
    $response = curl_exec($ch);

    // Vérifiez les erreurs
    if ($response === false) {
        die("Erreur cURL : " . curl_error($ch));
    }

    // Fermer cURL
    curl_close($ch);

    // Redirection vers la page newuser
    header("Location: ManageUsers.php");
    exit; // Arrête l'exécution après la redirection
}
?>
