<?php

$credentials = [
    "lastname" => "Boguszewski",
    "firstname" => "Oscar",
    "email" => "osc@.r",
    "active" => 1
];

$client = curl_init("http://localhost:8000/users");

// Pour effectuer une requête POST
curl_setopt($client, CURLOPT_POST, true);

// On fixe les headers de la requête
curl_setopt($client, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// On fixe les champs du corps de la requête en utilisant les identifiants
curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($credentials));

// Pour mettre le résultat dans la valeur de retour
curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($client);

var_dump(json_decode($response, true));