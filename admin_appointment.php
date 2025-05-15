<?php
session_start();
require_once "./data/config.php";

// Ensure only admin can access
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["role"] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle deletion of an appointment
if (isset($_GET['delete_id'])) {
    try {
        $deleteStmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $deleteStmt->execute([$_GET['delete_id']]);
        header("Location: admin_appointment.php?success=Appointment deleted successfully");
        exit();
    } catch (Exception $e) {
        $error = "Failed to delete the appointment.";
    }
}

// Fetch appointments
try {
    $stmt = $pdo->query("SELECT a.id, u.name AS user_name, a.appointment_date, a.status, a.doctor_name, a.reason, a.created_at 
                          FROM appointments a 
                          JOIN users u ON a.user_id = u.id");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $appointments = [];
    $error = "Failed to fetch appointments.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments - Admin Panel</title>
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
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="user_management.php">User Management</a>
    <a href="patient_management.php">Patient Management</a>
    <a href="admin_appointment.php" class="active">Appointments</a>
    <a href="prescriptions.php">Prescriptions</a>
    <a href="billing.php">Billing & Payments</a>
    <a href="reports.php">System Reports</a>
    <a href="logout.php" class="logout-link">Logout</a>
</div>

<div class="topbar">
    <div>Welcome, Admin</div>
</div>

<div class="main-content">
    <h3>Appointments</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"> <?= htmlspecialchars($_GET['success']) ?> </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Date</th>
            <th>Status</th>
            <th>Doctor</th>
            <th>Reason</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($appointments as $appointment): ?>
            <tr>
                <td><?= htmlspecialchars($appointment['id']) ?></td>
                <td><?= htmlspecialchars($appointment['user_name']) ?></td>
                <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                <td><?= htmlspecialchars($appointment['status']) ?></td>
                <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                <td><?= htmlspecialchars($appointment['reason']) ?></td>
                <td>
                    <a href="edit_appointment.php?id=<?= htmlspecialchars($appointment['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="admin_appointment.php?delete_id=<?= htmlspecialchars($appointment['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>