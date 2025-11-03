<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense");

$id = $_GET['id'];
$conn->query("DELETE FROM expenses WHERE id = $id");

header("Location: view_expenses.php");
exit;
?>