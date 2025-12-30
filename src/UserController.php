<?php

require_once 'UserRepository.php';

class UserController {
    private $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function index() {
        echo json_encode($this->repo->getAll());
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validation for name, email, and password
        if (!$data['name'] || !$data['email'] || !$data['password']) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }

        // Creating the user via the repository
        $this->repo->create($data['name'], $data['email'], $data['password']);
        
        http_response_code(201);
        echo json_encode(["message" => "User created"]);
    }
}