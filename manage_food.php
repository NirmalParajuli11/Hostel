<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'hostel');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$editMeal = null;

// Handle update
if (isset($_POST['update_meal'])) {
    $id = $_POST['meal_id'];
    $meal_name = $_POST['meal_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("UPDATE meals SET meal_name=?, description=?, price=?, category=? WHERE id=?");
    $stmt->bind_param("ssdsi", $meal_name, $description, $price, $category, $id);
    if ($stmt->execute()) {
        header("Location: manage_food.php?status=updated");
        exit();
    } else {
        $errorMsg = $stmt->error;
    }
    $stmt->close();
}

// Handle insertion
if (isset($_POST['add_meal'])) {
    $meal_name = $_POST['meal_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO meals (meal_name, description, price, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $meal_name, $description, $price, $category);
    if ($stmt->execute()) {
        header("Location: manage_food.php?status=added");
        exit();
    } else {
        $errorMsg = $stmt->error;
    }
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM meals WHERE id = $id");
    header("Location: manage_food.php?status=deleted");
    exit();
}

// Fetch meal to edit
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editMeal = $conn->query("SELECT * FROM meals WHERE id = $editId")->fetch_assoc();
}

// Fetch all meals
$meals = $conn->query("SELECT * FROM meals");

// 🛑 AFTER everything -> include admin navbar
include('partials/adminnavbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Meals</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f1f1f1;
      padding: 30px;
    }
    h2 {
      margin-bottom: 20px;
    }
    .form-container {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .form-container input, .form-container textarea, .form-container select {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .form-container button {
      padding: 10px 18px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .form-container button:hover {
      background: #218838;
    }
    table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    table th, table td {
      padding: 12px 16px;
      border: 1px solid #ddd;
      text-align: center;
    }
    table th {
      background: #4b0082;
      color: white;
    }
    .action-btn {
      padding: 6px 10px;
      border-radius: 4px;
      color: white;
      text-decoration: none;
      margin-right: 5px;
    }
    .edit-btn { background: #007bff; }
    .edit-btn:hover { background: #0056b3; }
    .delete-btn { background: #dc3545; }
    .delete-btn:hover { background: #c82333; }
    .status-msg {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      color: white;
      text-align: center;
    }
    .success { background: #28a745; }
    .error { background: #dc3545; }
    .back-button {
      display: inline-block;
      margin-top: 25px;
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

<h2>🍛 Manage Meal Options</h2>

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'added') {
        echo '<div id="status-msg" class="status-msg success">Meal added successfully.</div>';
    } elseif ($_GET['status'] === 'deleted') {
        echo '<div id="status-msg" class="status-msg error">Meal deleted successfully.</div>';
    } elseif ($_GET['status'] === 'updated') {
        echo '<div id="status-msg" class="status-msg success">Meal updated successfully.</div>';
    }
}
if (isset($errorMsg)) {
    echo '<div id="status-msg" class="status-msg error"> Error: ' . htmlspecialchars($errorMsg) . '</div>';
}
?>

<!-- Auto-close status message after 1 second -->
<script>
  setTimeout(() => {
    const msg = document.getElementById('status-msg');
    if (msg) {
      msg.style.opacity = '0';
      setTimeout(() => msg.style.display = 'none', 300);
    }
  }, 1000); // 1000ms = 1 second
</script>


<div class="form-container">
  <form method="POST">
    <?php if ($editMeal): ?>
      <input type="hidden" name="meal_id" value="<?php echo $editMeal['id']; ?>">
    <?php endif; ?>

    <label>Meal Name:</label>
    <input type="text" name="meal_name" required value="<?php echo $editMeal['meal_name'] ?? ''; ?>">

    <label>Description:</label>
    <textarea name="description" required><?php echo $editMeal['description'] ?? ''; ?></textarea>

    <label>Price (Rs):</label>
    <input type="number" name="price" min="0" step="0.01" required value="<?php echo $editMeal['price'] ?? ''; ?>">

    <label>Category:</label>
    <select name="category" required>
      <?php
      $categories = ['Veg', 'Non-Veg', 'Breakfast', 'Lunch', 'Dinner'];
      foreach ($categories as $cat) {
          $selected = ($editMeal && $editMeal['category'] === $cat) ? 'selected' : '';
          echo "<option value=\"$cat\" $selected>$cat</option>";
      }
      ?>
    </select>

    <button type="submit" name="<?php echo $editMeal ? 'update_meal' : 'add_meal'; ?>">
      <i class="fas fa-<?php echo $editMeal ? 'edit' : 'plus'; ?>"></i>
      <?php echo $editMeal ? 'Update Meal' : 'Add Meal'; ?>
    </button>
  </form>
</div>

<table>
  <tr>
    <th>Serial No</th>
    <th>Name</th>
    <th>Category</th>
    <th>Description</th>
    <th>Price (Rs)</th>
    <th>Action</th>
  </tr>

  <?php 
    if ($meals && $meals->num_rows > 0): 
    $i = 1; // Start serial number
    while ($row = $meals->fetch_assoc()): 
  ?>
    <tr>
      <td><?= $i++ ?></td> <!-- Serial number: 1,2,3,4 -->
      <td><?= htmlspecialchars($row['meal_name']) ?></td>
      <td><?= htmlspecialchars($row['category']) ?></td>
      <td><?= htmlspecialchars($row['description']) ?></td>
      <td>Rs. <?= number_format($row['price'], 2) ?></td>
      <td>
        <a href="manage_food.php?edit=<?= $row['id'] ?>" class="action-btn edit-btn">Edit</a>
        <a href="manage_food.php?delete=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this meal?')">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
  <?php else: ?>
    <tr>
      <td colspan="6" style="text-align: center;">No meals found.</td>
    </tr>
  <?php endif; ?>
</table>



<a href="dashboard.php" class="back-button">
  <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

</body>
</html>
