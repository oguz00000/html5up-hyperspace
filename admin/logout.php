<?php
session_start();

// Tüm session verilerini temizle
$_SESSION = array();

// Session'ı sonlandır
session_destroy();

// Login sayfasına yönlendir
header('Location: login.php');
exit;
?>


