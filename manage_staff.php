<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_id'])) {
        $update_id = intval($_POST['update_id']);
        $new_role = $_POST['new_role'];

        $stmt = $conn->prepare("UPDATE staff SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $update_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $role = $_POST['role'];
        $salary = $_POST['salary'];

        $stmt = $conn->prepare("INSERT INTO staff (name, email, phone, role, salary) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $name, $email, $phone, $role, $salary);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_staff.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM staff WHERE id = $id");
    header("Location: manage_staff.php");
    exit();
}

$staff_result = $conn->query("SELECT * FROM staff ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff - Saathi Hostel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-content {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }
        .form-card, .table-card {
            background: white;
            padding: 30px;
            margin-bottom: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        h2 {
            color: #4b0082;
            margin-bottom: 20px;
        }
        input, select {
            padding: 12px;
            margin-bottom: 15px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        button {
            background-color: #4b0082;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #6a0dad;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background-color: #4b0082;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .btn-danger, .btn-edit {
            padding: 8px 14px;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            margin: 5px 5px 0 0;
        }
        .btn-danger {
            background-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .btn-edit {
            background-color: #f39c12;
        }
        .btn-edit:hover {
            background-color: #e67e22;
        }
        .role-edit-form {
            margin-top: 10px;
            display: none;
        }
    </style>
    <script>
        function toggleEditForm(id) {
            const form = document.getElementById('role-form-' + id);
            form.style.display = form.style.display === 'none' ? 'inline-block' : 'none';
        }
    </script>
</head>
<body>
<?php include('partials/adminnavbar.php'); ?>

<div class="admin-content">
    <div class="form-card">
        <h2>Add New Staff</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number">
            <input type="number" step="0.01" name="salary" placeholder="Monthly Salary (e.g. 25000.00)" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="Warden">Warden</option>
                <option value="Cook">Cook</option>
                <option value="Security">Security</option>
                <option value="Cleaner">Cleaner</option>
            </select>
            <button type="submit">Add Staff</button>
        </form>
    </div>

    <div class="table-card">
        <h2>All Staff</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Salary</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $staff_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td><?php echo number_format($row['salary'], 2); ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td>
                        <button class="btn-edit" type="button" onclick="toggleEditForm(<?php echo $row['id']; ?>)">Edit</button>
                        <form method="POST" id="role-form-<?php echo $row['id']; ?>" class="role-edit-form">
                            <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                            <select name="new_role" style="padding:6px 10px; border-radius:6px;">
                                <option value="Warden" <?php if($row['role'] == 'Warden') echo 'selected'; ?>>Warden</option>
                                <option value="Cook" <?php if($row['role'] == 'Cook') echo 'selected'; ?>>Cook</option>
                                <option value="Security" <?php if($row['role'] == 'Security') echo 'selected'; ?>>Security</option>
                                <option value="Cleaner" <?php if($row['role'] == 'Cleaner') echo 'selected'; ?>>Cleaner</option>
                            </select>
                            <button type="submit" class="btn-edit">Save</button>
                        </form>
                        <a href="manage_staff.php?delete=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>
