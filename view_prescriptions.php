<?php
session_start();
require_once "./data/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "nurse") {
    header("location: nurse_login.php");
    exit;
}

if (!isset($_GET['patient_id'])) {
    echo "Invalid request.";
    exit;
}

$patient_id = $_GET['patient_id'];
$status_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = trim($_POST['status']);

    $stmt = $pdo->prepare("UPDATE patients SET status = :status WHERE id = :id");
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":id", $patient_id);
    $stmt->execute();
    $status_msg = "Status updated successfully.";
}

// Fetch patient
$stmt = $pdo->prepare("SELECT full_name, status FROM patients WHERE id = :id");
$stmt->bindParam(":id", $patient_id, PDO::PARAM_INT);
$stmt->execute();
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Patient Status</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h3>Update Status for <?= htmlspecialchars($patient['full_name']); ?></h3>
        <?php if (!empty($status_msg)): ?>
            <div class="alert alert-success"><?= $status_msg; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Status</label>
                <textarea name="status" class="form-control" rows="4"><?= htmlspecialchars($patient['status']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
            <a href="nurse_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
