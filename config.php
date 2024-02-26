<?php

// Configuration du serveur Redis
$redisConfig = [
    'scheme' => 'tcp',
    'host' => 'redis-10023.c321.us-east-1-2.ec2.cloud.redislabs.com:10023',
    'port' => 10023,
    'password' => 'mW9IwAzelGiIb1kj058S3Cg7ILeqHeqM',
];

// Configuration de la base de données MySQL
$server = "localhost";
$username = "root";
$password = "";
$db = "redis";
$conn = mysqli_connect($server, $username, $password, $db, 3306);

if ($conn->connect_error) {
    die("Erreur de connexion à MySQL : " . $conn->connect_error);
}