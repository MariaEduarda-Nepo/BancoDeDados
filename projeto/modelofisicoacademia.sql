-- Geração de Modelo físico
-- Sql ANSI 2003 - brModelo.



CREATE TABLE Professores (
nome_professor varchar(100),
agenda varchar(100),
CPF varchar(14),
salario decimal,
telefone varchar(19),
cod_professor int auto_increment PRIMARY KEY
)

CREATE TABLE Alunos (
endereco varchar(255) not null,
plano varchar(10) not null,
data_nascimento date,
nome_aluno varchar(100) not null,
cod_aluno int  auto_increment PRIMARY KEY,
telefone varchar(19),
CPF varchar(14)
)

CREATE TABLE Administracao (
nome_funcionario varchar(100) not null,
funcao Texto(1),
CPF varchar(14),
salario decimal,
cod_administracao int auto_increment PRIMARY KEY
)

CREATE TABLE Treino (
nome_treino varchar(100),
qtde_exercicios int,
professor_responsavel varchar(100),
cod_treino int auto_increment PRIMARY KEY
)

CREATE TABLE Ensina (
cod_professor int ,
cod_aluno int ,
FOREIGN KEY(cod_professor) REFERENCES Professores (cod_professor),
FOREIGN KEY(cod_aluno) REFERENCES Alunos (cod_aluno)
)

CREATE TABLE Passa (
cod_professor int ,
cod_treino int ,
FOREIGN KEY(cod_professor) REFERENCES Professores (cod_professor)/*falha: chave estrangeira*/
)

