<?php
session_start();
include('db/config.php');

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];
    $sql = "UPDATE users SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header("Location: approve_students.php?success=student_approved");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
