<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch the user by email
    $stmt = $conn->prepare("SELECT * FROM patients WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $patient = $result->fetch_assoc();
        if (password_verify($password, $patient['password'])) {
            // Start a session and redirect to dashboard
            session_start();
            $_SESSION['patient_id'] = $patient['id'];
            $_SESSION['patient_name'] = $patient['full_name'];
            header('Location: patient_dashboard.php');
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            background: #5D9CEC;
            border: none;
        }
        .btn-primary:hover {
            background: #4A89DC;
        }
        .text-muted {
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2 class="text-center">Admin Login</h2>
    <form action="admin_dashboard.php" method="post">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <p class="text-muted text-center">
        Don't have an account? <a href="patient_signup.php">Sign Up</a>
    </p>
</div>
</body>
</html>
