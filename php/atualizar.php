<?php
$conn = new mysqli("localhost", "root", "senaisp", "livraria");
if ($conn->connect_error) {
    die('Conexão falhou: ' . $conn->connect_error);
}

$cod = isset($_POST['Cod_Livro']) ? (int) $_POST['Cod_Livro'] : 0;
if ($cod <= 0) {
    die('Código inválido.');
}

$nome_titulo = $_POST['Nome_titulo'] ?? '';
$nome_autor = $_POST['Nome_autor'] ?? '';
$nome_editora = $_POST['Nome_editora'] ?? '';
$tipo_genero = $_POST['Tipo_genero'] ?? '';
$preco = $_POST['Preco'] ?? '';
$quantidade = isset($_POST['Quantidade']) ? (int) $_POST['Quantidade'] : 0;

$stmt = $conn->prepare("UPDATE Livros SET Nome_titulo = ?, Nome_autor = ?, Nome_editora = ?, Tipo_genero = ?, Preco = ?, Quantidade = ? WHERE Cod_Livro = ?");
if (!$stmt) {
    die('Erro ao preparar statement: ' . $conn->error);
}

$stmt->bind_param('sssssii', $nome_titulo, $nome_autor, $nome_editora, $tipo_genero, $preco, $quantidade, $cod);
if (!$stmt->execute()) {
    die('Erro ao executar atualização: ' . $stmt->error);
}

$stmt->close();
$conn->close();

// Redireciona de volta para a lista
header('Location: listar.php');
exit;
