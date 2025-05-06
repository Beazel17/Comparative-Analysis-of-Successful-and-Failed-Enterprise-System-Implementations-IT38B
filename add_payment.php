<?php
require_once "./data/config.php";

$amount = $payment_method = $notes = "";
$success_msg = $error_msg = "";

// Fetch patients for dropdown
$patients = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM patients");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching patients: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = trim($_POST["patient_id"]);
    $amount = trim($_POST["amount"]);
    $payment_method = trim($_POST["payment_method"]);
    $notes = trim($_POST["notes"]);

    if (!is_numeric($amount) || $amount <= 0) {
        $error_msg = "Please enter a valid amount.";
    } elseif (!is_numeric($patient_id)) {
        $error_msg = "Please select a valid patient.";
    } else {
        $sql = "INSERT INTO payments (patient_id, amount, payment_method, notes)
                VALUES (:patient_id, :amount, :payment_method, :notes)";
        
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":patient_id", $patient_id);
            $stmt->bindParam(":amount", $amount);
            $stmt->bindParam(":payment_method", $payment_method);
            $stmt->bindParam(":notes", $notes);

            if ($stmt->execute()) {
                $success_msg = "✅ Payment added successfully.";
                $amount = $payment_method = $notes = "";
            } else {
                $error_msg = "❌ Error processing payment.";
            }
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add Payment</h2>
    <p>Select a patient and enter payment details.</p>

    <?php 
    if (!empty($success_msg)) echo '<div class="alert alert-success">' . $success_msg . '</div>';
    if (!empty($error_msg)) echo '<div class="alert alert-danger">' . $error_msg . '</div>';
    ?>

    <form action="add_payment.php" method="post">
        <div class="form-group">
            <label>Patient</label>
            <select name="patient_id" class="form-control" required>
                <option value="">Select a patient</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?= htmlspecialchars($patient['id']) ?>">
                        <?= htmlspecialchars($patient['name']) ?> (ID: <?= $patient['id'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Amount</label>
            <input type="text" name="amount" class="form-control" value="<?= htmlspecialchars($amount) ?>" required>
        </div>

        <div class="form-group">
            <label>Payment Method</label>
            <input type="text" name="payment_method" class="form-control" value="<?= htmlspecialchars($payment_method) ?>">
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control"><?= htmlspecialchars($notes) ?></textarea>
        </div>

        <input type="submit" class="btn btn-primary" value="Add Payment">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>
</body>
</html>
