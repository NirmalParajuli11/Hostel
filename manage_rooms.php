<?php
session_start();
include('db/config.php');

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include navbar
include('partials/adminnavbar.php');

// Get selected sort type
$sortRoomType = $_GET['sort_room_type'] ?? '';

// Add condition for sorting
$filterSql = '';
if (!empty($sortRoomType)) {
    $filterSql = " WHERE LOWER(r.room_type) = '" . strtolower($conn->real_escape_string($sortRoomType)) . "'";
}
?>

<div class="admin-content">
    <div class="card">
        <h2>Manage Rooms</h2>

        <!-- Add Room Button and Sort Option -->
        <div class="top-actions">
            <a href="create_room.php" class="add-room-btn">➕ Add New Room</a>

            <form method="GET" class="sort-form">
                <label for="sort_room_type" style="font-weight: bold;">Sort by Room Type:</label>
                <select name="sort_room_type" id="sort_room_type" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="single" <?= $sortRoomType === 'single' ? 'selected' : '' ?>>Single</option>
                    <option value="double" <?= $sortRoomType === 'double' ? 'selected' : '' ?>>Double</option>
                    <option value="triple" <?= $sortRoomType === 'triple' ? 'selected' : '' ?>>Triple</option>
                </select>
            </form>
        </div>

        <!-- Room Table -->
        <table>
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Capacity</th>
                    <th>Available Beds</th>
                    <th>Price (Per Day)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT r.*, 
                            CASE 
                                WHEN LOWER(r.room_type) = 'double' THEN 2
                                WHEN LOWER(r.room_type) = 'triple' THEN 3
                                ELSE 1
                            END AS capacity,
                            (SELECT COUNT(*) FROM room_bookings b WHERE b.room_id = r.id) AS booked_beds
                        FROM rooms r $filterSql ORDER BY r.room_type ASC";

                $result = $conn->query($sql);
                $index = 1;

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $availableBeds = $row['capacity'] - $row['booked_beds'];

                        echo "<tr>
                                <td>" . $index++ . "</td>
                                <td>" . htmlspecialchars($row['room_number']) . "</td>
                                <td>" . htmlspecialchars($row['room_type']) . "</td>
                                <td>" . $row['capacity'] . "</td>
                                <td>" . $availableBeds . "</td>
                                <td>Rs. " . number_format($row['room_price'], 2) . "</td>
                                <td>";

                        if ($availableBeds > 0) {
                            echo "<a href='book_room.php?room_id=" . $row['id'] . "' class='action-btn edit-btn'>Available</a> | ";
                        } else {
                            echo "<span style='color: red;'>Full</span> | ";
                        }

                        echo "<a href='edit_room.php?id=" . $row['id'] . "' class='action-btn edit-btn'>Edit</a> | 
                              <a href='delete_room.php?id=" . $row['id'] . "' class='action-btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this room?\")'>Delete</a>
                              </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>No rooms available.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- CSS Styling -->
<style>
    .admin-content {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        background-color: #f5f7fa;
        padding: 20px;
    }

    .card {
        background-color: white;
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1100px;
        text-align: center;
    }

    .card h2 {
        font-size: 1.8rem;
        color: #4b0082;
        margin-bottom: 10px;
    }

    .top-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .add-room-btn {
        background-color: #28a745;
        color: white;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .add-room-btn:hover {
        background-color: #218838;
    }

    .sort-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sort-form select {
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 0.95rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px 18px;
        text-align: center;
        font-size: 1rem;
        color: #333;
    }

    th {
        background-color: #4b0082;
        color: white;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    td, th {
        border-bottom: 1px solid #ddd;
    }

    .action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
    }

    .edit-btn {
        background-color: #4b0082;
        color: white;
    }

    .edit-btn:hover {
        background-color: #6a0dad;
    }

    .delete-btn {
        background-color: #e74c3c;
        color: white;
    }

    .delete-btn:hover {
        background-color: #c0392b;
    }

    @media (max-width: 768px) {
        .card {
            padding: 30px 20px;
        }
        .card h2 {
            font-size: 1.5rem;
        }
        table th, table td {
            font-size: 0.9rem;
            padding: 10px;
        }
        .action-btn {
            padding: 4px 8px;
        }
    }
</style>
