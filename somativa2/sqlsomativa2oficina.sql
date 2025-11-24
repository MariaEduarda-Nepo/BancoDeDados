
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

-- 1. Selecione todos os veículos cadastrados que são da marca "Ford".
SELECT
    *
FROM
    Veiculo
WHERE
    marca = 'Ford';
    
-- 2. Liste todos os clientes que abriram uma Ordem de Serviço (OS) nos últimos 6 meses.
SELECT DISTINCT
    c.nome,
    c.cpf_cnpj,
    c.telefone
FROM
    Cliente c
JOIN
    Veiculo v ON c.id_cliente = v.id_cliente
JOIN
    Ordem_Servico os ON v.id_veiculo = os.id_veiculo
WHERE
    os.data_abertura >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH);

-- 3. Mostre os mecânicos que possuem a especialidade "Injeção Eletrônica".
SELECT
    *
FROM
    Mecanico
WHERE
    especialidade = 'Injeção Eletrônica';
    
-- 4. Exiba todas as Ordens de Serviço (OS) que estão com o status "Aguardando Peça".
SELECT
    *
FROM
    Ordem_Servico
WHERE
    status = 'Aguardando Peça';
    
-- 5. Liste as peças (tabela Peca) cujo estoque (quantidade_estoque) está abaixo de 5 unidades.
SELECT
    *
FROM
    Peca
WHERE
    quantidade_estoque < 5;

-- 6. Escreva uma consulta para encontrar os veículos que já tiveram mais de uma Ordem de Serviço (retornaram à oficina) usando uma subconsulta correlacionada.
SELECT
    v.placa,
    v.marca,
    v.modelo
FROM
    Veiculo v
WHERE
    (
        SELECT
            COUNT(os.id_os)
        FROM
            Ordem_Servico os
        WHERE
            os.id_veiculo = v.id_veiculo
    ) > 1;

-- 7. Identifique as Ordens de Serviço que foram executadas por um mecânico específico (ex: id_mecanico = 3).
SELECT
    os.*
FROM
    Ordem_Servico os
JOIN
    Executa e ON os.id_os = e.id_os
WHERE
    e.id_mecanico = 3;
    
-- 8. (Desafio) Liste o nome e o preco_venda de todas as peças cujo preco_custo é superior a R$ 200,00.
SELECT
    nome,
    preco_venda
FROM
    Peca
WHERE
    preco_custo > 200.00;
    
    
-- 1. Atualize o preco_venda de todas as peças do fabricante "Bosch", aplicando um aumento de 5%.
UPDATE
    Peca
SET
    preco_venda = preco_venda * 1.05
WHERE
    nome LIKE '%Bosch%';

-- 2. Modifique o status da Ordem de Serviço de ID 105 de "Em Execução" para "Concluída".

UPDATE
    Ordem_Servico
SET
    status = 'Concluida'
WHERE
    id_os = 105
    AND status = 'Em Andamento'; -- Incluir o status atual é uma boa prática de segurança
    
-- 3. Atualize a data_conclusao de todas as Ordens de Serviço que ainda estão "Em Andamento" e foram abertas há mais de 30 dias.
UPDATE
    Ordem_Servico
SET
    data_conclusao = CURRENT_DATE(),
    status = 'Concluida' -- Geralmente, se a data de conclusão é definida, o status muda para 'Concluida'
WHERE
    status = 'Em Andamento'
    AND data_abertura < DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY);

-- 4. (Desafio) Dobre a quantidade em estoque (quantidade_estoque) da peça com id_peca = 20 (ex: "Filtro de Ar"), pois um novo lote chegou.

UPDATE
    Peca
SET
    quantidade_estoque = quantidade_estoque * 2
WHERE
    id_peca = 20;
    
-- 1. Adicione uma nova coluna email (tipo VARCHAR(100)) à tabela Cliente.
ALTER TABLE
    Cliente
ADD COLUMN
    email VARCHAR(100);

-- 2. Modifique o tipo de dados da coluna especialidade na tabela Mecanico para VARCHAR(150).
ALTER TABLE
    Mecanico
