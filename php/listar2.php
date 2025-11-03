<?php

$conn = new mysqli("localhost", "root", "senaisp", "LOJA");
$result = $conn->query("SELECT * FROM PRODUTOS");

echo"<h2>Lista de Produtos</h2>";
echo"<table border='1'>";
echo"<tr><th>Código</th><th>Nome</th><th>Preço</th></tr>";

while ($row = $result->fetch_assoc()) {
echo"<tr>
     <td>{$row['ID']}</td>
        <td>{$row['NOME']}</td>
        <td>{$row['PRECO']}</td>
        <a href='delete.php?id={$row['ID']}'>Deletar</a>
        <td><a href='editar.php?ID={$row['ID']}'>Editar</a></td>
    </tr>";
}
echo"</table>";
$conn->close();
?>

<a href="index2.html"><button type="button">Página Inicial</button></a>