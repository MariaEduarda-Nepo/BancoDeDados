<?php
require_once 'config.php';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO Cliente (nome, cpf_cnpj, telefone, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nome'],
                $_POST['cpf_cnpj'],
                $_POST['telefone'],
                $_POST['email']
            ]);
            $success = "Cliente cadastrado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao cadastrar: " . $e->getMessage();
        }
    } elseif ($action === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE Cliente SET nome=?, cpf_cnpj=?, telefone=?, email=? WHERE id_cliente=?");
            $stmt->execute([
                $_POST['nome'],
                $_POST['cpf_cnpj'],
                $_POST['telefone'],
                $_POST['email'],
                $_POST['id_cliente']
            ]);
            $success = "Cliente atualizado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM Cliente WHERE id_cliente=?");
            $stmt->execute([$_POST['id_cliente']]);
            $success = "Cliente excluído com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao excluir: Este cliente possui veículos cadastrados.";
        }
    }
}

// Buscar clientes
$clientes = $pdo->query("SELECT * FROM Cliente ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Clientes</title>
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
        }

        .btn-danger {
            background: #ff6b6b;
            color: white;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Gestão de Clientes</h1>
            <div style="display: flex; gap: 10px;">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Cliente
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
                        <th>CPF/CNPJ</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['id_cliente']; ?></td>
                        <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['cpf_cnpj']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                        <td class="actions">
                            <button onclick='editCliente(<?php echo json_encode($cliente); ?>)' class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Deseja excluir este cliente?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
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
    <div id="clienteModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Novo Cliente</h2>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id_cliente" id="id_cliente">
                
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" id="nome" required>
                </div>
                
                <div class="form-group">
                    <label>CPF/CNPJ</label>
                    <input type="text" name="cpf_cnpj" id="cpf_cnpj">
                </div>
                
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="telefone">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email">
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
            document.getElementById('clienteModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Novo Cliente';
            document.getElementById('formAction').value = 'add';
            document.getElementById('id_cliente').value = '';
            document.querySelector('form').reset();
        }

        function closeModal() {
            document.getElementById('clienteModal').classList.remove('active');
        }

        function editCliente(cliente) {
            document.getElementById('clienteModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Editar Cliente';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('id_cliente').value = cliente.id_cliente;
            document.getElementById('nome').value = cliente.nome;
            document.getElementById('cpf_cnpj').value = cliente.cpf_cnpj || '';
            document.getElementById('telefone').value = cliente.telefone || '';
            document.getElementById('email').value = cliente.email || '';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('clienteModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>