MODIFY COLUMN
    especialidade VARCHAR(150);
    
-- 3. Remova uma coluna (ex: diagnostico_entrada) da tabela Ordem_Servico.
ALTER TABLE
    Ordem_Servico
DROP COLUMN
    diagnostico_entrada;
    
-- 4. (Desafio) Adicione uma restrição CHECK na tabela Peca para garantir que preco_venda seja sempre maior ou igual ao preco_custo.
ALTER TABLE
    Peca
ADD CONSTRAINT
    chk_preco_venda_minimo
CHECK
    (preco_venda >= preco_custo);
    
    
-- 1. Liste todas as Ordens de Serviço, incluindo o nome do cliente, a placa do veículo e a data de abertura da OS.

SELECT
    os.id_os AS 'OS ID',
    c.nome AS 'Nome do Cliente',
    v.placa AS 'Placa do Veículo',
    os.data_abertura AS 'Data de Abertura'
FROM
    Ordem_Servico os
JOIN
    Veiculo v ON os.id_veiculo = v.id_veiculo
JOIN
    Cliente c ON v.id_cliente = c.id_cliente;
    
    
-- 2. Mostre todas as peças usadas na OS de ID 50, incluindo o nome da peça (tabela Peca) e a quantidade (tabela OS_Utiliza_Peca).
SELECT
    p.nome AS 'Nome da Peça',
    osp.quantidade AS 'Quantidade Utilizada'
FROM
    OS_Utiliza_Peca osp
JOIN
    Peca p ON osp.id_peca = p.id_peca
WHERE
    osp.id_os = 50;
    
--  3. Exiba os nomes dos mecânicos que trabalharam na OS de ID 75 (consultando Mecanico e Executa).
SELECT
    m.nome AS 'Nome do Mecânico',
    m.especialidade
FROM
    Executa e
JOIN
    Mecanico m ON e.id_mecanico = m.id_mecanico
WHERE
    e.id_os = 75;
    
-- 4. (Desafio) Liste todos os veículos (placa e modelo) cadastrados e o nome do seu respectivo proprietário (cliente).
SELECT
    v.placa AS 'Placa',
    v.modelo AS 'Modelo',
    c.nome AS 'Proprietário'
FROM
    Veiculo v
JOIN
    Cliente c ON v.id_cliente = c.id_cliente;
    
    
-- 1. Liste a placa e o modelo dos veículos que estão atualmente com uma OS "Em Andamento".
SELECT
    v.placa,
    v.modelo
FROM
    Veiculo v
INNER JOIN
    Ordem_Servico os ON v.id_veiculo = os.id_veiculo
WHERE
    os.status = 'Em Andamento';
    
-- 2. Mostre o nome dos clientes que possuem veículos da marca "Volkswagen".

SELECT DISTINCT
    c.nome AS 'Nome do Cliente'
FROM
    Cliente c
INNER JOIN
    Veiculo v ON c.id_cliente = v.id_cliente
WHERE
    v.marca = 'Volkswagen';
    
    

-- 3. Exiba os nomes dos mecânicos que já trabalharam em pelo menos uma Ordem de Serviço (ou seja, que aparecem na tabela Executa).

SELECT DISTINCT
    m.nome AS 'Nome do Mecânico'
FROM
    Mecanico m
INNER JOIN
    Executa e ON m.id_mecanico = e.id_mecanico;
    
-- 4. (Desafio) Liste apenas os nomes dos serviços (da tabela Servico) que já foram executados (ou seja, que aparecem na tabela OS_Contem_Servico).

SELECT DISTINCT
    s.descricao AS 'Nome do Serviço'
FROM
    Servico s
INNER JOIN
    OS_Contem_Servico ocs ON s.id_servico = ocs.id_servico;
    
    
    
-- 1. Liste todos os clientes e, para aqueles que já tiveram OS, mostre os IDs das ordens. Clientes que nunca vieram à oficina devem aparecer na lista.

SELECT
    c.nome AS 'Nome do Cliente',
    v.placa AS 'Placa do Veículo',
    os.id_os AS 'ID da OS',
    os.data_abertura
