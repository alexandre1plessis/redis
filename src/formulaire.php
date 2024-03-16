<?php
// Inclure l'autoloader de Predis
require 'vendor/autoload.php';
require 'config.php';
require 'crud/create.php'; // Assurez-vous que c'est le bon chemin vers votre fichier create.php

// Initialiser la connexion Redis
$client = new Predis\Client($redisConfig);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Générer un ID utilisateur unique
        $userId = uniqid();

        // Récupérer les données du formulaire
        $userName = $_POST['userName'];
        $userEmail = $_POST['userEmail'];
        $userAddress = $_POST['userAddress']; // Récupération de l'adresse
        $userGender = $_POST['userGender'];

        // Ajouter un nouvel utilisateur
        createUser($client, $pdo, $userId, $userName, $userEmail, $userAddress, $userGender);
        echo "Utilisateur ajouté avec succès. ID: $userId";
    } catch (Predis\Response\ServerException $e) {
        echo "Erreur : " . $e->getMessage();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un utilisateur</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#userAddress').on('input', function() {
                var address = $(this).val();
                if (address.length > 3) { // Pour éviter des requêtes trop fréquentes
                    $.ajax({
                        url: 'https://api-adresse.data.gouv.fr/search/?q=' + encodeURIComponent(address),
                        type: 'GET',
                        success: function(data) {
                            $('#addressSuggestions').empty();
                            data.features.forEach(function(feature) {
                                $('#addressSuggestions').append('<option value="' + feature.properties.label + '">');
                            });
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <h1>Ajouter un utilisateur</h1>
    <form method="post">

        <label for="userName">Nom de l'utilisateur:</label>
        <input type="text" id="userName" name="userName" required><br>

        <label for="userEmail">Email de l'utilisateur:</label>
        <input type="email" id="userEmail" name="userEmail" required><br>

        <label for="userAddress">Adresse de l'utilisateur:</label>
        <input type="text" id="userAddress" name="userAddress" list="addressSuggestions" required><br> <!-- Champ pour l'adresse avec suggestions -->
        <datalist id="addressSuggestions"></datalist>

        <label for="userGender">Genre:</label>
        <select id="userGender" name="userGender">
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
            <option value="Autre">Autre</option>
        </select><br>

        <input type="submit" value="Ajouter l'utilisateur">
    </form>
    <br>
    <a href="index.php">Retour à la liste des utilisateurs</a>
</body>
</html>
