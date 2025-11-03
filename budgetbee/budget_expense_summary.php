<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "expense");
$user_id = $_SESSION['user_id'];

// Get budget and total expense per month for this user
$sql = "
    SELECT 
        b.month,
        b.amount AS budget_amount,
        IFNULL(SUM(e.amount), 0) AS expense_amount
    FROM budgets b
    LEFT JOIN expenses e ON b.user_id = e.user_id 
        AND DATE_FORMAT(e.expense_date, '%Y-%m') = b.month
    WHERE b.user_id = ?
    GROUP BY b.month, b.amount
    ORDER BY b.month DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$summary = [];
while ($row = $result->fetch_assoc()) {
    $summary[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Budget vs Expense Summary</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            color: white;
            padding: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 70%;
            margin: 0 auto 40px auto;
            border-collapse: collapse;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        th, td {
            padding: 12px 20px;
            text-align: center;
        }
        th {
            background: rgba(255,255,255,0.25);
            font-size: 18px;
        }
        tr:nth-child(even) {
            background: rgba(255,255,255,0.05);
        }
        tr:hover {
            background: rgba(255,255,255,0.15);
        }
        .back-link {
            display: block;
            width: 70%;
            margin: 0 auto;
            text-align: center;
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 40px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        #chart-container {
            max-width: 700px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <h2>Budget vs Expense Summary</h2>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>

    <?php if (count($summary) == 0): ?>
        <p style="text-align:center; font-size: 18px;">No budget data found. Please add budgets first.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Budget Amount (Tk)</th>
                    <th>Expense Amount (Tk)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($summary as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['month']); ?></td>
                        <td><?php echo number_format($row['budget_amount'], 2); ?></td>
                        <td><?php echo number_format($row['expense_amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="chart-container">
            <canvas id="budgetExpenseChart"></canvas>
        </div>

        <script>
            const months = <?php echo json_encode(array_column($summary, 'month')); ?>;
            const budgets = <?php echo json_encode(array_map(fn($r) => (float)$r['budget_amount'], $summary)); ?>;
            const expenses = <?php echo json_encode(array_map(fn($r) => (float)$r['expense_amount'], $summary)); ?>;

            const ctx = document.getElementById('budgetExpenseChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Budget (Tk)',
                            data: budgets,
                            backgroundColor: 'rgba(74, 0, 224, 0.7)',
                            borderColor: 'rgba(74, 0, 224, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expense (Tk)',
                            data: expenses,
                            backgroundColor: 'rgba(242, 38, 19, 0.7)',
                            borderColor: 'rgba(242, 38, 19, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // Include rupee symbol in ticks
                                callback: function(value) {
                                    return 'Tk' + value;
                                }
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>