<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php");
    exit;
}

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "medicare";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch appointments
$sql = "SELECT * FROM appointments ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointments - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e3f2fd;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-cancel {
            background-color: #ff1744;
            color: white;
        }
        .btn-cancel:hover {
            background-color: #f50057;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Appointments List</h2>
    <a href="dashboard.php" class="btn btn-primary mb-3">Back to Dashboard</a>
    
    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Age</th>
                <th>Barangay</th>
                <th>City</th>
                <th>Illness</th>
                <th>Contact</th>
                <th>Scheduled At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']); ?></td>
                        <td><?= htmlspecialchars($row['fullname']); ?></td>
                        <td><?= htmlspecialchars($row['age']); ?></td>
                        <td><?= htmlspecialchars($row['barangay']); ?></td>
                        <td><?= htmlspecialchars($row['city']); ?></td>
                        <td><?= htmlspecialchars($row['illness']); ?></td>
                        <td><?= htmlspecialchars($row['contact']); ?></td>
                        <td><?= htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a href="cancel_appointment.php?id=<?= $row['id']; ?>" 
                               class="btn btn-cancel btn-sm" 
                               onclick="return confirm('Are you sure you want to cancel this appointment?');">
                               Cancel
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
