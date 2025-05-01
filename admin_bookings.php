<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'hostel');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Approval/Rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestId = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        // Approve request and book the room
        $stmt = $conn->prepare("SELECT room_id, user_id FROM booking_requests WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();

        $updateRoom = $conn->prepare("UPDATE rooms SET status='booked', user_id=? WHERE id=? AND status='available'");
        $updateRoom->bind_param("ii", $request['user_id'], $request['room_id']);
        $updateRoom->execute();

        if ($updateRoom->affected_rows > 0) {
            $updateRequest = $conn->prepare("UPDATE booking_requests SET status='approved' WHERE id=?");
            $updateRequest->bind_param("i", $requestId);
            $updateRequest->execute();
        }
    } elseif ($action == 'reject') {
        $updateRequest = $conn->prepare("UPDATE booking_requests SET status='rejected' WHERE id=?");
        $updateRequest->bind_param("i", $requestId);
        $updateRequest->execute();
    }
}

// Fetch pending booking requests
$sql = "SELECT br.*, u.name AS student_name, r.room_number, r.room_type FROM booking_requests br 
        JOIN users u ON br.user_id = u.id 
        JOIN rooms r ON br.room_id = r.id 
        WHERE br.status = 'pending'";
$requests = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Requests - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        body {font-family: 'Poppins', sans-serif; background-color: #f4f4f4;}
        .container {max-width: 1200px; margin: auto; padding: 20px;}
        table {width: 100%; border-collapse: collapse; background-color: white;}
        th, td {padding: 10px; text-align: left; border-bottom: 1px solid #ddd;}
        th {background-color: #007bff; color: white;}
        button {padding: 6px 12px; border: none; cursor: pointer; border-radius: 4px; color: white;}
        .approve {background-color: #28a745;}
        .reject {background-color: #dc3545;}
    </style>
</head>
<body>

<?php include 'adminnavbar.php'; ?>

<div class="container">
    <h2>Pending Booking Requests</h2>
    <?php if (count($requests)): ?>
        <table>
            <tr>
                <th>Student</th>
                <th>Room</th>
                <th>Type</th>
                <th>Requested At</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?= htmlspecialchars($req['student_name']) ?></td>
                    <td><?= htmlspecialchars($req['room_number']) ?></td>
                    <td><?= htmlspecialchars($req['room_type']) ?></td>
                    <td><?= htmlspecialchars($req['requested_at']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <button name="action" value="approve" class="approve">Approve</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <button name="action" value="reject" class="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No pending requests.</p>
    <?php endif; ?>
</div>

</body>
</html>
