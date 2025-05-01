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
        <h1 onclick="window.location.href='dashboard.php';">Admin Panel</h1>
    </div>

    <div class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-ellipsis-v"></i>
    </div>

    <div class="nav-middle" id="mobileMenu">
        <!-- Management Dropdown -->
        <div class="dropdown">
            <span class="dropdown-title">Management</span>
            <div class="dropdown-content">
                <a href="manage_staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
                <a href="manage_finances.php"><i class="fas fa-wallet"></i> Manage Finances</a>
                <a href="view_reports.php"><i class="fas fa-chart-line"></i> View Reports</a>
            </div>
        </div>

        <!-- Rooms Dropdown -->
        <div class="dropdown">
            <span class="dropdown-title">Rooms</span>
            <div class="dropdown-content">
                <a href="create_room.php"><i class="fas fa-bed"></i> Create Rooms</a>
               
                <a href="manage_rooms.php"><i class="fas fa-cogs"></i> Manage Rooms</a>
            </div>
        </div>

        <!-- Students Dropdown -->
        <div class="dropdown">
            <span class="dropdown-title">Students</span>
            <div class="dropdown-content">
                <a href="approve_students.php"><i class="fas fa-check-circle"></i> Pending Students</a>
                <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
                <a href="manage_students.php"><i class="fas fa-cogs"></i> Registered Students</a>
            </div>
        </div>
    </div>

    <div class="nav-right">
        <div class="dropdown">
            <span class="username">
                <?= $_SESSION['user_name'] ?? 'Admin'; ?> (<?= ucfirst($_SESSION['user_role'] ?? ''); ?>)
            </span>
            <div class="dropdown-content">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Navbar Styles -->
<style>
    .topnav {
        background-color: #4b0082;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .topnav h1 {
        font-size: 1.6rem;
        font-weight: bold;
        color: white;
        margin: 0;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .topnav h1:hover {
        color: #dcdcdc;
        text-decoration: underline;
    }

    .menu-toggle {
        display: none;
        font-size: 1.5rem;
        color: white;
        cursor: pointer;
    }

    .dropdown {
        position: relative;
        display: inline-block;
        cursor: pointer;
        margin-left: 20px;
    }

    .dropdown-title, .username {
        color: white;
        font-size: 1.1rem;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .dropdown-title:hover, .username:hover {
        background-color: #6a0dad;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1001;
        border-radius: 6px;
        margin-top: 5px;
        right: 0;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        color: #4b0082;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 1rem;
        transition: background-color 0.2s;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .nav-left, .nav-middle, .nav-right {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .nav-middle {
        gap: 10px;
    }

    @media (max-width: 768px) {
        .menu-toggle {
            display: block;
        }

        .nav-middle {
            display: none;
            flex-direction: column;
            width: 100%;
        }

        .nav-middle.show {
            display: flex;
        }

        .dropdown-content {
            position: static;
            width: 100%;
            box-shadow: none;
            border: 1px solid #ccc;
        }
    }
</style>
