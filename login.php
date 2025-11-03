<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // Note: plain text password for now

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $row['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid username or password. <a href='login.html'>Try again</a>";
    }
}
?>