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

// Fetch reports sent to admin
try {
    $sql = "SELECT reports.*, nurses.full_name AS nurse_name 
            FROM reports 
            JOIN nurses ON reports.nurse_id = nurses.id
            WHERE reports.recipient_type = 'admin' 
            ORDER BY reports.created_at DESC";
    $stmt = $pdo->query($sql);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reports = [];
}

// Handle reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply'])) {
    $report_id = $_POST['report_id'];
    $reply_message = $_POST['reply_message'];
    $alert_level = $_POST['alert_level'];

    // Insert reply and update the alert level
    try {
        // Insert reply into reports table (or you could create a separate table for replies)
        $sql = "UPDATE reports 
                SET message = CONCAT(message, '\n\nAdmin Reply: ', ?) 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$reply_message, $report_id]);

        // Update the alert level of the report
        $sql = "UPDATE reports SET alert_level = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$alert_level, $report_id]);

        $success_message = "Reply sent successfully and alert level updated!";
    } catch (Exception $e) {
        $error_message = "Error sending reply: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reports - MediCare</title>
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
        .report-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .report-card h4 {
            color: #004080;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .report-card .meta {
            font-size: 14px;
            color: #666;
        }
        .report-card .message {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .alert-level {
            font-weight: bold;
        }
        .logout-link {
            color: #ff4d4d !important;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="patient_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'patient_management.php' ? 'active' : '' ?>">Patient Management</a>
        <a href="appointment.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin_appointment.php' ? 'active' : '' ?>">Appointments</a>
        <a href="prescriptions.php" class="<?= basename($_SERVER['PHP_SELF']) == 'prescriptions.php' ? 'active' : '' ?>">Prescriptions</a>
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">Reports</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <div class="topbar">
        <div>Welcome, <?= htmlspecialchars($admin_name) ?></div>
    </div>

    <div class="main-content">
        <h3>Reports Received</h3>

        <?php if (!empty($success_message)) echo "<p class='text-success'>$success_message</p>"; ?>
        <?php if (!empty($error_message)) echo "<p class='text-danger'>$error_message</p>"; ?>

        <?php if (!empty($reports)): ?>
            <?php foreach ($reports as $report): ?>
                <div class="report-card">
                    <h4>Report from Nurse: <?= htmlspecialchars($report['nurse_name']) ?></h4>
                    <div class="meta">
                        <span><strong>Date:</strong> <?= htmlspecialchars($report['created_at']) ?></span><br>
                        <span><strong>Patient:</strong> <?= $report['patient_id'] ? 'Patient ID: ' . htmlspecialchars($report['patient_id']) : 'N/A' ?></span><br>
                        <span><strong>Alert Level:</strong> 
                            <span class="alert-level"><?= htmlspecialchars($report['alert_level']) ?: 'Not Set' ?></span>
                        </span>
                    </div>
                    <div class="message">
                        <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($report['message'])) ?></p>
                    </div>

                    <!-- Reply and Alert Level Form -->
                    <form method="POST">
                        <div class="form-group">
                            <label for="reply_message">Reply to this Report:</label>
                            <textarea class="form-control" id="reply_message" name="reply_message" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="alert_level">Select Alert Level:</label>
                            <select class="form-control" id="alert_level" name="alert_level" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                        <button type="submit" name="reply" class="btn btn-primary">Send Reply and Update</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No reports received yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>
