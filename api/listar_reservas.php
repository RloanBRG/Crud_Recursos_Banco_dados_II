<?php
//auxilio com IA
header('Content-Type: application/json; charset=utf-8');
require_once '../conexao.php';

$sql = "SELECT 
            r.id_reserva,
            u.nome AS usuario,
            rec.nome AS recurso,
            rec.tipo AS tipo_recurso,
            r.data_reserva,
            r.hora_inicio,
            r.hora_fim,
            r.status
        FROM reservas r
        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
        INNER JOIN recursos rec ON r.id_recurso = rec.id_recurso
        ORDER BY r.data_reserva DESC, r.hora_inicio ASC";

$resultado = $conexao->query($sql);
$reservas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $reservas[] = $linha;
    }
}

echo json_encode($reservas);
$conexao->close();
?>