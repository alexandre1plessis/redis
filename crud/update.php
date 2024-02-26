<?php
try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    // Mettre à jour les données utilisateur
    function updateUser($client, $userId, $newName, $newEmail, $newGender) {
        $userKey = "user:$userId";

        if ($client->exists($userKey)) {
            $userData = [
                'name' => $newName,
                'email' => $newEmail,
                'gender' => $newGender,
            ];

            $client->hMset($userKey, $userData);
        } else {
            throw new Exception("L'utilisateur avec l'ID $userId n'existe pas.");
        }
    }
    
    // Fermer la connexion Redis
    $redis->disconnect();
} catch (Predis\Response\ServerException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}

?>
