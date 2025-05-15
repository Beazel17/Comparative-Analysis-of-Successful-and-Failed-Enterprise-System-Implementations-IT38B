<?php
session_start();

// Check if logged in and user ID is set
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["id"])) {
    header("location: patient_login.php");
    exit;
}

$servername = "localhost";
$username = "root";  // change as needed
$password = "";      // change as needed
$dbname = "medicare";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$patient_id = $_SESSION["patient_id"];

$sql = "SELECT id, appointment_date, status, doctor_name, reason, created_at FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Appointment History - MediCare</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
<style>
    body { background-color: #f4f4f9; font-family: Arial, sans-serif; margin: 0; padding: 0; }
    .sidebar { width: 250px; height: 100vh; background-color: #00264d; position: fixed; color: white; padding: 20px; }
    .sidebar h2 { text-align: center; margin-bottom: 20px; }
    .sidebar a { display: block; color: white; text-decoration: none; padding: 10px; margin: 5px 0; border-radius: 5px; }
    .sidebar a:hover, .sidebar a.bg-primary { background-color: #00509e; }
    .main-content { margin-left: 250px; padding: 20px; }
    .top-bar { background-color: #00509e; padding: 10px; color: white; display: flex; justify-content: space-between; align-items: center; }
    .top-bar input { border-radius: 5px; padding: 5px; }
</style>
</head>
<body>
    <div class="sidebar">
        <h2>Welcome Patient</h2>
        <a href="book_appointment.php">Book Appointment</a>
        <a href="appointment_history.php" class="bg-primary">View Appointment History</a>
        <a href="billing.php">Billing & Payments</a>
        <a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <input type="text" placeholder="Search..." />
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
                <a href="#"><img src="profile-icon.png" alt="Profile" width="30" /></a>
            </div>
        </div>

        <h1 class="my-4">Appointment History</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Appointment Date</th>
                            <th>Status</th>
                            <th>Doctor</th>
                            <th>Reason</th>
                            <th>Booked On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["id"]); ?></td>
                                <td><?php echo date("F j, Y, g:i A", strtotime($row["appointment_date"])); ?></td>
                                <td><?php echo htmlspecialchars($row["status"]); ?></td>
                                <td><?php echo htmlspecialchars($row["doctor_name"]); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row["reason"])); ?></td>
                                <td><?php echo date("F j, Y, g:i A", strtotime($row["created_at"])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>You have no appointment history.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
