<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php");
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "medicare";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fullname = $_POST['fullname'];
$service = $_POST['service'];
$amount = floatval($_POST['amount']);
$method = $_POST['method'];

$sql = "INSERT INTO billing (fullname, service, amount, method, payment_date) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssds", $fullname, $service, $amount, $method);

if ($stmt->execute()) {
    echo "<script>alert('Payment recorded successfully!'); window.location.href = 'view_bills.php';</script>";
} else {
    echo "<script>alert('Failed to record payment.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
