<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'hostel');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$bookingId = $_POST['booking_id'] ?? null;

if ($bookingId) {
    $userId = $_SESSION['user_id'];

    // Step 1: Fetch Room ID from Booking
    $stmt = $conn->prepare("SELECT room_id, checkin_date FROM room_bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookingId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if ($booking) {
        $roomId = $booking['room_id'];
        $checkinDate = $booking['checkin_date'];

        // Step 2: Fetch Room Price
        $roomStmt = $conn->prepare("SELECT room_price FROM rooms WHERE id = ?");
        $roomStmt->bind_param("i", $roomId);
        $roomStmt->execute();
        $roomResult = $roomStmt->get_result();
        $room = $roomResult->fetch_assoc();
        $roomPrice = $room['room_price'] ?? 0;
        $roomStmt->close();

        // Step 3: Calculate Stay Rent
        $today = new DateTime();
        $checkinDateObj = new DateTime($checkinDate);
        $daysStayed = max(1, $checkinDateObj->diff($today)->days);
        $totalRent = $daysStayed * $roomPrice;

        // Step 4: Fetch Food Booking Total (Last 30 days)
        $foodTotal = 0;
        $foodSql = "SELECT SUM(meals.price) AS total
                    FROM food_bookings
                    JOIN meals ON food_bookings.meal_id = meals.id
                    WHERE food_bookings.user_id = ?
                      AND booked_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $foodStmt = $conn->prepare($foodSql);
        $foodStmt->bind_param("i", $userId);
        $foodStmt->execute();
        $foodResult = $foodStmt->get_result();
        if ($foodRow = $foodResult->fetch_assoc()) {
            $foodTotal = $foodRow['total'] ?? 0;
        }
        $foodStmt->close();

        // Step 5: Fetch Total Paid
        $paidTotal = 0;
        $paySql = "SELECT SUM(amount_paid) AS total_paid FROM payments WHERE user_id = ?";
        $payStmt = $conn->prepare($paySql);
        $payStmt->bind_param("i", $userId);
        $payStmt->execute();
        $payResult = $payStmt->get_result();
        if ($payRow = $payResult->fetch_assoc()) {
            $paidTotal = $payRow['total_paid'] ?? 0;
        }
        $payStmt->close();

        // Step 6: Calculate Grand Total and Remaining
        $grandTotal = $totalRent + $foodTotal;
        $remaining = max(0, $grandTotal - $paidTotal);

        // Step 7: Check if remaining is zero
        if ($remaining > 0) {
            // ❌ Still dues, block checkout
            echo "<script>alert('You still have Rs. " . number_format($remaining, 2) . " dues remaining. Please clear your dues before checkout.'); window.location.href='book_room.php';</script>";
            exit();
        } else {
            // ✅ No dues, allow checkout

            // Delete the booking
            $deleteStmt = $conn->prepare("DELETE FROM room_bookings WHERE id = ?");
            $deleteStmt->bind_param("i", $bookingId);
            $deleteStmt->execute();
            $deleteStmt->close();

            // Mark room as available
            $updateStmt = $conn->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
            $updateStmt->bind_param("i", $roomId);
            $updateStmt->execute();
            $updateStmt->close();

            // ✅ Redirect to review page
            header("Location: review.php?room_id=$roomId");
            exit();
        }
    } else {
        // Booking not found
        header("Location: book_room.php?status=invalid_request");
        exit();
    }
} else {
    // No booking id provided
    header("Location: book_room.php?status=invalid_request");
    exit();
}
?>



