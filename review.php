<?php
session_start();
include('db/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$roomId = $_GET['room_id'] ?? null;

if (!$roomId) {
    header("Location: user_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $overall = $_POST['overall_rating'] ?? 0;
    $food = $_POST['food_quality'] ?? 0;
    $room = $_POST['room_quality'] ?? 0;
    $staff = $_POST['staff_behavior'] ?? 0;
    $comment = $_POST['comment'] ?? '';

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, room_id, rating, food_quality, room_quality, staff_behavior, comment) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiis", $userId, $roomId, $overall, $food, $room, $staff, $comment);
    $stmt->execute();

    header("Location: user_dashboard.php?reviewed=true");
   
    exit();
}
include('init_user_data.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rate Your Stay</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f5f5fc;
    }

    .review-form {
      background: white;
      max-width: 600px;
      margin: 120px auto 40px;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .review-form h2 {
      text-align: center;
      color: #4b0082;
      margin-bottom: 25px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 8px;
    }

    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 0.95rem;
    }

    .stars {
      display: flex;
      gap: 8px;
      font-size: 1.6rem;
      cursor: pointer;
      color: #ccc;
    }

    .stars i.selected {
      color: gold;
    }

    button {
      margin-top: 15px;
      padding: 12px;
      width: 100%;
      background-color: #4b0082;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
    }

    button:hover {
      background-color: #360062;
    }

    @media (max-width: 600px) {
      .review-form {
        margin: 100px 20px;
      }
    }
  </style>
</head>
<body>

<?php include('user_navbar.php'); ?>

<form method="POST" class="review-form" onsubmit="return validateForm();">
  <h2>We’d Love Your Feedback</h2>

  <!-- Overall -->
  <div class="form-group">
    <label><i class="fas fa-star"></i> Overall Rating</label>
    <div class="stars" id="overallStars">
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <i class="fas fa-star" data-value="<?= $i ?>"></i>
      <?php endfor; ?>
    </div>
    <input type="hidden" name="overall_rating" id="overallRating" required>
  </div>

  <!-- Food -->
  <div class="form-group">
    <label><i class="fas fa-utensils"></i> Food Quality</label>
    <div class="stars" id="foodStars">
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <i class="fas fa-star" data-value="<?= $i ?>"></i>
      <?php endfor; ?>
    </div>
    <input type="hidden" name="food_quality" id="foodRating" required>
  </div>

  <!-- Room -->
  <div class="form-group">
    <label><i class="fas fa-bed"></i> Room Cleanliness</label>
    <div class="stars" id="roomStars">
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <i class="fas fa-star" data-value="<?= $i ?>"></i>
      <?php endfor; ?>
    </div>
    <input type="hidden" name="room_quality" id="roomRating" required>
  </div>

  <!-- Staff -->
  <div class="form-group">
    <label><i class="fas fa-user-friends"></i> Staff Behavior</label>
    <div class="stars" id="staffStars">
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <i class="fas fa-star" data-value="<?= $i ?>"></i>
      <?php endfor; ?>
    </div>
    <input type="hidden" name="staff_behavior" id="staffRating" required>
  </div>

  <!-- Comment -->
  <div class="form-group">
    <label><i class="fas fa-comment-alt"></i> Your Comments</label>
    <textarea name="comment" rows="4" placeholder="Write your review..."></textarea>
  </div>

  <button type="submit">Submit Review</button>
</form>

<script>
  function setupStarRating(containerId, inputId) {
    const container = document.getElementById(containerId);
    const stars = container.querySelectorAll("i");
    const hiddenInput = document.getElementById(inputId);

    stars.forEach(star => {
      star.addEventListener("click", () => {
        const value = star.getAttribute("data-value");
        hiddenInput.value = value;

        stars.forEach(s => {
          s.classList.remove("selected");
          if (s.getAttribute("data-value") <= value) {
            s.classList.add("selected");
          }
        });
      });
    });
  }

  // Setup all star groups
  setupStarRating("overallStars", "overallRating");
  setupStarRating("foodStars", "foodRating");
  setupStarRating("roomStars", "roomRating");
  setupStarRating("staffStars", "staffRating");

  function validateForm() {
    const requiredRatings = ['overallRating', 'foodRating', 'roomRating', 'staffRating'];
    for (let id of requiredRatings) {
      if (!document.getElementById(id).value) {
        alert("Please rate all categories.");
        return false;
      }
    }
    return true;
  }
</script>

</body>
</html>
