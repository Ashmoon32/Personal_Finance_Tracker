<?php
require_once 'config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepared statement for safety
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php?deleted=1");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}
?>