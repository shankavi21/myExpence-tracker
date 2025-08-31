<?php
session_start();
include __DIR__ . '/confiq.php'; // DB connection

// 🔹 Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1");
    exit();
}

$userId = $_SESSION['user_id'];

// 🔹 Check if expense ID is provided
if (!isset($_GET['id'])) {
    header("Location: add_expense.php");
    exit();
}

$expenseId = intval($_GET['id']);

// 🔹 Fetch the expense data
$sql = "SELECT * FROM expenses WHERE expense_id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $expenseId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<script>alert('Expense not found!'); window.location='add_expense.php';</script>";
    exit();
}

$expense = $result->fetch_assoc();

// 🔹 Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = trim($_POST['title']);
    $amount      = trim($_POST['amount']);
    $category    = trim($_POST['category']);
    $expense_date = $_POST['expense_date'];
    $notes       = trim($_POST['notes']);

    if (empty($title) || empty($amount) || empty($category) || empty($expense_date)) {
        echo "<script>alert('Please fill all required fields!'); window.history.back();</script>";
        exit();
    }

    $updateSql = "UPDATE expenses SET title=?, amount=?, category=?, expense_date=?, notes=? WHERE expense_id=? AND user_id=?";
    $stmtUpdate = $conn->prepare($updateSql);
    $stmtUpdate->bind_param("sdsssii", $title, $amount, $category, $expense_date, $notes, $expenseId, $userId);
    
    if ($stmtUpdate->execute()) {
        echo "<script>alert('Expense updated successfully!'); window.location='add_expense.php';</script>";
    } else {
        echo "<script>alert('Error updating expense!'); window.history.back();</script>";
    }
    $stmtUpdate->close();
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Expense</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.theme-color { background-color: #113F67; color: #fff; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg theme-color navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-semibold text-white" href="home.php">MyExpenseTracker</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="home.php#features">Features</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="home.php#about">About</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="home.php#contact">Contact</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h2 class="mb-4">Edit Expense</h2>

  <!-- Expense Edit Form -->
  <form method="POST" class="mb-5">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Expense Title *</label>
        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($expense['title']); ?>" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Amount *</label>
        <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $expense['amount']; ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Category *</label>
        <select class="form-select" name="category" required>
          <?php
          $categories = ["Food", "Transport", "Shopping", "Entertainment", "Others"];
          foreach($categories as $cat) {
              $selected = ($expense['category'] === $cat) ? "selected" : "";
              echo "<option value='$cat' $selected>$cat</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Date *</label>
        <input type="date" class="form-control" name="expense_date" value="<?php echo $expense['expense_date']; ?>" required>
      </div>
    </div>
    <div class="mt-3">
      <label class="form-label">Notes (Optional)</label>
      <textarea class="form-control" name="notes" rows="2"><?php echo htmlspecialchars($expense['notes']); ?></textarea>
    </div>
    <div class="mt-3">
      <button type="submit" class="btn theme-color">Update Expense</button>
      <a href="add_expense.php" class="btn btn-secondary">Back</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
