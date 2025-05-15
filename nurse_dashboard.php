<?php
session_start();
require_once "./data/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "nurse") {
    header("location: nurse_login.php");
    exit;
}

$nurse_id = $_SESSION["id"]; // FIXED HERE
$full_name = $_SESSION["full_name"] ?? "Nurse";

// Get assigned patients
$sql = "SELECT * FROM patients WHERE assigned_nurse_id = :nurse_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":nurse_id", $nurse_id, PDO::PARAM_INT);
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch prescriptions per patient
$prescriptions = [];
foreach ($patients as $patient) {
    $sql = "SELECT * FROM prescriptions WHERE patient_id = :patient_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":patient_id", $patient['id'], PDO::PARAM_INT);
    $stmt->execute();
    $prescriptions[$patient['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nurse Dashboard - MediCare</title>
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
        .btn-sm {
            margin-bottom: 4px;
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
        <input type="text" placeholder="Search patient..." />
        <div>
            <span>Welcome, <?= htmlspecialchars($full_name); ?></span>
        </div>
    </div>

    <div class="table-container">
        <h3 class="mt-4">Assigned Patients</h3>
        <?php if (!empty($patients)): ?>
            <table class="table table-bordered table-striped mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>View Prescriptions</th>
                        <th>Update Patient Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?= htmlspecialchars($patient['full_name']); ?></td>
                            <td><?= htmlspecialchars($patient['email']); ?></td>
                            <td>
                                <a class="btn btn-sm btn-success" href="view_prescriptions.php?patient_id=<?= $patient['id']; ?>">View</a>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="update_patient_status.php?patient_id=<?= $patient['id']; ?>">Update Status</a>
                            </td>
                            <td>
    <a class="btn btn-sm btn-success" href="view_prescriptions.php?patient_id=<?= $patient['id']; ?>">View</a>
</td>
<td>
    <a class="btn btn-sm btn-primary" href="update_patient_status.php?patient_id=<?= $patient['id']; ?>">Update Status</a>
</td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No patients assigned to you yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
