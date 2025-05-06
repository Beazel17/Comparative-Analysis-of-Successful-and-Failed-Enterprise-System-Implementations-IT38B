<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>MediCare Appointments</title>
  <style>
    body { background-color: #11287a; color: white; font-family: Arial, sans-serif; }
    .main { padding: 50px; text-align: center; }
    button { background-color: cyan; padding: 15px 30px; margin: 20px; border: none; font-size: 16px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="main">
    <h1>MediCare - Appointments</h1>
    <a href="schedule.php"><button>Schedule Appointment</button></a>
    <a href="view.php"><button>View Appointments</button></a>
    <a href="logout.php"><button style="background: red; color: white;">Logout</button></a>
  </div>
</body>
</html>
