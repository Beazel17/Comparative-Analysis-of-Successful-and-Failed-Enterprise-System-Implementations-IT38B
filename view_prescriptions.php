<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "medicare");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM prescriptions ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prescriptions - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Prescriptions</h2>
    <a href="add_prescription.php" class="btn btn-success mb-3">Add Prescription</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Age</th>
                <th>Address</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= $row['age'] ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="prescription_details.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
