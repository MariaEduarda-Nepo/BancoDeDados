-- Geração de Modelo físico
-- Sql ANSI 2003 - brModelo.



CREATE TABLE Cliente (
id_cliente in primary key auto increment PRIMARY KEY,
nome varchar(100) not null,
cpf_cnpj varchar (18) unique,
telefone varchar (15),
email varchar(100)
)

CREATE TABLE veiculo+Ordem_Servico (
id_veiculo int primary key auto_increment,
placa varchar(7) unique not null,
marca varchar(50),
modelo varchar(50),
ano int,
cor varchar(30),
id_os int primary key auto_increment,
data_abertura date not null,
data_conclusao date,
status  ENUM ('Aberto', 'Em Andamento', 'Aguardando Peça', 'Concluida', 'Cancelada') not null default 'Aberta',
valor_total decimal (10,2) default 0.00,
observacoes Text,
PRIMARY KEY(id_veiculo,id_os)
)

CREATE TABLE Mecanico (
nome varchar(100) not null,
id_mecanico int primary key not null PRIMARY KEY,
especialidade varchar(50),
salario decimal (10,2)
)

CREATE TABLE Servico (
id_servico int primary key auto_increment PRIMARY KEY,
descricao varchar(100) not null unique,
valor_servico decimal(10,2) not null
)

CREATE TABLE peca (
id_peca int primary key auto_increment PRIMARY KEY,
nome varchar(100) not null unique,
preco_custo decimal(10,2),
preco_venda decimal(10,2) not null,
quantidade_estoque int default '0'
)

CREATE TABLE Possui (
id_veiculo int primary key auto_increment,
id_cliente in primary key auto increment,
FOREIGN KEY(id_veiculo,id_os) REFERENCES veiculo+Ordem_Servico (id_veiculo,id_os),
FOREIGN KEY(id_cliente) REFERENCES Cliente (id_cliente)
)

CREATE TABLE executada_por (
id_mecanico int primary key not null,
id_veiculo int primary key auto_increment,
id_os int primary key auto_increment,
FOREIGN KEY(id_mecanico) REFERENCES Mecanico (id_mecanico),
FOREIGN KEY(id_veiculo,id_os) REFERENCES veiculo+Ordem_Servico (id_veiculo,id_os)
)

CREATE TABLE Contem_Servico (
id_servico int primary key auto_increment,
id_veiculo int primary key auto_increment,
id_os int primary key auto_increment,
FOREIGN KEY(id_servico) REFERENCES Servico (id_servico),
FOREIGN KEY(id_veiculo,id_os) REFERENCES veiculo+Ordem_Servico (id_veiculo,id_os)
)

CREATE TABLE utiliza_peca (
id_peca int primary key auto_increment,
id_veiculo int primary key auto_increment,
id_os int primary key auto_increment,
FOREIGN KEY(id_peca) REFERENCES peca (id_peca),
FOREIGN KEY(id_veiculo,id_os) REFERENCES veiculo+Ordem_Servico (id_veiculo,id_os)/*falha: chave estrangeira*/
)

