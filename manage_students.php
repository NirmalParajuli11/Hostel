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

        .top-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .create-btn {
            background-color: #4b0082;
            color: white;
            padding: 10px 16px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s;
        }

        .create-btn i {
            margin-right: 8px;
        }

        .create-btn:hover {
            background-color: #630fb1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4b0082;
            color: white;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 6px 12px;
            font-size: 0.9rem;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            margin-right: 6px;
        }

        .approve-btn {
            background-color: green;
        }

        .edit-btn {
            background-color: orange;
        }

        .delete-btn {
            background-color: red;
        }

        .approve-btn:hover {
            background-color: #057305;
        }

        .edit-btn:hover {
            background-color: #e69500;
        }

        .delete-btn:hover {
            background-color: #cc0000;
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

<!-- Main Content -->
<div class="admin-content">
    <div class="card">
        <h2>All Registered Students</h2>
        
        <!-- Create Student Button -->
        <div class="top-actions">
            <a href="create_students.php" class="create-btn">
                <i class="fas fa-plus"></i> Create Student
            </a>
        </div>
        
        <table>
            <tr>
                <th>SN</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php
            include('db/config.php');

            //  Correct Query to select only students
            $result = $conn->query("SELECT * FROM users WHERE role = 'student' AND status IN ('approved', 'pending')");

            if ($result->num_rows > 0) {
                $sn = 1; //  Start serial number
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $status = $row['status'];
                    $name = $row['name'];
                    $email = $row['email'];

                    echo "<tr>
                        <td>" . $sn . "</td>
                        <td>" . htmlspecialchars($name) . "</td>
                        <td>" . htmlspecialchars($email) . "</td>
                        <td><strong style='color:" . ($status === 'approved' ? 'green' : 'orange') . "'>" . ucfirst($status) . "</strong></td>
                        <td>";

                    if ($status === 'pending') {
                        echo "<a class='btn approve-btn' href='approve_student_action.php?id=$id'>Approve</a>";
                        echo "<a class='btn delete-btn' href='delete_student.php?id=$id' onclick=\"return confirm('Delete this student?')\">Delete</a>";
                    } elseif ($status === 'approved') {
                        echo "<a class='btn edit-btn' href='edit_student.php?id=$id'>Edit</a>";
                        echo "<a class='btn delete-btn' href='delete_student.php?id=$id' onclick=\"return confirm('Delete this student?')\">Delete</a>";
                    }

                    echo "</td></tr>";

                    $sn++; //  Increase SN
                }
            } else {
                echo "<tr><td colspan='5' class='no-records'>No students found.</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </div>
</div>

</body>
</html>