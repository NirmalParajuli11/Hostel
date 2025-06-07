<?php
include('db/config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);
    $password = $_POST['password'];

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = "student";
    $status = "pending";
    $photo = "";

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, password, photo, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $phone, $address, $hashedPassword, $photo, $role, $status);

    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Registration successful!";
        header("Location: register.php?registered=1");
        exit();
    } else {
        header("Location: register.php?error=" . urlencode("Email already exists or server error."));
        exit();
    }
}
?>
