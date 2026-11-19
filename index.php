<?php
session_start();

// Roteamento inteligente de entrada
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;