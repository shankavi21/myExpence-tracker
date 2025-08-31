<?php
session_start();
include 'confiq.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1");
    exit();
}

$userId = $_SESSION['user_id'];

// Get income ID from URL
$incomeId = $_GET['id'] ?? 0;

// Fetch existing income data
$stmt = $conn->prepare("SELECT * FROM income WHERE income_id=? AND user_id=?");
$stmt->bind_param("ii", $incomeId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<script>alert('Income not found!'); window.location='track_income_expense.php';</script>";
    exit();
}

$income = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $amount = trim($_POST['amount']);
    $income_date = $_POST['income_date'];
    $notes = trim($_POST['notes']);

    $stmt = $conn->prepare("UPDATE income SET title=?, amount=?, income_date=?, notes=? WHERE income_id=? AND user_id=?");
    $stmt->bind_param("sdssii", $title, $amount, $income_date, $notes, $incomeId, $userId);
    if ($stmt->execute()) {
        echo "<script>alert('Income updated successfully!'); window.location='track_income_expense.php';</script>";
    } else {
        echo "<script>alert('Error updating income!'); window.history.back();</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Income</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2>Edit Income</h2>
<form method="POST" class="mt-3">
    <div class="mb-3">
        <label class="form-label">Title *</label>
        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($income['title']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount *</label>
        <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $income['amount']; ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Date *</label>
        <input type="date" class="form-control" name="income_date" value="<?php echo $income['income_date']; ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Notes (Optional)</label>
        <textarea class="form-control" name="notes" rows="2"><?php echo htmlspecialchars($income['notes']); ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Update</button>
    <a href="track_income_expense.php" class="btn btn-secondary">Back</a>
</form>

</body>
</html>
