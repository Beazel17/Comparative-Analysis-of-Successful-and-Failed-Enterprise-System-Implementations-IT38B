<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Appointment - MediCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0d47a1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #b0bec5;
            border-radius: 8px;
            outline: none;
            transition: 0.2s ease-in-out;
        }

        .form-group input:focus {
            border-color: #42a5f5;
            box-shadow: 0 0 5px rgba(66, 165, 245, 0.5);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
        }

        .form-actions button {
            width: 48%;
            padding: 10px;
            background-color: #0d47a1;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s ease-in-out;
        }

        .form-actions button:hover {
            background-color: #1565c0;
        }

        .form-actions .cancel-btn {
            background-color: #b0bec5;
            color: #263238;
        }

        .form-actions .cancel-btn:hover {
            background-color: #90a4ae;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>MediCare - New Appointment</h2>
    <form action="save_appointment.php" method="POST">
        <div class="form-group">
            <input type="text" name="fullname" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <input type="number" name="age" placeholder="Age" required>
        </div>
        <div class="form-group">
            <input type="text" name="barangay" placeholder="Barangay" required>
        </div>
        <div class="form-group">
            <input type="text" name="city" placeholder="City/Municipality" required>
        </div>
        <div class="form-group">
            <input type="text" name="illness" placeholder="Illness" required>
        </div>
        <div class="form-group">
            <input type="text" name="contact" placeholder="Contact Number" required>
        </div>

        <div class="form-actions">
            <button type="submit">Submit</button>
            <button type="button" class="cancel-btn" onclick="window.location.href='dashboard.php'">Cancel</button>
        </div>
    </form>
</div>

</body>
</html>
