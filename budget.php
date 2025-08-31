<?php
session_start();
include 'confiq.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1");
    exit();
}

$userId = $_SESSION['user_id'];
$currentMonth = date('n'); // 1-12
$currentYear = date('Y');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $budgetAmount = $_POST['budget_amount'];

    // Check if budget exists
    $checkSql = "SELECT * FROM budgets WHERE user_id=? AND month=? AND year=?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("iii", $userId, $currentMonth, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $updateSql = "UPDATE budgets SET amount=? WHERE user_id=? AND month=? AND year=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("diii", $budgetAmount, $userId, $currentMonth, $currentYear);
        $stmt->execute();
        $message = "Budget updated successfully!";
    } else {
        $insertSql = "INSERT INTO budgets (user_id, amount, month, year) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("diii", $userId, $budgetAmount, $currentMonth, $currentYear);
        $stmt->execute();
        $message = "Budget set successfully!";
    }
}

// Fetch budget
$budgetSql = "SELECT amount FROM budgets WHERE user_id=? AND month=? AND year=?";
$stmt = $conn->prepare($budgetSql);
$stmt->bind_param("iii", $userId, $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();
$budget = $result->fetch_assoc()['amount'] ?? 0;

// Total expenses for current month
$expenseSql = "SELECT SUM(amount) as total_expense FROM expenses WHERE user_id=? AND MONTH(expense_date)=? AND YEAR(expense_date)=?";
$stmt = $conn->prepare($expenseSql);
$stmt->bind_param("iii", $userId, $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();
$totalExpense = $result->fetch_assoc()['total_expense'] ?? 0;

// Category-wise expense for chart
$catSql = "SELECT category, SUM(amount) as total FROM expenses WHERE user_id=? AND MONTH(expense_date)=? AND YEAR(expense_date)=? GROUP BY category";
$stmt = $conn->prepare($catSql);
$stmt->bind_param("iii", $userId, $currentMonth, $currentYear);
$stmt->execute();
$catResult = $stmt->get_result();
$categories = [];
$catAmounts = [];
while($row = $catResult->fetch_assoc()){
    $categories[] = $row['category'];
    $catAmounts[] = $row['total'];
}

// Calculate progress
$progress = $budget > 0 ? ($totalExpense / $budget) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Budget</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>.theme-color { background-color: #113F67; color: #fff; }</style>
</head>
<body class="container-fluid py-4">

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



<h2>Set Monthly Budget</h2>
<?php if(isset($message)): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-4">
        <label>Budget Amount (Rs.)</label>
        <input type="number" step="0.01" name="budget_amount" class="form-control" value="<?php echo $budget; ?>" required>
    </div>
    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn theme-color">Set / Update</button>
    </div>
    <div class="col-md-2 align-self-end">
        <a href="home.php" class="btn btn-secondary">⬅ Back to Home</a>
    </div>
</form>

<h4>Monthly Expense Progress</h4>
<div class="progress mb-3" style="height: 30px;">
    <div class="progress-bar 
        <?php if($progress >= 100) echo 'bg-danger';
              elseif($progress >= 80) echo 'bg-warning';
              else echo 'bg-success'; ?>" 
        role="progressbar" style="width: <?php echo min($progress,100); ?>%;" 
        aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
        <?php echo number_format($progress,1); ?>%
    </div>
</div>

<?php if($progress >= 100): ?>
<div class="alert alert-danger">You have exceeded your budget!</div>
<?php elseif($progress >= 80): ?>
<div class="alert alert-warning">Warning: You have spent more than 80% of your budget.</div>
<?php endif; ?>

<h4>Category-wise Expenses (Current Month)</h4>
<div style="width:400px; height:400px;">
    <canvas id="categoryChart"></canvas>
</div>

<script>
const ctx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [{
            data: <?php echo json_encode($catAmounts); ?>,
            backgroundColor: ['#28a745','#dc3545','#ffc107','#17a2b8','#6f42c1','#fd7e14','#20c997']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
<!-- Footer -->
<footer class="theme-color py-4">
  <div class="container text-center">
    <p>&copy; 2025 MyExpenseTracker</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
