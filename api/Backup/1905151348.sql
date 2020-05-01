-- MySQL dump 10.13  Distrib 5.7.26, for Linux (x86_64)
--
-- Host: gcachuo.ml    Database: crypto
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu0.17.10.1

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
-- Table structure for table `monedas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monedas` (
  `id_moneda` varchar(5) NOT NULL,
  `par_moneda` varchar(5) NOT NULL,
  `nombre_moneda` varchar(100) NOT NULL,
  PRIMARY KEY (`id_moneda`),
  UNIQUE KEY `monedas_nombre_moneda_uindex` (`nombre_moneda`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('bat','mxn','BAT');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('bch','mxn','Bitcoin Cash');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('bsv','mxn','Bitcoin SV');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('btc','mxn','Bitcoin');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('eth','mxn','Ethereum');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('gnt','mxn','Golem');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('ltc','mxn','Litecoin');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('mana','mxn','Mana');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('tusd','mxn','True USD');
INSERT INTO `monedas` (`id_moneda`, `par_moneda`, `nombre_moneda`) VALUES ('xrp','mxn','Ripple');

--
-- Table structure for table `usuarios`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` bigint(20) NOT NULL,
  `id_cliente` bigint(20) DEFAULT NULL,
  `perfil_usuario` int(11) NOT NULL DEFAULT '1',
  `nombre_usuario` varchar(100) NOT NULL,
  `correo_usuario` varchar(100) NOT NULL,
  `password_usuario` varchar(255) NOT NULL,
  `last_login_usuario` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuarios_login_usuario_uindex` (`correo_usuario`),
  KEY `usuarios_usuarios_id_usuario_fk` (`id_cliente`),
  CONSTRAINT `usuarios_usuarios_id_usuario_fk` FOREIGN KEY (`id_cliente`) REFERENCES `usuarios` (`id_usuario`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (1,NULL,0,'Memo Cachu','gcachu.o@gmail.com','$2y$10$BgRs0zriuWHwPmX1Hm1OIu75q51aiyOvzKyXfG12AU.KUcBuZtBAG','2019-05-15 12:42:15');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (2,NULL,1,'Eduardo MuÃ±oz','fluknx@gmail.com','$2ovfs3ldGR1U',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (3,NULL,2,'Laura Osorio','lic_laura@live.com.mx','$2MSfsOCWMh.s','2018-10-11 12:39:39');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (4,NULL,1,'Daniel Hernán Cárdenas Mendoza','hyrule_95@hotmail.com','$2Q8pkd0kvoOU',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (5,NULL,1,'Alberto  Orozco Plascencia ','alberto_opl@outlook.com','$2IyMsQBJncnc',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (6,NULL,1,'michelle Alejandra Carrazco Delgado','edaclaim@gmail.com','$2OXZPPfB.v9w',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (7,1,2,'Eduardo Montes de Oca','eduardo.gm.300@gmail.com','$2y$10$hcLfVf7yO4r4ox6N9DiWn.XnITkDOPsyqbo2Hra098EwF9vJ4ajz6','2019-05-15 10:08:33');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (8,NULL,1,'Horacio Caro Aguilar','horacio_caro0524@hotmail.com','$2ysWcLKbjgOg',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (9,NULL,1,'Carla  Osorio Velasco ','carla.osorio.velasco@gmail.com','$2eJDlUhgMM.Y',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (10,1,2,'David Osorio','osorion_d@hotmail.com','$2Tija6ka8NfI','2018-05-15 15:00:54');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (12,NULL,2,'Lilia Osorio','lileon123@hotmail.com','$2DO2Tl8E3zYk',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (13,NULL,1,'','esaulhr@gmail.com','$2YPnffXfTco6',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (14,1,2,'EsaÃºl HernÃ¡ndez RodrÃ­guez','applexamx@gmail.com','$2i1/uJvbcBUc','2019-03-27 10:49:46');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (15,NULL,1,'Paulina Zanella','paulinaznll@gmail.com','$2n5GGqEWRHUE','2018-03-05 19:57:56');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (16,1,2,'Tu gfa','nadien@gmail.com','$2x1WLqfZms8g',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (17,NULL,1,'Kchita Esunaputina','kshibombo@gmail.com','$2eI0tK5DFWjw',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (18,1,2,'Oscar Alejandro  Montes de Oca ','alexander_z28@hotmail.com','$2y$10$hcLfVf7yO4r4ox6N9DiWn.XnITkDOPsyqbo2Hra098EwF9vJ4ajz6','2019-05-13 11:05:40');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (19,1,2,'Daniel Osorio','danieloso74@gmail.com','$2yHqMTJonN7s',NULL);
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (20,1,2,'Guillermo CachÃº','gcachub@hotmail.com','$2e.LuN5pdbdk','2019-05-13 15:49:26');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (21,1,2,'Christian Caballero','isackcaballero19@gmail.com','$2y$10$BjiNZGPt1somSOtBu/JswOOpM7KqS2DEvW5eWhaiGc5xxmo8uPsnq','2019-04-03 11:58:07');
INSERT INTO `usuarios` (`id_usuario`, `id_cliente`, `perfil_usuario`, `nombre_usuario`, `correo_usuario`, `password_usuario`, `last_login_usuario`) VALUES (22,1,2,'Angel Cachu','anncachu@gmail.com','$2y$10$/vtJiMi2gpNVMh0kroAz.eJL.oo4tqj4OMXOSW1kAiof8EtOYqnQG','2018-11-22 00:53:26');

--
-- Table structure for table `usuarios_transacciones`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_transacciones` (
  `id_usuario_transaccion` bigint(20) NOT NULL,
  `id_usuario` bigint(20) NOT NULL,
  `id_moneda` varchar(5) NOT NULL,
  `costo_usuario_moneda` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cantidad_usuario_moneda` decimal(12,8) NOT NULL,
  `fecha_usuario_transaccion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario_transaccion`),
  KEY `usuarios_transacciones_monedas_id_moneda_fk` (`id_moneda`),
  KEY `usuarios_transacciones_usuarios_id_usuario_fk` (`id_usuario`),
  CONSTRAINT `usuarios_transacciones_monedas_id_moneda_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`),
  CONSTRAINT `usuarios_transacciones_usuarios_id_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_transacciones`
--

INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (1,5,'btc',1816.62,0.03000000,'2018-11-14 21:58:33');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (2,5,'bch',30724.00,0.00430000,'2018-11-14 21:58:36');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (3,7,'bch',5500.00,0.26508635,'2018-11-14 21:58:30');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (4,7,'ltc',850.00,0.16732481,'2018-11-14 21:58:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (5,7,'eth',4500.00,0.14605927,'2018-11-14 21:58:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (6,7,'btc',11350.00,0.05780472,'2018-11-14 21:58:32');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (7,10,'bch',4500.00,0.02642130,'2018-11-14 21:58:33');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (9,12,'eth',261.08,0.00000001,'2018-11-14 21:58:37');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (10,12,'bch',3705.77,0.00000028,'2018-11-14 21:58:37');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (11,14,'btc',50.00,0.00164692,'2018-11-14 21:58:36');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (12,14,'bch',50.00,0.00738264,'2018-11-14 21:58:35');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (13,14,'ltc',400.00,0.01038960,'2018-11-14 21:58:35');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (14,14,'xrp',200.00,1.16278994,'2018-11-14 21:58:30');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (15,14,'eth',500.00,0.00767579,'2018-11-14 21:58:35');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (16,18,'btc',6500.00,0.03991249,'2018-11-14 21:58:32');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (17,18,'bch',2000.00,0.06843859,'2018-11-14 21:58:32');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (18,18,'eth',1500.00,0.08955148,'2018-11-14 21:58:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (19,19,'bch',398.66,0.01081746,'2018-11-14 21:58:34');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (20,19,'eth',557.84,0.02816740,'2018-11-14 21:58:33');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (22,20,'eth',27880.00,1.43184996,'2018-11-14 21:55:51');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (23,20,'bch',22120.00,0.79886800,'2018-11-14 21:58:30');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (24,21,'bch',101.24,0.00682652,'2018-11-14 21:58:35');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (25,21,'btc',102.84,0.00030794,'2018-11-14 21:58:37');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (26,21,'eth',45.92,0.01276160,'2018-11-14 21:58:34');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (27,22,'btc',518.12,0.00326217,'2018-11-14 21:58:36');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (28,22,'eth',514.70,0.06753640,'2018-11-14 21:58:32');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (29,22,'bch',467.18,0.03613200,'2018-11-14 21:58:33');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (30,1,'btc',220.00,0.00173462,'2018-11-21 21:19:49');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (31,1,'eth',220.00,0.05161929,'2018-11-21 21:19:59');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (32,1,'xrp',220.00,20.95556400,'2018-11-22 00:47:37');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (33,1,'tusd',220.00,11.11000000,'2018-11-22 00:49:12');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (34,1,'ltc',220.00,0.20560949,'2018-11-22 00:50:19');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (36,1,'gnt',220.00,66.16897366,'2018-11-22 00:51:30');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (38,1,'bat',220.00,33.12360517,'2018-11-22 00:54:21');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (39,1,'bch',220.00,0.01780077,'2018-11-22 00:54:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (40,1,'mana',220.00,141.79082980,'2018-11-22 00:59:08');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (41,1,'btc',220.00,0.00248942,'2018-11-22 15:42:18');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (42,1,'eth',220.00,0.08567006,'2018-11-22 15:46:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (43,1,'xrp',220.00,25.32676700,'2018-11-22 15:46:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (44,1,'ltc',220.00,0.32971791,'2018-11-22 15:46:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (45,1,'tusd',59.68,2.97000000,'2018-11-22 15:46:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (46,1,'tusd',380.32,18.92000000,'2018-11-22 15:46:49');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (47,1,'mana',220.00,170.75781250,'2018-11-22 15:46:49');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (48,1,'gnt',220.00,107.66995075,'2018-11-22 15:46:49');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (49,1,'bat',220.00,63.17052024,'2018-11-22 15:46:50');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (50,1,'tusd',-220.00,-11.20000000,'2018-11-23 17:21:06');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (51,1,'bch',7.60,0.00198700,'2018-11-23 17:21:07');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (52,1,'bch',212.40,0.05275485,'2018-11-23 17:21:07');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (53,7,'eth',1500.00,0.62201566,'2018-11-30 00:58:53');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (61,1,'eth',348.35,0.14575123,'2018-11-30 18:20:23');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (62,1,'eth',348.34,0.14575123,'2018-11-30 18:20:23');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (63,1,'eth',483.31,0.20175416,'2018-11-30 18:20:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (64,1,'btc',1180.00,0.01419791,'2018-11-30 18:20:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (65,1,'bch',1180.00,0.32216073,'2018-11-30 18:20:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (66,1,'bch',-179.86,-0.05172542,'2018-11-30 18:20:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (67,1,'bch',-115.14,-0.03311135,'2018-11-30 18:20:25');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (68,1,'btc',-59.61,-0.00073530,'2018-11-30 18:20:25');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (69,1,'btc',-235.39,-0.00290460,'2018-11-30 18:20:25');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (70,1,'eth',-295.00,-0.12888149,'2018-11-30 18:20:26');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (71,1,'tusd',885.00,42.33000000,'2018-11-30 18:20:26');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (72,1,'tusd',-704.06,-34.40000000,'2018-12-05 18:09:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (73,1,'tusd',-20.47,-1.00000000,'2018-12-05 18:09:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (74,1,'tusd',-160.47,-7.84000000,'2018-12-05 18:09:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (75,1,'mana',885.00,703.39800000,'2018-12-05 18:09:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (76,1,'mana',-650.00,-584.15414480,'2018-12-10 20:28:29');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (77,1,'bch',650.00,0.30928218,'2018-12-10 20:28:29');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (78,1,'btc',-240.00,-0.00347301,'2018-12-11 00:01:58');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (79,1,'gnt',136.00,99.35000000,'2018-12-11 00:01:59');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (80,1,'gnt',104.00,75.97352942,'2018-12-11 00:01:59');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (81,1,'bch',-300.00,-0.07954741,'2018-12-20 20:01:57');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (82,1,'bat',300.00,103.85017421,'2018-12-20 20:01:58');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (83,1,'bch',-700.00,-0.18561062,'2018-12-20 20:05:18');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (84,1,'xrp',350.00,46.36333300,'2018-12-20 20:05:18');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (85,1,'ltc',350.00,0.53360700,'2018-12-20 20:05:19');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (86,1,'mana',417.43,432.00000000,'2018-12-28 15:30:04');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (87,1,'gnt',454.45,350.00000000,'2018-12-28 15:30:04');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (88,1,'bat',523.40,200.00000000,'2018-12-28 15:30:04');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (89,1,'btc',821.42,0.01130904,'2018-12-28 15:32:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (90,1,'tusd',83.30,4.09000000,'2018-12-28 16:57:05');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (91,1,'bch',-200.00,-0.05978869,'2018-12-28 17:12:45');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (92,1,'tusd',200.00,9.83000000,'2018-12-28 17:12:46');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (94,7,'bsv',0.00,0.26508635,'2019-01-07 17:40:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (95,20,'bsv',0.00,0.79886800,'2019-01-07 17:40:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (96,18,'bsv',0.00,0.06843859,'2019-01-07 17:40:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (97,10,'bsv',0.00,0.02642130,'2019-01-07 17:40:41');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (98,22,'bsv',0.00,0.03613200,'2019-01-07 17:40:41');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (99,19,'bsv',0.00,0.01081746,'2019-01-07 17:40:41');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (100,14,'bsv',0.00,0.00738264,'2019-01-07 17:40:41');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (101,21,'bsv',0.00,0.00682652,'2019-01-07 17:40:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (102,5,'bsv',0.00,0.00430000,'2019-01-07 17:40:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (103,12,'bsv',0.00,0.00000028,'2019-01-07 17:40:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (104,1,'bsv',0.00,0.01779676,'2019-01-07 18:11:01');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (105,18,'bsv',0.00,-0.06843859,'2019-01-10 19:44:45');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (106,7,'bsv',0.00,0.06843859,'2019-01-10 19:44:45');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (107,1,'eth',2491.10,1.11342549,'2019-01-31 16:56:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (108,1,'eth',0.00,-0.01024151,'2019-01-31 20:37:51');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (109,1,'btc',0.00,-0.00009843,'2019-01-31 20:38:50');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (111,1,'eth',-500.00,-0.25054327,'2019-01-31 23:01:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (112,1,'mana',481.66,736.20750250,'2019-01-31 23:01:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (113,1,'eth',-550.00,-0.27577058,'2019-01-31 23:04:03');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (114,1,'xrp',523.15,87.35433600,'2019-01-31 23:04:04');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (115,1,'eth',-900.00,-0.45155561,'2019-01-31 23:07:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (116,1,'bat',46.08,21.09721342,'2019-01-31 23:07:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (117,1,'bat',827.28,378.75848696,'2019-01-31 23:07:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (118,1,'eth',71.81,0.03531843,'2019-01-31 23:29:59');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (119,1,'ltc',0.00,-0.00147278,'2019-02-19 16:57:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (120,1,'ltc',-289.86,-0.31746162,'2019-02-19 16:59:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (121,1,'tusd',102.92,5.29000000,'2019-02-19 17:01:02');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (122,1,'tusd',186.93,9.64000000,'2019-02-19 17:01:03');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (124,1,'bat',-95.13,-36.00000000,'2019-02-19 17:10:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (125,1,'bat',-49.67,-18.79699249,'2019-02-19 17:10:10');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (126,1,'bat',-645.56,-245.20300751,'2019-02-19 17:10:10');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (127,1,'tusd',561.96,28.94000000,'2019-02-19 17:12:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (128,1,'tusd',69.82,3.59000000,'2019-02-19 17:12:32');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (129,1,'tusd',158.58,8.30000000,'2019-02-19 17:12:32');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (130,1,'tusd',-564.00,-30.00000000,'2019-02-25 16:06:06');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (131,1,'tusd',-544.24,-28.98000000,'2019-02-25 16:06:06');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (132,1,'tusd',-19.15,-1.02000000,'2019-02-25 16:06:06');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (133,1,'eth',5057.59,1.94172469,'2019-03-04 16:51:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (134,1,'eth',0.00,-0.00046382,'2019-03-04 17:27:41');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (135,1,'eth',-1100.00,-0.45629187,'2019-03-04 17:45:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (136,1,'tusd',1120.07,57.41000000,'2019-03-04 17:45:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (137,1,'eth',-3000.00,-1.24557460,'2019-03-04 17:47:46');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (138,1,'eth',8217.08,2.72444324,'2019-03-05 17:06:21');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (139,1,'eth',-8050.00,-3.12893831,'2019-03-05 17:32:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (140,1,'tusd',-1200.00,-63.07000000,'2019-03-05 21:23:26');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (141,1,'eth',1271.69,0.38081179,'2019-03-05 21:24:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (142,1,'bat',-337.09,-90.00000000,'2019-03-08 18:20:01');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (143,1,'ltc',-194.75,-0.18000000,'2019-03-08 18:21:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (144,1,'gnt',454.94,345.03253091,'2019-03-08 18:24:50');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (145,1,'gnt',76.21,57.80501526,'2019-03-08 18:24:50');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (146,1,'tusd',904.58,46.11000000,'2019-03-13 18:40:00');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (147,1,'tusd',96.10,4.96000000,'2019-03-13 18:40:00');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (148,1,'tusd',0.00,0.02000000,'2019-03-13 18:40:46');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (149,1,'xrp',1000.00,165.30782000,'2019-03-18 02:44:49');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (150,1,'bat',0.00,-0.48149393,'2019-03-18 06:28:56');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (151,1,'bat',-515.14,-140.51850607,'2019-03-18 06:31:18');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (152,1,'btc',275.07,0.00358035,'2019-03-18 06:35:00');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (153,1,'xrp',-7.79,-1.30782000,'2019-03-18 06:36:36');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (154,1,'tusd',232.63,12.00000000,'2019-03-18 06:40:20');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (155,1,'ltc',-190.68,-0.17000000,'2019-03-18 06:42:30');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (156,1,'tusd',193.86,10.00000000,'2019-03-18 06:43:54');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (157,1,'bch',0.00,-0.00003786,'2019-03-18 09:09:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (158,1,'bch',-489.31,-0.16416418,'2019-03-18 09:11:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (159,1,'gnt',500.60,343.00000000,'2019-03-18 09:13:38');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (160,1,'tusd',-450.00,-23.93000000,'2019-03-21 01:53:50');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (161,1,'eth',1000.64,0.38090000,'2019-03-22 15:34:41');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (162,1,'gnt',-91.86,-58.80646483,'2019-03-25 15:20:23');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (163,1,'gnt',-277.00,-177.32455500,'2019-03-25 15:20:23');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (164,1,'gnt',-131.01,-83.86898017,'2019-03-25 15:20:23');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (165,1,'tusd',-500.00,-26.39000000,'2019-03-22 21:25:43');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (166,1,'gnt',-42.78,-26.87500000,'2019-03-25 19:53:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (167,1,'tusd',542.67,28.08000000,'2019-03-25 19:55:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (168,1,'gnt',-590.59,-398.96254619,'2019-03-26 16:48:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (169,1,'eth',-531.05,-0.21271998,'2019-03-26 16:52:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (170,1,'eth',-893.72,-0.35800000,'2019-03-26 16:52:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (171,1,'eth',-300.83,-0.12053998,'2019-03-26 16:52:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (172,1,'eth',-177.79,-0.07124275,'2019-03-26 16:52:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (173,1,'tusd',-206.00,-10.96000000,'2019-03-26 16:53:34');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (174,1,'mana',-498.31,-491.00000000,'2019-03-28 15:45:57');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (175,1,'btc',498.14,0.00662000,'2019-03-28 15:50:17');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (176,1,'btc',-880.83,-0.01020000,'2019-04-02 15:30:01');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (177,1,'eth',440.47,0.15280000,'2019-04-02 15:32:05');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (178,1,'xrp',161.58,25.93471900,'2019-04-02 15:33:18');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (179,1,'xrp',278.91,44.76528000,'2019-04-02 15:33:37');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (180,1,'xrp',0.00,0.00000100,'2019-04-03 00:42:30');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (181,1,'xrp',-800.02,-118.42000000,'2019-04-03 00:44:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (182,1,'eth',213.40,0.06709407,'2019-04-03 00:45:35');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (183,1,'eth',586.63,0.18449593,'2019-04-03 00:45:35');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (184,1,'btc',0.00,-0.00010701,'2019-04-03 16:04:32');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (185,1,'btc',-500.92,-0.00543000,'2019-04-03 16:07:53');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (186,1,'eth',500.93,0.15577000,'2019-04-03 16:09:17');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (187,1,'bat',-490.95,-85.49653980,'2019-04-06 00:53:48');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (188,1,'eth',490.96,0.15633614,'2019-04-06 00:56:56');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (189,1,'btc',-485.88,-0.00517201,'2019-04-06 00:58:07');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (190,1,'eth',485.89,0.15472269,'2019-04-06 00:58:38');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (191,1,'gnt',0.00,-0.91885639,'2019-04-08 05:21:10');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (192,1,'gnt',-347.32,-184.00000000,'2019-04-08 05:22:23');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (193,1,'gnt',-6.12,-3.24359742,'2019-04-08 05:22:23');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (194,1,'mana',0.00,-1.37298943,'2019-04-08 05:25:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (195,1,'mana',-68.93,-61.40481612,'2019-04-08 05:25:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (196,1,'mana',-193.34,-172.22219445,'2019-04-08 05:25:24');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (197,1,'btc',0.00,-0.00004993,'2019-04-08 05:54:18');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (198,1,'btc',-173.69,-0.00176105,'2019-04-08 05:54:18');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (199,1,'eth',789.42,0.22434531,'2019-04-08 05:57:28');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (200,1,'bat',0.00,5.70000000,'2019-04-09 22:24:00');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (201,1,'bat',-504.85,-63.20346020,'2019-04-23 23:44:43');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (203,1,'eth',504.85,0.15400843,'2019-04-23 23:48:37');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (204,1,'eth',0.00,-0.00380584,'2019-04-24 00:04:29');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (205,1,'eth',3033.11,0.86927671,'2019-05-03 17:25:07');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (206,1,'eth',0.00,-0.01000000,'2019-05-03 17:33:58');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (207,1,'eth',1000.00,0.32471989,'2019-05-04 15:01:45');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (208,1,'eth',-5635.00,-1.85123429,'2019-05-05 15:01:45');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (209,1,'tusd',2300.00,119.82000000,'2019-05-07 16:42:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (210,1,'xrp',2000.00,349.82394300,'2019-05-07 16:43:43');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (211,1,'eth',1300.00,0.38909374,'2019-05-07 16:44:31');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (213,1,'tusd',-300.00,-15.68000000,'2019-05-09 19:56:09');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (214,1,'bat',297.14,53.00000000,'2019-05-09 19:56:26');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (215,1,'btc',-174.35,-0.00150000,'2019-05-09 20:07:40');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (217,1,'mana',177.15,200.00000000,'2019-05-09 20:09:51');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (222,1,'ltc',-141.01,-0.10000000,'2019-05-09 21:57:33');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (223,1,'mana',140.83,159.00000000,'2019-05-09 22:01:10');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (224,1,'btc',240.51,0.00199640,'2019-05-09 23:39:36');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (225,1,'btc',240.51,0.00199640,'2019-05-09 23:39:43');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (226,1,'btc',1755.30,0.01457000,'2019-05-09 23:40:02');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (227,1,'tusd',-2236.08,-116.92000000,'2019-05-09 23:40:45');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (228,1,'mana',-55.55,-58.86660836,'2019-05-13 14:32:52');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (229,1,'mana',-9.93,-10.52631579,'2019-05-13 14:33:03');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (230,1,'mana',-4.96,-5.31914894,'2019-05-13 14:33:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (231,1,'mana',-9.93,-10.63829788,'2019-05-13 14:33:25');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (232,1,'mana',-9.93,-10.63829788,'2019-05-13 14:33:28');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (233,1,'mana',-118.27,-128.01133115,'2019-05-13 14:33:41');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (234,1,'bat',-252.53,-37.00000000,'2019-05-13 14:33:54');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (235,1,'tusd',-985.19,-52.00000000,'2019-05-13 14:42:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (236,1,'btc',723.18,0.00501382,'2019-05-13 14:43:54');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (237,1,'eth',723.17,0.19233965,'2019-05-13 14:45:11');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (238,1,'xrp',-7.96,-1.27591700,'2019-05-13 21:00:38');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (239,1,'xrp',-4.29,-0.68907500,'2019-05-13 21:00:47');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (240,1,'xrp',-6.27,-1.00654200,'2019-05-13 21:00:58');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (241,1,'xrp',-605.97,-97.13240700,'2019-05-13 21:01:07');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (242,1,'btc',-1000.42,-0.00649655,'2019-05-13 22:27:46');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (243,1,'eth',1624.93,0.41394157,'2019-05-13 22:28:46');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (245,1,'xrp',-626.85,-100.00000000,'2019-05-14 14:31:13');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (246,1,'xrp',-137.95,-18.10000000,'2019-05-14 14:38:53');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (247,1,'xrp',-689.00,-90.40000000,'2019-05-14 14:39:08');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (248,1,'xrp',-171.48,-22.50000000,'2019-05-14 14:39:19');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (249,1,'eth',1625.30,0.40930740,'2019-05-14 14:47:55');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (250,1,'xrp',-998.66,-136.00000000,'2019-05-14 16:07:17');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (251,1,'tusd',987.39,51.33000000,'2019-05-14 16:08:42');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (252,1,'tusd',11.26,0.58000000,'2019-05-14 16:08:50');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (253,1,'xrp',-384.56,-50.00000000,'2019-05-14 16:57:38');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (254,1,'tusd',384.56,19.90000000,'2019-05-14 16:58:29');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (255,1,'btc',-1938.64,-0.01281237,'2019-05-14 18:49:56');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (256,1,'tusd',491.65,25.44000000,'2019-05-14 18:51:45');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (257,1,'tusd',508.34,26.29000000,'2019-05-14 18:51:55');
INSERT INTO `usuarios_transacciones` (`id_usuario_transaccion`, `id_usuario`, `id_moneda`, `costo_usuario_moneda`, `cantidad_usuario_moneda`, `fecha_usuario_transaccion`) VALUES (259,1,'eth',938.62,0.23427200,'2019-05-14 18:53:58');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-05-15 13:48:31
