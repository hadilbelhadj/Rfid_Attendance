<?php
session_start();
require 'vendor/autoload.php';  // Assurez-vous d'avoir chargé PhpSpreadsheet avec Composer ou l'inclure manuellement.

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

// URL des bases de données Firebase
$passagesUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/passages.json";
$usersUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/users.json";

// Fonction pour obtenir les données JSON
function getJsonData($url) {
    $jsonData = file_get_contents($url);
    return json_decode($jsonData, true);
}

// Récupérer les données
$passages = getJsonData($passagesUrl);
$users = getJsonData($usersUrl);

// Récupérer les filtres
$filterUser = isset($_POST['card_sel']) ? $_POST['card_sel'] : '0';
$filterDate = isset($_POST['log_date']) ? $_POST['log_date'] : '';
$filterTime = isset($_POST['log_time']) ? $_POST['log_time'] : '';

// Initialiser le tableau de résultats
$result = [];
foreach ($passages as $key => $passage) {
    $passageDate = date('Y-m-d', $passage['time']);
    $passageTime = date('H:i', $passage['time']);

    if (
        ($filterUser === '0' || $passage['uid'] === $filterUser) &&
        ($filterDate === '' || $passageDate === $filterDate) &&
        ($filterTime === '' || $passageTime === $filterTime)
    ) {
        if (isset($users[$passage['uid']])) {
            $user = $users[$passage['uid']];
            $result[] = [
                'uid' => $passage['uid'],
                'name' => $user['Name'],
                'date' => $passageDate,
                'time_in' => ($passage['status'] == 1) ? date('H:i:s', $passage['time']) : '',
                'time_out' => ($passage['status'] == 0) ? date('H:i:s', $passage['time']) : ''
            ];
        }
    }
}

// Créer un nouvel objet Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'UID');
$sheet->setCellValue('B1', 'Name');
$sheet->setCellValue('C1', 'Date');
$sheet->setCellValue('D1', 'Time In');
$sheet->setCellValue('E1', 'Time Out');

// Remplir les données
$row = 2;
foreach ($result as $log) {
    $sheet->setCellValue('A' . $row, $log['uid']);
    $sheet->setCellValue('B' . $row, $log['name']);
    $sheet->setCellValue('C' . $row, $log['date']);
    $sheet->setCellValue('D' . $row, $log['time_in']);
    $sheet->setCellValue('E' . $row, $log['time_out']);
    $row++;
}

// Créer un writer pour enregistrer le fichier CSV
$writer = new Csv($spreadsheet);
$writer->setDelimiter(","); // Définir le délimiteur comme virgule
$writer->setEnclosure('"'); // Définir l'encapsulation des champs
$writer->setLineEnding("\n");

// Enregistrer le fichier dans le navigateur avec un nom spécifique
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="users_logs.csv"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
