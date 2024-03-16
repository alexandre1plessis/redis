<?php

include 'config.php'; // Assurez-vous que c'est le bon chemin vers votre fichier config

try {
    // Créer un nouveau client Predis
    $redis = new Predis\Client($redisConfig);

    function createUser($redis, $pdo, $userId, $userName, $userEmail, $userAddress, $userGender) {
        // Vérifier si l'utilisateur est déjà en cache dans Redis
        $key = "user:$userId";
        if (!$redis->exists($key)) {
            // Vérifier si l'email est unique dans la base de données MySQL
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $userEmail]);
            if ($stmt->rowCount() == 0) {
                // Préparer les données pour Redis, y compris l'id
                $userData = [
                    'id' => $userId, // Ajoutez cette ligne pour inclure l'ID
                    'name' => $userName,
                    'email' => $userEmail,
                    'address' => $userAddress,
                    'gender' => $userGender,
                ];
    
                // Ajouter l'utilisateur dans Redis
                $redis->hmset($key, $userData);
    
                // Ajouter l'utilisateur dans MySQL
                $insertQuery = "INSERT INTO users (id, name, email, address, gender) VALUES (:id, :name, :email, :address, :gender)";
                $stmt = $pdo->prepare($insertQuery);
                $stmt->execute([
                    'id' => $userId,
                    'name' => $userName,
                    'email' => $userEmail,
                    'address' => $userAddress,
                    'gender' => $userGender
                ]);
                return "Utilisateur créé avec succès.";
            } else {
                return "L'email est déjà utilisé.";
            }
        } else {
            return "L'utilisateur existe déjà dans le cache.";
        }
    }
    
    

    // Utiliser la fonction createUser
    // Exemple : createUser($redis, $conn, 1, "John Doe", "johndoe@example.com", "123 Main St", "male");

    // Fermer la connexion Redis
    $redis->disconnect();
} catch (Predis\Response\ServerException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} catch (mysqli_sql_exception $e) {
    echo "Erreur MySQL : " . $e->getMessage() . "\n";
}
?>
