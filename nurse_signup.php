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
                echo "<div class='alert alert-danger'>Oops! Something went wrong. Please try again later.</div>";
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
        if (empty($password_err) && ($password !== $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check for errors before inserting into database
    if (empty($full_name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO nurses (full_name, email, password) VALUES (:full_name, :email, :password)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":full_name", $param_full_name, PDO::PARAM_STR);
            $stmt->bindParam(":email",    $param_email,     PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password,  PDO::PARAM_STR);

            $param_full_name = $full_name;
            $param_email     = $email;
            $param_password  = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute()) {
                // Redirect to login page
                header("location: nurse_login.php");
                exit;
            } else {
                echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
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
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signup-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
        }
        .btn-primary {
            background: #5D9CEC;
            border: none;
        }
        .btn-primary:hover {
            background: #4A89DC;
        }
        .form-error {
            font-size: 0.875rem;
            color: #e74c3c;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <h2 class="text-center mb-4">Nurse Signup</h2>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                class="form-control <?= !empty($full_name_err) ? 'is-invalid' : ''; ?>"
                value="<?= htmlentities($full_name); ?>"
                required
            >
            <?php if (!empty($full_name_err)): ?>
                <div class="invalid-feedback"><?= $full_name_err; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control <?= !empty($email_err) ? 'is-invalid' : ''; ?>"
                value="<?= htmlentities($email); ?>"
                required
            >
            <?php if (!empty($email_err)): ?>
                <div class="invalid-feedback"><?= $email_err; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control <?= !empty($password_err) ? 'is-invalid' : ''; ?>"
                required
            >
            <?php if (!empty($password_err)): ?>
                <div class="invalid-feedback"><?= $password_err; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                class="form-control <?= !empty($confirm_password_err) ? 'is-invalid' : ''; ?>"
                required
            >
            <?php if (!empty($confirm_password_err)): ?>
                <div class="invalid-feedback"><?= $confirm_password_err; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
    </form>
    <p class="text-muted text-center mt-3">
        Already have an account? <a href="nurse_login.php">Log In</a>
    </p>
</div>
</body>
</html>
