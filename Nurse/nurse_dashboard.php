<?php
session_start();
require_once '../data/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: nurse_login.php");
    exit;
}

$full_name = $_SESSION["full_name"];
$nurse_id = $_SESSION["id"];

// Fetch all patients
$sql = "SELECT * FROM patients"; 

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $patients = [];
}

// Fetch reports (without replies)
$sql = "SELECT * FROM reports WHERE nurse_id = ?";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nurse_id]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reports = [];
}

// Handle form submission for sending a report
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_type = $_POST['recipient_type'];
    $patient_id = $recipient_type == 'patient' ? $_POST['patient_id'] : null;
    $message = $_POST['message'];

    // Insert report into database
    $sql = "INSERT INTO reports (nurse_id, patient_id, recipient_type, message) VALUES (?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nurse_id, $patient_id, $recipient_type, $message]);
        $success_message = "Report sent successfully!";
    } catch (Exception $e) {
        $error_message = "Error sending report: " . $e->getMessage();
    }
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
        .alert-low {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-medium {
            background-color: #fff3cd;
            color: #856404;
        }
        .alert-high {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Nurse Panel</h2>
    <a href="nurse_dashboard.php">Dashboard</a>
    <a href="view_appointments.php">View Appointments</a>
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
                        <th>Status</th>
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
                                <!-- Patient Status Display -->
                                <span class="badge <?= $patient['status'] == 'Active' ? 'badge-success' : ($patient['status'] == 'Inactive' ? 'badge-secondary' : 'badge-warning') ?>"><?= htmlspecialchars($patient['status']); ?></span>
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

    <!-- Report Form Section -->
    <div class="mt-4">
        <h3>Send a Report</h3>
        <?php if (!empty($success_message)) echo "<p class='text-success'>$success_message</p>"; ?>
        <?php if (!empty($error_message)) echo "<p class='text-danger'>$error_message</p>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="recipient_type">Send Report To:</label>
                <select class="form-control" id="recipient_type" name="recipient_type" required>
                    <option value="admin">Admin</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            <div class="form-group" id="patient-select-container" style="display: none;">
                <label for="patient_id">Select Patient:</label>
                <select class="form-control" id="patient_id" name="patient_id">
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= $patient['id']; ?>"><?= htmlspecialchars($patient['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Report</button>
        </form>
    </div>

    <!-- Reports Section -->
    <div class="mt-4">
        <h3>Reports</h3>
        <?php foreach ($reports as $report): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Report: <?= htmlspecialchars($report['id']); ?></h5>
                    <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($report['message'])); ?></p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($report['created_at']); ?></p>

                    <!-- Alert Level Display -->
                    <div class="alert alert-<?= strtolower($report['alert_level']); ?>" role="alert">
                        <strong>Alert Level:</strong> <?= htmlspecialchars($report['alert_level']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.getElementById('recipient_type').addEventListener('change', function() {
        var patientSelectContainer = document.getElementById('patient-select-container');
        if (this.value == 'patient') {
            patientSelectContainer.style.display = 'block';
        } else {
            patientSelectContainer.style.display = 'none';
        }
    });
</script>

</body>
</html>
