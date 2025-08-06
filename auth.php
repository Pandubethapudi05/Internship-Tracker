<?php
header('Content-Type: application/json');

// DB CONNECTION
$host = 'localhost';
$db   = 'db';  // ✅ Replace with your actual DB name
$user = 'root';
$pass = '';                    // ✅ Use your MySQL password if any
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
  exit;
}

// Read action
$action = $_POST['action'] ?? '';

if ($action === 'register') {
  // Get fields
  $name     = trim($_POST['fullname'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $role     = trim($_POST['role'] ?? '');

  // Validate
  if (!$name || !$email || !$password || !$role) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
  }

  // Check if email exists
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Email already registered.']);
    exit;
  }

  // Insert new user
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->execute([$name, $email, $hashedPassword, $role]);

  echo json_encode(['status' => 'success', 'message' => 'Registered successfully.']);

} elseif ($action === 'login') {
  // Get fields
  $email    = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  // Validate
  if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password required.']);
    exit;
  }

  // Look up user
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  // Verify password
  if ($user && password_verify($password, $user['password'])) {
    unset($user['password']); // Remove password from response
    echo json_encode(['status' => 'success', 'user' => $user]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
  }

} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
