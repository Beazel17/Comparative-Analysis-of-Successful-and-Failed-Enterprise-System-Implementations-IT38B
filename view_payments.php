<?php
require_once "./data/config.php";

try {
    $sql = "SELECT 
                payments.id, 
                patients.name AS patient_name, 
                payments.amount, 
                payments.payment_method, 
                payments.notes, 
                payments.payment_date
            FROM payments
            INNER JOIN patients ON payments.patient_id = patients.id
            ORDER BY payments.payment_date DESC";

    $stmt = $pdo->query($sql);
    $payments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching payments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Payments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>All Payments</h2>
    <p>Below is a list of recorded payments.</p>

    <?php if (count($payments) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $index => $payment): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($payment['patient_name']) ?></td>
                        <td>₱<?= number_format($payment['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                        <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                        <td><?= htmlspecialchars($payment['notes']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No payments found.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
