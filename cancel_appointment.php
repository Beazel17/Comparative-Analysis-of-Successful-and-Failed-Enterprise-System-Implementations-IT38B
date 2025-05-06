<?php
// Start the session
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php");
    exit;
}

// Check if appointment ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No appointment selected.'); window.location.href = 'view_appointments.php';</script>";
    exit;
}

// Database credentials
$host = "localhost";
$user = "root";
$password = "";
$dbname = "medicare";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize appointment ID
$appointment_id = intval($_GET['id']);

// Check if the appointment exists
$check_sql = "SELECT id FROM appointments WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $appointment_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    echo "<script>alert('Appointment not found.'); window.location.href = 'view_appointments.php';</script>";
    $check_stmt->close();
    $conn->close();
    exit;
}
$check_stmt->close();

// Prepare delete statement
$delete_sql = "DELETE FROM appointments WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);

if ($delete_stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$delete_stmt->bind_param("i", $appointment_id);

// Execute the delete statement
if ($delete_stmt->execute()) {
    echo "<script>alert('Appointment cancelled successfully.'); window.location.href = 'view_appointments.php';</script>";
} else {
    echo "<script>alert('Failed to cancel appointment. Please try again.'); window.location.href = 'view_appointments.php';</script>";
}

// Close connections
$delete_stmt->close();
$conn->close();
?>
