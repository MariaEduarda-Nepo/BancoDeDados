<?php
$conn = new mysqli("localhost", "root", "senaisp", "LOJA");

if ($conn->connect_error) {
    die('Conexão falhou: ' . $conn->connect_error);
}

$id = isset($_GET['ID']) ? (int) $_GET['ID'] : 0;
if ($id <= 0) {
    die('ID inválido.');
}

$stmt = $conn->prepare("SELECT * FROM PRODUTOS WHERE ID = ?");
if (!$stmt) {
    die('Erro ao preparar consulta: ' . $conn->error);
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    die('Registro não encontrado.');
}
?>

<form action="atualizar2.php" method="POST">
    <input type="hidden" name="ID" value="<?php echo $row['ID']; ?>">
    Nome: <input type="text" name="NOME" value="<?php echo htmlspecialchars($row['NOME'] ?? '', ENT_QUOTES); ?>"><br>
    Preço: <input type="text" name="PRECO" value="<?php echo htmlspecialchars($row['PRECO'] ?? '', ENT_QUOTES); ?>"><br>
    <input type="submit" value="Atualizar">
</form>

<?php $stmt->close(); $conn->close(); ?>
