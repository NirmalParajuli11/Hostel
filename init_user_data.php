<?php
if (!isset($_SESSION)) session_start();

include('db/config.php');

$userId = $_SESSION['user_id'] ?? null;

$name = 'Student';
$roomNo = 'N/A';
$imagePath = 'assets/images/profile_bg.jpg';

if ($userId) {
    $stmt = $conn->prepare("SELECT name, photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        if (!empty($row['photo'])) {
            $imagePath = 'assets/images/uploads/' . $row['photo'];
        }
    }
    $stmt->close();

    // Get latest booked room number
    $stmt = $conn->prepare("SELECT r.room_number FROM room_bookings b JOIN rooms r ON b.room_id = r.id WHERE b.user_id = ? ORDER BY b.checkin_date DESC LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $roomNo = $row['room_number'];
    }
    $stmt->close();
}
