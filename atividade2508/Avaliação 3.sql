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
