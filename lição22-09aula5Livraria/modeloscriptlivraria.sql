create database Livraria;
use Livraria;



CREATE TABLE Livros (
genero varchar(100) not null,
quantidade int,
editora varchar(100) not null,
autor varchar(100) not null,
titulo varchar(100) not null,
preco decimal,
cod_livros int auto_increment PRIMARY KEY
);

CREATE TABLE Autores (
nome_autor varchar(100) not null,
nacionalidade varchar(100) not null,
cod_autor int auto_increment PRIMARY KEY,
data_de_nascimento date
);

CREATE TABLE Editoras (
telefone varchar(19) not null,
contato varchar(100)not null,
endereco varchar(100) not null,
cod_editora int auto_increment PRIMARY KEY,
nome_editora varchar(100) not null,
cnpj varchar(19) not null
);

CREATE TABLE Clientes (
email varchar(100) not null,
nome_cliente varchar(100) not null,
telefone varchar(19) not null,
data_de_nascimento date,
cpf varchar(14) not null,
cod_cliente int auto_increment PRIMARY KEY
);

CREATE TABLE Vendas (
cod_venda int auto_increment PRIMARY KEY,
valor_total decimal,
quantidade int,
data_venda datetime,
cod_livros int,
cod_cliente int,
FOREIGN KEY(cod_livros) REFERENCES Livros (cod_livros),
FOREIGN KEY(cod_cliente)REFERENCES Clientes (cod_cliente)
);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Ficção Científica', 150, 'Editora Z', 'Isaac Asimov', 'Fundação', 45);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Fantasia', 200, 'Editora Y', 'J.R.R. Tolkien', 'O Senhor dos Anéis', 69);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Romance', 180, 'Editora W', 'Jane Austen', 'Orgulho e Preconceito', 38);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Suspense', 120, 'Editora V', 'Stephen King', 'O Iluminado', 55);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('História', 90, 'Editora U', 'Yuval Noah Harari', 'Sapiens', 72);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Poesia', 75, 'Editora T', 'Carlos Drummond de Andrade', 'Alguma Poesia', 29);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Aventura', 110, 'Editora S', 'Júlio Verne', 'Vinte Mil Léguas Submarinas', 42);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Biografia', 85, 'Editora R', 'Walter Isaacson', 'Steve Jobs', 65);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Terror', 130, 'Editora Q', 'Edgar Allan Poe', 'Contos de Terror, Mistério e Morte', 48);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES ('Infantil', 250, 'Editora P', 'Monteiro Lobato', 'Sítio do Picapau Amarelo', 35);

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES ('J.K. Rowling', 'Britânica', '1965-07-31');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES ('Gabriel García Márquez', 'Colombiana', '1927-03-06');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES ('Machado de Assis', 'Brasileira', '1839-06-21');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES ('Agatha Christie', 'Britânica', '1890-09-15');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES ('Haruki Murakami', 'Japonesa', '1949-01-12');

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES ('(11) 98765-4321', 'contaeditoraalfacom', 'Rua das Letras, 123 - São Paulo', 'Editora Alfa', '01.234.567/0001-89');

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES ('(21) 99887-6655', 'contalivrosbetacom', 'Avenida dos Autores, 456 - Rio de Janeiro', 'Livros Beta Ltda', '12.345.678/0001-90');

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES ('(31) 97654-3210', 'faleeditora_gamacom', 'Travessa dos Saberes, 789 - Belo Horizonte', 'Editora Gama', '23.456.789/0001-01');

INSERT INTO Clientes (email, nome_cliente, telefone, data_de_nascimento, cpf) VALUES ('joao.silva@email.com', 'João da Silva', '(11) 91234-5678', '1985-04-10', '123.456.789-01');

INSERT INTO Clientes (email, nome_cliente, telefone, data_de_nascimento, cpf) VALUES ('maria.santos@email.com', 'Maria Santos', '(21) 99876-5432', '1992-11-25', '987.654.321-09');

