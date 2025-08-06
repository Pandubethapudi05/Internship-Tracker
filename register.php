<?php
header('Content-Type: application/json');

// Update DB credentials as needed
$host = 'localhost';
$db   = 'your_database';
$user = 'your_db_user';
$pass = 'your_db_password';

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (
        !isset($data['name']) || !isset($data['email']) ||
        !isset($data['password']) || !isset($data['role'])
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing fields']);
        exit;
    }

    // Connect to DB
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insert new user
    $insert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $success = $insert->execute([
        $data['name'],
        $data['email'],
        $hashedPassword,
        $data['role'],
    ]);

    if ($success) {
        $userId = $pdo->lastInsertId();
        echo json_encode(['id' => $userId]);
    } else {
        echo json_encode(['error' => 'Registration failed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
