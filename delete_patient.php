<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "data/config.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
    $stmt->bindParam(":id", $id);

    if ($stmt->execute()) {
        header("Location: edit_delete_patient.php?deleted=true");
        exit;
    } else {
        echo "Error deleting record.";
    }
} else {
    echo "No ID provided.";
}
?>
