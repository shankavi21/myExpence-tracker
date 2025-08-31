<?php
session_start();
include __DIR__ . '/confiq.php'; // DB connection

// 🔹 Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1"); // redirect to login modal
    exit();
}

$userId = $_SESSION['user_id'];

// 🔹 Check if expense_id is provided
if (!isset($_GET['id'])) {
    header("Location: add_expense.php");
    exit();
}

$expenseId = (int)$_GET['id'];

// 🔹 Delete expense only if it belongs to the logged-in user
$sql = "DELETE FROM expenses WHERE expense_id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $expenseId, $userId);

if ($stmt->execute()) {
    echo "<script>alert('Expense deleted successfully!'); window.location='add_expense.php';</script>";
} else {
    echo "<script>alert('Error deleting expense!'); window.location='add_expense.php';</script>";
}

$stmt->close();
$conn->close();
?>
