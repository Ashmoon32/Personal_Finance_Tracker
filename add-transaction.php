<?php
session_start();
require_once 'config/db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Process Form Submission
if (isset($_POST['submit'])) {
    $amount = $_POST['amount'];
    $category_id = $_POST['category_id'];
    $t_date = $_POST['transaction_date'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO transactions (user_id, category_id, amount, description, transaction_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidss", $user_id, $category_id, $amount, $desc, $t_date);

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
    $stmt->close();
}

// 3. Fetch Categories for Dropdown
$cat_query = "SELECT * FROM categories";
$cat_result = $conn->query($cat_query);

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New Transaction</h4>
            </div>
            <div class="card-body">
                <form action="add-transaction.php" method="POST" id="transactionForm">
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php while($row = $cat_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>">
                                    <?php echo $row['name']; ?> (<?php echo ucfirst($row['type']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="transaction_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary w-100">Save Transaction</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('transactionForm').addEventListener('submit', function(e) {
        const amount = document.querySelector('input[name="amount"]').value;
        if (amount <= 0) {
            alert("Please enter an amount greater than zero.");
            e.preventDefault();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>