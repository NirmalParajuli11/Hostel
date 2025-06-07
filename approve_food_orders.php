<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = intval($_POST['order_id']);
    $action = $_POST['action'];
    $message = $_POST['message'] ?? '';

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE food_bookings SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        if ($stmt->execute()) {
            // Get user email for notification
            $stmt = $conn->prepare("SELECT u.email, m.meal_name FROM food_bookings fb 
                                  JOIN users u ON fb.user_id = u.id 
                                  JOIN meals m ON fb.meal_id = m.id 
                                  WHERE fb.id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            
            // Send notification email
            $to = $order['email'];
            $subject = "Food Order Approved";
            $message = "Your order for " . $order['meal_name'] . " has been approved.";
            $headers = "From: hostel@example.com";
            mail($to, $subject, $message, $headers);
            
            header("Location: approve_food_orders.php?status=approved");
        }
    } elseif ($action === 'decline') {
        $stmt = $conn->prepare("UPDATE food_bookings SET status = 'declined' WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        if ($stmt->execute()) {
            // Get user email for notification
            $stmt = $conn->prepare("SELECT u.email, m.meal_name FROM food_bookings fb 
                                  JOIN users u ON fb.user_id = u.id 
                                  JOIN meals m ON fb.meal_id = m.id 
                                  WHERE fb.id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            
            // Send notification email
            $to = $order['email'];
            $subject = "Food Order Declined";
            $message = "Your order for " . $order['meal_name'] . " has been declined. Reason: " . $message;
            $headers = "From: hostel@example.com";
            mail($to, $subject, $message, $headers);
            
            header("Location: approve_food_orders.php?status=declined");
        }
    }
    exit();
}

// Fetch all food bookings
$sql = "
    SELECT 
        fb.id, fb.booked_at, fb.status,
        u.name AS student_name, u.email,
        m.meal_name, m.category, m.price
    FROM food_bookings fb
    JOIN users u ON fb.user_id = u.id
    JOIN meals m ON fb.meal_id = m.id
    ORDER BY fb.booked_at DESC
";

$result = $conn->query($sql);
include('partials/adminnavbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approve Food Orders</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f1f3f8;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #4b0082;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 14px 18px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    th {
      background: #4b0082;
      color: white;
      text-transform: uppercase;
      font-size: 14px;
    }

    tr:last-child td {
      border-bottom: none;
    }

    .status {
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: bold;
    }

    .pending {
      background: #ffeaa7;
      color: #d35400;
    }

    .approved {
      background: #dff9fb;
      color: #27ae60;
    }

    .declined {
      background: #ffd8d8;
      color: #e74c3c;
    }

    .action-buttons {
      display: flex;
      gap: 8px;
      justify-content: center;
    }

    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .btn-approve {
      background-color: #27ae60;
      color: white;
    }

    .btn-decline {
      background-color: #e74c3c;
      color: white;
    }

    .btn:hover {
      opacity: 0.9;
    }

    .back-button {
      display: inline-block;
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

  <h2>🍽 Admin - Food Orders Management</h2>

  <?php if ($result->num_rows > 0): ?>
  <table>
    <tr>
      <th>S.N</th>
      <th>Student</th>
      <th>Meal</th>
      <th>Category</th>
      <th>Price</th>
      <th>Booked At</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['student_name']) ?></td>
        <td><?= htmlspecialchars($row['meal_name']) ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td>Rs. <?= number_format($row['price'], 2) ?></td>
        <td><?= date('d M Y, h:i A', strtotime($row['booked_at'])) ?></td>
        <td>
          <span class="status <?= $row['status'] ?>">
            <?= ucfirst($row['status']) ?>
          </span>
        </td>
        <td>
          <?php if ($row['status'] === 'pending'): ?>
            <div class="action-buttons">
              <button onclick="approveOrder(<?= $row['id'] ?>)" class="btn btn-approve">
                <i class="fas fa-check"></i> Approve
              </button>
              <button onclick="declineOrder(<?= $row['id'] ?>)" class="btn btn-decline">
                <i class="fas fa-times"></i> Decline
              </button>
            </div>
          <?php else: ?>
            <i class="fas fa-<?= $row['status'] === 'approved' ? 'check-circle' : 'times-circle' ?>" 
               style="color: <?= $row['status'] === 'approved' ? '#27ae60' : '#e74c3c' ?>; font-size: 1.2em;"></i>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center;">No food orders found.</p>
  <?php endif; ?>

  <a href="dashboard.php" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Dashboard
  </a>

  <script>
  function approveOrder(orderId) {
    Swal.fire({
      title: 'Approve Order?',
      text: "Are you sure you want to approve this food order?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#27ae60',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="order_id" value="${orderId}">
          <input type="hidden" name="action" value="approve">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  function declineOrder(orderId) {
    Swal.fire({
      title: 'Decline Order',
      text: 'Please provide a reason for declining this order:',
      input: 'text',
      inputPlaceholder: 'Enter reason for declining',
      showCancelButton: true,
      confirmButtonColor: '#e74c3c',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Decline Order',
      inputValidator: (value) => {
        if (!value) {
          return 'Please provide a reason!';
        }
      }
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="order_id" value="${orderId}">
          <input type="hidden" name="action" value="decline">
          <input type="hidden" name="message" value="${result.value}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  <?php
  if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message = $status === 'approved' ? 'Order has been approved!' : 'Order has been declined.';
    $icon = $status === 'approved' ? 'success' : 'error';
    echo "Swal.fire({
      icon: '$icon',
      title: '$message',
      showConfirmButton: false,
      timer: 2000
    });";
  }
  ?>
  </script>

  <?php $conn->close(); ?>

</body>
</html>
