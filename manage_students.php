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
    .edit-btn {
      background-color: orange;
    }
    .delete-btn {
      background-color: red;
    }
    .view-btn {
      background-color: #17a2b8;
    }
    .edit-btn:hover {
      background-color: #e69500;
    }
    .delete-btn:hover {
      background-color: #cc0000;
    }
    .view-btn:hover {
      background-color: #138496;
    }
    .no-records {
      text-align: center;
      color: #888;
      padding: 20px 0;
    }
    #toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #2c3e50;
      color: #fff;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      display: none;
      font-size: 14px;
      z-index: 9999;
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

<div id="toast"></div>

<div class="admin-content">
  <div class="card">
    <h2>All Approved Students</h2>

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
      $result = $conn->query("SELECT * FROM users WHERE role = 'student' AND status = 'approved'");
      if ($result->num_rows > 0) {
          $sn = 1;
          while ($row = $result->fetch_assoc()) {
              $id = $row['id'];
              $status = $row['status'];
              $name = $row['name'];
              $email = $row['email'];

              echo "<tr>
                  <td>{$sn}</td>
                  <td>" . htmlspecialchars($name) . "</td>
                  <td>" . htmlspecialchars($email) . "</td>
                  <td><strong style='color:green'>" . ucfirst($status) . "</strong></td>
                  <td>
                    <a class='btn edit-btn' href='edit_student.php?id=$id'>Edit</a>
                    <a class='btn view-btn' href='view_room_details.php?id=$id'>View</a>
                    <a class='btn delete-btn' href='delete_student.php?id=$id' onclick=\"return confirm('Delete this student?')\">Delete</a>
                  </td>
              </tr>";
              $sn++;
          }
      } else {
          echo "<tr><td colspan='5' class='no-records'>No approved students found.</td></tr>";
      }

      $conn->close();
      ?>
    </table>
  </div>
</div>

<script>
  function showToast(message) {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.style.display = "block";
    setTimeout(() => {
      toast.style.display = "none";
    }, 5000);
  }

  window.onload = function () {
    <?php
    if (isset($_SESSION['toast_message'])) {
      $msg = addslashes($_SESSION['toast_message']);
      echo "showToast('$msg');";
      unset($_SESSION['toast_message']);
    }
    ?>
  };
</script>

</body>
</html>
