<?php
// downloadEmails.php
session_start();
require '../vendor/autoload.php';
require 'config.php';
require 'crud/read.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="emails.csv"');

try {
    // Initialiser la connexion Redis
    $redis = new Predis\Client($redisConfig);
    $userList = readAllUsers($redis, $pdo);

    // Ouvrir le flux de sortie PHP
    $output = fopen('php://output', 'w');

    // Optionnel: Ajouter les en-têtes de colonne au CSV
    fputcsv($output, ['ID', 'Name', 'Email', 'Address', 'Gender'], ';'); // Notez le délimiteur ';'

    // Ajouter les lignes utilisateur au fichier CSV
    foreach ($userList as $user) {
        fputcsv($output, [$user['id'], $user['name'], $user['email'], $user['address'], $user['gender']], ';'); // Utilisation du délimiteur ';'
    }

    // Fermer le flux de sortie
    fclose($output);
    
} catch (Exception $e) {
    // Gérer l'exception et afficher une erreur
    echo "Erreur lors de la création du fichier CSV: ", $e->getMessage();
    exit;
}
