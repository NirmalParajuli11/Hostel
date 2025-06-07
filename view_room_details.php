<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include('partials/adminnavbar.php');
include('db/config.php');

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$user_id = intval($_GET['id']);

// Fetch student + room info
$sql = "SELECT u.name AS student_name, u.email, u.phone, u.photo, 
               r.room_number, r.room_type, r.room_price,
               b.checkin_date, r.id AS room_id
        FROM users u
        JOIN room_bookings b ON u.id = b.user_id
        JOIN rooms r ON b.room_id = r.id
        WHERE u.id = ? AND b.checkout_date IS NULL";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();
$stmt->close();

// Fetch roommates (excluding current user)
$roommates = [];
if (isset($data['room_id'])) {
    $room_id = $data['room_id'];
    $sqlRoommates = "SELECT name, photo FROM users 
                     WHERE id IN (
                        SELECT user_id FROM room_bookings WHERE room_id = ? AND checkout_date IS NULL
                     ) AND id != ?";
    $stmt2 = $conn->prepare($sqlRoommates);
    $stmt2->bind_param("ii", $room_id, $user_id);
    $stmt2->execute();
    $roommatesResult = $stmt2->get_result();
    while ($row = $roommatesResult->fetch_assoc()) {
        $roommates[] = $row;
    }
    $stmt2->close();
}

// Fetch complete room history
$historySql = "SELECT r.room_number, r.room_type, b.checkin_date, b.checkout_date,
               CASE 
                   WHEN b.checkout_date IS NULL THEN 'Active'
                   ELSE 'Left'
               END as status
               FROM room_bookings b
               JOIN rooms r ON b.room_id = r.id
               WHERE b.user_id = ?
               ORDER BY b.checkin_date DESC";
$historyStmt = $conn->prepare($historySql);
$historyStmt->bind_param("i", $user_id);
$historyStmt->execute();
$historyResult = $historyStmt->get_result();
$bookingHistory = [];
while ($row = $historyResult->fetch_assoc()) {
    $bookingHistory[] = $row;
}
$historyStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Details - Saathi Hostel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --danger: #f72585;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 24px 30px;
            position: relative;
        }
        
        .header h2 {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .back-btn {
            position: absolute;
            top: 24px;
            right: 30px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .back-btn i {
            margin-right: 6px;
        }
        
        .content {
            padding: 30px;
        }
        
        .profile-card {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
        }
        
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .profile-info {
            margin-left: 20px;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .profile-contact {
            display: flex;
            align-items: center;
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        .profile-contact i {
            margin-right: 8px;
        }
        
        .phone {
            margin-left: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background-color: #f8f9fa;
            padding: 18px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--accent);
        }
        
        .info-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .roommates-section {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: var(--border-radius);
        }
        
        .section-title {
            font-size: 1.2rem;
            color: var(--secondary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        .roommate-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .roommate-card {
            background-color: white;
            padding: 15px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }
        
        .roommate-card:hover {
            transform: translateY(-3px);
        }
        
        .roommate-image {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .roommate-name {
            margin-left: 12px;
            font-weight: 500;
        }
        
        .empty-roommates {
            color: #6c757d;
            font-style: italic;
        }

        /* Error handling styles */
        .error-container {
            background-color: #fff3f3;
            border-left: 4px solid var(--danger);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
        }
        
        .error-title {
            color: var(--danger);
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .room-history {
            margin-top: 20px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .room-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .room-history-table th,
        .room-history-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .room-history-table th {
            background-color: var(--secondary);
            color: white;
            font-weight: 500;
        }
        
        .room-history-table tr:last-child td {
            border-bottom: none;
        }
        
        .room-history-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-left {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .history-title {
            font-size: 1.1rem;
            color: var(--secondary);
            margin: 20px 0 10px 0;
            padding: 0 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Room Details</h2>
            <p>Student accommodation information</p>
            <a href="calculate_rent.php" class="back-btn">
                <i class="fas fa-file"></i> Students Room Rent
            </a>
        </div>
        <div class="content">
            <?php if(isset($data) && !empty($data)): ?>
                <div class="profile-card">
                    <img class="profile-image" src="<?= isset($data['photo']) && !empty($data['photo']) ? 'assets/images/uploads/' . htmlspecialchars($data['photo']) : 'assets/images/profile_bg.jpg' ?>" alt="Student Photo">
                    <div class="profile-info">
                        <div class="profile-name"><?= isset($data['student_name']) ? htmlspecialchars($data['student_name']) : 'N/A' ?></div>
                        <div class="profile-contact">
                            <span><i class="fas fa-envelope"></i> <?= isset($data['email']) ? htmlspecialchars($data['email']) : 'N/A' ?></span>
                            <span class="phone"><i class="fas fa-phone"></i> <?= isset($data['phone']) ? htmlspecialchars($data['phone']) : 'N/A' ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Current Room Number</div>
                        <div class="info-value"><?= isset($data['room_number']) ? htmlspecialchars($data['room_number']) : 'N/A' ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Room Type</div>
                        <div class="info-value"><?= isset($data['room_type']) ? ucfirst(htmlspecialchars($data['room_type'])) : 'N/A' ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Room Price (per day)</div>
                        <div class="info-value">Rs. <?= isset($data['room_price']) ? htmlspecialchars($data['room_price']) : 'N/A' ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Check-In Date</div>
                        <div class="info-value"><?= isset($data['checkin_date']) ? date('d M Y', strtotime($data['checkin_date'])) : 'N/A' ?></div>
                    </div>
                </div>
                
                <div class="roommates-section">
                    <div class="section-title">
                        <i class="fas fa-users"></i> Roommates
                    </div>
                    
                    <?php if (isset($roommates) && count($roommates) > 0): ?>
                        <div class="roommate-grid">
                            <?php foreach ($roommates as $mate): ?>
                                <div class="roommate-card">
                                    <img class="roommate-image" src="<?= isset($mate['photo']) && !empty($mate['photo']) ? 'assets/images/uploads/' . htmlspecialchars($mate['photo']) : 'assets/images/profile_bg.jpg' ?>">
                                    <div class="roommate-name"><?= isset($mate['name']) ? htmlspecialchars($mate['name']) : 'N/A' ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="empty-roommates">No roommates assigned yet.</p>
                    <?php endif; ?>

                    <div class="history-title">
                        <i class="fas fa-history"></i> Room History
                    </div>
                    
                    <div class="room-history">
                        <table class="room-history-table">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Room Type</th>
                                    <th>Check-In Date</th>
                                    <th>Check-Out Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bookingHistory)): ?>
                                    <?php foreach ($bookingHistory as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['room_number']) ?></td>
                                            <td><?= ucfirst(htmlspecialchars($booking['room_type'])) ?></td>
                                            <td><?= date('d M Y', strtotime($booking['checkin_date'])) ?></td>
                                            <td><?= $booking['checkout_date'] ? date('d M Y', strtotime($booking['checkout_date'])) : 'N/A' ?></td>
                                            <td>
                                                <span class="status-badge <?= $booking['status'] === 'Active' ? 'status-active' : 'status-left' ?>">
                                                    <?= htmlspecialchars($booking['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #6c757d;">No room history found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="error-container">
                    <div class="error-title">No Active Booking</div>
                    <p>This student currently has no active room booking.</p>
                </div>
            <?php endif; ?>
            <a href="view_students.php" class="back-btn">← Back to Students Manage</a>
        </div>
    </div>
</body>
</html>