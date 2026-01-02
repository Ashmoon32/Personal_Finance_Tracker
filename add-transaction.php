<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id']; // Use the logged-in ID instead of 1!

if (isset($_POST['submit'])) {
    // 1. Get the data from the form
    $amount = $_POST['amount'];
    $category_id = $_POST['category_id'];
    $t_date = $_POST['transaction_date'];
    $desc = $_POST['description'];
    $user_id = 1; // For now, we hardcode 1 until we build the login system

    // 2. Prepared Statement (Security first!)
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, category_id, amount, description, transaction_date) VALUES (?, ?, ?, ?, ?)");
    
    // "idss" means: integer, integer, double (decimal), string, string
    $stmt->bind_param("iidss", $user_id, $category_id, $amount, $desc, $t_date);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Transaction added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}
?>

<?php 
require_once 'config/db.php'; 
include 'includes/header.php'; 

// Fetch categories from DB to show in the dropdown
$cat_query = "SELECT * FROM categories";
$cat_result = $conn->query($cat_query);
?>

<div class="row justify-content-center">
    <div class="col-md-6">
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
        e.preventDefault(); // This stops the form from submitting
    }
});
</script>
<?php include 'includes/footer.php'; ?>