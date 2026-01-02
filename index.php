<?php
require_once 'config/db.php';
include 'includes/header.php';

$income_query = "SELECT SUM(amount) as total FROM transactions t 
                 JOIN categories c ON t.category_id = c.id 
                 WHERE c.type = 'income'";
$income_result = $conn->query($income_query);
$income_row = $income_result->fetch_assoc();
$total_income = $income_row['total'] ?? 0;

$expense_query = "SELECT SUM(amount) as total FROM transactions t 
                  JOIN categories c ON t.category_id = c.id 
                  WHERE c.type = 'expense'";
$expense_result = $conn->query($expense_query);
$expense_row = $expense_result->fetch_assoc();
$total_expense = $expense_row['total' ?? 0];

$balance = $total_income - $total_expense;
?>

<div class="row text-center">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="text-muted">Total Balance</h5>
                <h2 class="text-primary">$<?php echo number_format($balance, 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="text-muted">Total Income</h5>
                <h2 class="text-primary">$<?php echo number_format($total_income, 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="text-muted">Total Expense</h5>
                <h2 class="text-primary">$<?php echo number_format($total_expense, 2); ?></h2>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
