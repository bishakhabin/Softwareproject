<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "expense");
$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Get stored password hash
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($stored_hash);
        $stmt->fetch();
        $stmt->close();

        // Verify current password
        if (password_verify($current_password, $stored_hash)) {
            // Hash new password and update
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $new_hash, $user_id);
            if ($update->execute()) {
                $success = "Password changed successfully.";
            } else {
                $error = "Failed to update password.";
            }
            $update->close();
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f8;
            padding: 40px;
            color: #333;
        }
        .container {
            max-width: 500px;
            background: white;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background: #2c5364;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
        }
        input[type="submit"]:hover {
            background: #244953;
        }
        .success {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: #2c5364;
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <input type="submit" value="Change Password">
        </form>

        <a href="profile.php" class="back-link">← Back to Profile</a>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>