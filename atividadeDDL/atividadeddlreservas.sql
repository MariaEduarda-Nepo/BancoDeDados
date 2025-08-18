create database ReservaEscola;

use ReservaEscola;


create table solicitante (
cod_solicitante int auto_increment primary key not null,
cpf varchar(14) not null,
nome_solicitante varchar(100) not null,
reserva int not null,
telefone varchar(19) not null
);


create table reserva (
cod_reserva int auto_increment primary key not null,
produto_reservado varchar(255) not null,
nome_solicitante varchar(100) not null,
data_reserva datetime not null,
data_devolucao datetime not null
);

create table produto (
cod_produto int auto_increment primary key not null,
nome_produto varchar(255) not null,
quantidade int not null,
reservas varchar(255) not null,
descricao varchar(255)
);

create table estoque (
cod_estoque int auto_increment primary key not null,
nome_produto varchar(255) not null,
quantidade int not null,
validade datetime not null,
posicao varchar(100) not null
);

create table responsavel_reserva (
cod_responsavel int auto_increment primary key not null,
nome_responsavel varchar(255) not null,
endereco varchar(255) not null,
CPF varchar(14) not null,
telefone varchar(19) not null
);

create table tem (
cod_tem int auto_increment primary key not null,
cod_estoque int,
cod_produto int,
foreign key (cod_produto) references produto (cod_produto),
foreign key (cod_estoque) references estoque (cod_estoque)
);

create table solicita (
cod_tem int auto_increment primary key not null,
cod_solicitante int,
cod_reserva int,
foreign key (cod_solicitante) references solicitante (cod_solicitante),
foreign key (cod_reserva) references reserva (cod_reserva)
);

