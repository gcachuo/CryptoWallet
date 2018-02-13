-- MySQL dump 10.13  Distrib 5.7.21, for Linux (x86_64)
--
-- Host: localhost    Database: crypto
-- ------------------------------------------------------
-- Server version	5.7.21-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_acciones`
--

DROP TABLE IF EXISTS `_acciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_acciones` (
  `id_accion` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_accion` varchar(100) NOT NULL,
  `estatus_accion` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id_accion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_acciones`
--

LOCK TABLES `_acciones` WRITE;
/*!40000 ALTER TABLE `_acciones` DISABLE KEYS */;
INSERT INTO `_acciones` VALUES (1,'accesar',''),(2,'editar','');
/*!40000 ALTER TABLE `_acciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_modulos`
--

DROP TABLE IF EXISTS `_modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_modulos` (
  `id_modulo` bigint(20) NOT NULL,
  `icono_modulo` varchar(100) DEFAULT NULL,
  `padre_modulo` bigint(20) NOT NULL,
  `orden_modulo` bigint(20) NOT NULL,
  `navegar_modulo` varchar(100) DEFAULT NULL,
  `estatus_modulo` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_modulos`
--

LOCK TABLES `_modulos` WRITE;
/*!40000 ALTER TABLE `_modulos` DISABLE KEYS */;
INSERT INTO `_modulos` VALUES (1001,NULL,1000,1,'usuarios',''),(1002,NULL,1000,2,'perfiles',''),(1003,NULL,1000,3,'clientes',''),(2001,NULL,2000,1,'config',''),(3001,NULL,3000,1,'wallet',''),(4001,NULL,4000,1,'orders','');
/*!40000 ALTER TABLE `_modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_perfiles`
--

DROP TABLE IF EXISTS `_perfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_perfiles` (
  `id_perfil` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_perfil` varchar(100) NOT NULL,
  `id_usuario` bigint(20) NOT NULL DEFAULT '1' COMMENT 'Creador del perfil',
  `estatus_perfil` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id_perfil`),
  UNIQUE KEY `_perfiles_nombre_perfil_uindex` (`nombre_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_perfiles`
--

LOCK TABLES `_perfiles` WRITE;
/*!40000 ALTER TABLE `_perfiles` DISABLE KEYS */;
INSERT INTO `_perfiles` VALUES (1,'Administrador',1,''),(2,'Cliente',1,'');
/*!40000 ALTER TABLE `_perfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_perfiles_acciones`
--

DROP TABLE IF EXISTS `_perfiles_acciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_perfiles_acciones` (
  `id_perfil_accion` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_perfil` bigint(20) NOT NULL,
  `id_modulo` bigint(20) NOT NULL,
  `id_accion` bigint(20) NOT NULL,
  PRIMARY KEY (`id_perfil_accion`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_perfiles_acciones`
--

LOCK TABLES `_perfiles_acciones` WRITE;
/*!40000 ALTER TABLE `_perfiles_acciones` DISABLE KEYS */;
INSERT INTO `_perfiles_acciones` VALUES (4,1,2001,1),(5,1,3001,1),(6,1,4001,1),(7,2,3001,1),(8,1,3001,2);
/*!40000 ALTER TABLE `_perfiles_acciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_usuarios`
--

DROP TABLE IF EXISTS `_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_usuarios` (
  `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(100) DEFAULT NULL,
  `login_usuario` varchar(50) NOT NULL,
  `password_usuario` varchar(255) NOT NULL,
  `correo_usuario` varchar(255) DEFAULT NULL,
  `estatus_usuario` bit(1) NOT NULL DEFAULT b'1',
  `perfil_usuario` bigint(20) NOT NULL DEFAULT '1',
  `id_usuario_create` bigint(20) NOT NULL COMMENT 'usuario que creo el registro',
  `last_login_usuario` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuarios_login_usuario_uindex` (`login_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_usuarios`
--

LOCK TABLES `_usuarios` WRITE;
/*!40000 ALTER TABLE `_usuarios` DISABLE KEYS */;
INSERT INTO `_usuarios` VALUES (1,'Memo Cachu','gcachu.o@gmail.com','$2s8ly4H9Tsz2','gcachu.o@gmail.com','',0,0,'2018-02-10 14:06:05'),(2,'Eduardo MuÃ±oz','fluknx@gmail.com','$2ovfs3ldGR1U','fluknx@gmail.com','',1,0,NULL),(3,'Laura Osorio','lic_laura@live.com.mx','$2MSfsOCWMh.s','lic_laura@live.com.mx','',2,0,NULL),(4,'Daniel Hernán Cárdenas Mendoza','hyrule_95@hotmail.com','$2Q8pkd0kvoOU','hyrule_95@hotmail.com','',1,1,NULL),(5,'Alberto  Orozco Plascencia ','alberto_opl@outlook.com','$2IyMsQBJncnc','alberto_opl@outlook.com','',1,0,NULL),(6,'michelle Alejandra Carrazco Delgado','edaclaim@gmail.com','$2OXZPPfB.v9w','edaclaim@gmail.com','',1,1,NULL),(7,'Eduardo Montes de Oca','eduardo.gm.300@gmail.com','$2Gg.TPm1f9SA','eduardo.gm.300@gmail.com','',2,0,'2018-02-10 12:42:52'),(8,'Horacio Caro Aguilar','horacio_caro0524@hotmail.com','$2ysWcLKbjgOg','horacio_caro0524@hotmail.com','',1,0,NULL),(9,'Carla  Osorio Velasco ','carla.osorio.velasco@gmail.com','$2eJDlUhgMM.Y','carla.osorio.velasco@gmail.com','',1,0,NULL),(10,'David Osorio','osorion_d@hotmail.com','$2Tija6ka8NfI','osorion_d@hotmail.com','',2,0,'2018-02-04 11:39:20'),(12,'Lilia Osorio','lileon123@hotmail.com','$2DO2Tl8E3zYk','lileon123@hotmail.com','',2,0,NULL),(13,'','esaulhr@gmail.com','$2YPnffXfTco6','esaulhr@gmail.com','\0',1,1,NULL),(14,'EsaÃºl HernÃ¡ndez RodrÃ­guez','applexamx@gmail.com','$2i1/uJvbcBUc','applexamx@gmail.com','',2,0,'2018-01-26 10:42:10'),(15,'Paulina Zanella','paulinaznll@gmail.com','$2n5GGqEWRHUE','paulinaznll@gmail.com','',1,0,'2018-02-10 06:29:50'),(16,'Tu gfa','nadien@gmail.com','$2x1WLqfZms8g','nadien@gmail.com','',2,0,NULL),(17,'Kchita Esunaputina','kshibombo@gmail.com','$2eI0tK5DFWjw','kshibombo@gmail.com','',1,0,NULL),(18,'Oscar Alejandro  Montes de Oca ','alexander_z28@hotmail.com','$2Gg.TPm1f9SA','alexander_z28@hotmail.com','',2,0,'2018-02-10 11:50:33'),(19,'Daniel Osorio','danieloso74@gmail.com','$2yHqMTJonN7s','danieloso74@gmail.com','',2,0,NULL),(20,'Guillermo CachÃº','gcachub@hotmail.com','$2e.LuN5pdbdk','gcachub@hotmail.com','',2,0,'2018-02-10 11:56:57');
/*!40000 ALTER TABLE `_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id_cliente` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(255) NOT NULL,
  `direccion_eth_cliente` varchar(45) DEFAULT NULL,
  `id_admin` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Memo Cachu ','',NULL),(2,'Eduardo MuÃ±oz ',NULL,NULL),(3,'Laura Osorio ',NULL,1),(4,'Daniel Hernán Cárdenas Mendoza ',NULL,NULL),(5,'Alberto  Orozco Plascencia  ',NULL,NULL),(6,'michelle Alejandra Carrazco Delgado ',NULL,NULL),(7,'Eduardo Montes de Oca ',NULL,1),(8,'Horacio Caro Aguilar ',NULL,NULL),(9,'Carla  Osorio Velasco  ',NULL,NULL),(10,'David Osorio ',NULL,1),(12,'Lilia Osorio ',NULL,1),(13,'EsaÃºl HernÃ¡ndez RodrÃ­guez ',NULL,1),(14,'EsaÃºl HernÃ¡ndez RodrÃ­guez ',NULL,1),(15,'Paulina Zanella ','',NULL),(16,'Tu gfa ',NULL,NULL),(17,'Kchita Esunaputina ',NULL,NULL),(18,'Oscar Alejandro  Montes de Oca  ',NULL,1),(19,'Daniel Osorio ',NULL,1),(20,'Guillermo CachÃº ',NULL,1);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacto_clientes`
--

DROP TABLE IF EXISTS `contacto_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacto_clientes` (
  `id_contacto_cliente` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cliente` bigint(20) DEFAULT NULL,
  `telefono_cliente` varchar(50) DEFAULT NULL,
  `correo_cliente` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_contacto_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacto_clientes`
--

LOCK TABLES `contacto_clientes` WRITE;
/*!40000 ALTER TABLE `contacto_clientes` DISABLE KEYS */;
INSERT INTO `contacto_clientes` VALUES (1,1,'4771319246','gcachu.o@gmail.com'),(2,2,'4612275316','fluknx@gmail.com'),(3,3,'4771319246','lic_laura@live.com.mx'),(4,4,'9811049218','hyrule_95@hotmail.com'),(5,5,'3333987178','alberto_opl@outlook.com'),(6,6,'3334960843','edaclaim@gmail.com'),(7,7,'4772738611','eduardo.gm.300@gmail.com'),(8,8,'4771236850','horacio_caro0524@hotmail.com'),(9,9,'4771563195','carla.osorio.velasco@gmail.com'),(10,10,'0000000000','osorion_d@hotmail.com'),(11,11,'0000000000','lileon@hotmail.com'),(12,12,'0000000000','lileon123@hotmail.com'),(13,13,'4772099485','esaulhr@gmail.com'),(14,14,'4772099485','applexamx@gmail.com'),(15,15,'4771217717','paulinaznll@gmail.com'),(16,16,'1235551234','nadien@gmail.com'),(17,17,'1236549788','kshibombo@gmail.com'),(18,18,'4773846200','alexander_z28@hotmail.com'),(19,19,'0000000000','danieloso74@gmail.com'),(20,20,'0000000000','gcachub@hotmail.com');
/*!40000 ALTER TABLE `contacto_clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monedas`
--

DROP TABLE IF EXISTS `monedas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monedas` (
  `id_moneda` bigint(20) NOT NULL,
  `nombre_moneda` varchar(100) NOT NULL,
  `simbolo_moneda` varchar(3) NOT NULL,
  `book_moneda` varchar(7) NOT NULL,
  PRIMARY KEY (`id_moneda`),
  UNIQUE KEY `monedas_nombre_moneda_uindex` (`nombre_moneda`),
  UNIQUE KEY `monedas_book_moneda_uindex` (`book_moneda`),
  UNIQUE KEY `monedas_simbolo_moneda_uindex` (`simbolo_moneda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monedas`
--

LOCK TABLES `monedas` WRITE;
/*!40000 ALTER TABLE `monedas` DISABLE KEYS */;
INSERT INTO `monedas` VALUES (0,'Bitcoin Cash','bch','bch_btc'),(1,'Bitcoin','btc','btc_mxn'),(3,'Ethereum','eth','eth_mxn'),(4,'Litecoin','ltc','ltc_mxn'),(5,'Ripple','xrp','xrp_mxn');
/*!40000 ALTER TABLE `monedas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_llaves`
--

DROP TABLE IF EXISTS `usuario_llaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_llaves` (
  `id_usuario_llave` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) NOT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_usuario_llave`),
  UNIQUE KEY `usuario_llaves_id_usuario_uindex` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_llaves`
--

LOCK TABLES `usuario_llaves` WRITE;
/*!40000 ALTER TABLE `usuario_llaves` DISABLE KEYS */;
INSERT INTO `usuario_llaves` VALUES (2,1,'m6/KuuQ/XaI9Aw==','vvG0rONybMFNfxbyjOO+fRJoY4GjanJZqLROz56unhU='),(4,4,'49XQMV5V/6DFbw==','u/iJBjoPm6+UO6z906va5LoJzcfpNKgy/Xj14HacjZ0='),(7,5,'SBasKNdF8DDB8w==','MD7tIIVto2TPjDEieg9LVJaarlFXjR7UnqoMRFj4U3M='),(8,6,'o+yvXNRoacJ2kA==','tff0Oq1jebJ20uSbBnHvzoU3PdNwDH70cmpx1y3t9MU='),(10,8,'oAlySFIfaxs79g==','8Vp7bEdGDmwN2nMblT8kHM3ZD/0jWGDRXjOc3UcePvo='),(15,15,'+w5RX56D1XpDdA==','2XQGaNT7ij51PMnQ4uXYCZRzP5AZ/NVrJKChiCdmOAE=');
/*!40000 ALTER TABLE `usuario_llaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_monedas`
--

DROP TABLE IF EXISTS `usuario_monedas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_monedas` (
  `id_usuario_monedas` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) NOT NULL,
  `id_moneda` bigint(20) NOT NULL,
  `cantidad_usuario_moneda` decimal(10,8) NOT NULL,
  `costo_usuario_moneda` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_usuario_monedas`),
  UNIQUE KEY `usuario_monedas_id_usuario_id_moneda_pk` (`id_usuario`,`id_moneda`)
) ENGINE=InnoDB AUTO_INCREMENT=27458 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_monedas`
--

LOCK TABLES `usuario_monedas` WRITE;
/*!40000 ALTER TABLE `usuario_monedas` DISABLE KEYS */;
INSERT INTO `usuario_monedas` VALUES (2714,5,1,0.00430000,1816.62),(2722,5,0,0.03000000,30724.00),(4199,8,3,0.00000000,676.80),(4228,8,1,0.00000000,881.52),(4253,8,0,0.00000000,777.84),(4773,3,4,0.00000000,0.00),(6486,11,1,0.01449793,5000.00),(9197,10,1,0.00000000,0.00),(9199,12,1,0.00774846,1000.00),(9630,7,4,0.16732481,850.00),(10692,7,5,0.00000000,0.00),(12417,14,0,0.00738264,400.00),(13522,18,1,0.01609130,3000.00),(13545,18,0,0.06843859,2000.00),(13547,18,3,0.08955148,1500.00),(13593,7,0,0.14605927,4500.00),(13594,7,3,0.26508635,5500.00),(13693,14,4,0.01038964,50.00),(13946,14,5,1.16279100,50.00),(13948,7,1,0.04017428,8000.00),(13949,10,3,0.00000021,98.53),(13950,12,3,0.00000001,261.08),(15872,14,3,0.00767579,200.00),(15874,14,1,0.00164692,500.00),(15960,3,3,0.37264102,3877.64),(15962,3,0,0.05622071,1310.06),(15963,3,1,-0.00000001,-187.70),(15972,19,1,-0.00000003,43.49),(15973,19,0,0.01081746,398.66),(15974,19,3,0.02816795,557.85),(15995,20,0,0.56962999,15120.00),(15996,20,3,1.00292007,20010.00),(15997,20,1,0.07098428,14870.00),(25431,10,0,0.18631316,4401.47),(25442,12,0,0.24515212,5738.92),(27397,1,5,0.00000000,31.46),(27454,1,0,0.98930000,26319.79),(27455,1,1,0.00000000,16256.34),(27456,1,3,2.04280000,34060.91),(27457,1,4,0.16229439,774.11);
/*!40000 ALTER TABLE `usuario_monedas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-02-12 23:35:02
