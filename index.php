<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");

require_once 'src/UserController.php';

$controller = new UserController();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $controller->index();
}

if ($method === 'POST') {
    $controller->store();
}