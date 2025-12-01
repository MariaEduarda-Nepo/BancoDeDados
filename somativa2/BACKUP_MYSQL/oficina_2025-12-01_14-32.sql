-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: oficina
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `oficina`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `oficina` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `oficina`;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cpf_cnpj` varchar(18) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,'Maria Silva','111.222.333-44','(11) 98765-4321','maria.silva@email.com'),(2,'João Santos','555.666.777-88','(21) 99887-7665','joao.santos@email.com'),(3,'Empresa Alpha Ltda','01.234.567/0001-89','(31) 3210-9876','contato@alpha.com'),(4,'Carlos Souza','999.888.777-66','(11) 91234-5678','carlos.souza@email.com');
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `executa`
--

DROP TABLE IF EXISTS `executa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `executa` (
  `id_mecanico` int NOT NULL,
  `id_os` int NOT NULL,
  PRIMARY KEY (`id_mecanico`,`id_os`),
  KEY `id_os` (`id_os`),
  CONSTRAINT `executa_ibfk_1` FOREIGN KEY (`id_mecanico`) REFERENCES `mecanico` (`id_mecanico`),
  CONSTRAINT `executa_ibfk_2` FOREIGN KEY (`id_os`) REFERENCES `ordem_servico` (`id_os`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `executa`
--

LOCK TABLES `executa` WRITE;
/*!40000 ALTER TABLE `executa` DISABLE KEYS */;
INSERT INTO `executa` VALUES (1,1),(2,2),(1,3),(3,3),(3,5),(2,6);
/*!40000 ALTER TABLE `executa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mecanico`
--

DROP TABLE IF EXISTS `mecanico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mecanico` (
  `id_mecanico` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `especialidade` varchar(50) DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_mecanico`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mecanico`
--

LOCK TABLES `mecanico` WRITE;
/*!40000 ALTER TABLE `mecanico` DISABLE KEYS */;
INSERT INTO `mecanico` VALUES (1,'Ricardo Almeida','Motor e Câmbio',4500.00),(2,'Bianca Castro','Suspensão e Freios',3800.00),(3,'Carlos Dantas','Injeção Eletrônica',5200.00),(4,'Ana Ferreira','Funilaria',3500.00);
/*!40000 ALTER TABLE `mecanico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordem_servico`
--

DROP TABLE IF EXISTS `ordem_servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordem_servico` (
  `id_os` int NOT NULL AUTO_INCREMENT,
  `id_veiculo` int NOT NULL,
  `data_abertura` date NOT NULL,
  `data_conclusao` date DEFAULT NULL,
  `status` enum('Aberta','Em Andamento','Aguardando Peça','Concluida','Cancelada') NOT NULL DEFAULT 'Aberta',
  `valor_total` decimal(10,2) DEFAULT '0.00',
  `observacoes` text,
  PRIMARY KEY (`id_os`),
  KEY `id_veiculo` (`id_veiculo`),
  CONSTRAINT `ordem_servico_ibfk_1` FOREIGN KEY (`id_veiculo`) REFERENCES `veiculo` (`id_veiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordem_servico`
--

LOCK TABLES `ordem_servico` WRITE;
/*!40000 ALTER TABLE `ordem_servico` DISABLE KEYS */;
INSERT INTO `ordem_servico` VALUES (1,1,'2025-10-15','2025-10-16','Concluida',215.00,'Troca de óleo e filtro.'),(2,2,'2025-11-01',NULL,'Aguardando Peça',120.00,'Aguardando pastilhas traseiras.'),(3,3,'2025-11-10',NULL,'Em Andamento',650.00,'Substituição de amortecedores e revisão.'),(4,4,'2025-11-20',NULL,'Aberta',0.00,'Cliente aguardando orçamento.'),(5,1,'2025-11-22','2025-11-24','Concluida',170.00,'Diagnóstico e troca de velas.'),(6,5,'2025-05-01','2025-05-03','Concluida',1000.00,'Revisão geral.');
/*!40000 ALTER TABLE `ordem_servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `os_contem_servico`
--

DROP TABLE IF EXISTS `os_contem_servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `os_contem_servico` (
  `id_os` int NOT NULL,
  `id_servico` int NOT NULL,
  PRIMARY KEY (`id_os`,`id_servico`),
  KEY `id_servico` (`id_servico`),
  CONSTRAINT `os_contem_servico_ibfk_1` FOREIGN KEY (`id_os`) REFERENCES `ordem_servico` (`id_os`),
  CONSTRAINT `os_contem_servico_ibfk_2` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id_servico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `os_contem_servico`
--

LOCK TABLES `os_contem_servico` WRITE;
/*!40000 ALTER TABLE `os_contem_servico` DISABLE KEYS */;
/*!40000 ALTER TABLE `os_contem_servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `os_utiliza_peca`
--

DROP TABLE IF EXISTS `os_utiliza_peca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `os_utiliza_peca` (
  `id_os` int NOT NULL,
  `id_peca` int NOT NULL,
  `quantidade` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_os`,`id_peca`),
  KEY `id_peca` (`id_peca`),
  CONSTRAINT `os_utiliza_peca_ibfk_1` FOREIGN KEY (`id_os`) REFERENCES `ordem_servico` (`id_os`),
  CONSTRAINT `os_utiliza_peca_ibfk_2` FOREIGN KEY (`id_peca`) REFERENCES `peca` (`id_peca`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `os_utiliza_peca`
--

LOCK TABLES `os_utiliza_peca` WRITE;
/*!40000 ALTER TABLE `os_utiliza_peca` DISABLE KEYS */;
/*!40000 ALTER TABLE `os_utiliza_peca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peca`
--

DROP TABLE IF EXISTS `peca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peca` (
  `id_peca` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `preco_custo` decimal(10,2) DEFAULT NULL,
  `preco_venda` decimal(10,2) NOT NULL,
  `quantidade_estoque` int DEFAULT '0',
  PRIMARY KEY (`id_peca`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peca`
--

LOCK TABLES `peca` WRITE;
/*!40000 ALTER TABLE `peca` DISABLE KEYS */;
INSERT INTO `peca` VALUES (1,'Filtro de Óleo',15.00,25.00,80),(2,'Pastilha de Freio (Diant.)',80.00,140.00,30),(3,'Vela de Ignição Bosch',40.00,70.00,50),(4,'Amortecedor Dianteiro',180.00,299.90,12),(5,'Óleo 5W30 (Litro)',30.00,45.00,150);
/*!40000 ALTER TABLE `peca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servico`
--

DROP TABLE IF EXISTS `servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servico` (
  `id_servico` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  `valor_servico` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_servico`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servico`
--

LOCK TABLES `servico` WRITE;
/*!40000 ALTER TABLE `servico` DISABLE KEYS */;
INSERT INTO `servico` VALUES (1,'Troca de Óleo e Filtro',80.00),(2,'Revisão Completa Freios',120.00),(3,'Diagnóstico Injeção',150.00),(4,'Troca de Amortecedores',250.00),(5,'Alinhamento e Balanceamento',100.00);
/*!40000 ALTER TABLE `servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `veiculo`
--

DROP TABLE IF EXISTS `veiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `veiculo` (
  `id_veiculo` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `placa` varchar(7) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `ano` int DEFAULT NULL,
  `cor` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_veiculo`),
  UNIQUE KEY `placa` (`placa`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `veiculo_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `veiculo`
--

LOCK TABLES `veiculo` WRITE;
/*!40000 ALTER TABLE `veiculo` DISABLE KEYS */;
INSERT INTO `veiculo` VALUES (1,1,'ABC1234','Ford','Ka',2018,'Vermelho'),(2,2,'XYZ5678','Volkswagen','Gol',2015,'Prata'),(3,1,'DEF9012','Ford','Ranger',2020,'Preto'),(4,3,'GHI3456','Fiat','Uno',2010,'Branco'),(5,4,'JKL7890','Chevrolet','Onix',2022,'Azul'),(6,2,'MNO1122','Volkswagen','T-Cross',2023,'Cinza');
/*!40000 ALTER TABLE `veiculo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'oficina'
--

--
-- Dumping routines for database 'oficina'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 14:32:11
