
<?php
session_start();

$_SESSION = array();

session_destroy();

header("Location: nurse_login.php");
exit;
?>
