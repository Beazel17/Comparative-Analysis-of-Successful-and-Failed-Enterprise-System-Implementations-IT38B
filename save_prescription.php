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

$patient_name = $_POST['patient_name'];
$age = $_POST['age'];
$address = $_POST['address'];
$details = $_POST['details'];

$sql = "INSERT INTO prescriptions (patient_name, age, address, details) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siss", $patient_name, $age, $address, $details);

if ($stmt->execute()) {
    echo "<script>alert('Prescription added successfully'); window.location.href = 'view_prescriptions.php';</script>";
} else {
    echo "<script>alert('Error saving prescription'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