-- consulta * todos os dados
SELECT * FROM Autores, Livros;
SELECT * FROM Livros;


-- consulta por campos
SELECT titulo FROM Livros;
SELECT nome_autor FROM Autores;

-- consulta por data com condição
SELECT titulo, autor FROM Livros
WHERE preco > 25;

-- consulta por crescente e descrecente
SELECT titulo,preco FROM Livros
ORDER BY preco DESC;

-- CONSULTA POR LIMITE DE RESULTADO
SELECT titulo FROM livros
LIMIT 5;

-- renomear colunas com as
SELECT titulo AS nome, autor AS escritor
FROM Livros;

-- funções agregadas

SELECT COUNT(*) AS nome
FROM Livros;

SELECT SUM(PRECO) AS nome
FROM Livros;



-- agrupamentos com group by
SELECT autor, COUNT(*) AS quantidade
FROM Livros
GROUP BY autor;

-- uso de and ou or
SELECT titulo, preco FROM Livros
WHERE titulo = "Steve Jobs" AND preco > 30;

-- condições extras com group by, having e 
-- order by
SELECT cod_livros, COUNT(*) AS nome
FROM Livros
GROUP BY cod_livros
HAVING nome > 5
ORDER BY nome DESC;

-- uso do like
SELECT titulo FROM Livros
WHERE titulo LIKE '%Steve Jobs%';

-- uso do like com inicio por letras
SELECT titulo FROM Livros
WHERE titulo LIKE '%S%';

-- uso do like com termino por letras
SELECT titulo FROM Livros
WHERE titulo LIKE '%bs%';

-- uso do like por quantidade de letras
SELECT titulo FROM Livros
WHERE titulo Like 'S___s';

-- combinando situações
SELECT titulo, preco FROM livros
WHERE titulo LIKE '%Steve Jobs%'
ORDER BY preco DESC;


-- INSERIR 5 CAMPOS EM CADA TABELA
-- TRAZER QUANTIDADE DE LIVROS
-- CONSULTAR LIVROS QUE COMEÇAM COM A LETRA A E PREÇO ACIMA DE 100
-- DEMONSTRAR A SOMA DOS LIVROS VENDIDOS
-- DEMONSTRAR A QUANTIDADE DOS LIVROS EM ESTOQUES
-- APAGAR O LIVRO COM CÓDIGO 5
-- ALTERAR A TABELA LIVROS E INSERIR A COLUNA ANO PUBLICAÇÃO COM TITULO DE DADOS DATE

-- iNSERTS lIVROS

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES 
('Biografia', 50, 'História Real', 'Walter Isaacson', 'Steve Jobs', 95);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES 
('Poesia', 30, 'Versos Livres', 'Fernando Pessoa', 'Mensagem', 32);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES 
('Infantil', 250, 'Alegria de Ler', 'Ziraldo', 'O Menino Maluquinho', 29);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES 
('Distopia', 110, 'Futuro Incerto', 'George Orwell', '1984', 59);

INSERT INTO Livros (genero, quantidade, editora, autor, titulo, preco) VALUES 
('Terror', 85, 'Noites Escuras', 'H.P. Lovecraft', 'A Sombra de Innsmouth', 65);

-- INSERTS AUTORES

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES 
('J.K. Rowling', 'Britânica', '1965-07-31');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES 
('Jorge Amado', 'Brasileira', '1912-08-10');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES 
('Stephen King', 'Americana', '1947-09-21');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES 
('Franz Kafka', 'Austríaca', '1883-07-03');

INSERT INTO Autores (nome_autor, nacionalidade, data_de_nascimento) VALUES 
('Agatha Christie', 'Britânica', '1890-09-15');

