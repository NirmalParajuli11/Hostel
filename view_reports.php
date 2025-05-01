<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reports - Saathi Hostel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6fa;
        }

        .admin-content {
            max-width: 1200px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #4b0082;
            margin-bottom: 30px;
            font-size: 28px;
        }

        canvas {
            display: block;
            margin: auto;
            max-width: 100%;
            height: 400px !important;
        }
    </style>
</head>
<body>
<?php include('partials/adminnavbar.php'); ?>

<div class="admin-content">
    <h2>📊 Hostel Occupancy Report</h2>
    <canvas id="occupancyChart"></canvas>
</div>

<script>
const ctx = document.getElementById('occupancyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['January', 'February', 'March', 'April'],
        datasets: [{
            label: 'Occupancy Rate (%)',
            data: [75, 90, 85, 95],
            backgroundColor: '#6a0dad',
            borderRadius: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    color: '#333',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                backgroundColor: '#4b0082',
                titleColor: '#fff',
                bodyColor: '#fff'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#4b0082',
                    font: { size: 14, weight: 'bold' }
                }
            },
            x: {
                ticks: {
                    color: '#4b0082',
                    font: { size: 14, weight: 'bold' }
                }
            }
        }
    }
});
</script>
</body>
</html>
