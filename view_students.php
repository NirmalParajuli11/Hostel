<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include('partials/adminnavbar.php');
include('db/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students - Saathi Hostel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
        }

        .admin-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1100px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 1.8rem;
            color: #4b0082;
            margin: 0;
        }

        .create-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .create-btn:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 18px;
            text-align: center;
            font-size: 1rem;
            color: #333;
            vertical-align: middle;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        th {
            background-color: #4b0082;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-cell {
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn, .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s ease;
        }

        .edit-btn {
            background-color: #4b0082;
            color: white;
        }

        .edit-btn:hover {
            background-color: #6a0dad;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .success {
            background: #d4edda;
            padding: 10px;
            margin-bottom: 20px;
            color: green;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            text-align: center;
        }

        .no-records {
            color: #888;
            padding: 20px 0;
            text-align: center;
        }

        @media (max-width: 768px) {
            .card {
                padding: 20px;
            }

            .card h2 {
                font-size: 1.5rem;
            }

            table th, table td {
                font-size: 0.9rem;
                padding: 10px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 6px;
            }

            .action-btn, .btn {
                padding: 5px;
            }
        }
    </style>
</head>
<body>

<!-- Main Content -->
<div class="admin-content">
    <div class="card">
        <div class="card-header">
            <h2>All Registered Students</h2>
            <a href="create_students.php" class="create-btn">➕ Create Student</a>
        </div>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success"> Student deleted successfully!</div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <?php
            // Query to select only students
            $result = $conn->query("SELECT * FROM users WHERE role = 'student' AND status IN ('approved', 'pending')");

            if ($result->num_rows > 0) {
                $sn = 1; // Start serial number
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $status = $row['status'];
                    $name = $row['name'];
                    $email = $row['email'];

                    echo "<tr>
                        <td>{$sn}</td>
                        <td>" . htmlspecialchars($name) . "</td>
                        <td>" . htmlspecialchars($email) . "</td>
                        <td><strong style='color:" . ($status === 'approved' ? 'green' : 'orange') . "'>" . ucfirst($status) . "</strong></td>
                        <td class='action-cell'>";

                    echo "<div class='action-buttons'>";
                    
                    if ($status === 'pending') {
                        echo "<a class='action-btn approve-btn' href='approve_student_action.php?id=$id'>Approve</a>";
                        echo "<a class='action-btn delete-btn' href='delete_student.php?id=$id' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a>";
                    } elseif ($status === 'approved') {
                        echo "<a class='action-btn edit-btn' href='edit_student.php?id=$id'>Edit</a>";
                        echo "<a class='action-btn delete-btn' href='delete_student.php?id=$id' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a>";
                    }
                    
                    echo "</div>";
                    echo "</td></tr>";

                    $sn++; // Increase SN
                }
            } else {
                echo "<tr><td colspan='5' class='no-records'>No students found.</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </div>
</div>

<?php include('partials/footer.php'); ?>
</body>
</html>