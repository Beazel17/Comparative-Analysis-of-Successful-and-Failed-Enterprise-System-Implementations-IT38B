<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "./data/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $time = $_POST["time"];
    $doctor_name = $_POST["doctor_name"];
    $reason = $_POST["reason"];
    $user_id = $_SESSION["id"];

    // Combine date and time into a single datetime
    $appointment_date = date("Y-m-d H:i:s", strtotime("$date $time"));

    $sql = "INSERT INTO appointments (user_id, appointment_date, doctor_name, reason) VALUES (:user_id, :appointment_date, :doctor_name, :reason)";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":appointment_date", $appointment_date, PDO::PARAM_STR);
        $stmt->bindParam(":doctor_name", $doctor_name, PDO::PARAM_STR);
        $stmt->bindParam(":reason", $reason, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>alert('Appointment booked successfully!');</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }

        unset($stmt);
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="text-center">Book Appointment</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" name="time" id="time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="doctor_name">Doctor/Nurse Name</label>
                    <input type="text" name="doctor_name" id="doctor_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Appointment</label>
                    <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Book Appointment</button>
            </form>
        </div>
    </div>
</body>
</html>
