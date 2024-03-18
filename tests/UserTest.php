<?php

use PHPUnit\Framework\TestCase;
use Predis\Client as PredisClient;
require 'src/crud/create.php';
require 'src/crud/update.php';

class UserTest extends TestCase
{
    private $client;
    private $pdo;

    protected function setUp(): void
    {
        // Mockery pour simuler l'objet Predis\Client
        $this->client = Mockery::mock(PredisClient::class)->makePartial();
        // Autoriser le mocking des méthodes protégées (et donc des méthodes magiques)
        $this->client->shouldAllowMockingProtectedMethods();
        // Configurer le mock pour hmset
        $this->client->shouldReceive('hmset')->andReturn(true);
        // Mockery pour simuler l'objet PDO
        $this->pdo = Mockery::mock('PDO');
    }

    protected function mockRedisExists($userId, $exists)
    {
        // Définir le comportement de la méthode 'exists' avec Mockery
        $this->client->shouldReceive('exists')
            ->with("user:$userId")
            ->andReturn($exists);
    }

    protected function mockPdoForEmailCheck($email, $emailExists)
    {
        // Créer une simulation pour PDOStatement
        $stmt = Mockery::mock(PDOStatement::class);
        $stmt->shouldReceive('execute')->with(['email' => $email])->andReturn(true);
        $stmt->shouldReceive('rowCount')->andReturn($emailExists ? 1 : 0);

        // Définir le comportement de la méthode 'prepare' de PDO avec Mockery
        $this->pdo->shouldReceive('prepare')
            ->with("SELECT id FROM users WHERE email = :email")
            ->andReturn($stmt);
    }


    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateUserSuccess()
    {
        $userId = uniqid();
        $userName = 'Test User';
        $userEmail = 'test@example.com';
        $userAddress = '123 Test Address';
        $userGender = 'Homme';

        $this->mockRedisExists($userId, false);
        $this->mockPdoForEmailCheck($userEmail, false);

        // Simuler une insertion réussie en base de données avec Mockery
        $stmt = Mockery::mock(PDOStatement::class);
        $stmt->shouldReceive('execute')->andReturn(true);
        $this->pdo->shouldReceive('prepare')->andReturn($stmt);

        // Assume createUser() returns a success message on successful creation
        $result = createUser($this->client, $this->pdo, $userId, $userName, $userEmail, $userAddress, $userGender);
        $this->assertEquals("Utilisateur créé avec succès.", $result);
    }


    public function testCreateUserEmailUsed()
    {
        $userId = uniqid();
        $userName = 'Test User';
        $userEmail = 'existing@example.com'; // Assume this email is already taken
        $userAddress = '123 Test Address';
        $userGender = 'Homme';

        $this->mockRedisExists($userId, false);
        $this->mockPdoForEmailCheck($userEmail, true);

        // Simuler une insertion réussie en base de données
        $stmt = Mockery::mock(PDOStatement::class);
        $stmt->shouldReceive('execute')->andReturn(true);
        $this->pdo->shouldReceive('prepare')->andReturn($stmt);

        // Assume createUser() returns an error message if email is already used
        $result = createUser($this->client, $this->pdo, $userId, $userName, $userEmail, $userAddress, $userGender);
        $this->assertEquals("L'email est déjà utilisé.", $result);
    }

    public function testCreateUserAlreadyInCache()
    {
        $userId = 'existingUserId'; // Assume this user ID is already in cache
        $userName = 'Test User';
        $userEmail = 'test@example.com';
        $userAddress = '123 Test Address';
        $userGender = 'Homme';

        $this->mockRedisExists($userId, true);
        $this->mockPdoForEmailCheck($userEmail, false);

        // Simuler une insertion réussie en base de données
        $stmt = Mockery::mock(PDOStatement::class);
        $stmt->shouldReceive('execute')->andReturn(true);
        $this->pdo->shouldReceive('prepare')->andReturn($stmt);

        // Assume createUser() returns an error message if user already exists in cache
        $result = createUser($this->client, $this->pdo, $userId, $userName, $userEmail, $userAddress, $userGender);
        $this->assertEquals("L'utilisateur existe déjà dans le cache.", $result);
    }

    public function testUpdateUserSuccess()
    {
        $userId = 'existingUserId';
        $newName = 'Updated Name';
        $newEmail = 'updated@example.com';
        $newAddress = 'Updated Address';
        $newGender = 'Femme';

        // Simuler que l'utilisateur existe dans le cache Redis
        $this->mockRedisExists($userId, true);

        // Simuler la réponse de la mise à jour dans Redis
        $this->client->shouldReceive('hMset')->andReturn(true);

        // Simuler la réponse de la mise à jour dans MySQL
        $stmt = Mockery::mock(PDOStatement::class);
        $stmt->shouldReceive('execute')->andReturn(true);
        $this->pdo->shouldReceive('prepare')->andReturn($stmt);

        // Effectuer la mise à jour
        updateUser($this->client, $this->pdo, $userId, $newName, $newEmail, $newAddress, $newGender);

        // Récupérer les données mises à jour pour vérifier si la mise à jour a réussi
        $updatedUser = readUser($this->client, $userId);

        // Assertions pour vérifier si les données de l'utilisateur ont été correctement mises à jour
        $this->assertEquals($newName, $updatedUser['name']);
        $this->assertEquals($newEmail, $updatedUser['email']);
        $this->assertEquals($newAddress, $updatedUser['address']);
        $this->assertEquals($newGender, $updatedUser['gender']);
    }

    public function testUpdateUserNotInCache()
    {
        $userId = 'nonExistingUserId';
        $newName = 'New Name';
        $newEmail = 'new@example.com';
        $newAddress = 'New Address';
        $newGender = 'Non-Binaire';

        // Simuler que l'utilisateur n'existe pas dans le cache Redis
        $this->mockRedisExists($userId, false);

        // Simuler la réponse de la mise à jour dans MySQL
        $stmt = Mockery::mock(PDOStatement::class);
        $stmt->shouldReceive('execute')->andReturn(true);
        $this->pdo->shouldReceive('prepare')->andReturn($stmt);

        // Tester l'exception pour s'assurer qu'une Exception est lancée si l'utilisateur n'est pas dans le cache
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'utilisateur avec l'ID $userId n'existe pas dans Redis.");

        // Tenter de mettre à jour l'utilisateur
        updateUser($this->client, $this->pdo, $userId, $newName, $newEmail, $newAddress, $newGender);
    }


}
