<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Connect to the database
require_once "data/config.php";

$surname = $first_name = $gender = $dob = $phone = "";
$surname_err = $first_name_err = $gender_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate surname
    if(empty(trim($_POST["surname"]))){
        $surname_err = "Please enter the surname.";
    } else{
        $surname = trim($_POST["surname"]);
    }

    // Validate first name
    if(empty(trim($_POST["first_name"]))){
        $first_name_err = "Please enter the first name.";
    } else{
        $first_name = trim($_POST["first_name"]);
    }

    // Validate gender
    if(empty(trim($_POST["gender"]))){
        $gender_err = "Please select gender.";
    } else{
        $gender = trim($_POST["gender"]);
    }

    // Other fields
    $dob = !empty($_POST["dob"]) ? $_POST["dob"] : NULL;
    $phone = !empty($_POST["phone"]) ? trim($_POST["phone"]) : NULL;

    if(empty($surname_err) && empty($first_name_err) && empty($gender_err)){
        $sql = "INSERT INTO patients (surname, first_name, gender, dob, phone) VALUES (:surname, :first_name, :gender, :dob, :phone)";

        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":surname", $surname, PDO::PARAM_STR);
            $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
            $stmt->bindParam(":gender", $gender, PDO::PARAM_STR);
            $stmt->bindParam(":dob", $dob, PDO::PARAM_STR);
            $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);

            if($stmt->execute()){
                header("location: view_patients.php");
                exit();
            } else{
                echo "Something went wrong. Please try again.";
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
    <title>Add Patient</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #607d8b;
            color: white;
        }
        .form-container {
            background: white;
            color: black;
            padding: 30px;
            border-radius: 10px;
            margin-top: 50px;
        }
        .buttons {
            margin-top: 20px;
            text-align: center;
        }
        .buttons button {
            margin: 10px;
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
        }
        .btn-add {
            background-color: #00e5ff;
        }
        .btn-close {
            background-color: #b0bec5;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mt-4">Patient Details, Add New</h2>
    <div class="form-container mx-auto col-md-8">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Surname *</label>
                <input type="text" name="surname" class="form-control <?php echo (!empty($surname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $surname; ?>">
                <span class="invalid-feedback"><?php echo $surname_err; ?></span>
            </div>
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $first_name; ?>">
                <span class="invalid-feedback"><?php echo $first_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Gender *</label>
                <select name="gender" class="form-control <?php echo (!empty($gender_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Please select</option>
                    <option value="Male" <?php if($gender == "Male") echo "selected"; ?>>Male</option>
                    <option value="Female" <?php if($gender == "Female") echo "selected"; ?>>Female</option>
                </select>
                <span class="invalid-feedback"><?php echo $gender_err; ?></span>
            </div>
            <div class="form-group">
                <label>Date of Birth (DOB)</label>
                <input type="date" name="dob" class="form-control" value="<?php echo $dob; ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
            </div>

            <div class="buttons">
                <button type="submit" class="btn btn-add">ADD</button>
                <button type="button" class="btn btn-close" onclick="window.location.href='dashboard.php'">CLOSE</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
