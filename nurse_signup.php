<?php
session_start();
require_once "./data/config.php";

$email = $password = $confirm_password = $full_name = "";
$email_err = $password_err = $confirm_password_err = $full_name_err = "";

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate full name
    if (empty(trim($_POST["full_name"]))) {
        $full_name_err = "Please enter your full name.";
    } else {
        $full_name = trim($_POST["full_name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $sql = "SELECT id FROM nurses WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "This email is already registered.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check for errors before inserting into database
    if (empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($full_name_err)) {
        $sql = "INSERT INTO nurses (full_name, email, password) VALUES (:full_name, :email, :password)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":full_name", $param_full_name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

            $param_full_name = $full_name;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute()) {
                // Redirect to login page
                header("location: nurse_login.php");
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
    <title>Nurse Signup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Nurse Sign Up</h2>
        <form action="nurse_signup.php" method="post">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo $full_name; ?>" required>
                <span class="text-danger"><?php echo $full_name_err; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                <span class="text-danger"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <span class="text-danger"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <span class="text-danger"><?php echo $confirm_password_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
        </form>
        <p class="text-center"><a href="nurse_login.php">Already have an account? Log In</a></p>
    </div>
</body>
</html>
