<?php

header('Content-Type: application/json; charset=UTF-8');

[
    'DB_HOST' => $host,
    'DB_PORT' => $port,
    'DB_NAME' => $dbName,
    'DB_USER' => $dbUser,
    'DB_PASSWORD' => $dbPassword,
    'DB_CHARSET' => $dbCharset
] = parse_ini_file(__DIR__ . '/db.ini');

$dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=$dbCharset";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la connexion à la base de données']);
    exit;
}