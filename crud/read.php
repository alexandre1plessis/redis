<?php
require 'vendor/autoload.php';
require 'config.php';

try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    // Fonction pour lire les données d'un utilisateur spécifique
    function readUser($redis, $pdo, $userId) {
        $userKey = "user:$userId";

        // Tenter de récupérer l'utilisateur depuis Redis
        $userData = $redis->hGetAll($userKey);

        if (empty($userData)) {
            // Si l'utilisateur n'est pas trouvé dans Redis, le chercher dans MySQL
            $userData = readUserFromMySQL($pdo, $userId);
            if (!empty($userData)) {
                // Optionnel : mettre à jour Redis avec les données de l'utilisateur
                $redis->hMset($userKey, $userData);
            }
        }

        return $userData;
    }

    // Fonction pour lire un utilisateur depuis MySQL
    function readUserFromMySQL($pdo, $userId) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fonction pour lire tous les utilisateurs
    function readAllUsers($redis, $pdo) {
        $userKeys = $redis->keys("user:*");
        $users = [];

        foreach ($userKeys as $key) {
            $userData = $redis->hGetAll($key);

            if (empty($userData)) {
                // Lire les données depuis MySQL si elles ne sont pas dans Redis
                $userId = str_replace('user:', '', $key);
                $userData = readUserFromMySQL($pdo, $userId);
                if (!empty($userData)) {
                    // Optionnel : mettre à jour Redis avec les données de l'utilisateur
                    $redis->hMset($key, $userData);
                }
            }

            if (!empty($userData)) {
                $users[] = $userData;
            }
        }

        return $users;
    }

    // Vous pouvez ajouter ici la logique pour appeler readUser ou readAllUsers avec des ID spécifiques

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
