<div class="sidenav">
    <h2>Saathi Hostel</h2>
    <ul>
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>

        <?php if ($_SESSION['user_role'] === 'admin') { ?>
            <li><a href="create_rooms.php"><i class="fas fa-bed"></i> Create Rooms</a></li>
            <li><a href="approve_students.php"><i class="fas fa-check-circle"></i> Approve Students</a></li>
            <li><a href="create_students.php"><i class="fas fa-user-plus"></i> Create Student Accounts</a></li>
            <li><a href="manage_food.php"><i class="fas fa-utensils"></i> Manage Food Preferences</a></li>
            <li><a href="calculate_rent.php"><i class="fas fa-money-bill-wave"></i> Calculate Room Rent</a></li>
            <li><a href="profit_loss.php"><i class="fas fa-chart-line"></i> View Profit & Loss</a></li>
        <?php } ?>
    </ul>
</div>
