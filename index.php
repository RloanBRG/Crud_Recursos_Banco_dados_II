<?php
session_start();

// Roteamento inteligente de entrada
if (isset($_SESSION['usuario_id'])) {
    header("Location: ./html/dashboard.php");
} else {
    header("Location: ./html/login.php");
}
exit;