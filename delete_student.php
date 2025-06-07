<?php
session_start();
include('db/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ✅ Check if the student has any room booking
    $roomCheck = $conn->prepare("SELECT * FROM room_bookings WHERE user_id = ?");
    $roomCheck->bind_param("i", $id);
    $roomCheck->execute();
    $roomResult = $roomCheck->get_result();

    if ($roomResult->num_rows > 0) {
        $_SESSION['toast_message'] = "Cannot delete student. Room has already been booked.";
        header("Location: manage_students.php");
        exit();
    }

    // ✅ Check for food bookings (optional)
    $foodCheck = $conn->prepare("SELECT * FROM food_bookings WHERE user_id = ?");
    $foodCheck->bind_param("i", $id);
    $foodCheck->execute();
    $foodResult = $foodCheck->get_result();

    if ($foodResult->num_rows > 0) {
        $deleteFood = $conn->prepare("DELETE FROM food_bookings WHERE user_id = ?");
        $deleteFood->bind_param("i", $id);
        $deleteFood->execute();
    }

    // ✅ Now delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    try {
        $stmt->execute();
        $_SESSION['toast_message'] = "Student deleted successfully.";
    } catch (mysqli_sql_exception $e) {
        $_SESSION['toast_message'] = "Unexpected error: could not delete student.";
    }

    header("Location: manage_students.php");
    exit();
} else {
    header("Location: manage_students.php");
    exit();
}
?>
