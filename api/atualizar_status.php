<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reserva = $_POST['id_reserva'];
    $novo_status = $_POST['status'];

    $sql = "UPDATE reservas SET status = ? WHERE id_reserva = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $novo_status, $id_reserva);

    if ($stmt->execute()) {
        header("Location: ../html/dashboard.html?atualizado=1");
    } else {
        echo "Erro ao atualizar reserva: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
}
?>