<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">Billing and Payment</h2>
    <form action="process_payment.php" method="post">
        <div class="form-group">
            <label>Patient Full Name</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Service Description</label>
            <input type="text" name="service" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Amount (PHP)</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Payment Method</label>
            <select name="method" class="form-control" required>
                <option value="Cash">Cash</option>
                <option value="Gcash">Gcash</option>
                <option value="Card">Card</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Submit Payment</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
