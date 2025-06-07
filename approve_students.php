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
  <title>Pending Students - Saathi Hostel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      max-width: 1100px;
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
      padding: 14px;
      text-align: left;
      border-bottom: 1px solid #ddd;
      font-size: 0.95rem;
    }

    th {
      background-color: #4b0082;
      color: white;
    }

    tr:hover {
      background-color: #f2f2f2;
    }

    .action-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      color: white;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
    }

    .approve-btn {
      background-color: green;
    }

    .delete-btn {
      background-color: red;
    }

    .approve-btn:hover {
      background-color: #027502;
    }

    .delete-btn:hover {
      background-color: #c10000;
    }

    .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
    }

    .profile-cell {
      display: flex;
      align-items: center;
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
        font-size: 0.85rem;
        padding: 10px;
      }
    }
    .back-button {
      display: inline-block;
      margin-top: 20px;
      margin: 20px;
      background: #4b0082;
      color: white;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
    }

    .back-button:hover {
      background: #360061;
    }
  </style>
</head>
<body>

<div class="admin-content">
  <div class="card">
    <h2>Pending Student Approvals</h2>
    <table>
      <tr>
        <th>SN</th>
        <th>User</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
      <?php
      $sql = "SELECT * FROM users WHERE status = 'pending'";
      $result = $conn->query($sql);
      $sn = 1;
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $photo = !empty($row['photo']) ? 'assets/images/uploads/' . $row['photo'] : 'assets/images/profile_bg.jpg';
              echo "<tr>
                  <td>{$sn}</td>
                  <td class='profile-cell'><img src='{$photo}' class='profile-pic'> {$row['name']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['phone']}</td>
                  <td style='color: orange; font-weight: bold;'>{$row['status']}</td>
                  <td>
                    <a class='action-btn approve-btn' href='approve_student_action.php?id={$row['id']}'>Approve</a>
                    <a class='action-btn delete-btn' href='delete_student.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this user?')\">Delete</a>
                  </td>
              </tr>";
              $sn++;
          }
      } else {
          echo "<tr><td colspan='6' class='no-records'>No students pending approval.</td></tr>";
      }
      ?>
    </table>
  </div>
</div>

<!-- ✅ Toast Notification -->
<div id="toast"></div>
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
        echo "showToast('" . addslashes($_SESSION['toast_message']) . "');";
        unset($_SESSION['toast_message']);
    }
    ?>
  };
</script>
<a href="dashboard.php" class="back-button">
  <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>
</body>
</html>
