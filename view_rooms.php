<?php include('partials/adminnavbar.php'); ?>

<!-- Admin Content Section -->
<div class="admin-content">
    <div class="card">
        <h2>All Rooms</h2>

        <!-- Table displaying room details -->
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Price (Per Day)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include('db/config.php');
                $sql = "SELECT * FROM rooms";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" ."RN-" . $row['room_number'] . "</td>
                            <td>" . $row['room_type'] . "</td>
                            <td>" . $row['room_price'] . "</td>
                          </tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>



<!-- CSS for Admin Content & Table -->
<style>
    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px 18px;
        text-align: left;
        font-size: 1rem;
        color: #333;
    }

    th {
        background-color: #4b0082;
        color: white;
        font-weight: bold;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
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

    /* Admin Content Styling */
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
        max-width: 900px;
        text-align: center;
    }

    .card h2 {
        font-size: 1.8rem;
        color: #4b0082;
        margin-bottom: 30px;
    }

    .card table {
        width: 100%;
        margin-top: 20px;
    }

    /* Card styling for the table */
    .card table th, .card table td {
        padding: 15px;
    }

    .card table th {
        background-color: #4b0082;
        color: white;
        font-weight: bold;
    }

    .card table td {
        text-align: left;
        font-size: 1rem;
    }

    .card table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .card table tr:hover {
        background-color: #f1f1f1;
    }
</style>