FROM
    Cliente c
LEFT JOIN
    Veiculo v ON c.id_cliente = v.id_cliente
LEFT JOIN
    Ordem_Servico os ON v.id_veiculo = os.id_veiculo
ORDER BY
    c.nome, os.id_os;
    
-- 2. Mostre todos os mecânicos e a quantidade de Ordens de Serviço em que cada um trabalhou (use COUNT). Mecânicos que nunca trabalharam em uma OS (novatos) devem aparecer com contagem 0.


SELECT
    m.nome AS 'Nome do Mecânico',
    m.especialidade,
    COUNT(e.id_os) AS 'OSs Trabalhadas'
FROM
    Mecanico m
LEFT JOIN
    Executa e ON m.id_mecanico = e.id_mecanico
GROUP BY
    m.id_mecanico, m.nome, m.especialidade
ORDER BY
    'OSs Trabalhadas' DESC, m.nome;
    
-- 3. Exiba todas as peças cadastradas (tabela Peca) e, se houver, a quantidade total vendida de cada uma (somando de OS_Utiliza_Peca). Peças que nunca foram vendidas devem aparecer.

SELECT
    p.nome AS 'Nome da Peça',
    p.preco_venda,
    COALESCE(SUM(osu.quantidade), 0) AS 'Quantidade Total Vendida'
FROM
    Peca p
LEFT JOIN
    OS_Utiliza_Peca osu ON p.id_peca = osu.id_peca
GROUP BY
    p.id_peca, p.nome, p.preco_venda
ORDER BY
    'Quantidade Total Vendida' DESC, p.nome;
    
--  4. (Desafio) Liste todos os veículos e a data da última OS aberta para cada um. Veículos que nunca tiveram uma OS devem aparecer com a data nula.

SELECT
    v.placa,
    v.modelo,
    MAX(os.data_abertura) AS 'Data da Última OS'
FROM
    Veiculo v
LEFT JOIN
    Ordem_Servico os ON v.id_veiculo = os.id_veiculo
GROUP BY
    v.id_veiculo, v.placa, v.modelo
ORDER BY
    v.placa;
    
    
    
-- 1. (Inverso do 6.1) Liste todas as Ordens de Serviço e o nome do cliente correspondente.
SELECT
    os.id_os AS 'ID da OS',
    os.data_abertura,
    c.nome AS 'Nome do Cliente'
FROM
    Cliente c
INNER JOIN -- Usamos INNER JOIN aqui pois Veiculo.id_cliente é NOT NULL
    Veiculo v ON c.id_cliente = v.id_cliente
RIGHT JOIN -- Garante todas as OSs
    Ordem_Servico os ON v.id_veiculo = os.id_veiculo;
    
-- 2. Mostre todos os serviços (da tabela Servico) e os IDs das OS onde eles foram usados. Serviços que nunca foram executados devem aparecer na lista (com ID da OS nulo).
SELECT
    s.descricao AS 'Serviço Cadastrado',
    ocs.id_os AS 'ID da OS Onde Foi Usado'
FROM
    OS_Contem_Servico ocs
RIGHT JOIN
    Servico s ON ocs.id_servico = s.id_servico
ORDER BY
    s.descricao, ocs.id_os;
    
-- 3. Exiba todos os itens da tabela Executa e traga o nome completo do mecânico da tabela Mecanico.

SELECT
    e.id_os AS 'ID da OS',
    m.nome AS 'Nome do Mecânico'
FROM
    Executa e
INNER JOIN -- INNER JOIN é o ideal aqui
    Mecanico m ON e.id_mecanico = m.id_mecanico
ORDER BY
    e.id_os;
    
  --  4. (Desafio) Liste todos os veículos (tabela Veiculo, direita) e as OS associadas (tabela Ordem_Servico, esquerda). Veículos sem OS devem aparecer. (Demonstração de inversão do LEFT JOIN).

SELECT
    v.placa AS 'Placa do Veículo',
    v.modelo AS 'Modelo',
    os.id_os AS 'ID da OS'
FROM
    Ordem_Servico os
