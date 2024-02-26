<?php
try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    // Lire les données utilisateur
    function readUser($client, $userId) {
        $userKey = "user:$userId";

        return $client->hGetAll($userKey);
    }

    // Fonction pour lire tous les utilisateurs
    function readAllUsers($client)
    {
        // Utilisation de Redis pour stocker les utilisateurs
        $userKeys = $client->keys("user:*");
        $users = [];
    
        foreach ($userKeys as $key) {
            $userData = $client->hGetAll($key);
        
            if (!empty($userData)) {
                $users[] = $userData;
            }
        }
    
        return $users;
    }
    // Fermer la connexion Redis
    $redis->disconnect();
} catch (Predis\Response\ServerException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
