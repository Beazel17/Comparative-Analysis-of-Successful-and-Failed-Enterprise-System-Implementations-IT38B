<?php
session_start();

// Initialize variables
$email = $password = "";
$email_err = $password_err = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize inputs
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validate credentials
    if (empty($email)) {
        $email_err = "Please enter your email.";
    } elseif ($email !== "admin@gmail.com") {
        $email_err = "Email not recognized.";
    }

    if (empty($password)) {
        $password_err = "Please enter your password.";
    } elseif ($password !== "admin123123") {
        $password_err = "Incorrect password.";
    }

    // If no errors, log the user in
    if (empty($email_err) && empty($password_err)) {
        // Set session variables
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_email"] = $email;
        $_SESSION["role"] = "admin";  // Setting the user role as admin
        header("Location: admin_dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
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
    <form action="admin_login.php" method="post" novalidate>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            <span class="invalid-feedback"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required>
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <p class="text-muted text-center">
        Go back to <a href="index.php">Home</a>
    </p>
</div>
</body>
</html>
