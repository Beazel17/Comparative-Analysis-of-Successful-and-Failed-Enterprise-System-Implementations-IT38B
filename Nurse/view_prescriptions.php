<?php
session_start();
require_once '../data/config.php';

// Check if the nurse is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: nurse_login.php");
    exit;
}

$full_name = $_SESSION["full_name"];
$nurse_id = $_SESSION["id"];

// Check if a patient ID is passed via the URL
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    // Prepare SQL query to fetch the prescriptions for the selected patient
    $sql = "SELECT p.medication_name, p.dosage, p.frequency, p.duration, p.instructions, p.prescribed_at, a.full_name AS doctor_name
            FROM prescriptions p
            JOIN admins a ON p.doctor_id = a.id
            WHERE p.patient_id = :patient_id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $prescriptions = [];
    }
} else {
    // If no patient ID is provided, redirect to the nurse dashboard
    header("Location: nurse_dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Prescriptions - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #00264d;
            position: fixed;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #00509e;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .top-bar {
            background-color: #00509e;
            padding: 10px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
        }
        .top-bar input {
            border-radius: 5px;
            padding: 5px;
            border: none;
        }
        .table-container {
            margin-top: 30px;
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #00509e;
            color: white;
        }
        .back-btn {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Nurse Panel</h2>
    <a href="nurse_dashboard.php">Dashboard</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="main-content">
    <div class="top-bar">
        <input type="text" placeholder="Search..." />
        <div>
            <span>Welcome, <?= htmlspecialchars($full_name); ?></span>
        </div>
    </div>

    <div class="back-btn">
        <a href="nurse_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="table-container">
        <h3 class="mt-4">Patient Prescriptions</h3>
        <?php if (!empty($prescriptions)): ?>
            <table class="table table-bordered table-striped mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Medication</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Instructions</th>
                        <th>Prescribed At</th>
                        <th>Doctor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $prescription): ?>
                        <tr>
                            <td><?= htmlspecialchars($prescription['medication_name']); ?></td>
                            <td><?= htmlspecialchars($prescription['dosage']); ?></td>
                            <td><?= htmlspecialchars($prescription['frequency']); ?></td>
                            <td><?= htmlspecialchars($prescription['duration']); ?></td>
                            <td><?= htmlspecialchars($prescription['instructions']); ?></td>
                            <td><?= htmlspecialchars($prescription['prescribed_at']); ?></td>
                            <td><?= htmlspecialchars($prescription['doctor_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No prescriptions found for this patient.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
