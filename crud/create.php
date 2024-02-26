<?php

try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    // Opérations CRUD

    // Fonction pour créer un utilisateur
    function createUser($client, $userId, $userName, $userEmail, $userGender)
    {
        // Utilisation de Redis pour stocker l'utilisateur
        $userData = [
            'id' => $userId,
            'name' => $userName,
            'email' => $userEmail,
            'gender' => $userGender,
        ];

        $key = "user:$userId";
        $client->hmset($key, $userData);
    }

    // Fermer la connexion Redis
    $redis->disconnect();
} catch (Predis\Response\ServerException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}