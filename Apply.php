<?php
// apply.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$title = $data->title;
$company = $data->company;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // e.g., smtp.gmail.com
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com'; // Your Gmail address
    $mail->Password = 'your-app-password'; // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('your-email@gmail.com', 'Internship Portal');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "Internship Application Confirmation";
    $mail->Body = "Hi Student,<br><br>You have successfully applied to the internship:<br><br>
                  <strong>Title:</strong> $title <br>
                  <strong>Company:</strong> $company <br><br>
                  Our team will get back to you soon.<br><br>Regards,<br>Internship Cell";

    $mail->send();

    echo json_encode(['status' => 'success', 'message' => 'Verification email sent!']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
}
?>