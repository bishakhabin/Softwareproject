<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Delete
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM expenses WHERE id = $delete_id AND user_id = $user_id");
    $message = "Expense deleted successfully.";
}

// Update
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $amount = $_POST['amount'];
    $desc = $_POST['description'];
    $category = $_POST['category_id'];
    $date = $_POST['expense_date'];
    $stmt = $conn->prepare("UPDATE expenses SET amount=?, description=?, category_id=?, expense_date=? WHERE id=? AND user_id=?");
    $stmt->bind_param("dsisii", $amount, $desc, $category, $date, $id, $user_id);
    $stmt->execute();
    $stmt->close();
    $message = "Expense updated successfully.";
}

$result = $conn->query("SELECT * FROM expenses WHERE user_id = $user_id ORDER BY expense_date DESC");
$categories = $conn->query("SELECT * FROM categories WHERE user_id = $user_id");
$cat_map = [];
while ($cat = $categories->fetch_assoc()) {
    $cat_map[$cat['id']] = $cat['name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Expenses</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
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
            max-width: 900px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            color: black;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #ecf0f1;
        }

        tr:hover {
            background-color: #d0e6ff;
        }

        .actions a {
            margin-right: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .message {
            text-align: center;
            color: yellow;
            margin-bottom: 20px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: white;
            text-decoration: underline;
        }

        input, select {
            padding: 5px;
            width: 90%;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Expense Manager</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_expense.php">Add Expense</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Your Expenses</h2>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Amount (৳)</th>
            <th>Description</th>
            <th>Category</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) { ?>
                <form method="POST">
                    <tr>
                        <td><input type="number" step="0.01" name="amount" value="<?= $row['amount'] ?>" required></td>
                        <td><input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>" required></td>
                        <td>
                            <select name="category_id" required>
                                <?php foreach ($cat_map as $id => $name) { ?>
                                    <option value="<?= $id ?>" <?= ($id == $row['category_id']) ? 'selected' : '' ?>><?= $name ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><input type="date" name="expense_date" value="<?= $row['expense_date'] ?>" required></td>
                        <td>
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="update">Save</button>
                            <a href="view_expenses.php" style="color:red;">Cancel</a>
                        </td>
                    </tr>
                </form>
            <?php } else { ?>
                <tr>
                    <td><?= number_format($row['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= $cat_map[$row['category_id']] ?? 'Unknown' ?></td>
                    <td><?= date("d M Y", strtotime($row['expense_date'])) ?></td>
                    <td class="actions">
                        <a href="view_expenses.php?edit=<?= $row['id'] ?>" style="color:blue;">Edit</a>
                        <a href="view_expenses.php?delete=<?= $row['id'] ?>" style="color:red;" onclick="return confirm('Delete this expense?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>

    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>