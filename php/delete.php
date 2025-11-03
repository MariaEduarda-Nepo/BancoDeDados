<?php

$conn = new mysqli("localhost", "root", "senaisp","livraria");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

//prepara a declaração
$stmt = $conn->prepare("DELETE FROM livros WHERE id = ?");
//vincula o parâmetro 'id' como um inteiro (i)
$stmt->bind_param("i", $id);


//executar e verificar
if ($stmt->execute()) {
    echo "Usuário deletado com sucesso.";
} else {
    echo "Erro ao deletar: " . $stmt->error;
}
echo "<br><a href='listar.php'/index.php'>Voltar a lista</a>";

$stmt->close();
$conn->close();;
?>

