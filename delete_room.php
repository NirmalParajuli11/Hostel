<?php
session_start();
include('db/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];
    
    try {
        // First, check if there are any reviews for this room
        $check_reviews = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE room_id = ?");
        $check_reviews->bind_param("i", $room_id);
        $check_reviews->execute();
        $check_reviews->bind_result($review_count);
        $check_reviews->fetch();
        $check_reviews->close();

        if ($review_count > 0) {
            // If there are reviews, show error message
            header("Location: manage_rooms.php?error=" . urlencode("Cannot delete room: It has associated reviews. Please delete the reviews first."));
            exit();
        }

        // If no reviews, proceed with deletion
        $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $room_id);
        
        if ($stmt->execute()) {
            header("Location: manage_rooms.php?success=Room deleted successfully");
        } else {
            header("Location: manage_rooms.php?error=" . urlencode("Error deleting room: " . $stmt->error));
        }
        $stmt->close();
        
    } catch (mysqli_sql_exception $e) {
        // Handle foreign key constraint error
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            header("Location: manage_rooms.php?error=" . urlencode("Cannot delete room: It has associated records. Please delete all related records first."));
        } else {
            header("Location: manage_rooms.php?error=" . urlencode("Error: " . $e->getMessage()));
        }
    }
} else {
    header("Location: manage_rooms.php?error=" . urlencode("Invalid room ID"));
}
$conn->close();
?>
