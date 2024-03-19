<?php
require __DIR__ . '/../config.php'; // Ajustez le chemin selon la structure de votre projet
// require 'config.php';

try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    // Mettre à jour les données utilisateur dans Redis et MySQL
    function updateUser($redis, $pdo, $userId, $newName, $newEmail, $newAddress, $newGender) {
        $userKey = "user:$userId";

        // Mettre à jour les données dans Redis
        if ($redis->exists($userKey)) {
            $userData = [
                'name' => $newName,
                'email' => $newEmail,
                'address' => $newAddress,
                'gender' => $newGender,
            ];
            $redis->hMset($userKey, $userData);
        } else {
            throw new Exception("L'utilisateur avec l'ID $userId n'existe pas dans Redis.");
        }

        // Mettre à jour les données dans MySQL
        updateUserInMySQL($pdo, $userId, $newName, $newEmail, $newAddress, $newGender);
    }

    // Fonction pour mettre à jour un utilisateur dans MySQL
    function updateUserInMySQL($pdo, $userId, $newName, $newEmail, $newAddress, $newGender) {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, address = :address, gender = :gender WHERE id = :id");
        $stmt->execute([
            'name' => $newName,
            'email' => $newEmail,
            'address' => $newAddress,
            'gender' => $newGender,
            'id' => $userId
        ]);
    }

    // Vous pouvez ajouter ici la logique pour appeler updateUser avec des données spécifiques
    // Exemple : updateUser($redis, $conn, 'id_utilisateur', 'Nouveau Nom', 'nouvel_email@example.com', 'Nouvelle Adresse', 'Nouveau Genre');

    // Fermer la connexion Redis
    $redis->disconnect();
} catch (Predis\Response\ServerException $e) {
    echo "Erreur Redis: " . $e->getMessage() . "\n";
} catch (PDOException $e) {
    echo "Erreur PDO: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>
