<?php
header('Content-Type: text/plain');

$pdo = new PDO("mysql:host=localhost;dbname=db;charset=utf8mb4", 'root', '', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$user_id = $_POST['user_id'] ?? 0;
$project_id = $_POST['project_id'] ?? 0;

if (!$user_id || !$project_id) {
  echo "Missing user or project ID.";
  exit;
}

// Get latest resume
$resumeStmt = $pdo->prepare("SELECT file_path FROM resumes WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$resumeStmt->execute([$user_id]);
$resume_path = $resumeStmt->fetchColumn();

if (!$resume_path) {
  echo "Please upload your resume before applying.";
  exit;
}

// Prevent duplicate applications
$existsStmt = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND project_id = ?");
$existsStmt->execute([$user_id, $project_id]);
if ($existsStmt->fetch()) {
  echo "You have already applied to this project.";
  exit;
}

$applyStmt = $pdo->prepare("INSERT INTO applications (user_id, project_id, resume_path) VALUES (?, ?, ?)");
$applyStmt->execute([$user_id, $project_id, $resume_path]);

echo "Applied successfully with resume.";
