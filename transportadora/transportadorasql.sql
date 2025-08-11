create database transportadora;

use transportadora;

create table servicos (
cod_servicos int,
nome_servicos varchar(255),
rota_endereco varchar(255),
produtos varchar(255),
veiculo varchar(255)
);

create table estoque (
cod_estoque int,
nome_produto varchar(255),
quantidade int,
valor decimal,
validade datetime
);

create table rotas (
cod_rotas int,
endereco varchar(255),
ponto_de_parada varchar(255),
caminho_seguido varchar(255),
veiculo_usado varchar(255)
);

create table funcionarios (
cod_funcionarios int,
nome varchar(255),
cpf varchar(14),
telefone varchar(19),
veiculo varchar(255)
);

create table veiculos (
cod_veiculo int,
modelo varchar(255),
placa varchar(10),
tipo varchar(10),
funcionario_responsavel varchar(255)
);