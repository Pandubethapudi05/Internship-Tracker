<?php
require 'db.php';

parse_str(file_get_contents("php://input"), $data);
$status = $data['status'];
$id = basename($_SERVER['REQUEST_URI']);

$stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

echo json_encode(['status' => 'updated']);
?>
