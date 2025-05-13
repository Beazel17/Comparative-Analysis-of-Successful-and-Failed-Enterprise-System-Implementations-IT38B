<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "./data/config.php";

// Fetch user ID from the session
$user_id = $_SESSION["id"];

// Fetch appointments for the logged-in user
$sql = "SELECT * FROM appointments WHERE user_id = :user_id ORDER BY date DESC, time DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f9;
        }
        .container {
            margin-top: 50px;
        }
        table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Appointment History</h2>
    <?php if (count($appointments) > 0): ?>
        <table class="table table-bordered table-striped mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor/Nurse</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment["date"]); ?></td>
                        <td><?php echo htmlspecialchars($appointment["time"]); ?></td>
                        <td><?php echo htmlspecialchars($appointment["doctor_name"]); ?></td>
                        <td><?php echo htmlspecialchars($appointment["reason"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center mt-4">You have no appointment history yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
