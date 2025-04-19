<?php

$botToken = "7211177622:AAFFOfnwitfpg8y3lz7wTAzsx7P0lZSCXyA";  
$chatId = "1427781182";
$message = "Coucou Hadil 🌟 ! Voici ta notification via Telegram bot.";

// URL de l'API Telegram
$url = "https://api.telegram.org/bot$botToken/sendMessage";

// Données à envoyer
$data = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML'
];

// cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

// En cas d'erreur
if ($response === false) {
    echo "Erreur cURL : " . curl_error($ch);
} else {
    echo "Réponse Telegram : " . $response;
}

curl_close($ch);

?>
