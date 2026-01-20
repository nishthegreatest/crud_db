<?php

require_once 'UserRepository.php';

class UserController {
    private $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function index() {
        try {
            echo json_encode($this->repo->getAll());
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
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

        try {
            $this->repo->create($name, $email, $password);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
            return;
        }

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

        try {
            $this->repo->update($id, $name, $email, $password);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "message" => "updated successfully"
        ]);
    }

    public function delete() {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = null;

        if (is_array($data) && isset($data['id'])) {
            $id = $data['id'];
        } else if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }

        try {
            $this->repo->delete($id);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
            return;
        }

        http_response_code(200);
        echo json_encode(["message" => "User deleted"]);
    }
}
