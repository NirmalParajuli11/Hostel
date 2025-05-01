<?php
include('db/config.php');

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // First delete related child records (important to avoid foreign key constraint errors)
    $conn->query("DELETE FROM food_bookings WHERE user_id = $id");

    // Delete the student from the database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_students.php?deleted=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    // If no ID is provided, redirect
    header("Location: view_students.php");
    exit();
}
?>
