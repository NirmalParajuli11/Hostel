<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'hostel');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle food booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id'])) {
    $mealId = $_POST['meal_id'];
    $userId = $_SESSION['user_id'];

    $sql = "INSERT INTO food_bookings (user_id, meal_id, booked_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $mealId);

    if ($stmt->execute()) {
        header("Location: book_food.php?status=success");
        exit();
    } else {
        header("Location: book_food.php?status=error");
        exit();
    }
}

// Fetch meals from the database
$meals = $conn->query("SELECT * FROM meals WHERE meal_name IS NOT NULL AND meal_name != ''");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Meals</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
      margin-left: 280px;
    }

    .container {
      max-width: 1100px;
      margin: 80px auto;
      padding: 0 20px;
    }

    h2 {
      margin-bottom: 30px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 30px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 200px;
    }

    .card h3 {
      margin-top: 0;
      color: #333;
    }

    .card p {
      color: #666;
    }

    .card form {
      margin-top: auto;
    }

    .card form button {
      background: #28a745;
      color: white;
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      width: 100%;
    }

    .card form button:hover {
      background: #218838;
    }

    .popup {
      position: fixed;
      top: 80px;
      right: 30px;
      padding: 15px 20px;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      z-index: 9999;
      opacity: 1;
      transition: opacity 0.5s ease;
    }

    .popup.success {
      background-color: #28a745;
    }

    .popup.error {
      background-color: #dc3545;
    }
  </style>
</head>
<body>

<?php
include('init_user_data.php');
include('user_navbar.php');
?>

<div class="container">
  <h2>🍛 Available Meal Options</h2>

  <?php
  if (isset($_GET['status'])) {
      if ($_GET['status'] === 'success') {
          echo '<div class="popup success" id="popupMessage">✅ Meal booked successfully!</div>';
      } else {
          echo '<div class="popup error" id="popupMessage">❌ Error booking meal. Please try again.</div>';
      }
  }
  ?>

  <div class="grid">
    <?php
    if ($meals && $meals->num_rows > 0) {
        while ($meal = $meals->fetch_assoc()) {
            if (empty(trim($meal['meal_name']))) continue;

            echo '
            <div class="card">
              <div>
                <h3>' . htmlspecialchars($meal['meal_name']) . '</h3>
                <p>' . (!empty($meal['description']) ? htmlspecialchars($meal['description']) : '<em>No description</em>') . '</p>
                <p><strong>Rs. ' . number_format($meal['price'], 2) . '</strong></p>
              </div>
              <form method="POST" action="book_food.php">
                <input type="hidden" name="meal_id" value="' . $meal['id'] . '">
                <button type="submit"><i class="fas fa-check-circle"></i> Book Meal</button>
              </form>
            </div>';
        }
    } else {
        echo "<p>No meals available currently.</p>";
    }
    ?>
  </div>
</div>

<?php if (isset($_GET['status'])): ?>
<script>
  setTimeout(function () {
    var popup = document.getElementById('popupMessage');
    if (popup) {
      popup.style.opacity = '0';
      setTimeout(() => popup.remove(), 500); // remove after fade
    }
  }, 3000);
</script>
<?php endif; ?>

</body>
</html>
