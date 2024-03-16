<?php
// Démarrer la session
session_start();

// Inclure l'autoloader de Predis et la configuration
require '../vendor/autoload.php';
require 'config.php';
require 'crud/read.php';

// Initialiser la connexion Redis
$redis = new Predis\Client($redisConfig);

try {
    // Lire la liste des utilisateurs
    $userList = readAllUsers($redis, $pdo);
} catch (Predis\Response\ServerException $e) {
    echo "Erreur Redis: " . $e->getMessage();
} catch (PDOException $e) {
    echo "Erreur PDO: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des utilisateurs</title>
    <link rel="stylesheet" type="text/css" href="affichage.css">
</head>
<body>
    <h1>Liste des utilisateurs</h1>
    <?php if (!empty($userList)) : ?>
        <ul>
            <?php foreach ($userList as $user) : ?>
                <li class="display">
                    ID : <?php echo htmlspecialchars($user['id']); ?><br>
                    Nom : <?php echo htmlspecialchars($user['name']); ?><br>
                    Email : <?php echo htmlspecialchars($user['email']); ?><br>
                    Genre : <?php echo htmlspecialchars($user['gender']); ?><br>
                    Adresse : <?php echo htmlspecialchars($user['address']); ?><br> <!-- Affichage de l'adresse -->
                    
                    <!-- Formulaire pour supprimer l'utilisateur -->
                    <form method="post" action="RequestHandler.php">
                        <input type="hidden" name="deleteUserId" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <input type="submit" name="deleteUser" value="Supprimer">
                    </form>

                    <!-- Lien pour mettre à jour l'utilisateur -->
                    <form method="post" action="update.php">
                        <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <input type="submit" value="Mettre à jour">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Aucun utilisateur n'a été ajouté.</p>
    <?php endif; ?>

    <form method="post" action="RequestHandler.php">
        <input type="submit" name="deleteAll" value="Supprimer tous les utilisateurs">
    </form>
    <a href="formulaire.php">Ajouter des utilisateurs</a>
</body>
</html>