RIGHT JOIN
    Veiculo v ON os.id_veiculo = v.id_veiculo
ORDER BY
    v.placa;
    
-- 1. Encontre os clientes que já abriram mais de 3 Ordens de Serviço.

SELECT
    c.nome,
    c.cpf_cnpj
FROM
    Cliente c
WHERE
    c.id_cliente IN (
        SELECT
            v.id_cliente
        FROM
            Veiculo v
        JOIN
            Ordem_Servico os ON v.id_veiculo = os.id_veiculo
        GROUP BY
            v.id_cliente
        HAVING
            COUNT(os.id_os) > 3
    );
    
-- 2. Identifique as peças (nome) que foram utilizadas na mesma Ordem de Serviço do mecânico "Carlos" (ID 4).

SELECT DISTINCT
    p.nome AS 'Nome da Peça'
FROM
    Peca p
INNER JOIN
    OS_Utiliza_Peca osp ON p.id_peca = osp.id_peca
WHERE
    osp.id_os IN (
        -- Subconsulta: Encontra todas as OS onde o mecânico com ID 4 trabalhou
        SELECT
            e.id_os
        FROM
            Executa e
        WHERE
            e.id_mecanico = 4
    );
    
-- 3. Liste os veículos (placa e modelo) que nunca tiveram uma Ordem de Serviço (use NOT IN ou NOT EXISTS).

SELECT
    v.placa,
    v.modelo
FROM
    Veiculo v
WHERE
    v.id_veiculo NOT IN (
        SELECT DISTINCT
            os.id_veiculo
        FROM
            Ordem_Servico os
    );
    
    
-- 4. (Desafio) Encontre os serviços (descricao) cujo valor_servico é maior que o preço médio de todos os serviços. 

SELECT
    s.descricao AS 'Serviço Acima da Média',
    s.valor_servico
FROM
    Servico s
WHERE
    s.valor_servico > (
        -- Subconsulta: Calcula a média de valor_servico de todos os serviços
        SELECT
            AVG(valor_servico)
        FROM
            Servico
    );
    
    
-- 1. Calcular o Faturamento Total de uma OS Específica (Ex: ID 100).
-- Consulta Final (Unificada):

SELECT
    (
        -- Subconsulta para a SOMA TOTAL DOS SERVIÇOS
        SELECT
            COALESCE(SUM(s.valor_servico), 0)
        FROM
            OS_Contem_Servico ocs
        JOIN
            Servico s ON ocs.id_servico = s.id_servico
        WHERE
            ocs.id_os = 100
    ) +
    (
        -- Subconsulta para a SOMA TOTAL DAS PEÇAS
        SELECT
            COALESCE(SUM(osp.quantidade * p.preco_venda), 0)
        FROM
            OS_Utiliza_Peca osp
        JOIN
            Peca p ON osp.id_peca = p.id_peca
        WHERE
            osp.id_os = 100
    ) AS 'Faturamento Total da OS 100';
    
    
-- 2. Determine o Tempo Médio (em dias) que as Ordens de Serviço ficam abertas.

SELECT
    AVG(DATEDIFF(data_conclusao, data_abertura)) AS 'Tempo Médio Aberta (Dias)'
FROM
    Ordem_Servico
WHERE
    status = 'Concluida'
    AND data_conclusao IS NOT NULL; -- Garante que apenas OS concluídas sejam consideradas
    

-- 1. Calcule o número total de veículos cadastrados na oficina.

SELECT
    COUNT(id_veiculo) AS 'Total de Veículos Cadastrados'
FROM
    Veiculo;
    
-- 2. Determine o valor total do inventário (estoque).

SELECT
    SUM(quantidade_estoque * preco_custo) AS 'Valor Total do Estoque (Custo)'
FROM
    Peca;
    
-- 3. Encontre o preço médio da mão de obra de todos os serviços (tabela Servico).

SELECT
    AVG(valor_servico) AS 'Preço Médio da Mão de Obra (Serviços)'
FROM
    Servico;
    
    
-- 1. Agrupe os veículos por marca e conte quantos veículos de cada marca a oficina atende.

