<?php
require_once 'config.php';

// Estatísticas do dashboard
$stats = [
    'clientes' => $pdo->query("SELECT COUNT(*) FROM Cliente")->fetchColumn(),
    'veiculos' => $pdo->query("SELECT COUNT(*) FROM Veiculo")->fetchColumn(),
    'os_abertas' => $pdo->query("SELECT COUNT(*) FROM Ordem_Servico WHERE status IN ('Aberta', 'Em Andamento')")->fetchColumn(),
    'os_total_mes' => $pdo->query("SELECT COALESCE(SUM(valor_total), 0) FROM Ordem_Servico WHERE MONTH(data_abertura) = MONTH(CURRENT_DATE())")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão de Oficina</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 40px;
            margin-bottom: 15px;
            display: block;
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2);
        }

        .menu-card i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #667eea;
        }

        .menu-card h3 {
            font-size: 18px;
            color: #333;
        }

        .color-1 { color: #667eea !important; }
        .color-2 { color: #f093fb !important; }
        .color-3 { color: #4facfe !important; }
        .color-4 { color: #43e97b !important; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-wrench"></i> Sistema de Gestão de Oficina Mecânica</h1>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users color-1"></i>
                <h3>Total de Clientes</h3>
                <div class="value"><?php echo $stats['clientes']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-car color-2"></i>
                <h3>Veículos Cadastrados</h3>
                <div class="value"><?php echo $stats['veiculos']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clipboard-list color-3"></i>
                <h3>Ordens em Andamento</h3>
                <div class="value"><?php echo $stats['os_abertas']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign color-4"></i>
                <h3>Faturamento do Mês</h3>
                <div class="value">R$ <?php echo number_format($stats['os_total_mes'], 2, ',', '.'); ?></div>
            </div>
        </div>

        <div class="menu-grid">
            <a href="clientes.php" class="menu-card">
                <i class="fas fa-users"></i>
                <h3>Clientes</h3>
            </a>
            <a href="veiculos.php" class="menu-card">
                <i class="fas fa-car"></i>
                <h3>Veículos</h3>
            </a>
            <a href="ordens_servico.php" class="menu-card">
                <i class="fas fa-clipboard-list"></i>
                <h3>Ordens de Serviço</h3>
            </a>
            <a href="mecanicos.php" class="menu-card">
                <i class="fas fa-user-cog"></i>
                <h3>Mecânicos</h3>
            </a>
            <a href="servicos.php" class="menu-card">
                <i class="fas fa-tools"></i>
                <h3>Serviços</h3>
            </a>
            <a href="pecas.php" class="menu-card">
                <i class="fas fa-cog"></i>
                <h3>Peças</h3>
            </a>
        </div>
    </div>
</body>
</html>