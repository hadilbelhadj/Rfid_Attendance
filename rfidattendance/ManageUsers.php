<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/manageusers.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha1256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script src="js/manage_users.js"></script>

    <!-- Firebase JS SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.17.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.17.2/firebase-database.js"></script>

    <script>
        // Function to refresh the UID field every 5 seconds
        function updateUID() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_last_uid.php", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("uid").value = xhr.responseText;
                }
            };
            xhr.send();
        }

        window.onload = function () {
            updateUID();
            setInterval(updateUID, 5000); // Refresh UID every 5 seconds
        };
    </script>
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <h1 class="slideInDown animated">Add a new User</h1>
    <div class="form-style-5 slideInDown animated">
        <!-- Form for adding a new user -->
        <form method="POST" action="add_user_to_firebase.php">
            <div class="alert_user"></div>
            <fieldset>
                <legend><span class="number">1</span> User Info</legend>
                <input type="text" name="uid" id="uid" placeholder="Enter UID" readonly><br>

                <input type="text" name="name" id="name" placeholder="User Name..." required><br>
                <input type="number" name="cin" id="cin" placeholder="CIN" required><br>
                <input type="date" name="date" id="date" placeholder="Date of Birth" required><br>
            </fieldset>

            <fieldset>
                <legend><span class="number">2</span> Additional Info</legend>
                <label for="gender"><b>Gender:</b></label><br>
                <input type="radio" name="gender" value="Female"> Female
                <input type="radio" name="gender" value="Male" checked> Male
                <br>

                
            </fieldset>

            <!-- Buttons for adding, updating, and removing users -->
            <button type="submit" name="user_add" class="user_add">Add User</button>
            
        </form>
    </div>

    <div class="section">
        <div class="slideInRight animated">
            <div id="manage_users"></div>
        </div>
    </div>
</main>

</body>
</html>
