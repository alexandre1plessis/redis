<?php

// Configuration du serveur Redis
$redisConfig = [
    'scheme' => 'tcp',
    'host' => 'redis-10023.c321.us-east-1-2.ec2.cloud.redislabs.com:10023',
    'port' => 10023,
    'password' => 'mW9IwAzelGiIb1kj058S3Cg7ILeqHeqM',
];

// Configuration de la base de données MySQL avec PDO
$server = "mysql:host=localhost;dbname=redis;charset=utf8";
$username = "root";
$password = "";

try {
    $pdo = new PDO($server, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à MySQL : " . $e->getMessage());
}
