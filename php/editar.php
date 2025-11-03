<?php
$conn = new mysqli("localhost", "root", "senaisp", "livraria");

if ($conn->connect_error) {
    die('Conexão falhou: ' . $conn->connect_error);
}

$id = isset($_GET['Cod_Livro']) ? (int) $_GET['Cod_Livro'] : 0;
if ($id <= 0) {
    die('ID inválido.');
}

$result = $conn->query("SELECT * FROM Livros WHERE Cod_Livro = $id");
if (!$result) {
    die('Erro na consulta: ' . $conn->error);
}

$row = $result->fetch_assoc();
if (!$row) {
    die('Registro não encontrado.');
}
?>

<form action="atualizar.php" method="POST">
    <input type="hidden" name="Cod_Livro" value="<?php echo $row['Cod_Livro']; ?>">
    Título: <input type="text" name="Nome_titulo" value="<?php echo htmlspecialchars($row['Nome_titulo'] ?? '', ENT_QUOTES); ?>"><br>
    Autor: <input type="text" name="Nome_autor" value="<?php echo htmlspecialchars($row['Nome_autor'] ?? '', ENT_QUOTES); ?>"><br>
    Editora: <input type="text" name="Nome_editora" value="<?php echo htmlspecialchars($row['Nome_editora'] ?? '', ENT_QUOTES); ?>"><br>
    Gênero: <input type="text" name="Tipo_genero" value="<?php echo htmlspecialchars($row['Tipo_genero'] ?? '', ENT_QUOTES); ?>"><br>
    Preço: <input type="text" name="Preco" value="<?php echo htmlspecialchars($row['Preco'] ?? '', ENT_QUOTES); ?>"><br>
    Quantidade: <input type="number" name="Quantidade" value="<?php echo htmlspecialchars($row['Quantidade'] ?? '', ENT_QUOTES); ?>"><br>
    <input type="submit" value="Atualizar">
</form>

<?php $conn->close(); ?>


