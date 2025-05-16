<?php
require_once '../data/config.php'; // Include your database configuration file
session_start();

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If no errors, proceed with login
    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, full_name, email, password FROM admins WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind the email parameter to the prepared statement
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            // Execute the query
            if ($stmt->execute()) {
                // Check if the email exists in the database
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the row with user data

                    // Directly compare the password without using password_verify (no hashing)
                    if ($password === $row["password"]) {
                        // Password is correct, start the session and redirect to the dashboard
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $row["id"];
                        $_SESSION["email"] = $row["email"];
                        $_SESSION["full_name"] = $row["full_name"];
                        $_SESSION["role"] = "admin";

                        header("Location: admin_dashboard.php"); // Redirect to the admin dashboard
                        exit;
                    } else {
                        $password_err = "Invalid password.";
                    }
                } else {
                    $email_err = "No admin account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt); // Close the statement
        }
    }
    unset($pdo); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
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
        .text-danger {
            font-size: 0.9em;
            margin-top: 5px;
            display: block;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #5D9CEC;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        .back-btn:hover {
            background-color: #4A89DC;
        }
    </style>
</head>
<body>
    <a href="../index.php" class="back-btn">Back to Home</a>
    <div class="login-container">
        <h2 class="text-center mb-4">Admin Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" required autofocus>
                <span class="text-danger"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required>
                <span class="text-danger"><?php echo $password_err; ?></span>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
</body>
</html>
