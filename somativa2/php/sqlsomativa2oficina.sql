-- Geração de Modelo físico
-- Sql ANSI 2003 - brModelo.

CREATE DATABASE Oficina;
use  oficina;

CREATE TABLE Cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cpf_cnpj VARCHAR(18) UNIQUE,
    telefone VARCHAR(15),
    email VARCHAR(100)
);

CREATE TABLE Veiculo (
    id_veiculo INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NOT NULL, -- Chave estrangeira para o Cliente que possui o veículo
    placa VARCHAR(7) UNIQUE NOT NULL,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    ano INT,
    cor VARCHAR(30),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);


CREATE TABLE Ordem_Servico (
    id_os INT PRIMARY KEY AUTO_INCREMENT,
    id_veiculo INT NOT NULL,
    data_abertura DATE NOT NULL,
    data_conclusao DATE,
    status ENUM ('Aberta', 'Em Andamento', 'Aguardando Peça', 'Concluida', 'Cancelada') NOT NULL DEFAULT 'Aberta',
    valor_total DECIMAL(10, 2) DEFAULT 0.00,
    observacoes TEXT,
    FOREIGN KEY (id_veiculo) REFERENCES Veiculo(id_veiculo)
);


CREATE TABLE Mecanico (
    id_mecanico INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    especialidade VARCHAR(50),
    salario DECIMAL(10, 2)
);


CREATE TABLE Servico (
    id_servico INT PRIMARY KEY AUTO_INCREMENT,
    descricao VARCHAR(100) NOT NULL UNIQUE,
    valor_servico DECIMAL(10, 2) NOT NULL
);


CREATE TABLE Peca (
    id_peca INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE,
    preco_custo DECIMAL(10, 2),
    preco_venda DECIMAL(10, 2) NOT NULL,
    quantidade_estoque INT DEFAULT 0 -- 0 não precisa de aspas
);


CREATE TABLE Executa (
    id_mecanico INT NOT NULL,
    id_os INT NOT NULL,
    
    PRIMARY KEY (id_mecanico, id_os), -- Chave primária composta
    
    FOREIGN KEY (id_mecanico) REFERENCES Mecanico(id_mecanico),
    FOREIGN KEY (id_os) REFERENCES Ordem_Servico(id_os)
);


CREATE TABLE OS_Contem_Servico (
    id_os INT NOT NULL,
    id_servico INT NOT NULL,
    
    PRIMARY KEY (id_os, id_servico), -- Chave primária composta
    
    FOREIGN KEY (id_os) REFERENCES Ordem_Servico(id_os),
    FOREIGN KEY (id_servico) REFERENCES Servico(id_servico)
);


CREATE TABLE OS_Utiliza_Peca (
    id_os INT NOT NULL,
    id_peca INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1, -- Quantidade de peças utilizadas
    
    PRIMARY KEY (id_os, id_peca), -- Chave primária composta
    
    FOREIGN KEY (id_os) REFERENCES Ordem_Servico(id_os),
    FOREIGN KEY (id_peca) REFERENCES Peca(id_peca)
);