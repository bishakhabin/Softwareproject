<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "expense");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $expense_date = $_POST['expense_date'];

    $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, description, category_id, expense_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idsss", $user_id, $amount, $description, $category_id, $expense_date);
    $stmt->execute();

    header("Location: view_expenses.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #2c3e50, #3498db);
            margin: 0;
            padding: 0;
            color: white;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.3);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            margin-top: 5px;
        }

        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2ecc71;
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

<div class="navbar">
    <h1>Expense Manager</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_expenses.php">View Expenses</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Add New Expense</h2>
    <form method="POST" action="">
        <label>Amount</label>
        <input type="number" name="amount" step="0.01" required>

        <label>Description</label>
        <input type="text" name="description" required>

        <label>Category</label>
        <select name="category" required>
            <option value="" disabled selected>Select a category</option>
            <option value="Entertainment">Entertainment</option>
            <option value="Books">Books</option>
            <option value="Dress">Dress</option>
            <option value="Food">Food</option>
            <option value="Transport">Transport</option>
            <option value="Others">Others</option>
        </select>

        <label>Date</label>
        <input type="date" name="expense_date" required>

        <input type="submit" value="Add Expense">
    </form>

    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
