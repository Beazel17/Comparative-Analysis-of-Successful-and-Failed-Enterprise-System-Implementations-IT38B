<?php
session_start();

// Check if the user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MediCare</title>
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
        }
        .welcome {
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>MediCare</h2>
        <a href="#">Book Appointment</a>
        <a href="#">View Appointment History</a>
        <a href="#">View Bills & Payments</a>
        <a href="#">Edit Personal Info</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="main-content">
        <div class="top-bar">
            <input type="text" placeholder="Search..." />
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
                <a href="#"><img src="profile-icon.png" alt="Profile" width="30"></a>
            </div>
        </div>
        <div class="welcome">
            <h1>Welcome to MediCare</h1>
            <p>Please select a section from the sidebar to begin.</p>
        </div>
    </div>
</body>
</html>
