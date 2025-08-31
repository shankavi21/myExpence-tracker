<?php
session_start();
include 'confiq.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1");
    exit();
}

$userId = $_SESSION['user_id'];

// Date filter
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

// Total Income
$incomeSql = "SELECT SUM(amount) AS total_income FROM income WHERE user_id=?";
if ($startDate && $endDate) {
    $incomeSql .= " AND income_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($incomeSql);
    $stmt->bind_param("iss", $userId, $startDate, $endDate);
} else {
    $stmt = $conn->prepare($incomeSql);
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalIncome = $row['total_income'] ?? 0;
$stmt->close();

// Total Expense
$expenseSql = "SELECT SUM(amount) AS total_expense FROM expenses WHERE user_id=?";
if ($startDate && $endDate) {
    $expenseSql .= " AND expense_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($expenseSql);
    $stmt->bind_param("iss", $userId, $startDate, $endDate);
} else {
    $stmt = $conn->prepare($expenseSql);
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalExpense = $row['total_expense'] ?? 0;
$stmt->close();

$balance = $totalIncome - $totalExpense;

// Fetch transactions
$query = "
    SELECT income_id AS id, title, amount, 'Income' AS type, income_date AS date
    FROM income
    WHERE user_id=?
    UNION ALL
    SELECT expense_id AS id, title, amount, 'Expense' AS type, expense_date AS date
    FROM expenses
    WHERE user_id=?
    ORDER BY date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$transactions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Track Income vs Expense</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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



<div class="container py-4">
    <h2 class="mb-4">Income vs Expense Dashboard</h2>

    <!-- Filter Form -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="start_date" value="<?php echo $startDate; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="end_date" value="<?php echo $endDate; ?>">
        </div>
        <div class="col-md-6 align-self-end text-end">
            <button type="submit" class="btn theme-color">Filter</button>
            <a href="track_income_expense.php" class="btn btn-secondary">Reset</a>
            <a href="home.php" class="btn btn-secondary">⬅ Back to Home</a>
        </div>
    </form>

    <!-- Summary Boxes -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Total Income</h5>
                <h3 class="text-success">$<?php echo number_format($totalIncome,2); ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Total Expense</h5>
                <h3 class="text-danger">$<?php echo number_format($totalExpense,2); ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Balance</h5>
                <h3 class="<?php echo $balance>=0?'text-success':'text-danger'; ?>">
                    $<?php echo number_format($balance,2); ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="mb-4 text-center">
        <div style="width:300px; height:300px; margin:auto;">
            <canvas id="incomeExpenseChart"></canvas>
        </div>
    </div>

    <!-- Add Income Button -->
    <div class="mb-3 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addIncomeModal">
            + Add Income
        </button>
    </div>

    <!-- Add Income Modal -->
    <div class="modal fade" id="addIncomeModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="add_income.php" method="POST">
            <div class="modal-header">
              <h5 class="modal-title">Add Income</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
              </div>
              <div class="mb-3">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
              </div>
              <div class="mb-3">
                <label>Date</label>
                <input type="date" name="income_date" class="form-control" required>
              </div>
              <div class="mb-3">
                <label>Notes</label>
                <textarea name="notes" class="form-control"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Add Income</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Transactions Table -->
    <h4>All Transactions</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Title</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $transactions->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['type']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo number_format($row['amount'],2); ?></td>
                <td>
                    <?php if ($row['type'] == 'Income'): ?>
                        <a href="edit_income.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_income.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this income?');">Delete</a>
                    <?php else: ?>
                        <a href="edit_expense.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_expense.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this expense?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Income','Expense'],
        datasets: [{
            data: [<?php echo $totalIncome; ?>, <?php echo $totalExpense; ?>],
            backgroundColor: ['#28a745','#dc3545']
        }]
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
