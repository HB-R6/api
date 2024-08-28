<?php

require_once 'functions/db.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getConnection();

[
    'REQUEST_URI' => $uri,
    'REQUEST_METHOD' => $httpMethod
] = $_SERVER;

if ($uri === "/users" && $httpMethod === 'GET') {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Fonction anonyme ---
    // $output = array_map(function (array $user) {
    //     return [
    //         'uri' => '/users/' . $user['id'],
    //         ...$user
    //     ];
    // }, $users);
    // ------------------------

    $output = array_map(fn (array $u) => ['uri' => '/users/' . $u['id'], ...$u], $users);

    echo json_encode($output);
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

$uriParts = explode('/', ltrim($uri, '/'));

if (count($uriParts) === 2 && $uriParts[0] === 'users') { // 2 parties dans mon URI : élément seul
    $id = intval($uriParts[1]);

    if ($id === 0) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'error' => "Le format de l'ID est incorrect"
        ]);
        exit;
    }

    if ($httpMethod === 'GET') {
        $query = "SELECT * FROM users WHERE id=:id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user === false) {
            http_response_code(404);
            echo json_encode(['error' => 'Utilisateur non trouvé']);
            exit;
        }

        echo json_encode([
            'uri' => '/users/' . $user['id'],
            ...$user
        ]);
        exit;
    }

    if ($httpMethod === 'DELETE') {
        $query = "DELETE FROM users WHERE id=:id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);

        http_response_code(204); // No Content
    }
}