<?php
// Inclure l'autoloader de Predis et d'autres dépendances
require 'vendor/autoload.php';
require 'config.php';
require 'crud/create.php';
require 'crud/read.php';
require 'crud/update.php';
require 'crud/delete.php';

// Initialiser la connexion Redis
$client = new Predis\Client($redisConfig);

class RequestHandler
{
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function handleCreateRequest($userId, $userName, $userEmail, $userGender)
    {
        try {
            // Appel de la fonction createUser pour créer un nouvel utilisateur
            createUser($this->client, $userId, $userName, $userEmail, $userGender);
            return "Utilisateur ajouté avec succès.";
        } catch (Predis\Response\ServerException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function handleReadRequest()
    {
        try {
            // Appel de la fonction readAllUsers pour lire la liste des utilisateurs
            $userList = readAllUsers($this->client);
            return $userList;
        } catch (Predis\Response\ServerException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function handleUpdateRequest($userIdToUpdate, $updatedName, $updatedEmail, $updatedGender)
    {
        try {
            // Appel de la fonction updateUser pour mettre à jour l'utilisateur
            updateUser($this->client, $userIdToUpdate, $updatedName, $updatedEmail, $updatedGender);
            return "L'utilisateur avec l'ID $userIdToUpdate a été mis à jour avec succès.";
        } catch (Predis\Response\ServerException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function handleDeleteRequest($deleteUserId)
    {
        try {
            // Appel de la fonction deleteUser pour supprimer l'utilisateur
            deleteUser($this->client, $deleteUserId);
            return "L'utilisateur avec l'ID $deleteUserId a été supprimé avec succès.";
        } catch (Predis\Response\ServerException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
}


$requestHandler = new RequestHandler($client);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['createUser'])) {
        $userId = $_POST['userId'];
        $userName = $_POST['userName'];
        $userEmail = $_POST['userEmail'];
        $userGender = $_POST['userGender'];

        $result = $requestHandler->handleCreateRequest($userId, $userName, $userEmail, $userGender);
        echo $result;
    } elseif (isset($_POST['updateUser'])) {
        $userIdToUpdate = $_POST['userId'];
        $updatedName = $_POST['updatedName'];
        $updatedEmail = $_POST['updatedEmail'];
        $updatedGender = $_POST['updatedGender'];

        // Valider et nettoyer les entrées ici
        $result = $requestHandler->handleUpdateRequest($userIdToUpdate, $updatedName, $updatedEmail, $updatedGender);
        // Rediriger vers index.php après la mise à jour
        header('Location: index.php');
        exit;
    } elseif (isset($_POST['deleteUser'])) {
        $deleteUserId = $_POST['deleteUserId'];

        $result = $requestHandler->handleDeleteRequest($deleteUserId);
        // Rediriger vers index.php
        header('Location: index.php');
        echo $result;
    }
}
?>
