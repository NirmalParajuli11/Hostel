<?php
session_start();
include('db/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $student_email = $_POST['student_email'];
    $student_password = password_hash($_POST['student_password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'student', 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $student_name, $student_email, $student_password);

    if ($stmt->execute()) {
        header("Location: create_students.php?success=student_created");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
