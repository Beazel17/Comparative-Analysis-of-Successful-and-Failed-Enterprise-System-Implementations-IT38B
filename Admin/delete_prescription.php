<?php
require_once '../data/config.php';

// Check if the ID is passed in the URL
if (isset($_GET['id'])) {
    $prescription_id = $_GET['id'];

    try {
        // Delete the prescription from the database
        $stmt = $pdo->prepare("DELETE FROM prescriptions WHERE id = ?");
        $stmt->execute([$prescription_id]);

        // Redirect back to the prescriptions page with a success message
        header("Location: prescriptions.php?message=Prescription%20deleted%20successfully");
        exit;
    } catch (Exception $e) {
        // Redirect back with an error message
        header("Location: prescriptions.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: prescriptions.php");
    exit;
}
