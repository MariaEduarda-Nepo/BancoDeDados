-- Criação do Banco
CREATE DATABASE Avaliacao;
USE Avaliacao;

-- Tabela Fornecedor
CREATE TABLE Fornecedor (
    Fcodigo INT PRIMARY KEY,
    Fnome VARCHAR(100) NOT NULL,
    Status VARCHAR(20) DEFAULT 'Ativo',
    Cidade VARCHAR(100)
);

-- Tabela Peca
CREATE TABLE Peca (
    Pcodigo INT PRIMARY KEY,
    Pnome VARCHAR(100) NOT NULL,
    Cor VARCHAR(50) NOT NULL,
    Peso DECIMAL(10,2) NOT NULL,
    Cidade VARCHAR(100) NOT NULL
);

-- Tabela Instituicao
CREATE TABLE Instituicao (
    Icodigo INT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL
);

-- Tabela Projeto
CREATE TABLE Projeto (
    PRcod INT PRIMARY KEY,
    PRnome VARCHAR(100) NOT NULL,
    Cidade VARCHAR(100),
    Icod INT,
    FOREIGN KEY (Icod) REFERENCES Instituicao(Icodigo)
);

-- Tabela Fornecimento
CREATE TABLE Fornecimento (
    Fcod INT,
    Pcod INT,
    PRcod INT,
    Quantidade INT NOT NULL,
    PRIMARY KEY (Fcod, Pcod, PRcod),
    FOREIGN KEY (Fcod) REFERENCES Fornecedor(Fcodigo),
    FOREIGN KEY (Pcod) REFERENCES Peca(Pcodigo),
    FOREIGN KEY (PRcod) REFERENCES Projeto(PRcod)
);
