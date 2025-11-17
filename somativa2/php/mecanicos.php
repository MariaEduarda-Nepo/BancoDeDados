<?php
require_once 'config.php';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO Mecanico (nome, especialidade, salario) VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['nome'],
                $_POST['especialidade'],
                $_POST['salario']
            ]);
            $success = "Mecânico cadastrado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao cadastrar: " . $e->getMessage();
        }
    } elseif ($action === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE Mecanico SET nome=?, especialidade=?, salario=? WHERE id_mecanico=?");
            $stmt->execute([
                $_POST['nome'],
                $_POST['especialidade'],
                $_POST['salario'],
                $_POST['id_mecanico']
            ]);
            $success = "Mecânico atualizado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM Mecanico WHERE id_mecanico=?");
            $stmt->execute([$_POST['id_mecanico']]);
            $success = "Mecânico excluído com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao excluir: Este mecânico está vinculado a ordens de serviço.";
        }
    }
}

// Buscar mecânicos com estatísticas
$mecanicos = $pdo->query("
    SELECT m.*, 
           COUNT(DISTINCT e.id_os) as total_os,
           COALESCE(SUM(os.valor_total), 0) as valor_total_os
    FROM Mecanico m
    LEFT JOIN Executa e ON m.id_mecanico = e.id_mecanico
    LEFT JOIN Ordem_Servico os ON e.id_os = os.id_os
    GROUP BY m.id_mecanico
    ORDER BY m.nome
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Mecânicos</title>
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
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #e3f2fd;
            color: #1976d2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-cog"></i> Gestão de Mecânicos</h1>
            <div style="display: flex; gap: 10px;">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Mecânico
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
                        <th>Nome</th>
                        <th>Especialidade</th>
                        <th>Salário</th>
                        <th>OS Realizadas</th>
                        <th>Valor Total Gerado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mecanicos as $mecanico): ?>
                    <tr>
                        <td><?php echo $mecanico['id_mecanico']; ?></td>
                        <td><?php echo htmlspecialchars($mecanico['nome']); ?></td>
                        <td>
                            <?php if ($mecanico['especialidade']): ?>
                                <span class="badge"><?php echo htmlspecialchars($mecanico['especialidade']); ?></span>
                            <?php else: ?>
                                <span style="color: #999;">Não informado</span>
                            <?php endif; ?>
                        </td>
                        <td>R$ <?php echo number_format($mecanico['salario'], 2, ',', '.'); ?></td>
                        <td><?php echo $mecanico['total_os']; ?></td>
                        <td>R$ <?php echo number_format($mecanico['valor_total_os'], 2, ',', '.'); ?></td>
                        <td class="actions">
                            <button onclick='editMecanico(<?php echo json_encode($mecanico); ?>)' class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Deseja excluir este mecânico?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_mecanico" value="<?php echo $mecanico['id_mecanico']; ?>">
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
    <div id="mecanicoModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Novo Mecânico</h2>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id_mecanico" id="id_mecanico">
                
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" id="nome" required>
                </div>
                
                <div class="form-group">
                    <label>Especialidade</label>
                    <select name="especialidade" id="especialidade">
                        <option value="">Selecione...</option>
                        <option value="Motor">Motor</option>
                        <option value="Suspensão">Suspensão</option>
                        <option value="Freios">Freios</option>
                        <option value="Elétrica">Elétrica</option>
                        <option value="Ar Condicionado">Ar Condicionado</option>
                        <option value="Injeção Eletrônica">Injeção Eletrônica</option>
                        <option value="Funilaria">Funilaria</option>
                        <option value="Pintura">Pintura</option>
                        <option value="Alinhamento">Alinhamento</option>
                        <option value="Mecânica Geral">Mecânica Geral</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Salário *</label>
                    <input type="number" name="salario" id="salario" step="0.01" min="0" required placeholder="0.00">
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeModal()" class="btn btn-danger">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('mecanicoModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Novo Mecânico';
            document.getElementById('formAction').value = 'add';
            document.getElementById('id_mecanico').value = '';
            document.querySelector('form').reset();
        }

        function closeModal() {
            document.getElementById('mecanicoModal').classList.remove('active');
        }

        function editMecanico(mecanico) {
            document.getElementById('mecanicoModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Editar Mecânico';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('id_mecanico').value = mecanico.id_mecanico;
            document.getElementById('nome').value = mecanico.nome;
            document.getElementById('especialidade').value = mecanico.especialidade || '';
            document.getElementById('salario').value = mecanico.salario;
        }

        window.onclick = function(event) {
            const modal = document.getElementById('mecanicoModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>