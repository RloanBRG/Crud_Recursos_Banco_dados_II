<?php
session_start();

// Trava de segurança: obriga estar logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once file_exists('conexao.php') ? 'conexao.php' : '../conexao.php';

$mensagem_erro = '';
$mensagem_sucesso = '';

// Processar formulário de criação de recurso
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome'] ?? '');
    $tipo        = trim($_POST['tipo'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');
    $capacidade  = !empty($_POST['capacidade']) ? intval($_POST['capacidade']) : NULL;
    $localizacao = trim($_POST['localizacao'] ?? '');

    if (!empty($nome) && !empty($tipo)) {
        $ativo = 1;

        $stmt = $conexao->prepare("INSERT INTO recursos (nome, tipo, descricao, capacidade, localizacao, ativo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisi", $nome, $tipo, $descricao, $capacidade, $localizacao, $ativo);

        if ($stmt->execute()) {
            $mensagem_sucesso = "Recurso '" . htmlspecialchars($nome) . "' cadastrado com sucesso!";
        } else {
            $mensagem_erro = "Erro ao cadastrar recurso: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $mensagem_erro = "Preencha os campos obrigatórios (Nome e Tipo).";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Cadastrar Recurso - Sistema de Reservas</title>
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
                    <a class="px-space-md py-space-sm font-label-md text-label-md text-on-surface-variant hover:text-on-surface rounded-lg transition-colors" href="dashboard.php">Dashboard</a>
                    <a class="px-space-md py-space-sm bg-surface-container-high text-on-surface font-medium rounded-lg" href="cadastro_recurso.php">Novo Recurso</a>
                </nav>
            </div>
            <div class="flex items-center gap-space-md">
                <span class="font-body-sm text-body-sm text-on-surface-variant hidden sm:inline">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong></span>
                <a class="px-space-md py-space-sm font-label-md text-label-md text-error hover:bg-error-container/40 rounded-lg transition-colors" href="logout.php">Sair</a>
            </div>
        </div>
    </header>

    <main class="w-full pt-16 bg-surface">
        <div class="flex flex-col w-full">
            <div class="w-full flex items-center justify-center py-space-xl px-space-md sm:px-margin">
                <div class="w-full max-w-[540px] bg-surface-container-lowest rounded-xl shadow-sm p-space-lg sm:p-space-xl flex flex-col gap-space-lg relative overflow-hidden">

                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-primary via-primary-container to-secondary-container"></div>

                    <div class="flex flex-col gap-space-xs text-left">
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-primary tracking-widest uppercase">Gestão de Ativos</span>
                            <a href="dashboard.php" class="font-caption text-caption text-primary hover:underline">← Voltar ao Painel</a>
                        </div>
                        <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Cadastrar Novo Recurso</h1>
                        <p class="font-body-md text-body-md text-on-surface-variant">Adicione salas, laboratórios ou equipamentos à base do MariaDB.</p>
                    </div>

                    <!-- Feedbacks -->
                    <?php if (!empty($mensagem_erro)): ?>
                        <div class="p-3 rounded-lg bg-error-container text-error font-body-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            <span><?= htmlspecialchars($mensagem_erro) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($mensagem_sucesso)): ?>
                        <div class="p-3 rounded-lg bg-surface-container-high text-primary font-body-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            <span><?= htmlspecialchars($mensagem_sucesso) ?></span>
                        </div>
                    <?php endif; ?>

                    <form class="flex flex-col gap-space-md" action="cadastro_recurso.php" method="POST">

                        <!-- Nome do Recurso -->
                        <div class="flex flex-col gap-space-xs">
                            <label class="font-label-md text-label-md text-on-surface">Nome do Recurso *</label>
                            <input class="w-full h-[38px] px-3 bg-surface-container-low rounded-lg font-body-md text-on-surface focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all"
                                name="nome" placeholder="Ex: Laboratório de Redes 01 / Projetor Epson" required type="text">
                        </div>

                        <!-- Tipo (ENUM da Tabela) -->
                        <div class="flex flex-col gap-space-xs">
                            <label class="font-label-md text-label-md text-on-surface">Tipo de Recurso *</label>
                            <select name="tipo" class="w-full h-[38px] px-3 bg-surface-container-low rounded-lg font-body-md text-on-surface focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all" required>
                                <option value="">Selecione a categoria...</option>
                                <option value="sala">Sala de Reunião / Auditório</option>
                                <option value="laboratorio">Laboratório</option>
                                <option value="equipamento">Equipamento / Periférico</option>
                            </select>
                        </div>

                        <!-- Localização e Capacidade -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                            <div class="flex flex-col gap-space-xs">
                                <label class="font-label-md text-label-md text-on-surface">Localização</label>
                                <input class="w-full h-[38px] px-3 bg-surface-container-low rounded-lg font-body-md text-on-surface focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all"
                                    name="localizacao" placeholder="Ex: Bloco B - 2º Andar" type="text">
                            </div>
                            <div class="flex flex-col gap-space-xs">
                                <label class="font-label-md text-label-md text-on-surface">Capacidade (Pessoas)</label>
                                <input class="w-full h-[38px] px-3 bg-surface-container-low rounded-lg font-body-md text-on-surface focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all"
                                    name="capacidade" placeholder="Ex: 30" type="number" min="1">
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="flex flex-col gap-space-xs">
                            <label class="font-label-md text-label-md text-on-surface">Descrição / Especificações</label>
                            <textarea class="p-3 bg-surface-container-low rounded-lg font-body-md text-on-surface focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all resize-none"
                                name="descricao" rows="3" placeholder="Ex: Equipado com 20 computadores, ar-condicionado e projetor HD."></textarea>
                        </div>

                        <div class="pt-space-xs flex items-center justify-end gap-space-sm">
                            <a href="dashboard.php" class="px-space-md h-[40px] font-label-md text-on-surface-variant hover:text-on-surface rounded-lg flex items-center justify-center">Cancelar</a>
                            <button class="px-space-lg h-[40px] bg-primary text-on-primary font-label-md rounded-lg shadow-sm hover:bg-primary-container transition-colors duration-150 flex items-center justify-center gap-space-xs cursor-pointer" type="submit">
                                <span>Salvar Recurso</span>
                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </main>
</body>

</html>