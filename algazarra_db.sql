CREATE DATABASE IF NOT EXISTS algazarra_db;
USE algazarra_db;

-- ========================
-- TABELA: enc_educacao
-- ========================
CREATE TABLE IF NOT EXISTS enc_educacao (
  id int(11) NOT NULL AUTO_INCREMENT,
  nome varchar(100) NOT NULL,
  telemovel varchar(20) NOT NULL,
  email varchar(100) NOT NULL,
  morada varchar(150) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO enc_educacao VALUES
(1,'Carlos Carvalho','912345678','carlos@gmail.com','Lisboa'),
(2,'Ana Almeida','913456789','ana@gmail.com','Porto'),
(3,'João Ferreira','914567890','joao@gmail.com','Coimbra'),
(4,'Maria Martins','915678901','maria@gmail.com','Braga'),
(5,'Paulo Costa','916789012','paulo@gmail.com','Faro');

-- ========================
-- TABELA: aluno
-- ========================
CREATE TABLE IF NOT EXISTS aluno (
  id int(11) NOT NULL AUTO_INCREMENT,
  nome varchar(100) NOT NULL,
  data_nascimento date NOT NULL,
  enc_educacao int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (enc_educacao) REFERENCES enc_educacao(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO aluno VALUES
(1,'Sofia Almeida','2013-03-15',2),
(2,'Pedro Carvalho','2014-11-08',1),
(3,'Inês Ferreira','2014-07-22',3),
(4,'Tiago Martins','2015-09-30',4),
(5,'Beatriz Santos','2014-12-05',2),
(6,'Rafael Costa','2011-01-19',5),
(7,'Carolina Pinto','2013-06-10',3),
(8,'Miguel Rocha','2016-08-27',1),
(9,'Leonor Teixeira','2011-04-14',4),
(10,'André Correia','2012-10-03',2);

-- ========================
-- TABELA: professor
-- ========================
CREATE TABLE IF NOT EXISTS professor (
  id int(11) NOT NULL AUTO_INCREMENT,
  user varchar(30) NOT NULL,
  email varchar(100) NOT NULL,
  pwd varchar(200) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO professor VALUES
(1,'professor','professor@algazarra.pt','3f9cd3c7b11eb1bae99dddb3d05da3c5');

-- ========================
-- TABELA: atividade
-- ========================
CREATE TABLE IF NOT EXISTS atividade (
  id int(11) NOT NULL AUTO_INCREMENT,
  titulo varchar(100) NOT NULL,
  descricao text NOT NULL,
  imagem varchar(50) NOT NULL,
  data_inicio date NOT NULL,
  data_fim date NOT NULL,
  lotacao_max int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO atividade VALUES
(1,'Visita ao Estádio da Luz',
'Visita guiada ao Estádio da Luz...',
'img/image.png','2026-07-01','2026-07-05',50),

(2,'Paintball',
'Atividade de paintball em equipa...',
'img/paintball.jpg','2026-07-06','2026-07-10',40),

(3,'Jardim Zoológico de Lisboa',
'Visita ao Jardim zoológico de Lisboa ...',
'img/imagem2.png','2026-07-11','2026-07-15',50),

(4,'Mini Torneio de Futebol',
'Atividade de demonstração com limite máximo de 3 crianças inscritas.',
'img/futebol.jpg','2026-07-16','2026-07-18',3);

-- ========================
-- TABELA: horario
-- ========================
CREATE TABLE IF NOT EXISTS horario (
  id int(11) NOT NULL AUTO_INCREMENT,
  dia_semana varchar(20) NOT NULL,
  hora_inicio time DEFAULT NULL,
  hora_fim time DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO horario VALUES
(1,'segunda-feira','09:00:00','18:00:00'),
(2,'terça-feira','10:00:00','19:00:00'),
(3,'quarta-feira','08:30:00','17:30:00'),
(4,'quinta-feira','11:00:00','20:00:00'),
(5,'sexta-feira','09:00:00','16:00:00'),
(6,'sábado','10:00:00','14:00:00'),
(7,'domingo',NULL,NULL);

-- ========================
-- TABELA: inscricao
-- ========================
CREATE TABLE IF NOT EXISTS inscricao (
  aluno int(11) NOT NULL,
  atividade int(11) NOT NULL,
  dia date NOT NULL,
  esta_presente tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (aluno, atividade, dia),
  FOREIGN KEY (aluno) REFERENCES aluno(id),
  FOREIGN KEY (atividade) REFERENCES atividade(id)
) ENGINE=InnoDB;

INSERT INTO inscricao VALUES
(1,1,'2026-04-01',1),
(1,1,'2026-04-02',1),
(1,1,'2026-04-03',0),

(2,2,'2026-04-01',1),
(2,2,'2026-04-02',0),
(2,2,'2026-04-03',1),

(3,3,'2026-04-04',1),
(3,3,'2026-04-05',0),
(3,3,'2026-04-06',1),

-- Atividade 4 já cheia: 3 alunos inscritos
(1,4,'2026-07-16',0),
(2,4,'2026-07-16',0),
(3,4,'2026-07-16',0);

-- ========================
-- TABELA: utilizador
-- nível 1 = admin
-- nível 2 = encarregado
-- nível 3 = professor
-- ========================
CREATE TABLE IF NOT EXISTS utilizador (
  id int(11) NOT NULL AUTO_INCREMENT,
  user varchar(30) NOT NULL,
  nome varchar(100) NOT NULL,
  data_nascimento date NOT NULL,
  telemovel varchar(20) NOT NULL,
  email varchar(100) NOT NULL,
  pwd varchar(200) NOT NULL,
  nivel int(11) NOT NULL DEFAULT 2,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO utilizador (user, nome, data_nascimento, telemovel, email, pwd, nivel) VALUES
(
  'admin',
  'admin',
  '1980-01-01',
  '910000000',
  'admin@algazarra.pt',
  '21232f297a57a5a743894a0e4a801fc3',
  1
),
(
  'professor',
  'Professor Algazarra',
  '1985-02-15',
  '911111111',
  'professor@algazarra.pt',
  '3f9cd3c7b11eb1bae99dddb3d05da3c5',
  3
),
(
  'encarregado1',
  'Carlos Carvalho',
  '1975-05-10',
  '912345678',
  'carlos@gmail.com',
  '25d55ad283aa400af464c76d713c07ad',
  2
);