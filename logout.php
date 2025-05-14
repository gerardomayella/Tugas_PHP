<?php
session_start();
session_unset();
session_destroy();

// Hapus cookie jika ada
if (isset($_COOKIE['id'])) {
    setcookie('id', '', time() - 3600);
}
if (isset($_COOKIE['key'])) {
    setcookie('key', '', time() - 3600);
}
header("Location: login.php");
exit;
?>