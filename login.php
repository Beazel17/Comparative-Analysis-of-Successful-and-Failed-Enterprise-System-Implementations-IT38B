<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->execute([$username, $role]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php?page=dashboard&role=" . $role);
        exit;
    } else {
        echo '<div style="color: red; text-align: center;">Invalid credentials!</div>';
    }
}
