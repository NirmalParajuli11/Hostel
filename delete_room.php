<?php
session_start();
include('db/config.php');

// Check if the ID is set in the URL
if (!isset($_GET['id'])) {
    // If no ID, redirect safely
    header("Location: manage_rooms.php?error=no_id");
    exit();
}

$roomId = intval($_GET['id']);

// Step 1: Check if the room has any active bookings
$checkSql = "SELECT COUNT(*) AS total FROM room_bookings WHERE room_id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $roomId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$row = $checkResult->fetch_assoc();
$checkStmt->close();

if ($row['total'] > 0) {
    //  Room is booked. Cannot delete!
    header("Location: manage_rooms.php?error=room_booked");
    exit();
}

// Step 2: Delete the room if no booking found
$deleteSql = "DELETE FROM rooms WHERE id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $roomId);

if ($deleteStmt->execute()) {
    //  Successfully deleted
    header("Location: manage_rooms.php?success=room_deleted");
    exit();
} else {
    //  Deletion failed
    header("Location: manage_rooms.php?error=deletion_failed");
    exit();
}

$deleteStmt->close();
?>
