<?php
include('db/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];

    // Check if the room number already exists
    $check_sql = "SELECT * FROM rooms WHERE room_number = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $room_number);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Room number already exists
        echo "<script>showMessage('Error: Room number $room_number already exists. Please choose a different room number.', 'error');</script>";
    } else {
        // Insert new room into the database
        $sql = "INSERT INTO rooms (room_number, room_type, room_price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $room_number, $room_type, $room_price);

        if ($stmt->execute()) {
            echo "<script>showMessage('Room created successfully!', 'success');</script>";
        } else {
            echo "<script>showMessage('Error: " . $stmt->error . "', 'error');</script>";
        }

        $stmt->close();
    }

    $stmt_check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .admin-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background-color: white;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .card h2 {
            color: #4b0082;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .card label {
            font-size: 1.1rem;
            margin-bottom: 8px;
            display: block;
        }

        .card input, .card select {
            padding: 10px;
            font-size: 1rem;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .card button {
            background-color: #4b0082;
            color: white;
            padding: 10px 20px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .card button:hover {
            background-color: #350065;
        }

        /* Popup Styling */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-message {
            background-color: #4b0082;
            color: white;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            width: 300px;
        }

        .popup-message.error {
            background-color: red;
        }

        .popup-message.success {
            background-color: green;
        }

        .popup button {
            margin-top: 10px;
            background-color: white;
            color: #4b0082;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .popup button:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

<div class="admin-content">
    <div class="card">
        <h2>Create a New Room</h2>
        <form method="POST" action="">
            <label for="room_number">Room Number</label>
            <input type="text" id="room_number" name="room_number" required>

            <label for="room_type">Room Type</label>
            <select id="room_type" name="room_type" required>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="triple">Triple</option>
            </select>

            <label for="room_price">Room Price (Per Day)</label>
            <input type="number" id="room_price" name="room_price" required>

            <button type="submit">Create Room</button>
        </form>
    </div>
</div>

<!-- Popup Message -->
<div class="popup" id="popup">
    <div class="popup-message" id="popup-message"></div>
    <button onclick="closePopup()">OK</button>
</div>

<!-- JS for Popup -->
<script>
    function showMessage(message, type) {
        let popup = document.getElementById("popup");
        let popupMessage = document.getElementById("popup-message");
        popup.style.display = "flex";
        popupMessage.textContent = message;

        if (type === "error") {
            popupMessage.classList.add("error");
            popupMessage.classList.remove("success");
        } else {
            popupMessage.classList.add("success");
            popupMessage.classList.remove("error");
        }
    }

    function closePopup() {
        document.getElementById("popup").style.display = "none";
    }
</script>

</body>
</html>
