<?php
session_start();
require_once "./data/config.php";

// Ensure only admin can access
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["role"] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Fetch patient data
$query = "SELECT id, full_name, age, gender, contact, address FROM patients";
$stmt = $pdo->prepare($query);
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Management - Admin Panel - MediCare</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="user_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'active' : '' ?>">User Management</a>
        <a href="patient_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'patient_management.php' ? 'active' : '' ?>">Patient Management</a>
        <a href="appointments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : '' ?>">Appointments</a>
        <a href="prescriptions.php" class="<?= basename($_SERVER['PHP_SELF']) == 'prescriptions.php' ? 'active' : '' ?>">Prescriptions</a>
        <a href="billing.php" class="<?= basename($_SERVER['PHP_SELF']) == 'billing.php' ? 'active' : '' ?>">Billing & Payments</a>
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">System Reports</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <div class="topbar">
        <div>Welcome, Admin</div>
    </div>

    <div class="main-content">
        <h3>Patient Management</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Contact</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['id']) ?></td>
                        <td><?= htmlspecialchars($patient['full_name']) ?></td>
                        <td><?= htmlspecialchars($patient['age']) ?></td>
                        <td><?= htmlspecialchars($patient['gender']) ?></td>
                        <td><?= htmlspecialchars($patient['contact']) ?></td>
                        <td><?= htmlspecialchars($patient['address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
