<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include('partials/adminnavbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Students - Saathi Hostel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
        }

        .topnav {
            background-color: #4b0082;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topnav h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .topnav .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .topnav .nav-actions a {
            background-color: white;
            color: #4b0082;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .topnav .nav-actions a:hover {
            background-color: #ddd;
        }

        .admin-content {
            padding: 40px 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: 0 auto;
        }

        .card h2 {
            text-align: center;
            color: #4b0082;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4b0082;
            color: white;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        a.approve-btn {
            padding: 6px 14px;
            background-color: green;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        a.approve-btn:hover {
            background-color: #027502;
        }

        .no-records {
            text-align: center;
            color: #888;
            padding: 20px 0;
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 0.9rem;
                padding: 10px;
            }

            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<!-- 
Top Navigation
<div class="topnav">
    <h1>Approve Students</h1>
    <div class="nav-actions">
        <span><?= $_SESSION['user_name'] ?? 'Admin'; ?> (<?= ucfirst($_SESSION['user_role'] ?? ''); ?>)</span>
        <a href="dashboard.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div> -->

<!-- Main Content -->
<div class="admin-content">
    <div class="card">
        <h2>Pending Student Approvals</h2>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            include('db/config.php');
            $result = $conn->query("SELECT * FROM users WHERE status = 'pending'");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td><span style='color: orange; font-weight: bold;'>{$row['status']}</span></td>
                        <td><a class='approve-btn' href='approve_student_action.php?id={$row['id']}'>Approve</a></td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='no-records'>No students pending approval.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

</body>
</html>
