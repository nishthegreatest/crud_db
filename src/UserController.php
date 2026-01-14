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

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }

        $name = isset($data['name']) ? trim($data['name']) : '';
        $email = isset($data['email']) ? trim($data['email']) : '';
        $password = isset($data['password']) ? (string)$data['password'] : '';

        if ($name === '' || $email === '' || $password === '') {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }

        $this->repo->create($name, $email, $password);

        http_response_code(201);
        echo json_encode(["message" => "User created"]);
    }

    public function update() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }

        $id = isset($data['id']) ? $data['id'] : null;
        $name = isset($data['name']) ? trim($data['name']) : '';
        $email = isset($data['email']) ? trim($data['email']) : '';
        $password = isset($data['password']) ? (string)$data['password'] : '';

        if (empty($id) || $name === '' || $email === '' || $password === '') {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }

        $this->repo->update($id, $name, $email, $password);

        http_response_code(200);
        echo json_encode([
            "message" => "updated successfully"
        ]);
    }
}
