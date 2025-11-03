<?php
$CODIGO = $_POST['codigo'];
$UNIDADE = $_POST['unidade'];
$DESCRICAO = $_POST['descricao'];
$VALOR_UNIT = $_POST['valor_unit'];

$conn = new mysqli("localhost", "root", "senaisp", "remoterc");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$sql = "INSERT INTO produto (CODIGO, UNIDADE, DESCRICAO, VALOR_UNIT) VALUES ('$CODIGO', '$UNIDADE', '$DESCRICAO', '$VALOR_UNIT')";
if ($conn->query($sql) === TRUE) {
    echo "Dados salvos com sucesso!";
} else {
    echo "Erro" . $conn->error;
}
// header("Location: index1.html");
$conn->close();
exit;
?>
