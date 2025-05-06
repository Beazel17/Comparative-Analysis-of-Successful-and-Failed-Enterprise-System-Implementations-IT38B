<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Prescription - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add New Prescription</h2>
    <form action="save_prescription.php" method="POST">
        <div class="form-group">
            <label>Patient Name</label>
            <input type="text" name="patient_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Age</label>
            <input type="number" name="age" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Prescription Details</label>
            <textarea name="details" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Prescription</button>
    </form>
</div>
</body>
</html>
