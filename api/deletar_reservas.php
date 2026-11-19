<?php
require_once '../conexao.php';

if (isset($_GET['id_reserva'])) {
    $id_reserva = $_GET['id_reserva'];

    $sql = "DELETE FROM reservas WHERE id_reserva = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_reserva);

    if ($stmt->execute()) {
        header("Location: ../html/dashboard.html?deletado=1");
    } else {
        echo "Erro ao remover reserva: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
}
?>