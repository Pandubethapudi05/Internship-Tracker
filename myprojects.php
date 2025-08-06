<?php
require 'db.php';

$faculty_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM projects WHERE posted_by = ?");
$stmt->execute([$faculty_id]);

echo json_encode($stmt->fetchAll());
?>
