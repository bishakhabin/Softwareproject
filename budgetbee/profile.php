<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "expense");
$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Fetch user info
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);

    if (empty($new_username) || empty($new_email)) {
        $error = "Username and Email cannot be empty.";
    } else {
        // Update user info
        $update = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $update->bind_param("ssi", $new_username, $new_email, $user_id);
        if ($update->execute()) {
            $success = "Profile updated successfully.";
            $username = $new_username;
            $email = $new_email;
        } else {
            $error = "Error updating profile.";
        }
        $update->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
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
        input[type="text"], input[type="email"] {
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
        <h2>Your Profile</h2>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($username) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>

            <input type="submit" value="Update Profile">
        </form>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <a href="change_password.php" class="back-link">Change Password</a>
    </div>
</body>
</html>