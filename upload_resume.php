<?php
// Allow CORS if you're testing locally and calling from a different port
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

// DB CONNECTION
$host = 'localhost';
$db   = 'db';  // replace with your actual DB name
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  echo "Database connection failed.";
  exit;
}

// Ensure file and user_id exist
if (!isset($_FILES['resume']) || !isset($_POST['user_id'])) {
  http_response_code(400);
  echo "Missing file or user ID.";
  exit;
}

$userId = (int) $_POST['user_id'];
$file = $_FILES['resume'];

// Validate file
$allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
if (!in_array($file['type'], $allowedTypes)) {
  http_response_code(400);
  echo "Only PDF or Word documents are allowed.";
  exit;
}

if ($file['error'] !== UPLOAD_ERR_OK) {
  http_response_code(400);
  echo "File upload error.";
  exit;
}

// Prepare destination
$uploadDir = __DIR__ . '/../uploads/';
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'resume_user_' . $userId . '_' . time() . '.' . $extension;
$destPath = $uploadDir . $filename;

// Move file
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
  http_response_code(500);
  echo "Failed to save uploaded file.";
  exit;
}

// Update user's resume path in DB
$stmt = $pdo->prepare("UPDATE users SET resume_path = ? WHERE id = ?");
$stmt->execute([$filename, $userId]);

echo "Resume uploaded successfully.";
