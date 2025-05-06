<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "./data/config.php";

if (!isset($_GET['id'])) {
    echo "No invoice ID provided.";
    exit;
}

$bill_id = $_GET['id'];
$sql = "SELECT * FROM bills WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":id", $bill_id, PDO::PARAM_INT);
$stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);
$stmt->execute();
$bill = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bill) {
    echo "Invoice not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $bill['id']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <h2>MediCare - Invoice</h2>
    <p><strong>Invoice ID:</strong> <?= $bill['id']; ?></p>
    <p><strong>Patient Email:</strong> <?= htmlspecialchars($_SESSION['email']); ?></p>
    <p><strong>Description:</strong> <?= htmlspecialchars($bill['description']); ?></p>
    <p><strong>Amount Due:</strong> $<?= number_format($bill['amount_due'], 2); ?></p>
    <p><strong>Due Date:</strong> <?= $bill['due_date']; ?></p>
    <p><strong>Status:</strong> <?= $bill['status']; ?></p>
    <hr>
    <p>Thank you for choosing MediCare!</p>
    <button onclick="window.print()" class="btn btn-primary mt-3">Print Invoice</button>
    <a href="billing.php" class="btn btn-secondary mt-3">Back to Billing</a>
</div>
</body>
</html>
