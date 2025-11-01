-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: sweettooth
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `approval_requests`
--

DROP TABLE IF EXISTS `approval_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `setting_type` varchar(50) NOT NULL,
  `proposed_value` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `super_admin_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_requests_branch_id_foreign` (`branch_id`),
  KEY `approval_requests_super_admin_id_foreign` (`super_admin_id`),
  CONSTRAINT `approval_requests_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_requests_super_admin_id_foreign` FOREIGN KEY (`super_admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_requests`
--

LOCK TABLES `approval_requests` WRITE;
/*!40000 ALTER TABLE `approval_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `approval_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approved_items`
--

DROP TABLE IF EXISTS `approved_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approved_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `approved_by` char(36) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units','bags','cartons') NOT NULL,
  `approved_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','dispatched') NOT NULL DEFAULT 'pending',
  `shift` enum('morning','afternoon','night') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approved_items_request_id_foreign` (`request_id`),
  KEY `approved_items_item_id_foreign` (`item_id`),
  KEY `approved_items_branch_id_foreign` (`branch_id`),
  CONSTRAINT `approved_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approved_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `approved_items_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `item_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approved_items`
--

LOCK TABLES `approved_items` WRITE;
/*!40000 ALTER TABLE `approved_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `approved_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `setting_type` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_branch_id_foreign` (`branch_id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_accounting_cashes`
--

DROP TABLE IF EXISTS `branch_accounting_cashes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_accounting_cashes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `local_expenses` varchar(50) DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `cash_transactions` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_accounting_cashes_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_accounting_cashes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_accounting_cashes`
--

LOCK TABLES `branch_accounting_cashes` WRITE;
/*!40000 ALTER TABLE `branch_accounting_cashes` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_accounting_cashes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_business_configurations`
--

DROP TABLE IF EXISTS `branch_business_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_business_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `logo_upload` varchar(50) DEFAULT NULL,
  `contact_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`contact_details`)),
  `business_type` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`business_type`)),
  `storage_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`storage_settings`)),
  `subscription_plan` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_business_configurations_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_business_configurations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_business_configurations`
--

LOCK TABLES `branch_business_configurations` WRITE;
/*!40000 ALTER TABLE `branch_business_configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_business_configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_currency_localizations`
--

DROP TABLE IF EXISTS `branch_currency_localizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_currency_localizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `currency_display` varchar(50) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `units_local` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_currency_localizations_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_currency_localizations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_currency_localizations`
--

LOCK TABLES `branch_currency_localizations` WRITE;
/*!40000 ALTER TABLE `branch_currency_localizations` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_currency_localizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_customer_supplier_managements`
--

DROP TABLE IF EXISTS `branch_customer_supplier_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_customer_supplier_managements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `local_customers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`local_customers`)),
  `local_suppliers` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_customer_supplier_managements_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_customer_supplier_managements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_customer_supplier_managements`
--

LOCK TABLES `branch_customer_supplier_managements` WRITE;
/*!40000 ALTER TABLE `branch_customer_supplier_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_customer_supplier_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_employee_managements`
--

DROP TABLE IF EXISTS `branch_employee_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_employee_managements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `branch_staff` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`branch_staff`)),
  `permissions_local` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions_local`)),
  `pin_assign` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_employee_managements_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_employee_managements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_employee_managements`
--

LOCK TABLES `branch_employee_managements` WRITE;
/*!40000 ALTER TABLE `branch_employee_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_employee_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_inventory_managements`
--

DROP TABLE IF EXISTS `branch_inventory_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_inventory_managements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `local_stock` varchar(50) DEFAULT NULL,
  `stock_adjustment` varchar(50) DEFAULT NULL,
  `purchase_returns` varchar(50) DEFAULT NULL,
  `supplier_link` varchar(50) DEFAULT NULL,
  `low_stock_alert` varchar(50) DEFAULT NULL,
  `csv_import` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_inventory_managements_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_inventory_managements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_inventory_managements`
--

LOCK TABLES `branch_inventory_managements` WRITE;
/*!40000 ALTER TABLE `branch_inventory_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_inventory_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_managements`
--

DROP TABLE IF EXISTS `branch_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_managements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `branch_details` varchar(50) DEFAULT NULL,
  `operating_hours` varchar(50) DEFAULT NULL,
  `tax_override` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_managements_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_managements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_managements`
--

LOCK TABLES `branch_managements` WRITE;
/*!40000 ALTER TABLE `branch_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_notifications_alerts`
--

DROP TABLE IF EXISTS `branch_notifications_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_notifications_alerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `alerts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`alerts`)),
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_notifications_alerts_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_notifications_alerts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_notifications_alerts`
--

LOCK TABLES `branch_notifications_alerts` WRITE;
/*!40000 ALTER TABLE `branch_notifications_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_notifications_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_pos_configurations`
--

DROP TABLE IF EXISTS `branch_pos_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_pos_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `pos_use` varchar(50) DEFAULT NULL,
  `payment_modes` varchar(50) DEFAULT NULL,
  `receipt_custom` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_pos_configurations_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_pos_configurations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_pos_configurations`
--

LOCK TABLES `branch_pos_configurations` WRITE;
/*!40000 ALTER TABLE `branch_pos_configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_pos_configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_reports_analytics`
--

DROP TABLE IF EXISTS `branch_reports_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_reports_analytics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `branch_reports` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`branch_reports`)),
  `date_filter` varchar(50) DEFAULT NULL,
  `export` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_reports_analytics_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_reports_analytics_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_reports_analytics`
--

LOCK TABLES `branch_reports_analytics` WRITE;
/*!40000 ALTER TABLE `branch_reports_analytics` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_reports_analytics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_security_accesses`
--

DROP TABLE IF EXISTS `branch_security_accesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_security_accesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `local_logs` varchar(50) DEFAULT NULL,
  `is_overridden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_security_accesses_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_security_accesses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_security_accesses`
--

LOCK TABLES `branch_security_accesses` WRITE;
/*!40000 ALTER TABLE `branch_security_accesses` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_security_accesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `manager_user_id` char(36) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_name_unique` (`name`),
  UNIQUE KEY `branches_code_unique` (`code`),
  UNIQUE KEY `branches_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES ('019a2f19-bde1-73ae-b1f7-81d61b668e39','SweetTooth Calabar','CAL-001','12 Marian Road, Calabar Municipal','+234-809-012-3456','calabar@sweettooth.com','Calabar flagship store with full production and sales',NULL,'Nigeria','Cross River','Calabar','540001','Africa/Lagos',1,'2025-10-29 07:33:27','2025-10-29 07:33:27',NULL),('019a2f19-be9d-70d3-80ee-fb41b8a27a7c','SweetTooth Port Harcourt','PHC-002','78 Trans Amadi Industrial Layout','+234-803-456-7890','portharcourt@sweettooth.com','Port Harcourt main branch',NULL,'Nigeria','Rivers','Port Harcourt','500001','Africa/Lagos',1,'2025-10-29 07:33:27','2025-10-29 07:33:27',NULL),('019a2f19-bf8a-713c-b295-f1c53d78fb12','SweetTooth Lagos','LAG-003','45 Admiralty Way, Lekki Phase 1','+234-801-234-5678','lagos@sweettooth.com','Lagos head office and production center',NULL,'Nigeria','Lagos','Lagos','101001','Africa/Lagos',1,'2025-10-29 07:33:27','2025-10-29 07:33:27',NULL),('019a2f19-c0c9-71a0-9cb2-881fed1d26ca','SweetTooth Abuja','ABJ-004','23 Gimbiya Street, Area 11, Garki','+234-802-345-6789','abuja@sweettooth.com','Abuja branch with gelato specialty',NULL,'Nigeria','FCT','Abuja','900001','Africa/Lagos',1,'2025-10-29 07:33:28','2025-10-29 07:33:28',NULL),('019a2f19-c0f6-7167-8a54-a58bb08550b7','SweetTooth Enugu','ENU-005','34 Ogui Road, New Haven','+234-806-789-0123','enugu@sweettooth.com','Enugu branch serving South-East region',NULL,'Nigeria','Enugu','Enugu','400001','Africa/Lagos',1,'2025-10-29 07:33:28','2025-10-29 07:33:28',NULL);
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `call_backs`
--

DROP TABLE IF EXISTS `call_backs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `call_backs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint(20) unsigned NOT NULL,
  `callback_type` varchar(255) NOT NULL,
  `reference_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units') NOT NULL,
  `reason` enum('expired','damaged','quality_issue','contaminated','other') NOT NULL,
  `description` text DEFAULT NULL,
  `reported_by` char(36) NOT NULL,
  `callback_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `action_taken` enum('disposed','returned_to_supplier','reprocessed','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `call_backs_shift_id_foreign` (`shift_id`),
  KEY `call_backs_reported_by_foreign` (`reported_by`),
  CONSTRAINT `call_backs_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `call_backs_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `call_backs`
--

LOCK TABLES `call_backs` WRITE;
/*!40000 ALTER TABLE `call_backs` DISABLE KEYS */;
/*!40000 ALTER TABLE `call_backs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clock_ins`
--

DROP TABLE IF EXISTS `clock_ins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clock_ins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `date` datetime NOT NULL,
  `shift` varchar(255) NOT NULL,
  `clock_in_time` datetime NOT NULL,
  `clock-out-time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clock_ins_employee_id_foreign` (`employee_id`),
  CONSTRAINT `clock_ins_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clock_ins`
--

LOCK TABLES `clock_ins` WRITE;
/*!40000 ALTER TABLE `clock_ins` DISABLE KEYS */;
/*!40000 ALTER TABLE `clock_ins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_produces`
--

DROP TABLE IF EXISTS `daily_produces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_produces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint(20) unsigned NOT NULL,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `produce_date` date NOT NULL,
  `shift_type` enum('morning','afternoon') NOT NULL,
  `opening_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `requested_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `produced_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sent_out_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `order_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `callback_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `closing_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `expected_closing` decimal(12,2) NOT NULL DEFAULT 0.00,
  `variance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_produces_shift_id_recipe_id_unique` (`shift_id`,`recipe_id`),
  KEY `daily_produces_recipe_id_foreign` (`recipe_id`),
  CONSTRAINT `daily_produces_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_produces_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_produces`
--

LOCK TABLES `daily_produces` WRITE;
/*!40000 ALTER TABLE `daily_produces` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_produces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_categories`
--

DROP TABLE IF EXISTS `department_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_categories`
--

LOCK TABLES `department_categories` WRITE;
/*!40000 ALTER TABLE `department_categories` DISABLE KEYS */;
INSERT INTO `department_categories` VALUES ('019a2f19-c1cd-7026-82a1-58af7664f8e1','Sales','Departments focused on selling products and services, customer acquisition, and revenue generation.','2025-10-29 07:33:28','2025-10-29 07:33:28'),('019a2f19-c2be-7012-8390-ef3509105df8','Production','Departments responsible for manufacturing, production processes, and quality control.','2025-10-29 07:33:28','2025-10-29 07:33:28'),('019a2f19-c37c-7246-987e-3024c6a2c1d2','Support','Departments providing assistance, customer service, and technical support services.','2025-10-29 07:33:28','2025-10-29 07:33:28');
/*!40000 ALTER TABLE `department_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `category_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_name_unique` (`name`),
  KEY `departments_branch_id_foreign` (`branch_id`),
  KEY `departments_category_id_foreign` (`category_id`),
  CONSTRAINT `departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departments_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `department_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,NULL,'019a2f19-c2be-7012-8390-ef3509105df8','Kitchen','Prepares food for Till, Confectionaries, Corner Store','2025-10-29 07:33:29','2025-10-29 07:33:29'),(2,NULL,'019a2f19-c2be-7012-8390-ef3509105df8','Gelato Production','Makes gelato/ice cream','2025-10-29 07:33:29','2025-10-29 07:33:29'),(3,NULL,'019a2f19-c2be-7012-8390-ef3509105df8','Confectionaries Production','Makes confectionery items','2025-10-29 07:33:29','2025-10-29 07:33:29'),(4,NULL,'019a2f19-c1cd-7026-82a1-58af7664f8e1','Till','Sells ready-made snacks','2025-10-29 07:33:30','2025-10-29 07:33:30'),(5,NULL,'019a2f19-c1cd-7026-82a1-58af7664f8e1','Corner Store','On-demand food sales','2025-10-29 07:33:30','2025-10-29 07:33:30'),(6,NULL,'019a2f19-c1cd-7026-82a1-58af7664f8e1','Confectionaries Sales','Sells confectionery items','2025-10-29 07:33:30','2025-10-29 07:33:30'),(7,NULL,'019a2f19-c37c-7246-987e-3024c6a2c1d2','Inventory/Store','Manages all stock','2025-10-29 07:33:31','2025-10-29 07:33:31'),(8,NULL,'019a2f19-c37c-7246-987e-3024c6a2c1d2','HR','Human resources (corporate level)','2025-10-29 07:33:31','2025-10-29 07:33:31');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` char(36) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `manager_id` char(36) DEFAULT NULL,
  `employee_number` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `hire_date` date NOT NULL,
  `termination_date` date DEFAULT NULL,
  `status` enum('active','inactive','terminated','on_probation','on_leave') NOT NULL DEFAULT 'active',
  `probation_end_date` date DEFAULT NULL,
  `shift_preference` enum('morning','afternoon','night','rotating','flexible') DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `last_performance_review_date` date DEFAULT NULL,
  `performance_rating` decimal(3,1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT 'password',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  UNIQUE KEY `employees_email_unique` (`email`),
  KEY `employees_branch_id_foreign` (`branch_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_manager_id_foreign` (`manager_id`),
  CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES ('0087cce4-5743-35cb-960d-eaf180f130ab','019a2f19-bde1-73ae-b1f7-81d61b668e39',4,NULL,'EMP-CAL001-0013','Folake Williams','folake.williams.13@sweettooth.com','+234-802-791-4525','6622 Mertz Camp, Calabar, Cross River State, Nigeria','2003-07-23','female','Nigerian','Chukwuemeka Bello','+234-843-998-5694','2023-04-29',NULL,'active',NULL,'afternoon',344469.52,NULL,'TIN-58849922','7731265661','Shellfish',NULL,'2025-09-10',4.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('0096b282-ce7b-379e-a979-0402e9195e9e','019a2f19-bf8a-713c-b295-f1c53d78fb12',4,NULL,'EMP-LAG003-0053','Chigozie Bello','chigozie.bello.53@sweettooth.com','+234-869-927-4725','955 Jarred Groves, Lagos, Lagos State, Nigeria','1997-07-19','male','Nigerian','Folake Johnson','+234-854-847-6861','2023-10-16',NULL,'active',NULL,'rotating',282215.45,NULL,'TIN-70877035','6153275921',NULL,NULL,'2025-08-20',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('023e3e2d-1ecf-3c65-b4df-0dd97a18aac6','019a2f19-bf8a-713c-b295-f1c53d78fb12',6,NULL,'EMP-LAG003-0061','Oluwaseun Mohammed','oluwaseun.mohammed.61@sweettooth.com','+234-809-755-9113','44572 Dahlia Inlet, Lagos, Lagos State, Nigeria','2003-09-12','male','Nigerian','Blessing Ogunleye','+234-825-290-6666','2024-02-18',NULL,'active',NULL,'flexible',306333.38,NULL,'TIN-12618041','5609103859','Shellfish',NULL,'2025-09-18',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('06c08dac-d94a-36ac-997b-a94a7f651471','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',1,NULL,'EMP-PHC002-0024','Chioma Okafor','chioma.okafor.24@sweettooth.com','+234-857-958-2233','731 Paul Ports Apt. 526, Port Harcourt, Rivers State, Nigeria','1987-06-08','female','Nigerian','Yusuf Eze','+234-827-749-9465','2023-03-30',NULL,'active',NULL,'morning',176804.09,NULL,'TIN-99293294','9561167427',NULL,NULL,'2025-07-19',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('0ec41491-b751-351e-b390-2f56f1f9f3c2','019a2f19-bf8a-713c-b295-f1c53d78fb12',6,NULL,'EMP-LAG003-0060','Yusuf Johnson','yusuf.johnson.60@sweettooth.com','+234-846-922-7740','16236 Skiles Avenue, Lagos, Lagos State, Nigeria','1997-10-20','male','Nigerian','Folake Eze','+234-804-528-4333','2025-03-03',NULL,'on_probation','2025-11-24','afternoon',85021.19,NULL,'TIN-61294568','5212789447',NULL,NULL,'2025-09-26',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('14ca919e-4a2e-319a-960c-e5dd4f6e679d','019a2f19-bde1-73ae-b1f7-81d61b668e39',2,NULL,'EMP-CAL001-0007','Yusuf Eze','yusuf.eze.7@sweettooth.com','+234-858-739-2252','49883 Heller Mews, Calabar, Cross River State, Nigeria','1990-12-22','male','Nigerian','Blessing Chukwu','+234-845-629-4987','2024-10-23',NULL,'active',NULL,'flexible',228406.12,NULL,'TIN-23187534','0226631737',NULL,NULL,'2025-05-09',3.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('15b12678-ec14-3578-9d33-c76f9f8498f4','019a2f19-bde1-73ae-b1f7-81d61b668e39',3,NULL,'EMP-CAL001-0008','Ibrahim Eze','ibrahim.eze.8@sweettooth.com','+234-850-627-7482','7208 Dagmar Ridge, Calabar, Cross River State, Nigeria','1999-08-27','male','Nigerian','Ngozi Williams','+234-900-947-3264','2023-05-27',NULL,'active',NULL,'afternoon',239081.60,NULL,'TIN-40647634','8127773145','Lactose',NULL,'2025-05-23',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('18fb5f49-1f27-3808-9356-3b7f85b01232','019a2f19-c0f6-7167-8a54-a58bb08550b7',5,NULL,'EMP-ENU005-0100','Nneka Okoro','nneka.okoro.100@sweettooth.com','+234-878-106-3085','312 Kirlin Path Apt. 204, Enugu, Enugu State, Nigeria','1995-08-19','female','Nigerian','Ibrahim Johnson','+234-824-300-3192','2022-10-31',NULL,'active',NULL,'rotating',205089.54,NULL,'TIN-93921833','9135730110',NULL,NULL,'2025-09-29',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('1ce1a4a4-0e67-33ba-ab99-271bbc7d5b5d','019a2f19-bf8a-713c-b295-f1c53d78fb12',1,NULL,'EMP-LAG003-0043','Kunle Bello','kunle.bello.43@sweettooth.com','+234-903-425-9093','174 Amparo Crossing Apt. 671, Lagos, Lagos State, Nigeria','1987-06-28','male','Nigerian','Amina Adebayo','+234-899-777-8341','2024-01-11',NULL,'active',NULL,'morning',220204.35,NULL,'TIN-80764276','7480653423',NULL,NULL,'2025-07-23',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('215a3565-68ba-3910-b6c0-4d2f590501e8','019a2f19-bde1-73ae-b1f7-81d61b668e39',3,NULL,'EMP-CAL001-0010','Chukwuemeka Bello','chukwuemeka.bello.10@sweettooth.com','+234-815-881-6239','7526 Flatley Plaza Apt. 921, Calabar, Cross River State, Nigeria','1988-12-22','male','Nigerian','Nneka Adebayo','+234-842-972-1263','2024-11-16',NULL,'active',NULL,'afternoon',132182.71,NULL,'TIN-23743034','6057149696',NULL,NULL,'2025-09-22',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('21dc23b7-f5ef-3815-a1ab-0ff7da65a133','019a2f19-bde1-73ae-b1f7-81d61b668e39',4,NULL,'EMP-CAL001-0012','Ada Aliyu','ada.aliyu.12@sweettooth.com','+234-895-126-2072','491 Tianna Crossroad, Calabar, Cross River State, Nigeria','1993-01-01','female','Nigerian','Abubakar Nwankwo','+234-829-328-8056','2022-12-08',NULL,'active',NULL,'afternoon',147832.88,NULL,'TIN-26638799','5991520534',NULL,NULL,'2025-09-12',3.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('2474bdb1-3e06-3acc-a704-d094a3446289','019a2f19-bde1-73ae-b1f7-81d61b668e39',7,NULL,'EMP-CAL001-0020','Ada Okafor','ada.okafor.20@sweettooth.com','+234-831-948-2772','137 Olga Underpass Apt. 211, Calabar, Cross River State, Nigeria','1985-06-06','female','Nigerian','Obinna Nwankwo','+234-811-230-4569','2025-05-13',NULL,'active',NULL,'morning',300398.81,NULL,'TIN-13782594','5959549033',NULL,NULL,'2025-09-30',4.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('2891f768-96f1-3708-82e2-9b17a9886c45','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',3,NULL,'EMP-ABJ004-0071','Kemi Eze','kemi.eze.71@sweettooth.com','+234-817-533-1963','30140 Brennon Bridge, Abuja, FCT State, Nigeria','1983-11-23','female','Nigerian','Chukwuemeka Mohammed','+234-816-658-5395','2025-03-03',NULL,'active',NULL,'morning',224981.55,NULL,'TIN-96369330','5394043545',NULL,NULL,'2025-10-08',3.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('297fa011-e955-30f4-b391-927ed97ee879','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',6,NULL,'EMP-PHC002-0040','Ada Adebayo','ada.adebayo.40@sweettooth.com','+234-859-789-5490','706 Antonetta Islands Suite 032, Port Harcourt, Rivers State, Nigeria','2003-10-15','female','Nigerian','Yusuf Johnson','+234-881-172-9414','2024-11-28',NULL,'active',NULL,'rotating',324912.89,NULL,'TIN-38789568','0780631916',NULL,NULL,'2025-05-23',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('29c824ad-fb8a-3c9b-8e83-19e37b09f8c7','019a2f19-c0f6-7167-8a54-a58bb08550b7',1,NULL,'EMP-ENU005-0086','Obinna Aliyu','obinna.aliyu.86@sweettooth.com','+234-821-198-7546','56740 Dillon Viaduct, Enugu, Enugu State, Nigeria','1991-03-19','male','Nigerian','Nneka Nwankwo','+234-908-172-8072','2023-10-24',NULL,'active',NULL,'rotating',291499.50,NULL,'TIN-21527204','5847466645',NULL,NULL,'2025-07-18',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('29e54463-476c-37b8-850b-6ac2fe3b7c08','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',5,NULL,'EMP-ABJ004-0079','Hauwa Ogunleye','hauwa.ogunleye.79@sweettooth.com','+234-862-370-4259','1614 Delphine Village, Abuja, FCT State, Nigeria','1986-10-11','female','Nigerian','Chigozie Ogunleye','+234-898-884-5668','2024-06-19',NULL,'on_probation','2025-12-04','rotating',111760.46,NULL,'TIN-54549594','3119377852',NULL,NULL,'2025-10-09',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('2cdb22f7-32db-3fb5-9f4a-57f2d095e502','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',1,NULL,'EMP-ABJ004-0067','Abubakar Chukwu','abubakar.chukwu.67@sweettooth.com','+234-827-801-8329','790 McLaughlin Mall Apt. 285, Abuja, FCT State, Nigeria','1998-06-28','male','Nigerian','Folake Okafor','+234-813-876-7613','2023-07-29',NULL,'on_probation','2025-12-05','morning',279095.63,NULL,'TIN-11699004','5240342831',NULL,NULL,'2025-10-06',4.4,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('2ce49628-3b70-39c5-88bb-e35f5d4b81b6','019a2f19-bde1-73ae-b1f7-81d61b668e39',5,NULL,'EMP-CAL001-0016','Oluwaseun Okafor','oluwaseun.okafor.16@sweettooth.com','+234-902-775-6701','86451 Caitlyn Hill, Calabar, Cross River State, Nigeria','1984-09-11','male','Nigerian','Chioma Mohammed','+234-807-401-4400','2024-03-22',NULL,'active',NULL,'flexible',292546.52,NULL,'TIN-79270402','0438241615',NULL,NULL,'2025-08-19',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('31551347-fcb3-35b9-b1a9-391758b65399','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',1,NULL,'EMP-PHC002-0023','Obinna Bello','obinna.bello.23@sweettooth.com','+234-902-525-2181','3960 Rutherford Green, Port Harcourt, Rivers State, Nigeria','1997-01-20','male','Nigerian','Ngozi Eze','+234-885-919-3812','2024-11-09',NULL,'active',NULL,'morning',345677.70,NULL,'TIN-18325311','0052344198',NULL,NULL,'2025-06-21',4.2,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('3a29f08c-566c-32ea-a635-5554aeefd8ab','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',2,NULL,'EMP-PHC002-0027','Chigozie Bello','chigozie.bello.27@sweettooth.com','+234-840-197-8592','35174 William Mill, Port Harcourt, Rivers State, Nigeria','1994-02-17','male','Nigerian','Fatima Bello','+234-816-470-9881','2025-09-25',NULL,'on_probation','2026-01-23','rotating',82926.21,NULL,'TIN-85125277','1711278960','Shellfish',NULL,'2025-09-08',5.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('3afd9e43-0ae6-3ffe-a85e-819b3ee31a46','019a2f19-bf8a-713c-b295-f1c53d78fb12',3,NULL,'EMP-LAG003-0052','Nneka Bello','nneka.bello.52@sweettooth.com','+234-894-564-9418','305 Karina Shoal Suite 461, Lagos, Lagos State, Nigeria','1989-02-21','female','Nigerian','Emeka Adebayo','+234-815-518-7461','2025-05-31',NULL,'on_probation','2026-01-15','flexible',286372.95,NULL,'TIN-25612255','4523425726',NULL,NULL,'2025-10-03',4.4,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('3f9ca8b3-c237-3e9c-91a8-c4db13600b2e','019a2f19-c0f6-7167-8a54-a58bb08550b7',3,NULL,'EMP-ENU005-0092','Ibrahim Okoro','ibrahim.okoro.92@sweettooth.com','+234-864-146-9501','253 Mills Wells Suite 618, Enugu, Enugu State, Nigeria','1990-05-24','male','Nigerian','Ngozi Okafor','+234-883-504-6698','2023-11-12',NULL,'on_probation','2026-01-05','morning',347369.87,NULL,'TIN-18237347','9496738799',NULL,NULL,'2025-08-03',4.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('402ac4ac-26a5-3e11-bcd1-32b7b5a81bd4','019a2f19-bde1-73ae-b1f7-81d61b668e39',1,NULL,'EMP-CAL001-0004','Yusuf Bello','yusuf.bello.4@sweettooth.com','+234-813-449-6002','739 Wolf Radial, Calabar, Cross River State, Nigeria','1998-05-31','male','Nigerian','Nneka Eze','+234-852-565-2795','2025-08-26',NULL,'on_probation','2026-01-27','morning',333015.90,NULL,'TIN-32028204','3915515799',NULL,NULL,'2025-06-12',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('4411d4c6-67fb-3dce-a475-9f8d6cf0d058','019a2f19-c0f6-7167-8a54-a58bb08550b7',2,NULL,'EMP-ENU005-0091','Abubakar Nwankwo','abubakar.nwankwo.91@sweettooth.com','+234-861-843-9079','5251 Skiles Lakes, Enugu, Enugu State, Nigeria','1995-06-14','male','Nigerian','Blessing Nwankwo','+234-820-928-1769','2024-04-16',NULL,'on_probation','2025-12-13','morning',132425.45,NULL,'TIN-38039804','6472349958',NULL,NULL,'2025-07-05',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('46adffa9-2198-331e-bfcb-5db26772e3ad','019a2f19-c0f6-7167-8a54-a58bb08550b7',1,NULL,'EMP-ENU005-0088','Abubakar Johnson','abubakar.johnson.88@sweettooth.com','+234-813-810-6875','289 Horacio Point, Enugu, Enugu State, Nigeria','1992-10-18','male','Nigerian','Blessing Nwankwo','+234-894-896-8059','2024-02-16',NULL,'active',NULL,'morning',246714.89,NULL,'TIN-39812480','8888378812',NULL,NULL,'2025-05-21',3.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('46d41b16-04f2-3665-91bb-b8defd768789','019a2f19-bde1-73ae-b1f7-81d61b668e39',7,NULL,'EMP-CAL001-0021','Chioma Johnson','chioma.johnson.21@sweettooth.com','+234-889-339-2020','803 Bode Motorway Apt. 635, Calabar, Cross River State, Nigeria','1988-12-31','female','Nigerian','Chukwuemeka Johnson','+234-892-935-4872','2023-02-16',NULL,'active',NULL,'morning',329402.40,NULL,'TIN-29474750','8258553551',NULL,NULL,'2025-09-21',3.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('48108713-1f14-39a5-a26d-5c6b3dc5246a','019a2f19-bde1-73ae-b1f7-81d61b668e39',1,NULL,'EMP-CAL001-0002','Oluwaseun Eze','oluwaseun.eze.2@sweettooth.com','+234-855-692-5472','53543 Halvorson Mill, Calabar, Cross River State, Nigeria','1999-04-20','male','Nigerian','Hauwa Bello','+234-811-654-4391','2022-12-24',NULL,'active',NULL,'afternoon',268266.63,NULL,'TIN-65790694','3037182405',NULL,NULL,'2025-05-29',4.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('4a0bf585-4a6f-3d3c-a7c2-5c6240793c02','019a2f19-c0f6-7167-8a54-a58bb08550b7',2,NULL,'EMP-ENU005-0090','Oluwaseun Johnson','oluwaseun.johnson.90@sweettooth.com','+234-873-437-9722','7181 Shawn Mountain Suite 412, Enugu, Enugu State, Nigeria','1993-03-25','male','Nigerian','Amina Eze','+234-900-822-3960','2024-04-14',NULL,'active',NULL,'flexible',322814.62,NULL,'TIN-42844373','8625178199',NULL,NULL,'2025-09-24',4.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('4b7ad081-5576-37dd-988a-f4daf5501a89','019a2f19-c0f6-7167-8a54-a58bb08550b7',4,NULL,'EMP-ENU005-0098','Fatima Bello','fatima.bello.98@sweettooth.com','+234-806-260-6410','535 Vito Lodge, Enugu, Enugu State, Nigeria','1991-02-15','female','Nigerian','Emeka Okafor','+234-849-371-9873','2024-03-09',NULL,'active',NULL,'afternoon',122963.77,NULL,'TIN-73917439','6541485956',NULL,NULL,'2025-08-30',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('4dc82590-b38f-3ea7-9775-5a8537ea9247','019a2f19-bf8a-713c-b295-f1c53d78fb12',7,NULL,'EMP-LAG003-0062','Ada Williams','ada.williams.62@sweettooth.com','+234-804-681-9503','49709 Stracke Plaza, Lagos, Lagos State, Nigeria','1994-12-19','female','Nigerian','Chigozie Aliyu','+234-873-909-7718','2024-05-25',NULL,'active',NULL,'morning',82819.04,NULL,'TIN-25987979','5015239563',NULL,NULL,'2025-07-13',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('51f7a4ea-a85c-315a-a151-ad3fe2541448','019a2f19-bde1-73ae-b1f7-81d61b668e39',6,NULL,'EMP-CAL001-0019','Chioma Johnson','chioma.johnson.19@sweettooth.com','+234-882-341-2264','69678 Robel Via Suite 687, Calabar, Cross River State, Nigeria','2001-09-30','female','Nigerian','Oluwaseun Okoro','+234-807-200-6396','2025-09-19',NULL,'on_probation','2025-11-19','flexible',255863.50,NULL,'TIN-56325055','5007135986',NULL,NULL,'2025-05-13',4.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('53e8b254-7196-3e07-ae19-4d17717a9b82','019a2f19-bde1-73ae-b1f7-81d61b668e39',5,NULL,'EMP-CAL001-0017','Fatima Williams','fatima.williams.17@sweettooth.com','+234-812-929-6487','562 Gleason Run, Calabar, Cross River State, Nigeria','1990-04-16','female','Nigerian','Abubakar Okoro','+234-909-257-9088','2023-07-15',NULL,'active',NULL,'rotating',326706.77,NULL,'TIN-11814811','3991051790',NULL,NULL,'2025-04-30',4.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('544512e3-d74e-335f-81ee-0748644d799e','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',4,NULL,'EMP-PHC002-0032','Chigozie Ogunleye','chigozie.ogunleye.32@sweettooth.com','+234-844-337-3556','67754 Mollie Islands, Port Harcourt, Rivers State, Nigeria','1994-03-06','male','Nigerian','Ngozi Johnson','+234-839-706-7073','2024-04-01',NULL,'on_probation','2026-01-21','rotating',129395.00,NULL,'TIN-67500313','7879200181',NULL,NULL,'2025-04-30',3.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('57530a02-afc1-333e-8471-b5c55ca3504c','019a2f19-bf8a-713c-b295-f1c53d78fb12',2,NULL,'EMP-LAG003-0049','Folake Chukwu','folake.chukwu.49@sweettooth.com','+234-838-468-4600','3513 Koelpin Lodge Suite 031, Lagos, Lagos State, Nigeria','1991-01-12','female','Nigerian','Abubakar Williams','+234-813-576-2442','2024-04-06',NULL,'on_probation','2026-01-25','morning',349299.07,NULL,'TIN-11882826','6047845929',NULL,NULL,'2025-07-20',3.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('5c1b76f5-624d-3fb7-92e7-ce150fcc1bb0','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',2,NULL,'EMP-ABJ004-0070','Ada Nwankwo','ada.nwankwo.70@sweettooth.com','+234-804-373-8017','3518 Neoma Skyway Apt. 315, Abuja, FCT State, Nigeria','1988-11-07','female','Nigerian','Emeka Okafor','+234-840-665-9763','2025-07-21',NULL,'on_probation','2025-11-06','afternoon',234064.81,NULL,'TIN-69690380','3037235405',NULL,NULL,'2025-08-22',4.4,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('6488d38a-7d14-359f-b688-411426d90bca','019a2f19-c0f6-7167-8a54-a58bb08550b7',4,NULL,'EMP-ENU005-0096','Chigozie Williams','chigozie.williams.96@sweettooth.com','+234-837-922-2516','3236 Opal Loaf Suite 039, Enugu, Enugu State, Nigeria','1997-12-10','male','Nigerian','Blessing Bello','+234-819-263-9208','2024-08-28',NULL,'on_probation','2025-12-10','afternoon',272182.93,NULL,'TIN-22928077','3175592876',NULL,NULL,'2025-07-31',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('659cd289-b0cf-339a-b354-8774a6dbd257','019a2f19-bf8a-713c-b295-f1c53d78fb12',4,NULL,'EMP-LAG003-0055','Oluwaseun Mohammed','oluwaseun.mohammed.55@sweettooth.com','+234-871-521-6851','746 Renner Branch, Lagos, Lagos State, Nigeria','1984-05-18','male','Nigerian','Nneka Mohammed','+234-872-607-1107','2025-01-03',NULL,'active',NULL,'rotating',278476.54,NULL,'TIN-49864181','3006160560',NULL,NULL,'2025-09-05',3.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('6ba0acee-c543-3d76-90af-33e65c6a1955','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',3,NULL,'EMP-PHC002-0031','Ada Johnson','ada.johnson.31@sweettooth.com','+234-816-888-4676','972 Nina Springs Suite 684, Port Harcourt, Rivers State, Nigeria','1987-02-15','female','Nigerian','Yusuf Okafor','+234-901-267-3780','2024-09-30',NULL,'active',NULL,'afternoon',251196.88,NULL,'TIN-22459487','7136051815',NULL,NULL,'2025-08-27',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('6e0e71ca-03bf-38ae-9aa9-9308f59d4815','019a2f19-c0f6-7167-8a54-a58bb08550b7',6,NULL,'EMP-ENU005-0102','Ada Adebayo','ada.adebayo.102@sweettooth.com','+234-868-656-7790','58721 Wunsch Green Suite 981, Enugu, Enugu State, Nigeria','1990-04-28','female','Nigerian','Yusuf Williams','+234-850-855-1672','2024-05-30',NULL,'active',NULL,'morning',143573.85,NULL,'TIN-99375381','9528414204',NULL,NULL,'2025-08-22',4.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('6f42a8ff-e1e6-3560-bf8b-e17f568c134a','019a2f19-c0f6-7167-8a54-a58bb08550b7',2,NULL,'EMP-ENU005-0089','Chukwuemeka Williams','chukwuemeka.williams.89@sweettooth.com','+234-907-258-1483','9724 Schneider Common Apt. 386, Enugu, Enugu State, Nigeria','1990-09-11','male','Nigerian','Fatima Williams','+234-807-706-5654','2024-01-16',NULL,'active',NULL,'flexible',114825.97,NULL,'TIN-51015382','1108794181',NULL,NULL,'2025-10-11',4.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('72691061-592a-32cf-a17c-d8441f89c038','019a2f19-c0f6-7167-8a54-a58bb08550b7',3,NULL,'EMP-ENU005-0093','Folake Adebayo','folake.adebayo.93@sweettooth.com','+234-859-315-5601','76469 Stoltenberg Garden Apt. 931, Enugu, Enugu State, Nigeria','1995-12-12','female','Nigerian','Emeka Eze','+234-880-345-7113','2024-09-29',NULL,'on_probation','2026-01-28','morning',272217.24,NULL,'TIN-40518831','1835240884',NULL,NULL,'2025-05-09',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('72a4d37f-8ba1-335d-ab7d-62ac811b3baf','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',4,NULL,'EMP-PHC002-0035','Ngozi Chukwu','ngozi.chukwu.35@sweettooth.com','+234-834-155-6230','12690 Jessie Mews, Port Harcourt, Rivers State, Nigeria','2002-11-28','female','Nigerian','Chigozie Bello','+234-895-843-5614','2022-12-10',NULL,'active',NULL,'morning',303297.21,NULL,'TIN-18213858','5916299700','Lactose',NULL,'2025-08-24',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('73d7cfd8-d396-3554-8dbc-e966d889f8b8','019a2f19-bf8a-713c-b295-f1c53d78fb12',4,NULL,'EMP-LAG003-0054','Tunde Williams','tunde.williams.54@sweettooth.com','+234-880-823-8502','3194 Gibson Tunnel, Lagos, Lagos State, Nigeria','1987-12-07','male','Nigerian','Fatima Okoro','+234-844-328-9252','2023-08-01',NULL,'on_probation','2025-11-04','rotating',140657.25,NULL,'TIN-67527352','2919265989',NULL,NULL,'2025-10-26',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('756cf9ea-af65-3770-9ca4-b65296ce1f49','019a2f19-bde1-73ae-b1f7-81d61b668e39',2,NULL,'EMP-CAL001-0005','Abubakar Johnson','abubakar.johnson.5@sweettooth.com','+234-802-809-9697','1595 Will Street Suite 800, Calabar, Cross River State, Nigeria','1983-07-12','male','Nigerian','Amina Eze','+234-842-409-7766','2023-06-30',NULL,'active',NULL,'rotating',249772.42,NULL,'TIN-23471340','5903303087','Peanuts',NULL,'2025-06-03',4.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('75f17701-ea28-3321-96a9-ec0489b54c3d','019a2f19-bf8a-713c-b295-f1c53d78fb12',2,NULL,'EMP-LAG003-0048','Ngozi Bello','ngozi.bello.48@sweettooth.com','+234-840-949-9406','8058 Kris Junctions Suite 925, Lagos, Lagos State, Nigeria','2001-04-07','female','Nigerian','Chigozie Bello','+234-827-876-8218','2025-07-31',NULL,'on_probation','2025-12-28','afternoon',165368.14,NULL,'TIN-31756750','0273643349',NULL,NULL,'2025-07-20',5.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('7ec1c366-04ae-3773-852e-dc26f5fdd6f4','019a2f19-bf8a-713c-b295-f1c53d78fb12',4,NULL,'EMP-LAG003-0056','Emeka Mohammed','emeka.mohammed.56@sweettooth.com','+234-854-335-3720','71901 Letha Branch, Lagos, Lagos State, Nigeria','1996-09-27','male','Nigerian','Ada Johnson','+234-875-611-8246','2022-12-23',NULL,'on_probation','2025-12-12','afternoon',284214.81,NULL,'TIN-28226450','0040550558',NULL,NULL,'2025-05-16',3.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('859ccd79-9861-366d-8409-07abb8758ab1','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',6,NULL,'EMP-ABJ004-0081','Ngozi Okoro','ngozi.okoro.81@sweettooth.com','+234-908-717-1853','46531 Schuppe Station Apt. 030, Abuja, FCT State, Nigeria','1989-04-22','female','Nigerian','Chukwuemeka Adebayo','+234-818-579-4685','2024-09-12',NULL,'active',NULL,'rotating',331847.28,NULL,'TIN-65652806','1232184423',NULL,NULL,'2025-08-09',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('90d18d0c-a92f-3948-aedd-47adac59c897','019a2f19-c0f6-7167-8a54-a58bb08550b7',4,NULL,'EMP-ENU005-0097','Tunde Nwankwo','tunde.nwankwo.97@sweettooth.com','+234-821-779-8305','90790 Carolyn Camp Suite 321, Enugu, Enugu State, Nigeria','2001-12-12','male','Nigerian','Amina Eze','+234-870-763-1129','2025-07-10',NULL,'on_probation','2025-12-08','afternoon',299068.34,NULL,'TIN-77457679','4051153994',NULL,NULL,'2025-09-09',3.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('92e38cf7-2745-3706-97ee-b43df45bd2fb','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',2,NULL,'EMP-ABJ004-0069','Blessing Aliyu','blessing.aliyu.69@sweettooth.com','+234-874-174-5256','5387 Kemmer Port Apt. 932, Abuja, FCT State, Nigeria','1993-11-04','female','Nigerian','Abubakar Ogunleye','+234-816-293-6434','2024-07-14',NULL,'on_probation','2025-11-18','afternoon',179401.51,NULL,'TIN-30939517','7868273089',NULL,NULL,'2025-05-21',4.2,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('94cc6e26-51e4-32e8-b918-d2c9fdc9bcf4','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',7,NULL,'EMP-ABJ004-0084','Kemi Williams','kemi.williams.84@sweettooth.com','+234-828-852-6622','29209 Mann Route Suite 978, Abuja, FCT State, Nigeria','1986-08-12','female','Nigerian','Chukwuemeka Mohammed','+234-855-855-3937','2023-08-01',NULL,'active',NULL,'flexible',340651.85,NULL,'TIN-23526522','5482020185','Lactose',NULL,'2025-07-30',3.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('9627dd07-674e-3589-8f98-31de69199c4b','019a2f19-c0f6-7167-8a54-a58bb08550b7',7,NULL,'EMP-ENU005-0104','Hauwa Johnson','hauwa.johnson.104@sweettooth.com','+234-898-416-7271','70102 Bartell Burgs, Enugu, Enugu State, Nigeria','1992-03-24','female','Nigerian','Yusuf Chukwu','+234-839-293-9712','2024-12-13',NULL,'active',NULL,'morning',202674.56,NULL,'TIN-88159230','4272743246',NULL,NULL,'2025-07-02',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('969f92ea-1f76-36e4-8a5e-34e0f960465a','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,NULL,'EMP-ABJ004-0077','Abubakar Nwankwo','abubakar.nwankwo.77@sweettooth.com','+234-849-227-5742','902 Rowe Forge Suite 539, Abuja, FCT State, Nigeria','2003-04-14','male','Nigerian','Amina Chukwu','+234-908-166-8106','2023-08-10',NULL,'active',NULL,'morning',288890.86,NULL,'TIN-38903439','6773705339','Lactose',NULL,'2025-05-30',5.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('96b818bb-5b49-3d7a-87fa-995a4b18864c','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,NULL,'EMP-ABJ004-0075','Ada Okafor','ada.okafor.75@sweettooth.com','+234-807-488-2150','4995 Price Via, Abuja, FCT State, Nigeria','1986-09-30','female','Nigerian','Emeka Eze','+234-814-613-4569','2024-05-31',NULL,'active',NULL,'flexible',218314.77,NULL,'TIN-72149642','9031627367',NULL,NULL,'2025-08-27',4.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('986fff17-19db-3600-9f55-989e7f7cc0c7','019a2f19-bde1-73ae-b1f7-81d61b668e39',1,NULL,'EMP-CAL001-0003','Ibrahim Adebayo','ibrahim.adebayo.3@sweettooth.com','+234-857-143-9064','479 Madelynn Fords, Calabar, Cross River State, Nigeria','1989-11-27','male','Nigerian','Hauwa Johnson','+234-818-888-5124','2022-11-14',NULL,'active',NULL,'afternoon',238486.55,NULL,'TIN-23722234','6073119198',NULL,NULL,'2025-09-02',5.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('9a5cd20b-24e1-351d-a97b-788a22dadaad','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',1,NULL,'EMP-ABJ004-0065','Ngozi Okoro','ngozi.okoro.65@sweettooth.com','+234-894-179-1386','399 Connelly Avenue Apt. 381, Abuja, FCT State, Nigeria','1989-06-27','female','Nigerian','Obinna Chukwu','+234-858-969-2887','2024-09-30',NULL,'active',NULL,'morning',107042.23,NULL,'TIN-47764503','8335392492',NULL,NULL,'2025-10-16',3.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('a5903508-2103-36f4-aafe-6205252fce04','019a2f19-bde1-73ae-b1f7-81d61b668e39',4,NULL,'EMP-CAL001-0011','Chioma Okafor','chioma.okafor.11@sweettooth.com','+234-813-458-4611','847 Cole Radial Suite 639, Calabar, Cross River State, Nigeria','1990-01-20','female','Nigerian','Kunle Bello','+234-882-682-3706','2023-12-31',NULL,'active',NULL,'afternoon',131943.94,NULL,'TIN-16727418','7949941483',NULL,NULL,'2025-08-24',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('a642e202-9f55-33c3-bd24-b91dbf4ee777','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',3,NULL,'EMP-PHC002-0029','Chukwuemeka Nwankwo','chukwuemeka.nwankwo.29@sweettooth.com','+234-815-346-1608','15612 Grady Mills Suite 311, Port Harcourt, Rivers State, Nigeria','1983-02-19','male','Nigerian','Nneka Eze','+234-818-137-7821','2024-09-28',NULL,'active',NULL,'flexible',155758.14,NULL,'TIN-20036413','3868216706',NULL,NULL,'2025-10-26',4.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('a6dc7408-6412-3e6e-93eb-d72f2b3e6cd2','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',5,NULL,'EMP-PHC002-0038','Chigozie Johnson','chigozie.johnson.38@sweettooth.com','+234-840-126-5113','64422 Ellis Freeway Suite 847, Port Harcourt, Rivers State, Nigeria','1994-07-02','male','Nigerian','Hauwa Mohammed','+234-831-397-4994','2022-11-08',NULL,'active',NULL,'flexible',247271.77,NULL,'TIN-11942922','9412519777',NULL,NULL,'2025-09-03',4.4,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ab70cc57-3767-3df9-a875-481539a5015c','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',3,NULL,'EMP-PHC002-0030','Kemi Aliyu','kemi.aliyu.30@sweettooth.com','+234-893-120-5088','77827 Fatima View, Port Harcourt, Rivers State, Nigeria','2000-05-16','female','Nigerian','Tunde Okafor','+234-864-270-9420','2024-11-18',NULL,'on_probation','2026-01-15','rotating',305049.69,NULL,'TIN-26045277','2065749112',NULL,NULL,'2025-07-08',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('abd9c8ab-73e5-335c-a381-8f0a0f2e53cc','019a2f19-c0f6-7167-8a54-a58bb08550b7',7,NULL,'EMP-ENU005-0105','Kemi Okoro','kemi.okoro.105@sweettooth.com','+234-825-839-5454','52496 Gleichner Port Suite 315, Enugu, Enugu State, Nigeria','1991-05-24','female','Nigerian','Ibrahim Johnson','+234-881-758-4422','2025-09-19',NULL,'on_probation','2025-12-28','rotating',254886.87,NULL,'TIN-40268344','1537203662','Peanuts',NULL,'2025-04-30',5.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ac80660e-2ca0-3501-abaa-6521a81baf23','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,NULL,'EMP-ABJ004-0076','Kunle Johnson','kunle.johnson.76@sweettooth.com','+234-814-716-8669','16866 Annie Highway Suite 061, Abuja, FCT State, Nigeria','1980-12-20','male','Nigerian','Hauwa Okoro','+234-904-211-6764','2023-03-11',NULL,'active',NULL,'flexible',148689.17,NULL,'TIN-29609255','0870099128',NULL,NULL,'2025-07-09',3.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ade922b6-689a-396b-ba69-97c9564ae99c','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',7,NULL,'EMP-PHC002-0042','Obinna Johnson','obinna.johnson.42@sweettooth.com','+234-837-211-4287','73224 Joaquin Estates Apt. 807, Port Harcourt, Rivers State, Nigeria','1981-11-03','male','Nigerian','Nneka Aliyu','+234-885-924-4128','2025-02-03',NULL,'active',NULL,'flexible',285145.11,NULL,'TIN-75137428','5299914142',NULL,NULL,'2025-07-15',3.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ae6f1006-9b37-312f-803d-8c87a0d28f8c','019a2f19-bf8a-713c-b295-f1c53d78fb12',5,NULL,'EMP-LAG003-0059','Folake Okafor','folake.okafor.59@sweettooth.com','+234-843-248-9476','6975 Schoen Curve Apt. 770, Lagos, Lagos State, Nigeria','2001-12-14','female','Nigerian','Ibrahim Nwankwo','+234-863-967-8724','2025-04-13',NULL,'active',NULL,'flexible',198404.95,NULL,'TIN-37968945','3414023480',NULL,NULL,'2025-09-05',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('b3182133-1f1e-3c5b-ab0d-31f6356289f2','019a2f19-c0f6-7167-8a54-a58bb08550b7',4,NULL,'EMP-ENU005-0095','Kemi Eze','kemi.eze.95@sweettooth.com','+234-908-167-5770','6800 Boyer Parks Suite 900, Enugu, Enugu State, Nigeria','1993-10-27','female','Nigerian','Yusuf Eze','+234-811-409-5360','2022-11-21',NULL,'on_probation','2026-01-20','afternoon',113578.15,NULL,'TIN-27130982','7318547617',NULL,NULL,'2025-09-15',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('b6339e8d-510c-3fbc-af87-6bca2077ac7a','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',5,NULL,'EMP-ABJ004-0080','Folake Nwankwo','folake.nwankwo.80@sweettooth.com','+234-843-761-6275','156 Lebsack Row, Abuja, FCT State, Nigeria','1984-02-12','female','Nigerian','Chigozie Ogunleye','+234-856-475-7203','2024-01-03',NULL,'active',NULL,'flexible',170494.08,NULL,'TIN-21705641','3322312651',NULL,NULL,'2025-06-25',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('b728aafc-4608-371a-b123-cbe44389a6d7','019a2f19-bf8a-713c-b295-f1c53d78fb12',1,NULL,'EMP-LAG003-0046','Obinna Adebayo','obinna.adebayo.46@sweettooth.com','+234-810-666-9602','85876 Erdman Freeway, Lagos, Lagos State, Nigeria','1991-05-17','male','Nigerian','Chioma Chukwu','+234-905-353-7254','2023-11-30',NULL,'active',NULL,'afternoon',222020.95,NULL,'TIN-27483333','8180634353',NULL,NULL,'2025-06-17',3.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('bc7269a2-c59d-3842-b332-701d7cd4a5fe','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',6,NULL,'EMP-PHC002-0039','Chioma Okoro','chioma.okoro.39@sweettooth.com','+234-906-794-5943','7857 Becker Causeway, Port Harcourt, Rivers State, Nigeria','1989-04-24','female','Nigerian','Kunle Chukwu','+234-898-245-1633','2025-02-04',NULL,'active',NULL,'morning',155188.62,NULL,'TIN-77809091','5836660309',NULL,NULL,'2025-06-17',4.4,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('bf174f9c-320f-3b95-aba9-7e325da0bb81','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',1,NULL,'EMP-ABJ004-0066','Amina Mohammed','amina.mohammed.66@sweettooth.com','+234-804-279-9089','99246 Raheem Plain Apt. 154, Abuja, FCT State, Nigeria','1995-09-24','female','Nigerian','Abubakar Williams','+234-905-253-2650','2025-07-14',NULL,'on_probation','2026-01-11','morning',328845.07,NULL,'TIN-34791991','6149524950',NULL,NULL,'2025-06-24',4.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('bf26551e-05c6-309a-955d-16dad3f3c419','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',7,NULL,'EMP-PHC002-0041','Chukwuemeka Ogunleye','chukwuemeka.ogunleye.41@sweettooth.com','+234-846-279-2856','944 Marvin Tunnel, Port Harcourt, Rivers State, Nigeria','1997-01-26','male','Nigerian','Ada Chukwu','+234-828-524-3540','2024-08-26',NULL,'on_probation','2025-12-17','rotating',210115.72,NULL,'TIN-34410190','1489584554',NULL,NULL,'2025-09-23',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('c357ef42-fe0d-369a-9ad8-448518d70e37','019a2f19-bde1-73ae-b1f7-81d61b668e39',1,NULL,'EMP-CAL001-0001','Tunde Aliyu','tunde.aliyu.1@sweettooth.com','+234-885-527-1756','136 Rosamond Locks Apt. 526, Calabar, Cross River State, Nigeria','1985-07-27','male','Nigerian','Folake Bello','+234-820-208-6202','2024-08-24',NULL,'active',NULL,'afternoon',322156.48,NULL,'TIN-65893849','9179082716',NULL,NULL,'2025-05-01',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('c469b20c-58b4-3859-b71d-45e1b1c431e8','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',2,NULL,'EMP-ABJ004-0068','Kemi Bello','kemi.bello.68@sweettooth.com','+234-875-116-5105','30492 Kassulke Views Suite 381, Abuja, FCT State, Nigeria','1983-07-10','female','Nigerian','Abubakar Okafor','+234-832-428-7752','2023-09-05',NULL,'active',NULL,'afternoon',193080.38,NULL,'TIN-94190399','8535585138',NULL,NULL,'2025-06-01',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('c6b49bc7-385c-31e1-af37-cfb63f147f71','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',1,NULL,'EMP-PHC002-0025','Fatima Johnson','fatima.johnson.25@sweettooth.com','+234-840-100-6447','29577 Dickinson Prairie, Port Harcourt, Rivers State, Nigeria','1985-09-10','female','Nigerian','Emeka Ogunleye','+234-888-766-6508','2024-11-20',NULL,'on_probation','2026-01-21','rotating',217246.12,NULL,'TIN-78105938','0631468067',NULL,NULL,'2025-09-07',4.3,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('c927a925-e722-34be-83ea-2c20584df86c','019a2f19-bde1-73ae-b1f7-81d61b668e39',4,NULL,'EMP-CAL001-0014','Abubakar Johnson','abubakar.johnson.14@sweettooth.com','+234-866-121-2482','908 Bashirian Mount Suite 388, Calabar, Cross River State, Nigeria','2000-12-23','male','Nigerian','Kemi Okoro','+234-883-487-6655','2025-02-03',NULL,'active',NULL,'morning',246086.43,NULL,'TIN-86981844','4969324664',NULL,NULL,'2025-08-26',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('c998ba4f-5df0-3e28-8525-5da50568406b','019a2f19-bf8a-713c-b295-f1c53d78fb12',1,NULL,'EMP-LAG003-0044','Yusuf Johnson','yusuf.johnson.44@sweettooth.com','+234-847-369-2123','867 Jaskolski Station, Lagos, Lagos State, Nigeria','1994-11-01','male','Nigerian','Ngozi Adebayo','+234-892-462-4918','2024-03-08',NULL,'active',NULL,'flexible',239784.11,NULL,'TIN-19426336','6163127848','Shellfish',NULL,'2025-06-01',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ca24804a-039d-38b9-811f-145ca746cc3a','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',4,NULL,'EMP-PHC002-0034','Ada Okoro','ada.okoro.34@sweettooth.com','+234-820-562-5422','1582 Cleora View Suite 459, Port Harcourt, Rivers State, Nigeria','1998-06-22','female','Nigerian','Abubakar Eze','+234-882-798-7131','2024-06-07',NULL,'active',NULL,'afternoon',280466.57,NULL,'TIN-17010009','1800623655',NULL,NULL,'2025-06-07',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('cbba4f5b-e809-3bef-83aa-730772cfc5a4','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',5,NULL,'EMP-PHC002-0037','Blessing Mohammed','blessing.mohammed.37@sweettooth.com','+234-873-292-5666','4356 Verna Hollow Suite 954, Port Harcourt, Rivers State, Nigeria','2002-02-27','female','Nigerian','Abubakar Okoro','+234-830-679-7655','2024-01-09',NULL,'active',NULL,'afternoon',240943.95,NULL,'TIN-11276681','8579858881','Peanuts',NULL,'2025-06-27',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('cdb735d3-ff10-3341-b806-c5989c342ab8','019a2f19-c0f6-7167-8a54-a58bb08550b7',5,NULL,'EMP-ENU005-0099','Obinna Williams','obinna.williams.99@sweettooth.com','+234-826-237-7035','85292 Mraz Vista, Enugu, Enugu State, Nigeria','1982-05-13','male','Nigerian','Hauwa Chukwu','+234-828-308-1092','2023-05-26',NULL,'active',NULL,'rotating',114242.83,NULL,'TIN-19092040','4550287206',NULL,NULL,'2025-08-12',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('cec0cc61-4493-3327-9bdd-569929d13f41','019a2f19-c0f6-7167-8a54-a58bb08550b7',1,NULL,'EMP-ENU005-0085','Fatima Okoro','fatima.okoro.85@sweettooth.com','+234-871-376-3993','54129 Hyatt Isle Apt. 945, Enugu, Enugu State, Nigeria','1992-02-10','female','Nigerian','Kunle Mohammed','+234-863-422-6561','2024-12-16',NULL,'active',NULL,'flexible',213506.53,NULL,'TIN-81360720','1636964970',NULL,NULL,'2025-07-30',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('cf138f90-7769-39a7-b2be-b68f2c5074ab','019a2f19-bf8a-713c-b295-f1c53d78fb12',7,NULL,'EMP-LAG003-0063','Tunde Ogunleye','tunde.ogunleye.63@sweettooth.com','+234-832-696-7601','28795 King Trafficway Apt. 625, Lagos, Lagos State, Nigeria','1982-06-14','male','Nigerian','Chioma Chukwu','+234-867-385-4277','2023-01-04',NULL,'active',NULL,'flexible',101717.41,NULL,'TIN-54501067','1146845994','Peanuts',NULL,'2025-05-02',4.4,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('d1d953da-3950-337e-8a6c-7e1ff92ff36b','019a2f19-bde1-73ae-b1f7-81d61b668e39',3,NULL,'EMP-CAL001-0009','Obinna Aliyu','obinna.aliyu.9@sweettooth.com','+234-899-205-1575','40018 Hahn Port, Calabar, Cross River State, Nigeria','1990-03-07','male','Nigerian','Amina Chukwu','+234-902-588-5536','2023-06-19',NULL,'active',NULL,'flexible',300390.20,NULL,'TIN-84330096','9523948691',NULL,NULL,'2025-10-04',4.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('d1de081b-57ee-3971-969a-7aee86843804','019a2f19-bde1-73ae-b1f7-81d61b668e39',2,NULL,'EMP-CAL001-0006','Hauwa Aliyu','hauwa.aliyu.6@sweettooth.com','+234-863-453-9709','16456 Stone Orchard Apt. 120, Calabar, Cross River State, Nigeria','1987-04-12','female','Nigerian','Abubakar Aliyu','+234-877-669-4259','2023-09-13',NULL,'active',NULL,'afternoon',327390.98,NULL,'TIN-12039618','5388783026','Lactose',NULL,'2025-09-17',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('d456e286-f8ae-3c8b-9688-9c0e5b1ffd04','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',1,NULL,'EMP-PHC002-0022','Folake Aliyu','folake.aliyu.22@sweettooth.com','+234-810-717-5114','46061 Kristofer Alley, Port Harcourt, Rivers State, Nigeria','1989-07-12','female','Nigerian','Abubakar Eze','+234-887-180-7764','2024-08-16',NULL,'active',NULL,'morning',93619.97,NULL,'TIN-48515849','1041629418',NULL,NULL,'2025-08-09',4.2,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('d6dd0904-8d3c-3f21-b40e-99b9916e6659','019a2f19-c0f6-7167-8a54-a58bb08550b7',3,NULL,'EMP-ENU005-0094','Oluwaseun Williams','oluwaseun.williams.94@sweettooth.com','+234-829-449-8318','22453 Destinee Expressway Suite 321, Enugu, Enugu State, Nigeria','1998-10-07','male','Nigerian','Fatima Aliyu','+234-863-840-2926','2023-03-07',NULL,'active',NULL,'flexible',181799.60,NULL,'TIN-31045443','9465449773','Shellfish',NULL,'2025-05-26',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('d7653847-e167-38d8-bd67-d3de3803ae85','019a2f19-bde1-73ae-b1f7-81d61b668e39',5,NULL,'EMP-CAL001-0015','Oluwaseun Okafor','oluwaseun.okafor.15@sweettooth.com','+234-825-745-1944','5962 Josue Shoals, Calabar, Cross River State, Nigeria','2001-12-25','male','Nigerian','Folake Eze','+234-804-547-9814','2022-11-22',NULL,'active',NULL,'afternoon',109164.05,NULL,'TIN-14962739','4061653636',NULL,NULL,'2025-05-21',4.2,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('dac3cbd1-b6e3-33bb-bb29-0dfd858593c1','019a2f19-bde1-73ae-b1f7-81d61b668e39',6,NULL,'EMP-CAL001-0018','Ibrahim Aliyu','ibrahim.aliyu.18@sweettooth.com','+234-871-928-6664','56687 Russel Mall, Calabar, Cross River State, Nigeria','1990-06-19','male','Nigerian','Amina Okoro','+234-850-861-3376','2023-05-23',NULL,'active',NULL,'rotating',81512.07,NULL,'TIN-30702409','9439842431',NULL,NULL,'2025-07-21',3.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('dc49b60e-06f1-329f-86cd-e4ecdd001ed3','019a2f19-c0f6-7167-8a54-a58bb08550b7',5,NULL,'EMP-ENU005-0101','Kunle Williams','kunle.williams.101@sweettooth.com','+234-855-395-5145','9688 Mia Highway, Enugu, Enugu State, Nigeria','1992-07-12','male','Nigerian','Blessing Eze','+234-852-749-5895','2025-06-28',NULL,'active',NULL,'flexible',192557.29,NULL,'TIN-97025596','6976473506',NULL,NULL,'2025-10-22',3.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('e0e4effe-086c-3396-9a95-f25f394634b1','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',5,NULL,'EMP-PHC002-0036','Fatima Williams','fatima.williams.36@sweettooth.com','+234-841-950-7002','5360 Theodore Court, Port Harcourt, Rivers State, Nigeria','1985-02-14','female','Nigerian','Ibrahim Chukwu','+234-842-129-5595','2023-05-05',NULL,'active',NULL,'morning',253955.82,NULL,'TIN-72371811','5347623685',NULL,NULL,'2025-06-08',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('e1125a64-15ab-33fb-af7b-50a5575a108b','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',6,NULL,'EMP-ABJ004-0082','Ngozi Nwankwo','ngozi.nwankwo.82@sweettooth.com','+234-853-581-8511','587 Ruthe Shore, Abuja, FCT State, Nigeria','1981-02-20','female','Nigerian','Abubakar Aliyu','+234-823-627-8593','2024-09-07',NULL,'active',NULL,'rotating',256871.23,NULL,'TIN-59126992','7655320919',NULL,NULL,'2025-07-26',4.9,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('e194ae89-e6ba-39d3-999b-ef1802e3382d','019a2f19-bf8a-713c-b295-f1c53d78fb12',5,NULL,'EMP-LAG003-0057','Blessing Johnson','blessing.johnson.57@sweettooth.com','+234-823-170-5922','55177 Hagenes Lakes, Lagos, Lagos State, Nigeria','1982-07-26','female','Nigerian','Chukwuemeka Adebayo','+234-901-220-4388','2023-03-20',NULL,'on_probation','2025-11-06','morning',259872.34,NULL,'TIN-65573784','0906463016',NULL,NULL,'2025-08-25',4.2,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('e401c6a7-859d-3ba9-8bcd-1e2dc81fecc7','019a2f19-bf8a-713c-b295-f1c53d78fb12',2,NULL,'EMP-LAG003-0047','Ngozi Aliyu','ngozi.aliyu.47@sweettooth.com','+234-889-789-8323','8666 Baumbach Bridge, Lagos, Lagos State, Nigeria','1998-09-12','female','Nigerian','Kunle Aliyu','+234-810-664-7116','2023-12-26',NULL,'active',NULL,'afternoon',198188.86,NULL,'TIN-11804777','8286209543',NULL,NULL,'2025-07-10',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('e4dc55f4-09ac-3525-8664-e9fa0f00c9cd','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',1,NULL,'EMP-ABJ004-0064','Emeka Bello','emeka.bello.64@sweettooth.com','+234-846-224-6103','43388 Hegmann Glens, Abuja, FCT State, Nigeria','1994-06-29','male','Nigerian','Chioma Adebayo','+234-904-890-2287','2024-03-29',NULL,'active',NULL,'flexible',323912.81,NULL,'TIN-74986201','4565770642',NULL,NULL,'2025-10-22',4.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('e57a001a-9772-3c78-9fbf-5a582a066266','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',2,NULL,'EMP-PHC002-0028','Folake Eze','folake.eze.28@sweettooth.com','+234-809-153-9641','27758 Maggio Parkways Suite 795, Port Harcourt, Rivers State, Nigeria','1992-08-21','female','Nigerian','Kunle Okafor','+234-870-802-3848','2025-01-28',NULL,'active',NULL,'flexible',99019.89,NULL,'TIN-50104427','3611227537',NULL,NULL,'2025-05-17',4.2,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ec5d87d0-38f1-3533-ac30-978b5eac1f23','019a2f19-bf8a-713c-b295-f1c53d78fb12',1,NULL,'EMP-LAG003-0045','Oluwaseun Okoro','oluwaseun.okoro.45@sweettooth.com','+234-827-289-9041','5382 Paris Hollow, Lagos, Lagos State, Nigeria','1981-01-30','male','Nigerian','Folake Ogunleye','+234-901-214-6215','2023-02-09',NULL,'active',NULL,'afternoon',148746.72,NULL,'TIN-78311265','2146907273',NULL,NULL,'2025-07-21',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ee104e7e-d2d1-3a7a-a5fd-f7bd856c3088','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',3,NULL,'EMP-ABJ004-0072','Nneka Eze','nneka.eze.72@sweettooth.com','+234-855-326-3343','9212 Stephon Course Apt. 120, Abuja, FCT State, Nigeria','1989-11-29','female','Nigerian','Abubakar Nwankwo','+234-816-381-6283','2023-06-11',NULL,'active',NULL,'flexible',91110.71,NULL,'TIN-48005139','3623550602',NULL,NULL,'2025-05-10',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ef606b73-139a-307e-92c1-260cc1cc1d7c','019a2f19-bf8a-713c-b295-f1c53d78fb12',5,NULL,'EMP-LAG003-0058','Chioma Chukwu','chioma.chukwu.58@sweettooth.com','+234-852-836-8511','96562 Anderson Row, Lagos, Lagos State, Nigeria','1988-04-29','female','Nigerian','Chukwuemeka Mohammed','+234-880-929-1079','2023-07-05',NULL,'active',NULL,'afternoon',186733.88,NULL,'TIN-84725491','1996517207',NULL,NULL,'2025-10-20',3.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('f107a8ac-7c27-3470-b87a-a190d98092f5','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',2,NULL,'EMP-PHC002-0026','Fatima Johnson','fatima.johnson.26@sweettooth.com','+234-892-892-7800','1629 Hirthe Circle, Port Harcourt, Rivers State, Nigeria','1999-04-18','female','Nigerian','Emeka Bello','+234-818-340-9766','2024-12-14',NULL,'active',NULL,'morning',319737.55,NULL,'TIN-37548624','0083577552','None',NULL,'2025-09-05',3.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('f3f18a4b-6654-39a1-a305-a65e0334b5dd','019a2f19-bf8a-713c-b295-f1c53d78fb12',3,NULL,'EMP-LAG003-0051','Kunle Adebayo','kunle.adebayo.51@sweettooth.com','+234-895-163-8908','78315 Welch Key, Lagos, Lagos State, Nigeria','1990-06-01','male','Nigerian','Ada Johnson','+234-843-370-5848','2025-08-30',NULL,'active',NULL,'flexible',342313.27,NULL,'TIN-57405319','9229785033','Lactose',NULL,'2025-09-21',4.7,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('f74340dd-e310-3f1f-8565-2f35c296e2cd','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',7,NULL,'EMP-ABJ004-0083','Tunde Mohammed','tunde.mohammed.83@sweettooth.com','+234-871-429-2479','13714 Russel Landing Apt. 624, Abuja, FCT State, Nigeria','1984-09-08','male','Nigerian','Kemi Bello','+234-817-449-1500','2023-02-13',NULL,'active',NULL,'flexible',160696.11,NULL,'TIN-31872075','2946265667',NULL,NULL,'2025-09-24',4.1,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('fa9b2227-9c60-3e9e-be6c-a1e90e4a670e','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',3,NULL,'EMP-ABJ004-0073','Chigozie Okafor','chigozie.okafor.73@sweettooth.com','+234-849-152-7010','33618 Stiedemann Ports, Abuja, FCT State, Nigeria','2001-11-26','male','Nigerian','Kemi Adebayo','+234-845-503-8006','2024-06-07',NULL,'on_probation','2025-11-25','morning',243486.57,NULL,'TIN-69916058','6563078782','None',NULL,'2025-08-21',4.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('fad77429-6c57-3d9d-96a0-c2816c25e8cf','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,NULL,'EMP-ABJ004-0074','Ada Nwankwo','ada.nwankwo.74@sweettooth.com','+234-830-152-6780','1896 Alex Dam, Abuja, FCT State, Nigeria','1997-11-09','female','Nigerian','Ibrahim Mohammed','+234-878-806-3864','2023-09-08',NULL,'active',NULL,'morning',124712.46,NULL,'TIN-96732014','9186605242',NULL,NULL,'2025-10-10',4.2,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('fafca340-b095-3dd5-9065-e0d661a1cd12','019a2f19-be9d-70d3-80ee-fb41b8a27a7c',4,NULL,'EMP-PHC002-0033','Oluwaseun Okafor','oluwaseun.okafor.33@sweettooth.com','+234-879-784-7916','558 Swift Throughway, Port Harcourt, Rivers State, Nigeria','1991-03-01','male','Nigerian','Kemi Okafor','+234-827-297-3203','2024-10-07',NULL,'active',NULL,'morning',94380.53,NULL,'TIN-73010975','1330554249',NULL,NULL,'2025-09-08',4.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('fb18a32a-2a3b-3663-86d3-34daf6b14647','019a2f19-c0f6-7167-8a54-a58bb08550b7',1,NULL,'EMP-ENU005-0087','Ada Mohammed','ada.mohammed.87@sweettooth.com','+234-852-396-7338','27821 Deonte Forest, Enugu, Enugu State, Nigeria','1993-02-01','female','Nigerian','Tunde Ogunleye','+234-823-555-7824','2025-06-13',NULL,'active',NULL,'morning',108094.76,NULL,'TIN-72524190','1356174037',NULL,NULL,'2025-08-11',5.0,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('fbb0d48f-9768-3e0d-9320-ddc288b41c9d','019a2f19-c0f6-7167-8a54-a58bb08550b7',6,NULL,'EMP-ENU005-0103','Ada Okoro','ada.okoro.103@sweettooth.com','+234-804-490-3057','503 Carli Pike, Enugu, Enugu State, Nigeria','1981-05-18','female','Nigerian','Oluwaseun Okoro','+234-894-886-5151','2024-07-16',NULL,'active',NULL,'afternoon',176709.05,NULL,'TIN-19611170','9976199619',NULL,NULL,'2025-10-27',4.6,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('fc15e9b6-5021-3f25-a271-f457f0b2c9c4','019a2f19-c0c9-71a0-9cb2-881fed1d26ca',5,NULL,'EMP-ABJ004-0078','Tunde Williams','tunde.williams.78@sweettooth.com','+234-893-122-9018','238 Tromp Underpass Apt. 018, Abuja, FCT State, Nigeria','1986-07-25','male','Nigerian','Blessing Nwankwo','+234-900-736-4485','2023-01-05',NULL,'active',NULL,'flexible',153089.19,NULL,'TIN-78239001','3832521407',NULL,NULL,'2025-10-03',3.8,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL),('ff83b476-f249-38c6-8196-a97f8cf8e6e7','019a2f19-bf8a-713c-b295-f1c53d78fb12',3,NULL,'EMP-LAG003-0050','Abubakar Nwankwo','abubakar.nwankwo.50@sweettooth.com','+234-832-384-4577','2981 Parisian View, Lagos, Lagos State, Nigeria','1999-02-05','male','Nigerian','Ada Nwankwo','+234-815-983-3281','2023-09-27',NULL,'on_probation','2025-11-16','flexible',142590.43,NULL,'TIN-65173386','1549213761',NULL,NULL,'2025-05-11',4.5,'2025-10-29 07:33:31','2025-10-29 07:33:31',NULL,'$2y$12$UDh.cEuQo3offRPqo3gCnOgcmMtx1Tl9I/yX4R/91tTXmi9fNclz6','2025-10-29 07:33:31',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expiry_confirmations`
--

DROP TABLE IF EXISTS `expiry_confirmations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expiry_confirmations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_stock_id` bigint(20) unsigned NOT NULL,
  `sales_shift_id` bigint(20) unsigned NOT NULL,
  `confirmed_by` char(36) NOT NULL,
  `action` enum('confirmed_good','marked_callback') NOT NULL COMMENT 'Action taken: confirmed still good or marked as callback',
  `notes` text DEFAULT NULL COMMENT 'Reason for confirmation or callback',
  `confirmed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_expiry_confirmation` (`product_stock_id`,`sales_shift_id`),
  KEY `expiry_confirmations_sales_shift_id_foreign` (`sales_shift_id`),
  KEY `expiry_confirmations_confirmed_by_foreign` (`confirmed_by`),
  CONSTRAINT `expiry_confirmations_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expiry_confirmations_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `product_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expiry_confirmations_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expiry_confirmations`
--

LOCK TABLES `expiry_confirmations` WRITE;
/*!40000 ALTER TABLE `expiry_confirmations` DISABLE KEYS */;
/*!40000 ALTER TABLE `expiry_confirmations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_accounting_cashes`
--

DROP TABLE IF EXISTS `global_accounting_cashes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_accounting_cashes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expenses_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit"]' CHECK (json_valid(`expenses_categories`)),
  `cash_bank` varchar(50) NOT NULL DEFAULT 'enabled',
  `profit_loss_reports` varchar(50) NOT NULL DEFAULT 'by_date',
  `accounting_entries` varchar(50) NOT NULL DEFAULT 'auto',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_accounting_cashes`
--

LOCK TABLES `global_accounting_cashes` WRITE;
/*!40000 ALTER TABLE `global_accounting_cashes` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_accounting_cashes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_branch_management`
--

DROP TABLE IF EXISTS `global_branch_management`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_branch_management` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_add` varchar(50) NOT NULL DEFAULT 'enabled',
  `branch_edit` varchar(50) NOT NULL DEFAULT 'enabled',
  `branch_delete` varchar(50) NOT NULL DEFAULT 'enabled',
  `branch_admin_assign` varchar(50) NOT NULL DEFAULT 'enabled',
  `inter_branch_transfer` varchar(50) NOT NULL DEFAULT 'enabled',
  `central_warehouse` varchar(50) NOT NULL DEFAULT 'enabled',
  `branch_hours` varchar(50) NOT NULL DEFAULT 'set',
  `saas_tenant` varchar(50) NOT NULL DEFAULT 'enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_branch_management`
--

LOCK TABLES `global_branch_management` WRITE;
/*!40000 ALTER TABLE `global_branch_management` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_branch_management` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_business_configurations`
--

DROP TABLE IF EXISTS `global_business_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_business_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL DEFAULT 'Your Business Name',
  `logo_upload` varchar(50) NOT NULL DEFAULT 'enabled',
  `contact_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["phone","email","website","vat_number"]' CHECK (json_valid(`contact_details`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `backup_interval` int(11) NOT NULL DEFAULT 2,
  `backup_period` varchar(255) NOT NULL DEFAULT 'months',
  `auto_backup` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_business_configurations`
--

LOCK TABLES `global_business_configurations` WRITE;
/*!40000 ALTER TABLE `global_business_configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_business_configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_currency_localizations`
--

DROP TABLE IF EXISTS `global_currency_localizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_currency_localizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `multi_currency` varchar(50) NOT NULL DEFAULT 'enabled',
  `primary_currency` varchar(10) NOT NULL DEFAULT 'NGN',
  `primary_currency_exchange_rate` varchar(255) DEFAULT NULL,
  `currency_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["USD","EUR","GBP","INR","NGN"]' CHECK (json_valid(`currency_list`)),
  `multi_tax` varchar(50) NOT NULL DEFAULT 'enabled',
  `default_language` varchar(10) NOT NULL DEFAULT 'en',
  `language_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["en","es","fr","ar"]' CHECK (json_valid(`language_options`)),
  `date_format` varchar(50) NOT NULL DEFAULT 'MM/DD/YYYY',
  `units_of_measure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["piece","kg","liter"]' CHECK (json_valid(`units_of_measure`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_currency_localizations`
--

LOCK TABLES `global_currency_localizations` WRITE;
/*!40000 ALTER TABLE `global_currency_localizations` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_currency_localizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_customer_supplier_managements`
--

DROP TABLE IF EXISTS `global_customer_supplier_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_customer_supplier_managements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit","groups"]' CHECK (json_valid(`customers`)),
  `suppliers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit"]' CHECK (json_valid(`suppliers`)),
  `party_import` varchar(50) NOT NULL DEFAULT 'enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_customer_supplier_managements`
--

LOCK TABLES `global_customer_supplier_managements` WRITE;
/*!40000 ALTER TABLE `global_customer_supplier_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_customer_supplier_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_employee_managements`
--

DROP TABLE IF EXISTS `global_employee_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_employee_managements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["create","edit"]' CHECK (json_valid(`roles`)),
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["pos","inventory","reports"]' CHECK (json_valid(`permissions`)),
  `staff_profiles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit","delete"]' CHECK (json_valid(`staff_profiles`)),
  `departments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["sales","warehouse"]' CHECK (json_valid(`departments`)),
  `shift_scheduling` varchar(50) NOT NULL DEFAULT 'enabled',
  `pin_login` varchar(50) NOT NULL DEFAULT 'enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_employee_managements`
--

LOCK TABLES `global_employee_managements` WRITE;
/*!40000 ALTER TABLE `global_employee_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_employee_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_inventory_managements`
--

DROP TABLE IF EXISTS `global_inventory_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_inventory_managements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit","delete"]' CHECK (json_valid(`categories`)),
  `brands` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit","delete"]' CHECK (json_valid(`brands`)),
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit","sku_auto"]' CHECK (json_valid(`products`)),
  `multi_variant` varchar(50) NOT NULL DEFAULT 'enabled',
  `stock_adjustment` varchar(50) NOT NULL DEFAULT 'enabled',
  `purchase_returns` varchar(50) NOT NULL DEFAULT 'enabled',
  `supplier_management` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit","link"]' CHECK (json_valid(`supplier_management`)),
  `low_stock_alert` varchar(50) NOT NULL DEFAULT 'threshold:10%',
  `expiry_tracking` varchar(50) NOT NULL DEFAULT 'enabled',
  `import_csv` varchar(50) NOT NULL DEFAULT 'enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_inventory_managements`
--

LOCK TABLES `global_inventory_managements` WRITE;
/*!40000 ALTER TABLE `global_inventory_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_inventory_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_notifications_alerts`
--

DROP TABLE IF EXISTS `global_notifications_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_notifications_alerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alerts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["email","sms"]' CHECK (json_valid(`alerts`)),
  `task_todo` varchar(50) NOT NULL DEFAULT 'enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_notifications_alerts`
--

LOCK TABLES `global_notifications_alerts` WRITE;
/*!40000 ALTER TABLE `global_notifications_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_notifications_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_pos_configurations`
--

DROP TABLE IF EXISTS `global_pos_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_pos_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pos_interface` varchar(50) NOT NULL DEFAULT 'enabled',
  `payment_modes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["add","edit"]' CHECK (json_valid(`payment_modes`)),
  `receipt_template` varchar(50) NOT NULL DEFAULT 'custom',
  `sales_returns` varchar(50) NOT NULL DEFAULT 'enabled',
  `offline_mode` varchar(50) NOT NULL DEFAULT 'enabled',
  `online_shop_sync` varchar(50) NOT NULL DEFAULT 'enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_pos_configurations`
--

LOCK TABLES `global_pos_configurations` WRITE;
/*!40000 ALTER TABLE `global_pos_configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_pos_configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_reports_analytics`
--

DROP TABLE IF EXISTS `global_reports_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_reports_analytics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reports` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["sales","purchases","stock","pl"]' CHECK (json_valid(`reports`)),
  `custom_date_range` varchar(50) NOT NULL DEFAULT 'enabled',
  `multi_select_delete` varchar(50) NOT NULL DEFAULT 'enabled',
  `export` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["csv","pdf"]' CHECK (json_valid(`export`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_reports_analytics`
--

LOCK TABLES `global_reports_analytics` WRITE;
/*!40000 ALTER TABLE `global_reports_analytics` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_reports_analytics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_security_accesses`
--

DROP TABLE IF EXISTS `global_security_accesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_security_accesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authentication` varchar(50) NOT NULL DEFAULT '2fa',
  `audit_logs` varchar(50) NOT NULL DEFAULT 'enabled',
  `data_isolation` varchar(50) NOT NULL DEFAULT 'saas_company',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_security_accesses`
--

LOCK TABLES `global_security_accesses` WRITE;
/*!40000 ALTER TABLE `global_security_accesses` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_security_accesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `health_checks`
--

DROP TABLE IF EXISTS `health_checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `health_checks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` bigint(20) unsigned NOT NULL,
  `checked_by` char(36) NOT NULL,
  `check_date` date NOT NULL,
  `condition` enum('excellent','good','fair','poor','damaged','expired') NOT NULL,
  `quantity_affected` decimal(12,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `health_checks_stock_id_foreign` (`stock_id`),
  KEY `health_checks_checked_by_foreign` (`checked_by`),
  CONSTRAINT `health_checks_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `health_checks_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `health_checks`
--

LOCK TABLES `health_checks` WRITE;
/*!40000 ALTER TABLE `health_checks` DISABLE KEYS */;
/*!40000 ALTER TABLE `health_checks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_dispatches`
--

DROP TABLE IF EXISTS `item_dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_dispatches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `dispatched_by` char(36) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `received_by` char(36) DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units','bags','cartons') NOT NULL,
  `dispatch_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `received_time` timestamp NULL DEFAULT NULL,
  `shift` enum('morning','afternoon','night') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_dispatches_request_id_foreign` (`request_id`),
  KEY `item_dispatches_item_id_foreign` (`item_id`),
  KEY `item_dispatches_branch_id_foreign` (`branch_id`),
  KEY `item_dispatches_dispatched_by_foreign` (`dispatched_by`),
  KEY `item_dispatches_received_by_foreign` (`received_by`),
  CONSTRAINT `item_dispatches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_dispatches_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `item_dispatches_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `item_dispatches_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `item_dispatches_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `item_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_dispatches`
--

LOCK TABLES `item_dispatches` WRITE;
/*!40000 ALTER TABLE `item_dispatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_dispatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_request_details`
--

DROP TABLE IF EXISTS `item_request_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_request_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity_requested` decimal(12,2) NOT NULL,
  `quantity_approved` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity_dispatched` decimal(12,2) NOT NULL DEFAULT 0.00,
  `uom` enum('grams','kg','liters','ml','pcs','units','bags','cartons') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_request_details_request_id_foreign` (`request_id`),
  KEY `item_request_details_item_id_foreign` (`item_id`),
  CONSTRAINT `item_request_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `item_request_details_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `item_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_request_details`
--

LOCK TABLES `item_request_details` WRITE;
/*!40000 ALTER TABLE `item_request_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_request_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_requests`
--

DROP TABLE IF EXISTS `item_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `requested_by` char(36) NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `request_date` date NOT NULL,
  `shift` enum('morning','afternoon') DEFAULT NULL,
  `status` enum('pending','approved','partially_dispatched','completed','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_requests_request_number_unique` (`request_number`),
  KEY `item_requests_branch_id_foreign` (`branch_id`),
  KEY `item_requests_department_id_foreign` (`department_id`),
  KEY `item_requests_requested_by_foreign` (`requested_by`),
  KEY `item_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `item_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `item_requests_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_requests_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_requests`
--

LOCK TABLES `item_requests` WRITE;
/*!40000 ALTER TABLE `item_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `category` enum('raw_material','packaging','consumable','equipment') NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units','bags','cartons') NOT NULL,
  `description` text DEFAULT NULL,
  `reorder_level` decimal(10,2) DEFAULT NULL,
  `max_stock_level` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_sku_unique` (`sku`),
  KEY `items_branch_id_foreign` (`branch_id`),
  CONSTRAINT `items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Sugar - White Granulated','CAL-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(2,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Flour - All Purpose','CAL-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(3,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Cocoa Powder - Premium Dark','CAL-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(4,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Butter - Salted','CAL-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(5,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Eggs - Large Grade A','CAL-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(6,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Vanilla Extract - Pure','CAL-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(7,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Chocolate Chips - Dark','CAL-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(8,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Milk - Fresh Whole','CAL-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(9,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Cream - Heavy Whipping','CAL-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(10,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Yeast - Active Dry','CAL-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(11,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Vegetable Oil - Cooking','CAL-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-10-29 07:33:51','2025-10-29 07:33:51'),(12,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Salt - Table Salt','CAL-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(13,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Cake Boxes - 10 inch','CAL-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(14,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Pastry Boxes - Small','CAL-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(15,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Paper Bags - Brown','CAL-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(16,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Plastic Food Containers','CAL-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(17,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Dishwashing Liquid - Industrial','CAL-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(18,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Paper Towels - Kitchen Roll','CAL-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(19,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Garbage Bags - Large','CAL-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(20,'019a2f19-bde1-73ae-b1f7-81d61b668e39','Mixing Bowls - Stainless Steel','CAL-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-10-29 07:33:52','2025-10-29 07:33:52'),(21,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Sugar - White Granulated','PHC-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-10-29 07:33:55','2025-10-29 07:33:55'),(22,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Flour - All Purpose','PHC-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-10-29 07:33:55','2025-10-29 07:33:55'),(23,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Cocoa Powder - Premium Dark','PHC-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-10-29 07:33:55','2025-10-29 07:33:55'),(24,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Butter - Salted','PHC-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-10-29 07:33:55','2025-10-29 07:33:55'),(25,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Eggs - Large Grade A','PHC-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-10-29 07:33:55','2025-10-29 07:33:55'),(26,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Vanilla Extract - Pure','PHC-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-10-29 07:33:56','2025-10-29 07:33:56'),(27,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Chocolate Chips - Dark','PHC-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-10-29 07:33:56','2025-10-29 07:33:56'),(28,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Milk - Fresh Whole','PHC-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-10-29 07:33:56','2025-10-29 07:33:56'),(29,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Cream - Heavy Whipping','PHC-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-10-29 07:33:57','2025-10-29 07:33:57'),(30,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Yeast - Active Dry','PHC-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-10-29 07:33:57','2025-10-29 07:33:57'),(31,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Vegetable Oil - Cooking','PHC-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-10-29 07:33:57','2025-10-29 07:33:57'),(32,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Salt - Table Salt','PHC-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-10-29 07:33:57','2025-10-29 07:33:57'),(33,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Cake Boxes - 10 inch','PHC-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-10-29 07:33:57','2025-10-29 07:33:57'),(34,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Pastry Boxes - Small','PHC-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-10-29 07:33:57','2025-10-29 07:33:57'),(35,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Paper Bags - Brown','PHC-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-10-29 07:33:58','2025-10-29 07:33:58'),(36,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Plastic Food Containers','PHC-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-10-29 07:33:58','2025-10-29 07:33:58'),(37,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Dishwashing Liquid - Industrial','PHC-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-10-29 07:33:58','2025-10-29 07:33:58'),(38,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Paper Towels - Kitchen Roll','PHC-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-10-29 07:33:58','2025-10-29 07:33:58'),(39,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Garbage Bags - Large','PHC-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-10-29 07:33:58','2025-10-29 07:33:58'),(40,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c','Mixing Bowls - Stainless Steel','PHC-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-10-29 07:33:59','2025-10-29 07:33:59'),(41,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Sugar - White Granulated','LAG-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-10-29 07:34:02','2025-10-29 07:34:02'),(42,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Flour - All Purpose','LAG-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-10-29 07:34:02','2025-10-29 07:34:02'),(43,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Cocoa Powder - Premium Dark','LAG-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-10-29 07:34:02','2025-10-29 07:34:02'),(44,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Butter - Salted','LAG-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-10-29 07:34:02','2025-10-29 07:34:02'),(45,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Eggs - Large Grade A','LAG-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-10-29 07:34:03','2025-10-29 07:34:03'),(46,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Vanilla Extract - Pure','LAG-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-10-29 07:34:03','2025-10-29 07:34:03'),(47,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Chocolate Chips - Dark','LAG-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-10-29 07:34:03','2025-10-29 07:34:03'),(48,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Milk - Fresh Whole','LAG-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-10-29 07:34:03','2025-10-29 07:34:03'),(49,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Cream - Heavy Whipping','LAG-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-10-29 07:34:04','2025-10-29 07:34:04'),(50,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Yeast - Active Dry','LAG-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-10-29 07:34:04','2025-10-29 07:34:04'),(51,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Vegetable Oil - Cooking','LAG-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-10-29 07:34:04','2025-10-29 07:34:04'),(52,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Salt - Table Salt','LAG-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-10-29 07:34:04','2025-10-29 07:34:04'),(53,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Cake Boxes - 10 inch','LAG-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-10-29 07:34:04','2025-10-29 07:34:04'),(54,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Pastry Boxes - Small','LAG-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-10-29 07:34:04','2025-10-29 07:34:04'),(55,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Paper Bags - Brown','LAG-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-10-29 07:34:05','2025-10-29 07:34:05'),(56,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Plastic Food Containers','LAG-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-10-29 07:34:05','2025-10-29 07:34:05'),(57,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Dishwashing Liquid - Industrial','LAG-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-10-29 07:34:05','2025-10-29 07:34:05'),(58,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Paper Towels - Kitchen Roll','LAG-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-10-29 07:34:05','2025-10-29 07:34:05'),(59,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Garbage Bags - Large','LAG-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-10-29 07:34:05','2025-10-29 07:34:05'),(60,'019a2f19-bf8a-713c-b295-f1c53d78fb12','Mixing Bowls - Stainless Steel','LAG-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-10-29 07:34:05','2025-10-29 07:34:05'),(61,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Sugar - White Granulated','ABJ-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-10-29 07:34:08','2025-10-29 07:34:08'),(62,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Flour - All Purpose','ABJ-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-10-29 07:34:08','2025-10-29 07:34:08'),(63,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Cocoa Powder - Premium Dark','ABJ-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-10-29 07:34:08','2025-10-29 07:34:08'),(64,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Butter - Salted','ABJ-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-10-29 07:34:08','2025-10-29 07:34:08'),(65,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Eggs - Large Grade A','ABJ-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-10-29 07:34:08','2025-10-29 07:34:08'),(66,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Vanilla Extract - Pure','ABJ-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(67,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Chocolate Chips - Dark','ABJ-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(68,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Milk - Fresh Whole','ABJ-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(69,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Cream - Heavy Whipping','ABJ-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(70,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Yeast - Active Dry','ABJ-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(71,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Vegetable Oil - Cooking','ABJ-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(72,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Salt - Table Salt','ABJ-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(73,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Cake Boxes - 10 inch','ABJ-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(74,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Pastry Boxes - Small','ABJ-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(75,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Paper Bags - Brown','ABJ-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(76,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Plastic Food Containers','ABJ-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-10-29 07:34:09','2025-10-29 07:34:09'),(77,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Dishwashing Liquid - Industrial','ABJ-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-10-29 07:34:10','2025-10-29 07:34:10'),(78,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Paper Towels - Kitchen Roll','ABJ-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-10-29 07:34:10','2025-10-29 07:34:10'),(79,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Garbage Bags - Large','ABJ-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-10-29 07:34:10','2025-10-29 07:34:10'),(80,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca','Mixing Bowls - Stainless Steel','ABJ-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-10-29 07:34:10','2025-10-29 07:34:10'),(81,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Sugar - White Granulated','ENU-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-10-29 07:34:11','2025-10-29 07:34:11'),(82,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Flour - All Purpose','ENU-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-10-29 07:34:11','2025-10-29 07:34:11'),(83,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Cocoa Powder - Premium Dark','ENU-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-10-29 07:34:11','2025-10-29 07:34:11'),(84,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Butter - Salted','ENU-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(85,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Eggs - Large Grade A','ENU-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(86,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Vanilla Extract - Pure','ENU-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(87,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Chocolate Chips - Dark','ENU-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(88,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Milk - Fresh Whole','ENU-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(89,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Cream - Heavy Whipping','ENU-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(90,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Yeast - Active Dry','ENU-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(91,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Vegetable Oil - Cooking','ENU-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(92,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Salt - Table Salt','ENU-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(93,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Cake Boxes - 10 inch','ENU-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-10-29 07:34:12','2025-10-29 07:34:12'),(94,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Pastry Boxes - Small','ENU-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-10-29 07:34:13','2025-10-29 07:34:13'),(95,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Paper Bags - Brown','ENU-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-10-29 07:34:13','2025-10-29 07:34:13'),(96,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Plastic Food Containers','ENU-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-10-29 07:34:13','2025-10-29 07:34:13'),(97,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Dishwashing Liquid - Industrial','ENU-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-10-29 07:34:13','2025-10-29 07:34:13'),(98,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Paper Towels - Kitchen Roll','ENU-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-10-29 07:34:13','2025-10-29 07:34:13'),(99,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Garbage Bags - Large','ENU-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-10-29 07:34:13','2025-10-29 07:34:13'),(100,'019a2f19-c0f6-7167-8a54-a58bb08550b7','Mixing Bowls - Stainless Steel','ENU-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-10-29 07:34:13','2025-10-29 07:34:13');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_09_22_145432_add_two_factor_columns_to_users_table',1),(5,'2025_10_02_163836_create_branches_table',1),(6,'2025_10_02_220032_create_department_categories_table',1),(7,'2025_10_04_053818_create_departments_table',1),(8,'2025_10_04_053818_create_employees_table',1),(9,'2025_10_05_192144_add_password_to_employees_table',1),(10,'2025_10_06_051232_add_auth_columns_to_employees_table',1),(11,'2025_10_07_030703_create_permission_tables',1),(12,'2025_10_08_223243_add_position_and_manager_to_employees_table',1),(13,'2025_10_09_012549_create_items_table',1),(14,'2025_10_09_012613_create_purchases_table',1),(15,'2025_10_09_012614_create_purchase_items_table',1),(16,'2025_10_09_012615_create_stocks_table',1),(17,'2025_10_09_012617_create_stock_movements_table',1),(18,'2025_10_09_012619_create_item_requests_table',1),(19,'2025_10_09_012621_create_item_request_details_table',1),(20,'2025_10_09_012622_create_item_dispatches_table',1),(21,'2025_10_09_012623_create_stock_takes_table',1),(22,'2025_10_09_012626_create_stock_take_details_table',1),(23,'2025_10_09_012627_create_health_checks_table',1),(24,'2025_10_13_050139_create_clock_ins_table',1),(25,'2025_10_15_053301_create_recipes_table',1),(26,'2025_10_15_053356_create_recipe_ingredients_table',1),(27,'2025_10_15_053520_create_shifts_table',1),(28,'2025_10_15_053537_create_daily_produces_table',1),(29,'2025_10_15_054442_create_production_records_table',1),(30,'2025_10_15_055019_create_production_requests_table',1),(31,'2025_10_15_055201_create_call_backs_table',1),(32,'2025_10_15_055221_create_raw_material_utilizations_table',1),(33,'2025_10_16_074716_create_product_types_table',1),(34,'2025_10_16_074717_create_products_table',1),(35,'2025_10_18_075757_add_branch_id_to_products_table',1),(36,'2025_10_18_194413_add_cost_and_waste_fields_to_recipe_ingredients_table',1),(37,'2025_10_19_030711_add_status_to_daily_produces_table',1),(38,'2025_10_19_083910_create_employee_shifts_table',1),(39,'2025_10_20_063309_create_sales_hifts_table',1),(40,'2025_10_20_063310_create_product_stocks_table',1),(41,'2025_10_20_063352_create_sales_table',1),(42,'2025_10_20_063409_create_sale_items_table',1),(43,'2025_10_20_063429_create_payments_table',1),(44,'2025_10_21_073703_create_product_callbacks_table',1),(45,'2025_10_22_094919_create_approved_items_table',1),(46,'2025_10_23_100000_create_expiry_confirmations_table',1),(47,'2025_10_23_120000_create_product_dispatches_table',1),(48,'2025_10_24_200648_add_batch_tracking_to_production_records_table',1),(49,'2025_10_25_045338_make_sales_shift_id_nullable_in_product_stocks_table',1),(50,'2025_10_25_073857_create_receipts_table',1),(51,'2025_10_27_000814_create_global_business_configurations_table',1),(52,'2025_10_27_000816_create_global_currency_localizations_table',1),(53,'2025_10_27_000818_create_global_branch_management_table',1),(54,'2025_10_27_000820_create_global_inventory_management_table',1),(55,'2025_10_27_000821_create_branch_inventory_management_table',1),(56,'2025_10_27_000823_create_global_employee_management_table',1),(57,'2025_10_27_000824_create_global_pos_configurations_table',1),(58,'2025_10_27_000827_create_global_accounting_cashes_table',1),(59,'2025_10_27_000829_create_global_customer_supplier_management_table',1),(60,'2025_10_27_000831_create_global_reports_analytics_table',1),(61,'2025_10_27_000833_create_global_security_accesses_table',1),(62,'2025_10_27_000835_create_global_notifications_alerts_table',1),(63,'2025_10_27_000837_create_audit_logs_table',1),(64,'2025_10_27_000838_create_approval_requests_table',1),(65,'2025_10_27_042254_remove_business_type_from_global_business_configurations',1),(66,'2025_10_27_042747_remove_columns_from_global_business_configurations',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (8,'App\\Models\\Employee','1ce1a4a4-0e67-33ba-ab99-271bbc7d5b5d'),(8,'App\\Models\\Employee','c357ef42-fe0d-369a-9ad8-448518d70e37'),(8,'App\\Models\\Employee','cec0cc61-4493-3327-9bdd-569929d13f41'),(8,'App\\Models\\Employee','d456e286-f8ae-3c8b-9688-9c0e5b1ffd04'),(8,'App\\Models\\Employee','e4dc55f4-09ac-3525-8664-e9fa0f00c9cd'),(9,'App\\Models\\Employee','6f42a8ff-e1e6-3560-bf8b-e17f568c134a'),(9,'App\\Models\\Employee','756cf9ea-af65-3770-9ca4-b65296ce1f49'),(9,'App\\Models\\Employee','c469b20c-58b4-3859-b71d-45e1b1c431e8'),(9,'App\\Models\\Employee','e401c6a7-859d-3ba9-8bcd-1e2dc81fecc7'),(9,'App\\Models\\Employee','f107a8ac-7c27-3470-b87a-a190d98092f5'),(10,'App\\Models\\Employee','15b12678-ec14-3578-9d33-c76f9f8498f4'),(10,'App\\Models\\Employee','2891f768-96f1-3708-82e2-9b17a9886c45'),(10,'App\\Models\\Employee','3f9ca8b3-c237-3e9c-91a8-c4db13600b2e'),(10,'App\\Models\\Employee','a642e202-9f55-33c3-bd24-b91dbf4ee777'),(10,'App\\Models\\Employee','ff83b476-f249-38c6-8196-a97f8cf8e6e7'),(11,'App\\Models\\Employee','0096b282-ce7b-379e-a979-0402e9195e9e'),(11,'App\\Models\\Employee','544512e3-d74e-335f-81ee-0748644d799e'),(11,'App\\Models\\Employee','a5903508-2103-36f4-aafe-6205252fce04'),(11,'App\\Models\\Employee','b3182133-1f1e-3c5b-ab0d-31f6356289f2'),(11,'App\\Models\\Employee','fad77429-6c57-3d9d-96a0-c2816c25e8cf'),(12,'App\\Models\\Employee','cdb735d3-ff10-3341-b806-c5989c342ab8'),(12,'App\\Models\\Employee','d7653847-e167-38d8-bd67-d3de3803ae85'),(12,'App\\Models\\Employee','e0e4effe-086c-3396-9a95-f25f394634b1'),(12,'App\\Models\\Employee','e194ae89-e6ba-39d3-999b-ef1802e3382d'),(12,'App\\Models\\Employee','fc15e9b6-5021-3f25-a271-f457f0b2c9c4'),(13,'App\\Models\\Employee','06c08dac-d94a-36ac-997b-a94a7f651471'),(13,'App\\Models\\Employee','29c824ad-fb8a-3c9b-8e83-19e37b09f8c7'),(13,'App\\Models\\Employee','2cdb22f7-32db-3fb5-9f4a-57f2d095e502'),(13,'App\\Models\\Employee','31551347-fcb3-35b9-b1a9-391758b65399'),(13,'App\\Models\\Employee','402ac4ac-26a5-3e11-bcd1-32b7b5a81bd4'),(13,'App\\Models\\Employee','46adffa9-2198-331e-bfcb-5db26772e3ad'),(13,'App\\Models\\Employee','48108713-1f14-39a5-a26d-5c6b3dc5246a'),(13,'App\\Models\\Employee','986fff17-19db-3600-9f55-989e7f7cc0c7'),(13,'App\\Models\\Employee','9a5cd20b-24e1-351d-a97b-788a22dadaad'),(13,'App\\Models\\Employee','b728aafc-4608-371a-b123-cbe44389a6d7'),(13,'App\\Models\\Employee','bf174f9c-320f-3b95-aba9-7e325da0bb81'),(13,'App\\Models\\Employee','c6b49bc7-385c-31e1-af37-cfb63f147f71'),(13,'App\\Models\\Employee','c998ba4f-5df0-3e28-8525-5da50568406b'),(13,'App\\Models\\Employee','ec5d87d0-38f1-3533-ac30-978b5eac1f23'),(13,'App\\Models\\Employee','fb18a32a-2a3b-3663-86d3-34daf6b14647'),(14,'App\\Models\\Employee','14ca919e-4a2e-319a-960c-e5dd4f6e679d'),(14,'App\\Models\\Employee','3a29f08c-566c-32ea-a635-5554aeefd8ab'),(14,'App\\Models\\Employee','4411d4c6-67fb-3dce-a475-9f8d6cf0d058'),(14,'App\\Models\\Employee','4a0bf585-4a6f-3d3c-a7c2-5c6240793c02'),(14,'App\\Models\\Employee','57530a02-afc1-333e-8471-b5c55ca3504c'),(14,'App\\Models\\Employee','5c1b76f5-624d-3fb7-92e7-ce150fcc1bb0'),(14,'App\\Models\\Employee','75f17701-ea28-3321-96a9-ec0489b54c3d'),(14,'App\\Models\\Employee','92e38cf7-2745-3706-97ee-b43df45bd2fb'),(14,'App\\Models\\Employee','d1de081b-57ee-3971-969a-7aee86843804'),(14,'App\\Models\\Employee','e57a001a-9772-3c78-9fbf-5a582a066266'),(15,'App\\Models\\Employee','215a3565-68ba-3910-b6c0-4d2f590501e8'),(15,'App\\Models\\Employee','3afd9e43-0ae6-3ffe-a85e-819b3ee31a46'),(15,'App\\Models\\Employee','6ba0acee-c543-3d76-90af-33e65c6a1955'),(15,'App\\Models\\Employee','72691061-592a-32cf-a17c-d8441f89c038'),(15,'App\\Models\\Employee','ab70cc57-3767-3df9-a875-481539a5015c'),(15,'App\\Models\\Employee','d1d953da-3950-337e-8a6c-7e1ff92ff36b'),(15,'App\\Models\\Employee','d6dd0904-8d3c-3f21-b40e-99b9916e6659'),(15,'App\\Models\\Employee','ee104e7e-d2d1-3a7a-a5fd-f7bd856c3088'),(15,'App\\Models\\Employee','f3f18a4b-6654-39a1-a305-a65e0334b5dd'),(15,'App\\Models\\Employee','fa9b2227-9c60-3e9e-be6c-a1e90e4a670e'),(16,'App\\Models\\Employee','0087cce4-5743-35cb-960d-eaf180f130ab'),(16,'App\\Models\\Employee','21dc23b7-f5ef-3815-a1ab-0ff7da65a133'),(16,'App\\Models\\Employee','4b7ad081-5576-37dd-988a-f4daf5501a89'),(16,'App\\Models\\Employee','6488d38a-7d14-359f-b688-411426d90bca'),(16,'App\\Models\\Employee','659cd289-b0cf-339a-b354-8774a6dbd257'),(16,'App\\Models\\Employee','72a4d37f-8ba1-335d-ab7d-62ac811b3baf'),(16,'App\\Models\\Employee','73d7cfd8-d396-3554-8dbc-e966d889f8b8'),(16,'App\\Models\\Employee','7ec1c366-04ae-3773-852e-dc26f5fdd6f4'),(16,'App\\Models\\Employee','90d18d0c-a92f-3948-aedd-47adac59c897'),(16,'App\\Models\\Employee','969f92ea-1f76-36e4-8a5e-34e0f960465a'),(16,'App\\Models\\Employee','96b818bb-5b49-3d7a-87fa-995a4b18864c'),(16,'App\\Models\\Employee','ac80660e-2ca0-3501-abaa-6521a81baf23'),(16,'App\\Models\\Employee','c927a925-e722-34be-83ea-2c20584df86c'),(16,'App\\Models\\Employee','ca24804a-039d-38b9-811f-145ca746cc3a'),(16,'App\\Models\\Employee','fafca340-b095-3dd5-9065-e0d661a1cd12'),(17,'App\\Models\\Employee','18fb5f49-1f27-3808-9356-3b7f85b01232'),(17,'App\\Models\\Employee','29e54463-476c-37b8-850b-6ac2fe3b7c08'),(17,'App\\Models\\Employee','2ce49628-3b70-39c5-88bb-e35f5d4b81b6'),(17,'App\\Models\\Employee','53e8b254-7196-3e07-ae19-4d17717a9b82'),(17,'App\\Models\\Employee','a6dc7408-6412-3e6e-93eb-d72f2b3e6cd2'),(17,'App\\Models\\Employee','ae6f1006-9b37-312f-803d-8c87a0d28f8c'),(17,'App\\Models\\Employee','b6339e8d-510c-3fbc-af87-6bca2077ac7a'),(17,'App\\Models\\Employee','cbba4f5b-e809-3bef-83aa-730772cfc5a4'),(17,'App\\Models\\Employee','dc49b60e-06f1-329f-86cd-e4ecdd001ed3'),(17,'App\\Models\\Employee','ef606b73-139a-307e-92c1-260cc1cc1d7c'),(18,'App\\Models\\Employee','023e3e2d-1ecf-3c65-b4df-0dd97a18aac6'),(18,'App\\Models\\Employee','0ec41491-b751-351e-b390-2f56f1f9f3c2'),(18,'App\\Models\\Employee','297fa011-e955-30f4-b391-927ed97ee879'),(18,'App\\Models\\Employee','51f7a4ea-a85c-315a-a151-ad3fe2541448'),(18,'App\\Models\\Employee','6e0e71ca-03bf-38ae-9aa9-9308f59d4815'),(18,'App\\Models\\Employee','859ccd79-9861-366d-8409-07abb8758ab1'),(18,'App\\Models\\Employee','bc7269a2-c59d-3842-b332-701d7cd4a5fe'),(18,'App\\Models\\Employee','dac3cbd1-b6e3-33bb-bb29-0dfd858593c1'),(18,'App\\Models\\Employee','e1125a64-15ab-33fb-af7b-50a5575a108b'),(18,'App\\Models\\Employee','fbb0d48f-9768-3e0d-9320-ddc288b41c9d'),(19,'App\\Models\\Employee','46d41b16-04f2-3665-91bb-b8defd768789'),(19,'App\\Models\\Employee','94cc6e26-51e4-32e8-b918-d2c9fdc9bcf4'),(19,'App\\Models\\Employee','abd9c8ab-73e5-335c-a381-8f0a0f2e53cc'),(19,'App\\Models\\Employee','ade922b6-689a-396b-ba69-97c9564ae99c'),(19,'App\\Models\\Employee','cf138f90-7769-39a7-b2be-b68f2c5074ab'),(20,'App\\Models\\Employee','2474bdb1-3e06-3acc-a704-d094a3446289'),(20,'App\\Models\\Employee','4dc82590-b38f-3ea7-9775-5a8537ea9247'),(20,'App\\Models\\Employee','9627dd07-674e-3589-8f98-31de69199c4b'),(20,'App\\Models\\Employee','bf26551e-05c6-309a-955d-16dad3f3c419'),(20,'App\\Models\\Employee','f74340dd-e310-3f1f-8565-2f35c296e2cd'),(22,'App\\Models\\User','019a2f19-bbba-7078-807a-1b34476665c7');
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('cash','pos','transfer','card','mobile') NOT NULL DEFAULT 'cash',
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_sale_id_foreign` (`sale_id`),
  CONSTRAINT `payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view-employee-dashboard','employees','2025-10-29 07:32:51','2025-10-29 07:32:51'),(2,'view-analytics','employees','2025-10-29 07:32:51','2025-10-29 07:32:51'),(3,'view-profile','employees','2025-10-29 07:32:52','2025-10-29 07:32:52'),(4,'edit-profile','employees','2025-10-29 07:32:52','2025-10-29 07:32:52'),(5,'view-production-queue','employees','2025-10-29 07:32:52','2025-10-29 07:32:52'),(6,'start-production','employees','2025-10-29 07:32:52','2025-10-29 07:32:52'),(7,'complete-production','employees','2025-10-29 07:32:53','2025-10-29 07:32:53'),(8,'approve-production','employees','2025-10-29 07:32:53','2025-10-29 07:32:53'),(9,'manage-recipes','employees','2025-10-29 07:32:53','2025-10-29 07:32:53'),(10,'process-sale','employees','2025-10-29 07:32:53','2025-10-29 07:32:53'),(11,'issue-refund','employees','2025-10-29 07:32:54','2025-10-29 07:32:54'),(12,'view-daily-sales','employees','2025-10-29 07:32:54','2025-10-29 07:32:54'),(13,'close-register','employees','2025-10-29 07:32:54','2025-10-29 07:32:54'),(14,'receive-stock','employees','2025-10-29 07:32:54','2025-10-29 07:32:54'),(15,'transfer-stock','employees','2025-10-29 07:32:54','2025-10-29 07:32:54'),(16,'adjust-inventory','employees','2025-10-29 07:32:55','2025-10-29 07:32:55'),(17,'view-stock-levels','employees','2025-10-29 07:32:55','2025-10-29 07:32:55'),(18,'view-employees','employees','2025-10-29 07:32:55','2025-10-29 07:32:55'),(19,'create-employees','employees','2025-10-29 07:32:55','2025-10-29 07:32:55'),(20,'edit-employees','employees','2025-10-29 07:32:56','2025-10-29 07:32:56'),(21,'delete-employees','employees','2025-10-29 07:32:56','2025-10-29 07:32:56'),(22,'assign-roles','employees','2025-10-29 07:32:56','2025-10-29 07:32:56'),(23,'view-departments','employees','2025-10-29 07:32:56','2025-10-29 07:32:56'),(24,'view-branches','employees','2025-10-29 07:32:56','2025-10-29 07:32:56'),(25,'view-roles','employees','2025-10-29 07:32:57','2025-10-29 07:32:57'),(26,'view-department-reports','employees','2025-10-29 07:32:57','2025-10-29 07:32:57'),(27,'manage-staff-schedule','employees','2025-10-29 07:32:57','2025-10-29 07:32:57'),(28,'view-orders','employees','2025-10-29 07:32:57','2025-10-29 07:32:57'),(29,'create-orders','employees','2025-10-29 07:32:58','2025-10-29 07:32:58'),(30,'edit-orders','employees','2025-10-29 07:32:58','2025-10-29 07:32:58'),(31,'delete-orders','employees','2025-10-29 07:32:58','2025-10-29 07:32:58'),(32,'process-orders','employees','2025-10-29 07:32:58','2025-10-29 07:32:58'),(33,'cancel-orders','employees','2025-10-29 07:32:59','2025-10-29 07:32:59'),(34,'view-products','employees','2025-10-29 07:32:59','2025-10-29 07:32:59'),(35,'create-products','employees','2025-10-29 07:32:59','2025-10-29 07:32:59'),(36,'edit-products','employees','2025-10-29 07:32:59','2025-10-29 07:32:59'),(37,'delete-products','employees','2025-10-29 07:32:59','2025-10-29 07:32:59'),(38,'manage-inventory','employees','2025-10-29 07:33:00','2025-10-29 07:33:00'),(39,'view-customers','employees','2025-10-29 07:33:00','2025-10-29 07:33:00'),(40,'create-customers','employees','2025-10-29 07:33:00','2025-10-29 07:33:00'),(41,'edit-customers','employees','2025-10-29 07:33:00','2025-10-29 07:33:00'),(42,'delete-customers','employees','2025-10-29 07:33:01','2025-10-29 07:33:01'),(43,'view-reports','employees','2025-10-29 07:33:01','2025-10-29 07:33:01'),(44,'generate-reports','employees','2025-10-29 07:33:01','2025-10-29 07:33:01'),(45,'export-reports','employees','2025-10-29 07:33:01','2025-10-29 07:33:01'),(46,'view-settings','employees','2025-10-29 07:33:02','2025-10-29 07:33:02'),(47,'edit-settings','employees','2025-10-29 07:33:02','2025-10-29 07:33:02'),(48,'view-employees','web','2025-10-29 07:33:02','2025-10-29 07:33:02'),(49,'create-employees','web','2025-10-29 07:33:02','2025-10-29 07:33:02'),(50,'edit-employees','web','2025-10-29 07:33:02','2025-10-29 07:33:02'),(51,'delete-employees','web','2025-10-29 07:33:03','2025-10-29 07:33:03'),(52,'view-roles','web','2025-10-29 07:33:03','2025-10-29 07:33:03'),(53,'create-roles','web','2025-10-29 07:33:03','2025-10-29 07:33:03'),(54,'edit-roles','web','2025-10-29 07:33:03','2025-10-29 07:33:03'),(55,'delete-roles','web','2025-10-29 07:33:04','2025-10-29 07:33:04'),(56,'view-permissions','web','2025-10-29 07:33:04','2025-10-29 07:33:04'),(57,'create-permissions','web','2025-10-29 07:33:04','2025-10-29 07:33:04'),(58,'edit-permissions','web','2025-10-29 07:33:04','2025-10-29 07:33:04'),(59,'delete-permissions','web','2025-10-29 07:33:05','2025-10-29 07:33:05'),(60,'view-branches','web','2025-10-29 07:33:05','2025-10-29 07:33:05'),(61,'create-branches','web','2025-10-29 07:33:05','2025-10-29 07:33:05'),(62,'edit-branches','web','2025-10-29 07:33:06','2025-10-29 07:33:06'),(63,'delete-branches','web','2025-10-29 07:33:06','2025-10-29 07:33:06'),(64,'view-system-settings','web','2025-10-29 07:33:06','2025-10-29 07:33:06'),(65,'edit-system-settings','web','2025-10-29 07:33:06','2025-10-29 07:33:06'),(66,'view-audit-logs','web','2025-10-29 07:33:06','2025-10-29 07:33:06'),(67,'view employees','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(68,'create employees','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(69,'edit employees','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(70,'delete employees','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(71,'approve employee leave','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(72,'view employee attendance','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(73,'manage payroll','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(74,'view hr reports','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(75,'assign departments','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(76,'approve recruitment','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(77,'view employee profile','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(78,'generate employee report','web','2025-10-29 07:33:14','2025-10-29 07:33:14'),(79,'terminate employee','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(80,'view salary structure','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(81,'update employee role','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(82,'view performance reviews','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(83,'view production batches','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(84,'create production batch','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(85,'edit production batch','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(86,'delete production batch','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(87,'approve production plan','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(88,'view production schedule','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(89,'manage production line','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(90,'monitor production status','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(91,'record production output','web','2025-10-29 07:33:15','2025-10-29 07:33:15'),(92,'update production cost','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(93,'view quality control report','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(94,'approve quality control','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(95,'view wastage reports','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(96,'update machinery status','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(97,'log equipment maintenance','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(98,'view inventory','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(99,'create inventory item','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(100,'edit inventory item','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(101,'delete inventory item','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(102,'adjust stock levels','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(103,'view stock history','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(104,'transfer stock','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(105,'receive stock','web','2025-10-29 07:33:16','2025-10-29 07:33:16'),(106,'issue raw materials','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(107,'view warehouse report','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(108,'manage warehouse location','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(109,'view expiry tracking','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(110,'mark damaged goods','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(111,'approve restock','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(112,'monitor inventory alerts','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(113,'view suppliers','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(114,'create supplier','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(115,'edit supplier','web','2025-10-29 07:33:17','2025-10-29 07:33:17'),(116,'delete supplier','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(117,'approve purchase order','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(118,'create purchase order','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(119,'edit purchase order','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(120,'view purchase history','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(121,'receive goods','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(122,'approve vendor payment','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(123,'view logistics status','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(124,'schedule delivery','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(125,'approve transportation','web','2025-10-29 07:33:18','2025-10-29 07:33:18'),(126,'track shipment','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(127,'view import/export records','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(128,'manage procurement report','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(129,'view sales records','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(130,'create sales order','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(131,'edit sales order','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(132,'delete sales order','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(133,'approve sales invoice','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(134,'process refund','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(135,'view financial dashboard','web','2025-10-29 07:33:19','2025-10-29 07:33:19'),(136,'approve expense','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(137,'view profit and loss','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(138,'manage tax settings','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(139,'generate sales reports','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(140,'approve discounts','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(141,'manage pricing structure','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(142,'view payment history','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(143,'view cash flow','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(144,'update financial policy','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(145,'view audit logs','web','2025-10-29 07:33:20','2025-10-29 07:33:20'),(146,'manage users','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(147,'manage roles','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(148,'manage permissions','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(149,'view system settings','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(150,'update company info','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(151,'backup database','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(152,'restore backup','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(153,'view activity logs','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(154,'manage notifications','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(155,'view dashboard','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(156,'access API tokens','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(157,'view analytics','web','2025-10-29 07:33:21','2025-10-29 07:33:21'),(158,'view KPI metrics','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(159,'manage departments','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(160,'configure approval workflow','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(161,'access admin panel','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(162,'view change history','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(163,'enable maintenance mode','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(164,'disable maintenance mode','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(165,'generate compliance report','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(166,'view supplier compliance','web','2025-10-29 07:33:22','2025-10-29 07:33:22'),(167,'approve compliance status','web','2025-10-29 07:33:23','2025-10-29 07:33:23'),(168,'view environmental reports','web','2025-10-29 07:33:23','2025-10-29 07:33:23'),(169,'manage food safety records','web','2025-10-29 07:33:23','2025-10-29 07:33:23'),(170,'view traceability logs','web','2025-10-29 07:33:23','2025-10-29 07:33:23'),(171,'approve export documents','web','2025-10-29 07:33:23','2025-10-29 07:33:23'),(172,'review regulatory submissions','web','2025-10-29 07:33:23','2025-10-29 07:33:23'),(173,'view recall reports','web','2025-10-29 07:33:23','2025-10-29 07:33:23');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_callbacks`
--

DROP TABLE IF EXISTS `product_callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_callbacks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_stock_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `sales_shift_id` bigint(20) unsigned NOT NULL,
  `recorded_by` char(36) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `reason` enum('expired','damaged','quality_issue','customer_return','other') NOT NULL,
  `notes` text DEFAULT NULL,
  `callback_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_callbacks_product_stock_id_foreign` (`product_stock_id`),
  KEY `product_callbacks_recorded_by_foreign` (`recorded_by`),
  KEY `product_callbacks_product_id_callback_time_index` (`product_id`,`callback_time`),
  KEY `product_callbacks_sales_shift_id_callback_time_index` (`sales_shift_id`,`callback_time`),
  KEY `product_callbacks_reason_index` (`reason`),
  CONSTRAINT `product_callbacks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_callbacks_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `product_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_callbacks_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_callbacks_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_callbacks`
--

LOCK TABLES `product_callbacks` WRITE;
/*!40000 ALTER TABLE `product_callbacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_callbacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_dispatches`
--

DROP TABLE IF EXISTS `product_dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_dispatches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `daily_produce_id` bigint(20) unsigned DEFAULT NULL,
  `production_shift_id` bigint(20) unsigned NOT NULL COMMENT 'Kitchen/Production shift',
  `product_id` char(36) NOT NULL,
  `dispatched_by` char(36) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `dispatch_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `shift_type` enum('morning','afternoon','night') NOT NULL,
  `dispatch_date` date NOT NULL,
  `received_by` char(36) DEFAULT NULL COMMENT 'Sales employee who received',
  `received_at` timestamp NULL DEFAULT NULL,
  `status` enum('dispatched','received','rejected') NOT NULL DEFAULT 'dispatched',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_dispatches_daily_produce_id_foreign` (`daily_produce_id`),
  KEY `product_dispatches_production_shift_id_foreign` (`production_shift_id`),
  KEY `product_dispatches_dispatched_by_foreign` (`dispatched_by`),
  KEY `product_dispatches_received_by_foreign` (`received_by`),
  KEY `product_dispatches_product_id_dispatch_date_shift_type_index` (`product_id`,`dispatch_date`,`shift_type`),
  KEY `product_dispatches_branch_id_dispatch_date_index` (`branch_id`,`dispatch_date`),
  CONSTRAINT `product_dispatches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_daily_produce_id_foreign` FOREIGN KEY (`daily_produce_id`) REFERENCES `daily_produces` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_dispatches_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_production_shift_id_foreign` FOREIGN KEY (`production_shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_dispatches`
--

LOCK TABLES `product_dispatches` WRITE;
/*!40000 ALTER TABLE `product_dispatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_dispatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_stocks`
--

DROP TABLE IF EXISTS `product_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_shift_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `stock_date` date NOT NULL,
  `shift_type` enum('morning','afternoon') NOT NULL,
  `opening_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `addition_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `production_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `callback_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `redress_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_available` decimal(12,2) NOT NULL DEFAULT 0.00,
  `transfer_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `glovo_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity_sold` decimal(12,2) NOT NULL DEFAULT 0.00,
  `closing_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_stock` (`sales_shift_id`,`product_id`,`stock_date`,`shift_type`),
  KEY `product_stocks_product_id_foreign` (`product_id`),
  CONSTRAINT `product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stocks_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_stocks`
--

LOCK TABLES `product_stocks` WRITE;
/*!40000 ALTER TABLE `product_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_types`
--

DROP TABLE IF EXISTS `product_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'Short code for product type (e.g., GB, GF, PT)',
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Display order',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_types_name_unique` (`name`),
  UNIQUE KEY `product_types_code_unique` (`code`),
  KEY `product_types_department_id_index` (`department_id`),
  KEY `product_types_status_index` (`status`),
  KEY `product_types_sort_order_index` (`sort_order`),
  CONSTRAINT `product_types_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_types`
--

LOCK TABLES `product_types` WRITE;
/*!40000 ALTER TABLE `product_types` DISABLE KEYS */;
INSERT INTO `product_types` VALUES (1,1,'Pastries','PT','Baked pastries including croissants, danishes, and puff pastries','active',1,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(2,1,'Breads','BR','Fresh baked breads, loaves, and rolls','active',2,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(3,1,'Cakes','CK','Layer cakes, sponge cakes, and celebration cakes','active',3,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(4,1,'Cookies','CO','Baked cookies and biscuits','active',4,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(5,1,'Muffins','MF','Sweet and savory muffins','active',5,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(6,1,'Pies & Tarts','PIT','Fruit pies, cream pies, and tarts','active',6,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(7,1,'Sandwiches','SW','Fresh sandwiches and wraps','active',7,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(8,1,'Hot Kitchen','HK','Cooked meals and hot food items','active',8,'2025-10-29 07:34:32','2025-10-29 07:34:32',NULL),(9,2,'Gelato Base','GB','Base gelato mixtures before flavoring','active',1,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(10,2,'Gelato Flavors','GF','Finished gelato in various flavors','active',2,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(11,2,'Sorbet','SB','Fruit-based frozen desserts without dairy','active',3,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(12,2,'Ice Cream','IC','Traditional ice cream products','active',4,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(13,2,'Frozen Yogurt','FY','Frozen yogurt in various flavors','active',5,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(14,2,'Gelato Toppings','GT','House-made toppings and mix-ins for gelato','active',6,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(15,3,'Chocolates','CH','Handcrafted chocolates and truffles','active',1,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(16,3,'Candies','CD','Hard and soft candies','active',2,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(17,3,'Fudge','FG','Traditional and flavored fudge','active',3,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(18,3,'Caramels','CR','Soft and hard caramels','active',4,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(19,3,'Marshmallows','MM','Gourmet marshmallows in various flavors','active',5,'2025-10-29 07:34:33','2025-10-29 07:34:33',NULL),(20,3,'Nougat','NG','Traditional nougat confections','active',6,'2025-10-29 07:34:34','2025-10-29 07:34:34',NULL);
/*!40000 ALTER TABLE `product_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_records`
--

DROP TABLE IF EXISTS `production_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `daily_produce_id` bigint(20) unsigned NOT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `produced_by` char(36) NOT NULL,
  `quantity_produced` decimal(12,2) NOT NULL,
  `quantity_approved` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity_sent_out` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity_for_order` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity_remaining` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity_rejected` decimal(12,2) NOT NULL DEFAULT 0.00,
  `production_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `quality_status` enum('excellent','good','acceptable','rejected') NOT NULL DEFAULT 'good',
  `dispatch_status` enum('available','partial','fully_dispatched') NOT NULL DEFAULT 'available',
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_records_daily_produce_id_foreign` (`daily_produce_id`),
  KEY `production_records_recipe_id_foreign` (`recipe_id`),
  KEY `production_records_produced_by_foreign` (`produced_by`),
  CONSTRAINT `production_records_daily_produce_id_foreign` FOREIGN KEY (`daily_produce_id`) REFERENCES `daily_produces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_records_produced_by_foreign` FOREIGN KEY (`produced_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `production_records_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_records`
--

LOCK TABLES `production_records` WRITE;
/*!40000 ALTER TABLE `production_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `production_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_requests`
--

DROP TABLE IF EXISTS `production_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint(20) unsigned NOT NULL,
  `item_request_id` bigint(20) unsigned NOT NULL,
  `recipe_id` bigint(20) unsigned DEFAULT NULL,
  `planned_production_quantity` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_requests_shift_id_foreign` (`shift_id`),
  KEY `production_requests_item_request_id_foreign` (`item_request_id`),
  KEY `production_requests_recipe_id_foreign` (`recipe_id`),
  CONSTRAINT `production_requests_item_request_id_foreign` FOREIGN KEY (`item_request_id`) REFERENCES `item_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_requests_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_requests_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_requests`
--

LOCK TABLES `production_requests` WRITE;
/*!40000 ALTER TABLE `production_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `production_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `product_type_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cost` decimal(10,2) DEFAULT NULL COMMENT 'Cost price for margin calculation',
  `shelf_life_days` int(11) NOT NULL DEFAULT 0,
  `uom` enum('grams','kg','liters','ml','pcs','units') NOT NULL DEFAULT 'pcs',
  `recipe_yield` decimal(10,2) NOT NULL DEFAULT 1.00 COMMENT 'How many units one recipe batch produces',
  `recipe_yield_weight` decimal(10,2) DEFAULT NULL COMMENT 'Total weight/volume of one recipe batch (in grams/ml)',
  `unit_weight` decimal(10,2) DEFAULT NULL COMMENT 'Weight of one unit (in grams/ml)',
  `yield_percentage` decimal(5,2) NOT NULL DEFAULT 100.00 COMMENT 'Expected yield percentage (accounts for waste/loss)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Available for production/sale',
  `image_url` varchar(255) DEFAULT NULL,
  `allergens` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'List of allergens' CHECK (json_valid(`allergens`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Product tags for filtering' CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_branch_id_foreign` (`branch_id`),
  KEY `products_product_type_id_index` (`product_type_id`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_is_active_index` (`is_active`),
  KEY `products_is_available_index` (`is_available`),
  KEY `products_created_at_index` (`created_at`),
  CONSTRAINT `products_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_product_type_id_foreign` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES ('019a2f1a-c21a-7327-ace1-f554318e3b9c','Butter Croissant','PT-BUT-001',NULL,1,NULL,'Classic French butter croissant, flaky and golden',3.50,1.20,2,'pcs',24.00,2400.00,100.00,95.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"breakfast\",\"french\",\"popular\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c255-7131-a00f-ea99e38ae770','Almond Danish','PT-ALM-002',NULL,1,NULL,'Sweet Danish pastry topped with sliced almonds',4.00,1.50,2,'pcs',18.00,2700.00,150.00,93.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\",\"nuts\"]','[\"breakfast\",\"pastry\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c281-72ca-87da-43b46d28811c','Sourdough Loaf','BR-SOU-001',NULL,2,NULL,'Artisan sourdough bread with tangy flavor',6.50,2.00,4,'pcs',4.00,3200.00,800.00,90.00,1,1,NULL,'[\"gluten\"]','[\"artisan\",\"sourdough\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c307-71ca-b382-8076545802e0','Banana Bread','BR-BAN-002',NULL,2,NULL,'Moist banana bread with walnuts',5.99,2.50,7,'pcs',2.00,1800.00,900.00,92.00,1,1,NULL,'[\"gluten\",\"eggs\",\"nuts\"]','[\"sweet\",\"popular\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c39d-72e2-8aa2-1f2dc2634952','Chocolate Cake Slice','CK-CHO-001',NULL,3,NULL,'Rich chocolate layer cake with chocolate ganache',7.50,2.80,5,'pcs',12.00,2400.00,200.00,95.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"chocolate\",\"dessert\",\"popular\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c42c-72a3-8312-07027b151812','Chocolate Chip Cookie','CO-CHI-001',NULL,4,NULL,'Classic chocolate chip cookies',2.50,0.80,10,'pcs',36.00,1800.00,50.00,96.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"popular\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c484-73a2-8624-160a488b8b3e','Oatmeal Raisin Cookie','CO-OAT-002',NULL,4,NULL,'Wholesome oatmeal cookies with plump raisins',2.50,0.75,10,'pcs',40.00,2000.00,50.00,96.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"healthy\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c4f4-7104-8bcc-be66a428b6b8','Vanilla Gelato Base','GB-VAN-001',NULL,9,NULL,'Premium vanilla gelato base mixture',0.00,8.50,3,'kg',5.00,5000.00,1000.00,98.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"base\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c529-7014-917f-d571ad574159','Chocolate Gelato','GF-CHO-001',NULL,10,NULL,'Rich dark chocolate gelato',4.50,1.80,30,'grams',5000.00,5000.00,100.00,97.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"chocolate\",\"popular\"]','2025-10-29 07:34:34','2025-10-29 07:34:34',NULL),('019a2f1a-c584-7325-9331-754d0ef870b8','Strawberry Gelato','GF-STR-002',NULL,10,NULL,'Fresh strawberry gelato',4.50,1.90,30,'grams',5000.00,5000.00,100.00,97.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"fruit\"]','2025-10-29 07:34:35','2025-10-29 07:34:35',NULL),('019a2f1a-c5af-714c-8ee7-facc827ff1a3','Pistachio Gelato','GF-PIS-003',NULL,10,NULL,'Authentic pistachio gelato from Sicily',5.50,2.50,30,'grams',5000.00,5000.00,100.00,96.00,1,1,NULL,'[\"dairy\",\"nuts\"]','[\"gelato\",\"premium\",\"nuts\"]','2025-10-29 07:34:35','2025-10-29 07:34:35',NULL),('019a2f1a-c5dc-700f-b151-2a98f81608e5','Dark Chocolate Truffle','CH-DAR-001',NULL,15,NULL,'Hand-rolled dark chocolate truffle',2.50,0.90,21,'pcs',50.00,1000.00,20.00,94.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"premium\",\"truffle\"]','2025-10-29 07:34:35','2025-10-29 07:34:35',NULL),('019a2f1a-c60a-72cc-bb25-f5984327bccc','Salted Caramel Chocolate','CH-SAL-002',NULL,15,NULL,'Milk chocolate with salted caramel filling',2.75,1.00,21,'pcs',48.00,1200.00,25.00,93.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"caramel\",\"popular\"]','2025-10-29 07:34:35','2025-10-29 07:34:35',NULL),('019a2f1a-c640-7186-8b0c-6ae9f190cde5','Fruit Gummies','CD-FRU-001',NULL,16,NULL,'Assorted fruit-flavored gummy candies',1.50,0.40,180,'grams',2000.00,2000.00,100.00,95.00,1,1,NULL,'[]','[\"candy\",\"fruit\",\"kids\"]','2025-10-29 07:34:35','2025-10-29 07:34:35',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units','bags','cartons') NOT NULL,
  `fob_fc` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fob_ngn` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_costs` decimal(12,2) NOT NULL DEFAULT 0.00,
  `landing_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(12,2) NOT NULL,
  `cost_per_unit` decimal(12,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_item_id_foreign` (`item_id`),
  CONSTRAINT `purchase_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `recorded_by` char(36) NOT NULL,
  `purchase_number` varchar(255) NOT NULL,
  `purchase_date` date NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_contact` varchar(255) DEFAULT NULL,
  `total_fob_fc` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_fob_ngn` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_costs` decimal(12,2) NOT NULL DEFAULT 0.00,
  `landing_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) NOT NULL DEFAULT 'NGN',
  `exchange_rate` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `payment_status` enum('paid','partial','pending') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_purchase_number_unique` (`purchase_number`),
  KEY `purchases_branch_id_foreign` (`branch_id`),
  KEY `purchases_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `purchases_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raw_material_utilizations`
--

DROP TABLE IF EXISTS `raw_material_utilizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raw_material_utilizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint(20) unsigned NOT NULL,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity_required` decimal(12,4) NOT NULL,
  `quantity_used` decimal(12,4) NOT NULL,
  `units_produced` decimal(12,2) NOT NULL,
  `variance` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `variance_type` enum('within_tolerance','over_used','under_used') NOT NULL DEFAULT 'within_tolerance',
  `cost_impact` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `raw_material_utilizations_recipe_id_foreign` (`recipe_id`),
  KEY `raw_material_utilizations_item_id_foreign` (`item_id`),
  KEY `raw_material_utilizations_shift_id_recipe_id_index` (`shift_id`,`recipe_id`),
  CONSTRAINT `raw_material_utilizations_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `raw_material_utilizations_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `raw_material_utilizations_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raw_material_utilizations`
--

LOCK TABLES `raw_material_utilizations` WRITE;
/*!40000 ALTER TABLE `raw_material_utilizations` DISABLE KEYS */;
/*!40000 ALTER TABLE `raw_material_utilizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `receipt_number` char(36) NOT NULL,
  `content` longtext NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `payments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payments`)),
  `change_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipts_receipt_number_unique` (`receipt_number`),
  KEY `receipts_sale_id_foreign` (`sale_id`),
  CONSTRAINT `receipts_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipts`
--

LOCK TABLES `receipts` WRITE;
/*!40000 ALTER TABLE `receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipe_ingredients`
--

DROP TABLE IF EXISTS `recipe_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipe_ingredients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units') NOT NULL,
  `cost_per_unit` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `waste_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Percentage of ingredient lost during preparation',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `preparation_notes` text DEFAULT NULL COMMENT 'Specific prep instructions for this ingredient',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_ingredients_recipe_id_foreign` (`recipe_id`),
  KEY `recipe_ingredients_item_id_foreign` (`item_id`),
  CONSTRAINT `recipe_ingredients_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `recipe_ingredients_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_ingredients`
--

LOCK TABLES `recipe_ingredients` WRITE;
/*!40000 ALTER TABLE `recipe_ingredients` DISABLE KEYS */;
INSERT INTO `recipe_ingredients` VALUES (1,1,1,500.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:15','2025-10-29 07:34:15'),(2,1,2,250.0000,'grams',0.0000,0.00,2,'Butter',NULL,'2025-10-29 07:34:15','2025-10-29 07:34:15'),(3,1,3,300.0000,'grams',0.0000,0.00,3,'Chicken (shredded)',NULL,'2025-10-29 07:34:15','2025-10-29 07:34:15'),(4,1,4,1.0000,'grams',0.0000,0.00,4,'Onion',NULL,'2025-10-29 07:34:15','2025-10-29 07:34:15'),(5,1,5,1.0000,'grams',0.0000,0.00,5,'Carrot',NULL,'2025-10-29 07:34:15','2025-10-29 07:34:15'),(6,1,6,1.0000,'grams',0.0000,0.00,6,'Seasoning cube',NULL,'2025-10-29 07:34:15','2025-10-29 07:34:15'),(7,1,7,1.0000,'grams',0.0000,0.00,7,'Salt',NULL,'2025-10-29 07:34:15','2025-10-29 07:34:15'),(8,1,8,100.0000,'grams',0.0000,0.00,8,'Water',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(9,2,1,400.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(10,2,2,200.0000,'grams',0.0000,0.00,2,'Butter',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(11,2,3,250.0000,'grams',0.0000,0.00,3,'Ground beef',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(12,2,4,1.0000,'grams',0.0000,0.00,4,'Onion',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(13,2,5,0.5000,'grams',0.0000,0.00,5,'Salt',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(14,2,6,0.2500,'grams',0.0000,0.00,6,'Black pepper',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(15,2,7,1.0000,'grams',0.0000,0.00,7,'Egg',NULL,'2025-10-29 07:34:16','2025-10-29 07:34:16'),(16,3,1,450.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(17,3,2,220.0000,'grams',0.0000,0.00,2,'Butter',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(18,3,3,250.0000,'grams',0.0000,0.00,3,'Sausage meat',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(19,3,4,0.5000,'grams',0.0000,0.00,4,'Salt',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(20,3,5,1.0000,'grams',0.0000,0.00,5,'Egg (for brushing)',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(21,3,6,80.0000,'grams',0.0000,0.00,6,'Water',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(22,4,1,500.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(23,4,2,100.0000,'grams',0.0000,0.00,2,'Sugar',NULL,'2025-10-29 07:34:17','2025-10-29 07:34:17'),(24,4,3,10.0000,'grams',0.0000,0.00,3,'Yeast',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(25,4,4,350.0000,'grams',0.0000,0.00,4,'Water',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(26,4,5,0.5000,'grams',0.0000,0.00,5,'Salt',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(27,4,6,1000.0000,'grams',0.0000,0.00,6,'Oil (for frying)',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(28,5,1,500.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(29,5,2,7.0000,'grams',0.0000,0.00,2,'Yeast',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(30,5,3,80.0000,'grams',0.0000,0.00,3,'Sugar',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(31,5,4,60.0000,'grams',0.0000,0.00,4,'Butter',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(32,5,5,150.0000,'grams',0.0000,0.00,5,'Milk',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(33,5,6,1.0000,'grams',0.0000,0.00,6,'Egg',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(34,5,7,1000.0000,'grams',0.0000,0.00,7,'Oil',NULL,'2025-10-29 07:34:18','2025-10-29 07:34:18'),(35,6,1,600.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(36,6,2,300.0000,'grams',0.0000,0.00,2,'Butter',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(37,6,3,300.0000,'grams',0.0000,0.00,3,'Minced beef',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(38,6,4,1.0000,'grams',0.0000,0.00,4,'Potato',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(39,6,5,1.0000,'grams',0.0000,0.00,5,'Carrot',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(40,6,6,1.0000,'grams',0.0000,0.00,6,'Onion',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(41,6,7,1.0000,'grams',0.0000,0.00,7,'Salt',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(42,6,8,100.0000,'grams',0.0000,0.00,8,'Water',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(43,7,1,500.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(44,7,2,250.0000,'grams',0.0000,0.00,2,'Butter',NULL,'2025-10-29 07:34:19','2025-10-29 07:34:19'),(45,7,3,200.0000,'grams',0.0000,0.00,3,'Canned tuna',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(46,7,4,1.0000,'grams',0.0000,0.00,4,'Onion',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(47,7,5,0.5000,'grams',0.0000,0.00,5,'Pepper',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(48,7,6,0.5000,'grams',0.0000,0.00,6,'Salt',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(49,7,7,1.0000,'grams',0.0000,0.00,7,'Egg',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(50,8,1,400.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(51,8,2,50.0000,'grams',0.0000,0.00,2,'Sugar',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(52,8,3,1.0000,'grams',0.0000,0.00,3,'Baking powder',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(53,8,4,4.0000,'grams',0.0000,0.00,4,'Egg (boiled)',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(54,8,5,100.0000,'grams',0.0000,0.00,5,'Water',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(55,8,6,1000.0000,'grams',0.0000,0.00,6,'Oil',NULL,'2025-10-29 07:34:20','2025-10-29 07:34:20'),(56,9,1,4.0000,'grams',0.0000,0.00,1,'Egg (boiled)',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(57,9,2,300.0000,'grams',0.0000,0.00,2,'Sausage meat',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(58,9,3,50.0000,'grams',0.0000,0.00,3,'Flour',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(59,9,4,100.0000,'grams',0.0000,0.00,4,'Bread crumbs',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(60,9,5,1000.0000,'grams',0.0000,0.00,5,'Oil',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(61,10,1,500.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(62,10,2,100.0000,'grams',0.0000,0.00,2,'Sugar',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(63,10,3,100.0000,'grams',0.0000,0.00,3,'Butter',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(64,10,4,100.0000,'grams',0.0000,0.00,4,'Milk',NULL,'2025-10-29 07:34:21','2025-10-29 07:34:21'),(65,10,5,1.0000,'grams',0.0000,0.00,5,'Egg',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(66,10,6,1.0000,'grams',0.0000,0.00,6,'Baking powder',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(67,10,7,1000.0000,'grams',0.0000,0.00,7,'Oil',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(68,11,1,5.0000,'grams',0.0000,0.00,1,'Plantain',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(69,11,2,1.0000,'grams',0.0000,0.00,2,'Salt',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(70,11,3,1000.0000,'grams',0.0000,0.00,3,'Oil',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(71,12,1,200.0000,'grams',0.0000,0.00,1,'Corn kernels',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(72,12,2,2.0000,'grams',0.0000,0.00,2,'Oil',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(73,12,3,50.0000,'grams',0.0000,0.00,3,'Sugar',NULL,'2025-10-29 07:34:22','2025-10-29 07:34:22'),(74,12,4,0.5000,'grams',0.0000,0.00,4,'Salt',NULL,'2025-10-29 07:34:23','2025-10-29 07:34:23'),(75,13,1,300.0000,'grams',0.0000,0.00,1,'Groundnuts',NULL,'2025-10-29 07:34:23','2025-10-29 07:34:23'),(76,13,2,200.0000,'grams',0.0000,0.00,2,'Flour',NULL,'2025-10-29 07:34:23','2025-10-29 07:34:23'),(77,13,3,50.0000,'grams',0.0000,0.00,3,'Sugar',NULL,'2025-10-29 07:34:23','2025-10-29 07:34:23'),(78,13,4,1.0000,'grams',0.0000,0.00,4,'Egg',NULL,'2025-10-29 07:34:23','2025-10-29 07:34:23'),(79,13,5,2.0000,'grams',0.0000,0.00,5,'Milk powder',NULL,'2025-10-29 07:34:23','2025-10-29 07:34:23'),(80,13,6,1000.0000,'grams',0.0000,0.00,6,'Oil',NULL,'2025-10-29 07:34:23','2025-10-29 07:34:23'),(81,14,1,250.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:24','2025-10-29 07:34:24'),(82,14,2,150.0000,'grams',0.0000,0.00,2,'Sugar',NULL,'2025-10-29 07:34:24','2025-10-29 07:34:24'),(83,14,3,150.0000,'grams',0.0000,0.00,3,'Butter',NULL,'2025-10-29 07:34:24','2025-10-29 07:34:24'),(84,14,4,3.0000,'grams',0.0000,0.00,4,'Egg',NULL,'2025-10-29 07:34:24','2025-10-29 07:34:24'),(85,14,5,1.0000,'grams',0.0000,0.00,5,'Baking powder',NULL,'2025-10-29 07:34:24','2025-10-29 07:34:24'),(86,14,6,100.0000,'grams',0.0000,0.00,6,'Milk',NULL,'2025-10-29 07:34:24','2025-10-29 07:34:24'),(87,14,7,1.0000,'grams',0.0000,0.00,7,'Vanilla essence',NULL,'2025-10-29 07:34:24','2025-10-29 07:34:24'),(88,15,1,300.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(89,15,2,120.0000,'grams',0.0000,0.00,2,'Sugar',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(90,15,3,200.0000,'grams',0.0000,0.00,3,'Butter',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(91,15,4,1.0000,'grams',0.0000,0.00,4,'Egg',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(92,15,5,100.0000,'grams',0.0000,0.00,5,'Chocolate chips',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(93,15,6,0.5000,'grams',0.0000,0.00,6,'Baking soda',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(94,16,1,250.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(95,16,2,125.0000,'grams',0.0000,0.00,2,'Butter',NULL,'2025-10-29 07:34:25','2025-10-29 07:34:25'),(96,16,3,120.0000,'grams',0.0000,0.00,3,'Sugar',NULL,'2025-10-29 07:34:26','2025-10-29 07:34:26'),(97,16,4,2.0000,'grams',0.0000,0.00,4,'Egg',NULL,'2025-10-29 07:34:26','2025-10-29 07:34:26'),(98,16,5,1.0000,'grams',0.0000,0.00,5,'Baking powder',NULL,'2025-10-29 07:34:26','2025-10-29 07:34:26'),(99,16,6,80.0000,'grams',0.0000,0.00,6,'Milk',NULL,'2025-10-29 07:34:27','2025-10-29 07:34:27'),(100,17,1,4.0000,'grams',0.0000,0.00,1,'Bread slices',NULL,'2025-10-29 07:34:27','2025-10-29 07:34:27'),(101,17,2,2.0000,'grams',0.0000,0.00,2,'Lettuce',NULL,'2025-10-29 07:34:27','2025-10-29 07:34:27'),(102,17,3,2.0000,'grams',0.0000,0.00,3,'Tomato',NULL,'2025-10-29 07:34:27','2025-10-29 07:34:27'),(103,17,4,2.0000,'grams',0.0000,0.00,4,'Cucumber',NULL,'2025-10-29 07:34:27','2025-10-29 07:34:27'),(104,17,5,1.0000,'grams',0.0000,0.00,5,'Egg',NULL,'2025-10-29 07:34:27','2025-10-29 07:34:27'),(105,17,6,1.0000,'grams',0.0000,0.00,6,'Mayonnaise',NULL,'2025-10-29 07:34:28','2025-10-29 07:34:28'),(106,18,1,500.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:28','2025-10-29 07:34:28'),(107,18,2,7.0000,'grams',0.0000,0.00,2,'Yeast',NULL,'2025-10-29 07:34:28','2025-10-29 07:34:28'),(108,18,3,60.0000,'grams',0.0000,0.00,3,'Sugar',NULL,'2025-10-29 07:34:28','2025-10-29 07:34:28'),(109,18,4,50.0000,'grams',0.0000,0.00,4,'Butter',NULL,'2025-10-29 07:34:28','2025-10-29 07:34:28'),(110,18,5,100.0000,'grams',0.0000,0.00,5,'Milk',NULL,'2025-10-29 07:34:29','2025-10-29 07:34:29'),(111,18,6,1.0000,'grams',0.0000,0.00,6,'Egg',NULL,'2025-10-29 07:34:29','2025-10-29 07:34:29'),(112,18,7,150.0000,'grams',0.0000,0.00,7,'Water',NULL,'2025-10-29 07:34:29','2025-10-29 07:34:29'),(113,19,1,500.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:29','2025-10-29 07:34:29'),(114,19,2,7.0000,'grams',0.0000,0.00,2,'Yeast',NULL,'2025-10-29 07:34:29','2025-10-29 07:34:29'),(115,19,3,60.0000,'grams',0.0000,0.00,3,'Sugar',NULL,'2025-10-29 07:34:30','2025-10-29 07:34:30'),(116,19,4,50.0000,'grams',0.0000,0.00,4,'Butter',NULL,'2025-10-29 07:34:30','2025-10-29 07:34:30'),(117,19,5,100.0000,'grams',0.0000,0.00,5,'Milk',NULL,'2025-10-29 07:34:30','2025-10-29 07:34:30'),(118,19,6,1.0000,'grams',0.0000,0.00,6,'Egg',NULL,'2025-10-29 07:34:30','2025-10-29 07:34:30'),(119,19,7,150.0000,'grams',0.0000,0.00,7,'Water',NULL,'2025-10-29 07:34:30','2025-10-29 07:34:30'),(120,20,1,400.0000,'grams',0.0000,0.00,1,'Flour',NULL,'2025-10-29 07:34:30','2025-10-29 07:34:30'),(121,20,2,7.0000,'grams',0.0000,0.00,2,'Yeast',NULL,'2025-10-29 07:34:31','2025-10-29 07:34:31'),(122,20,3,100.0000,'grams',0.0000,0.00,3,'Tomato paste',NULL,'2025-10-29 07:34:31','2025-10-29 07:34:31'),(123,20,4,150.0000,'grams',0.0000,0.00,4,'Mozzarella cheese',NULL,'2025-10-29 07:34:31','2025-10-29 07:34:31'),(124,20,5,100.0000,'grams',0.0000,0.00,5,'Sausage',NULL,'2025-10-29 07:34:31','2025-10-29 07:34:31'),(125,20,6,1.0000,'grams',0.0000,0.00,6,'Onion',NULL,'2025-10-29 07:34:31','2025-10-29 07:34:31'),(126,20,7,0.5000,'grams',0.0000,0.00,7,'Pepper',NULL,'2025-10-29 07:34:31','2025-10-29 07:34:31'),(127,20,8,2.0000,'grams',0.0000,0.00,8,'Oil',NULL,'2025-10-29 07:34:32','2025-10-29 07:34:32');
/*!40000 ALTER TABLE `recipe_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `product_type` enum('gelato_base','gelato_flavor','pastry','hot_kitchen','beverage') NOT NULL,
  `cost_per_unit` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `uom` enum('grams','kg','liters','ml','pcs','units') NOT NULL DEFAULT 'pcs',
  `yield_quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `preparation_time` int(11) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `status` enum('active','inactive','testing') NOT NULL DEFAULT 'active',
  `created_by` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipes_sku_unique` (`sku`),
  KEY `recipes_branch_id_foreign` (`branch_id`),
  KEY `recipes_department_id_foreign` (`department_id`),
  KEY `recipes_created_by_foreign` (`created_by`),
  CONSTRAINT `recipes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `recipes_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (1,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Chicken Pie','CHICKEN-PIE-UjTx','pastry',23.6000,'pcs',5.00,50,'Prepare Chicken Pie according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:15','2025-10-29 07:34:15'),(2,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Beef Roll','BEEF-ROLL-HOX4','pastry',28.9000,'pcs',17.00,51,'Prepare Beef Roll according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:16','2025-10-29 07:34:16'),(3,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Sausage Roll','SAUSAGE-ROLL-QH4i','pastry',29.8000,'pcs',14.00,51,'Prepare Sausage Roll according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:17','2025-10-29 07:34:17'),(4,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Puff Puff','PUFF-PUFF-G3SQ','pastry',27.4000,'pcs',12.00,58,'Prepare Puff Puff according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:17','2025-10-29 07:34:17'),(5,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Doughnut','DOUGHNUT-ljQr','pastry',23.2000,'pcs',19.00,70,'Prepare Doughnut according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:18','2025-10-29 07:34:18'),(6,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Meat Pie','MEAT-PIE-jw0v','pastry',14.0000,'pcs',12.00,65,'Prepare Meat Pie according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:18','2025-10-29 07:34:18'),(7,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Fish Pie','FISH-PIE-2lJY','pastry',33.7000,'pcs',7.00,23,'Prepare Fish Pie according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:19','2025-10-29 07:34:19'),(8,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Egg Roll','EGG-ROLL-b0Or','pastry',42.0000,'pcs',14.00,80,'Prepare Egg Roll according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:20','2025-10-29 07:34:20'),(9,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Scotch Egg','SCOTCH-EGG-zIGU','pastry',21.5000,'pcs',17.00,86,'Prepare Scotch Egg according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:21','2025-10-29 07:34:21'),(10,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Chin Chin','CHIN-CHIN-dgYg','pastry',8.0000,'pcs',11.00,41,'Prepare Chin Chin according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:21','2025-10-29 07:34:21'),(11,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Plantain Chips','PLANTAIN-CHIPS-YrLG','pastry',47.2000,'pcs',10.00,60,'Prepare Plantain Chips according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:22','2025-10-29 07:34:22'),(12,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Popcorn','POPCORN-bwyx','pastry',26.3000,'pcs',20.00,50,'Prepare Popcorn according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:22','2025-10-29 07:34:22'),(13,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Peanut Burger','PEANUT-BURGER-W1JO','pastry',37.3000,'pcs',7.00,60,'Prepare Peanut Burger according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:23','2025-10-29 07:34:23'),(14,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Cake Slice','CAKE-SLICE-fI8h','pastry',41.8000,'pcs',15.00,48,'Prepare Cake Slice according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:23','2025-10-29 07:34:23'),(15,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Cookies','COOKIES-1cXm','pastry',35.4000,'pcs',15.00,90,'Prepare Cookies according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:24','2025-10-29 07:34:24'),(16,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Cupcake','CUPCAKE-sLYH','pastry',7.4000,'pcs',12.00,74,'Prepare Cupcake according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:25','2025-10-29 07:34:25'),(17,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Sandwich','SANDWICH-xMqP','pastry',33.9000,'pcs',19.00,49,'Prepare Sandwich according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:27','2025-10-29 07:34:27'),(18,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Burger Bun','BURGER-BUN-rYpK','pastry',38.9000,'pcs',17.00,73,'Prepare Burger Bun according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:28','2025-10-29 07:34:28'),(19,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Hotdog Bun','HOTDOG-BUN-xkUa','pastry',39.0000,'pcs',8.00,28,'Prepare Hotdog Bun according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:29','2025-10-29 07:34:29'),(20,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',4,'Pizza Slice','PIZZA-SLICE-IirQ','pastry',45.2000,'pcs',5.00,20,'Prepare Pizza Slice according to standard recipe steps.','active','06c08dac-d94a-36ac-997b-a94a7f651471','2025-10-29 07:34:30','2025-10-29 07:34:30');
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,3),(2,3),(2,4),(2,5),(2,6),(2,7),(3,3),(4,3),(5,3),(5,4),(5,8),(5,9),(5,10),(5,13),(5,14),(5,15),(6,3),(6,4),(6,8),(6,9),(6,10),(6,13),(6,14),(6,15),(7,3),(7,4),(7,8),(7,9),(7,10),(7,13),(7,14),(7,15),(8,3),(8,4),(9,3),(9,4),(9,8),(9,9),(9,10),(10,3),(10,5),(10,10),(10,11),(10,12),(10,16),(10,17),(10,18),(11,3),(11,5),(11,11),(11,12),(12,3),(12,5),(12,8),(12,10),(12,11),(12,12),(12,16),(12,17),(12,18),(13,3),(13,5),(13,11),(13,12),(14,3),(14,7),(14,19),(14,20),(15,3),(15,7),(15,19),(16,3),(16,7),(16,19),(17,3),(17,4),(17,5),(17,7),(17,8),(17,9),(17,10),(17,11),(17,12),(17,13),(17,14),(17,15),(17,16),(17,17),(17,18),(17,19),(17,20),(18,3),(18,4),(18,5),(18,6),(18,7),(18,8),(18,9),(18,10),(18,11),(18,12),(18,21),(19,3),(19,6),(19,21),(20,3),(20,6),(20,21),(21,3),(21,6),(22,3),(22,6),(23,3),(23,4),(23,5),(23,6),(23,7),(23,21),(24,3),(24,6),(24,21),(25,3),(25,6),(26,3),(26,4),(26,5),(26,6),(26,7),(27,3),(27,4),(27,5),(27,6),(28,3),(29,3),(30,3),(31,3),(32,3),(33,3),(34,3),(35,3),(36,3),(37,3),(38,3),(39,3),(40,3),(41,3),(42,3),(43,3),(44,3),(45,3),(46,3),(47,3),(48,1),(48,2),(49,1),(49,2),(50,1),(50,2),(51,2),(52,1),(52,2),(53,2),(54,2),(55,2),(56,1),(56,2),(57,2),(58,2),(59,2),(60,1),(60,2),(61,1),(61,2),(62,1),(62,2),(63,2),(64,2),(65,2),(66,2),(67,22),(68,22),(69,22),(70,22),(71,22),(72,22),(73,22),(74,22),(75,22),(76,22),(77,22),(78,22),(79,22),(80,22),(81,22),(82,22),(83,22),(84,22),(85,22),(86,22),(87,22),(88,22),(89,22),(90,22),(91,22),(92,22),(93,22),(94,22),(95,22),(96,22),(97,22),(98,22),(99,22),(100,22),(101,22),(102,22),(103,22),(104,22),(105,22),(106,22),(107,22),(108,22),(109,22),(110,22),(111,22),(112,22),(113,22),(114,22),(115,22),(116,22),(117,22),(118,22),(119,22),(120,22),(121,22),(122,22),(123,22),(124,22),(125,22),(126,22),(127,22),(128,22),(129,22),(130,22),(131,22),(132,22),(133,22),(134,22),(135,22),(136,22),(137,22),(138,22),(139,22),(140,22),(141,22),(142,22),(143,22),(144,22),(145,22),(146,22),(147,22),(148,22),(149,22),(150,22),(151,22),(152,22),(153,22),(154,22),(155,22),(156,22),(157,22),(158,22),(159,22),(160,22),(161,22),(162,22),(163,22),(164,22),(165,22),(166,22),(167,22),(168,22),(169,22),(170,22),(171,22),(172,22),(173,22);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','web','2025-10-29 07:33:07','2025-10-29 07:33:07'),(2,'Super Admin','web','2025-10-29 07:33:07','2025-10-29 07:33:07'),(3,'Managing Director','employees','2025-10-29 07:33:07','2025-10-29 07:33:07'),(4,'Head of Production','employees','2025-10-29 07:33:07','2025-10-29 07:33:07'),(5,'Sales Manager','employees','2025-10-29 07:33:08','2025-10-29 07:33:08'),(6,'HR Manager','employees','2025-10-29 07:33:08','2025-10-29 07:33:08'),(7,'Inventory Manager','employees','2025-10-29 07:33:08','2025-10-29 07:33:08'),(8,'Chef','employees','2025-10-29 07:33:09','2025-10-29 07:33:09'),(9,'Head of Gelato','employees','2025-10-29 07:33:09','2025-10-29 07:33:09'),(10,'Confectionaries Manager','employees','2025-10-29 07:33:09','2025-10-29 07:33:09'),(11,'Till Supervisor','employees','2025-10-29 07:33:10','2025-10-29 07:33:10'),(12,'Corner Store Manager','employees','2025-10-29 07:33:10','2025-10-29 07:33:10'),(13,'Kitchen Staff','employees','2025-10-29 07:33:10','2025-10-29 07:33:10'),(14,'Gelato Production Staff','employees','2025-10-29 07:33:11','2025-10-29 07:33:11'),(15,'Confectionaries Production Staff','employees','2025-10-29 07:33:11','2025-10-29 07:33:11'),(16,'Cashier','employees','2025-10-29 07:33:11','2025-10-29 07:33:11'),(17,'Corner Store Staff','employees','2025-10-29 07:33:12','2025-10-29 07:33:12'),(18,'Confectionaries Sales Staff','employees','2025-10-29 07:33:12','2025-10-29 07:33:12'),(19,'Stock Controller','employees','2025-10-29 07:33:12','2025-10-29 07:33:12'),(20,'Store Keeper','employees','2025-10-29 07:33:13','2025-10-29 07:33:13'),(21,'HR Officer','employees','2025-10-29 07:33:13','2025-10-29 07:33:13'),(22,'MD','web','2025-10-29 07:33:14','2025-10-29 07:33:14');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_shift_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `sold_by` char(36) NOT NULL,
  `sale_number` varchar(255) NOT NULL,
  `sale_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed','cancelled','refunded','hold') NOT NULL DEFAULT 'completed',
  `order_type` enum('dine-in','takeaway','delivery','dine_in','glovo','transfer') NOT NULL DEFAULT 'dine_in',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_sale_number_unique` (`sale_number`),
  KEY `sales_sales_shift_id_foreign` (`sales_shift_id`),
  KEY `sales_department_id_foreign` (`department_id`),
  KEY `sales_sold_by_foreign` (`sold_by`),
  KEY `sales_branch_id_department_id_sale_time_index` (`branch_id`,`department_id`,`sale_time`),
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_sold_by_foreign` FOREIGN KEY (`sold_by`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_shifts`
--

DROP TABLE IF EXISTS `sales_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `employee_id` char(36) NOT NULL,
  `shift_number` varchar(255) NOT NULL,
  `shift_date` date NOT NULL,
  `shift_type` enum('morning','afternoon','night') NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `opening_cash` decimal(10,2) NOT NULL DEFAULT 0.00,
  `closing_cash` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expected_cash` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash_variance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','closed','submitted','verified') NOT NULL DEFAULT 'active',
  `verified_by` char(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_shifts_shift_number_unique` (`shift_number`),
  KEY `sales_shifts_branch_id_foreign` (`branch_id`),
  KEY `sales_shifts_department_id_foreign` (`department_id`),
  KEY `sales_shifts_employee_id_foreign` (`employee_id`),
  KEY `sales_shifts_verified_by_foreign` (`verified_by`),
  CONSTRAINT `sales_shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_shifts_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_shifts_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_shifts`
--

LOCK TABLES `sales_shifts` WRITE;
/*!40000 ALTER TABLE `sales_shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `employee_id` char(36) NOT NULL,
  `shift_number` varchar(255) NOT NULL,
  `shift_date` date NOT NULL,
  `shift_type` enum('morning','afternoon','night') NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `status` enum('active','closed','submitted') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shifts_shift_number_unique` (`shift_number`),
  KEY `shifts_department_id_foreign` (`department_id`),
  KEY `shifts_employee_id_foreign` (`employee_id`),
  KEY `shifts_branch_id_department_id_shift_date_index` (`branch_id`,`department_id`,`shift_date`),
  CONSTRAINT `shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shifts_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` bigint(20) unsigned NOT NULL,
  `type` enum('in','out','adjustment','transfer','damaged','return') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `quantity_before` decimal(12,2) NOT NULL,
  `quantity_after` decimal(12,2) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `moved_by` char(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `movement_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_stock_id_foreign` (`stock_id`),
  KEY `stock_movements_moved_by_foreign` (`moved_by`),
  CONSTRAINT `stock_movements_moved_by_foreign` FOREIGN KEY (`moved_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `stock_movements_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_take_details`
--

DROP TABLE IF EXISTS `stock_take_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_take_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_take_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `system_quantity` decimal(12,2) NOT NULL,
  `physical_quantity` decimal(12,2) NOT NULL,
  `variance` decimal(12,2) NOT NULL,
  `variance_type` enum('surplus','shortage','match') NOT NULL DEFAULT 'match',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_take_details_stock_take_id_foreign` (`stock_take_id`),
  KEY `stock_take_details_item_id_foreign` (`item_id`),
  CONSTRAINT `stock_take_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `stock_take_details_stock_take_id_foreign` FOREIGN KEY (`stock_take_id`) REFERENCES `stock_takes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_take_details`
--

LOCK TABLES `stock_take_details` WRITE;
/*!40000 ALTER TABLE `stock_take_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_take_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_takes`
--

DROP TABLE IF EXISTS `stock_takes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_takes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `stock_take_number` varchar(255) NOT NULL,
  `stock_take_date` date NOT NULL,
  `type` enum('daily','weekly','monthly','annual','ad_hoc') NOT NULL,
  `conducted_by` char(36) NOT NULL,
  `status` enum('in_progress','completed','verified') NOT NULL DEFAULT 'in_progress',
  `verified_by` char(36) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_takes_stock_take_number_unique` (`stock_take_number`),
  KEY `stock_takes_branch_id_foreign` (`branch_id`),
  KEY `stock_takes_conducted_by_foreign` (`conducted_by`),
  KEY `stock_takes_verified_by_foreign` (`verified_by`),
  CONSTRAINT `stock_takes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_takes_conducted_by_foreign` FOREIGN KEY (`conducted_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `stock_takes_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_takes`
--

LOCK TABLES `stock_takes` WRITE;
/*!40000 ALTER TABLE `stock_takes` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_takes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity_available` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity_reserved` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity_damaged` decimal(12,2) NOT NULL DEFAULT 0.00,
  `average_cost` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `last_stock_take_date` date DEFAULT NULL,
  `health_status` enum('good','warning','critical','expired') NOT NULL DEFAULT 'good',
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stocks_branch_id_item_id_unique` (`branch_id`,`item_id`),
  KEY `stocks_item_id_foreign` (`item_id`),
  CONSTRAINT `stocks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stocks_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
INSERT INTO `stocks` VALUES (1,'019a2f19-bde1-73ae-b1f7-81d61b668e39',1,92.00,5.00,3.00,242.9000,'2025-10-09','good','2026-05-01','2025-10-29 07:33:52','2025-10-29 07:33:52'),(2,'019a2f19-bde1-73ae-b1f7-81d61b668e39',2,317.00,19.00,2.00,235.7000,'2025-10-08','good','2026-08-10','2025-10-29 07:33:53','2025-10-29 07:33:53'),(3,'019a2f19-bde1-73ae-b1f7-81d61b668e39',3,80.00,7.00,0.00,226.4000,'2025-10-09','good','2026-03-31','2025-10-29 07:33:53','2025-10-29 07:33:53'),(4,'019a2f19-bde1-73ae-b1f7-81d61b668e39',4,369.00,9.00,12.00,318.7000,'2025-10-13','good','2026-04-19','2025-10-29 07:33:53','2025-10-29 07:33:53'),(5,'019a2f19-bde1-73ae-b1f7-81d61b668e39',5,241.00,19.00,3.00,489.1000,'2025-10-18','good','2026-04-15','2025-10-29 07:33:53','2025-10-29 07:33:53'),(6,'019a2f19-bde1-73ae-b1f7-81d61b668e39',6,251.00,14.00,5.00,381.1000,'2025-10-15','good','2026-05-11','2025-10-29 07:33:53','2025-10-29 07:33:53'),(7,'019a2f19-bde1-73ae-b1f7-81d61b668e39',7,157.00,3.00,3.00,469.3000,'2025-09-29','good','2026-04-09','2025-10-29 07:33:53','2025-10-29 07:33:53'),(8,'019a2f19-bde1-73ae-b1f7-81d61b668e39',8,135.00,1.00,3.00,493.5000,'2025-10-18','good','2026-06-28','2025-10-29 07:33:53','2025-10-29 07:33:53'),(9,'019a2f19-bde1-73ae-b1f7-81d61b668e39',9,304.00,22.00,7.00,259.0000,'2025-10-13','critical','2025-11-19','2025-10-29 07:33:53','2025-10-29 07:33:53'),(10,'019a2f19-bde1-73ae-b1f7-81d61b668e39',10,117.00,7.00,2.00,310.4000,'2025-10-11','critical','2025-11-05','2025-10-29 07:33:53','2025-10-29 07:33:53'),(11,'019a2f19-bde1-73ae-b1f7-81d61b668e39',11,298.00,28.00,13.00,333.9000,'2025-10-22','good','2026-03-31','2025-10-29 07:33:54','2025-10-29 07:33:54'),(12,'019a2f19-bde1-73ae-b1f7-81d61b668e39',12,394.00,6.00,3.00,456.6000,'2025-10-16','good','2026-05-27','2025-10-29 07:33:54','2025-10-29 07:33:54'),(13,'019a2f19-bde1-73ae-b1f7-81d61b668e39',13,660.00,31.00,5.00,28.9000,'2025-10-11','critical',NULL,'2025-10-29 07:33:54','2025-10-29 07:33:54'),(14,'019a2f19-bde1-73ae-b1f7-81d61b668e39',14,248.00,5.00,1.00,40.5000,'2025-10-25','good',NULL,'2025-10-29 07:33:54','2025-10-29 07:33:54'),(15,'019a2f19-bde1-73ae-b1f7-81d61b668e39',15,223.00,22.00,3.00,30.9000,'2025-10-25','critical',NULL,'2025-10-29 07:33:54','2025-10-29 07:33:54'),(16,'019a2f19-bde1-73ae-b1f7-81d61b668e39',16,410.00,15.00,4.00,31.5000,'2025-10-12','critical',NULL,'2025-10-29 07:33:54','2025-10-29 07:33:54'),(17,'019a2f19-bde1-73ae-b1f7-81d61b668e39',17,139.00,13.00,5.00,225.7000,'2025-10-13','critical','2025-11-08','2025-10-29 07:33:54','2025-10-29 07:33:54'),(18,'019a2f19-bde1-73ae-b1f7-81d61b668e39',18,127.00,7.00,2.00,273.7000,'2025-10-06','critical','2025-11-24','2025-10-29 07:33:54','2025-10-29 07:33:54'),(19,'019a2f19-bde1-73ae-b1f7-81d61b668e39',19,140.00,1.00,5.00,285.1000,'2025-10-11','critical','2025-11-27','2025-10-29 07:33:54','2025-10-29 07:33:54'),(20,'019a2f19-bde1-73ae-b1f7-81d61b668e39',20,13.00,0.00,0.00,801.7000,'2025-10-01','warning',NULL,'2025-10-29 07:33:55','2025-10-29 07:33:55'),(21,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',21,259.00,19.00,2.00,275.9000,'2025-10-17','good','2026-09-18','2025-10-29 07:33:59','2025-10-29 07:33:59'),(22,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',22,170.00,9.00,8.00,327.4000,'2025-10-23','warning','2026-01-25','2025-10-29 07:33:59','2025-10-29 07:33:59'),(23,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',23,399.00,38.00,0.00,215.6000,'2025-10-18','warning','2026-01-12','2025-10-29 07:33:59','2025-10-29 07:33:59'),(24,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',24,309.00,18.00,14.00,437.6000,'2025-10-28','warning','2026-01-02','2025-10-29 07:33:59','2025-10-29 07:33:59'),(25,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',25,239.00,18.00,1.00,379.6000,'2025-10-03','critical','2025-11-09','2025-10-29 07:33:59','2025-10-29 07:33:59'),(26,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',26,272.00,6.00,9.00,266.1000,'2025-10-05','critical','2025-11-11','2025-10-29 07:33:59','2025-10-29 07:33:59'),(27,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',27,281.00,10.00,9.00,416.1000,'2025-10-23','warning','2026-01-25','2025-10-29 07:33:59','2025-10-29 07:33:59'),(28,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',28,378.00,36.00,9.00,265.8000,'2025-10-21','critical','2025-11-20','2025-10-29 07:34:00','2025-10-29 07:34:00'),(29,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',29,229.00,6.00,2.00,71.4000,'2025-10-10','critical','2025-11-27','2025-10-29 07:34:00','2025-10-29 07:34:00'),(30,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',30,138.00,6.00,0.00,488.7000,'2025-10-25','critical','2025-11-14','2025-10-29 07:34:00','2025-10-29 07:34:00'),(31,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',31,209.00,7.00,1.00,265.9000,'2025-10-27','good','2026-09-06','2025-10-29 07:34:00','2025-10-29 07:34:00'),(32,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',32,301.00,19.00,9.00,423.1000,'2025-10-26','critical','2025-11-08','2025-10-29 07:34:00','2025-10-29 07:34:00'),(33,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',33,604.00,43.00,15.00,31.2000,'2025-10-24','good',NULL,'2025-10-29 07:34:00','2025-10-29 07:34:00'),(34,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',34,564.00,39.00,14.00,30.2000,'2025-10-15','good',NULL,'2025-10-29 07:34:01','2025-10-29 07:34:01'),(35,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',35,662.00,63.00,23.00,30.0000,'2025-10-02','good',NULL,'2025-10-29 07:34:01','2025-10-29 07:34:01'),(36,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',36,610.00,4.00,10.00,39.9000,'2025-10-14','good',NULL,'2025-10-29 07:34:01','2025-10-29 07:34:01'),(37,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',37,42.00,4.00,1.00,70.6000,'2025-10-05','critical','2025-11-18','2025-10-29 07:34:01','2025-10-29 07:34:01'),(38,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',38,32.00,1.00,0.00,129.3000,'2025-10-19','good','2026-05-10','2025-10-29 07:34:01','2025-10-29 07:34:01'),(39,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',39,74.00,5.00,2.00,38.9000,'2025-10-11','critical','2025-11-11','2025-10-29 07:34:02','2025-10-29 07:34:02'),(40,'019a2f19-be9d-70d3-80ee-fb41b8a27a7c',40,5.00,0.00,0.00,660.4000,'2025-10-02','critical',NULL,'2025-10-29 07:34:02','2025-10-29 07:34:02'),(41,'019a2f19-bf8a-713c-b295-f1c53d78fb12',41,365.00,21.00,6.00,482.9000,'2025-10-26','good','2026-07-21','2025-10-29 07:34:05','2025-10-29 07:34:05'),(42,'019a2f19-bf8a-713c-b295-f1c53d78fb12',42,368.00,9.00,14.00,268.5000,'2025-10-05','critical','2025-11-22','2025-10-29 07:34:05','2025-10-29 07:34:05'),(43,'019a2f19-bf8a-713c-b295-f1c53d78fb12',43,104.00,1.00,1.00,456.8000,'2025-10-08','good','2026-08-03','2025-10-29 07:34:05','2025-10-29 07:34:05'),(44,'019a2f19-bf8a-713c-b295-f1c53d78fb12',44,102.00,7.00,2.00,453.2000,'2025-10-22','good','2026-09-01','2025-10-29 07:34:05','2025-10-29 07:34:05'),(45,'019a2f19-bf8a-713c-b295-f1c53d78fb12',45,301.00,26.00,15.00,433.6000,'2025-10-22','good','2026-05-30','2025-10-29 07:34:06','2025-10-29 07:34:06'),(46,'019a2f19-bf8a-713c-b295-f1c53d78fb12',46,136.00,9.00,2.00,375.2000,'2025-10-21','critical','2025-11-16','2025-10-29 07:34:06','2025-10-29 07:34:06'),(47,'019a2f19-bf8a-713c-b295-f1c53d78fb12',47,356.00,5.00,8.00,304.1000,'2025-10-28','warning','2025-12-25','2025-10-29 07:34:06','2025-10-29 07:34:06'),(48,'019a2f19-bf8a-713c-b295-f1c53d78fb12',48,181.00,6.00,3.00,58.7000,'2025-10-25','good','2026-04-16','2025-10-29 07:34:06','2025-10-29 07:34:06'),(49,'019a2f19-bf8a-713c-b295-f1c53d78fb12',49,343.00,32.00,1.00,394.3000,'2025-10-19','good','2026-04-29','2025-10-29 07:34:06','2025-10-29 07:34:06'),(50,'019a2f19-bf8a-713c-b295-f1c53d78fb12',50,100.00,9.00,3.00,229.6000,'2025-10-15','good','2026-07-21','2025-10-29 07:34:06','2025-10-29 07:34:06'),(51,'019a2f19-bf8a-713c-b295-f1c53d78fb12',51,313.00,1.00,14.00,202.3000,'2025-10-25','good','2026-08-07','2025-10-29 07:34:06','2025-10-29 07:34:06'),(52,'019a2f19-bf8a-713c-b295-f1c53d78fb12',52,86.00,1.00,0.00,464.0000,'2025-10-04','critical','2025-11-16','2025-10-29 07:34:06','2025-10-29 07:34:06'),(53,'019a2f19-bf8a-713c-b295-f1c53d78fb12',53,284.00,15.00,11.00,22.7000,'2025-10-01','good',NULL,'2025-10-29 07:34:07','2025-10-29 07:34:07'),(54,'019a2f19-bf8a-713c-b295-f1c53d78fb12',54,342.00,3.00,16.00,10.5000,'2025-10-05','good',NULL,'2025-10-29 07:34:07','2025-10-29 07:34:07'),(55,'019a2f19-bf8a-713c-b295-f1c53d78fb12',55,472.00,18.00,14.00,30.9000,'2025-10-07','good',NULL,'2025-10-29 07:34:07','2025-10-29 07:34:07'),(56,'019a2f19-bf8a-713c-b295-f1c53d78fb12',56,406.00,12.00,15.00,27.2000,'2025-10-20','warning',NULL,'2025-10-29 07:34:07','2025-10-29 07:34:07'),(57,'019a2f19-bf8a-713c-b295-f1c53d78fb12',57,132.00,0.00,6.00,256.1000,'2025-10-26','good','2026-08-23','2025-10-29 07:34:07','2025-10-29 07:34:07'),(58,'019a2f19-bf8a-713c-b295-f1c53d78fb12',58,29.00,0.00,0.00,235.3000,'2025-10-11','good','2026-07-25','2025-10-29 07:34:07','2025-10-29 07:34:07'),(59,'019a2f19-bf8a-713c-b295-f1c53d78fb12',59,24.00,2.00,1.00,135.9000,'2025-10-17','critical','2025-11-08','2025-10-29 07:34:08','2025-10-29 07:34:08'),(60,'019a2f19-bf8a-713c-b295-f1c53d78fb12',60,15.00,0.00,0.00,1890.2000,'2025-10-08','good',NULL,'2025-10-29 07:34:08','2025-10-29 07:34:08'),(61,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',61,179.00,5.00,1.00,128.9000,'2025-10-08','good','2026-08-12','2025-10-29 07:34:10','2025-10-29 07:34:10'),(62,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',62,155.00,4.00,6.00,442.6000,'2025-10-13','good','2026-10-15','2025-10-29 07:34:10','2025-10-29 07:34:10'),(63,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',63,155.00,8.00,6.00,170.7000,'2025-10-20','warning','2026-01-24','2025-10-29 07:34:10','2025-10-29 07:34:10'),(64,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',64,384.00,16.00,18.00,456.8000,'2025-10-04','good','2026-10-28','2025-10-29 07:34:10','2025-10-29 07:34:10'),(65,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',65,52.00,2.00,0.00,311.3000,'2025-10-04','good','2026-03-02','2025-10-29 07:34:10','2025-10-29 07:34:10'),(66,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',66,60.00,4.00,3.00,288.4000,'2025-10-12','warning','2025-12-16','2025-10-29 07:34:10','2025-10-29 07:34:10'),(67,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',67,339.00,32.00,5.00,456.8000,'2025-10-14','good','2026-01-29','2025-10-29 07:34:10','2025-10-29 07:34:10'),(68,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',68,53.00,0.00,2.00,324.9000,'2025-10-18','warning','2025-12-08','2025-10-29 07:34:10','2025-10-29 07:34:10'),(69,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',69,77.00,1.00,2.00,312.7000,'2025-10-09','good','2026-10-20','2025-10-29 07:34:10','2025-10-29 07:34:10'),(70,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',70,111.00,4.00,3.00,110.3000,'2025-10-22','good','2026-06-26','2025-10-29 07:34:10','2025-10-29 07:34:10'),(71,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',71,357.00,30.00,5.00,401.9000,'2025-10-17','critical','2025-11-11','2025-10-29 07:34:11','2025-10-29 07:34:11'),(72,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',72,58.00,0.00,1.00,279.5000,'2025-10-02','good','2026-06-27','2025-10-29 07:34:11','2025-10-29 07:34:11'),(73,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',73,306.00,22.00,11.00,29.0000,'2025-10-24','warning',NULL,'2025-10-29 07:34:11','2025-10-29 07:34:11'),(74,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',74,541.00,15.00,6.00,13.8000,'2025-10-22','good',NULL,'2025-10-29 07:34:11','2025-10-29 07:34:11'),(75,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',75,730.00,25.00,15.00,40.6000,'2025-10-01','critical',NULL,'2025-10-29 07:34:11','2025-10-29 07:34:11'),(76,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',76,179.00,4.00,7.00,38.9000,'2025-10-28','good',NULL,'2025-10-29 07:34:11','2025-10-29 07:34:11'),(77,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',77,137.00,11.00,0.00,88.9000,'2025-10-24','critical','2025-11-19','2025-10-29 07:34:11','2025-10-29 07:34:11'),(78,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',78,80.00,2.00,4.00,166.1000,'2025-10-18','good','2026-04-30','2025-10-29 07:34:11','2025-10-29 07:34:11'),(79,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',79,98.00,8.00,1.00,35.0000,'2025-10-21','good','2026-04-24','2025-10-29 07:34:11','2025-10-29 07:34:11'),(80,'019a2f19-c0c9-71a0-9cb2-881fed1d26ca',80,24.00,0.00,1.00,307.2000,'2025-10-11','warning',NULL,'2025-10-29 07:34:11','2025-10-29 07:34:11'),(81,'019a2f19-c0f6-7167-8a54-a58bb08550b7',81,102.00,3.00,1.00,197.4000,'2025-10-18','warning','2026-01-13','2025-10-29 07:34:13','2025-10-29 07:34:13'),(82,'019a2f19-c0f6-7167-8a54-a58bb08550b7',82,213.00,16.00,0.00,196.0000,'2025-10-28','warning','2025-12-28','2025-10-29 07:34:13','2025-10-29 07:34:13'),(83,'019a2f19-c0f6-7167-8a54-a58bb08550b7',83,61.00,0.00,1.00,375.9000,'2025-10-05','good','2026-08-16','2025-10-29 07:34:13','2025-10-29 07:34:13'),(84,'019a2f19-c0f6-7167-8a54-a58bb08550b7',84,177.00,7.00,6.00,281.5000,'2025-10-21','warning','2026-01-14','2025-10-29 07:34:13','2025-10-29 07:34:13'),(85,'019a2f19-c0f6-7167-8a54-a58bb08550b7',85,395.00,10.00,15.00,75.9000,'2025-10-15','good','2026-10-07','2025-10-29 07:34:13','2025-10-29 07:34:13'),(86,'019a2f19-c0f6-7167-8a54-a58bb08550b7',86,327.00,17.00,13.00,85.6000,'2025-10-09','good','2026-09-09','2025-10-29 07:34:13','2025-10-29 07:34:13'),(87,'019a2f19-c0f6-7167-8a54-a58bb08550b7',87,117.00,8.00,5.00,364.9000,'2025-10-07','critical','2025-11-24','2025-10-29 07:34:14','2025-10-29 07:34:14'),(88,'019a2f19-c0f6-7167-8a54-a58bb08550b7',88,63.00,5.00,3.00,196.8000,'2025-10-21','warning','2025-12-18','2025-10-29 07:34:14','2025-10-29 07:34:14'),(89,'019a2f19-c0f6-7167-8a54-a58bb08550b7',89,308.00,30.00,12.00,190.6000,'2025-10-05','good','2026-08-11','2025-10-29 07:34:14','2025-10-29 07:34:14'),(90,'019a2f19-c0f6-7167-8a54-a58bb08550b7',90,393.00,34.00,5.00,194.1000,'2025-10-11','good','2026-08-10','2025-10-29 07:34:14','2025-10-29 07:34:14'),(91,'019a2f19-c0f6-7167-8a54-a58bb08550b7',91,198.00,13.00,5.00,201.6000,'2025-10-28','warning','2026-01-13','2025-10-29 07:34:14','2025-10-29 07:34:14'),(92,'019a2f19-c0f6-7167-8a54-a58bb08550b7',92,63.00,6.00,1.00,73.0000,'2025-10-08','good','2026-01-31','2025-10-29 07:34:14','2025-10-29 07:34:14'),(93,'019a2f19-c0f6-7167-8a54-a58bb08550b7',93,175.00,7.00,1.00,41.3000,'2025-10-19','critical',NULL,'2025-10-29 07:34:14','2025-10-29 07:34:14'),(94,'019a2f19-c0f6-7167-8a54-a58bb08550b7',94,675.00,45.00,15.00,9.8000,'2025-10-01','warning',NULL,'2025-10-29 07:34:14','2025-10-29 07:34:14'),(95,'019a2f19-c0f6-7167-8a54-a58bb08550b7',95,100.00,10.00,5.00,29.5000,'2025-10-15','critical',NULL,'2025-10-29 07:34:14','2025-10-29 07:34:14'),(96,'019a2f19-c0f6-7167-8a54-a58bb08550b7',96,492.00,14.00,16.00,6.1000,'2025-10-04','good',NULL,'2025-10-29 07:34:14','2025-10-29 07:34:14'),(97,'019a2f19-c0f6-7167-8a54-a58bb08550b7',97,20.00,1.00,0.00,259.1000,'2025-10-19','good','2026-04-08','2025-10-29 07:34:14','2025-10-29 07:34:14'),(98,'019a2f19-c0f6-7167-8a54-a58bb08550b7',98,116.00,7.00,3.00,219.7000,'2025-10-23','warning','2025-12-27','2025-10-29 07:34:14','2025-10-29 07:34:14'),(99,'019a2f19-c0f6-7167-8a54-a58bb08550b7',99,122.00,3.00,5.00,176.8000,'2025-10-21','critical','2025-11-09','2025-10-29 07:34:14','2025-10-29 07:34:14'),(100,'019a2f19-c0f6-7167-8a54-a58bb08550b7',100,18.00,1.00,0.00,776.7000,'2025-10-05','critical',NULL,'2025-10-29 07:34:14','2025-10-29 07:34:14');
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('019a2f19-bbba-7078-807a-1b34476665c7','Managing Director','md@foodcompany.com','$2y$12$.8moyYYOryl8GKn1h4QN..n3ZjLyV5rkAvJ3qpra4VgPMOI5GdYNa',NULL,NULL,NULL,NULL,NULL,'2025-10-29 07:33:26','2025-10-29 07:33:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-29  9:36:33
