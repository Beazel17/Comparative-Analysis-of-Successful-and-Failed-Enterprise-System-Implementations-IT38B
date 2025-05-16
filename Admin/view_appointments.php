<?php
require_once '../data/config.php'; 
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['patient_id']) && !empty($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    $sql_patient = "SELECT * FROM patients WHERE id = :patient_id";
    $stmt_patient = $pdo->prepare($sql_patient);
    $stmt_patient->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
    $stmt_patient->execute();
    $patient = $stmt_patient->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        header("Location: patient_management.php");
        exit;
    }

    $sql_appointments = "SELECT * FROM appointments WHERE patient_id = :patient_id";
    $stmt_appointments = $pdo->prepare($sql_appointments);
    $stmt_appointments->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
    $stmt_appointments->execute();
    $appointments = $stmt_appointments->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: patient_management.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments for <?php echo htmlspecialchars($patient['full_name']); ?> - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
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
        .logout-link {
            color: #ff4d4d !important;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="patient_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'patient_management.php' ? 'active' : '' ?>">Patient Management</a>
        <a href="appointment.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin_appointment.php' ? 'active' : '' ?>">Appointments</a>
        <a href="prescriptions.php" class="<?= basename($_SERVER['PHP_SELF']) == 'prescriptions.php' ? 'active' : '' ?>">Prescriptions</a>
        <a href="billing.php" class="<?= basename($_SERVER['PHP_SELF']) == 'billing.php' ? 'active' : '' ?>">Billing & Payments</a>
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">System Reports</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <div class="topbar">
        <div>Welcome, Admin</div>
       
    </div>

    <div class="main-content">
        <h3 class="text-center">Appointments for <?php echo htmlspecialchars($patient['full_name']); ?></h3>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Department</th>
                        <th>Doctor Name</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['department']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No appointments found for this patient.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="patient_management.php" class="btn btn-secondary">Back to Patient Management</a>
    </div>

</body>
</html>

<?php
unset($stmt_patient);
unset($stmt_appointments); 
unset($pdo); 
?>
