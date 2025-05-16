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

    // Prepare SQL query to fetch patient details
    $sql = "SELECT * FROM patients WHERE id = :patient_id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the patient was found
        if (!$patient) {
            echo "Patient not found.";
            exit;
        }
    } catch (Exception $e) {
        echo "Error fetching patient details: " . $e->getMessage();
        exit;
    }
} else {
    // If no patient ID is provided, redirect to the nurse dashboard
    header("Location: nurse_dashboard.php");
    exit;
}

// Handle the status update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];

    // Update patient status
    $update_sql = "UPDATE patients SET status = :status WHERE id = :patient_id";

    try {
        $stmt = $pdo->prepare($update_sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to dashboard or view patient status page after update
        header("Location: nurse_dashboard.php");
        exit;
    } catch (Exception $e) {
        echo "Error updating patient status: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Patient Status - MediCare</title>
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
        .form-container {
            margin-top: 30px;
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

    <!-- Back Button -->
    <div class="back-btn">
        <a href="nurse_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="form-container">
        <h3 class="mt-4">Update Patient Status</h3>

        <!-- Patient Details Form -->
        <form action="update_patient_status.php?patient_id=<?= $patient['id']; ?>" method="POST">
            <div class="form-group">
                <label for="patientName">Patient Name</label>
                <input type="text" class="form-control" id="patientName" value="<?= htmlspecialchars($patient['full_name']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="patientEmail">Patient Email</label>
                <input type="email" class="form-control" id="patientEmail" value="<?= htmlspecialchars($patient['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="status">Select Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Active" <?= $patient['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?= $patient['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="Under Treatment" <?= $patient['status'] == 'Under Treatment' ? 'selected' : ''; ?>>Under Treatment</option>
                    <option value="Recovered" <?= $patient['status'] == 'Recovered' ? 'selected' : ''; ?>>Recovered</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    </div>
</div>

</body>
</html>
