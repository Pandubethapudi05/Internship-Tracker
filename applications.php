<?php
require 'db.php';

$project_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
  SELECT a.id, u.name, u.email, a.status 
  FROM applications a 
  JOIN users u ON a.user_id = u.id 
  WHERE a.project_id = ?
");
$stmt->execute([$project_id]);

echo json_encode($stmt->fetchAll());
?>
