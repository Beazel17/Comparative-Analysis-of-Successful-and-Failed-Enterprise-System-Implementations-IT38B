<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    require_once "./data/config.php";

    $sql = "SELECT role FROM users WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $_SESSION["id"], PDO::PARAM_INT);
        if ($stmt->execute()) {
            $role = $stmt->fetchColumn();
            if ($role === "admin") {
                header("location: admin_dashboard.php");
                exit;
            } elseif ($role === "nurse") {
                header("location: nurse_dashboard.php");
                exit;
            } elseif ($role === "user") {
                header("location: user_dashboard.php");
                exit;
            }
        }
    }
}

// Login logic
require_once "./data/config.php";

$email = $password = "";
$email_err = $password_err = $login_err = "";

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
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = $email;

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $email = $row["email"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;

                            header("location: index.php");
                            exit;
                        } else {
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid email or password.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Medicare System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Reset margins and paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            display: flex;
            flex-direction: column;
        }

        nav.navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 80px; /* offset for navbar */
        }

        .wrapper {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .wrapper h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }

        .wrapper p {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
            font-size: 14px;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #5D9CEC;
            box-shadow: 0 0 5px rgba(93, 156, 236, 0.5);
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            background: #5D9CEC;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #4A89DC;
        }

        .alert-danger {
            text-align: center;
            font-size: 14px;
            border-radius: 8px;
            padding: 10px;
        }

        p a {
            color: #4A89DC;
            font-weight: bold;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Medicare System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Patient</a></li>
                <li class="nav-item"><a class="nav-link" href="nurse_dashboard.php">Nurse</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<main>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

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
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p class="text-center">Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</main>

</body>
</html>
