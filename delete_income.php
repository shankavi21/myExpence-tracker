<?php
session_start();
include 'confiq.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1");
    exit();
}

$userId = $_SESSION['user_id'];
$incomeId = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM income WHERE income_id=? AND user_id=?");
$stmt->bind_param("ii", $incomeId, $userId);

if ($stmt->execute()) {
    echo "<script>alert('Income deleted successfully!'); window.location='track_income_expense.php';</script>";
} else {
    echo "<script>alert('Error deleting income!'); window.history.back();</script>";
}
$stmt->close();
?>
