<?php
// Fonction pour interagir avec Firebase
function firebaseRequest($method, $endpoint, $data = null) {
    $firebaseUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app";
    $url = $firebaseUrl . $endpoint . ".json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// Actions Firebase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'get_devices') {
        // Récupérer les appareils
        $response = firebaseRequest("GET", "/devices");
        $devices = json_decode($response, true);
        
        if ($devices) {
            foreach ($devices as $id => $device) {
                echo "<p><b>" . htmlspecialchars($device['name']) . "</b> (" . htmlspecialchars($device['department']) . ")</p>";
            }
        } else {
            echo "<p>Aucun appareil trouvé.</p>";
        }
    } elseif ($action === 'add_device') {
        // Ajouter un appareil
        $devName = $_POST['dev_name'];
        $devDep = $_POST['dev_dep'];
        
        $data = [
            "name" => $devName,
            "department" => $devDep
        ];
        
        $response = firebaseRequest("POST", "/devices", $data);
        echo "Appareil ajouté avec succès !";
    }
}
?>
