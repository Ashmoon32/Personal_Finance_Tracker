<?php
session_start();
require_once 'config/db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Calculations (Filtered by User ID)
$income_query = "SELECT SUM(amount) as total FROM transactions t 
                 JOIN categories c ON t.category_id = c.id 
                 WHERE c.type = 'income' AND t.user_id = $user_id";
$income_result = $conn->query($income_query);
$total_income = $income_result->fetch_assoc()['total'] ?? 0;

$expense_query = "SELECT SUM(amount) as total FROM transactions t 
                  JOIN categories c ON t.category_id = c.id 
                  WHERE c.type = 'expense' AND t.user_id = $user_id";
$expense_result = $conn->query($expense_query);
$total_expense = $expense_result->fetch_assoc()['total'] ?? 0;

$balance = $total_income - $total_expense;

// 3. History Table (Filtered by User ID)
$history_query = "SELECT t.*, c.name as category_name, c.type 
                  FROM transactions t 
                  JOIN categories c ON t.category_id = c.id 
                  WHERE t.user_id = $user_id 
                  ORDER BY t.transaction_date DESC";
$history_result = $conn->query($history_query);

include 'includes/header.php';
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
                <h2 class="text-success">$<?php echo number_format($total_income, 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="text-muted">Total Expense</h5>
                <h2 class="text-danger">$<?php echo number_format($total_expense, 2); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white d-flex justify-content-between">
        <h5 class="mb-0">Recent Transactions</h5>
        <a href="add-transaction.php" class="btn btn-sm btn-primary">Add New</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['transaction_date']; ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo $row['category_name']; ?></span></td>
                            <td class="<?php echo $row['type'] == 'income' ? 'text-success' : 'text-danger'; ?>">
                                <strong><?php echo ($row['type'] == 'income' ? '+' : '-'); ?> $<?php echo number_format($row['amount'], 2); ?></strong>
                            </td>
                            <td>
                                <a href="delete-transaction.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>