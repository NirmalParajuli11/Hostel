<?php
session_start();
include('db/config.php');

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch and sanitize input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare SQL using prepared statements
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if account is approved
        if ($user['status'] !== 'approved') {
            // ❌ User is not approved yet
            header("Location: login.php?error=pending_approval");
            exit();
        }

        // Now verify password
        if (password_verify($password, $user['password'])) {
            // ✅ Correct password and approved
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            // ❌ Wrong password
            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        // ❌ No user found
        header("Location: login.php?error=user_not_found");
        exit();
    }
} else {
    // If accessed directly, redirect
    header("Location: login.php");
    exit();
}
?>
