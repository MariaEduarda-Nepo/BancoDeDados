create database loja_limpeza;
use loja_limpeza;

create table clientes (
cod_cliente int,
nome_cliente varchar(255),
cpf_cliente varchar(14),
endereco varchar(255),
telefone varchar(19)

);

create table estoque (
cod_estoque int,
nome_produto varchar(255),
validade datetime,
quantidade int,
posicao varchar(10)
);

create table produto (
cod_produto int,
nome_produto varchar(255),
quantidade int,
descricao varchar(255),
valor decimal
);

create table funcionarios (
cod_funcionario int,
nome_funcionario varchar(255),
cpf_funcionario varchar(14),
funcao varchar(100),
salario decimal
);

create table pedido (
cod_pedido int,
nome_cliente varchar(255),
endereco_entrega varchar(255),
produtos varchar(255),
valor decimal
);

create table manutencao (
cod_manutencao int,
servico_manutencao varchar(255),
cliente_solicitante varchar(255),
funcionario_responsavel varchar(255),
valor decimal
);