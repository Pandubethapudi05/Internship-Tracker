<?php
// get_profile.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$password = "";
$dbname = "internship_portal";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  echo json_encode(["error" => "DB Connection failed"]);
  exit;
}

$email = $_GET['email'] ?? '';

if (empty($email)) {
  echo json_encode(["error" => "Email is required"]);
  exit;
}

$stmt = $conn->prepare("SELECT name, email, department, year, resume_path FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode($row);
} else {
  echo json_encode(["error" => "Student not found"]);
}

$conn->close();
