<?php
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $pdo = new PDO("mysql:host=localhost;dbname=db;charset=utf8mb4", 'root', '', [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $title = $_POST['title'] ?? '';
    $domain = $_POST['domain'] ?? '';
    $link = $_POST['link'] ?? '';
    $desc = $_POST['description'] ?? '';
    $image = $_FILES['image'] ?? null;

    if (!$title || !$domain) {
      $errorMsg = "Title and Domain are required.";
    } else {
      $imagePath = '';
      if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/projects/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $imagePath = 'uploads/projects/' . uniqid() . '_' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], __DIR__ . '/../' . $imagePath);
      }

      $stmt = $pdo->prepare("INSERT INTO projects (title, domain, image_path, description, link) VALUES (?, ?, ?, ?, ?)");
      $stmt->execute([$title, $domain, $imagePath, $desc, $link]);
      $successMsg = "✅ Project added successfully!";
    }

  } catch (Exception $e) {
    $errorMsg = "❌ Error: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Project</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      padding: 30px;
    }
    form {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      max-width: 500px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      background: #28a745;
      color: white;
      padding: 10px;
      border: none;
      width: 100%;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .message {
      text-align: center;
      margin-bottom: 15px;
    }
    .success { color: green; }
    .error { color: red; }
  </style>
</head>
<body>

  <form action="add_project.php" method="POST" enctype="multipart/form-data">
    <h2>Add Internship / Project</h2>

    <?php if ($successMsg): ?>
      <div class="message success"><?= htmlspecialchars($successMsg) ?></div>
    <?php elseif ($errorMsg): ?>
      <div class="message error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <input type="text" name="title" placeholder="Project Title" required>
    <input type="text" name="domain" placeholder="Domain" required>
    <input type="text" name="link" placeholder="Link to internship (optional)">
    <textarea name="description" placeholder="Description..."></textarea>
    <input type="file" name="image" accept="image/*">
    <button type="submit">Add Project</button>
  </form>

</body>
</html>
