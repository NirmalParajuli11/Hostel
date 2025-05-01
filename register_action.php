<?php
include('db/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $food     = $_POST['food_preference'];

    // Handle image upload
    $photo = $_FILES['photo'];
    $photo_name = time() . "_" . basename($photo["name"]);
    $target_dir = "assets/images/uploads/";
    $target_file = $target_dir . $photo_name;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($photo["tmp_name"], $target_file)) {
        $role = "student";
        $status = "pending";

        $sql = "INSERT INTO users (name, email, phone, address, password, food_preference, photo, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssss", $name, $email, $phone, $address, $password, $food, $photo_name, $role, $status);

            if ($stmt->execute()) {
                header("Location: register.php?registered=1");
                exit();
            } else {
                header("Location: register.php?error=" . urlencode("Failed to register user. Email might already exist."));
                exit();
            }
        } else {
            header("Location: register.php?error=" . urlencode("Database error. Please try again."));
            exit();
        }
    } else {
        header("Location: register.php?error=" . urlencode("Image upload failed. Try a different image."));
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
