<?php
session_start(); // Démarrer la session

// Inclure l'autoloader de Predis
require 'vendor/autoload.php';
require 'config.php';
require 'crud/read.php';
require 'crud/update.php';

// Initialiser la connexion Redis
$client = new Predis\Client($redisConfig);

// Vérifier si l'ID utilisateur est passé via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userId'])) {
    $userIdToUpdate = $_POST['userId'];
    try {
        // Lire les données de l'utilisateur à mettre à jour
        $userDataToUpdate = readUser($client, $userIdToUpdate);
    } catch (Predis\Response\ServerException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    // Gérer le cas où l'ID utilisateur n'est pas fourni
    echo "Aucun utilisateur spécifié pour la mise à jour.";
    // Optionnellement, rediriger vers une autre page
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Mettre à jour l'utilisateur</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Mettre à jour l'utilisateur</h1>
    <?php if (isset($userDataToUpdate)) : ?>
    <form method="post" action="RequestHandler.php">
        <input type="hidden" name="userId" value="<?php echo $userDataToUpdate['id']; ?>">
        
        <label for="updatedName">Nom de l'utilisateur:</label>
        <input type="text" id="updatedName" name="updatedName" value="<?php echo $userDataToUpdate['name']; ?>" required><br>
        
        <label for="updatedEmail">Email de l'utilisateur:</label>
        <input type="email" id="updatedEmail" name="updatedEmail" value="<?php echo $userDataToUpdate['email']; ?>" required><br>

        <label for="updatedGender">Genre actuel:</label>
        <span><?php echo $userDataToUpdate['gender']; ?></span><br>

        <label for="updatedGender">Modifier le genre:</label>
        <select id="updatedGender" name="updatedGender">
            <option value="Homme" <?php echo ($userDataToUpdate['gender'] === 'Homme') ? 'selected' : ''; ?>>Homme</option>
            <option value="Femme" <?php echo ($userDataToUpdate['gender'] === 'Femme') ? 'selected' : ''; ?>>Femme</option>
            <option value="Autre" <?php echo ($userDataToUpdate['gender'] === 'Autre') ? 'selected' : ''; ?>>Autre</option>
        </select><br>

        <input type="submit" name="updateUser" value="Mettre à jour">
    </form>
    <?php else : ?>
        <p>Utilisateur non trouvé.</p>
    <?php endif; ?>

</body>
</html>