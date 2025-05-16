<?php
require_once '../data/config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_appointment'])) {
    $patient_id = $_POST['patient_id'];
    $department = $_POST['department'];
    $doctor_name = $_POST['doctor_name'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    if (empty($patient_id) || empty($department) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = "All fields are required!";
    } else {
        $sql = "INSERT INTO appointments (patient_id, department, doctor_name, appointment_date, appointment_time, reason, status)
                VALUES (:patient_id, :department, :doctor_name, :appointment_date, :appointment_time, :reason, 'Pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':doctor_name', $doctor_name);
        $stmt->bindParam(':appointment_date', $appointment_date);
        $stmt->bindParam(':appointment_time', $appointment_time);
        $stmt->bindParam(':reason', $reason);

        if ($stmt->execute()) {
            $success_message = "Appointment created successfully!";
        } else {
            $error_message = "Failed to create appointment. Please try again.";
        }
    }
}

// Handle Edit Appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $patient_id = $_POST['patient_id'];
    $department = $_POST['department'];
    $doctor_name = $_POST['doctor_name'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    if (empty($patient_id) || empty($department) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = "All fields are required!";
    } else {
        $sql = "UPDATE appointments SET patient_id = :patient_id, department = :department, doctor_name = :doctor_name,
                appointment_date = :appointment_date, appointment_time = :appointment_time, reason = :reason
                WHERE id = :appointment_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':doctor_name', $doctor_name);
        $stmt->bindParam(':appointment_date', $appointment_date);
        $stmt->bindParam(':appointment_time', $appointment_time);
        $stmt->bindParam(':reason', $reason);

        if ($stmt->execute()) {
            $success_message = "Appointment updated successfully!";
        } else {
            $error_message = "Failed to update appointment. Please try again.";
        }
    }
}

// Fetch all appointments and patients
$sql_appointments = "SELECT * FROM appointments";
$stmt_appointments = $pdo->prepare($sql_appointments);
$stmt_appointments->execute();
$appointments = $stmt_appointments->fetchAll(PDO::FETCH_ASSOC);

$sql_patients = "SELECT * FROM patients";
$stmt_patients = $pdo->prepare($sql_patients);
$stmt_patients->execute();
$patients = $stmt_patients->fetchAll(PDO::FETCH_ASSOC);

$appointment_to_edit = null;
if (isset($_GET['edit'])) {
    $appointment_id = $_GET['edit'];
    $sql_edit = "SELECT * FROM appointments WHERE id = :appointment_id";
    $stmt_edit = $pdo->prepare($sql_edit);
    $stmt_edit->bindParam(':appointment_id', $appointment_id);
    $stmt_edit->execute();
    $appointment_to_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments - MediCare</title>
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
        <a href="appointment.php" class="<?= basename($_SERVER['PHP_SELF']) == 'appointment.php' ? 'active' : '' ?>">Manage Appointments</a>
        <a href="prescriptions.php" class="<?= basename($_SERVER['PHP_SELF']) == 'prescriptions.php' ? 'active' : '' ?>">Prescriptions</a>
      
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>"> Reports</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <div class="topbar">
        <div>Welcome, Admin</div>
    </div>

    <div class="main-content">
        <h3 class="text-center">Manage Appointments</h3>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif (isset($success_message)): ?>
            <div id="success-message" class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <!-- Appointment creation form -->
        <h4>Create New Appointment</h4>
        <form method="POST" action="appointment.php">
            <div class="form-group">
                <label for="patient_id">Patient</label>
                <select class="form-control" id="patient_id" name="patient_id" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= htmlspecialchars($patient['id']) ?>" <?= isset($appointment_to_edit) && $appointment_to_edit['patient_id'] == $patient['id'] ? 'selected' : '' ?>><?= htmlspecialchars($patient['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="department">Department/Specialty</label>
                <input type="text" class="form-control" id="department" name="department" value="<?= isset($appointment_to_edit) ? htmlspecialchars($appointment_to_edit['department']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="doctor_name">Doctor's Name</label>
                <input type="text" class="form-control" id="doctor_name" name="doctor_name" value="<?= isset($appointment_to_edit) ? htmlspecialchars($appointment_to_edit['doctor_name']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="appointment_date">Appointment Date</label>
                <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?= isset($appointment_to_edit) ? htmlspecialchars($appointment_to_edit['appointment_date']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="appointment_time">Appointment Time</label>
                <input type="time" class="form-control" id="appointment_time" name="appointment_time" value="<?= isset($appointment_to_edit) ? htmlspecialchars($appointment_to_edit['appointment_time']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="reason">Reason</label>
                <textarea class="form-control" id="reason" name="reason"><?= isset($appointment_to_edit) ? htmlspecialchars($appointment_to_edit['reason']) : '' ?></textarea>
            </div>
            <?php if (isset($appointment_to_edit)): ?>
                <input type="hidden" name="appointment_id" value="<?= $appointment_to_edit['id'] ?>">
                <button type="submit" name="edit_appointment" class="btn btn-warning">Update Appointment</button>
                <a href="appointment.php" class="btn btn-secondary">Cancel Edit</a>
            <?php else: ?>
                <button type="submit" name="create_appointment" class="btn btn-primary">Create Appointment</button>
            <?php endif; ?>
        </form>

        <hr>

        <!-- Existing appointments -->
        <h4>Existing Appointments</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient Name</th>
                        <th>Department</th>
                        <th>Doctor Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['id']) ?></td>
                            <td><?= htmlspecialchars($appointment['patient_id']) ?></td>
                            <td><?= htmlspecialchars($appointment['department']) ?></td>
                            <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                            <td><?= htmlspecialchars($appointment['status']) ?></td>
                            <td><?= htmlspecialchars($appointment['reason']) ?></td>
                            <td>
                                <a href="appointment.php?edit=<?= $appointment['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        setTimeout(function() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);
    </script>

</body>
</html>
