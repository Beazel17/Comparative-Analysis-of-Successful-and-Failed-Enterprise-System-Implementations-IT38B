<?php
session_start();
require_once '../data/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: patient_login.php");
    exit;
}

$department = $doctor = $date = $time = $reason = "";
$department_err = $date_err = $time_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = trim($_POST["department"]);
    $doctor = trim($_POST["doctor"]);
    $date = trim($_POST["date"]);
    $time = trim($_POST["time"]);
    $reason = trim($_POST["reason"]);

    if (empty($department)) {
        $department_err = "Please select a department.";
    }

    if (empty($date)) {
        $date_err = "Please select a date.";
    }

    if (empty($time)) {
        $time_err = "Please select a time.";
    }

    if (empty($department_err) && empty($date_err) && empty($time_err)) {
        $sql = "INSERT INTO appointments (patient_id, department, doctor_name, appointment_date, appointment_time, reason) 
                VALUES (:patient_id, :department, :doctor, :date, :time, :reason)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":patient_id", $_SESSION["id"], PDO::PARAM_INT);
        $stmt->bindParam(":department", $department);
        $stmt->bindParam(":doctor", $doctor);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":time", $time);
        $stmt->bindParam(":reason", $reason);
        if ($stmt->execute()) {
            header("Location: appointment_history.php");
            exit;
        } else {
            echo "Something went wrong. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #00264d;
            position: fixed;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #00509e;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .top-bar {
            background-color: #00509e;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar input {
            border-radius: 5px;
            padding: 5px;
            border: none;
        }
        .top-bar img {
            border-radius: 50%;
            margin-left: 10px;
        }
        .form-container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            margin: 30px auto;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Welcome Patient</h2>
    <a href="patient_dashboard.php">Dashboard</a>
    <a href="book_appointment.php">Book Appointment</a>
    <a href="appointment_history.php">View Appointment History</a>
    <a href="view_prescriptions.php">view prescriptions</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="main-content">
    <div class="top-bar">
        <input type="text" placeholder="Search..." />
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
            <a href="#"><img src="data:image/png;base64,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" alt="Profile" width="30"></a>
        </div>
    </div>

    <div class="form-container">
        <h2 class="text-center mb-4">Book an Appointment</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="department">Department/Specialty</label>
                <select name="department" class="form-control <?php echo (!empty($department_err)) ? 'is-invalid' : ''; ?>" required>
                    <option value="">-- Select --</option>
                    <option value="General Medicine" <?php if ($department == 'General Medicine') echo 'selected'; ?>>General Medicine</option>
                    <option value="Cardiology" <?php if ($department == 'Cardiology') echo 'selected'; ?>>Cardiology</option>
                    <option value="Pediatrics" <?php if ($department == 'Pediatrics') echo 'selected'; ?>>Pediatrics</option>
                    <option value="Dermatology" <?php if ($department == 'Dermatology') echo 'selected'; ?>>Dermatology</option>
                    <option value="Orthopedics" <?php if ($department == 'Orthopedics') echo 'selected'; ?>>Orthopedics</option>
                </select>
                <span class="text-danger"><?php echo $department_err; ?></span>
            </div>

            <div class="form-group">
                <label for="doctor">Doctor Name (optional)</label>
                <input type="text" name="doctor" class="form-control" value="<?php echo htmlspecialchars($doctor); ?>">
            </div>

            <div class="form-group">
                <label for="date">Appointment Date</label>
                <input type="date" name="date" class="form-control <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($date); ?>" required>
                <span class="text-danger"><?php echo $date_err; ?></span>
            </div>

            <div class="form-group">
                <label for="time">Appointment Time</label>
                <input type="time" name="time" class="form-control <?php echo (!empty($time_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($time); ?>" required>
                <span class="text-danger"><?php echo $time_err; ?></span>
            </div>

            <div class="form-group">
                <label for="reason">Reason for Visit (optional)</label>
                <textarea name="reason" rows="4" class="form-control"><?php echo htmlspecialchars($reason); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Book Appointment</button>
        </form>
    </div>
</div>
</body>
</html>
