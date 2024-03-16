<?php

use PHPUnit\Framework\TestCase;
use Predis\Client as PredisClient;
require 'src/crud/create.php';

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
}
