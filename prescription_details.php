<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "medicare");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM prescriptions WHERE id = $id");

if ($result->num_rows === 0) {
    echo "Prescription not found.";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prescription Details - MediCare</title>
    <style>
        body { font-family: Arial; padding: 40px; }
        .pad { border: 1px solid #000; padding: 20px; max-width: 600px; margin: auto; }
        .header { font-weight: bold; margin-bottom: 20px; font-size: 20px; }
    </style>
</head>
<body>

<div class="pad">
    <div class="header">MediCare PRESCRIPTION PAD</div>
    <p><strong>Patient Name:</strong> <?= htmlspecialchars($row['patient_name']) ?></p>
    <p><strong>Age:</strong> <?= $row['age'] ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
    <p><strong>Date:</strong> <?= $row['created_at'] ?></p>
    <hr>
    <p><strong>Prescription:</strong></p>
    <p><?= nl2br(htmlspecialchars($row['details'])) ?></p>
    <hr>
    <p><strong>Doctor:</strong> Gerald Undanetan, M.D.</p>
    <p><strong>Signature:</strong> ______________________</p>
    <a href="javascript:history.back();" class="btn btn-secondary mt-3">Back</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
