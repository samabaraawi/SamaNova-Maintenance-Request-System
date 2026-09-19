-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: ticketsystem
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` enum('manager','customer','staff') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1001,'System Manager','manger@gmail.com','comp@334','manager'),(1002,'Lina Nasser','lina.manager@gmail.com','manager123','manager'),(1003,'Omar Khalil','omar.manager@gmail.com','manager456','manager'),(2001,'Test Customer','cust@gmail.com','comp#334','customer'),(2002,'Ahmad Saleh','ahmad.customer@gmail.com','cust2002','customer'),(2003,'Noor Hamdan','noor.customer@gmail.com','cust2003','customer'),(2004,'Sara Ali','sara.customer@gmail.com','cust2004','customer'),(2005,'Tareq Mansour','tareq.customer@gmail.com','cust2005','customer'),(2006,'Dana Hassan','dana.customer@gmail.com','cust2006','customer'),(2007,'Rami Issa','rami.customer@gmail.com','cust2007','customer'),(2008,'Hala Yasin','hala.customer@gmail.com','cust2008','customer'),(2009,'Sami Odeh','sami.customer@gmail.com','cust2009','customer'),(2010,'Reem Qasem','reem.customer@gmail.com','cust2010','customer'),(3001,'Khaled Electrician','khaled.staff@gmail.com','staff3001','staff'),(3002,'Maya Plumber','maya.staff@gmail.com','staff3002','staff'),(3003,'Samer Technician','samer.staff@gmail.com','staff3003','staff'),(3004,'Dalia Carpenter','dalia.staff@gmail.com','staff3004','staff'),(3005,'Yazan HVAC Technician','yazan.staff@gmail.com','staff3005','staff'),(3006,'Rawan Maintenance','rawan.staff@gmail.com','staff3006','staff'),(3007,'Fadi Technician','fadi.staff@gmail.com','staff3007','staff'),(3008,'Lama Electrician','lama.staff@gmail.com','staff3008','staff'),(3009,'Nidal Plumber','nidal.staff@gmail.com','staff3009','staff'),(3010,'Haneen Maintenance','haneen.staff@gmail.com','staff3010','staff');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `ticket_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `contact_email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `submitted_date` date NOT NULL,
  `status` enum('Pending','Assigned','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `emergency_level` enum('Low','Medium','High') COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_date` date DEFAULT NULL,
  `assigned_staff_id` int DEFAULT NULL,
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `fk_ticket_customer` (`customer_id`),
  KEY `fk_ticket_staff` (`assigned_staff_id`),
  CONSTRAINT `fk_ticket_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_ticket_staff` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (101,2001,'cust@gmail.com','Room A101','The air conditioner is not cooling the room.','2026-09-01','Pending','High',NULL,NULL,NULL),(102,2002,'ahmad.customer@gmail.com','Computer Lab 2','Several electrical outlets are not working.','2026-09-01','Pending','High',NULL,NULL,NULL),(103,2003,'noor.customer@gmail.com','Office B205','The ceiling light keeps flickering.','2026-08-31','Pending','Medium',NULL,NULL,NULL),(104,2004,'sara.customer@gmail.com','Main Building - Floor 1','Water is leaking from the bathroom sink.','2026-08-30','Assigned','High','2026-09-05',3009,NULL),(105,2005,'tareq.customer@gmail.com','Room C110','The door lock is damaged and cannot be closed.','2026-08-29','Pending','Medium',NULL,NULL,NULL),(106,2001,'cust@gmail.com','Room A102','The projector does not turn on.','2026-08-28','Assigned','Medium','2026-08-29',3003,NULL),(107,2006,'dana.customer@gmail.com','Library - Floor 2','The reading table has a broken leg.','2026-08-27','Assigned','Low','2026-08-28',3004,NULL),(108,2007,'rami.customer@gmail.com','Science Lab','The water faucet cannot be turned off.','2026-08-26','Assigned','High','2026-08-26',3002,NULL),(109,2008,'hala.customer@gmail.com','Administration Office','The air conditioning unit is making a loud noise.','2026-08-25','Assigned','Medium','2026-08-26',3005,NULL),(110,2001,'cust@gmail.com','Room A103','A damaged light switch needs replacement.','2026-08-22','Completed','Low','2026-08-23',3001,NULL),(111,2009,'sami.customer@gmail.com','Lecture Hall 3','The microphone connection is not working.','2026-08-21','Completed','Medium','2026-08-22',3007,NULL),(112,2010,'reem.customer@gmail.com','Student Lounge','The sink drain was blocked.','2026-08-20','Completed','Low','2026-08-21',3009,NULL),(113,2001,'cust@gmail.com','Computer Lab 3','The computer does not turn on','2026-09-05','Pending','High',NULL,NULL,'113.jpeg');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ticketsystem'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-08  2:53:12