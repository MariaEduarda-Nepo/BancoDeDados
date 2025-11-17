<?php
require_once 'config.php';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("INSERT INTO Ordem_Servico (id_veiculo, data_abertura, status, observacoes) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['id_veiculo'],
                $_POST['data_abertura'],
                $_POST['status'],
                $_POST['observacoes']
            ]);
            
            $id_os = $pdo->lastInsertId();
            
            // Adicionar serviços
            if (!empty($_POST['servicos'])) {
                $stmt = $pdo->prepare("INSERT INTO OS_Contem_Servico (id_os, id_servico) VALUES (?, ?)");
                foreach ($_POST['servicos'] as $id_servico) {
                    $stmt->execute([$id_os, $id_servico]);
                }
            }
            
            // Adicionar mecânicos
            if (!empty($_POST['mecanicos'])) {
                $stmt = $pdo->prepare("INSERT INTO Executa (id_mecanico, id_os) VALUES (?, ?)");
                foreach ($_POST['mecanicos'] as $id_mecanico) {
                    $stmt->execute([$id_mecanico, $id_os]);
                }
            }
            
            // Calcular valor total
            $valor = $pdo->query("SELECT COALESCE(SUM(valor_servico), 0) FROM Servico s INNER JOIN OS_Contem_Servico os ON s.id_servico = os.id_servico WHERE os.id_os = $id_os")->fetchColumn();
            $pdo->exec("UPDATE Ordem_Servico SET valor_total = $valor WHERE id_os = $id_os");
            
            $pdo->commit();
            $success = "Ordem de serviço criada com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao criar OS: " . $e->getMessage();
        }
    } elseif ($action === 'update_status') {
        try {
            $stmt = $pdo->prepare("UPDATE Ordem_Servico SET status=?, data_conclusao=? WHERE id_os=?");
            $data_conclusao = ($_POST['status'] === 'Concluida') ? date('Y-m-d') : null;
            $stmt->execute([$_POST['status'], $data_conclusao, $_POST['id_os']]);
            $success = "Status atualizado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar: " . $e->getMessage();
        }
    }
}

// Buscar dados
$veiculos = $pdo->query("SELECT v.*, c.nome as cliente_nome FROM Veiculo v INNER JOIN Cliente c ON v.id_cliente = c.id_cliente ORDER BY c.nome")->fetchAll();
$servicos = $pdo->query("SELECT * FROM Servico ORDER BY descricao")->fetchAll();
$mecanicos = $pdo->query("SELECT * FROM Mecanico ORDER BY nome")->fetchAll();

$ordens = $pdo->query("
    SELECT os.*, v.placa, v.marca, v.modelo, c.nome as cliente_nome,
           GROUP_CONCAT(DISTINCT m.nome SEPARATOR ', ') as mecanicos
    FROM Ordem_Servico os
    INNER JOIN Veiculo v ON os.id_veiculo = v.id_veiculo
    INNER JOIN Cliente c ON v.id_cliente = c.id_cliente
    LEFT JOIN Executa e ON os.id_os = e.id_os
    LEFT JOIN Mecanico m ON e.id_mecanico = m.id_mecanico
    GROUP BY os.id_os
    ORDER BY os.data_abertura DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Serviço</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-back {
            background: #f093fb;
            color: white;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background: #f8f9fa;
            color: #667eea;
            font-weight: 600;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-Aberta {
            background: #fff3cd;
            color: #856404;
        }

        .status-Em.Andamento {
            background: #cce5ff;
            color: #004085;
        }

        .status-Concluida {
            background: #d4edda;
            color: #155724;
        }

        .status-Cancelada {
            background: #f8d7da;
            color: #721c24;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-clipboard-list"></i> Ordens de Serviço</h1>
            <div style="display: flex; gap: 10px;">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova OS
                </button>
                <a href="index.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Cliente</th>
                        <th>Veículo</th>
                        <th>Data Abertura</th>
                        <th>Status</th>
                        <th>Mecânicos</th>
                        <th>Valor Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordens as $ordem): ?>
                    <tr>
                        <td>#<?php echo $ordem['id_os']; ?></td>
                        <td><?php echo htmlspecialchars($ordem['cliente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($ordem['placa'] . ' - ' . $ordem['marca'] . ' ' . $ordem['modelo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($ordem['data_abertura'])); ?></td>
                        <td><span class="status-badge status-<?php echo $ordem['status']; ?>"><?php echo $ordem['status']; ?></span></td>
                        <td><?php echo htmlspecialchars($ordem['mecanicos'] ?: 'Nenhum'); ?></td>
                        <td>R$ <?php echo number_format($ordem['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <button onclick='updateStatus(<?php echo $ordem['id_os']; ?>)' class="btn btn-primary" style="padding: 8px 16px;">
                                <i class="fas fa-edit"></i> Status
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nova OS -->
    <div id="osModal" class="modal">
        <div class="modal-content">
            <h2>Nova Ordem de Serviço</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Veículo *</label>
                    <select name="id_veiculo" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($veiculos as $veiculo): ?>
                            <option value="<?php echo $veiculo['id_veiculo']; ?>">
                                <?php echo $veiculo['cliente_nome'] . ' - ' . $veiculo['placa'] . ' (' . $veiculo['marca'] . ' ' . $veiculo['modelo'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Data Abertura *</label>
                    <input type="date" name="data_abertura" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="Aberta">Aberta</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Aguardando Peça">Aguardando Peça</option>
                        <option value="Concluida">Concluída</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Serviços</label>
                    <div class="checkbox-group">
                        <?php foreach ($servicos as $servico): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="servicos[]" value="<?php echo $servico['id_servico']; ?>" id="serv_<?php echo $servico['id_servico']; ?>">
                                <label for="serv_<?php echo $servico['id_servico']; ?>" style="margin: 0;">
                                    <?php echo $servico['descricao']; ?> (R$ <?php echo number_format($servico['valor_servico'], 2, ',', '.'); ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mecânicos</label>
                    <div class="checkbox-group">
                        <?php foreach ($mecanicos as $mecanico): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="mecanicos[]" value="<?php echo $mecanico['id_mecanico']; ?>" id="mec_<?php echo $mecanico['id_mecanico']; ?>">
                                <label for="mec_<?php echo $mecanico['id_mecanico']; ?>" style="margin: 0;">
                                    <?php echo $mecanico['nome']; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Observações</label>
                    <textarea name="observacoes" rows="3"></textarea>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeModal()" class="btn" style="background: #ff6b6b; color: white;">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar OS</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Status -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h2>Atualizar Status</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id_os" id="status_os_id">
                
                <div class="form-group">
                    <label>Novo Status *</label>
                    <select name="status" required>
                        <option value="Aberta">Aberta</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Aguardando Peça">Aguardando Peça</option>
                        <option value="Concluida">Concluída</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeStatusModal()" class="btn" style="background: #ff6b6b; color: white;">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('osModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('osModal').classList.remove('active');
        }

        function updateStatus(id) {
            document.getElementById('statusModal').classList.add('active');
            document.getElementById('status_os_id').value = id;
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>