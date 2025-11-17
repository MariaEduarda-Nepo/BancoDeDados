<?php
require_once 'config.php';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO Veiculo (id_cliente, placa, marca, modelo, ano, cor) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['id_cliente'],
                strtoupper($_POST['placa']),
                $_POST['marca'],
                $_POST['modelo'],
                $_POST['ano'],
                $_POST['cor']
            ]);
            $success = "Veículo cadastrado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao cadastrar: " . $e->getMessage();
        }
    } elseif ($action === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE Veiculo SET id_cliente=?, placa=?, marca=?, modelo=?, ano=?, cor=? WHERE id_veiculo=?");
            $stmt->execute([
                $_POST['id_cliente'],
                strtoupper($_POST['placa']),
                $_POST['marca'],
                $_POST['modelo'],
                $_POST['ano'],
                $_POST['cor'],
                $_POST['id_veiculo']
            ]);
            $success = "Veículo atualizado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM Veiculo WHERE id_veiculo=?");
            $stmt->execute([$_POST['id_veiculo']]);
            $success = "Veículo excluído com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao excluir: Este veículo possui ordens de serviço.";
        }
    }
}

// Buscar veículos com dados do cliente
$veiculos = $pdo->query("
    SELECT v.*, c.nome as cliente_nome, c.telefone as cliente_telefone
    FROM Veiculo v
    INNER JOIN Cliente c ON v.id_cliente = c.id_cliente
    ORDER BY c.nome, v.placa
")->fetchAll();

// Buscar clientes para o formulário
$clientes = $pdo->query("SELECT * FROM Cliente ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Veículos</title>
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
            transform: translateY(-2px);
        }

        .btn-success {
            background: #43e97b;
            color: white;
            padding: 8px 16px;
        }

        .btn-danger {
            background: #ff6b6b;
            color: white;
            padding: 8px 16px;
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

        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        input:focus, select:focus {
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

        .actions {
            display: flex;
            gap: 10px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .placa {
            text-transform: uppercase;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-car"></i> Gestão de Veículos</h1>
            <div style="display: flex; gap: 10px;">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Veículo
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
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Marca/Modelo</th>
                        <th>Ano</th>
                        <th>Cor</th>
                        <th>Proprietário</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($veiculos as $veiculo): ?>
                    <tr>
                        <td><?php echo $veiculo['id_veiculo']; ?></td>
                        <td><span class="placa"><?php echo htmlspecialchars($veiculo['placa']); ?></span></td>
                        <td><?php echo htmlspecialchars($veiculo['marca'] . ' ' . $veiculo['modelo']); ?></td>
                        <td><?php echo htmlspecialchars($veiculo['ano']); ?></td>
                        <td><?php echo htmlspecialchars($veiculo['cor']); ?></td>
                        <td><?php echo htmlspecialchars($veiculo['cliente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($veiculo['cliente_telefone']); ?></td>
                        <td class="actions">
                            <button onclick='editVeiculo(<?php echo json_encode($veiculo); ?>)' class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Deseja excluir este veículo?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_veiculo" value="<?php echo $veiculo['id_veiculo']; ?>">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="veiculoModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Novo Veículo</h2>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id_veiculo" id="id_veiculo">
                
                <div class="form-group">
                    <label>Proprietário *</label>
                    <select name="id_cliente" id="id_cliente" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id_cliente']; ?>">
                                <?php echo htmlspecialchars($cliente['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Placa *</label>
                    <input type="text" name="placa" id="placa" maxlength="7" style="text-transform: uppercase;" required placeholder="ABC1234">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Marca *</label>
                        <input type="text" name="marca" id="marca" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Modelo *</label>
                        <input type="text" name="modelo" id="modelo" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Ano *</label>
                        <input type="number" name="ano" id="ano" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Cor *</label>
                        <input type="text" name="cor" id="cor" required>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" onclick="closeModal()" class="btn btn-danger">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('veiculoModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Novo Veículo';
            document.getElementById('formAction').value = 'add';
            document.getElementById('id_veiculo').value = '';
            document.querySelector('form').reset();
        }

        function closeModal() {
            document.getElementById('veiculoModal').classList.remove('active');
        }

        function editVeiculo(veiculo) {
            document.getElementById('veiculoModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Editar Veículo';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('id_veiculo').value = veiculo.id_veiculo;
            document.getElementById('id_cliente').value = veiculo.id_cliente;
            document.getElementById('placa').value = veiculo.placa;
            document.getElementById('marca').value = veiculo.marca;
            document.getElementById('modelo').value = veiculo.modelo;
            document.getElementById('ano').value = veiculo.ano;
            document.getElementById('cor').value = veiculo.cor;
        }

        window.onclick = function(event) {
            const modal = document.getElementById('veiculoModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>