<?php
session_start();

// Distruge sesiunea
session_unset();
session_destroy();

// Redirecționează la pagina de login
header("Location: proiect_login.php");
exit;
?>