<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'hostel');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch count of pending food orders
$pendingFoodQuery = $conn->query("SELECT COUNT(*) AS pending_food FROM food_bookings WHERE status = 'pending'");
$pendingFoodRow = $pendingFoodQuery->fetch_assoc();
$pendingFoodOrders = $pendingFoodRow['pending_food'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Saathi Hostel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('assets/images/nirmal1.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(3px);
            z-index: -1;
        }

        .topnav {
            background: linear-gradient(to right, #4b0082, #6a0dad);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .topnav h1 {
            font-size: 1.8rem;
            font-weight: bold;
            cursor: pointer;
            color: white;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-actions .dropdown {
            position: relative;
        }

        .nav-actions .dropdown span {
            font-weight: bold;
            cursor: pointer;
        }

        .nav-actions .dropdown-content {
            position: absolute;
            right: 0;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 14px;
            padding: 10px 0;
            display: none;
            flex-direction: column;
            box-shadow: 0 12px 24px rgba(0,0,0,0.25);
            min-width: 200px;
            z-index: 1003;
        }

        .nav-actions .dropdown:hover .dropdown-content {
            display: flex;
        }

        .nav-actions .dropdown-content a {
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            min-width: 200px;
        }

        .nav-actions .dropdown-content a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .hamburger {
            display: none;
            font-size: 1.6rem;
            color: white;
            cursor: pointer;
        }

        .hamburger-menu {
            position: absolute;
            top: 60px;
            right: 20px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 14px;
            padding: 15px 20px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.25);
            display: none;
            flex-direction: column;
            gap: 15px;
            animation: fadeIn 0.3s ease-in-out;
            z-index: 1002;
        }

        .hamburger-menu.show {
            display: flex;
        }

        .hamburger-menu a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .hamburger-menu a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .admin-content {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 40px;
            text-align: center;
            color: #fff;
        }

        .welcome-card h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .welcome-card p {
            font-size: 1.1rem;
            color: #ddd;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px 20px;
            border-radius: 16px;
            text-align: center;
            color: #fff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-6px);
            background-color: rgba(255, 255, 255, 0.25);
        }

        .card i {
            font-size: 2.5rem;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .card a {
            display: inline-block;
            padding: 10px 24px;
            background-color: #4b0082;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .card a:hover {
            background-color: #6a0dad;
        }
        .card-count {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 24px;
    background-color: #4b0082;
    color: white;
    font-weight: bold;
    border-radius: 8px;
    font-size: 1rem;
    text-decoration: none;
    transition: background-color 0.3s;
}
.card-count:hover {
    background-color: #6a0dad;
}

        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-actions {
                display: none;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="topnav">
    <h1 onclick="location.href='dashboard.php'">Saathi Hostel</h1>
    <div class="nav-actions">
        <div class="dropdown">
            <span>Management</span>
            <div class="dropdown-content">
                <a href="manage_staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
                <a href="manage_finances.php"><i class="fas fa-wallet"></i> Manage Finances</a>
                <a href="calculate_rent.php"><i class="fas fa-file"></i> Manage Rent</a>
                <a href="approve_food_orders.php"><i class="fas fa-concierge-bell"></i> Manage Food Requests</a>
            </div>
        </div>
        <div class="dropdown">
            <span>Rooms</span>
            <div class="dropdown-content">
                <a href="create_room.php"><i class="fas fa-bed"></i> Create Room</a>
                <a href="view_rooms.php"><i class="fas fa-eye"></i> View Rooms</a>
                <a href="manage_rooms.php"><i class="fas fa-tools"></i> Manage Rooms</a>
            </div>
        </div>
        <div class="dropdown">
            <span>Students</span>
            <div class="dropdown-content">
                <a href="approve_students.php"><i class="fas fa-check-circle"></i> Pending Students</a>
                <a href="create_students.php"><i class="fas fa-user-plus"></i> Create Student</a>
                <a href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a>
            </div>
        </div>
        <div class="dropdown">
            <span><?= $_SESSION['user_name'] ?? 'Admin' ?> <i class="fas fa-user-circle"></i></span>
            <div class="dropdown-content">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
    <div class="hamburger" onclick="document.querySelector('.hamburger-menu').classList.toggle('show')">
        <i class="fas fa-bars"></i>
    </div>
    <div class="hamburger-menu">
        <a href="manage_staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
        <a href="manage_finances.php"><i class="fas fa-wallet"></i> Manage Finances</a>
        <a href="calculate_rent.php"><i class="fas fa-file"></i> Room Rent</a>
        <a href="create_room.php"><i class="fas fa-bed"></i> Create Room</a>
        <a href="view_rooms.php"><i class="fas fa-eye"></i> View Rooms</a>
        <a href="manage_rooms.php"><i class="fas fa-tools"></i> Manage Rooms</a>
        <a href="approve_students.php"><i class="fas fa-check-circle"></i> Approve Students</a>
        <a href="create_students.php"><i class="fas fa-user-plus"></i> Create Student</a>
        <a href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- The dashboard content below remains unchanged -->


<div class="admin-content">
    <div class="welcome-card">
        <h2>Welcome to the Admin Dashboard</h2>
        <p>Manage all aspects of <strong>Saathi Hostel</strong> from here.</p>
    </div>

    <div class="grid">
        <?php
        // Connect to database
        $conn = new mysqli('localhost', 'root', '', 'hostel');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch total rooms
        $sql = "SELECT COUNT(*) as total_rooms FROM rooms";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $totalRooms = $row['total_rooms'] ?? 0;

         // Initialize available beds
        $availableBeds = 0;

        // Fetch all rooms
        $sql = "SELECT id, room_type FROM rooms";
        $result = $conn->query($sql);

        if ($result) {
            while ($room = $result->fetch_assoc()) {
                $roomId = $room['id'];
                $roomType = strtolower($room['room_type']);

                // Determine capacity based on room type
                if ($roomType == 'single') {
                    $capacity = 1;
                } elseif ($roomType == 'double') {
                    $capacity = 2;
                } elseif ($roomType == 'triple') {
                    $capacity = 3;
                } else {
                    $capacity = 1; // Default fallback
                }

        // Count how many students booked this room
        $bookingQuery = $conn->query("SELECT COUNT(*) as booked FROM room_bookings WHERE room_id = $roomId");
        $bookingRow = $bookingQuery->fetch_assoc();
        $bookedBeds = $bookingRow['booked'];

        // Calculate available beds for this room
        $roomAvailableBeds = $capacity - $bookedBeds;

        if ($roomAvailableBeds > 0) {
            $availableBeds += $roomAvailableBeds;
        }
    }
}

        // Fetch available rooms
        $sqlAvailable = "SELECT COUNT(*) as available_rooms FROM rooms WHERE status = 'available'";
        $resultAvailable = $conn->query($sqlAvailable);
        $rowAvailable = $resultAvailable->fetch_assoc();
        $availableRooms = $rowAvailable['available_rooms'] ?? 0;

        // Fetch total number of students
        $sql = "SELECT COUNT(*) as total_students FROM users WHERE role = 'student'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $totalStudents = $row['total_students'] ?? 0;

        // Fetch total students who have booked rooms
        $sql = "SELECT COUNT(DISTINCT b.user_id) AS total_booked
        FROM room_bookings b
        JOIN users u ON b.user_id = u.id
        WHERE u.role = 'student'";

        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $totalBookedStudents = $row['total_booked'] ?? 0;
        

        // Fetch pending students count
        $sql = "SELECT COUNT(*) AS pending_students FROM users WHERE role = 'student' AND status = 'pending'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $pendingStudents = $row['pending_students'] ?? 0;

       

?>


            <div class="card">
                <i class="fas fa-house"></i>
                <div class="card-title">Total Rooms</div>
                <a href="manage_rooms.php"><?php echo $totalRooms; ?></a>
            </div>


            <div class="card">
                <i class="fas fa-bed"></i>
                <div class="card-title">Available Beds</div>
                <a href="manage_rooms.php"><?php echo $availableBeds; ?></a>
            </div>


            <div class="card">
                <i class="fas fa-users"></i>
                <div class="card-title">Total Students</div>
                <a href="manage_students.php"><?php echo $totalStudents; ?></a>
            </div>


            <div class="card">
            <i class="fas fa-user-plus"></i>
            <div class="card-title">Total Booked Students</div>
            <a href="view_students.php"><?php echo $totalBookedStudents; ?></a>
            </div>


        <div class="card">
            <i class="fas fa-hourglass-half"></i> 
            <div class="card-title">Pending Students</div>
            <a href="approve_students.php"><?php echo $pendingStudents; ?></a>
        </div>
        
        

        <div class="card">
            <i class="fas fa-utensils"></i>
            <div class="card-title">Manage Food Preferences</div>
            <a href="manage_food.php">Go</a>
        </div>
        <div class="card">
    <i class="fas fa-concierge-bell"></i>
    <div class="card-title">Order Food Requests</div>
    <a href="approve_food_orders.php" style="background: <?= $pendingFoodOrders > 0 ? '#e74c3c' : '#4b0082' ?>;">
        <?= $pendingFoodOrders ?>
    </a>
</div>


        <div class="card">
            <i  class="fas fa-file"></i>
            <div class="card-title">Calculate Room Rent</div>
            <a href="calculate_rent.php">Go</a>
        </div>

        <div class="card">
            <i class="fas fa-chart-line"></i>
            <div class="card-title">Profit & Loss</div>
            <a href="profit_loss.php">Go</a>
        </div>
    </div>
</div>
</body>
</html>