<?php
session_start();
include 'confiq.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1");
    exit();
}

$userId = $_SESSION['user_id'];
$title = trim($_POST['title']);
$amount = trim($_POST['amount']);
$income_date = $_POST['income_date'];
$notes = trim($_POST['notes']);

$stmt = $conn->prepare("INSERT INTO income (user_id, title, amount, income_date, notes) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isdss", $userId, $title, $amount, $income_date, $notes);

if ($stmt->execute()) {
    echo "<script>alert('Income added successfully!'); window.location='track_income_expense.php';</script>";
} else {
    echo "<script>alert('Error adding income!'); window.history.back();</script>";
}
$stmt->close();
?>
