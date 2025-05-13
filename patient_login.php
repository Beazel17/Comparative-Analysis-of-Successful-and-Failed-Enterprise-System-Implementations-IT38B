<?php
session_start();

// Include database configuration
require_once "./data/config.php";

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, email, password FROM users WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($password, $row['password'])) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["patient_id"] = $row["id"];
                        $_SESSION["email"] = $row["email"];
                        header("location: patient_dashboard.php");
                    } else {
                        $password_err = "Invalid password.";
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
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
    <h2 class="text-center">Patient Login</h2>
    <form action="patient_login.php" method="post">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
            <span class="text-danger"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <span class="text-danger"><?php echo $password_err; ?></span>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <p class="text-muted text-center">
        Don't have an account? <a href="patient_signup.php">Sign Up</a>
    </p>
</div>
</body>
</html>
