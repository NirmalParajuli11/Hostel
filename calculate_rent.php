<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include('partials/adminnavbar.php');

$conn = new mysqli("localhost", "root", "", "hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate remaining for all users
$remainingBalanceMap = [];
$balances = $conn->query("SELECT u.id, r.room_price, b.checkin_date,
    (SELECT SUM(meals.price) FROM food_bookings fb
     JOIN meals ON fb.meal_id = meals.id
     WHERE fb.user_id = u.id AND booked_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) AS food_total,
    (SELECT SUM(amount_paid) FROM payments WHERE user_id = u.id) AS total_paid
    FROM users u
    JOIN room_bookings b ON u.id = b.user_id
    JOIN rooms r ON b.room_id = r.id
    WHERE u.role = 'student'");

while ($row = $balances->fetch_assoc()) {
    $checkin = new DateTime($row['checkin_date']);
    $days = max(1, $checkin->diff(new DateTime())->days);
    $rent = $days * $row['room_price'];
    $food = $row['food_total'] ?? 0;
    $paid = $row['total_paid'] ?? 0;
    $remainingBalanceMap[$row['id']] = max(0, ($rent + $food) - $paid);
}

// Handle Add Payment with remaining limit check
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay_user_id']) && isset($_POST['mode']) && $_POST['mode'] === 'add') {
    $userId = $_POST['pay_user_id'];
    $amount = $_POST['pay_amount'];
    $remaining = $remainingBalanceMap[$userId] ?? 0;

    if ($amount <= $remaining) {
        $stmt = $conn->prepare("INSERT INTO payments (user_id, amount_paid, paid_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("id", $userId, $amount);
        $stmt->execute();
        echo "<script>alert('Payment added successfully!');</script>";
    } else {
        echo "<script>alert('Cannot pay more than remaining balance (Rs. $remaining)');</script>";
    }
}

// Handle Edit Payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_id']) && $_POST['mode'] === 'edit') {
    $paymentId = $_POST['payment_id'];
    $amount = $_POST['edit_amount'];

    $result = $conn->query("SELECT user_id, amount_paid FROM payments WHERE id = $paymentId");
    $payment = $result ? $result->fetch_assoc() : null;

    if ($payment) {
        $userId = $payment['user_id'];
        $previous = $payment['amount_paid'];
        $remaining = $remainingBalanceMap[$userId] + $previous;

        if ($amount <= $remaining) {
            $stmt = $conn->prepare("UPDATE payments SET amount_paid = ?, paid_at = NOW() WHERE id = ?");
            $stmt->bind_param("di", $amount, $paymentId);
            $stmt->execute();
            echo "<script>alert('Payment updated successfully!');</script>";
        } else {
            echo "<script>alert('Cannot update payment to exceed remaining balance (Rs. $remaining)');</script>";
        }
    } else {
        echo "<script>alert('Payment not found.');</script>";
    }
}

$sql = "SELECT u.id, u.name, r.room_number, r.room_price, r.room_type, b.checkin_date
        FROM users u
        JOIN room_bookings b ON u.id = b.user_id
        JOIN rooms r ON b.room_id = r.id
        WHERE u.role = 'student'";
$students = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Monthly Rent</title>
  <style>
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
    th { background-color: #4b0082; color: white; }
    .btn { padding: 6px 12px; background: #4b0082; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); }
    .modal-content { background: white; padding: 20px; margin: 100px auto; max-width: 400px; border-radius: 8px; }
    .close { float: right; font-size: 20px; cursor: pointer; }
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
<h2>Monthly Rent Calculation</h2>
<table>
  <tr>
    <th>Student</th><th>Room No</th><th>Type</th><th>Days</th><th>Rent</th><th>Food</th><th>Total</th><th>Paid</th><th>Remaining</th><th>Action</th>
  </tr>
  <?php while ($student = $students->fetch_assoc()):
    $userId = $student['id'];
    $checkin = new DateTime($student['checkin_date']);
    $today = new DateTime();
    $days = max(1, $checkin->diff($today)->days);
    $rent = $days * $student['room_price'];
    $food = 0;
    $q = $conn->query("SELECT SUM(meals.price) AS total FROM food_bookings JOIN meals ON food_bookings.meal_id = meals.id WHERE user_id = $userId AND booked_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    if ($row = $q->fetch_assoc()) $food = $row['total'] ?? 0;
    $total = $rent + $food;
    $paid = 0; $lastPaymentId = null;
    $payQ = $conn->query("SELECT id, amount_paid FROM payments WHERE user_id = $userId ORDER BY paid_at DESC LIMIT 1");
    if ($p = $payQ->fetch_assoc()) {
      $paid = $conn->query("SELECT SUM(amount_paid) AS total_paid FROM payments WHERE user_id = $userId")->fetch_assoc()['total_paid'] ?? 0;
      $lastPaymentId = $p['id'];
    }
    $remain = max(0, $total - $paid);
  ?>
  <tr>
    <td><?= $student['name'] ?></td>
    <td><?= $student['room_number'] ?></td>
    <td><?= ucfirst($student['room_type']) ?></td>
    <td><?= $days ?> days</td>
    <td>Rs. <?= number_format($rent, 2) ?></td>
    <td>Rs. <?= number_format($food, 2) ?></td>
    <td>Rs. <?= number_format($total, 2) ?></td>
    <td>Rs. <?= number_format($paid, 2) ?></td>
    <td>Rs. <?= number_format($remain, 2) ?></td>
    <td>
      <button class="btn" onclick="openAddModal(<?= $userId ?>, '<?= $student['name'] ?>')">Add Payment</button>
      <?php if ($lastPaymentId): ?>
        <button class="btn" onclick="openEditModal(<?= $lastPaymentId ?>, <?= $p['amount_paid'] ?>, '<?= $student['name'] ?>')">Edit Last Payment</button>
      <?php endif; ?>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<!-- Add Payment Modal -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('paymentModal')">&times;</span>
    <h3 id="modalTitle">Add Payment</h3>
    <form method="POST">
      <input type="hidden" name="pay_user_id" id="pay_user_id">
      <input type="hidden" name="mode" value="add">
      <p id="studentLabel"></p>
      <label>Amount:</label>
      <input type="number" name="pay_amount" required>
      <br><br>
      <button class="btn" type="submit">Submit</button>
    </form>
  </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('editPaymentModal')">&times;</span>
    <h3>Edit Last Payment</h3>
    <form method="POST">
      <input type="hidden" name="payment_id" id="payment_id">
      <input type="hidden" name="mode" value="edit">
      <p id="editStudentLabel"></p>
      <label>Amount:</label>
      <input type="number" name="edit_amount" id="edit_amount" required>
      <br><br>
      <button class="btn" type="submit">Update</button>
    </form>
  </div>
</div>

<script>
function openAddModal(userId, name) {
  document.getElementById('pay_user_id').value = userId;
  document.getElementById('studentLabel').innerText = "Student: " + name;
  document.getElementById('paymentModal').style.display = 'block';
}

function openEditModal(paymentId, amount, name) {
  document.getElementById('payment_id').value = paymentId;
  document.getElementById('edit_amount').value = amount;
  document.getElementById('editStudentLabel').innerText = "Student: " + name;
  document.getElementById('editPaymentModal').style.display = 'block';
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(e) {
  if (e.target === document.getElementById('paymentModal')) closeModal('paymentModal');
  if (e.target === document.getElementById('editPaymentModal')) closeModal('editPaymentModal');
}
</script>

<a href="dashboard.php" class="back-button">
  <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>
</body>
</html>