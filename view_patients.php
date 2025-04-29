<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "data/config.php";

$search = "";
if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
    $sql = "SELECT * FROM patients WHERE surname LIKE :search OR first_name LIKE :search ORDER BY surname";
    $stmt = $pdo->prepare($sql);
    $param_search = "%$search%";
    $stmt->bindParam(":search", $param_search, PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM patients ORDER BY surname";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Database</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #607d8b;
            color: white;
        }
        .table-container {
            background: white;
            color: black;
            padding: 30px;
            margin-top: 40px;
            border-radius: 10px;
        }
        h2 {
            font-family: sans-serif;
            font-weight: bold;
            color: #3f51b5;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .btn-close {
            background-color: #b0bec5;
            color: black;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mt-4">Patient Database</h2>

    <div class="table-container">
        <form method="GET" class="form-inline search-bar">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search patients by name" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Surname</th>
                    <th>First Name</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($patients) > 0): ?>
                    <?php foreach ($patients as $index => $patient): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($patient["surname"]); ?></td>
                            <td><?php echo htmlspecialchars($patient["first_name"]); ?></td>
                            <td><?php echo htmlspecialchars($patient["gender"]); ?></td>
                            <td><?php echo htmlspecialchars($patient["dob"]); ?></td>
                            <td><?php echo htmlspecialchars($patient["phone"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No patients found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <button class="btn btn-close" onclick="window.location.href='dashboard.php'">CLOSE</button>
        </div>
    </div>
</div>
</body>
</html>
