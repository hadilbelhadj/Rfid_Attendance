<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="icon" type="image/png" href="icon/ok_check.png"> -->
    <link rel="stylesheet" type="text/css" href="css/userslog.css">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha1256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
    </script>   
    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script src="js/user_log.js"></script>
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
    <script>
      $(document).ready(function(){
        $.ajax({
          url: "user_log_up.php",
          type: 'POST',
          data: {
              'select_date': 1,
          }
          }).done(function(data) {
            $('#userslog').html(data);
          });

        setInterval(function(){
          $.ajax({
            url: "user_log_up.php",
            type: 'POST',
            data: {
                'select_date': 0,
            }
            }).done(function(data) {
              $('#userslog').html(data);
            });
        },5000);
      });
    </script>
<link rel="stylesheet" type="text/css" href="css/userslog.css"></head>
<body>
<?php include'header.php'; ?> 
    <h1 class="slideInDown animated">Passed Card</h1>
    <table style="width: 100%; height: 100vh; text-align: center; vertical-align: middle;">

        <thead>
            <tr>
               
                
            </tr>
        </thead>
        <tbody id="passages">
            <!-- Les données seront insérées ici -->
        </tbody>
       
<div id="uid-box">
    <p id="uid-count">Chargement...</p>
</div>

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

<style>
    #uid-box {
        width: 200px;
        padding: 15px;
        border: 2px solid #007BFF;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        font-family: Arial, sans-serif;
        margin: 10px auto;
        color: #007BFF;
    }

    #uid-count {
        font-size: 20px;
        font-weight: bold;
    }
</style>


    </table>

    <script>
        function chargerPassages() {
            $.ajax({
                url: "fetch_passages.php",
                method: "GET",
                success: function(data) {
                    $("#passages").html(data);
                },
                error: function() {
                    console.error("Erreur lors de la récupération des données.");
                }
            });
        }

        // Rafraîchit toutes les 2 secondes
        setInterval(chargerPassages, 2000);
        chargerPassages(); // Charger immédiatement lors du chargement de la page
    </script>





</body>
</html>
