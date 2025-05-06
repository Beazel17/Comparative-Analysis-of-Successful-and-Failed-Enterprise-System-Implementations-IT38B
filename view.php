<?php
require_once "./data/config.php";
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Appointments</title>
  <style>
    body { background: #4e8a8f; font-family: Arial; color: white; }
    table { width: 100%; background-color: #407b7b; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #333; padding: 10px; }
    th { background: #336b6b; }
    .back-btn { margin-top: 20px; }
  </style>
</head>
<body>
  <h2>MediCare - Your Appointments</h2>
  <table>
    <tr>
      <th>Full Name</th>
      <th>Barangay</th>
      <th>City</th>
      <th>Contact</th>
      <th>Illness</th>
    </tr>
    <?php
    $sql = "SELECT name, barangay, city, contact_number, illness FROM appointments WHERE user_id = :uid ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":uid", $_SESSION["id"], PDO::PARAM_INT);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["barangay"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["city"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["contact_number"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["illness"]) . "</td>";
        echo "</tr>";
    }
    ?>
  </table>
  <div class="back-btn">
    <a href="appointments.php"><button>Back</button></a>
  </div>
</body>
</html>
