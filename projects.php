<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$title = $data['title'];
$domain = $data['domain'];
$duration = $data['duration'];
$stipend = $data['stipend'];
$posted_by = $data['posted_by'];

$stmt = $pdo->prepare("INSERT INTO projects (title, domain, duration, stipend, posted_by) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$title, $domain, $duration, $stipend, $posted_by]);

echo json_encode(['status' => 'success']);
?>