SELECT
    marca AS 'Marca do Veículo',
    COUNT(id_veiculo) AS 'Quantidade Atendida'
FROM
    Veiculo
GROUP BY
    marca
ORDER BY
    'Quantidade Atendida' DESC;

-- 2. Determine o número de Ordens de Serviço abertas por mês.

SELECT
    YEAR(data_abertura) AS 'Ano',
    MONTH(data_abertura) AS 'Mês',
    COUNT(id_os) AS 'OSs Abertas no Mês'
FROM
    Ordem_Servico
GROUP BY
    YEAR(data_abertura), MONTH(data_abertura)
ORDER BY
    'Ano' ASC, 'Mês' ASC;
    
-- 3. Conte quantas OS cada status possui atualmente (agrupando por status).

SELECT
    status AS 'Status da OS',
    COUNT(id_os) AS 'Total de OSs'
FROM
    Ordem_Servico
GROUP BY
    status
ORDER BY
    'Total de OSs' DESC;
    
    
-- 1. Calcule o número total de OS que estão com o status "Concluída".

SELECT
    COUNT(id_os) AS 'Total de OSs Concluídas'
FROM
    Ordem_Servico
WHERE
    status = 'Concluida';
    
-- 2. Determine o faturamento total (peças + serviços) apenas dos veículos da marca "Fiat" no último ano.

SELECT
    SUM(os.valor_total) AS 'Faturamento Total da Fiat no Último Ano'
FROM
    Ordem_Servico os
JOIN
    Veiculo v ON os.id_veiculo = v.id_veiculo
WHERE
    v.marca = 'Fiat'
    AND os.data_abertura >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 YEAR);
    
    
-- 3. Encontre o preço médio da mão de obra apenas dos serviços na especialidade "Motor".

SELECT
    AVG(valor_servico) AS 'Preço Médio de Serviços de Motor'
FROM
    Servico
WHERE
    descricao LIKE '%Motor%';
    
    
-- 1. Encontre os id_cliente dos clientes que já gastaram (soma total em OS) mais de R$ 5.000,00 na oficina.

SELECT
    v.id_cliente,
    c.nome AS 'Nome do Cliente',
    SUM(os.valor_total) AS 'Gasto Total'
FROM
    Ordem_Servico os
JOIN
    Veiculo v ON os.id_veiculo = v.id_veiculo
JOIN
    Cliente c ON v.id_cliente = c.id_cliente
GROUP BY
    v.id_cliente, c.nome
HAVING
    SUM(os.valor_total) > 5000.00
ORDER BY
    'Gasto Total' DESC;
    
    
-- 2. Liste as id_peca das peças que foram vendidas (em OS_Utiliza_Peca) mais de 100 vezes no total.

SELECT
    osp.id_peca,
    p.nome AS 'Nome da Peça',
    SUM(osp.quantidade) AS 'Total Vendido'
FROM
    OS_Utiliza_Peca osp
JOIN
    Peca p ON osp.id_peca = p.id_peca
GROUP BY
    osp.id_peca, p.nome
HAVING
    SUM(osp.quantidade) > 100
ORDER BY
    'Total Vendido' DESC;
    
-- 3. Encontre as especialidades dos mecânicos que (agrupadas por especialidade) trabalharam em mais de 20 Ordens de Serviço no total.

SELECT
    m.especialidade,
    COUNT(e.id_os) AS 'Total de OSs por Especialidade'
FROM
    Executa e
JOIN
    Mecanico m ON e.id_mecanico = m.id_mecanico
GROUP BY
    m.especialidade
HAVING
    COUNT(e.id_os) > 20
ORDER BY
    'Total de OSs por Especialidade' DESC;
    
    
-- 4. (Desafio) Encontre o nome do mecânico que mais trabalhou em Ordens de Serviço (maior COUNT).

SELECT
    m.nome AS 'Mecânico Mais Ativo',
    COUNT(e.id_os) AS 'Número de OSs'
FROM
    Mecanico m
JOIN
    Executa e ON m.id_mecanico = e.id_mecanico
GROUP BY
    m.id_mecanico, m.nome
