<?php
$conn = new mysqli("localhost", "root", "senaisp", "livraria");
$result = $conn->query("SELECT * FROM Livros");

echo"<h2>Lista de Livros</h2>";
echo"<table border='1'>";
echo"<tr><th>Código</th><th>Nome</th><th>Email</th></tr>";

while ($row = $result->fetch_assoc()) {
echo"<tr>
     <td>{$row['Cod_Livro']}</td>
        <td>{$row['Nome_titulo']}</td>
        <td>{$row['Nome_autor']}</td>
        <td><a href='editar.php?Cod_Livro={$row['Cod_Livro']}'>Editar</a></td>
    </tr>";
}
echo"</table>";
$conn->close();
?>

<a hred="index1.html"><button type="button">Página Inicial</button></a>