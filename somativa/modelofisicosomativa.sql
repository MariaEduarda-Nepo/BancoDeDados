-- Geração de Modelo físico
-- Sql ANSI 2003 - brModelo.



CREATE TABLE Alunos (
nome varchar(100) not null,
data_nascimento date,
email Texto(1),
id_aluno int auto_increment  PRIMARY KEY
)

CREATE TABLE Cursos (
carga_horaria int,
descricao Texto(255),
titulo Texto(100),
status texto (ativo/inativo) default 'ativo',
id_curso int  auto_increment PRIMARY KEY
)

CREATE TABLE Inscricao+avaliacoes (
data_inscricao date,
id_inscricao int,
id_avaliacao int,
comentario Texto(255),
nota decimal,
PRIMARY KEY(id_inscricao,id_avaliacao)
)

CREATE TABLE Faz (
id_inscricao int ,
id_aluno int ,
FOREIGN KEY(id_inscricao,id_avaliacao) REFERENCES Inscricao+avaliacoes (id_inscricao,id_avaliacao),
FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno)
)

CREATE TABLE seleciona (
id_inscricao int ,
id_curso int ,
FOREIGN KEY(id_inscricao,id_avaliacao) REFERENCES Inscricao+avaliacoes (id_inscricao,id_avaliacao),
FOREIGN KEY(id_curso) REFERENCES Cursos (id_curso)
)

