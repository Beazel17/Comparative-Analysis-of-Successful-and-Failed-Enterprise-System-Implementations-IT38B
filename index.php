<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Medicare System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .landing-page {
            text-align: center;
            color: white;
            margin-top: 60px;
        }
        .landing-page h1 {
            font-size: 48px;
            font-weight: bold;
        }
        .landing-page p {
            font-size: 18px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Medicare System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="./Patient/patient_login.php">Patient</a></li>
                <li class="nav-item"><a class="nav-link" href="./nurse/nurse_login.php">Nurse</a></li>
                <li class="nav-item"><a class="nav-link" href="./admin/admin_login.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="landing-page">
    <h1>Welcome to Medicare!</h1>
    <p>Your health, our priority.</p>
</div>
</body>
</html>