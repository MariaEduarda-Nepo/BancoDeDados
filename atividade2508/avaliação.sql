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

-- Criar tabela Cidade
CREATE TABLE Cidade (
    Ccod INT PRIMARY KEY,
    Cnome VARCHAR(100) NOT NULL,
    UF CHAR(2) NOT NULL
);

-- Alterar Fornecedor
ALTER TABLE Fornecedor
    ADD Fone VARCHAR(20),
    ADD Ccod INT,
    ADD CONSTRAINT fk_fornecedor_cidade FOREIGN KEY (Ccod) REFERENCES Cidade(Ccod);

-- Alterar Peca
ALTER TABLE Peca
    ADD Ccod INT,
    ADD CONSTRAINT fk_peca_cidade FOREIGN KEY (Ccod) REFERENCES Cidade(Ccod);

-- Alterar Projeto
ALTER TABLE Projeto
    DROP COLUMN Cidade,
    DROP FOREIGN KEY projeto_ibfk_1, -- exclui FK antiga de Instituicao
    DROP COLUMN Icod,
    ADD Ccod INT,
    ADD CONSTRAINT fk_projeto_cidade FOREIGN KEY (Ccod) REFERENCES Cidade(Ccod);

-- Excluir tabela Instituicao
DROP TABLE Instituicao;

-- Índices para acelerar consultas
CREATE INDEX idx_fornecedor_nome ON Fornecedor(Fnome);
CREATE INDEX idx_peca_nome ON Peca(Pnome);
CREATE INDEX idx_projeto_nome ON Projeto(PRnome);
CREATE INDEX idx_cidade_nome ON Cidade(Cnome);

