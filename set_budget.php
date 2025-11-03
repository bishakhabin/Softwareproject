<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "expense");
$user_id = $_SESSION['user_id'];
$success = false;
$delete_msg = '';

// Delete budget
if (isset($_GET['delete'])) {
    $monthToDelete = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM budgets WHERE user_id = ? AND month = ?");
    $stmt->bind_param("is", $user_id, $monthToDelete);
    $stmt->execute();
    $stmt->close();
    $delete_msg = "Budget for $monthToDelete deleted.";
}

// Save budget
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $month = $_POST['month'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO budgets (user_id, month, amount)
                            VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE amount = ?");
    $stmt->bind_param("isdd", $user_id, $month, $amount, $amount);
    $stmt->execute();
    $stmt->close();
    $success = true;
}

// Fetch all budgets
$budgets = $conn->query("SELECT * FROM budgets WHERE user_id = $user_id ORDER BY month DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Budget</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            color: white;
            margin: 0;
            padding: 50px 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        label, input {
            display: block;
            width: 100%;
            font-size: 16px;
        }
        input[type="month"],
        input[type="number"] {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
            border: none;
        }
        input[type="submit"] {
            background: white;
            color: #2c5364;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        input[type="submit"]:hover {
            background: #ddd;
        }
        .success {
            color: lightgreen;
            text-align: center;
            margin-bottom: 20px;
        }
        .deleted {
            color: lightcoral;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            background: white;
            color: black;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background: #f0f0f0;
        }
        tr:hover {
            background: #d0e6ff;
        }
        .actions a {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Set Monthly Budget</h2>

    <?php if ($success): ?>
        <p class="success">Budget saved successfully!</p>
    <?php endif; ?>
    <?php if ($delete_msg): ?>
        <p class="deleted"><?= $delete_msg ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="month">Month (YYYY-MM):</label>
        <input type="month" name="month" id="month" required>

        <label for="amount">Budget Amount (Tk):</label>
        <input type="number" step="0.01" name="amount" id="amount" required>

        <input type="submit" value="Save Budget">
    </form>

    <h3 style="margin-top:40px;">Your Monthly Budgets</h3>
    <table>
        <tr>
            <th>Month</th>
            <th>Amount (Tk)</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $budgets->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['month']) ?></td>
            <td><?= number_format($row['amount'], 2) ?></td>
            <td class="actions">
                <a href="?delete=<?= $row['month'] ?>" onclick="return confirm('Delete budget for <?= $row['month'] ?>?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>