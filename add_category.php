<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_name = trim($_POST['category_name']);
    if ($cat_name !== '') {
        $stmt_check = $conn->prepare("SELECT id FROM categories WHERE user_id = ? AND name = ?");
        $stmt_check->bind_param("is", $user_id, $cat_name);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $message = "Category already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (user_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $cat_name);
            $stmt->execute();
            $message = $stmt->affected_rows > 0 ? "Category added successfully!" : "Error adding category: " . $conn->error;
            $stmt->close();
        }
        $stmt_check->close();
    } else {
        $message = "Please enter a category name.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #2193b0, #6dd5ed);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            color: white;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            background-color: #ffffff;
            color: #2193b0;
            font-weight: bold;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #f0f0f0;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .error {
            color: #ff4d4d;
        }
        .success {
            color: #00ff99;
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Add New Category</h2>

    <?php if ($message): ?>
        <p class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="add_category.php">
        <label>Category Name:</label>
        <input type="text" name="category_name" placeholder="e.g. Groceries" required>
        <button type="submit">Add Category</button>
    </form>

    <div class="link">
        <a href="add_expense.php">← Back to Add Expense</a>
    </div>
</div>
</body>
</html>