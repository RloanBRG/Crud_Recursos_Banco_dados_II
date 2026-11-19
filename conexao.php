<?php
$host     = 'mariadb'; // Nome do servico configurado no docker-compose
$usuario  = 'usuario_reservas';
$senha    = 'Reserva@123';
$banco    = 'sistema_reservas';

// Criando a conexao via MySQLi
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verificando se houve erro
if ($conexao->connect_error) {
    die("Erro de Conexão com o MariaDB: " . $conexao->connect_error);
}

?>