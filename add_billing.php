<?php
require_once "./data/config.php";

$message = "";

// Fetch all patients
$sql = "SELECT id, email FROM users";
$stmt = $pdo->query($sql);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle bill creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_POST["patient_id"];
    $service_description = $_POST["service_description"];
    $amount = $_POST["amount"];

    $sql = "INSERT INTO billing (patient_id, service_description, amount) VALUES (:patient_id, :service_description, :amount)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":patient_id", $patient_id, PDO::PARAM_INT);
    $stmt->bindParam(":service_description", $service_description, PDO::PARAM_STR);
    $stmt->bindParam(":amount", $amount, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $message = "Bill added successfully.";
    } else {
        $message = "Failed to add bill. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Billing</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add Billing</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="patient_id">Patient</label>
            <select id="patient_id" name="patient_id" class="form-control" required>
                <option value="" disabled selected>Select a patient</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?= $patient["id"]; ?>"><?= $patient["email"]; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="service_description">Service Description</label>
            <input type="text" id="service_description" name="service_description" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" id="amount" name="amount" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Bill</button>
    </form>
</div>
</body>
</html>
