<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - MediCare</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1a237e;
            color: white;
        }
        .sidebar {
            height: 100vh;
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #0d47a1;
            padding-top: 20px;
        }
        .sidebar button {
            width: 90%;
            margin: 10px;
            padding: 10px;
            background-color: #e3f2fd;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .sidebar button:hover {
            background-color: #bbdefb;
        }
        .main-content {
            margin-left: 230px;
            padding: 20px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-bar input {
            padding: 5px;
            width: 300px;
        }
        .section {
            margin-top: 30px;
            text-align: center;
            display: none;
        }
        .section button {
            margin: 10px;
            background-color: cyan;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .section button:hover {
            background-color: #00e5ff;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 style="text-align: center;">MediCare</h4>
    <button onclick="showSection('patient')">Patient Management</button>
    <button onclick="showSection('appointment')">Appointments</button>
    <button onclick="showSection('prescription')">Prescriptions</button>
    <button onclick="showSection('billing')">Billing & Payments</button>
    <button onclick="location.href='logout.php'">Logout</button>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
        <div>
            <img src="https://via.placeholder.com/30" alt="Profile" style="border-radius: 50%;">
        </div>
    </div>

    <!-- Patient Section -->
    <div id="patient-section" class="section">
        <button onclick="location.href='add_patient.php'">Add Patient</button>
        <button onclick="location.href='view_patients.php'">View Patients</button>
        <button onclick="location.href='edit_delete_patient.php'">Edit/Delete Patient</button>
    </div>

    <!-- Appointment Section -->
    <div id="appointment-section" class="section">
        <button onclick="location.href='schedule.php'">Schedule Appointment</button>
        <button onclick="location.href='view_appointments.php'">View Appointments</button>
    </div>

    <!-- Prescription Section -->
    <div id="prescription-section" class="section">
        <button onclick="location.href='add_prescription.php'">Add Prescription</button>
        <button onclick="location.href='view_prescriptions.php'">View Prescriptions</button>
    </div>

    <!-- Billing Section -->
    <div id="billing-section" class="section">
        <button onclick="location.href='add_bill.php'">Add Payment</button>
        <button onclick="location.href='view_bills.php'">View Bills</button>
    </div>
</div>

<script>
function showSection(section) {
    const sections = ['patient', 'appointment', 'prescription', 'billing'];
    sections.forEach(sec => {
        document.getElementById(sec + '-section').style.display = (section === sec) ? 'block' : 'none';
    });
}
</script>

</body>
</html>