ORDER BY
    'Número de OSs' DESC
LIMIT 1;

-- 1. Criar Índice na Coluna placa da Tabela Veiculo

CREATE INDEX idx_veiculo_placa ON Veiculo (placa);


-- 2. Indexação da Chave Estrangeira e Impacto na Performance

CREATE INDEX fk_os_veiculo ON Ordem_Servico (id_veiculo);

INSERT INTO Cliente (nome, cpf_cnpj, telefone, email) VALUES
('Maria Silva', '111.222.333-44', '(11) 98765-4321', 'maria.silva@email.com'),
('João Santos', '555.666.777-88', '(21) 99887-7665', 'joao.santos@email.com'),
('Empresa Alpha Ltda', '01.234.567/0001-89', '(31) 3210-9876', 'contato@alpha.com'),
('Carlos Souza', '999.888.777-66', '(11) 91234-5678', 'carlos.souza@email.com');

INSERT INTO Veiculo (id_cliente, placa, marca, modelo, ano, cor) VALUES
(1, 'ABC1234', 'Ford', 'Ka', 2018, 'Vermelho'),
(2, 'XYZ5678', 'Volkswagen', 'Gol', 2015, 'Prata'),
(1, 'DEF9012', 'Ford', 'Ranger', 2020, 'Preto'),
(3, 'GHI3456', 'Fiat', 'Uno', 2010, 'Branco'),
(4, 'JKL7890', 'Chevrolet', 'Onix', 2022, 'Azul'),
(2, 'MNO1122', 'Volkswagen', 'T-Cross', 2023, 'Cinza');

INSERT INTO Mecanico (nome, especialidade, salario) VALUES
('Ricardo Almeida', 'Motor e Câmbio', 4500.00),
('Bianca Castro', 'Suspensão e Freios', 3800.00),
('Carlos Dantas', 'Injeção Eletrônica', 5200.00),
('Ana Ferreira', 'Funilaria', 3500.00);

INSERT INTO Servico (descricao, valor_servico) VALUES
('Troca de Óleo e Filtro', 80.00),
('Revisão Completa Freios', 120.00),
('Diagnóstico Injeção', 150.00),
('Troca de Amortecedores', 250.00),
('Alinhamento e Balanceamento', 100.00);

INSERT INTO Peca (nome, preco_custo, preco_venda, quantidade_estoque) VALUES
('Filtro de Óleo', 15.00, 25.00, 80),
('Pastilha de Freio (Diant.)', 80.00, 140.00, 30),
('Vela de Ignição Bosch', 40.00, 70.00, 50),
('Amortecedor Dianteiro', 180.00, 299.90, 12),
('Óleo 5W30 (Litro)', 30.00, 45.00, 150);

INSERT INTO Ordem_Servico (id_veiculo, data_abertura, data_conclusao, status, valor_total, observacoes) VALUES
(1, '2025-10-15', '2025-10-16', 'Concluida', 215.00, 'Troca de óleo e filtro.'), -- OS 1
(2, '2025-11-01', NULL, 'Aguardando Peça', 120.00, 'Aguardando pastilhas traseiras.'), -- OS 2
(3, '2025-11-10', NULL, 'Em Andamento', 650.00, 'Substituição de amortecedores e revisão.'), -- OS 3
(4, '2025-11-20', NULL, 'Aberta', 0.00, 'Cliente aguardando orçamento.'), -- OS 4
(1, '2025-11-22', '2025-11-24', 'Concluida', 170.00, 'Diagnóstico e troca de velas.'), -- OS 5
(5, '2025-05-01', '2025-05-03', 'Concluida', 1000.00, 'Revisão geral.'); -- OS 6 (Para testes de 6 meses)


INSERT INTO Executa (id_mecanico, id_os) VALUES
(1, 1), -- Ricardo fez a OS 1
(2, 2), -- Bianca está na OS 2
(3, 3), -- Carlos está na OS 3
(1, 3), -- Ricardo também está na OS 3 (2 mecânicos)
(3, 5), -- Carlos fez a OS 5
(2, 6); -- Bianca fez a OS 6
