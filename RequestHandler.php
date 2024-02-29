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
    private $pdo;

    public function __construct($client, $pdo)
    {
        $this->client = $client;
        $this->pdo = $pdo;
    }

    public function handleCreateRequest($userId, $userName, $userEmail, $userAddress, $userGender)
    {
        try {
            // Mise à jour: inclure $userAddress dans l'appel de la fonction
            createUser($this->client, $userId, $userName, $userEmail, $userAddress, $userGender);
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

    public function handleUpdateRequest($userIdToUpdate, $updatedName, $updatedEmail, $updatedAddress, $updatedGender)
    {
        try {
            // Mise à jour: inclure $updatedAddress dans l'appel de la fonction
            updateUser($this->client, $this->pdo, $userIdToUpdate, $updatedName, $updatedEmail, $updatedAddress, $updatedGender);
            return "L'utilisateur avec l'ID $userIdToUpdate a été mis à jour avec succès.";
        } catch (Predis\Response\ServerException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function handleDeleteRequest($deleteUserId)
    {
        try {
            // Appel de la fonction deleteUser pour supprimer l'utilisateur
            deleteUser($this->client, $this->pdo, $deleteUserId);
            return "L'utilisateur avec l'ID $deleteUserId a été supprimé avec succès.";
        } catch (Predis\Response\ServerException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
}

$pdo = new PDO($server, $username, $password);
$requestHandler = new RequestHandler($client, $pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['createUser'])) {
        // Mise à jour: récupérer $userAddress de $_POST
        $userId = $_POST['userId'];
        $userName = $_POST['userName'];
        $userEmail = $_POST['userEmail'];
        $userAddress = $_POST['userAddress']; // Ajout de cette ligne
        $userGender = $_POST['userGender'];

        // Mise à jour: passer $userAddress à la fonction
        $result = $requestHandler->handleCreateRequest($userId, $userName, $userEmail, $userAddress, $userGender);
        echo $result;
    } elseif (isset($_POST['updateUser'])) {
        // Mise à jour: récupérer $updatedAddress de $_POST
        $userIdToUpdate = $_POST['userId'];
        $updatedName = $_POST['updatedName'];
        $updatedEmail = $_POST['updatedEmail'];
        $updatedAddress = $_POST['updatedAddress']; // Ajout de cette ligne
        $updatedGender = $_POST['updatedGender'];

        // Mise à jour: passer $updatedAddress à la fonction
        $result = $requestHandler->handleUpdateRequest($userIdToUpdate, $updatedName, $updatedEmail, $updatedAddress, $updatedGender);
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
