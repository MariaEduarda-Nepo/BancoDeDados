<?php
require_once 'config.php';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO Peca (nome, preco_custo, preco_venda, quantidade_estoque) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nome'],
                $_POST['preco_custo'],
                $_POST['preco_venda'],
                $_POST['quantidade_estoque']
            ]);
            $success = "Peça cadastrada com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao cadastrar: Esta peça já está cadastrada.";
        }
    } elseif ($action === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE Peca SET nome=?, preco_custo=?, preco_venda=?, quantidade_estoque=? WHERE id_peca=?");
            $stmt->execute([
                $_POST['nome'],
                $_POST['preco_custo'],
                $_POST['preco_venda'],
                $_POST['quantidade_estoque'],
                $_POST['id_peca']
            ]);
            $success = "Peça atualizada com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM Peca WHERE id_peca=?");
            $stmt->execute([$_POST['id_peca']]);
            $success = "Peça excluída com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao excluir: Esta peça está vinculada a ordens de serviço.";
        }
    } elseif ($action === 'ajustar_estoque') {
        try {
            $stmt = $pdo->prepare("UPDATE Peca SET quantidade_estoque = quantidade_estoque + ? WHERE id_peca=?");
            $stmt->execute([
                $_POST['quantidade'],
                $_POST['id_peca']
            ]);
            $success = "Estoque ajustado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao ajustar estoque: " . $e->getMessage();
        }
    }
}

