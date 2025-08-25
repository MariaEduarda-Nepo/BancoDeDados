-- Índices para acelerar consultas
CREATE INDEX idx_fornecedor_nome ON Fornecedor(Fnome);
CREATE INDEX idx_peca_nome ON Peca(Pnome);
CREATE INDEX idx_projeto_nome ON Projeto(PRnome);
CREATE INDEX idx_cidade_nome ON Cidade(Cnome);
