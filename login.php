<?php
require_once 'config/db.php';
session_start(); // Start the session to track the user

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // 3. Verify the hashed password
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; // This is our "VIP Pass"
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with that email!";
    }
}
include 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <div class="card shadow">
            <div class="card-body">
                <h3>Login</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>