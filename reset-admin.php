<?php

require_once __DIR__ . "/includes/config.php";

$email = "admin@khulisa.co.za";
$newPassword = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    UPDATE users
    SET password = ?, user_type = 'admin'
    WHERE email = ?
");

$stmt->bind_param("ss", $newPassword, $email);

if ($stmt->execute()) {
    echo "Admin password reset successfully.<br>";
    echo "Email: admin@khulisa.co.za<br>";
    echo "Password: admin123";
} else {
    echo "Error: " . $stmt->error;
}

?>