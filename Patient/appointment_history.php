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

    header("Location: appointment_history.php");
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
        .welcome {
            text-align: center;
            margin-top: 50px;
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #00509e;
            color: white;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Welcome Patient</h2>
    <a href="patient_dashboard.php">Dashboard</a>
    <a href="book_appointment.php">Book Appointment</a>
    <a href="appointment_history.php">View Appointment History</a>
    <a href="view_prescriptions.php">view prescriptions</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="main-content">
    <div class="top-bar">
        <input type="text" placeholder="Search..." />
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
            <a href="#"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAACoCAMAAABt9SM9AAAA5FBMVEX///8AAGwAAAAAAGMBAQYAAGkAAGUAAGEAAGcAAF8AAG7x8fL7+/z29vfq6usgIHj09PnZ2efb29zk5OVSUpDAwNaNjbPw8Pbo6PElJXrs7O3Z2dqJiYteXmClpcPd3eqXl7kzM4DQ0NO8vL2wsMuwsLFtbZ8kJCfJydyfn79kZJp4eKbHx8scHB9qamw5OYNbW5VOTo2JibCWlpgTE3RCQkRTU1VAQEKqqq2BgYNycnQNDHJERIhycqLExNgxMX8yMjQTExhZWGCurLWQkJFubHVkZGWpqap5eXoAAFW7ucJ2doD5mWlLAAAPdElEQVR4nO1cCVvaShceErNAErYIIqAgAQuauoAbSq96a8Xr/f//55s550wWFi3aev3KvM/TmmUmmXlz9glhTEFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQeFn4LosbJTZle+FXsP9r0fzqVEflQM2YhcsDN08O2J+r+z/12P6nAhYh43DOrsadRgb1/g/9oN5HfajoQhLoecx1myGbFR2nGfvR55xwtwRp4uxTs8pe2WlkQS3zBp18ffBD67CTqPsNpjjsvIVG+fzrNnosCYnTgHgjUYh17zyVZnVZySoXHeaTlivj5VkAbjMOH7n7/zSBo4/5TLH2PIW64Pv1y57fkVwylzwuHNk7jqbeo8T8NwYB/mf1LKr3u8dz+fGiMcLnvezrRsj5l79zuF8YlyNPf+pDI7w58DlqsN+nts/CHzS4UPTWUWx/EbDccdl57eN6dMiDJkXruzhfHbtlX/HcD41QuaELFhZpTpstH4hRP3aZ85brE+nx/NFiOcvg188ps+KkLGn6fObuvpXnQfBcl7T1iMHcqejd0TkTlP8H2ja5a8b0eeF47Pe9duD8Qeg+VLTvv6yEX1i+OPvrP7OcOlI29DufsVgPj9GD+8sI4y1jQ2t+WsG8+nReV/3puBKW4e6zZtChhSOBFdf16AE4YSdxvsSFvcCuFqHrKf3vdd8Xzh5CVyJ/PuPD+bd8Pvze4QiD3K1Iy5R1x5+1ahexC5g0KfdrVrxQ24LCN4jWA1NcHUN26GmfUTK42ZNgewu7h3qpn3yAbd9P5JcsS+a9rSgTTdXQNy0E0e3DDq6XVjxnn09I2CVYO9Y7OkfwlbQe5cP63GuNIqv3B2+vagcNjQNhPlt0VH7dsWbdpEsHZSvn8WdrbdNYCW49fFR4829r4GrEHe4nde+LGq1Z2QQ9n58sCgPZuzhinfdt0U3Yw92qmZSzH4v8o7nvjmWhPBKmqkLwduiOmBNl7QYCWVpm/KoWV3xthMjwTxdR/8AG+81m8Fb9dABN/gFY3//PCFjaVRisrajg0U7E5G1u9p9SSbNNuyhGurfXu7zS8AlwXsjWWjav6BYdrRlcsXYYYKXcN6wXjHTbb5f4BgF3hs11bn2AX33izEbdTme8gOGr0FyV1BmbL9jNFiBlK/fn9nffuZ7yAAlA3gNLR/P4Xjb2A2CwQAAAAAElFTkSuQmCC" alt="User Profile" width="40" height="40" />
        </div>
    </div>

    <div class="container">
        <h1 class="history-title">Appointment History</h1>

        <?php if (empty($appointments)): ?>
            <p class="no-appointments">You have no past appointments yet.</p>
        <?php else: ?>
            <?php foreach ($appointments as $appointment): ?>
                <div class="card">
                    <div class="card-header">
                        <strong>Appointment with Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($appointment['status']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
