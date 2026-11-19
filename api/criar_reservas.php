<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario   = $_POST['id_usuario'];
    $id_recurso   = $_POST['id_recurso'];
    $data_reserva = $_POST['data_reserva'];
    $hora_inicio  = $_POST['hora_inicio'];
    $hora_fim     = $_POST['hora_fim'];
    $observacao   = $_POST['observacao'] ?? '';

    $sql = "INSERT INTO reservas (id_usuario, id_recurso, data_reserva, hora_inicio, hora_fim, observacao) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iissss", $id_usuario, $id_recurso, $data_reserva, $hora_inicio, $hora_fim, $observacao);

    if ($stmt->execute()) {
        header("Location: ../html/dashboard.html?sucesso=1");
    } else {
        echo "Erro ao cadastrar reserva: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
}
?>