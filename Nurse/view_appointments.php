<?php
session_start();
require_once '../data/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: patient_login.php");
    exit;
}

$sql = "SELECT * FROM appointments WHERE patient_id = :patient_id ORDER BY appointment_date DESC, appointment_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":patient_id", $_SESSION["id"], PDO::PARAM_INT);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['finish'])) {
        $appointment_id = $_POST['appointment_id'];
        $sql_update = "UPDATE appointments SET status = 'Finished' WHERE id = :appointment_id AND patient_id = :patient_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(":appointment_id", $appointment_id, PDO::PARAM_INT);
        $stmt_update->bindParam(":patient_id", $_SESSION["id"], PDO::PARAM_INT);
        $stmt_update->execute();
    }

    if (isset($_POST['cancel'])) {
        $appointment_id = $_POST['appointment_id'];
        $sql_update = "UPDATE appointments SET status = 'Cancelled' WHERE id = :appointment_id AND patient_id = :patient_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(":appointment_id", $appointment_id, PDO::PARAM_INT);
        $stmt_update->bindParam(":patient_id", $_SESSION["id"], PDO::PARAM_INT);
        $stmt_update->execute();
    }

    header("Location: view_appointment.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment History - MediCare</title>
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
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar input {
            border-radius: 5px;
            padding: 5px;
            border: none;
        }
        .top-bar img {
            border-radius: 50%;
            margin-left: 10px;
        }
        .history-title {
            margin: 30px 0 20px;
        }
        .card {
            border-left: 5px solid #5D9CEC;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #e9f2ff;
            font-weight: bold;
        }
        .no-appointments {
            text-align: center;
            margin-top: 50px;
            font-size: 1.2em;
            color: #666;
        }
        .status-btn {
            margin-top: 10px;
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
        <input type="text" placeholder="Search..." />
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
            <a href="#"><img src="data:image/png;base64,..."></a>
        </div>
    </div>

    <h3 class="history-title">Your Appointment History</h3>

    <?php if (count($appointments) > 0): ?>
        <?php foreach ($appointments as $appointment): ?>
            <div class="card">
                <div class="card-header">
                    <?php echo htmlspecialchars($appointment['department']); ?>
                    <?php if (!empty($appointment['doctor_name'])): ?>
                        - Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($appointment['reason'] ?: 'N/A'); ?></p>
                    <p class="text-muted small">Booked on: <?php echo date("F j, Y, g:i a", strtotime($appointment['created_at'])); ?></p>

                    <!-- Display the current status -->
                    <p><strong>Status:</strong> <span class="badge badge-<?php echo getStatusClass($appointment['status']); ?>"><?php echo htmlspecialchars($appointment['status']); ?></span></p>

                    <!-- Buttons for changing status -->
                    <?php if ($appointment['status'] == 'Pending'): ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>" />
                            <button type="submit" name="finish" class="btn btn-success status-btn">Mark as Finished</button>
                            <button type="submit" name="cancel" class="btn btn-danger status-btn">Cancel Appointment</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-appointments">You have no appointment history.</div>
    <?php endif; ?>
</div>

</body>
</html>

<?php
function getStatusClass($status) {
    switch ($status) {
        case 'Finished':
            return 'success';
        case 'Cancelled':
            return 'danger';
        case 'Pending':
            return 'warning';
        default:
            return 'secondary';
    }
}
?>
