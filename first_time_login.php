<?php
session_start();

// Include database configuration
require_once "./data/config.php";

// Get the patient ID from the session
$patient_id = $_SESSION["patient_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $age = trim($_POST["age"]);
    $contact = trim($_POST["contact"]);
    $address = trim($_POST["address"]);
    $gender = trim($_POST["gender"]);

    // Validation
    if (empty($age) || empty($contact) || empty($address) || empty($gender)) {
        // Error handling
        $error = "Please fill in all fields.";
    } else {
        // Update the user's details and set `first_time_login` to false
        $sql = "UPDATE patients SET age = :age, contact = :contact, address = :address, gender = :gender, first_time_login = 0 WHERE id = :id";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":age", $age, PDO::PARAM_INT);
            $stmt->bindParam(":contact", $contact, PDO::PARAM_STR);
            $stmt->bindParam(":address", $address, PDO::PARAM_STR);
            $stmt->bindParam(":gender", $gender, PDO::PARAM_STR);
            $stmt->bindParam(":id", $patient_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Redirect to the patient dashboard
                header("location: patient_dashboard.php");
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        }
        unset($stmt);
    }
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>First Time Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="text-center">Complete Your Profile</h2>
    <form action="first_time_login.php" method="post">
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" id="age" name="age" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" class="form-control" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Submit</button>
    </form>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
    <?php endif; ?>
</div>
</body>
</html>
