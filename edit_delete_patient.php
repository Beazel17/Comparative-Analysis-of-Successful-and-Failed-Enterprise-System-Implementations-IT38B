<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "medicare");

// Delete logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM patients WHERE id=$id");
    header("Location: edit_delete_patient.php");
    exit();
}

// Fetch patients
$result = $conn->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit/Delete Patients</title>
    <style>
        body {
            background-color: #0b0b87;
            font-family: Arial, sans-serif;
            color: white;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            padding-top: 20px;
        }

        table {
            width: 90%;
            margin: 20px auto;
            background-color: white;
            color: black;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #333;
            color: white;
        }

        a.button {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .edit-btn {
            background-color: #17a2b8;
            color: white;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #ddd;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Edit/Delete Patients</h2>

    <table>
        <tr>
            <th>#</th>
            <th>Surname</th>
            <th>First Name</th>
            <th>Gender</th>
            <th>DOB</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            $i = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$i}</td>
                    <td>{$row['surname']}</td>
                    <td>{$row['first_name']}</td>
                    <td>{$row['gender']}</td>
                    <td>{$row['dob']}</td>
                    <td>{$row['phone']}</td>
                    <td>
                        <a class='button edit-btn' href='edit_patient.php?id={$row['id']}'>Edit</a>
                        <a class='button delete-btn' href='edit_delete_patient.php?delete={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this patient?')\">Delete</a>
                    </td>
                </tr>";
                $i++;
            }
        } else {
            echo "<tr><td colspan='7'>No patient records found.</td></tr>";
        }
        ?>
    </table>

    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
</body>
</html>