// Buscar peças com estatísticas
$pecas = $pdo->query("
    SELECT p.*, 
           COALESCE(SUM(osp.quantidade), 0) as total_usado,
           COUNT(DISTINCT osp.id_os) as vezes_usado
    FROM Peca p
    LEFT JOIN OS_Utiliza_Peca osp ON p.id_peca = osp.id_peca
    GROUP BY p.id_peca
    ORDER BY p.nome
")->fetchAll();

// Estatísticas gerais
$stats = [
    'total_pecas' => count($pecas),
    'valor_estoque' => $pdo->query("SELECT COALESCE(SUM(preco_custo * quantidade_estoque), 0) FROM Peca")->fetchColumn(),
    'estoque_baixo' => $pdo->query("SELECT COUNT(*) FROM Peca WHERE quantidade_estoque <= 5")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Peças</title>
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

        .btn-warning {
            background: #ffa726;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .stat-card i {
            font-size: 32px;
            margin-bottom: 10px;
            color: #667eea;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .stat-card .label {
            color: #666;
            font-size: 14px;
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

        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        input:focus {
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

        .estoque-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .estoque-ok {
            background: #d4edda;
            color: #155724;
        }

        .estoque-baixo {
            background: #fff3cd;
            color: #856404;
        }

        .estoque-zero {
            background: #f8d7da;
            color: #721c24;
        }

        .lucro {
            color: #28a745;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-cog"></i> Gestão de Peças e Estoque</h1>
            <div style="display: flex; gap: 10px;">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Peça
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

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-boxes"></i>
                <div class="value"><?php echo $stats['total_pecas']; ?></div>
                <div class="label">Total de Peças</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <div class="value">R$ <?php echo number_format($stats['valor_estoque'], 2, ',', '.'); ?></div>
                <div class="label">Valor em Estoque</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-exclamation-triangle" style="color: #ffa726;"></i>
                <div class="value"><?php echo $stats['estoque_baixo']; ?></div>
                <div class="label">Peças com Estoque Baixo</div>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço Custo</th>
                        <th>Preço Venda</th>
                        <th>Lucro</th>
                        <th>Estoque</th>
                        <th>Usado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pecas as $peca): 
                        $lucro = $peca['preco_venda'] - $peca['preco_custo'];
                        $lucro_percentual = ($peca['preco_custo'] > 0) ? ($lucro / $peca['preco_custo']) * 100 : 0;
                        
                        if ($peca['quantidade_estoque'] == 0) {
                            $badge_class = 'estoque-zero';
                            $badge_text = 'SEM ESTOQUE';
                        } elseif ($peca['quantidade_estoque'] <= 5) {
                            $badge_class = 'estoque-baixo';
                            $badge_text = 'ESTOQUE BAIXO';
                        } else {
                            $badge_class = 'estoque-ok';
                            $badge_text = 'OK';
                        }
                    ?>
                    <tr>
                        <td><?php echo $peca['id_peca']; ?></td>
                        <td><?php echo htmlspecialchars($peca['nome']); ?></td>
                        <td>R$ <?php echo number_format($peca['preco_custo'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($peca['preco_venda'], 2, ',', '.'); ?></td>
                        <td class="lucro">
                            R$ <?php echo number_format($lucro, 2, ',', '.'); ?>
                            (<?php echo number_format($lucro_percentual, 1); ?>%)
                        </td>
                        <td>
                            <span class="estoque-badge <?php echo $badge_class; ?>">
                                <?php echo $peca['quantidade_estoque']; ?> un - <?php echo $badge_text; ?>
                            </span>
                        </td>
                        <td><?php echo $peca['total_usado']; ?> vezes</td>
                        <td class="actions">
                            <button onclick='editPeca(<?php echo json_encode($peca); ?>)' class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick='ajustarEstoque(<?php echo $peca['id_peca']; ?>, "<?php echo addslashes($peca['nome']); ?>")' class="btn btn-warning">
                                <i class="fas fa-warehouse"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Deseja excluir esta peça?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_peca" value="<?php echo $peca['id_peca']; ?>">
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

    <!-- Modal Cadastro -->
    <div id="pecaModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Nova Peça</h2>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id_peca" id="id_peca">
                
                <div class="form-group">
                    <label>Nome da Peça *</label>
                    <input type="text" name="nome" id="nome" required placeholder="Ex: Filtro de óleo">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Preço de Custo *</label>
                        <input type="number" name="preco_custo" id="preco_custo" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Preço de Venda *</label>
                        <input type="number" name="preco_venda" id="preco_venda" step="0.01" min="0" required placeholder="0.00">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Quantidade em Estoque *</label>
                    <input type="number" name="quantidade_estoque" id="quantidade_estoque" min="0" required value="0">
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeModal()" class="btn btn-danger">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ajustar Estoque -->
    <div id="estoqueModal" class="modal">
        <div class="modal-content">
            <h2>Ajustar Estoque</h2>
            <p id="pecaNome" style="margin-bottom: 20px; color: #666;"></p>
            <form method="POST">
                <input type="hidden" name="action" value="ajustar_estoque">
                <input type="hidden" name="id_peca" id="estoque_id_peca">
                
                <div class="form-group">
                    <label>Quantidade (use + para adicionar ou - para remover) *</label>
                    <input type="number" name="quantidade" id="quantidade" required placeholder="Ex: 10 ou -5">
                    <small style="color: #666;">Use números positivos para adicionar ou negativos para remover</small>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" onclick="closeEstoqueModal()" class="btn btn-danger">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Ajustar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('pecaModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Nova Peça';
            document.getElementById('formAction').value = 'add';
            document.getElementById('id_peca').value = '';
            document.querySelector('#pecaModal form').reset();
        }

        function closeModal() {
            document.getElementById('pecaModal').classList.remove('active');
        }

        function editPeca(peca) {
            document.getElementById('pecaModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Editar Peça';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('id_peca').value = peca.id_peca;
            document.getElementById('nome').value = peca.nome;
            document.getElementById('preco_custo').value = peca.preco_custo;
            document.getElementById('preco_venda').value = peca.preco_venda;
            document.getElementById('quantidade_estoque').value = peca.quantidade_estoque;
        }

        function ajustarEstoque(id, nome) {
            document.getElementById('estoqueModal').classList.add('active');
            document.getElementById('estoque_id_peca').value = id;
            document.getElementById('pecaNome').textContent = 'Peça: ' + nome;
            document.getElementById('quantidade').value = '';
        }

        function closeEstoqueModal() {
            document.getElementById('estoqueModal').classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>