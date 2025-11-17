<?php
require_once 'config.php';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO Servico (descricao, valor_servico) VALUES (?, ?)");
            $stmt->execute([
                $_POST['descricao'],
                $_POST['valor_servico']
            ]);
            $success = "Serviço cadastrado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao cadastrar: Este serviço já está cadastrado.";
        }
    } elseif ($action === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE Servico SET descricao=?, valor_servico=? WHERE id_servico=?");
            $stmt->execute([
                $_POST['descricao'],
                $_POST['valor_servico'],
                $_POST['id_servico']
            ]);
            $success = "Serviço atualizado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM Servico WHERE id_servico=?");
            $stmt->execute([$_POST['id_servico']]);
            $success = "Serviço excluído com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao excluir: Este serviço está vinculado a ordens de serviço.";
        }
    }
}

// Buscar serviços com estatísticas
$servicos = $pdo->query("
    SELECT s.*, 
           COUNT(DISTINCT os.id_os) as vezes_usado,
           COALESCE(SUM(osc.id_os), 0) as total_uso
    FROM Servico s
    LEFT JOIN OS_Contem_Servico osc ON s.id_servico = osc.id_servico
    LEFT JOIN Ordem_Servico os ON osc.id_os = os.id_os
    GROUP BY s.id_servico
    ORDER BY s.descricao
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Serviços</title>
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
            max-width: 1200px;
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

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .service-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px;
            border-radius: 15px;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .service-price {
            font-size: 28px;
            font-weight: bold;
            margin: 15px 0;
        }

        .service-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .service-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
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

        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tools"></i> Gestão de Serviços</h1>
            <div style="display: flex; gap: 10px;">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Serviço
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

        <div class="services-grid">
            <?php foreach ($servicos as $servico): ?>
            <div class="service-card">
                <h3>
                    <i class="fas fa-wrench"></i>
                    <?php echo htmlspecialchars($servico['descricao']); ?>
                </h3>
                
                <div class="service-price">
                    R$ <?php echo number_format($servico['valor_servico'], 2, ',', '.'); ?>
                </div>
                
                <div class="service-stats">
                    <div>
                        <small>Vezes usado:</small>
                        <div style="font-size: 20px; font-weight: bold;">
                            <?php echo $servico['vezes_usado']; ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <small>Receita gerada:</small>
                        <div style="font-size: 20px; font-weight: bold;">
                            R$ <?php echo number_format($servico['vezes_usado'] * $servico['valor_servico'], 2, ',', '.'); ?>
                        </div>
                    </div>
                </div>
                
                <div class="service-actions">
                    <button onclick='editServico(<?php echo json_encode($servico); ?>)' class="btn btn-success" style="flex: 1;">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <form method="POST" style="flex: 1;" onsubmit="return confirm('Deseja excluir este serviço?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id_servico" value="<?php echo $servico['id_servico']; ?>">
                        <button type="submit" class="btn btn-danger" style="width: 100%;">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="servicoModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Novo Serviço</h2>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id_servico" id="id_servico">
                
                <div class="form-group">
                    <label>Descrição *</label>
                    <input type="text" name="descricao" id="descricao" required placeholder="Ex: Troca de óleo">
                </div>
                
                <div class="form-group">
                    <label>Valor do Serviço *</label>
                    <input type="number" name="valor_servico" id="valor_servico" step="0.01" min="0" required placeholder="0.00">
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
            document.getElementById('servicoModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Novo Serviço';
            document.getElementById('formAction').value = 'add';
            document.getElementById('id_servico').value = '';
            document.querySelector('form').reset();
        }

        function closeModal() {
            document.getElementById('servicoModal').classList.remove('active');
        }

        function editServico(servico) {
            document.getElementById('servicoModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Editar Serviço';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('id_servico').value = servico.id_servico;
            document.getElementById('descricao').value = servico.descricao;
            document.getElementById('valor_servico').value = servico.valor_servico;
        }

        window.onclick = function(event) {
            const modal = document.getElementById('servicoModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>