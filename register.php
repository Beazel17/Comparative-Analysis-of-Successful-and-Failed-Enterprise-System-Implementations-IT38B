<?php
session_start();

// If already logged in, redirect to dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ./public/user/dashboard.php");
    exit;
}

require_once "./data/config.php";

$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate emil
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = :email";

        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

            $param_email = trim($_POST["email"]);

            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This email is already registered.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)){

        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";

        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            if($stmt->execute()){
                // Redirect to login page
                header("location: index.php");
            } else{
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
<title>Sign Up</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="./css/register.css">
</head>
<body>
<div class="wrapper">
<h2>Sign Up</h2>
<p>Please fill this form to create an account.</p>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
        <span class="invalid-feedback"><?php echo $email_err; ?></span>
    </div>    
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Register" style="color: black;">
    </div>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
   
</form>
</div>    
</body>
</html>