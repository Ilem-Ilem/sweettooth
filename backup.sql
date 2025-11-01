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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
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
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_09_22_145432_add_two_factor_columns_to_users_table',1),(5,'2025_10_02_163836_create_branches_table',1),(6,'2025_10_02_220032_create_department_categories_table',1),(7,'2025_10_04_053818_create_departments_table',1),(8,'2025_10_04_053818_create_employees_table',1),(9,'2025_10_05_192144_add_password_to_employees_table',1),(10,'2025_10_06_051232_add_auth_columns_to_employees_table',1),(11,'2025_10_07_030703_create_permission_tables',1),(12,'2025_10_08_223243_add_position_and_manager_to_employees_table',1),(13,'2025_10_09_012549_create_items_table',1),(14,'2025_10_09_012613_create_purchases_table',1),(15,'2025_10_09_012614_create_purchase_items_table',1),(16,'2025_10_09_012615_create_stocks_table',1),(17,'2025_10_09_012617_create_stock_movements_table',1),(18,'2025_10_09_012619_create_item_requests_table',1),(19,'2025_10_09_012621_create_item_request_details_table',1),(20,'2025_10_09_012622_create_item_dispatches_table',1),(21,'2025_10_09_012623_create_stock_takes_table',1),(22,'2025_10_09_012626_create_stock_take_details_table',1),(23,'2025_10_09_012627_create_health_checks_table',1),(24,'2025_10_13_050139_create_clock_ins_table',1),(25,'2025_10_15_053301_create_recipes_table',1),(26,'2025_10_15_053356_create_recipe_ingredients_table',1),(27,'2025_10_15_053520_create_shifts_table',1),(28,'2025_10_15_053537_create_daily_produces_table',1),(29,'2025_10_15_054442_create_production_records_table',1),(30,'2025_10_15_055019_create_production_requests_table',1),(31,'2025_10_15_055201_create_call_backs_table',1),(32,'2025_10_15_055221_create_raw_material_utilizations_table',1),(33,'2025_10_16_074716_create_product_types_table',1),(34,'2025_10_16_074717_create_products_table',1),(35,'2025_10_18_075757_add_branch_id_to_products_table',1),(36,'2025_10_18_194413_add_cost_and_waste_fields_to_recipe_ingredients_table',1),(37,'2025_10_19_030711_add_status_to_daily_produces_table',1),(38,'2025_10_19_083910_create_employee_shifts_table',1),(39,'2025_10_20_063309_create_sales_hifts_table',1),(40,'2025_10_20_063310_create_product_stocks_table',1),(41,'2025_10_20_063352_create_sales_table',1),(42,'2025_10_20_063409_create_sale_items_table',1),(43,'2025_10_20_063429_create_payments_table',1),(44,'2025_10_21_073703_create_product_callbacks_table',1),(45,'2025_10_22_094919_create_approved_items_table',1),(46,'2025_10_23_100000_create_expiry_confirmations_table',1),(47,'2025_10_23_120000_create_product_dispatches_table',1),(48,'2025_10_24_200648_add_batch_tracking_to_production_records_table',1),(49,'2025_10_25_045338_make_sales_shift_id_nullable_in_product_stocks_table',1),(50,'2025_10_25_073857_create_receipts_table',1),(51,'2025_10_27_000814_create_global_business_configurations_table',1),(52,'2025_10_27_000816_create_global_currency_localizations_table',1),(53,'2025_10_27_000818_create_global_branch_management_table',1),(54,'2025_10_27_000820_create_global_inventory_management_table',1),(55,'2025_10_27_000821_create_branch_inventory_management_table',1),(56,'2025_10_27_000823_create_global_employee_management_table',1),(57,'2025_10_27_000824_create_global_pos_configurations_table',1),(58,'2025_10_27_000827_create_global_accounting_cashes_table',1),(59,'2025_10_27_000829_create_global_customer_supplier_management_table',1),(60,'2025_10_27_000831_create_global_reports_analytics_table',1),(61,'2025_10_27_000833_create_global_security_accesses_table',1),(62,'2025_10_27_000835_create_global_notifications_alerts_table',1),(63,'2025_10_27_000837_create_audit_logs_table',2),(64,'2025_10_27_000838_create_approval_requests_table',2),(65,'2025_10_27_042254_remove_business_type_from_global_business_configurations',2),(66,'2025_10_27_042747_remove_columns_from_global_business_configurations',2);
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
  `model_id` bigint(20) unsigned NOT NULL,
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
  `model_id` bigint(20) unsigned NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_types`
--

LOCK TABLES `product_types` WRITE;
/*!40000 ALTER TABLE `product_types` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_ingredients`
--

LOCK TABLES `recipe_ingredients` WRITE;
/*!40000 ALTER TABLE `recipe_ingredients` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
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

-- Dump completed on 2025-10-28 19:42:44
