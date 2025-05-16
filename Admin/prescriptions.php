<?php
session_start();
require_once '../data/config.php';

// Check if admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Admin full name
$admin_name = $_SESSION["full_name"];
$admin_id = $_SESSION["id"]; // Assuming admin's ID is stored in session

// Fetch all patients for the dropdown
$stmt = $pdo->query("SELECT id, full_name FROM patients");
$patients = $stmt->fetchAll();

// Handle form submission for new prescriptions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_POST['patient_id'];
    $medication_name = $_POST['medication_name'];
    $dosage = $_POST['dosage'];
    $frequency = $_POST['frequency'];
    $duration = $_POST['duration'];
    $instructions = $_POST['instructions'];
    $pharmacy_info = $_POST['pharmacy_info'];

    try {
        // Insert prescription into the database
        $stmt = $pdo->prepare("INSERT INTO prescriptions 
            (patient_id, medication_name, dosage, frequency, duration, instructions, doctor_id, pharmacy_info) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patient_id, $medication_name, $dosage, $frequency, $duration, $instructions, $admin_id, $pharmacy_info]);

        $message = "Prescription successfully added!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch all prescriptions
$stmt = $pdo->query("SELECT p.id, p.medication_name, p.dosage, p.frequency, p.duration, p.instructions, p.pharmacy_info, pt.full_name AS patient_name, d.full_name AS doctor_name
                     FROM prescriptions p
                     JOIN patients pt ON p.patient_id = pt.id
                     JOIN doctors d ON p.doctor_id = d.id");
$prescriptions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Prescribe Medication</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            width: 220px;
            height: 100vh;
            position: fixed;
            background-color: #002244;
            color: white;
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #004080;
        }
        .topbar {
            margin-left: 220px;
            background-color: #004080;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .main-content {
            margin-left: 220px;
            padding: 30px;
        }
        .form-group label {
            font-weight: bold;
        }
        .table th, .table td {
            text-align: center;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
    <script>
        // Function to hide the success message after 5 seconds
        window.onload = function() {
            var message = document.getElementById("success-message");
            if (message) {
                setTimeout(function() {
                    message.style.display = 'none';
                }, 5000);
            }
        };
    </script>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="patient_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'patient_management.php' ? 'active' : '' ?>">Patient Management</a>
        <a href="appointment.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin_appointment.php' ? 'active' : '' ?>">Appointments</a>
        <a href="prescriptions.php" class="<?= basename($_SERVER['PHP_SELF']) == 'prescriptions.php' ? 'active' : '' ?>">Prescriptions</a>
      
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>"> Reports</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <div class="topbar">
        <div>Welcome, <?= htmlspecialchars($admin_name) ?></div>
    </div>

    <div class="main-content">
        <h3>Prescribe Medication</h3>

        <?php if (isset($message)): ?>
            <div id="success-message" class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form action="prescriptions.php" method="POST">
            <div class="form-group">
                <label for="patient_id">Select Patient</label>
                <select class="form-control" id="patient_id" name="patient_id" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= $patient['id'] ?>"><?= $patient['full_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="medication_name">Medication Name</label>
                <input type="text" class="form-control" id="medication_name" name="medication_name" required>
            </div>

            <div class="form-group">
                <label for="dosage">Dosage</label>
                <input type="text" class="form-control" id="dosage" name="dosage" required>
            </div>

            <div class="form-group">
                <label for="frequency">Frequency</label>
                <input type="text" class="form-control" id="frequency" name="frequency" required>
            </div>

            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" class="form-control" id="duration" name="duration" required>
            </div>

            <div class="form-group">
                <label for="instructions">Instructions</label>
                <textarea class="form-control" id="instructions" name="instructions"></textarea>
            </div>

            <div class="form-group">
                <label for="pharmacy_info">Pharmacy Information (Optional)</label>
                <textarea class="form-control" id="pharmacy_info" name="pharmacy_info"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Prescribe Medication</button>
        </form>

        <hr>

        <h3>All Prescriptions</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Prescription ID</th>
                    <th>Patient Name</th>
                    <th>Medication Name</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                    <th>Pharmacy Info</th>
                    <th>Doctor Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td><?= $prescription['id'] ?></td>
                        <td><?= $prescription['patient_name'] ?></td>
                        <td><?= $prescription['medication_name'] ?></td>
                        <td><?= $prescription['dosage'] ?></td>
                        <td><?= $prescription['frequency'] ?></td>
                        <td><?= $prescription['duration'] ?></td>
                        <td><?= $prescription['instructions'] ?></td>
                        <td><?= $prescription['pharmacy_info'] ?></td>
                        <td><?= $prescription['doctor_name'] ?></td>
                        <td>
                            <a href="edit_prescription.php?id=<?= $prescription['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_prescription.php?id=<?= $prescription['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this prescription?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
