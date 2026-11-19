<?php
session_start();

// Trava de segurança: obriga login prévio
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once file_exists('conexao.php') ? 'conexao.php' : '../conexao.php';

// 1. Processar exclusão via GET (DELETE)
if (isset($_GET['deletar'])) {
    $id_del = intval($_GET['deletar']);
    $stmt = $conexao->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $stmt->bind_param("i", $id_del);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// 2. Processar alteração rápida de status via POST (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_atualizar_status'])) {
    $id_reserva  = intval($_POST['id_reserva']);
    $novo_status = $_POST['novo_status'];

    $stmt = $conexao->prepare("UPDATE reservas SET status = ? WHERE id_reserva = ?");
    $stmt->bind_param("si", $novo_status, $id_reserva);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// 3. Processar inserção via POST (INSERT - Modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_criar'])) {
    $id_usuario   = $_POST['id_usuario'];
    $id_recurso   = $_POST['id_recurso'];
    $data_reserva = $_POST['data_reserva'];
    $hora_inicio  = $_POST['hora_inicio'];
    $hora_fim     = $_POST['hora_fim'];
    $status       = $_POST['status'] ?? 'pendente';
    $observacao   = $_POST['observacao'] ?? '';

    $stmt = $conexao->prepare("INSERT INTO reservas (id_usuario, id_recurso, data_reserva, hora_inicio, hora_fim, status, observacao) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $id_usuario, $id_recurso, $data_reserva, $hora_inicio, $hora_fim, $status, $observacao);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// 4. Métricas para os Cards KPI
$total_reservas = $conexao->query("SELECT COUNT(*) as total FROM reservas")->fetch_assoc()['total'] ?? 0;
$pendentes      = $conexao->query("SELECT COUNT(*) as total FROM reservas WHERE status = 'pendente'")->fetch_assoc()['total'] ?? 0;
$confirmadas    = $conexao->query("SELECT COUNT(*) as total FROM reservas WHERE status = 'confirmada'")->fetch_assoc()['total'] ?? 0;
$recursos_atv   = $conexao->query("SELECT COUNT(*) as total FROM recursos WHERE ativo = TRUE")->fetch_assoc()['total'] ?? 0;

// 5. Consulta Principal (JOIN 3 Tabelas)
$sql_tabela = "SELECT 
            r.id_reserva,
            u.nome AS usuario,
            u.matricula,
            u.tipo_usuario,
            rec.nome AS recurso,
            rec.tipo AS tipo_recurso,
            rec.localizacao,
            r.data_reserva,
            r.hora_inicio,
            r.hora_fim,
            r.status,
            r.observacao
        FROM reservas r
        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
        INNER JOIN recursos rec ON r.id_recurso = rec.id_recurso
        ORDER BY r.id_reserva DESC";
$resultado_tabela = $conexao->query($sql_tabela);

// 6. Listas para o Form do Modal
$usuarios_list = $conexao->query("SELECT id_usuario, nome, matricula FROM usuarios WHERE ativo = TRUE ORDER BY nome ASC");
$recursos_list = $conexao->query("SELECT id_recurso, nome, tipo, localizacao FROM recursos WHERE ativo = TRUE ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Reserva de Recursos - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
    <style>
        @layer base {

            html,
            body {
                margin: 0;
                padding: 0;
            }

            body {
                overscroll-behavior: none;
            }
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            "darkMode": "class",
            "theme": {
                "extend": {
                    "colors": {
                        "on-secondary": "#ffffff",
                        "on-surface": "#0b1c30",
                        "outline-variant": "#c3c6d7",
                        "surface": "#f8f9ff",
                        "tertiary": "#943700",
                        "on-tertiary-fixed": "#360f00",
                        "tertiary-fixed-dim": "#ffb596",
                        "on-tertiary": "#ffffff",
                        "on-tertiary-container": "#ffede6",
                        "on-surface-variant": "#434655",
                        "secondary-fixed": "#d5e3fd",
                        "error": "#ba1a1a",
                        "surface-container-lowest": "#ffffff",
                        "inverse-primary": "#b4c5ff",
                        "surface-container-high": "#dce9ff",
                        "surface-container-highest": "#d3e4fe",
                        "on-primary-fixed-variant": "#003ea8",
                        "inverse-on-surface": "#eaf1ff",
                        "secondary-fixed-dim": "#b9c7e0",
                        "on-primary-fixed": "#00174b",
                        "surface-bright": "#f8f9ff",
                        "on-primary-container": "#eeefff",
                        "primary-container": "#2563eb",
                        "on-secondary-container": "#57657b",
                        "secondary-container": "#d5e3fd",
                        "on-error-container": "#93000a",
                        "on-secondary-fixed": "#0d1c2f",
                        "inverse-surface": "#213145",
                        "on-tertiary-fixed-variant": "#7d2d00",
                        "surface-container-low": "#eff4ff",
                        "on-primary": "#ffffff",
                        "surface-variant": "#d3e4fe",
                        "surface-container": "#e5eeff",
                        "primary-fixed": "#dbe1ff",
                        "secondary": "#515f74",
                        "error-container": "#ffdad6",
                        "on-background": "#0b1c30",
                        "background": "#f8f9ff",
                        "surface-dim": "#cbdbf5",
                        "on-secondary-fixed-variant": "#3a485c",
                        "outline": "#737686",
                        "surface-tint": "#0053db",
                        "tertiary-container": "#bc4800",
                        "on-error": "#ffffff",
                        "primary": "#004ac6",
                        "primary-fixed-dim": "#b4c5ff",
                        "tertiary-fixed": "#ffdbcd"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "gutter": "1.5rem",
                        "space-xs": "0.25rem",
                        "space-md": "1rem",
                        "space-lg": "1.5rem",
                        "gutter-mobile": "1rem",
                        "space-xl": "2.5rem",
                        "margin": "2rem",
                        "margin-mobile": "1rem",
                        "space-sm": "0.5rem"
                    },
                    "fontFamily": {
                        "caption": ["Inter"],
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "headline-md": ["Inter"],
                        "headline-xl": ["Inter"],
                        "body-sm": ["Inter"],
                        "headline-xl-mobile": ["Inter"],
                        "headline-lg": ["Inter"]
                    },
                    "fontSize": {
                        "caption": ["11px", {
                            "lineHeight": "14px",
                            "letterSpacing": "0.05em",
                            "fontWeight": "600"
                        }],
                        "body-lg": ["16px", {
                            "lineHeight": "24px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "400"
                        }],
                        "body-md": ["14px", {
                            "lineHeight": "20px",
                            "letterSpacing": "0em",
                            "fontWeight": "400"
                        }],
                        "label-md": ["14px", {
                            "lineHeight": "20px",
                            "letterSpacing": "-0.005em",
                            "fontWeight": "500"
                        }],
                        "label-sm": ["12px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.02em",
                            "fontWeight": "500"
                        }],
                        "headline-md": ["20px", {
                            "lineHeight": "28px",
                            "letterSpacing": "-0.015em",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["36px", {
                            "lineHeight": "44px",
                            "letterSpacing": "-0.025em",
                            "fontWeight": "600"
                        }],
                        "body-sm": ["12px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0em",
                            "fontWeight": "400"
                        }],
                        "headline-xl-mobile": ["28px", {
                            "lineHeight": "36px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "600"
                        }],
                        "headline-lg": ["24px", {
                            "lineHeight": "32px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "600"
                        }]
                    }
                }
            }
        };
    </script>
</head>

<body class="bg-surface font-body-md text-on-surface min-h-screen">
    <header class="fixed top-0 left-0 right-0 z-50 bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div class="h-16 max-w-7xl mx-auto px-margin flex items-center justify-between">
            <div class="flex items-center gap-space-xl">
                <span class="font-headline-md text-headline-md tracking-tight text-on-surface font-medium select-none">Reserva de Recursos</span>
                <nav class="hidden md:flex items-center gap-space-xs p-space-xs">
                    <a class="px-space-md py-space-sm bg-surface-container-high text-on-surface font-medium rounded-lg" href="dashboard.php">Dashboard</a>
                    <a class="px-space-md py-space-sm font-label-md text-label-md text-on-surface-variant hover:text-on-surface rounded-lg transition-colors" href="cadastro_recurso.php">+ Novo Recurso</a>
                </nav>
            </div>
            <div class="flex items-center gap-space-md">
                <span class="font-body-sm text-body-sm text-on-surface-variant hidden sm:inline">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong></span>
                <a class="px-space-md py-space-sm font-label-md text-label-md text-error hover:bg-error-container/40 rounded-lg transition-colors" href="logout.php">
                    Sair
                </a>
            </div>
        </div>
    </header>

    <main class="w-full pt-16 bg-surface">
        <div class="flex flex-col w-full">
            <div class="max-w-7xl mx-auto w-full px-margin py-space-xl flex flex-col gap-space-xl">

                <!-- Header Principal -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md">
                    <div class="flex flex-col gap-space-xs">
                        <div class="flex items-center gap-space-xs">
                            <span class="font-caption text-caption text-primary tracking-widest uppercase">MariaDB + PHP + Docker</span>
                            <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                            <span class="font-caption text-caption text-on-surface-variant">Ambiente Ativo</span>
                        </div>
                        <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Painel de Reservas</h1>
                        <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                            Visão em tempo real dos agendamentos e disponibilidade no banco de dados.
                        </p>
                    </div>
                    <div class="flex items-center gap-space-sm shrink-0">
                        <button class="inline-flex items-center gap-space-xs bg-primary text-on-primary hover:bg-primary-container font-label-md text-label-md px-space-md py-space-sm rounded-lg shadow-sm transition-all duration-150 active:scale-95 cursor-pointer" id="openModalBtn">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            <span>Nova Reserva</span>
                        </button>
                    </div>
                </div>

                <!-- Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-on-surface-variant uppercase tracking-wider">Total de Reservas</span>
                            <span class="p-1 rounded-lg bg-surface-container text-on-surface-variant">
                                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                            </span>
                        </div>
                        <div class="mt-space-md flex items-baseline justify-between">
                            <span class="font-headline-xl text-headline-xl text-on-surface"><?= $total_reservas ?></span>
                            <span class="font-caption text-caption text-primary">Registros</span>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-on-surface-variant uppercase tracking-wider">Pendentes</span>
                            <span class="p-1 rounded-lg bg-tertiary-fixed text-tertiary">
                                <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                            </span>
                        </div>
                        <div class="mt-space-md flex items-baseline justify-between">
                            <span class="font-headline-xl text-headline-xl text-on-surface"><?= $pendentes ?></span>
                            <span class="font-caption text-caption text-tertiary font-medium">Aguardando</span>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-on-surface-variant uppercase tracking-wider">Confirmadas</span>
                            <span class="p-1 rounded-lg bg-surface-container text-primary">
                                <span class="material-symbols-outlined text-[20px]">event_available</span>
                            </span>
                        </div>
                        <div class="mt-space-md flex items-baseline justify-between">
                            <span class="font-headline-xl text-headline-xl text-on-surface"><?= $confirmadas ?></span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Aprovadas</span>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-on-surface-variant uppercase tracking-wider">Recursos Ativos</span>
                            <span class="p-1 rounded-lg bg-surface-container text-secondary">
                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            </span>
                        </div>
                        <div class="mt-space-md flex items-baseline justify-between">
                            <span class="font-headline-xl text-headline-xl text-on-surface"><?= $recursos_atv ?></span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Disponíveis</span>
                        </div>
                    </div>
                </div>

                <!-- Tabela Dinâmica com UPDATE Rápido -->
                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-surface-container-low text-on-surface-variant">
                                    <th class="py-space-md px-space-lg font-caption text-caption uppercase tracking-wider">ID</th>
                                    <th class="py-space-md px-space-lg font-caption text-caption uppercase tracking-wider">Recurso</th>
                                    <th class="py-space-md px-space-lg font-caption text-caption uppercase tracking-wider">Solicitante</th>
                                    <th class="py-space-md px-space-lg font-caption text-caption uppercase tracking-wider">Data &amp; Horário</th>
                                    <th class="py-space-md px-space-lg font-caption text-caption uppercase tracking-wider">Observação</th>
                                    <th class="py-space-md px-space-lg font-caption text-caption uppercase tracking-wider">Status (Edição Rápida)</th>
                                    <th class="py-space-md px-space-lg font-caption text-caption uppercase tracking-wider text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="font-body-md text-body-md text-on-surface" id="reservationsTableBody">
                                <?php if ($resultado_tabela && $resultado_tabela->num_rows > 0): ?>
                                    <?php while ($r = $resultado_tabela->fetch_assoc()): ?>
                                        <tr class="hover:bg-surface-container-low transition-colors duration-100 group">
                                            <td class="py-space-md px-space-lg font-mono font-medium text-body-sm text-on-surface-variant">#<?= $r['id_reserva'] ?></td>
                                            <td class="py-space-md px-space-lg">
                                                <div class="flex items-center gap-space-sm">
                                                    <div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-primary shrink-0">
                                                        <span class="material-symbols-outlined text-[20px]">
                                                            <?= $r['tipo_recurso'] === 'sala' ? 'meeting_room' : ($r['tipo_recurso'] === 'laboratorio' ? 'science' : 'devices') ?>
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-col min-w-0">
                                                        <span class="font-label-md text-label-md text-on-surface font-semibold truncate"><?= htmlspecialchars($r['recurso']) ?></span>
                                                        <div class="flex items-center gap-1.5 mt-0.5">
                                                            <span class="font-caption text-caption px-1.5 py-0.5 rounded bg-surface-container text-on-surface-variant capitalize"><?= htmlspecialchars($r['tipo_recurso']) ?></span>
                                                            <span class="font-caption text-caption text-on-surface-variant truncate"><?= htmlspecialchars($r['localizacao'] ?? 'Geral') ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-space-md px-space-lg">
                                                <div class="flex flex-col">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-label-md text-label-md text-on-surface font-medium"><?= htmlspecialchars($r['usuario']) ?></span>
                                                        <span class="font-caption text-caption px-1.5 py-0.5 rounded bg-surface-container-high text-primary font-medium capitalize"><?= htmlspecialchars($r['tipo_usuario']) ?></span>
                                                    </div>
                                                    <span class="font-caption text-caption text-on-surface-variant">Matrícula: <?= htmlspecialchars($r['matricula'] ?? 'N/A') ?></span>
                                                </div>
                                            </td>
                                            <td class="py-space-md px-space-lg">
                                                <div class="flex flex-col">
                                                    <span class="font-body-sm text-body-sm font-medium"><?= date('d/m/Y', strtotime($r['data_reserva'])) ?></span>
                                                    <span class="font-caption text-caption text-on-surface-variant"><?= substr($r['hora_inicio'], 0, 5) ?> - <?= substr($r['hora_fim'], 0, 5) ?></span>
                                                </div>
                                            </td>
                                            <td class="py-space-md px-space-lg">
                                                <span class="font-body-sm text-body-sm text-on-surface-variant truncate max-w-xs block"><?= htmlspecialchars($r['observacao'] ?? '-') ?></span>
                                            </td>

                                            <!-- Coluna Status com UPDATE Instantâneo -->
                                            <td class="py-space-md px-space-lg">
                                                <form method="POST" action="dashboard.php" class="inline-block">
                                                    <input type="hidden" name="acao_atualizar_status" value="1">
                                                    <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">

                                                    <select name="novo_status" onchange="this.form.submit()"
                                                        class="h-[32px] px-2 text-caption font-label-sm rounded-lg bg-surface-container-low border border-outline-variant/40 text-on-surface focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer transition-all">
                                                        <option value="pendente" <?= $r['status'] === 'pendente' ? 'selected' : '' ?>>🟡 Pendente</option>
                                                        <option value="confirmada" <?= $r['status'] === 'confirmada' ? 'selected' : '' ?>>🟢 Confirmada</option>
                                                        <option value="concluida" <?= $r['status'] === 'concluida' ? 'selected' : '' ?>>🔵 Concluída</option>
                                                        <option value="cancelada" <?= $r['status'] === 'cancelada' ? 'selected' : '' ?>>🔴 Cancelada</option>
                                                        <option value="rejeitada" <?= $r['status'] === 'rejeitada' ? 'selected' : '' ?>>⚪ Rejeitada</option>
                                                    </select>
                                                </form>
                                            </td>

                                            <td class="py-space-md px-space-lg text-right">
                                                <a class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/40 transition-colors inline-block"
                                                    href="dashboard.php?deletar=<?= $r['id_reserva'] ?>"
                                                    onclick="return confirm('Deseja cancelar esta reserva?')">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="py-space-md px-space-lg text-center text-on-surface-variant">Nenhuma reserva encontrada no MariaDB.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-space-md bg-surface-container-lowest border-t border-surface-container-low flex items-center justify-between">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            Total de <strong><?= $total_reservas ?></strong> reserva(s) retornadas do banco de dados.
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal de Nova Reserva -->
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/40 backdrop-blur-sm hidden transition-opacity duration-200" id="reservationModal">
            <div class="bg-surface-container-lowest w-full max-w-lg mx-space-md rounded-xl shadow-xl flex flex-col overflow-hidden max-h-[921px]">
                <div class="p-space-lg flex items-center justify-between bg-surface-container-low">
                    <div class="flex flex-col">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Nova Reserva de Recurso</h2>
                        <span class="font-caption text-caption text-on-surface-variant">Alocação no MariaDB</span>
                    </div>
                    <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface cursor-pointer" id="closeModalBtn">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form class="p-space-lg flex flex-col gap-space-md overflow-y-auto" action="dashboard.php" method="POST">
                    <input type="hidden" name="acao_criar" value="1">

                    <div class="flex flex-col gap-space-xs">
                        <label class="font-caption text-caption uppercase tracking-wider text-on-surface-variant">Recurso Desejado</label>
                        <select name="id_recurso" class="h-[38px] px-space-md bg-surface-container-low text-on-surface font-body-md rounded-lg focus:outline-none" required>
                            <option value="">Selecione o recurso...</option>
                            <?php while ($rec = $recursos_list->fetch_assoc()): ?>
                                <option value="<?= $rec['id_recurso'] ?>">
                                    <?= htmlspecialchars($rec['nome']) ?> (<?= ucfirst($rec['tipo']) ?> - <?= htmlspecialchars($rec['localizacao'] ?? 'Geral') ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        <label class="font-caption text-caption uppercase tracking-wider text-on-surface-variant">Solicitante (Usuário)</label>
                        <select name="id_usuario" class="h-[38px] px-space-md bg-surface-container-low text-on-surface font-body-md rounded-lg focus:outline-none" required>
                            <option value="">Selecione o usuário...</option>
                            <?php while ($u = $usuarios_list->fetch_assoc()): ?>
                                <option value="<?= $u['id_usuario'] ?>">
                                    <?= htmlspecialchars($u['nome']) ?> (Matrícula: <?= htmlspecialchars($u['matricula'] ?? 'N/A') ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                        <div class="flex flex-col gap-space-xs">
                            <label class="font-caption text-caption uppercase tracking-wider text-on-surface-variant">Data da Reserva</label>
                            <input name="data_reserva" class="h-[38px] px-space-md bg-surface-container-low text-on-surface font-body-md rounded-lg focus:outline-none" type="date" required>
                        </div>
                        <div class="flex flex-col gap-space-xs">
                            <label class="font-caption text-caption uppercase tracking-wider text-on-surface-variant">Status Inicial</label>
                            <select name="status" class="h-[38px] px-space-md bg-surface-container-low text-on-surface font-body-md rounded-lg focus:outline-none" required>
                                <option value="pendente" selected>Pendente</option>
                                <option value="confirmada">Confirmada</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                        <div class="flex flex-col gap-space-xs">
                            <label class="font-caption text-caption uppercase tracking-wider text-on-surface-variant">Horário Início</label>
                            <input name="hora_inicio" class="h-[38px] px-space-md bg-surface-container-low text-on-surface font-body-md rounded-lg focus:outline-none" type="time" required>
                        </div>
                        <div class="flex flex-col gap-space-xs">
                            <label class="font-caption text-caption uppercase tracking-wider text-on-surface-variant">Horário Término</label>
                            <input name="hora_fim" class="h-[38px] px-space-md bg-surface-container-low text-on-surface font-body-md rounded-lg focus:outline-none" type="time" required>
                        </div>
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        <label class="font-caption text-caption uppercase tracking-wider text-on-surface-variant">Observações / Especificações</label>
                        <textarea name="observacao" class="p-space-md bg-surface-container-low text-on-surface font-body-md rounded-lg focus:outline-none resize-none" rows="3" placeholder="Insira detalhes adicionais sobre o agendamento..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-space-sm pt-space-sm">
                        <button class="px-space-md h-[38px] font-label-md text-on-surface-variant hover:text-on-surface rounded-lg cursor-pointer" id="cancelModalBtn" type="button">Cancelar</button>
                        <button class="px-space-lg h-[38px] bg-primary text-on-primary font-label-md rounded-lg hover:bg-primary-container shadow-sm cursor-pointer" type="submit">Salvar no Banco</button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    <script>
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const reservationModal = document.getElementById('reservationModal');

        function toggleModal(show) {
            if (show) {
                reservationModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                reservationModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        openModalBtn.addEventListener('click', () => toggleModal(true));
        closeModalBtn.addEventListener('click', () => toggleModal(false));
        cancelModalBtn.addEventListener('click', () => toggleModal(false));

        reservationModal.addEventListener('click', (e) => {
            if (e.target === reservationModal) toggleModal(false);
        });
    </script>
</body>

</html>