<?php
$conn = new mysqli("localhost", "root", "senaisp", "LOJA");
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

$cod = isset($_POST['ID']) ? (int) $_POST['ID'] : 0;
if ($cod <= 0) {
    die('Código inválido.');
}

$nome = $_POST['NOME'] ?? '';
$id = $_POST['ID'] ?? '';
$preco = $_POST['PRECO'] ?? '';


$stmt = $conn->prepare("UPDATE cliente SET NOME = ?, ID = ?, PRECO = ? WHERE ID = ?");
if (!$stmt) {
    die('Erro ao preparar statement: ' . $conn->error);
}

$stmt->bind_param('ssssi', $nome, $id, $preco, $cod);
if (!$stmt->execute()) {
    die('Erro ao atualizar: ' . $stmt->error);
}

$stmt->close();
$conn->close();

header('Location: listar2.php');
exit;

?>
