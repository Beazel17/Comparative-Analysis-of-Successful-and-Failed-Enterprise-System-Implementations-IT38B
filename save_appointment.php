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

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['fullname'];
    $age = $_POST['age'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $illness = $_POST['illness'];
    $contact = $_POST['contact'];

    // Prepare SQL statement
    $sql = "INSERT INTO appointments (fullname, age, barangay, city, illness, contact)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sissss", $name, $age, $barangay, $city, $illness, $contact);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment scheduled successfully!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
