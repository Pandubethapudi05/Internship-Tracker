<?php
header('Content-Type: application/json');

$pdo = new PDO("mysql:host=localhost;dbname=db;charset=utf8mb4", 'root', '', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$user_id = $_GET['user_id'] ?? 0;

$stmt = $pdo->query("SELECT id, title, domain image_path,link FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($projects as &$project) {
  $check = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND project_id = ?");
  $check->execute([$user_id, $project['id']]);
  $project['applied'] = $check->fetch() ? true : false;
}

echo json_encode($projects);
