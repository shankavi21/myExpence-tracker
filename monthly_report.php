<?php
session_start();
include 'confiq.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php?login=1");
    exit();
}

$userId = $_SESSION['user_id'];

// Selected month & year
$selectedMonth = $_GET['month'] ?? date('m');
$selectedYear = $_GET['year'] ?? date('Y');

// Fetch category-wise expenses
$sql = "SELECT category, SUM(amount) AS category_total
        FROM expenses
        WHERE user_id=? AND MONTH(expense_date)=? AND YEAR(expense_date)=?
        GROUP BY category";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userId, $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

$categoryData = [];
$totalExpense = 0;
while($row = $result->fetch_assoc()){
    $categoryData[] = $row;
    $totalExpense += $row['category_total'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Expense Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.theme-color { background-color: #113F67; color: #fff; }
</style>
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



<h2 class="mb-3">Monthly Expense Report</h2>

<!-- Back Button -->
<a href="home.php" class="btn btn-secondary mb-3">⬅ Back to Home</a>

<!-- Month & Year Filter -->
<form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Month</label>
        <select name="month" class="form-select">
            <?php for($m=1;$m<=12;$m++): ?>
            <option value="<?php echo $m; ?>" <?php if($m==$selectedMonth) echo 'selected'; ?>>
                <?php echo date('F', mktime(0,0,0,$m,1)); ?>
            </option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Year</label>
        <select name="year" class="form-select">
            <?php for($y=2023;$y<=date('Y');$y++): ?>
            <option value="<?php echo $y; ?>" <?php if($y==$selectedYear) echo 'selected'; ?>>
                <?php echo $y; ?>
            </option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-3 align-self-end">
        <button type="submit" class="btn theme-color">Filter</button>
        <a href="monthly_report.php" class="btn btn-secondary">Reset</a>
    </div>
</form>

<!-- Table -->
<h4>Expenses by Category</h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Category</th>
            <th>Amount</th>
            <th>% of Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($categoryData as $cat): ?>
        <tr>
            <td><?php echo htmlspecialchars($cat['category']); ?></td>
            <td><?php echo number_format($cat['category_total'],2); ?></td>
            <td><?php echo $totalExpense>0 ? round($cat['category_total']/$totalExpense*100,1) : 0; ?>%</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pie Chart -->
<div class="mb-4 text-center">
    <div style="width:300px; height:300px; margin:auto;">
        <canvas id="categoryChart"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($categoryData,'category')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($categoryData,'category_total')); ?>,
            backgroundColor: ['#28a745','#dc3545','#ffc107','#17a2b8','#6c757d','#fd7e14','#6610f2','#20c997','#fd7e14','#e83e8c']
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
