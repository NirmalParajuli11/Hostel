<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Saathi Hostel</title>
    <link rel="stylesheet" href="assets/css/admin.css"> <!-- Link to Admin CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>
<body>

<?php include('partials/adminnavbar.php'); ?> <!-- Include Admin Navbar -->

<!-- Admin Content Section -->
<div class="admin-content">
    <!-- Welcome Card -->
    <div class="card">
        <h2>Welcome to the Admin Dashboard</h2>
        <p>Manage all aspects of Saathi Hostel from here.</p>
    </div>

    <!-- Manage Hostel Card -->
    <div class="card">
        <h2>Manage Hostel</h2>
        <ul>
            <li><a href="create_rooms.php"><i class="fas fa-bed"></i> Create Rooms</a></li>
            <li><a href="approve_students.php"><i class="fas fa-check-circle"></i> Approve Students</a></li>
            <li><a href="create_students.php"><i class="fas fa-user-plus"></i> Create Student Accounts</a></li>
            <li><a href="manage_food.php"><i class="fas fa-utensils"></i> Manage Food Preferences</a></li>
            <li><a href="calculate_rent.php"><i class="fas fa-money-bill-wave"></i> Calculate Room Rent</a></li>
            <li><a href="profit_loss.php"><i class="fas fa-chart-line"></i> View Profit & Loss</a></li>
        </ul>
    </div>
</div>

</body>
</html>
