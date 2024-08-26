<?php

require_once __DIR__ . '/data/users.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");

if (!isset($_GET['id'])) {
    http_response_code(400); // 400 : Bad request
}

$id = intval($_GET['id']);

if ($id === 0) { // intval a échoué : l'ID n'est pas un nombre
    http_response_code(400); // 400 : Bad request
}

$usersFound = array_filter($users, fn ($user) => $user['id'] === $id);

if (empty($usersFound)) {
    http_response_code(404); // Not Found
    exit;
}

echo json_encode(reset($usersFound));
