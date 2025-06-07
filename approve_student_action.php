<?php
session_start();
include('db/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $sql = "UPDATE users SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Student approved successfully.";
    } else {
        $_SESSION['toast_message'] = "Failed to approve student.";
    }

    $stmt->close();
    $conn->close();
    header("Location: approve_students.php");
    exit();
} else {
    $_SESSION['toast_message'] = "Invalid student ID.";
    header("Location: approve_students.php");
    exit();
}
?>
