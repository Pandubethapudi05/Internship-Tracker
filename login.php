<?php
header('Content-Type: application/json');

// Database connection parameters - update as needed
$host = 'localhost';
$db   = 'your_database';
$user = 'your_db_user';
$pass = 'your_db_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        exit;
    }

    $email = $input['email'];
    $password = $input['password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password (assuming hashed passwords in DB)
        if (password_verify($password, $user['password'])) {
            // Remove password from output for security
            unset($user['password']);
            echo json_encode(['user' => $user]);
            exit;
        }
    }

    // If we reach here, login failed
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
