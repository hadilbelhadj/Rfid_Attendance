<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
    exit();
}

// URL de la base de données Firebase
define('FIREBASE_DB_URL', 'https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app');

// Fonction pour récupérer les données de Firebase
function getFirebaseData($path)
{
    $url = FIREBASE_DB_URL . '/' . $path . '.json';
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0, // Désactiver la vérification SSL
    ]);

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo '<p class="error">Erreur cURL : ' . curl_error($curl) . '</p>';
        return null;
    }
    curl_close($curl);

    return json_decode($response, true);
}


// Récupérer les utilisateurs depuis Firebase
$users = getFirebaseData('users');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <script>
      $(window).on("load resize", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
</head>
<body>
<?php include 'header.php'; ?>
<main>
<section>
  <h1 class="slideInDown animated">Here are all the Users</h1>
  <!--User table-->
  <div class="table-responsive slideInRight animated" style="max-height: 400px;"> 
    <table class="table">
      <thead class="table-primary">
        <tr>
          <th>Card UID</th>
          <th>Name</th>
          <th>Gender</th>
          <th>CIN</th>
          <th>Date</th>
          
        </tr>
      </thead>
      <tbody class="table-secondary">
        <?php if (!empty($users)): ?>
          <?php foreach ($users as $id => $user): ?>
            <tr>
              <td><?php echo $id  . htmlspecialchars($user['username'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($user['name'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($user['gender'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($user['cin'] ?? ''); ?></td>
              <td>
    <?php 
    $timestamp = is_numeric($user['date'] ?? '') ? (int) $user['date'] : strtotime($user['date'] ?? ''); 
    echo htmlspecialchars(date('d/m/Y', $timestamp ?: 0)); 
    ?>
</td>              
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">No users found in the database.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
</main>
</body>
</html>

