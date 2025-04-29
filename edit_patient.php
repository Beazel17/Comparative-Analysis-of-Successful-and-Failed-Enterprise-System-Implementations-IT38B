<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "medicare");

// Fetch patient data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM patients WHERE id=$id");

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        echo "Patient not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Update patient
if (isset($_POST['update'])) {
    $surname = $_POST['surname'];
    $first_name = $_POST['first_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];

    $updateQuery = "UPDATE patients SET 
        surname='$surname', 
        first_name='$first_name', 
        gender='$gender', 
        dob='$dob', 
        phone='$phone' 
        WHERE id=$id";

    if ($conn->query($updateQuery)) {
        header("Location: edit_delete_patient.php");
        exit();
    } else {
        echo "Update failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Patient</title>
    <style>
        body {
            background-color: #0b0b87;
            font-family: Arial, sans-serif;
            color: white;
            padding-top: 50px;
        }

        .form-container {
            background-color: white;
            color: black;
            width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            background-color: #0b7dda;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #095ca1;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ddd;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Edit Patient</h2>
        <form method="POST" action="edit_patient.php?id=<?= $id ?>">
            <label>Surname:</label>
            <input type="text" name="surname" value="<?= $patient['surname'] ?? '' ?>" required>

            <label>First Name:</label>
            <input type="text" name="first_name" value="<?= $patient['first_name'] ?? '' ?>" required>

            <label>Gender:</label>
            <select name="gender" required>
                <option value="Male" <?= ($patient['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($patient['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= ($patient['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
            </select>

            <label>Date of Birth:</label>
            <input type="date" name="dob" value="<?= $patient['dob'] ?? '' ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= $patient['phone'] ?? '' ?>" required>

            <button class="btn" type="submit" name="update">Update Patient</button>
        </form>

        <a class="back-link" href="edit_delete_patient.php">← Back to Edit/Delete</a>
    </div>

</body>
</html>
