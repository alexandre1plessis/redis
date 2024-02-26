<?php
// Inclure l'autoloader de Predis
require 'vendor/autoload.php';
require 'config.php';

try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    // Fonction pour supprimer un utilisateur spécifique par son ID
    function deleteUser($client, $userId) {
        $userKey = "user:$userId";
        $client->del($userKey);
    }

    // Fonction pour supprimer tous les utilisateurs
    function deleteAllUsers($client)
    {
        // Utilisation de Redis pour stocker les utilisateurs
        $userKeys = $client->keys("user:*");
    
        foreach ($userKeys as $key) {
            $client->del($key);
        }
    }
    // Fermer la connexion Redis
    $redis->disconnect();
} catch (Predis\Response\ServerException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
