<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}
?>

<!-- Top Navigation -->
<div class="topnav">
    <div class="nav-left">
        <h1 onclick="window.location.href='dashboard.php';">Saathi Hostel</h1>
    </div>

    <div class="nav-middle">
        <div class="dropdown">
            <span>Management</span>
            <div class="dropdown-content">
                <a href="calculate_rent.php"><i class="fas fa-file"></i> Manage Rent</a>
                <a href="manage_finances.php"><i class="fas fa-wallet"></i> Manage Finances</a>
                <a href="manage_staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
                <a href="approve_food_orders.php"><i class="fas fa-concierge-bell"></i> Manage Food </a>

            </div>
        </div>
        <div class="dropdown">
            <span>Rooms</span>
            <div class="dropdown-content">
                <a href="create_room.php"><i class="fas fa-bed"></i> Create Rooms</a>
                <a href="manage_rooms.php"><i class="fas fa-cogs"></i> Manage Rooms</a>
            </div>
        </div>
        <div class="dropdown">
            <span>Students</span>
            <div class="dropdown-content">
                <a href="approve_students.php"><i class="fas fa-check-circle"></i> Pending Students</a>
                <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
                <a href="manage_students.php"><i class="fas fa-cogs"></i> Registered Students</a>
            </div>
        </div>
        <div class="dropdown">
            <span>
                <?= $_SESSION['user_name'] ?? 'Admin'; ?> <i class="fas fa-user-circle"></i>
            </span>
            <div class="dropdown-content">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<!-- Modern Styles -->
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .topnav {
        background: linear-gradient(to right, #4b0082, #6a0dad);
        color: white;
        padding: 14px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .nav-left h1 {
        font-size: 1.6rem;
        font-weight: bold;
        cursor: pointer;
        margin: 0;
        color: white;
    }

    .nav-middle {
        display: flex;
        align-items: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    .dropdown {
        position: relative;
        font-weight: bold;
        cursor: pointer;
    }

    .dropdown > span {
        color: white;
        padding: 8px;
        border-radius: 6px;
        transition: background 0.2s ease;
    }

    .dropdown:hover > span {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #fff;
        color: #4b0082;
        min-width: 220px;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        padding: 10px 0;
        animation: fadeIn 0.2s ease-in-out;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #4b0082;
        text-decoration: none;
        font-size: 15px;
        transition: background 0.2s ease;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .nav-middle {
            gap: 15px;
        }
        .dropdown-content {
            left: auto;
            right: 0;
        }
    }
</style>
