<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
    exit();
}

// Fonction pour convertir UNIX timestamp → heure lisible
function formatTime($timestamp) {
    return date("H:i:s", $timestamp);
}

// Fonction d'envoi d'alerte Telegram
function sendTelegramAlert($uid, $entryTime) {
    $message = "🚨 UID $uid a une entrée anormale à $entryTime";
    file_get_contents("http://localhost/rfidattendance/telegram.php?msg=" . urlencode($message));
}

// Charger les données Firebase converties en JSON
$passagesFile = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/passages.json"; // ⚠️ Le fichier doit contenir la structure actuelle de Firebase
$jsonData = file_get_contents($passagesFile);
$data = json_decode($jsonData, true);

// Organiser les timestamps par UID
$userLogs = [];

foreach ($data as $uid => $logs) {
    foreach ($logs as $entry) {
        if (isset($entry['time'])) {
            $userLogs[$uid][] = $entry['time'];
        }
    }
}

// Détection des anomalies
$anomalies = [];

foreach ($userLogs as $uid => $timestamps) {
    if (count($timestamps) < 3) continue; // Pas assez de données

    sort($timestamps); // ordre chronologique

    $mean = array_sum($timestamps) / count($timestamps);

    // Écart-type
    $sumSq = 0;
    foreach ($timestamps as $t) {
        $sumSq += pow($t - $mean, 2);
    }
    $stdDev = sqrt($sumSq / count($timestamps));

    $lower = $mean - 2 * $stdDev;
    $upper = $mean + 2 * $stdDev;

    foreach ($timestamps as $t) {
        if ($t < $lower || $t > $upper) {
            $anomalies[] = [
                'uid' => $uid,
                'entry_time' => formatTime($t),
                'mean' => formatTime($mean),
                'std_dev' => gmdate("H:i:s", $stdDev),
                'status' => 'Anomalie détectée'
            ];
            sendTelegramAlert($uid, formatTime($t));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détection d'anomalies</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body class="p-4">
    <h2>🔍 Détection d'Anomalies dans les Heures d'Entrée</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>UID</th>
                <th>Heure d'entrée</th>
                <th>Moyenne (μ)</th>
                <th>Écart-type (σ)</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($anomalies)): ?>
                <tr><td colspan="5">✅ Aucune anomalie détectée.</td></tr>
            <?php else: ?>
                <?php foreach ($anomalies as $anomaly): ?>
                    <tr>
                        <td><?= htmlspecialchars($anomaly['uid']) ?></td>
                        <td><?= $anomaly['entry_time'] ?></td>
                        <td><?= $anomaly['mean'] ?></td>
                        <td><?= $anomaly['std_dev'] ?></td>
                        <td><span class="text-danger"><?= $anomaly['status'] ?></span></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</body>
</html>
