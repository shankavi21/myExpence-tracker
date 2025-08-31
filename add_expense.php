<?php
session_start();
include __DIR__ . '/confiq.php'; // DB connection

// 🔹 Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1"); // redirect to login modal
    exit();
}

$userId = $_SESSION['user_id'];

// 🔹 Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = trim($_POST['title']);
    $amount      = trim($_POST['amount']);
    $category    = trim($_POST['category']);
    $expense_date = $_POST['expense_date'];
    $notes       = trim($_POST['notes']);

    // Validate required fields
    if (empty($title) || empty($amount) || empty($category) || empty($expense_date)) {
        echo "<script>alert('Please fill all required fields!'); window.history.back();</script>";
        exit();
    }

    // Insert into database
    $sql = "INSERT INTO expenses (user_id, title, amount, category, expense_date, notes) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdsss", $userId, $title, $amount, $category, $expense_date, $notes);
    if ($stmt->execute()) {
        echo "<script>alert('Expense added successfully!'); window.location='add_expense.php';</script>";
    } else {
        echo "<script>alert('Error adding expense!'); window.history.back();</script>";
    }
    $stmt->close();
}

// 🔹 Fetch user's expenses
$sql = "SELECT * FROM expenses WHERE user_id=? ORDER BY expense_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add & Categorize Expenses</title>
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
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="home.php#features">Features</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="home.php#about">About</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="contact.php#contact">Contact</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-white" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h2 class="mb-4">Add & Categorize Expenses</h2>

  <!-- Expense Form -->
  <form method="POST" class="mb-5">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Expense Title *</label>
        <input type="text" class="form-control" name="title" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Amount *</label>
        <input type="number" step="0.01" class="form-control" name="amount" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Category *</label>
        <select class="form-select" name="category" required>
          <option value="">Select</option>
          <option value="Food">Food</option>
          <option value="Transport">Transport</option>
          <option value="Shopping">Shopping</option>
          <option value="Entertainment">Entertainment</option>
          <option value="Others">Others</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Date *</label>
        <input type="date" class="form-control" name="expense_date" required>
      </div>
    </div>
    <div class="mt-3">
      <label class="form-label">Notes (Optional)</label>
      <textarea class="form-control" name="notes" rows="2"></textarea>
    </div>
    <div class="mt-3">
      <button type="submit" class="btn theme-color">Add Expense</button>
      <a href="home.php" class="btn btn-secondary">Back to Home</a>
    </div>
  </form>

  <!-- Expenses Table -->
  <h3>Recent Expenses</h3>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Title</th>
        <th>Amount</th>
        <th>Category</th>
        <th>Date</th>
        <th>Notes</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td><?php echo number_format($row['amount'],2); ?></td>
        <td><?php echo htmlspecialchars($row['category']); ?></td>
        <td><?php echo $row['expense_date']; ?></td>
        <td><?php echo htmlspecialchars($row['notes']); ?></td>
        <td>
          <a href="edit_expense.php?id=<?php echo $row['expense_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="delete_expense.php?id=<?php echo $row['expense_id']; ?>" class="btn btn-sm btn-danger" 
             onclick="return confirm('Are you sure you want to delete this expense?');">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</div>
<!-- Footer -->
<footer class="theme-color py-4">
  <div class="container text-center">
    <p>&copy; 2025 MyExpenseTracker</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
