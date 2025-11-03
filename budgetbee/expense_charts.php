<?php
// expense_charts.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "expense");
$user_id = $_SESSION['user_id'];

$sql = "SELECT c.name AS category, SUM(e.amount) AS total 
        FROM expenses e 
        JOIN categories c ON e.category_id = c.id 
        WHERE e.user_id = ? 
        GROUP BY c.name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
$totals = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row['category'];
    $totals[] = (float)$row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expenses by Category</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>Expenses by Category</h2>
    <canvas id="expensePieChart" width="400" height="400"></canvas>

    <script>
        const ctx = document.getElementById('expensePieChart').getContext('2d');
        const expensePieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($categories); ?>,
                datasets: [{
                    data: <?php echo json_encode($totals); ?>,
                    backgroundColor: [
                        '#4a00e0', '#8e2de2', '#f23812', '#e0a800', '#007bff',
                        '#28a745', '#dc3545', '#17a2b8', '#ffc107', '#6c757d'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>