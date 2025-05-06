<?php
require_once "./data/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO appointments (user_id, name, age, barangay, city, illness, contact_number) 
            VALUES (:user_id, :name, :age, :barangay, :city, :illness, :contact)";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);
        $stmt->bindParam(":name", $_POST["name"]);
        $stmt->bindParam(":age", $_POST["age"]);
        $stmt->bindParam(":barangay", $_POST["barangay"]);
        $stmt->bindParam(":city", $_POST["city"]);
        $stmt->bindParam(":illness", $_POST["illness"]);
        $stmt->bindParam(":contact", $_POST["contact_number"]);

        if ($stmt->execute()) {
            header("location: view.php");
            exit;
        } else {
            echo "Something went wrong. Try again.";
        }
    }
    unset($stmt);
}
unset($pdo);
?>
