create database Livraria;
use Livraria;

select database();

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


