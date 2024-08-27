<?php

require_once 'functions/db.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getConnection();

[
    'REQUEST_URI' => $uri,
    'REQUEST_METHOD' => $httpMethod
] = $_SERVER;

// var_dump(explode('/', $uri));

if ($uri === "/users" && $httpMethod === 'GET') {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
    exit;
}

if ($uri === "/users" && $httpMethod === 'POST') {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true); // true : tableau associatif

    // TODO: Gestion d'erreurs (champs requis, vérification de l'existence des clés, etc...)
    // Si erreur de validation : code 400 Bad Request ou 422 Unprocessable Content

    $query = "INSERT INTO users (lastname, firstname, email, active) VALUES (:lastname, :firstname, :email, :active)";
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([
        'lastname' => $data['lastname'],
        'firstname' => $data['firstname'],
        'email' => $data['email'],
        'active' => $data['active']
    ]);

    if (!$success) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Impossible d\'enregistrer le nouvel utilisateur'
        ]);
        exit;
    }

    $id = $pdo->lastInsertId();
    $userUri = "/users/$id";

    http_response_code(201); // Created

    echo json_encode([
        'uri' => $userUri,
        'id' => $id,
        ...$data
    ]);

    exit;
}