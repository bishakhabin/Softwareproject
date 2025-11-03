<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense");

if (!isset($_SESSION['user_id'])) die("Login required.");

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM expenses WHERE id = $id");
$expense = $result->fetch_assoc();

$categories = $conn->query("SELECT * FROM categories WHERE user_id = " . $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Expense</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="form-container">
    <h2>Edit Expense</h2>
    <form action="update_expense.php" method="POST">
      <input type="hidden" name="id" value="<?= $expense['id'] ?>">
      <input type="number" name="amount" value="<?= $expense['amount'] ?>" required>
      <input type="date" name="date" value="<?= $expense['expense_date'] ?>" required>
      <select name="category_id" required>
        <?php while ($cat = $categories->fetch_assoc()) { ?>
          <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $expense['category_id'] ? 'selected' : '' ?>>
            <?= $cat['name'] ?>
          </option>
        <?php } ?>
      </select>
      <textarea name="description"><?= $expense['description'] ?></textarea>
      <button type="submit">Update</button>
    </form>
  </div>
</body>
</html>