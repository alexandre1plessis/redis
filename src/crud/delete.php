<?php
// Inclure l'autoloader de Predis
require '../vendor/autoload.php';
require 'config.php';

try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    // Fonction pour supprimer un utilisateur spécifique par son ID
    function deleteUser($redis, $pdo, $userId) {
        // Supprimer l'utilisateur de Redis
        $userKey = "user:$userId";
        $redis->del($userKey);

        // Supprimer l'utilisateur de MySQL
        deleteUserFromMySQL($pdo, $userId);
    }

    // Fonction pour supprimer un utilisateur de MySQL
    function deleteUserFromMySQL($pdo, $userId) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
    }

    // Exemple : deleteUser($redis, $conn, 'id_utilisateur');

    // Fermer la connexion Redis
    $redis->disconnect();
} catch (Predis\Response\ServerException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "\n";
}
?>
