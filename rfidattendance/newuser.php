<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>
    <script>
        // Fonction pour actualiser le champ UID toutes les 5 secondes
        function updateUID() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_last_uid.php", true); // Requête AJAX vers le backend
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("uid").value = xhr.responseText; // Mise à jour du champ UID
                }
            };
            xhr.send();
        }

        // Appel initial et actualisation toutes les 5 secondes
        window.onload = function () {
            updateUID();
            setInterval(updateUID, 5000); // Actualiser toutes les 5 secondes
        };
    </script>
</head>
<body>
    <h1>Ajouter un nouvel utilisateur</h1>
    <form method="POST" action="add_user_to_firebase.php">
        <label for="uid">Dernier UID :</label><br>
        <input type="text" id="uid" name="uid" readonly><br>

        <label for="name">Nom complet :</label><br>
        <input type="text" id="name" name="name" required><br>

        <label for="cin">CIN :</label><br>
        <input type="number" id="cin" name="cin" required><br>

        <label for="date">Date de naissance :</label><br>
        <input type="date" id="date" name="date" required><br>

        <label for="gender">Genre :</label><br>
        <select id="gender" name="gender" required>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
        </select><br><br>

        <input type="submit" value="Ajouter l'utilisateur">
    </form>
</body>
</html>
