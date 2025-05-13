<?php
session_start();
require_once "./data/config.php";

// Ensure the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: patient_login.php");
    exit;
}

$patient_id = $_SESSION["id"];
$message = "";

// Fetch bills for the logged-in patient
$sql = "SELECT * FROM billing WHERE patient_id = :patient_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":patient_id", $patient_id, PDO::PARAM_INT);
$stmt->execute();
$bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle payment action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pay_bill_id"])) {
    $bill_id = $_POST["pay_bill_id"];
    $sql = "UPDATE billing SET payment_status = 'Paid' WHERE id = :bill_id AND patient_id = :patient_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":bill_id", $bill_id, PDO::PARAM_INT);
    $stmt->bindParam(":patient_id", $patient_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $message = "Payment successful for bill ID: $bill_id.";
    } else {
        $message = "Payment failed. Please try again.";
    }
    // Refresh the bills list
    header("location: billing.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing and Payments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .message {
            margin-bottom: 20px;
        }
        .btn-pay {
            background-color: #5D9CEC;
            color: #fff;
        }
        .btn-pay:hover {
            background-color: #4A89DC;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Billing and Payments</h2>
    <?php if ($message): ?>
        <div class="alert alert-success message"><?= $message; ?></div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Service</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($bills): ?>
            <?php foreach ($bills as $bill): ?>
                <tr>
                    <td><?= $bill["id"]; ?></td>
                    <td><?= $bill["service_description"]; ?></td>
                    <td>$<?= number_format($bill["amount"], 2); ?></td>
                    <td><?= $bill["payment_status"]; ?></td>
                    <td>
                        <?php if ($bill["payment_status"] === "Unpaid"): ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="pay_bill_id" value="<?= $bill["id"]; ?>">
                                <button type="submit" class="btn btn-pay btn-sm">Pay Now</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-success btn-sm" disabled>Paid</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No bills available.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
