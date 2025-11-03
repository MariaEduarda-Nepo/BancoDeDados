<?php
$ID = $_POST['ID'];
$NOME = $_POST['NOME'];
$PRECO = $_POST['PRECO'];

$conn = new mysqli("localhost", "root", "senaisp", "LOJA");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$sql = "INSERT INTO produto (ID, NOME, PRECO) VALUES ('$ID', '$NOME', '$PRECO')";
if ($conn->query($sql) === TRUE) {
    echo "Dados salvos com sucesso!";
} else {
    echo "Erro" . $conn->error;
}
$conn->close();
exit;
?>