-- Inserts EDITORAS

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES 
('(11) 98765-4321', 'Ana Silva', 'Rua das Letras, 123', 'Editora Z', '12.345.678/0001-90');

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES 
('(21) 91234-5678', 'Pedro Costa', 'Avenida dos Livros, 456', 'Mundo dos Livros', '98.765.432/0001-12');

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES 
('(31) 95555-4444', 'Mariana Gomes', 'Praça da Cultura, 789', 'Mistério Total', '45.678.912/0001-34');

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES 
('(85) 96666-7777', 'Rafaela Lima', 'Travessa dos Autores, 101', 'Coração Editora', '78.912.345/0001-56');

INSERT INTO Editoras (telefone, contato, endereco, nome_editora, cnpj) VALUES 
('(61) 90000-1111', 'Fernando Santos', 'Rua da Sabedoria, 202', 'Saber Mais', '32.109.876/0001-78');

-- INSERTS CLIENTES

INSERT INTO Clientes (email, nome_cliente, telefone, data_de_nascimento, cpf) VALUES 
('fernanda@email.com', 'Fernanda Oliveira', '(19) 98765-4321', '1991-02-12', '678.901.234-56');

INSERT INTO Clientes (email, nome_cliente, telefone, data_de_nascimento, cpf) VALUES 
('ricardo@email.com', 'Ricardo Costa', '(12) 95432-1098', '1987-04-22', '789.012.345-67');

INSERT INTO Clientes (email, nome_cliente, telefone, data_de_nascimento, cpf) VALUES 
('patricia@email.com', 'Patricia Santos', '(13) 91122-3344', '1994-08-01', '890.123.456-78');

INSERT INTO Clientes (email, nome_cliente, telefone, data_de_nascimento, cpf) VALUES 
('marcelo@email.com', 'Marcelo Lima', '(14) 92233-4455', '1999-06-18', '901.234.567-89');

INSERT INTO Clientes (email, nome_cliente, telefone, data_de_nascimento, cpf) VALUES 
('julia@email.com', 'Julia Mendes', '(15) 93344-5566', '1983-10-05', '012.345.678-90');


-- INSERTS VENDAS

INSERT INTO Vendas (valor_total, quantidade, data_venda, cod_livros, cod_cliente) VALUES 
(110.00, 2, '2025-09-29 10:00:00', 1, 1);

INSERT INTO Vendas (valor_total, quantidade, data_venda, cod_livros, cod_cliente) VALUES 
(89.00, 1, '2025-09-28 14:30:00', 2, 2);

INSERT INTO Vendas (valor_total, quantidade, data_venda, cod_livros, cod_cliente) VALUES 
(205.00, 3, '2025-09-27 18:00:00', 3, 3);

INSERT INTO Vendas (valor_total, quantidade, data_venda, cod_livros, cod_cliente) VALUES 
(45.00, 1, '2025-09-26 09:45:00', 4, 4);

INSERT INTO Vendas (valor_total, quantidade, data_venda, cod_livros, cod_cliente) VALUES 
(159.00, 2, '2025-09-25 11:20:00', 5, 5);

-- contagem de livros
SELECT COUNT(*) AS total_de_livros FROM Livros;

-- CONSULTAR LIVROS QUE COMEÇAM COM A LETRA A E PREÇO ACIMA DE 100
SELECT * FROM Livros WHERE titulo LIKE 'A%' AND preco > 100;

-- DEMONSTRAR A SOMA DOS LIVROS VENDIDOS
SELECT SUM(quantidade) AS total_livros_vendidos FROM Vendas;

-- DEMONSTRAR A QUANTIDADE DOS LIVROS EM ESTOQUES
SELECT SUM(quantidade) AS total_livros_em_estoque FROM Livros;

-- APAGAR O LIVRO COM CÓDIGO 5
DELETE FROM Vendas WHERE cod_livros = 5;

-- ALTERAR A TABELA LIVROS E INSERIR A COLUNA ANO PUBLICAÇÃO COM TITULO DE DADOS DATE
ALTER TABLE Livros ADD COLUMN ano_publicacao DATE;











