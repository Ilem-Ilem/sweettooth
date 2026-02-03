/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.3-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: sweettooth
-- ------------------------------------------------------
-- Server version	11.8.3-MariaDB-0+deb13u1 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `account_transfers`
--

DROP TABLE IF EXISTS `account_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_bank_account_id` bigint(20) unsigned NOT NULL,
  `to_bank_account_id` bigint(20) unsigned NOT NULL,
  `transfer_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_transfers_from_bank_account_id_foreign` (`from_bank_account_id`),
  KEY `account_transfers_to_bank_account_id_foreign` (`to_bank_account_id`),
  KEY `account_transfers_branch_id_foreign` (`branch_id`),
  KEY `account_transfers_created_by_foreign` (`created_by`),
  KEY `account_transfers_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `account_transfers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `account_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `account_transfers_from_bank_account_id_foreign` FOREIGN KEY (`from_bank_account_id`) REFERENCES `bank_accounts` (`id`),
  CONSTRAINT `account_transfers_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `account_transfers_to_bank_account_id_foreign` FOREIGN KEY (`to_bank_account_id`) REFERENCES `bank_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_transfers`
--

LOCK TABLES `account_transfers` WRITE;
/*!40000 ALTER TABLE `account_transfers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `account_transfers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `accounting_periods`
--

DROP TABLE IF EXISTS `accounting_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounting_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `year` year(4) NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` enum('open','closed','locked') NOT NULL DEFAULT 'open',
  `closed_by_id` char(36) DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closing_notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accounting_periods_year_month_unique` (`year`,`month`),
  KEY `accounting_periods_status_period_start_index` (`status`,`period_start`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_periods`
--

LOCK TABLES `accounting_periods` WRITE;
/*!40000 ALTER TABLE `accounting_periods` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `accounting_periods` VALUES
(1,2025,1,'2025-01-01','2025-01-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(2,2025,2,'2025-02-01','2025-02-28','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(3,2025,3,'2025-03-01','2025-03-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(4,2025,4,'2025-04-01','2025-04-30','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(5,2025,5,'2025-05-01','2025-05-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(6,2025,6,'2025-06-01','2025-06-30','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(7,2025,7,'2025-07-01','2025-07-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(8,2025,8,'2025-08-01','2025-08-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(9,2025,9,'2025-09-01','2025-09-30','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(10,2025,10,'2025-10-01','2025-10-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(11,2025,11,'2025-11-01','2025-11-30','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(12,2025,12,'2025-12-01','2025-12-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(13,2026,1,'2026-01-01','2026-01-31','locked',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(14,2026,2,'2026-02-01','2026-02-28','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(15,2026,3,'2026-03-01','2026-03-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(16,2026,4,'2026-04-01','2026-04-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(17,2026,5,'2026-05-01','2026-05-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(18,2026,6,'2026-06-01','2026-06-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(19,2026,7,'2026-07-01','2026-07-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(20,2026,8,'2026-08-01','2026-08-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(21,2026,9,'2026-09-01','2026-09-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(22,2026,10,'2026-10-01','2026-10-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(23,2026,11,'2026-11-01','2026-11-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(24,2026,12,'2026-12-01','2026-12-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(25,2027,1,'2027-01-01','2027-01-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(26,2027,2,'2027-02-01','2027-02-28','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(27,2027,3,'2027-03-01','2027-03-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(28,2027,4,'2027-04-01','2027-04-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(29,2027,5,'2027-05-01','2027-05-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(30,2027,6,'2027-06-01','2027-06-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(31,2027,7,'2027-07-01','2027-07-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(32,2027,8,'2027-08-01','2027-08-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(33,2027,9,'2027-09-01','2027-09-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(34,2027,10,'2027-10-01','2027-10-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(35,2027,11,'2027-11-01','2027-11-30','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(36,2027,12,'2027-12-01','2027-12-31','open',NULL,NULL,NULL,NULL,'2026-02-02 12:46:57','2026-02-02 12:46:57');
/*!40000 ALTER TABLE `accounting_periods` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `appraisal_cycles`
--

DROP TABLE IF EXISTS `appraisal_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `appraisal_cycles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('planned','active','completed') NOT NULL DEFAULT 'planned',
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appraisal_cycles_department_id_foreign` (`department_id`),
  KEY `appraisal_cycles_status_department_id_index` (`status`,`department_id`),
  KEY `appraisal_cycles_start_date_end_date_index` (`start_date`,`end_date`),
  CONSTRAINT `appraisal_cycles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appraisal_cycles`
--

LOCK TABLES `appraisal_cycles` WRITE;
/*!40000 ALTER TABLE `appraisal_cycles` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `appraisal_cycles` VALUES
(1,'first half of the year','2026-02-01','2026-02-28','2026-02-25','planned',NULL,'2026-02-02 19:36:12','2026-02-02 19:36:12');
/*!40000 ALTER TABLE `appraisal_cycles` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `appraisals`
--

DROP TABLE IF EXISTS `appraisals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `appraisals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `manager_id` char(36) NOT NULL,
  `appraisal_cycle_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('draft','self_review','manager_review','hr_review','completed') NOT NULL DEFAULT 'draft',
  `self_assessment_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`self_assessment_data`)),
  `manager_assessment_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`manager_assessment_data`)),
  `hr_assessment_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hr_assessment_data`)),
  `final_rating` decimal(4,2) DEFAULT NULL,
  `final_comments` text DEFAULT NULL,
  `development_plan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`development_plan`)),
  `submitted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appraisals_appraisal_cycle_id_foreign` (`appraisal_cycle_id`),
  KEY `appraisals_employee_id_appraisal_cycle_id_index` (`employee_id`,`appraisal_cycle_id`),
  KEY `appraisals_manager_id_status_index` (`manager_id`,`status`),
  KEY `appraisals_status_appraisal_cycle_id_index` (`status`,`appraisal_cycle_id`),
  CONSTRAINT `appraisals_appraisal_cycle_id_foreign` FOREIGN KEY (`appraisal_cycle_id`) REFERENCES `appraisal_cycles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appraisals_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appraisals_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appraisals`
--

LOCK TABLES `appraisals` WRITE;
/*!40000 ALTER TABLE `appraisals` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `appraisals` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `approval_audit_requests`
--

DROP TABLE IF EXISTS `approval_audit_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval_audit_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `requester_id` char(36) DEFAULT NULL,
  `requester_type` varchar(255) DEFAULT NULL,
  `approver_id` char(36) DEFAULT NULL,
  `approver_type` varchar(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `comment` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `denied_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_audit_requests`
--

LOCK TABLES `approval_audit_requests` WRITE;
/*!40000 ALTER TABLE `approval_audit_requests` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `approval_audit_requests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `approval_requests`
--

DROP TABLE IF EXISTS `approval_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requested_by_id` char(36) NOT NULL,
  `requested_by_type` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `auditable_id` char(36) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reason` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `approved_at` datetime DEFAULT NULL,
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `executed_at` datetime DEFAULT NULL,
  `executed_by_id` char(36) DEFAULT NULL,
  `executed_by_type` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_requests_status_index` (`status`),
  KEY `approval_requests_action_index` (`action`),
  KEY `approval_requests_requested_by_id_index` (`requested_by_id`),
  KEY `approval_requests_auditable_id_index` (`auditable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_requests`
--

LOCK TABLES `approval_requests` WRITE;
/*!40000 ALTER TABLE `approval_requests` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `approval_requests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `approved_items`
--

DROP TABLE IF EXISTS `approved_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `approved_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `approved_by_id` char(36) NOT NULL,
  `approved_by_type` varchar(255) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units','bags','cartons') NOT NULL,
  `approved_time` timestamp NOT NULL,
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
set autocommit=0;
/*!40000 ALTER TABLE `approved_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` char(36) DEFAULT NULL,
  `auditable_type` varchar(255) DEFAULT NULL,
  `auditable_id` char(36) DEFAULT NULL,
  `setting_type` varchar(50) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'completed',
  `approval_request_id` char(36) DEFAULT NULL,
  `logged_at` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
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
set autocommit=0;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) NOT NULL,
  `bank_code` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) NOT NULL,
  `account_type` enum('checking','savings','money_market') NOT NULL DEFAULT 'checking',
  `gl_account_id` bigint(20) unsigned DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `interest_rate` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bank_accounts_account_number_unique` (`account_number`),
  KEY `bank_accounts_gl_account_id_foreign` (`gl_account_id`),
  KEY `bank_accounts_is_active_bank_name_index` (`is_active`,`bank_name`),
  CONSTRAINT `bank_accounts_gl_account_id_foreign` FOREIGN KEY (`gl_account_id`) REFERENCES `gl_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_accounts`
--

LOCK TABLES `bank_accounts` WRITE;
/*!40000 ALTER TABLE `bank_accounts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `bank_accounts` VALUES
(1,'Access Bank','044','0012345678','checking',63,500000.00,0.0000,1,NULL,'2026-02-02 12:47:01','2026-02-02 12:47:01'),
(2,'GTBank','057','0087654321','savings',63,300000.00,0.0000,1,NULL,'2026-02-02 12:47:01','2026-02-02 12:47:01'),
(3,'First Bank','011','0054321678','checking',63,750000.00,0.0000,1,NULL,'2026-02-02 12:47:01','2026-02-02 12:47:01');
/*!40000 ALTER TABLE `bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `bank_reconciliation_details`
--

DROP TABLE IF EXISTS `bank_reconciliation_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_reconciliation_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_reconciliation_id` bigint(20) unsigned NOT NULL,
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `daily_bank_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `matched_amount` decimal(15,2) NOT NULL,
  `matched_at` timestamp NOT NULL,
  `matched_by_id` char(36) DEFAULT NULL,
  `match_type` enum('auto','manual') NOT NULL DEFAULT 'manual',
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_reconciliation_details_bank_reconciliation_id_index` (`bank_reconciliation_id`),
  KEY `bank_reconciliation_details_gl_entry_id_index` (`gl_entry_id`),
  KEY `bank_reconciliation_details_daily_bank_transaction_id_index` (`daily_bank_transaction_id`),
  CONSTRAINT `bank_reconciliation_details_bank_reconciliation_id_foreign` FOREIGN KEY (`bank_reconciliation_id`) REFERENCES `bank_reconciliations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_reconciliation_details_daily_bank_transaction_id_foreign` FOREIGN KEY (`daily_bank_transaction_id`) REFERENCES `daily_bank_transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_reconciliation_details_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_reconciliation_details`
--

LOCK TABLES `bank_reconciliation_details` WRITE;
/*!40000 ALTER TABLE `bank_reconciliation_details` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `bank_reconciliation_details` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `bank_reconciliation_unmatched`
--

DROP TABLE IF EXISTS `bank_reconciliation_unmatched`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_reconciliation_unmatched` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_reconciliation_id` bigint(20) unsigned NOT NULL,
  `item_type` enum('gl_entry','bank_transaction') NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `resolution_status` enum('pending','resolved','ignored') NOT NULL DEFAULT 'pending',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_reconciliation_unmatched_bank_reconciliation_id_index` (`bank_reconciliation_id`),
  KEY `bank_reconciliation_unmatched_item_type_index` (`item_type`),
  KEY `bank_reconciliation_unmatched_resolution_status_index` (`resolution_status`),
  CONSTRAINT `bank_reconciliation_unmatched_bank_reconciliation_id_foreign` FOREIGN KEY (`bank_reconciliation_id`) REFERENCES `bank_reconciliations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_reconciliation_unmatched`
--

LOCK TABLES `bank_reconciliation_unmatched` WRITE;
/*!40000 ALTER TABLE `bank_reconciliation_unmatched` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `bank_reconciliation_unmatched` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `bank_reconciliations`
--

DROP TABLE IF EXISTS `bank_reconciliations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_reconciliations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `bank_account_id` bigint(20) unsigned NOT NULL,
  `reconciliation_date` date NOT NULL,
  `bank_balance` decimal(15,2) NOT NULL,
  `book_balance` decimal(15,2) NOT NULL,
  `difference` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','in_progress','completed','verified') NOT NULL DEFAULT 'draft',
  `completed_at` timestamp NULL DEFAULT NULL,
  `completed_by_id` char(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_reconciliations_branch_id_foreign` (`branch_id`),
  KEY `bank_reconciliations_bank_account_id_reconciliation_date_index` (`bank_account_id`,`reconciliation_date`),
  KEY `bank_reconciliations_status_index` (`status`),
  CONSTRAINT `bank_reconciliations_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_reconciliations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_reconciliations`
--

LOCK TABLES `bank_reconciliations` WRITE;
/*!40000 ALTER TABLE `bank_reconciliations` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `bank_reconciliations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `billable_time_entries`
--

DROP TABLE IF EXISTS `billable_time_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `billable_time_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `entry_date` date NOT NULL,
  `hours` decimal(8,2) NOT NULL,
  `hourly_rate` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `billable` tinyint(1) NOT NULL DEFAULT 1,
  `billed` tinyint(1) NOT NULL DEFAULT 0,
  `sale_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `billable_time_entries_employee_id_foreign` (`employee_id`),
  KEY `billable_time_entries_branch_id_foreign` (`branch_id`),
  KEY `billable_time_entries_sale_id_foreign` (`sale_id`),
  CONSTRAINT `billable_time_entries_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `billable_time_entries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  CONSTRAINT `billable_time_entries_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billable_time_entries`
--

LOCK TABLES `billable_time_entries` WRITE;
/*!40000 ALTER TABLE `billable_time_entries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `billable_time_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_accounting_cashes`
--

DROP TABLE IF EXISTS `branch_accounting_cashes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_accounting_cashes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_business_configurations`
--

DROP TABLE IF EXISTS `branch_business_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_business_configurations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_currency_localizations`
--

DROP TABLE IF EXISTS `branch_currency_localizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_currency_localizations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_customer_supplier_managements`
--

DROP TABLE IF EXISTS `branch_customer_supplier_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_customer_supplier_managements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_employee_managements`
--

DROP TABLE IF EXISTS `branch_employee_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_employee_managements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_inventory_managements`
--

DROP TABLE IF EXISTS `branch_inventory_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_inventory_managements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_managements`
--

DROP TABLE IF EXISTS `branch_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_managements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_notifications_alerts`
--

DROP TABLE IF EXISTS `branch_notifications_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_notifications_alerts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_pos_configurations`
--

DROP TABLE IF EXISTS `branch_pos_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_pos_configurations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_reports_analytics`
--

DROP TABLE IF EXISTS `branch_reports_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_reports_analytics` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branch_security_accesses`
--

DROP TABLE IF EXISTS `branch_security_accesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `branch_security_accesses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `enable_table_management` tinyint(1) NOT NULL DEFAULT 0,
  `table_management_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_management_settings`)),
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
set autocommit=0;
INSERT INTO `branches` VALUES
('019c1e63-b2e7-70f2-81ab-c6347efd07f0','SweetTooth Calabar','CAL-001','12 Marian Road, Calabar Municipal','+234-809-012-3456','calabar@sweettooth.com','Calabar flagship store with full production and sales',NULL,'Nigeria','Cross River','Calabar','540001','Africa/Lagos',1,0,NULL,'2026-02-02 12:46:16','2026-02-02 12:46:16',NULL),
('019c1e63-b2f6-729f-bd59-bbe4340497fe','SweetTooth Port Harcourt','PHC-002','78 Trans Amadi Industrial Layout','+234-803-456-7890','portharcourt@sweettooth.com','Port Harcourt main branch',NULL,'Nigeria','Rivers','Port Harcourt','500001','Africa/Lagos',1,0,NULL,'2026-02-02 12:46:16','2026-02-02 12:46:16',NULL),
('019c1e63-b300-7276-abd5-9a31664cea89','SweetTooth Lagos','LAG-003','45 Admiralty Way, Lekki Phase 1','+234-801-234-5678','lagos@sweettooth.com','Lagos head office and production center',NULL,'Nigeria','Lagos','Lagos','101001','Africa/Lagos',1,0,NULL,'2026-02-02 12:46:16','2026-02-02 12:46:16',NULL),
('019c1e63-b321-71ba-a153-c8fc9e31bcab','SweetTooth Abuja','ABJ-004','23 Gimbiya Street, Area 11, Garki','+234-802-345-6789','abuja@sweettooth.com','Abuja branch with gelato specialty',NULL,'Nigeria','FCT','Abuja','900001','Africa/Lagos',1,0,NULL,'2026-02-02 12:46:16','2026-02-02 12:46:16',NULL),
('019c1e63-b32d-72bd-847a-c1c17e7b8674','SweetTooth Enugu','ENU-005','34 Ogui Road, New Haven','+234-806-789-0123','enugu@sweettooth.com','Enugu branch serving South-East region',NULL,'Nigeria','Enugu','Enugu','400001','Africa/Lagos',1,0,NULL,'2026-02-02 12:46:16','2026-02-02 12:46:16',NULL),
('019c1e6b-b78e-718c-a7bc-292923cb07a1','SweetTooth Jos Branch 1','JOS-001','21437 Barrows Crest Suite 541, Jos','+234-902-237-1198','jos1@sweettooth.com','SweetTooth branch in Jos',NULL,'Nigeria','Jos','Jos','514478','Africa/Lagos',1,0,NULL,'2026-02-02 12:55:01','2026-02-02 12:55:01',NULL),
('019c1e6b-b7c3-70d0-85fd-59596666fa66','SweetTooth Port Harcourt Branch 2','POR-002','3196 Parisian Valley Apt. 190, Port Harcourt','+234-876-281-4169','port harcourt2@sweettooth.com','SweetTooth branch in Port Harcourt',NULL,'Nigeria','Port Harcourt','Port Harcourt','243135','Africa/Lagos',1,0,NULL,'2026-02-02 12:55:01','2026-02-02 12:55:01',NULL),
('019c1e6b-b7ca-73f4-a70f-6d66974c17f7','SweetTooth Ibadan Branch 3','IBA-003','801 Richmond View, Ibadan','+234-898-373-4105','ibadan3@sweettooth.com','SweetTooth branch in Ibadan',NULL,'Nigeria','Ibadan','Ibadan','681263','Africa/Lagos',1,0,NULL,'2026-02-02 12:55:01','2026-02-02 12:55:01',NULL),
('019c1e6b-b7e0-728e-8ac5-f7cd65c89c02','SweetTooth Ibadan Branch 4','IBA-004','753 Kareem Rapid, Ibadan','+234-895-690-6941','ibadan4@sweettooth.com','SweetTooth branch in Ibadan',NULL,'Nigeria','Ibadan','Ibadan','715390','Africa/Lagos',1,0,NULL,'2026-02-02 12:55:01','2026-02-02 12:55:01',NULL),
('019c1e6b-b7ec-70e2-8ca0-08c3a7442bbe','SweetTooth Benin City Branch 5','BEN-005','14745 Chaya Valley Apt. 190, Benin City','+234-871-346-5812','benin city5@sweettooth.com','SweetTooth branch in Benin City',NULL,'Nigeria','Benin City','Benin City','860995','Africa/Lagos',1,0,NULL,'2026-02-02 12:55:01','2026-02-02 12:55:01',NULL),
('019c1e6c-a3f7-73d1-8219-4cb5533b54f0','SweetTooth Abuja Branch 1','ABU-001','2644 Mayer Path Suite 754, Abuja','+234-855-665-1684','abuja1@sweettooth.com','SweetTooth branch in Abuja',NULL,'Nigeria','Abuja','Abuja','248281','Africa/Lagos',1,0,NULL,'2026-02-02 12:56:02','2026-02-02 12:56:02',NULL),
('019c1e6c-a424-70d5-9cbd-04aad6191aeb','SweetTooth Enugu Branch 2','ENU-002','620 Klein Ford, Enugu','+234-813-832-9640','enugu2@sweettooth.com','SweetTooth branch in Enugu',NULL,'Nigeria','Enugu','Enugu','574203','Africa/Lagos',1,0,NULL,'2026-02-02 12:56:02','2026-02-02 12:56:02',NULL),
('019c1e6c-a440-7392-96a4-4940eede44f0','SweetTooth Kano Branch 3','KAN-003','20249 Crona Plain, Kano','+234-891-641-1210','kano3@sweettooth.com','SweetTooth branch in Kano',NULL,'Nigeria','Kano','Kano','656721','Africa/Lagos',1,0,NULL,'2026-02-02 12:56:02','2026-02-02 12:56:02',NULL),
('019c1e6c-a44e-7339-bf80-83640887216d','SweetTooth Calabar Branch 4','CAL-004','279 Feest Rapid Apt. 175, Calabar','+234-899-346-2617','calabar4@sweettooth.com','SweetTooth branch in Calabar',NULL,'Nigeria','Calabar','Calabar','911964','Africa/Lagos',1,0,NULL,'2026-02-02 12:56:02','2026-02-02 12:56:02',NULL),
('019c1e6c-a468-70e6-b6bf-d2344101f318','SweetTooth Jos Branch 5','JOS-005','3084 Hillard Hill Suite 535, Jos','+234-819-155-5515','jos5@sweettooth.com','SweetTooth branch in Jos',NULL,'Nigeria','Jos','Jos','161455','Africa/Lagos',1,0,NULL,'2026-02-02 12:56:02','2026-02-02 12:56:02',NULL);
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
INSERT INTO `cache` VALUES
('laravel_cache_settings_model_App\\Models\\BranchBusinessConfiguration','N;',1770061522),
('laravel_cache_settings_model_App\\Models\\GlobalBusinessConfiguration','O:38:\"App\\Models\\GlobalBusinessConfiguration\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:30:\"global_business_configurations\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:1;s:12:\"company_name\";s:10:\"SweetTooth\";s:11:\"logo_upload\";s:50:\"logos/fUOXOVW77o7S601AskSi4wcRWKf7ViA85lu3KAKP.jpg\";s:15:\"contact_details\";s:39:\"{\"phone\":\"\",\"email\":\"\",\"vat_number\":\"\"}\";s:10:\"created_at\";s:19:\"2026-02-02 17:38:46\";s:10:\"updated_at\";s:19:\"2026-02-02 20:03:54\";s:15:\"backup_interval\";i:2;s:13:\"backup_period\";s:6:\"months\";s:11:\"auto_backup\";i:1;}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:1;s:12:\"company_name\";s:10:\"SweetTooth\";s:11:\"logo_upload\";s:50:\"logos/fUOXOVW77o7S601AskSi4wcRWKf7ViA85lu3KAKP.jpg\";s:15:\"contact_details\";s:39:\"{\"phone\":\"\",\"email\":\"\",\"vat_number\":\"\"}\";s:10:\"created_at\";s:19:\"2026-02-02 17:38:46\";s:10:\"updated_at\";s:19:\"2026-02-02 20:03:54\";s:15:\"backup_interval\";i:2;s:13:\"backup_period\";s:6:\"months\";s:11:\"auto_backup\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:15:\"contact_details\";s:5:\"array\";s:11:\"auto_backup\";s:7:\"boolean\";s:15:\"backup_interval\";s:7:\"integer\";s:13:\"backup_period\";s:6:\"string\";s:10:\"created_at\";s:8:\"datetime\";s:10:\"updated_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:12:\"company_name\";i:1;s:11:\"logo_upload\";i:2;s:15:\"contact_details\";i:3;s:11:\"auto_backup\";i:4;s:13:\"backup_period\";i:5;s:15:\"backup_interval\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}',1770061244);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `call_backs`
--

DROP TABLE IF EXISTS `call_backs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_backs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint(20) unsigned NOT NULL,
  `callback_type` varchar(255) NOT NULL,
  `reference_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units') NOT NULL,
  `reason` enum('expired','damaged','quality_issue','contaminated','other') NOT NULL,
  `description` text DEFAULT NULL,
  `reported_by_id` char(36) NOT NULL,
  `reported_by_type` varchar(255) NOT NULL,
  `callback_time` timestamp NOT NULL,
  `action_taken` enum('disposed','returned_to_supplier','reprocessed','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `call_backs_shift_id_foreign` (`shift_id`),
  CONSTRAINT `call_backs_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `call_backs`
--

LOCK TABLES `call_backs` WRITE;
/*!40000 ALTER TABLE `call_backs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `call_backs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cash_positions`
--

DROP TABLE IF EXISTS `cash_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_positions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cash_type` enum('sales_cash','petty_cash','drawer_cash') NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `position_date` date NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sales_receipts` decimal(15,2) NOT NULL DEFAULT 0.00,
  `withdrawals` decimal(15,2) NOT NULL DEFAULT 0.00,
  `closing_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `physical_count` decimal(15,2) DEFAULT NULL,
  `counted_at` timestamp NULL DEFAULT NULL,
  `variance_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `variance_notes` text DEFAULT NULL,
  `counted_by_id` char(36) DEFAULT NULL,
  `counted_by_type` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_positions_cash_type_branch_id_position_date_unique` (`cash_type`,`branch_id`,`position_date`),
  KEY `cash_positions_branch_id_foreign` (`branch_id`),
  KEY `cash_positions_position_date_cash_type_index` (`position_date`,`cash_type`),
  CONSTRAINT `cash_positions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_positions`
--

LOCK TABLES `cash_positions` WRITE;
/*!40000 ALTER TABLE `cash_positions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cash_positions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `clock_ins`
--

DROP TABLE IF EXISTS `clock_ins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  CONSTRAINT `clock_ins_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clock_ins`
--

LOCK TABLES `clock_ins` WRITE;
/*!40000 ALTER TABLE `clock_ins` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `clock_ins` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `compiled_report_department_report`
--

DROP TABLE IF EXISTS `compiled_report_department_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `compiled_report_department_report` (
  `compiled_report_id` char(36) NOT NULL,
  `department_report_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`compiled_report_id`,`department_report_id`),
  KEY `compiled_report_department_report_department_report_id_foreign` (`department_report_id`),
  CONSTRAINT `compiled_report_department_report_compiled_report_id_foreign` FOREIGN KEY (`compiled_report_id`) REFERENCES `compiled_reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compiled_report_department_report_department_report_id_foreign` FOREIGN KEY (`department_report_id`) REFERENCES `department_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compiled_report_department_report`
--

LOCK TABLES `compiled_report_department_report` WRITE;
/*!40000 ALTER TABLE `compiled_report_department_report` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `compiled_report_department_report` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `compiled_reports`
--

DROP TABLE IF EXISTS `compiled_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `compiled_reports` (
  `id` char(36) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `compiled_by_id` char(36) DEFAULT NULL,
  `compiled_by_type` varchar(255) DEFAULT NULL,
  `compilation_title` varchar(255) NOT NULL,
  `compilation_description` text DEFAULT NULL,
  `compilation_date` date NOT NULL,
  `period_from` date NOT NULL,
  `period_to` date NOT NULL,
  `included_reports` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`included_reports`)),
  `executive_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`executive_summary`)),
  `key_metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`key_metrics`)),
  `recommendations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommendations`)),
  `status` enum('draft','pending_approval','approved','sent_to_md','reviewed_by_md') NOT NULL DEFAULT 'draft',
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `sent_to_md_at` timestamp NULL DEFAULT NULL,
  `md_user_id` char(36) DEFAULT NULL,
  `md_reviewed_at` timestamp NULL DEFAULT NULL,
  `md_feedback` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compiled_reports_md_user_id_foreign` (`md_user_id`),
  KEY `compiled_reports_branch_id_compilation_date_index` (`branch_id`,`compilation_date`),
  KEY `compiled_reports_status_compilation_date_index` (`status`,`compilation_date`),
  CONSTRAINT `compiled_reports_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compiled_reports_md_user_id_foreign` FOREIGN KEY (`md_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compiled_reports`
--

LOCK TABLES `compiled_reports` WRITE;
/*!40000 ALTER TABLE `compiled_reports` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `compiled_reports` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `credit_note_items`
--

DROP TABLE IF EXISTS `credit_note_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_note_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `credit_note_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_note_items_credit_note_id_foreign` (`credit_note_id`),
  KEY `credit_note_items_product_id_foreign` (`product_id`),
  CONSTRAINT `credit_note_items_credit_note_id_foreign` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_note_items`
--

LOCK TABLES `credit_note_items` WRITE;
/*!40000 ALTER TABLE `credit_note_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `credit_note_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `credit_notes`
--

DROP TABLE IF EXISTS `credit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `credit_note_number` varchar(255) NOT NULL,
  `sale_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `credit_note_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `reason` text DEFAULT NULL,
  `amount_applied` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` char(36) DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credit_notes_credit_note_number_unique` (`credit_note_number`),
  KEY `credit_notes_sale_id_foreign` (`sale_id`),
  KEY `credit_notes_branch_id_foreign` (`branch_id`),
  KEY `credit_notes_created_by_foreign` (`created_by`),
  KEY `credit_notes_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `credit_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `credit_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `credit_notes_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `credit_notes_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_notes`
--

LOCK TABLES `credit_notes` WRITE;
/*!40000 ALTER TABLE `credit_notes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `credit_notes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `daily_bank_positions`
--

DROP TABLE IF EXISTS `daily_bank_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_bank_positions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` bigint(20) unsigned NOT NULL,
  `position_date` date NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `inflows_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `outflows_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unavailable_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unreflected_transfers_bf` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unreflected_pos_bf` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unreflected_transfers_cd` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unreflected_pos_cd` decimal(15,2) NOT NULL DEFAULT 0.00,
  `available_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `variance_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `variance_notes` text DEFAULT NULL,
  `reconciled` tinyint(1) NOT NULL DEFAULT 0,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_bank_positions_bank_account_id_position_date_unique` (`bank_account_id`,`position_date`),
  KEY `daily_bank_positions_position_date_reconciled_index` (`position_date`,`reconciled`),
  CONSTRAINT `daily_bank_positions_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_bank_positions`
--

LOCK TABLES `daily_bank_positions` WRITE;
/*!40000 ALTER TABLE `daily_bank_positions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `daily_bank_positions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `daily_bank_transactions`
--

DROP TABLE IF EXISTS `daily_bank_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_bank_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `daily_bank_position_id` bigint(20) unsigned NOT NULL,
  `bank_account_id` bigint(20) unsigned NOT NULL,
  `transaction_type` enum('inflow','outflow') NOT NULL,
  `transaction_subtype` enum('pos_sales','transfer_sales','reversed_transfer','other_income','cash_deposit','transfer_expense','cash_withdrawal','chargeback','bank_charge','other') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `cleared_date` datetime DEFAULT NULL,
  `status` enum('pending','cleared','reversed') NOT NULL DEFAULT 'pending',
  `reconciled` tinyint(1) NOT NULL DEFAULT 0,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `reconciled_by_id` char(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `daily_bank_transactions_daily_bank_position_id_foreign` (`daily_bank_position_id`),
  KEY `daily_bank_transactions_bank_account_id_transaction_date_index` (`bank_account_id`,`transaction_date`),
  KEY `daily_bank_transactions_transaction_type_status_index` (`transaction_type`,`status`),
  KEY `daily_bank_transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `daily_bank_transactions_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_bank_transactions_daily_bank_position_id_foreign` FOREIGN KEY (`daily_bank_position_id`) REFERENCES `daily_bank_positions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_bank_transactions`
--

LOCK TABLES `daily_bank_transactions` WRITE;
/*!40000 ALTER TABLE `daily_bank_transactions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `daily_bank_transactions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `daily_produces`
--

DROP TABLE IF EXISTS `daily_produces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `estimated_completion_time` timestamp NULL DEFAULT NULL,
  `actual_completion_time` timestamp NULL DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `assigned_to` char(36) DEFAULT NULL,
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_produces_shift_id_recipe_id_unique` (`shift_id`,`recipe_id`),
  KEY `daily_produces_recipe_id_foreign` (`recipe_id`),
  KEY `daily_produces_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `daily_produces_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_produces_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_produces_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_produces`
--

LOCK TABLES `daily_produces` WRITE;
/*!40000 ALTER TABLE `daily_produces` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `daily_produces` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `debit_note_items`
--

DROP TABLE IF EXISTS `debit_note_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `debit_note_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `debit_note_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `debit_note_items_debit_note_id_foreign` (`debit_note_id`),
  KEY `debit_note_items_product_id_foreign` (`product_id`),
  CONSTRAINT `debit_note_items_debit_note_id_foreign` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `debit_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debit_note_items`
--

LOCK TABLES `debit_note_items` WRITE;
/*!40000 ALTER TABLE `debit_note_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `debit_note_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `debit_notes`
--

DROP TABLE IF EXISTS `debit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `debit_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `debit_note_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `purchase_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `debit_note_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `reason` text DEFAULT NULL,
  `amount_applied` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` char(36) DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `debit_notes_debit_note_number_unique` (`debit_note_number`),
  KEY `debit_notes_supplier_id_foreign` (`supplier_id`),
  KEY `debit_notes_purchase_id_foreign` (`purchase_id`),
  KEY `debit_notes_branch_id_foreign` (`branch_id`),
  KEY `debit_notes_created_by_foreign` (`created_by`),
  KEY `debit_notes_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `debit_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `debit_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `debit_notes_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `debit_notes_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  CONSTRAINT `debit_notes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debit_notes`
--

LOCK TABLES `debit_notes` WRITE;
/*!40000 ALTER TABLE `debit_notes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `debit_notes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `delivery_note_items`
--

DROP TABLE IF EXISTS `delivery_note_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_note_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_note_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `sales_order_item_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_note_items_delivery_note_id_foreign` (`delivery_note_id`),
  KEY `delivery_note_items_product_id_foreign` (`product_id`),
  KEY `delivery_note_items_sales_order_item_id_foreign` (`sales_order_item_id`),
  CONSTRAINT `delivery_note_items_delivery_note_id_foreign` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `delivery_note_items_sales_order_item_id_foreign` FOREIGN KEY (`sales_order_item_id`) REFERENCES `sales_order_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_note_items`
--

LOCK TABLES `delivery_note_items` WRITE;
/*!40000 ALTER TABLE `delivery_note_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `delivery_note_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `delivery_notes`
--

DROP TABLE IF EXISTS `delivery_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_note_number` varchar(255) NOT NULL,
  `sales_order_id` bigint(20) unsigned DEFAULT NULL,
  `sale_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `delivery_date` date NOT NULL,
  `delivery_address` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `carrier` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `delivered_by` char(36) DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_notes_delivery_note_number_unique` (`delivery_note_number`),
  KEY `delivery_notes_sales_order_id_foreign` (`sales_order_id`),
  KEY `delivery_notes_sale_id_foreign` (`sale_id`),
  KEY `delivery_notes_branch_id_foreign` (`branch_id`),
  KEY `delivery_notes_delivered_by_foreign` (`delivered_by`),
  KEY `delivery_notes_created_by_foreign` (`created_by`),
  CONSTRAINT `delivery_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `delivery_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `delivery_notes_delivered_by_foreign` FOREIGN KEY (`delivered_by`) REFERENCES `users` (`id`),
  CONSTRAINT `delivery_notes_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `delivery_notes_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_notes`
--

LOCK TABLES `delivery_notes` WRITE;
/*!40000 ALTER TABLE `delivery_notes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `delivery_notes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `department_categories`
--

DROP TABLE IF EXISTS `department_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
INSERT INTO `department_categories` VALUES
('019c1e63-f79c-71ae-bfcd-7add8ac01a51','Sales','Departments focused on selling products and services, customer acquisition, and revenue generation.','2026-02-02 12:46:33','2026-02-02 12:46:33'),
('019c1e63-f7a8-72eb-868e-b1a1aeb6a4b2','Production','Departments responsible for manufacturing, production processes, and quality control.','2026-02-02 12:46:33','2026-02-02 12:46:33'),
('019c1e63-f7b3-73d2-9498-8c8470eb9837','Support','Departments providing assistance, customer service, and technical support services.','2026-02-02 12:46:33','2026-02-02 12:46:33');
/*!40000 ALTER TABLE `department_categories` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `department_pages`
--

DROP TABLE IF EXISTS `department_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_pages_department_id_slug_unique` (`department_id`,`slug`),
  CONSTRAINT `department_pages_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_pages`
--

LOCK TABLES `department_pages` WRITE;
/*!40000 ALTER TABLE `department_pages` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `department_pages` VALUES
(1,1,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(2,1,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(3,1,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(4,1,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2026-02-02 12:46:34','2026-02-02 12:46:56'),
(5,1,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2026-02-02 12:46:34','2026-02-02 12:46:56'),
(6,1,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2026-02-02 12:46:34','2026-02-02 12:46:56'),
(7,1,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(8,1,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(9,1,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(10,1,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',10,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(11,1,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',11,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(12,1,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',12,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(13,2,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(14,2,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(15,2,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(16,2,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2026-02-02 12:46:34','2026-02-02 12:46:56'),
(17,2,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2026-02-02 12:46:34','2026-02-02 12:46:57'),
(18,2,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2026-02-02 12:46:34','2026-02-02 12:46:57'),
(19,2,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(20,2,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(21,2,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(22,2,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',10,1,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(23,2,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',11,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(24,2,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',12,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(25,3,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(26,3,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(27,3,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(28,3,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2026-02-02 12:46:35','2026-02-02 12:46:57'),
(29,3,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2026-02-02 12:46:35','2026-02-02 12:46:57'),
(30,3,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2026-02-02 12:46:35','2026-02-02 12:46:57'),
(31,3,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(32,3,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(33,3,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(34,3,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',10,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(35,3,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',11,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(36,3,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',12,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(37,4,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(38,4,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(39,4,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(40,4,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(41,5,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(42,5,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(43,5,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(44,5,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(45,6,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(46,6,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(47,6,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
(48,6,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
(49,1,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2026-02-02 12:46:56','2026-02-02 12:46:56'),
(50,1,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2026-02-02 12:46:56','2026-02-02 12:46:56'),
(51,1,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2026-02-02 12:46:56','2026-02-02 12:46:56'),
(52,2,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2026-02-02 12:46:56','2026-02-02 12:46:56'),
(53,2,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2026-02-02 12:46:56','2026-02-02 12:46:56'),
(54,2,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2026-02-02 12:46:56','2026-02-02 12:46:56'),
(55,3,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(56,3,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(57,3,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2026-02-02 12:46:57','2026-02-02 12:46:57'),
(58,10,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(59,10,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(60,10,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(61,10,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',4,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(62,10,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',5,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(63,10,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',6,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(64,10,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(65,10,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(66,10,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(67,10,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',10,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(68,10,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',11,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(69,10,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',12,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(70,11,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(71,11,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(72,11,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(73,11,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',4,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(74,11,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',5,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(75,11,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',6,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(76,11,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(77,11,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(78,11,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(79,11,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',10,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(80,11,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',11,1,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(81,11,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',12,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(82,12,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(83,12,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(84,12,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(85,12,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(86,13,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(87,13,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(88,13,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(89,13,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-02-02 12:56:03','2026-02-02 12:56:03');
/*!40000 ALTER TABLE `department_pages` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `department_product`
--

DROP TABLE IF EXISTS `department_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this product is available in this department',
  `department_price` decimal(10,2) DEFAULT NULL COMMENT 'Department-specific price override',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Display order in department',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_product_department_id_product_id_unique` (`department_id`,`product_id`),
  KEY `department_product_product_id_foreign` (`product_id`),
  KEY `department_product_is_available_index` (`is_available`),
  CONSTRAINT `department_product_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_product`
--

LOCK TABLES `department_product` WRITE;
/*!40000 ALTER TABLE `department_product` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `department_product` VALUES
(1,4,'019c1e64-3746-7337-af33-de19ff585db2',1,NULL,0,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(2,4,'019c1e64-3763-720c-9310-55f745030649',1,NULL,1,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(3,4,'019c1e64-376d-7041-8424-c7bf61aee980',1,NULL,2,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(4,4,'019c1e64-3784-7343-808d-0a9a36197eaa',1,NULL,3,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(5,4,'019c1e64-379b-72dd-8488-7de003fe2455',1,NULL,4,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(6,4,'019c1e64-37a6-7070-b493-09c4d7d7673c',1,NULL,5,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(7,5,'019c1e64-3746-7337-af33-de19ff585db2',1,NULL,0,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(8,5,'019c1e64-3763-720c-9310-55f745030649',1,NULL,1,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(9,5,'019c1e64-376d-7041-8424-c7bf61aee980',1,NULL,2,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(10,5,'019c1e64-3784-7343-808d-0a9a36197eaa',1,NULL,3,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(11,5,'019c1e64-378f-726c-83b2-eef3a471fecf',1,NULL,4,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(12,6,'019c1e64-37bc-71d4-83ef-a6d51c6a53d6',1,NULL,0,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(13,6,'019c1e64-37c7-7367-956f-fc4b6b738929',1,NULL,1,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(14,6,'019c1e64-37d2-735a-8546-9961146ab53a',1,NULL,2,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(15,6,'019c1e64-37dd-7108-ae30-90f9166313bc',1,NULL,3,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(16,6,'019c1e64-37e8-72c5-9697-0bb2b5000050',1,NULL,4,'2026-02-02 12:46:50','2026-02-02 12:46:50'),
(17,6,'019c1e64-37f3-7120-b56b-2c9646c12849',1,NULL,5,'2026-02-02 12:46:50','2026-02-02 12:46:50');
/*!40000 ALTER TABLE `department_product` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `department_reports`
--

DROP TABLE IF EXISTS `department_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_reports` (
  `id` char(36) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `generated_by_id` char(36) DEFAULT NULL,
  `generated_by_type` varchar(255) DEFAULT NULL,
  `report_type` varchar(255) NOT NULL,
  `report_category` varchar(255) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `report_date` date NOT NULL,
  `period_from` date NOT NULL,
  `period_to` date NOT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`report_data`)),
  `summary_metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary_metrics`)),
  `charts_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`charts_data`)),
  `status` enum('draft','pending_review','reviewed','compiled','sent_to_md') NOT NULL DEFAULT 'draft',
  `reviewed_by_id` char(36) DEFAULT NULL,
  `reviewed_by_type` varchar(255) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `export_format` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_reports_branch_id_report_date_index` (`branch_id`,`report_date`),
  KEY `department_reports_department_id_report_date_index` (`department_id`,`report_date`),
  KEY `department_reports_report_type_report_category_index` (`report_type`,`report_category`),
  KEY `department_reports_status_report_date_index` (`status`,`report_date`),
  CONSTRAINT `department_reports_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_reports_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_reports`
--

LOCK TABLES `department_reports` WRITE;
/*!40000 ALTER TABLE `department_reports` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `department_reports` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `category_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `manager_user_id` char(36) DEFAULT NULL,
  `enable_table_management` tinyint(1) NOT NULL DEFAULT 0,
  `table_management_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_management_settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_name_unique` (`name`),
  UNIQUE KEY `departments_branch_id_slug_unique` (`branch_id`,`slug`),
  KEY `departments_category_id_foreign` (`category_id`),
  KEY `departments_is_active_index` (`is_active`),
  KEY `departments_manager_user_id_index` (`manager_user_id`),
  CONSTRAINT `departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departments_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `department_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departments_manager_user_id_foreign` FOREIGN KEY (`manager_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `departments` VALUES
(1,NULL,'019c1e63-f7a8-72eb-868e-b1a1aeb6a4b2','Kitchen','kitchen','Prepares food for Till, Confectionaries, Corner Store',1,NULL,0,NULL,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(2,NULL,'019c1e63-f7a8-72eb-868e-b1a1aeb6a4b2','Gelato Production','gelato-production','Makes gelato/ice cream',1,NULL,0,NULL,'2026-02-02 12:46:34','2026-02-02 12:46:34'),
(3,NULL,'019c1e63-f7a8-72eb-868e-b1a1aeb6a4b2','Confectionaries Production','confectionaries-production','Makes confectionery items',1,NULL,0,NULL,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(4,NULL,'019c1e63-f79c-71ae-bfcd-7add8ac01a51','Till','till','Sells ready-made snacks',1,NULL,0,NULL,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(5,NULL,'019c1e63-f79c-71ae-bfcd-7add8ac01a51','Corner Store','corner-store','On-demand food sales',1,NULL,0,NULL,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(6,NULL,'019c1e63-f79c-71ae-bfcd-7add8ac01a51','Confectionaries Sales','confectionaries-sales','Sells confectionery items',1,NULL,0,NULL,'2026-02-02 12:46:35','2026-02-02 12:46:35'),
(7,NULL,'019c1e63-f7b3-73d2-9498-8c8470eb9837','Inventory/Store','inventorystore','Manages all stock',1,NULL,0,NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
(8,NULL,'019c1e63-f7b3-73d2-9498-8c8470eb9837','HR','hr','Human resources (corporate level)',1,NULL,0,NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
(10,'019c1e6c-a44e-7339-bf80-83640887216d','019c1e63-f7a8-72eb-868e-b1a1aeb6a4b2','Bakery','bakery','Bakery department',1,NULL,0,NULL,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(11,'019c1e6c-a440-7392-96a4-4940eede44f0','019c1e63-f7a8-72eb-868e-b1a1aeb6a4b2','Beverage Production','beverage-production','Beverage Production department',1,NULL,0,NULL,'2026-02-02 12:56:02','2026-02-02 12:56:02'),
(12,'019c1e6c-a468-70e6-b6bf-d2344101f318','019c1e63-f79c-71ae-bfcd-7add8ac01a51','Dine-in Service','dine-in-service','Dine-in Service department',1,NULL,0,NULL,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(13,'019c1e6c-a44e-7339-bf80-83640887216d','019c1e63-f79c-71ae-bfcd-7add8ac01a51','Online Orders','online-orders','Online Orders department',1,NULL,0,NULL,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(14,'019c1e6c-a468-70e6-b6bf-d2344101f318','019c1e63-f7b3-73d2-9498-8c8470eb9837','Finance','finance','Finance department',1,NULL,0,NULL,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(15,'019c1e6c-a440-7392-96a4-4940eede44f0','019c1e63-f7b3-73d2-9498-8c8470eb9837','IT','it','IT department',1,NULL,0,NULL,'2026-02-02 12:56:03','2026-02-02 12:56:03'),
(16,'019c1e6c-a44e-7339-bf80-83640887216d','019c1e63-f7b3-73d2-9498-8c8470eb9837','Maintenance','maintenance','Maintenance department',1,NULL,0,NULL,'2026-02-02 12:56:03','2026-02-02 12:56:03');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `employee_leave_allocations`
--

DROP TABLE IF EXISTS `employee_leave_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_leave_allocations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `year` int(11) NOT NULL,
  `allocated_days` decimal(8,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `allocated_by_id` char(36) NOT NULL,
  `allocated_by_type` varchar(255) NOT NULL,
  `allocated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_leave_allocations_employee_id_leave_type_id_year_unique` (`employee_id`,`leave_type_id`,`year`),
  KEY `employee_leave_allocations_leave_type_id_foreign` (`leave_type_id`),
  KEY `employee_leave_allocations_employee_id_year_index` (`employee_id`,`year`),
  CONSTRAINT `employee_leave_allocations_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_leave_allocations`
--

LOCK TABLES `employee_leave_allocations` WRITE;
/*!40000 ALTER TABLE `employee_leave_allocations` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `employee_leave_allocations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `employee_leave_balances`
--

DROP TABLE IF EXISTS `employee_leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_leave_balances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `year` int(11) NOT NULL,
  `total_days` decimal(8,2) NOT NULL DEFAULT 0.00,
  `used_days` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pending_days` decimal(8,2) NOT NULL DEFAULT 0.00,
  `remaining_days` decimal(8,2) NOT NULL DEFAULT 0.00,
  `carried_forward` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_leave_balances_employee_id_leave_type_id_year_unique` (`employee_id`,`leave_type_id`,`year`),
  KEY `employee_leave_balances_leave_type_id_foreign` (`leave_type_id`),
  CONSTRAINT `employee_leave_balances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_leave_balances`
--

LOCK TABLES `employee_leave_balances` WRITE;
/*!40000 ALTER TABLE `employee_leave_balances` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `employee_leave_balances` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `employee_stepouts`
--

DROP TABLE IF EXISTS `employee_stepouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_stepouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `reason` enum('restroom','water_break','prayer','emergency','medical','other') NOT NULL DEFAULT 'restroom',
  `reason_details` text DEFAULT NULL,
  `request_time` timestamp NOT NULL,
  `approved_time` timestamp NULL DEFAULT NULL,
  `stepout_time` timestamp NULL DEFAULT NULL,
  `return_time` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `expected_duration_minutes` int(11) NOT NULL DEFAULT 15,
  `status` enum('pending','approved','rejected','in_progress','completed','overdue') NOT NULL DEFAULT 'pending',
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejected_by_id` char(36) DEFAULT NULL,
  `rejected_by_type` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `is_overdue` tinyint(1) NOT NULL DEFAULT 0,
  `overdue_minutes` int(11) DEFAULT NULL,
  `overdue_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_stepouts_branch_id_foreign` (`branch_id`),
  KEY `employee_stepouts_shift_id_foreign` (`shift_id`),
  KEY `employee_stepouts_employee_id_request_time_index` (`employee_id`,`request_time`),
  KEY `employee_stepouts_status_request_time_index` (`status`,`request_time`),
  CONSTRAINT `employee_stepouts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_stepouts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_stepouts_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_stepouts`
--

LOCK TABLES `employee_stepouts` WRITE;
/*!40000 ALTER TABLE `employee_stepouts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `employee_stepouts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` char(36) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  UNIQUE KEY `employees_email_unique` (`email`),
  KEY `employees_branch_id_foreign` (`branch_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `expense_claim_items`
--

DROP TABLE IF EXISTS `expense_claim_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_claim_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_claim_id` bigint(20) unsigned NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `gl_account_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expense_claim_items_expense_claim_id_foreign` (`expense_claim_id`),
  KEY `expense_claim_items_gl_account_id_foreign` (`gl_account_id`),
  CONSTRAINT `expense_claim_items_expense_claim_id_foreign` FOREIGN KEY (`expense_claim_id`) REFERENCES `expense_claims` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expense_claim_items_gl_account_id_foreign` FOREIGN KEY (`gl_account_id`) REFERENCES `gl_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_claim_items`
--

LOCK TABLES `expense_claim_items` WRITE;
/*!40000 ALTER TABLE `expense_claim_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `expense_claim_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `expense_claims`
--

DROP TABLE IF EXISTS `expense_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_claims` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `claim_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `description` text DEFAULT NULL,
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_via_bank_account_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expense_claims_employee_id_foreign` (`employee_id`),
  KEY `expense_claims_branch_id_foreign` (`branch_id`),
  KEY `expense_claims_approved_by_foreign` (`approved_by`),
  KEY `expense_claims_paid_via_bank_account_id_foreign` (`paid_via_bank_account_id`),
  KEY `expense_claims_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `expense_claims_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `expense_claims_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `expense_claims_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  CONSTRAINT `expense_claims_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `expense_claims_paid_via_bank_account_id_foreign` FOREIGN KEY (`paid_via_bank_account_id`) REFERENCES `bank_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_claims`
--

LOCK TABLES `expense_claims` WRITE;
/*!40000 ALTER TABLE `expense_claims` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `expense_claims` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `expiry_confirmations`
--

DROP TABLE IF EXISTS `expiry_confirmations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expiry_confirmations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_stock_id` bigint(20) unsigned NOT NULL,
  `sales_shift_id` bigint(20) unsigned NOT NULL,
  `confirmed_by_id` char(36) NOT NULL,
  `confirmed_by_type` varchar(255) NOT NULL,
  `action` enum('confirmed_good','marked_callback') NOT NULL COMMENT 'Action taken: confirmed still good or marked as callback',
  `notes` text DEFAULT NULL COMMENT 'Reason for confirmation or callback',
  `confirmed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_expiry_confirmation` (`product_stock_id`,`sales_shift_id`),
  KEY `expiry_confirmations_sales_shift_id_foreign` (`sales_shift_id`),
  CONSTRAINT `expiry_confirmations_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `product_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expiry_confirmations_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expiry_confirmations`
--

LOCK TABLES `expiry_confirmations` WRITE;
/*!40000 ALTER TABLE `expiry_confirmations` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `expiry_confirmations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `gl_accounts`
--

DROP TABLE IF EXISTS `gl_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gl_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_number` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('asset','liability','equity','revenue','cost_of_goods_sold','expense','other_income','other_expense','tax') NOT NULL,
  `account_category` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `debit_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `normal_balance` enum('debit','credit') NOT NULL DEFAULT 'debit',
  `is_header` tinyint(1) NOT NULL DEFAULT 0,
  `parent_account_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `allow_manual_entry` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gl_accounts_account_number_unique` (`account_number`),
  KEY `gl_accounts_account_type_is_active_index` (`account_type`,`is_active`),
  KEY `gl_accounts_parent_account_id_index` (`parent_account_id`),
  CONSTRAINT `gl_accounts_parent_account_id_foreign` FOREIGN KEY (`parent_account_id`) REFERENCES `gl_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gl_accounts`
--

LOCK TABLES `gl_accounts` WRITE;
/*!40000 ALTER TABLE `gl_accounts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gl_accounts` VALUES
(1,'1100','Current Assets','asset','Current Assets','Short-term assets expected to convert to cash within one year',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:46:58','2026-02-02 12:46:58'),
(2,'1101','Cash on Hand','asset','Cash & Equivalents','Physical cash held at branches/locations',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(3,'1110','Cash in Bank - Main','asset','Cash & Equivalents','Primary operating bank account',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(4,'1120','Cash in Bank - Petty Cash','asset','Cash & Equivalents','Petty cash/imprest account',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(5,'1200','Accounts Receivable','asset','Receivables','Credit sales awaiting payment',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(6,'1210','Allowance for Bad Debts','asset','Receivables','Contra-asset account for doubtful receivables',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(7,'1300','Inventory Assets','asset','Inventory','Raw materials, WIP, and finished goods inventory',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(8,'1310','Raw Materials Inventory','asset','Inventory','Raw materials held for production',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(9,'1320','Work in Process Inventory','asset','Inventory','Partially completed production items',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(10,'1330','Finished Goods Inventory','asset','Inventory','Completed products ready for sale',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(11,'1340','Supplies Inventory','asset','Inventory','Consumable supplies and materials',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(12,'1500','Fixed Assets','asset','Fixed Assets','Long-term tangible assets',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(13,'1510','Equipment','asset','Fixed Assets','Production and operational equipment',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(14,'1520','Accumulated Depreciation - Equipment','asset','Fixed Assets','Contra-asset account for equipment depreciation',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(15,'1530','Furniture & Fixtures','asset','Fixed Assets','Office furniture and fixtures',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(16,'1540','Accumulated Depreciation - Furniture','asset','Fixed Assets','Contra-asset account for furniture depreciation',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(17,'2100','Current Liabilities','liability','Current Liabilities','Short-term obligations due within one year',0.00,0.00,'credit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(18,'2101','Accounts Payable','liability','Payables','Amounts owed to suppliers for goods/services',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(19,'2102','Accrued Expenses','liability','Payables','Expenses incurred but not yet paid',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(20,'2110','Sales Tax Payable','liability','Tax Liabilities','Sales tax collected from customers',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(21,'2120','VAT Payable','liability','Tax Liabilities','Value Added Tax liability',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(22,'2200','Short-term Debt','liability','Debt','Short-term loans and borrowings',0.00,0.00,'credit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(23,'2201','Short-term Loans','liability','Debt','Short-term borrowings from financial institutions',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(24,'2210','Current Portion of Long-term Debt','liability','Debt','Portion of long-term debt due within 12 months',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(25,'3100','Owner\'s Equity','equity','Equity','Owners investment and accumulated earnings',0.00,0.00,'credit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(26,'3101','Capital Stock','equity','Equity','Owners equity investment',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(27,'3110','Retained Earnings','equity','Equity','Accumulated profits from prior periods',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(28,'3120','Current Period Earnings','equity','Equity','Profit/Loss for the current accounting period',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(29,'4100','Product Sales','revenue','Sales Revenue','Revenue from product sales',0.00,0.00,'credit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(30,'4110','Product Sales - Main','revenue','Sales Revenue','Primary product sales revenue',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(31,'4111','Product Sales - Secondary','revenue','Sales Revenue','Secondary/ancillary product sales revenue',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(32,'4200','Service Revenue','revenue','Service Revenue','Revenue from services rendered',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(33,'4300','Other Income','other_income','Other Income','Miscellaneous revenue sources',0.00,0.00,'credit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(34,'4310','Discount Received','other_income','Other Income','Purchase discounts and rebates received',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(35,'4311','Interest Income','other_income','Other Income','Interest earned on bank accounts',0.00,0.00,'credit',0,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(36,'5100','Cost of Goods Sold','cost_of_goods_sold','COGS','Direct costs of products sold',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:46:59','2026-02-02 12:46:59'),
(37,'5110','COGS - Production','cost_of_goods_sold','COGS','Cost of internally produced goods sold',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(38,'5120','COGS - Purchased','cost_of_goods_sold','COGS','Cost of purchased goods sold',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(39,'5200','Inventory Adjustments','cost_of_goods_sold','Inventory','Inventory valuation adjustments',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(40,'5210','Inventory Writeoff','cost_of_goods_sold','Inventory','Obsolete or damaged inventory write-offs',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(41,'5211','Obsolescence Adjustment','cost_of_goods_sold','Inventory','Adjustment for obsolete inventory',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(42,'6100','Production Expenses','expense','Production','Indirect production and manufacturing expenses',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(43,'6110','Raw Materials Used','expense','Production','Raw materials consumed in production',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(44,'6120','Direct Labor','expense','Production','Wages of production workers',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(45,'6130','Manufacturing Overhead','expense','Production','Indirect manufacturing costs and overhead',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(46,'6200','Operating Expenses','expense','Operating','General operating and administrative expenses',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(47,'6210','Salaries & Wages','expense','Payroll','Employee salaries and wages (non-production)',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(48,'6220','Rent/Lease Expense','expense','Facilities','Rent or lease expenses for buildings/facilities',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(49,'6230','Utilities Expense','expense','Facilities','Electricity, water, gas, and other utilities',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(50,'6240','Maintenance & Repairs','expense','Facilities','Building and equipment maintenance and repairs',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(51,'6250','Transportation & Delivery','expense','Distribution','Product delivery and transportation costs',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(52,'6300','Administrative Expenses','expense','Administrative','General administrative and overhead expenses',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(53,'6310','Depreciation Expense','expense','Administrative','Depreciation of fixed assets',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(54,'6320','Office Supplies','expense','Administrative','Office supplies and consumables',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(55,'6330','Insurance Expense','expense','Administrative','Business insurance premiums',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(56,'6340','Professional Fees','expense','Administrative','Legal, accounting, and consulting fees',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(57,'6400','Sales & Marketing','expense','Sales & Marketing','Advertising and sales promotion expenses',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(58,'6410','Advertising Expense','expense','Sales & Marketing','Advertising and promotional expenses',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(59,'6420','Sales Commissions','expense','Sales & Marketing','Sales commissions and incentives',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(60,'7100','Tax Expense','tax','Taxes','Income and other tax expenses',0.00,0.00,'debit',1,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(61,'7110','Income Tax Expense','tax','Taxes','Current and deferred income tax expense',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(62,'7120','Sales Tax Expense','tax','Taxes','Sales and excise tax expenses',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:00','2026-02-02 12:47:00'),
(63,'1050','Main Bank Account','asset','bank','Main bank account for the business',0.00,0.00,'debit',0,NULL,1,1,NULL,'2026-02-02 12:47:01','2026-02-02 12:47:01');
/*!40000 ALTER TABLE `gl_accounts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `gl_entries`
--

DROP TABLE IF EXISTS `gl_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gl_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gl_account_id` bigint(20) unsigned NOT NULL,
  `accounting_period_id` bigint(20) unsigned NOT NULL,
  `entry_type` varchar(255) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `entry_date` datetime NOT NULL,
  `status` enum('draft','posted','reversed') NOT NULL DEFAULT 'draft',
  `reconciled` tinyint(1) NOT NULL DEFAULT 0,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `reconciled_by_id` char(36) DEFAULT NULL,
  `entered_by_id` char(36) DEFAULT NULL,
  `entered_by_type` varchar(255) DEFAULT NULL,
  `posted_by_id` char(36) DEFAULT NULL,
  `posted_by_type` varchar(255) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `reversed_by_id` char(36) DEFAULT NULL,
  `reversed_by_type` varchar(255) DEFAULT NULL,
  `reversed_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `cost_center` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gl_entries_branch_id_foreign` (`branch_id`),
  KEY `gl_entries_gl_account_id_entry_date_index` (`gl_account_id`,`entry_date`),
  KEY `gl_entries_accounting_period_id_status_index` (`accounting_period_id`,`status`),
  KEY `gl_entries_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `gl_entries_entry_date_branch_id_index` (`entry_date`,`branch_id`),
  KEY `gl_entries_entered_by_index` (`entered_by_id`,`entered_by_type`),
  KEY `gl_entries_posted_by_index` (`posted_by_id`,`posted_by_type`),
  CONSTRAINT `gl_entries_accounting_period_id_foreign` FOREIGN KEY (`accounting_period_id`) REFERENCES `accounting_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gl_entries_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `gl_entries_gl_account_id_foreign` FOREIGN KEY (`gl_account_id`) REFERENCES `gl_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gl_entries`
--

LOCK TABLES `gl_entries` WRITE;
/*!40000 ALTER TABLE `gl_entries` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gl_entries` VALUES
(1,2,14,'sales',NULL,NULL,'SALE-001','Cash sales for the day',150000.00,0.00,'2026-01-28 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:02','2026-02-02 12:47:02'),
(2,30,14,'sales',NULL,NULL,'SALE-001','Sales revenue from cash sales',0.00,150000.00,'2026-01-28 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:02','2026-02-02 12:47:02'),
(3,8,14,'purchase',NULL,NULL,'PUR-001','Purchase of raw materials',75000.00,0.00,'2026-01-29 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03'),
(4,18,14,'purchase',NULL,NULL,'PUR-001','Accounts payable for raw materials',0.00,75000.00,'2026-01-29 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03'),
(5,47,14,'expense',NULL,NULL,'SAL-001','Monthly salary payment',500000.00,0.00,'2026-01-30 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03'),
(6,3,14,'expense',NULL,NULL,'SAL-001','Bank payment for salary',0.00,500000.00,'2026-01-30 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03'),
(7,3,14,'sales',NULL,NULL,'SALE-002','Bank deposit from sales',200000.00,0.00,'2026-01-31 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03'),
(8,30,14,'sales',NULL,NULL,'SALE-002','Sales revenue from bank deposit',0.00,200000.00,'2026-01-31 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03'),
(9,2,14,'expense',NULL,NULL,'UTL-001','Utility payment',0.00,30000.00,'2026-02-01 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03'),
(10,49,14,'expense',NULL,NULL,'UTL-001','Utility expense',30000.00,0.00,'2026-02-01 13:47:02','posted',0,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:47:03','2026-02-02 12:47:03');
/*!40000 ALTER TABLE `gl_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_accounting_cashes`
--

DROP TABLE IF EXISTS `global_accounting_cashes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_accounting_cashes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_branch_management`
--

DROP TABLE IF EXISTS `global_branch_management`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_branch_management` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_business_configurations`
--

DROP TABLE IF EXISTS `global_business_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_business_configurations`
--

LOCK TABLES `global_business_configurations` WRITE;
/*!40000 ALTER TABLE `global_business_configurations` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `global_business_configurations` VALUES
(1,'SweetTooth','logos/fUOXOVW77o7S601AskSi4wcRWKf7ViA85lu3KAKP.jpg','{\"phone\":\"\",\"email\":\"\",\"vat_number\":\"\"}','2026-02-02 16:38:46','2026-02-02 19:03:54',2,'months',1);
/*!40000 ALTER TABLE `global_business_configurations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_currency_localizations`
--

DROP TABLE IF EXISTS `global_currency_localizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_currency_localizations`
--

LOCK TABLES `global_currency_localizations` WRITE;
/*!40000 ALTER TABLE `global_currency_localizations` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `global_currency_localizations` VALUES
(1,'enabled','NGN',NULL,'[\"USD\",\"EUR\",\"GBP\",\"INR\",\"NGN\"]','enabled','en','[\"en\",\"es\",\"fr\",\"ar\"]','MM/DD/YYYY','[\"piece\",\"kg\",\"liter\"]','2026-02-02 12:46:56','2026-02-02 12:46:56');
/*!40000 ALTER TABLE `global_currency_localizations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_customer_supplier_managements`
--

DROP TABLE IF EXISTS `global_customer_supplier_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_customer_supplier_managements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_employee_managements`
--

DROP TABLE IF EXISTS `global_employee_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_employee_managements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_inventory_managements`
--

DROP TABLE IF EXISTS `global_inventory_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_inventory_managements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_notifications_alerts`
--

DROP TABLE IF EXISTS `global_notifications_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_notifications_alerts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_pos_configurations`
--

DROP TABLE IF EXISTS `global_pos_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_pos_configurations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_reports_analytics`
--

DROP TABLE IF EXISTS `global_reports_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_reports_analytics` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `global_security_accesses`
--

DROP TABLE IF EXISTS `global_security_accesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `global_security_accesses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `goods_receipt_items`
--

DROP TABLE IF EXISTS `goods_receipt_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods_receipt_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `goods_receipt_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `purchase_order_item_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity_expected` decimal(15,4) NOT NULL,
  `quantity_received` decimal(15,4) NOT NULL,
  `quantity_accepted` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `quantity_rejected` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_receipt_items_goods_receipt_id_foreign` (`goods_receipt_id`),
  KEY `goods_receipt_items_product_id_foreign` (`product_id`),
  KEY `goods_receipt_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  CONSTRAINT `goods_receipt_items_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_receipt_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `goods_receipt_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_receipt_items`
--

LOCK TABLES `goods_receipt_items` WRITE;
/*!40000 ALTER TABLE `goods_receipt_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `goods_receipt_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `goods_receipts`
--

DROP TABLE IF EXISTS `goods_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(255) NOT NULL,
  `purchase_order_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `receipt_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `received_by` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_receipts_receipt_number_unique` (`receipt_number`),
  KEY `goods_receipts_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `goods_receipts_purchase_id_foreign` (`purchase_id`),
  KEY `goods_receipts_supplier_id_foreign` (`supplier_id`),
  KEY `goods_receipts_branch_id_foreign` (`branch_id`),
  KEY `goods_receipts_received_by_foreign` (`received_by`),
  KEY `goods_receipts_created_by_foreign` (`created_by`),
  CONSTRAINT `goods_receipts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `goods_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `goods_receipts_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  CONSTRAINT `goods_receipts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `goods_receipts_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`),
  CONSTRAINT `goods_receipts_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_receipts`
--

LOCK TABLES `goods_receipts` WRITE;
/*!40000 ALTER TABLE `goods_receipts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `goods_receipts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `health_checks`
--

DROP TABLE IF EXISTS `health_checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `health_checks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` bigint(20) unsigned NOT NULL,
  `checked_by_id` char(36) NOT NULL,
  `checked_by_type` varchar(255) NOT NULL,
  `check_date` date NOT NULL,
  `condition` enum('excellent','good','fair','poor','damaged','expired') NOT NULL,
  `quantity_affected` decimal(12,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `health_checks_stock_id_foreign` (`stock_id`),
  CONSTRAINT `health_checks_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `health_checks`
--

LOCK TABLES `health_checks` WRITE;
/*!40000 ALTER TABLE `health_checks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `health_checks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `inventory_adjustments`
--

DROP TABLE IF EXISTS `inventory_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_number` varchar(255) NOT NULL,
  `product_id` char(36) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `quantity_before` decimal(15,4) NOT NULL,
  `quantity_change` decimal(15,4) NOT NULL,
  `quantity_after` decimal(15,4) NOT NULL,
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `cost_impact` decimal(15,2) DEFAULT NULL,
  `adjustment_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_adjustments_adjustment_number_unique` (`adjustment_number`),
  KEY `inventory_adjustments_product_id_foreign` (`product_id`),
  KEY `inventory_adjustments_branch_id_foreign` (`branch_id`),
  KEY `inventory_adjustments_approved_by_foreign` (`approved_by`),
  KEY `inventory_adjustments_created_by_foreign` (`created_by`),
  KEY `inventory_adjustments_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `inventory_adjustments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `inventory_adjustments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `inventory_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `inventory_adjustments_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `inventory_adjustments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_adjustments`
--

LOCK TABLES `inventory_adjustments` WRITE;
/*!40000 ALTER TABLE `inventory_adjustments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `inventory_adjustments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `inventory_kit_components`
--

DROP TABLE IF EXISTS `inventory_kit_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_kit_components` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inventory_kit_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_kit_components_inventory_kit_id_foreign` (`inventory_kit_id`),
  KEY `inventory_kit_components_product_id_foreign` (`product_id`),
  CONSTRAINT `inventory_kit_components_inventory_kit_id_foreign` FOREIGN KEY (`inventory_kit_id`) REFERENCES `inventory_kits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_kit_components_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_kit_components`
--

LOCK TABLES `inventory_kit_components` WRITE;
/*!40000 ALTER TABLE `inventory_kit_components` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `inventory_kit_components` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `inventory_kits`
--

DROP TABLE IF EXISTS `inventory_kits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_kits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `kit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_kits_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_kits`
--

LOCK TABLES `inventory_kits` WRITE;
/*!40000 ALTER TABLE `inventory_kits` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `inventory_kits` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `item_dispatches`
--

DROP TABLE IF EXISTS `item_dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_dispatches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `dispatched_by_id` char(36) NOT NULL,
  `dispatched_by_type` varchar(255) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `received_by_id` char(36) DEFAULT NULL,
  `received_by_type` varchar(255) DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` enum('grams','kg','liters','ml','pcs','units','bags','cartons') NOT NULL,
  `dispatch_time` timestamp NOT NULL,
  `received_time` timestamp NULL DEFAULT NULL,
  `shift` enum('morning','afternoon','night') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_dispatches_request_id_foreign` (`request_id`),
  KEY `item_dispatches_item_id_foreign` (`item_id`),
  KEY `item_dispatches_branch_id_foreign` (`branch_id`),
  CONSTRAINT `item_dispatches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_dispatches_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `item_dispatches_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `item_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_dispatches`
--

LOCK TABLES `item_dispatches` WRITE;
/*!40000 ALTER TABLE `item_dispatches` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `item_dispatches` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `item_request_details`
--

DROP TABLE IF EXISTS `item_request_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_request_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity_requested` decimal(12,2) NOT NULL,
  `quantity_approved` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity_dispatched` decimal(12,2) NOT NULL DEFAULT 0.00,
  `uom_id` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_request_details_request_id_foreign` (`request_id`),
  KEY `item_request_details_item_id_foreign` (`item_id`),
  KEY `item_request_details_uom_id_foreign` (`uom_id`),
  CONSTRAINT `item_request_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `item_request_details_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `item_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_request_details_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `units_of_measure` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_request_details`
--

LOCK TABLES `item_request_details` WRITE;
/*!40000 ALTER TABLE `item_request_details` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `item_request_details` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `item_requests`
--

DROP TABLE IF EXISTS `item_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `requested_by_type` varchar(255) NOT NULL,
  `requested_by_id` char(36) NOT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `approved_by_id` char(36) DEFAULT NULL,
  `cancelled_by_type` varchar(255) DEFAULT NULL,
  `cancelled_by_id` char(36) DEFAULT NULL,
  `dispatched_by_type` varchar(255) DEFAULT NULL,
  `dispatched_by_id` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `dispatched_at` timestamp NULL DEFAULT NULL,
  `request_number` varchar(255) NOT NULL,
  `request_date` date NOT NULL,
  `shift` enum('morning','afternoon') DEFAULT NULL,
  `status` enum('pending','approved','partially_dispatched','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_requests_request_number_unique` (`request_number`),
  KEY `item_requests_branch_id_foreign` (`branch_id`),
  KEY `item_requests_department_id_foreign` (`department_id`),
  KEY `item_requests_requested_by_type_requested_by_id_index` (`requested_by_type`,`requested_by_id`),
  KEY `item_requests_approved_by_type_approved_by_id_index` (`approved_by_type`,`approved_by_id`),
  KEY `item_requests_cancelled_by_type_cancelled_by_id_index` (`cancelled_by_type`,`cancelled_by_id`),
  KEY `item_requests_dispatched_by_type_dispatched_by_id_index` (`dispatched_by_type`,`dispatched_by_id`),
  CONSTRAINT `item_requests_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_requests_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_requests`
--

LOCK TABLES `item_requests` WRITE;
/*!40000 ALTER TABLE `item_requests` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `item_requests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `category` enum('raw_material','packaging','consumable','equipment') NOT NULL,
  `uom_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `reorder_level` decimal(10,2) DEFAULT NULL,
  `max_stock_level` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_sku_unique` (`sku`),
  KEY `items_branch_id_foreign` (`branch_id`),
  KEY `items_uom_id_foreign` (`uom_id`),
  CONSTRAINT `items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `units_of_measure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `items` VALUES
(1,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Sugar - White Granulated','CAL-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(2,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Flour - All Purpose','CAL-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(3,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Cocoa Powder - Premium Dark','CAL-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(4,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Butter - Salted','CAL-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(5,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Eggs - Large Grade A','CAL-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(6,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Vanilla Extract - Pure','CAL-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(7,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Chocolate Chips - Dark','CAL-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(8,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Milk - Fresh Whole','CAL-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(9,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Cream - Heavy Whipping','CAL-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-02-02 12:46:44','2026-02-02 12:46:44'),
(10,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Yeast - Active Dry','CAL-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(11,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Vegetable Oil - Cooking','CAL-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(12,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Salt - Table Salt','CAL-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(13,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Cake Boxes - 10 inch','CAL-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(14,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Pastry Boxes - Small','CAL-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(15,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Paper Bags - Brown','CAL-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(16,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Plastic Food Containers','CAL-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(17,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Dishwashing Liquid - Industrial','CAL-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(18,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Paper Towels - Kitchen Roll','CAL-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(19,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Garbage Bags - Large','CAL-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(20,'019c1e63-b2e7-70f2-81ab-c6347efd07f0','Mixing Bowls - Stainless Steel','CAL-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(21,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Sugar - White Granulated','PHC-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(22,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Flour - All Purpose','PHC-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(23,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Cocoa Powder - Premium Dark','PHC-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(24,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Butter - Salted','PHC-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(25,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Eggs - Large Grade A','PHC-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(26,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Vanilla Extract - Pure','PHC-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(27,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Chocolate Chips - Dark','PHC-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(28,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Milk - Fresh Whole','PHC-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(29,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Cream - Heavy Whipping','PHC-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(30,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Yeast - Active Dry','PHC-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(31,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Vegetable Oil - Cooking','PHC-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(32,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Salt - Table Salt','PHC-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(33,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Cake Boxes - 10 inch','PHC-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(34,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Pastry Boxes - Small','PHC-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(35,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Paper Bags - Brown','PHC-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(36,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Plastic Food Containers','PHC-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(37,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Dishwashing Liquid - Industrial','PHC-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(38,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Paper Towels - Kitchen Roll','PHC-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(39,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Garbage Bags - Large','PHC-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(40,'019c1e63-b2f6-729f-bd59-bbe4340497fe','Mixing Bowls - Stainless Steel','PHC-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(41,'019c1e63-b300-7276-abd5-9a31664cea89','Sugar - White Granulated','LAG-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(42,'019c1e63-b300-7276-abd5-9a31664cea89','Flour - All Purpose','LAG-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(43,'019c1e63-b300-7276-abd5-9a31664cea89','Cocoa Powder - Premium Dark','LAG-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(44,'019c1e63-b300-7276-abd5-9a31664cea89','Butter - Salted','LAG-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(45,'019c1e63-b300-7276-abd5-9a31664cea89','Eggs - Large Grade A','LAG-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(46,'019c1e63-b300-7276-abd5-9a31664cea89','Vanilla Extract - Pure','LAG-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(47,'019c1e63-b300-7276-abd5-9a31664cea89','Chocolate Chips - Dark','LAG-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(48,'019c1e63-b300-7276-abd5-9a31664cea89','Milk - Fresh Whole','LAG-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(49,'019c1e63-b300-7276-abd5-9a31664cea89','Cream - Heavy Whipping','LAG-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(50,'019c1e63-b300-7276-abd5-9a31664cea89','Yeast - Active Dry','LAG-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(51,'019c1e63-b300-7276-abd5-9a31664cea89','Vegetable Oil - Cooking','LAG-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(52,'019c1e63-b300-7276-abd5-9a31664cea89','Salt - Table Salt','LAG-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(53,'019c1e63-b300-7276-abd5-9a31664cea89','Cake Boxes - 10 inch','LAG-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(54,'019c1e63-b300-7276-abd5-9a31664cea89','Pastry Boxes - Small','LAG-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(55,'019c1e63-b300-7276-abd5-9a31664cea89','Paper Bags - Brown','LAG-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(56,'019c1e63-b300-7276-abd5-9a31664cea89','Plastic Food Containers','LAG-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(57,'019c1e63-b300-7276-abd5-9a31664cea89','Dishwashing Liquid - Industrial','LAG-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(58,'019c1e63-b300-7276-abd5-9a31664cea89','Paper Towels - Kitchen Roll','LAG-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(59,'019c1e63-b300-7276-abd5-9a31664cea89','Garbage Bags - Large','LAG-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(60,'019c1e63-b300-7276-abd5-9a31664cea89','Mixing Bowls - Stainless Steel','LAG-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(61,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Sugar - White Granulated','ABJ-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(62,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Flour - All Purpose','ABJ-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(63,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Cocoa Powder - Premium Dark','ABJ-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(64,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Butter - Salted','ABJ-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(65,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Eggs - Large Grade A','ABJ-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(66,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Vanilla Extract - Pure','ABJ-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(67,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Chocolate Chips - Dark','ABJ-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(68,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Milk - Fresh Whole','ABJ-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(69,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Cream - Heavy Whipping','ABJ-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(70,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Yeast - Active Dry','ABJ-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(71,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Vegetable Oil - Cooking','ABJ-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(72,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Salt - Table Salt','ABJ-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(73,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Cake Boxes - 10 inch','ABJ-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(74,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Pastry Boxes - Small','ABJ-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(75,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Paper Bags - Brown','ABJ-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(76,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Plastic Food Containers','ABJ-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(77,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Dishwashing Liquid - Industrial','ABJ-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(78,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Paper Towels - Kitchen Roll','ABJ-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(79,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Garbage Bags - Large','ABJ-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(80,'019c1e63-b321-71ba-a153-c8fc9e31bcab','Mixing Bowls - Stainless Steel','ABJ-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(81,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Sugar - White Granulated','ENU-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(82,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Flour - All Purpose','ENU-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(83,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Cocoa Powder - Premium Dark','ENU-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(84,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Butter - Salted','ENU-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(85,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Eggs - Large Grade A','ENU-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(86,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Vanilla Extract - Pure','ENU-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(87,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Chocolate Chips - Dark','ENU-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(88,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Milk - Fresh Whole','ENU-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(89,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Cream - Heavy Whipping','ENU-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(90,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Yeast - Active Dry','ENU-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(91,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Vegetable Oil - Cooking','ENU-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(92,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Salt - Table Salt','ENU-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(93,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Cake Boxes - 10 inch','ENU-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(94,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Pastry Boxes - Small','ENU-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(95,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Paper Bags - Brown','ENU-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(96,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Plastic Food Containers','ENU-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(97,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Dishwashing Liquid - Industrial','ENU-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(98,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Paper Towels - Kitchen Roll','ENU-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(99,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Garbage Bags - Large','ENU-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(100,'019c1e63-b32d-72bd-847a-c1c17e7b8674','Mixing Bowls - Stainless Steel','ENU-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-02-02 12:46:49','2026-02-02 12:46:49');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `late_payment_fees`
--

DROP TABLE IF EXISTS `late_payment_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `late_payment_fees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `fee_amount` decimal(15,2) NOT NULL,
  `fee_rate` decimal(5,2) DEFAULT NULL,
  `fee_date` date NOT NULL,
  `days_overdue` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `late_payment_fees_sale_id_foreign` (`sale_id`),
  KEY `late_payment_fees_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `late_payment_fees_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `late_payment_fees_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `late_payment_fees`
--

LOCK TABLES `late_payment_fees` WRITE;
/*!40000 ALTER TABLE `late_payment_fees` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `late_payment_fees` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `leave_applications`
--

DROP TABLE IF EXISTS `leave_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_number` varchar(255) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(8,2) NOT NULL,
  `reason` text NOT NULL,
  `emergency_contact` text DEFAULT NULL,
  `supporting_document` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejected_by_id` char(36) DEFAULT NULL,
  `rejected_by_type` varchar(255) DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by_id` char(36) DEFAULT NULL,
  `cancelled_by_type` varchar(255) DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_applications_application_number_unique` (`application_number`),
  KEY `leave_applications_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_applications_employee_id_foreign` (`employee_id`),
  KEY `leave_applications_branch_id_foreign` (`branch_id`),
  CONSTRAINT `leave_applications_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_applications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_applications_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_applications`
--

LOCK TABLES `leave_applications` WRITE;
/*!40000 ALTER TABLE `leave_applications` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `leave_applications` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `default_days_per_year` int(11) NOT NULL DEFAULT 0,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1,
  `requires_document` tinyint(1) NOT NULL DEFAULT 0,
  `max_consecutive_days` int(11) DEFAULT NULL,
  `min_notice_days` int(11) NOT NULL DEFAULT 0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `color` varchar(255) NOT NULL DEFAULT '#3b82f6',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_types_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `leave_types` VALUES
(1,'Annual Leave','ANNUAL','Yearly vacation leave for rest and recreation',21,1,0,14,7,1,1,'#3b82f6','2026-02-02 12:46:15','2026-02-02 12:46:15'),
(2,'Sick Leave','SICK','Medical leave for illness or injury',10,1,1,NULL,0,1,1,'#ef4444','2026-02-02 12:46:15','2026-02-02 12:46:15'),
(3,'Emergency Leave','EMERGENCY','Urgent personal or family emergencies',5,1,0,3,0,1,1,'#f59e0b','2026-02-02 12:46:15','2026-02-02 12:46:15'),
(4,'Maternity Leave','MATERNITY','Leave for childbirth and post-natal care',90,1,1,NULL,30,1,1,'#ec4899','2026-02-02 12:46:15','2026-02-02 12:46:15'),
(5,'Paternity Leave','PATERNITY','Leave for fathers following childbirth',7,1,1,NULL,7,1,1,'#6366f1','2026-02-02 12:46:15','2026-02-02 12:46:15'),
(6,'Bereavement Leave','BEREAVEMENT','Leave for death of a family member',3,1,0,3,0,1,1,'#6b7280','2026-02-02 12:46:15','2026-02-02 12:46:15'),
(7,'Study Leave','STUDY','Leave for educational purposes or examinations',5,1,1,5,14,0,1,'#8b5cf6','2026-02-02 12:46:15','2026-02-02 12:46:15'),
(8,'Unpaid Leave','UNPAID','Additional leave without pay',0,1,0,NULL,14,0,1,'#64748b','2026-02-02 12:46:15','2026-02-02 12:46:15');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2025_09_22_145432_add_two_factor_columns_to_users_table',1),
(5,'2025_10_02_163836_create_branches_table',1),
(6,'2025_10_02_220032_create_department_categories_table',1),
(7,'2025_10_04_053818_create_departments_table',1),
(8,'2025_10_04_053818_create_employees_table',1),
(9,'2025_10_05_192144_add_password_to_employees_table',1),
(10,'2025_10_06_051232_add_auth_columns_to_employees_table',1),
(11,'2025_10_07_030703_create_permission_tables',1),
(12,'2025_10_08_223243_add_position_and_manager_to_employees_table',1),
(13,'2025_10_09_012549_create_items_table',1),
(14,'2025_10_09_012613_create_purchases_table',1),
(15,'2025_10_09_012614_create_purchase_items_table',1),
(16,'2025_10_09_012615_create_stocks_table',1),
(17,'2025_10_09_012617_create_stock_movements_table',1),
(18,'2025_10_09_012619_create_item_requests_table',1),
(19,'2025_10_09_012621_create_item_request_details_table',1),
(20,'2025_10_09_012622_create_item_dispatches_table',1),
(21,'2025_10_09_012623_create_stock_takes_table',1),
(22,'2025_10_09_012626_create_stock_take_details_table',1),
(23,'2025_10_09_012627_create_health_checks_table',1),
(24,'2025_10_10_000001_add_shift_and_department_to_stock_movements',1),
(25,'2025_10_10_000002_add_analytics_indexes',1),
(26,'2025_10_13_050139_create_clock_ins_table',1),
(27,'2025_10_15_053301_create_recipes_table',1),
(28,'2025_10_15_053356_create_recipe_ingredients_table',1),
(29,'2025_10_15_053520_create_shifts_table',1),
(30,'2025_10_15_053537_create_daily_produces_table',1),
(31,'2025_10_15_054442_create_production_records_table',1),
(32,'2025_10_15_055019_create_production_requests_table',1),
(33,'2025_10_15_055201_create_call_backs_table',1),
(34,'2025_10_15_055221_create_raw_material_utilizations_table',1),
(35,'2025_10_16_074716_create_product_types_table',1),
(36,'2025_10_16_074717_create_products_table',1),
(37,'2025_10_18_075757_add_branch_id_to_products_table',1),
(38,'2025_10_18_194413_add_cost_and_waste_fields_to_recipe_ingredients_table',1),
(39,'2025_10_19_030711_add_status_to_daily_produces_table',1),
(40,'2025_10_19_083910_create_employee_shifts_table',1),
(41,'2025_10_20_063309_create_sales_hifts_table',1),
(42,'2025_10_20_063310_create_product_stocks_table',1),
(43,'2025_10_20_063352_create_sales_table',1),
(44,'2025_10_20_063409_create_sale_items_table',1),
(45,'2025_10_20_063429_create_payments_table',1),
(46,'2025_10_21_073703_create_product_callbacks_table',1),
(47,'2025_10_22_094919_create_approved_items_table',1),
(48,'2025_10_23_100000_create_expiry_confirmations_table',1),
(49,'2025_10_23_120000_create_product_dispatches_table',1),
(50,'2025_10_24_200648_add_batch_tracking_to_production_records_table',1),
(51,'2025_10_25_045338_make_sales_shift_id_nullable_in_product_stocks_table',1),
(52,'2025_10_25_073857_create_receipts_table',1),
(53,'2025_10_27_000814_create_global_business_configurations_table',1),
(54,'2025_10_27_000816_create_global_currency_localizations_table',1),
(55,'2025_10_27_000818_create_global_branch_management_table',1),
(56,'2025_10_27_000820_create_global_inventory_management_table',1),
(57,'2025_10_27_000821_create_branch_inventory_management_table',1),
(58,'2025_10_27_000823_create_global_employee_management_table',1),
(59,'2025_10_27_000824_create_global_pos_configurations_table',1),
(60,'2025_10_27_000827_create_global_accounting_cashes_table',1),
(61,'2025_10_27_000829_create_global_customer_supplier_management_table',1),
(62,'2025_10_27_000831_create_global_reports_analytics_table',1),
(63,'2025_10_27_000833_create_global_security_accesses_table',1),
(64,'2025_10_27_000835_create_global_notifications_alerts_table',1),
(65,'2025_10_27_000837_create_audit_logs_table',1),
(66,'2025_10_27_000838_create_approval_requests_table',1),
(67,'2025_10_27_042254_remove_business_type_from_global_business_configurations',1),
(68,'2025_10_27_042747_remove_columns_from_global_business_configurations',1),
(69,'2025_11_01_082233_create_department_pages_table',1),
(70,'2025_11_01_082403_add_slug_to_departments_table',1),
(71,'2025_11_02_021715_create_product_dispatch_callbacks_table',1),
(72,'2025_11_02_104432_create_production_callbacks_table',1),
(73,'2025_11_02_110718_add_sales_shift_and_received_quantity_to_product_dispatches_table',1),
(74,'2025_11_06_055946_create_tables_table',1),
(75,'2025_11_06_055949_add_table_id_to_sales_table',1),
(76,'2025_11_06_055953_add_table_management_settings_to_branches_table',1),
(77,'2025_11_06_063045_add_table_management_to_departments_table',1),
(78,'2025_11_06_063047_add_department_id_to_sale_items_table',1),
(79,'2025_11_06_063247_add_department_id_to_tables_table',1),
(80,'2025_11_06_100524_create_department_product_table',1),
(81,'2025_11_06_102006_add_product_id_to_recipes_table',1),
(82,'2025_11_06_175546_make_product_dispatch_id_nullable_in_product_dispatch_callbacks_table',1),
(83,'2025_11_07_175717_add_sales_department_id_to_product_dispatches_table',1),
(84,'2025_11_07_183133_remove_redundant_yield_fields_from_products_table',1),
(85,'2025_11_09_053144_create_leave_types_table',1),
(86,'2025_11_09_053147_create_employee_leave_balances_table',1),
(87,'2025_11_09_053149_create_leave_applications_table',1),
(88,'2025_11_09_053151_create_probation_reviews_table',1),
(89,'2025_11_09_053154_create_salary_history_table',1),
(90,'2025_11_09_053507_create_employee_stepouts_table',1),
(91,'2025_11_09_103341_create_employee_leave_allocations_table',1),
(92,'2025_11_11_055910_add_branch_id_to_leave_application_table',1),
(93,'2025_11_14_000001_create_department_reports_table',1),
(94,'2025_11_14_000002_create_compiled_reports_table',1),
(95,'2025_11_14_000003_create_report_schedules_table',1),
(96,'2025_11_14_000004_create_report_distributions_table',1),
(97,'2025_11_14_000005_create_report_templates_table',1),
(98,'2025_11_14_000006_create_compiled_report_department_report_table',1),
(99,'2025_11_14_225458_create_product_transfers_table',1),
(100,'2025_11_15_061641_add_last_accessed_branch_id_to_users_table',1),
(101,'2025_11_15_170737_add_department_id_to_products_table',1),
(102,'2025_11_15_173556_add_is_siper_admin_request_to_production_requests_table',1),
(103,'2025_11_15_174249_add_is_super_admin_to_employees_table',1),
(104,'2025_11_24_032703_update_audit_logs_table_add_missing_columns',1),
(105,'2025_11_24_032823_update_audit_logs_setting_type_nullable',1),
(106,'2025_11_24_033049_alter_audit_logs_details_nullable',1),
(107,'2025_11_24_034645_create_approval_audit_requests_table',1),
(108,'2025_11_24_035053_add_branch_id_to_approval_audit_requests_table',1),
(109,'2025_12_02_000001_fix_audit_logs_auditable_id_column',1),
(110,'2025_12_02_000002_increase_audit_logs_action_column_size',1),
(111,'2025_12_02_100000_recreate_approval_requests_table',1),
(112,'2025_12_03_004522_make_audit_log_description_string_text',1),
(113,'2025_12_04_103748_drop_currency_and_exchange_rate_from_purchases',1),
(114,'2025_12_04_110900_add_status_to_purchases_table',1),
(115,'2025_12_04_111720_create_purchase_approval_requests_table',1),
(116,'2025_12_07_000002_add_callback_indexes',1),
(117,'2025_12_09_185202_add_yield_fields_to_recipes_table',1),
(118,'2025_12_10_215015_update_recipes_table_remove_ambiguous_columns',1),
(119,'2025_12_10_215656_create_units_of_measure_table',1),
(120,'2025_12_10_215758_update_tables_use_uom_id',1),
(121,'2025_12_12_000001_add_protection_to_roles',1),
(122,'2025_12_12_000002_create_role_permission_audit_logs_table',1),
(123,'2025_12_13_100001_create_gl_accounts_table',1),
(124,'2025_12_13_100002_create_accounting_periods_table',1),
(125,'2025_12_13_100003_create_gl_entries_table',1),
(126,'2025_12_13_100004_create_bank_accounts_table',1),
(127,'2025_12_13_100005_create_daily_bank_positions_table',1),
(128,'2025_12_13_100006_create_daily_bank_transactions_table',1),
(129,'2025_12_13_100007_create_cash_positions_table',1),
(130,'2025_12_15_000001_add_reconciliation_fields_to_bank_tables',1),
(131,'2025_12_15_000001_add_unit_cost_to_production_records',1),
(132,'2025_12_15_000002_create_bank_reconciliations_tables',1),
(133,'2025_12_15_000002_ensure_accounting_fields_in_sales_and_payments',1),
(134,'2025_12_15_000003_link_sales_payments_to_bank_accounts',1),
(135,'2025_12_15_120000_add_gl_posting_status_to_transaction_tables',1),
(136,'2025_12_15_120001_add_branch_id_to_tables',1),
(137,'2025_12_15_181510_alter_entry_type_in_gl_entries_table',1),
(138,'2025_12_15_233546_add_branch_and_active_to_users_table',1),
(139,'2025_12_16_000001_add_workflow_metadata_to_shifts',1),
(140,'2025_12_16_000002_add_stock_workflow_fields',1),
(141,'2025_12_16_000003_create_workflow_audit_logs_table',1),
(142,'2025_12_16_073857_add_columns_to_users_table',1),
(143,'2025_12_16_164052_add_employee_fields_to_users_table',1),
(144,'2025_12_17_041526_create_shift_configurations_table',1),
(145,'2025_12_17_041725_add_auto_clock_out_fields_to_shifts_table',1),
(146,'2025_12_17_054438_add_description_and_display_order_to_roles_table',1),
(147,'2025_12_20_000001_add_progress_fields_to_existing_tables',1),
(148,'2025_12_20_000002_create_production_milestones_and_alerts_tables',1),
(149,'2025_12_20_000004_add_quality_fields_to_production_records',1),
(150,'2025_12_22_173350_make_item_request_morph_columns_nullable',1),
(151,'2025_12_23_100822_add_production_record_id_to_product_dispatches_table',1),
(152,'2025_12_23_181627_create_appraisal_system_tables',1),
(153,'2025_12_24_092911_make_appraisal_cycle_and_template_nullable_in_appraisals_table',1),
(154,'2025_12_26_220705_alter_appraisal_templates_created_by_column',1),
(155,'2025_12_26_225338_drop_appraisal_templates_table',1),
(156,'2025_12_27_061851_create_appraisals_table',1),
(157,'2025_12_27_120000_create_sales_to_production_workflow',1),
(158,'2025_12_27_141037_create_sales_to_production_workflow',1),
(159,'2025_12_28_235959_create_suppliers_table',1),
(160,'2025_12_28_235960_create_supplier_contacts_table',1),
(161,'2025_12_28_235961_create_supplier_terms_table',1),
(162,'2025_12_28_235962_create_supplier_bank_accounts_table',1),
(163,'2025_12_28_235963_create_supplier_documents_table',1),
(164,'2025_12_28_235964_create_supplier_performance_history_table',1),
(165,'2025_12_28_235965_add_supplier_id_to_purchases_table',1),
(166,'2025_12_28_add_production_request_to_dispatches',1),
(167,'2025_12_29_000001_create_manager_io_accounting_tables',1),
(168,'2025_12_29_000002_make_item_request_id_nullable_in_production_requests',1),
(169,'2025_12_29_000003_fix_accounting_periods_uuid_fields',1),
(170,'2025_12_29_000004_fix_gl_entries_uuid_fields',1),
(171,'2025_12_29_075219_make_shift_id_nullable_in_production_requests_for_pos',1),
(172,'2025_12_29_113100_add_approved_to_production_requests_status',1),
(173,'2025_12_30_000000_sync_migrations_table',1),
(174,'2025_12_30_000001_update_product_dispatch_status_enum',1),
(175,'2025_12_30_041253_fix_production_requests_nullable_columns',1),
(176,'2025_12_30_041801_make_approved_by_columns_nullable_in_item_requests',1),
(177,'2025_12_30_042000_make_morph_columns_nullable_in_item_requests',1),
(178,'2025_12_30_042518_replace_uom_enum_with_foreign_key_in_item_request_details',1),
(179,'2025_12_30_044044_increase_audit_logs_description_column_size',1),
(180,'2026_01_02_131657_fix_role_permission_audit_logs_user_id_column',1),
(181,'2026_01_08_000001_create_role_category_constraints_table',1),
(182,'2026_01_08_000002_extend_departments_table',1),
(183,'2026_01_08_152030_add_department_requirement_to_users_table',1),
(184,'2026_01_08_174620_add_level_to_roles_and_create_simplified_roles',1),
(185,'2026_01_08_175511_create_role_migration_log_table',1),
(186,'2026_01_09_053531_add_polymorphic_columns_to_gl_entries_table',1),
(187,'2026_01_19_154150_update_purchase_items_uom_to_string',1),
(188,'2026_02_01_040248_remove_kitchen_module_from_department_pages',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
INSERT INTO `model_has_roles` VALUES
(70,'user','00418083-571e-396c-85ae-733e3120f146'),
(89,'user','00418083-571e-396c-85ae-733e3120f146'),
(64,'user','019c1e63-b272-7201-9b33-e6a9a238db71'),
(89,'user','02166c38-5b52-3f93-b327-f59ca44aad8e'),
(91,'user','02166c38-5b52-3f93-b327-f59ca44aad8e'),
(71,'user','0368494f-608e-3afc-bdfd-c95bd55ed739'),
(68,'user','0529d41f-2018-3075-84ef-cf6c30c0180e'),
(69,'user','076e56ba-50c7-347f-b8a5-b4e39464ebb2'),
(67,'user','08aa1e7f-4ae9-37a4-8c06-ad3a64103d5a'),
(77,'user','139b21c2-9ef9-3834-ab45-74dc6a54ed0c'),
(78,'user','17d66ef3-95ca-3e42-8a38-10435e89111f'),
(79,'user','1aed8ef5-0082-3b97-843a-1c6b2ef2a02e'),
(80,'user','1bab56ab-b45e-3db7-a01f-9632a0578755'),
(73,'user','1d706d00-4612-3564-b6e8-b03dbb0d720b'),
(81,'user','1d74937d-75eb-32f0-adb7-ad3e20dbaee9'),
(82,'user','204cd648-21ad-3e5f-90af-2b0d5279ecaa'),
(86,'user','238e3565-9304-3049-893e-8b547f70a940'),
(87,'user','245b1a46-456b-3670-a0e9-7ee3b3682f42'),
(88,'user','261d373d-1139-3313-a728-e0de0b1d320b'),
(89,'user','26d46859-baa8-3aa7-89bd-4e60fe9b5892'),
(89,'user','272eea60-73fc-3bab-afd3-95d853086d39'),
(89,'user','2fa819ad-148a-363d-8617-926c9f427fba'),
(89,'user','34bd1150-1566-333d-9302-21ac62c17eaa'),
(89,'user','3644514b-03df-3da6-9707-7d7685bb67bb'),
(89,'user','38afa22b-f11c-3e78-87de-130edc2f9bba'),
(89,'user','3a827a3e-e440-3e82-ae15-0c145475040b'),
(89,'user','3d6ba483-9200-3a8b-8128-afcd03af29e7'),
(89,'user','3d989def-3a3f-38b7-9064-d33d53e56497'),
(89,'user','40ad6863-710c-32d1-aebb-579fcf494961'),
(89,'user','40ec8cc7-3fa0-37b0-b074-ac782c99c385'),
(89,'user','420528dd-e794-3f64-9ed2-2707d27efa20'),
(89,'user','44422fd8-eab6-3afa-8683-3530874bf82d'),
(89,'user','45b5b9ed-8865-3595-8194-a6087387667c'),
(89,'user','464bad5d-34c0-34f6-8c2f-7828c8b9bb62'),
(89,'user','4a4d8927-f11a-33ae-97d5-19b712cf318b'),
(89,'user','4db015fa-6d1f-3317-beed-8b0276bc26e6'),
(89,'user','4e2ab46d-0dd6-3c35-a602-1db1f1d3d82c'),
(89,'user','4f8c7c83-0cfb-3462-8f82-a7b7d2b408d6'),
(89,'user','51062e63-1529-38d6-961d-18a9f579bad0'),
(89,'user','53ccec4b-0a09-3be4-8207-ff667e1887df'),
(89,'user','616ae92b-d9b8-3c3d-a22e-92223b27e9c3'),
(89,'user','616b24a8-a1c7-34db-a35f-67968fcbae0d'),
(89,'user','678deb6e-1e92-3623-b038-28f7ccdf6172'),
(89,'user','6d86654a-0152-36e1-a587-a71caa84973a'),
(89,'user','6ec92775-5d45-33ab-b271-8c63ef5bfb22'),
(89,'user','6fdb1848-5d9e-3cd2-8379-206a33133a26'),
(89,'user','741c8f01-162e-39ad-9286-5388355d377e'),
(89,'user','772f94c2-02fa-3034-ad10-fc9c5329fc59'),
(89,'user','77ff5796-a534-32c6-aa9c-d50189651422'),
(89,'user','7c9fb706-83ad-3801-8f4a-95c3ea75bc52'),
(89,'user','7d4e488a-6348-325a-a163-2a11d71ff672'),
(89,'user','7eec6bd2-d402-3c80-917f-819af4f05f91'),
(89,'user','7f6b3885-1069-3265-a471-38768fecbbf3'),
(89,'user','7f8f572d-85a1-3d53-b212-fc6c5b159653'),
(89,'user','800a6fd1-7366-3196-ab78-74b781a1fe5b'),
(89,'user','80170dd7-7a24-3ff7-a0de-cb061542eaff'),
(89,'user','81ff45b4-8087-3328-959f-a357e64ca963'),
(89,'user','84ded2e2-2100-3c19-9253-1cbc35ed3d77'),
(89,'user','862994cc-393b-37d4-965d-bd74be6ffee2'),
(89,'user','892bd8f9-23cb-3b26-b44b-41cad260ea5f'),
(89,'user','8d1c2dbe-1e9b-35b9-9686-9c7636f801bc'),
(89,'user','8ff5d862-163e-3260-b1f9-9622caf4c730'),
(89,'user','903f0a97-fec4-30ef-8054-20acbc202356'),
(89,'user','9120f862-b5f6-3576-8967-1bb3124957e3'),
(89,'user','9155bee1-4cde-3051-b492-e8f71ea161cb'),
(89,'user','92e5b61f-0df8-3c47-a9b4-0f628af3bd3b'),
(89,'user','933001bd-35ce-3253-bfe8-795c1a9b9fa1'),
(89,'user','973607ee-523b-32ce-a8fb-e65085bc7b35'),
(89,'user','9ba216ac-fa68-36f8-96ed-59b7ba33f82a'),
(89,'user','9be27a9a-0b32-3b51-b289-c279cabb6fe0'),
(89,'user','a118e86d-ab6b-3cb2-9f17-2800a47c319c'),
(89,'user','a332ef26-2af7-314b-b314-ad3727f84699'),
(89,'user','a6c75990-2166-3e02-b0b0-6fa78575eb87'),
(89,'user','a8bd8824-4ad5-3693-9c47-10d8ef22ee01'),
(89,'user','a927f87c-901f-361c-b59c-2c1c6ef57dd1'),
(89,'user','aada24bd-3632-3379-afcb-b052f74ecb2a'),
(89,'user','ab2d4f51-3ea1-3643-a039-793bce8bc173'),
(89,'user','ab38e16a-37d6-3ef2-8ab5-2569ced9e2e1'),
(89,'user','abf80b71-a68c-340a-ba65-cd24f8a81918'),
(89,'user','ac146832-5af0-3f63-900b-43c34c127717'),
(89,'user','b41b5850-7463-34f7-a180-a78b3f25364e'),
(89,'user','b4712989-2497-3bfa-a0e4-4f6f66f9cc6e'),
(89,'user','b4ea499f-42fb-3d72-95e9-94b4d220a91d'),
(89,'user','bbb93c6a-f068-3e5c-93ef-4e5c03e80b2b'),
(89,'user','bf16d7d7-9032-386a-bfec-24957cc698b3'),
(89,'user','c50afcf1-2591-339a-976a-6f214a7b55bb'),
(89,'user','cb746d53-40d4-33da-97d3-fdb0f672bf97'),
(89,'user','cec5735d-51f1-361b-ac1c-4ab643e9a1f4'),
(89,'user','cf5c519b-17e8-375d-a424-c64b53b7a17f'),
(89,'user','d11ec69d-355e-3292-9fab-fbbbddfec7cd'),
(89,'user','d5773999-b7ba-3690-8c1f-5b272ac1819e'),
(89,'user','d9240843-ecf7-34ec-842f-9de2107884bd'),
(89,'user','d9539d9f-f81b-36ea-9e98-fb6b18c697d1'),
(89,'user','dd7e65c1-c178-3565-99f2-70d2ed250373'),
(89,'user','de6a6d49-98d3-3989-a192-ec3a5e8c8a41'),
(89,'user','decf4493-1c81-38ec-86cd-81d3ca9ea94f'),
(89,'user','e0df346b-3538-36ca-807b-698db170bad0'),
(89,'user','e3c6337f-4c3e-3c47-9a20-a57e32fe0c8a'),
(89,'user','e99cba90-91a4-3e94-ac64-279fa397d5eb'),
(89,'user','ec9c3044-b335-3f6f-abc8-126ff94c9062'),
(89,'user','ee9d5f19-bde9-35bc-8868-f35492db7f2d'),
(89,'user','ef8a91de-2d9d-3bd1-b7ac-6b97effba98d'),
(89,'user','f0eb9223-685d-3512-911d-1e63c001de9c'),
(89,'user','f15467cd-28a3-3dcc-90a7-7962f96ca108'),
(89,'user','f496d51f-b171-395f-ac4a-8896e8615229'),
(89,'user','f64488f3-4461-3522-8c8b-da481a1a9c2c'),
(89,'user','f77ac672-fb6d-3563-9d61-b55160b4f126'),
(89,'user','f92e3e38-6888-3efc-87e3-9d02bf7ddccf');
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('cash','pos','transfer','card','mobile') NOT NULL DEFAULT 'cash',
  `amount` decimal(10,2) NOT NULL,
  `bank_account_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Link to bank account for payment',
  `gl_posting_status` enum('pending','posted','failed') NOT NULL DEFAULT 'pending' COMMENT 'GL posting status for accounting integration',
  `gl_posted_at` timestamp NULL DEFAULT NULL COMMENT 'When the GL entries were posted',
  `gl_posting_error` text DEFAULT NULL COMMENT 'Error message if GL posting failed',
  `reference_number` varchar(255) DEFAULT NULL,
  `payment_time` timestamp NOT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_sale_id_foreign` (`sale_id`),
  KEY `payments_branch_id_index` (`branch_id`),
  CONSTRAINT `payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_is_protected_index` (`is_protected`)
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `permissions` VALUES
(119,'view-roles','web',0,'View all roles','system','2026-02-02 13:08:26','2026-02-02 13:08:26'),
(120,'create-roles','web',0,'Create new roles','system','2026-02-02 13:08:26','2026-02-02 13:08:26'),
(121,'edit-roles','web',0,'Edit roles','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(122,'delete-roles','web',0,'Delete roles','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(123,'assign-roles','web',0,'Assign roles to users','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(124,'view-permissions','web',0,'View all permissions','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(125,'manage-permissions','web',0,'Manage permissions','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(126,'view-branches','web',0,'View all branches','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(127,'create-branches','web',0,'Create new branches','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(128,'edit-branches','web',0,'Edit branch information','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(129,'delete-branches','web',0,'Delete branches','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(130,'manage-settings','web',0,'Manage system settings','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(131,'view-audit-logs','web',0,'View audit logs','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(132,'view-activity-logs','web',0,'View activity logs','system','2026-02-02 13:08:27','2026-02-02 13:08:27'),
(133,'manage-system','web',0,'Full system management','system','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(134,'view-employees','web',0,'View employee list','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(135,'create-employees','web',0,'Create new employees','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(136,'edit-employees','web',0,'Edit employee information','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(137,'delete-employees','web',0,'Delete employee records','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(138,'view-departments','web',0,'View departments','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(139,'create-departments','web',0,'Create departments','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(140,'edit-departments','web',0,'Edit departments','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(141,'delete-departments','web',0,'Delete departments','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(142,'manage-staff-schedule','web',0,'Manage employee schedules','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(143,'manage-leave','web',0,'Manage employee leave','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(144,'approve-leave','web',0,'Approve leave requests','hr','2026-02-02 13:08:28','2026-02-02 13:08:28'),
(145,'view-payroll','web',0,'View payroll information','hr','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(146,'manage-payroll','web',0,'Manage payroll','hr','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(147,'view-hr-reports','web',0,'View HR reports','hr','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(148,'manage-roles-assignments','web',0,'Manage employee role assignments','hr','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(149,'view-employee-details','web',0,'View detailed employee information','hr','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(150,'view-production-queue','web',0,'View production queue','production','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(151,'create-production-order','web',0,'Create production orders','production','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(152,'start-production','web',0,'Start production batches','production','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(153,'complete-production','web',0,'Complete production batches','production','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(154,'approve-production','web',0,'Approve production','production','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(155,'manage-recipes','web',0,'Create and edit recipes','production','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(156,'view-recipes','web',0,'View recipes','production','2026-02-02 13:08:29','2026-02-02 13:08:29'),
(157,'view-production-reports','web',0,'View production analytics','production','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(158,'manage-quality-control','web',0,'Manage quality control','production','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(159,'view-batch-history','web',0,'View production batch history','production','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(160,'edit-production-order','web',0,'Edit production orders','production','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(161,'cancel-production','web',0,'Cancel production orders','production','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(162,'view-production-cost','web',0,'View production costs','production','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(163,'manage-production-settings','web',0,'Manage production settings','production','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(164,'view-stock-levels','web',0,'View stock levels','inventory','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(165,'receive-stock','web',0,'Receive inventory','inventory','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(166,'transfer-stock','web',0,'Transfer stock between locations','inventory','2026-02-02 13:08:30','2026-02-02 13:08:30'),
(167,'adjust-inventory','web',0,'Adjust inventory counts','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(168,'create-purchase-order','web',0,'Create purchase orders','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(169,'approve-purchase-order','web',0,'Approve purchase orders','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(170,'view-inventory-reports','web',0,'View inventory reports','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(171,'manage-suppliers','web',0,'Full supplier management','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(172,'view-suppliers','web',0,'View suppliers list','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(173,'create-suppliers','web',0,'Create new suppliers','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(174,'edit-suppliers','web',0,'Edit supplier information','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(175,'delete-suppliers','web',0,'Delete suppliers','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(176,'view-stock-valuation','web',0,'View stock valuation','inventory','2026-02-02 13:08:31','2026-02-02 13:08:31'),
(177,'manage-stock-categories','web',0,'Manage stock categories','inventory','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(178,'view-reorder-levels','web',0,'View reorder levels','inventory','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(179,'manage-reorder-levels','web',0,'Manage reorder levels','inventory','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(180,'write-off-stock','web',0,'Write off stock items','inventory','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(181,'view-stock-history','web',0,'View stock transaction history','inventory','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(182,'manage-inventory-settings','web',0,'Manage inventory settings','inventory','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(183,'view-sales-dashboard','web',0,'Access sales dashboard','sales','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(184,'process-sale','web',0,'Process sales transactions','sales','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(185,'issue-refund','web',0,'Issue refunds','sales','2026-02-02 13:08:32','2026-02-02 13:08:32'),
(186,'view-daily-sales','web',0,'View daily sales','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(187,'close-register','web',0,'Close cash registers','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(188,'view-sales-reports','web',0,'View sales analytics','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(189,'manage-sales-discounts','web',0,'Manage sales discounts','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(190,'view-sales-transactions','web',0,'View sales transactions','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(191,'edit-sales-transactions','web',0,'Edit sales transactions','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(192,'void-sales-transactions','web',0,'Void sales transactions','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(193,'manage-payment-methods','web',0,'Manage payment methods','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(194,'view-till-records','web',0,'View till/register records','sales','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(195,'view-chart-accounts','web',0,'View chart of accounts','accounting','2026-02-02 13:08:33','2026-02-02 13:08:33'),
(196,'create-accounts','web',0,'Create general ledger accounts','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(197,'edit-accounts','web',0,'Edit general ledger accounts','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(198,'view-gl-entries','web',0,'View general ledger entries','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(199,'create-gl-entries','web',0,'Create general ledger entries','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(200,'post-gl-entries','web',0,'Post general ledger entries','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(201,'reverse-gl-entries','web',0,'Reverse general ledger entries','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(202,'view-accounting-reports','web',0,'View accounting reports','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(203,'reconcile-accounts','web',0,'Reconcile bank accounts','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(204,'manage-bank-accounts','web',0,'Manage bank accounts','accounting','2026-02-02 13:08:34','2026-02-02 13:08:34'),
(205,'view-trial-balance','web',0,'View trial balance','accounting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(206,'view-financial-statements','web',0,'View financial statements','accounting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(207,'manage-accounting-period','web',0,'Manage accounting periods','accounting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(208,'view-account-reconciliation','web',0,'View account reconciliation','accounting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(209,'view-analytics','web',0,'View analytics dashboard','reporting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(210,'view-department-reports','web',0,'View department reports','reporting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(211,'generate-reports','web',0,'Generate custom reports','reporting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(212,'export-reports','web',0,'Export reports to files','reporting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(213,'schedule-reports','web',0,'Schedule automated reports','reporting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(214,'view-dashboard','web',0,'View main dashboard','reporting','2026-02-02 13:08:35','2026-02-02 13:08:35'),
(215,'view-branch-reports','web',0,'View branch-specific reports','reporting','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(216,'view-kpi-metrics','web',0,'View KPI metrics','reporting','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(217,'export-data','web',0,'Export system data','reporting','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(218,'view-activity-timeline','web',0,'View activity timeline','reporting','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(219,'view_inventory_dashboard','web',0,'Access inventory dashboard','dashboard','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(220,'view_production_dashboard','web',0,'Access production dashboard','dashboard','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(221,'view_sales_dashboard','web',0,'Access sales dashboard','dashboard','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(222,'view_corner_store_dashboard','web',0,'Access corner store dashboard','dashboard','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(223,'view_hr_dashboard','web',0,'Access HR dashboard','dashboard','2026-02-02 13:08:36','2026-02-02 13:08:36'),
(224,'view_admin_dashboard','web',0,'Access admin dashboard','dashboard','2026-02-02 13:08:37','2026-02-02 13:08:37'),
(225,'view_super_admin_dashboard','web',0,'Access super admin dashboard','dashboard','2026-02-02 13:08:37','2026-02-02 13:08:37'),
(226,'manage_organization','web',0,'Manage organization (employees, departments)','organization','2026-02-02 13:08:37','2026-02-02 13:08:37'),
(227,'manage_roles','web',0,'Manage user roles and permissions','organization','2026-02-02 13:08:37','2026-02-02 13:08:37'),
(228,'manage_branches','web',0,'Manage branches','organization','2026-02-02 13:08:37','2026-02-02 13:08:37'),
(229,'manage_settings','web',0,'Manage system settings','organization','2026-02-02 13:08:37','2026-02-02 13:08:37'),
(230,'view_reports','web',0,'View system reports','organization','2026-02-02 13:08:37','2026-02-02 13:08:37'),
(231,'access_accounting','web',0,'Access accounting module','accounting','2026-02-02 13:08:38','2026-02-02 13:08:38'),
(232,'view_financial_reports','web',0,'View financial reports','accounting','2026-02-02 13:08:38','2026-02-02 13:08:38'),
(233,'manage_accounts','web',0,'Manage chart of accounts','accounting','2026-02-02 13:08:38','2026-02-02 13:08:38'),
(234,'manage_periods','web',0,'Manage accounting periods','accounting','2026-02-02 13:08:38','2026-02-02 13:08:38'),
(235,'create_journal_entries','web',0,'Create journal entries','accounting','2026-02-02 13:08:38','2026-02-02 13:08:38'),
(236,'reconcile_bank_accounts','web',0,'Reconcile bank accounts','accounting','2026-02-02 13:08:38','2026-02-02 13:08:38');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `probation_reviews`
--

DROP TABLE IF EXISTS `probation_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `probation_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `review_date` date NOT NULL,
  `probation_start_date` date NOT NULL,
  `probation_end_date` date NOT NULL,
  `review_type` enum('initial','midpoint','final','extension') NOT NULL DEFAULT 'initial',
  `reviewer_id` char(36) NOT NULL,
  `performance_score` int(11) DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `areas_for_improvement` text DEFAULT NULL,
  `goals_achievements` text DEFAULT NULL,
  `training_recommendations` text DEFAULT NULL,
  `overall_comments` text NOT NULL,
  `recommendation` enum('pass','extend','terminate') NOT NULL DEFAULT 'pass',
  `extended_probation_end_date` date DEFAULT NULL,
  `extension_days` int(11) DEFAULT NULL,
  `extension_reason` text DEFAULT NULL,
  `status` enum('draft','submitted','acknowledged') NOT NULL DEFAULT 'draft',
  `acknowledged_by_id` char(36) DEFAULT NULL,
  `acknowledged_by_type` varchar(255) DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `probation_reviews_employee_id_foreign` (`employee_id`),
  KEY `probation_reviews_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `probation_reviews_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `probation_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `probation_reviews`
--

LOCK TABLES `probation_reviews` WRITE;
/*!40000 ALTER TABLE `probation_reviews` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `probation_reviews` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `product_callbacks`
--

DROP TABLE IF EXISTS `product_callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_callbacks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_stock_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `sales_shift_id` bigint(20) unsigned NOT NULL,
  `recorded_by_id` char(36) NOT NULL,
  `recorded_by_type` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `reason` enum('expired','damaged','quality_issue','customer_return','other') NOT NULL,
  `notes` text DEFAULT NULL,
  `callback_time` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_callbacks_product_stock_id_foreign` (`product_stock_id`),
  KEY `product_callbacks_product_id_callback_time_index` (`product_id`,`callback_time`),
  KEY `product_callbacks_sales_shift_id_callback_time_index` (`sales_shift_id`,`callback_time`),
  KEY `product_callbacks_reason_index` (`reason`),
  CONSTRAINT `product_callbacks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_callbacks_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `product_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_callbacks_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_callbacks`
--

LOCK TABLES `product_callbacks` WRITE;
/*!40000 ALTER TABLE `product_callbacks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `product_callbacks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `product_dispatch_callbacks`
--

DROP TABLE IF EXISTS `product_dispatch_callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_dispatch_callbacks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_dispatch_id` bigint(20) unsigned DEFAULT NULL,
  `sales_shift_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `recorded_by_id` char(36) NOT NULL,
  `recorded_by_type` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `reason` enum('expired','damaged','quality_issue','customer_return','over_received','over_stock','wrong_item','other') DEFAULT NULL,
  `status` enum('pending','approved_by_production','received_by_production','completed') NOT NULL DEFAULT 'pending',
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `received_by_id` char(36) DEFAULT NULL,
  `received_by_type` varchar(255) DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `callback_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_dispatch_callbacks_product_id_foreign` (`product_id`),
  KEY `product_dispatch_callbacks_product_dispatch_id_status_index` (`product_dispatch_id`,`status`),
  KEY `product_dispatch_callbacks_sales_shift_id_callback_time_index` (`sales_shift_id`,`callback_time`),
  KEY `product_dispatch_callbacks_status_callback_time_index` (`status`,`callback_time`),
  CONSTRAINT `product_dispatch_callbacks_product_dispatch_id_foreign` FOREIGN KEY (`product_dispatch_id`) REFERENCES `product_dispatches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatch_callbacks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatch_callbacks_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_dispatch_callbacks`
--

LOCK TABLES `product_dispatch_callbacks` WRITE;
/*!40000 ALTER TABLE `product_dispatch_callbacks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `product_dispatch_callbacks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `product_dispatches`
--

DROP TABLE IF EXISTS `product_dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_dispatches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `production_request_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) NOT NULL,
  `daily_produce_id` bigint(20) unsigned DEFAULT NULL,
  `production_record_id` bigint(20) unsigned DEFAULT NULL,
  `production_shift_id` bigint(20) unsigned NOT NULL COMMENT 'Kitchen/Production shift',
  `sales_shift_id` bigint(20) unsigned DEFAULT NULL,
  `sales_department_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` char(36) NOT NULL,
  `dispatched_by_id` char(36) NOT NULL,
  `dispatched_by_type` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `received_quantity` decimal(12,2) DEFAULT NULL,
  `uom` varchar(50) NOT NULL,
  `dispatch_time` timestamp NOT NULL,
  `shift_type` enum('morning','afternoon','night') NOT NULL,
  `dispatch_date` date NOT NULL,
  `received_by_id` char(36) DEFAULT NULL COMMENT 'Sales employee who received',
  `received_by_type` varchar(255) DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending_verification','accepted','received','rejected') DEFAULT 'pending_verification',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_dispatches_daily_produce_id_foreign` (`daily_produce_id`),
  KEY `product_dispatches_production_shift_id_foreign` (`production_shift_id`),
  KEY `product_dispatches_product_id_dispatch_date_shift_type_index` (`product_id`,`dispatch_date`,`shift_type`),
  KEY `product_dispatches_branch_id_dispatch_date_index` (`branch_id`,`dispatch_date`),
  KEY `product_dispatches_sales_shift_id_foreign` (`sales_shift_id`),
  KEY `product_dispatches_sales_department_id_index` (`sales_department_id`),
  KEY `product_dispatches_production_record_id_index` (`production_record_id`),
  KEY `product_dispatches_production_request_id_foreign` (`production_request_id`),
  CONSTRAINT `product_dispatches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_daily_produce_id_foreign` FOREIGN KEY (`daily_produce_id`) REFERENCES `daily_produces` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_dispatches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_production_record_id_foreign` FOREIGN KEY (`production_record_id`) REFERENCES `production_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_production_request_id_foreign` FOREIGN KEY (`production_request_id`) REFERENCES `production_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_production_shift_id_foreign` FOREIGN KEY (`production_shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_sales_department_id_foreign` FOREIGN KEY (`sales_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_dispatches_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_dispatches`
--

LOCK TABLES `product_dispatches` WRITE;
/*!40000 ALTER TABLE `product_dispatches` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `product_dispatches` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `product_stocks`
--

DROP TABLE IF EXISTS `product_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_shift_id` bigint(20) unsigned DEFAULT NULL,
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
  `is_workflow_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` char(36) DEFAULT NULL,
  `workflow_step` enum('opening_pending','opening_verified','closing_pending','closing_completed') NOT NULL DEFAULT 'opening_pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_stock` (`sales_shift_id`,`product_id`,`stock_date`,`shift_type`),
  KEY `product_stocks_verified_by_foreign` (`verified_by`),
  KEY `product_stocks_workflow_idx` (`stock_date`,`shift_type`,`is_workflow_verified`),
  KEY `product_stocks_product_workflow_idx` (`product_id`,`stock_date`,`workflow_step`),
  CONSTRAINT `product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stocks_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_stocks_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_stocks`
--

LOCK TABLES `product_stocks` WRITE;
/*!40000 ALTER TABLE `product_stocks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `product_stocks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `product_transfers`
--

DROP TABLE IF EXISTS `product_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_transfers`
--

LOCK TABLES `product_transfers` WRITE;
/*!40000 ALTER TABLE `product_transfers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `product_transfers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `product_types`
--

DROP TABLE IF EXISTS `product_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
INSERT INTO `product_types` VALUES
(1,1,'Pastries','PT','Baked pastries including croissants, danishes, and puff pastries','active',1,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(2,1,'Breads','BR','Fresh baked breads, loaves, and rolls','active',2,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(3,1,'Cakes','CK','Layer cakes, sponge cakes, and celebration cakes','active',3,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(4,1,'Cookies','CO','Baked cookies and biscuits','active',4,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(5,1,'Muffins','MF','Sweet and savory muffins','active',5,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(6,1,'Pies & Tarts','PIT','Fruit pies, cream pies, and tarts','active',6,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(7,1,'Sandwiches','SW','Fresh sandwiches and wraps','active',7,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(8,1,'Hot Kitchen','HK','Cooked meals and hot food items','active',8,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(9,2,'Gelato Base','GB','Base gelato mixtures before flavoring','active',1,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(10,2,'Gelato Flavors','GF','Finished gelato in various flavors','active',2,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(11,2,'Sorbet','SB','Fruit-based frozen desserts without dairy','active',3,'2026-02-02 12:46:49','2026-02-02 12:46:49',NULL),
(12,2,'Ice Cream','IC','Traditional ice cream products','active',4,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(13,2,'Frozen Yogurt','FY','Frozen yogurt in various flavors','active',5,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(14,2,'Gelato Toppings','GT','House-made toppings and mix-ins for gelato','active',6,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(15,3,'Chocolates','CH','Handcrafted chocolates and truffles','active',1,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(16,3,'Candies','CD','Hard and soft candies','active',2,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(17,3,'Fudge','FG','Traditional and flavored fudge','active',3,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(18,3,'Caramels','CR','Soft and hard caramels','active',4,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(19,3,'Marshmallows','MM','Gourmet marshmallows in various flavors','active',5,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
(20,3,'Nougat','NG','Traditional nougat confections','active',6,'2026-02-02 12:46:50','2026-02-02 12:46:50',NULL);
/*!40000 ALTER TABLE `product_types` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_callbacks`
--

DROP TABLE IF EXISTS `production_callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_callbacks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint(20) unsigned NOT NULL,
  `source_type` enum('raw_material_from_stock','finished_product_reject') NOT NULL,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` char(36) DEFAULT NULL,
  `recorded_by_id` char(36) NOT NULL,
  `recorded_by_type` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `reason` enum('damaged','expired','quality_issue','wrong_batch','contamination','other') NOT NULL,
  `status` enum('pending','approved_by_inventory','completed','rejected') NOT NULL DEFAULT 'pending',
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `callback_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_callbacks_item_id_foreign` (`item_id`),
  KEY `production_callbacks_product_id_foreign` (`product_id`),
  KEY `production_callbacks_shift_id_callback_time_index` (`shift_id`,`callback_time`),
  KEY `production_callbacks_status_callback_time_index` (`status`,`callback_time`),
  KEY `production_callbacks_source_type_item_id_index` (`source_type`,`item_id`),
  CONSTRAINT `production_callbacks_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_callbacks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_callbacks_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_callbacks`
--

LOCK TABLES `production_callbacks` WRITE;
/*!40000 ALTER TABLE `production_callbacks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_callbacks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_milestones`
--

DROP TABLE IF EXISTS `production_milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_milestones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `production_record_id` bigint(20) unsigned NOT NULL,
  `milestone_type` enum('ingredients_collected','production_started','quality_check_passed','packaging_completed','dispatch_ready') NOT NULL,
  `achieved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `achieved_by` char(36) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_milestones_achieved_by_foreign` (`achieved_by`),
  KEY `production_milestones_production_record_id_milestone_type_index` (`production_record_id`,`milestone_type`),
  KEY `production_milestones_achieved_at_index` (`achieved_at`),
  CONSTRAINT `production_milestones_achieved_by_foreign` FOREIGN KEY (`achieved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_milestones_production_record_id_foreign` FOREIGN KEY (`production_record_id`) REFERENCES `production_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_milestones`
--

LOCK TABLES `production_milestones` WRITE;
/*!40000 ALTER TABLE `production_milestones` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_milestones` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_order_components`
--

DROP TABLE IF EXISTS `production_order_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_order_components` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `production_order_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) NOT NULL,
  `quantity_required` decimal(15,4) NOT NULL,
  `quantity_used` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_order_components_production_order_id_foreign` (`production_order_id`),
  KEY `production_order_components_product_id_foreign` (`product_id`),
  CONSTRAINT `production_order_components_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `production_order_components_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_order_components`
--

LOCK TABLES `production_order_components` WRITE;
/*!40000 ALTER TABLE `production_order_components` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_order_components` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_orders`
--

DROP TABLE IF EXISTS `production_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `output_product_id` char(36) NOT NULL,
  `output_quantity` decimal(15,4) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `planned_date` date NOT NULL,
  `completed_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'planned',
  `total_material_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_labor_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_overhead_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `production_orders_order_number_unique` (`order_number`),
  KEY `production_orders_output_product_id_foreign` (`output_product_id`),
  KEY `production_orders_branch_id_foreign` (`branch_id`),
  KEY `production_orders_created_by_foreign` (`created_by`),
  KEY `production_orders_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `production_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `production_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `production_orders_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `production_orders_output_product_id_foreign` FOREIGN KEY (`output_product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_orders`
--

LOCK TABLES `production_orders` WRITE;
/*!40000 ALTER TABLE `production_orders` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_orders` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_progress_alerts`
--

DROP TABLE IF EXISTS `production_progress_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_progress_alerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `daily_produce_id` bigint(20) unsigned NOT NULL,
  `alert_type` enum('delay_warning','quality_issue','resource_shortage','completion_milestone','bottleneck_detected') NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `message` text NOT NULL,
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT 0,
  `acknowledged_by` char(36) DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `auto_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_progress_alerts_daily_produce_id_foreign` (`daily_produce_id`),
  KEY `production_progress_alerts_acknowledged_by_foreign` (`acknowledged_by`),
  KEY `production_progress_alerts_alert_type_severity_index` (`alert_type`,`severity`),
  KEY `production_progress_alerts_is_acknowledged_index` (`is_acknowledged`),
  KEY `production_progress_alerts_created_at_index` (`created_at`),
  CONSTRAINT `production_progress_alerts_acknowledged_by_foreign` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_progress_alerts_daily_produce_id_foreign` FOREIGN KEY (`daily_produce_id`) REFERENCES `daily_produces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_progress_alerts`
--

LOCK TABLES `production_progress_alerts` WRITE;
/*!40000 ALTER TABLE `production_progress_alerts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_progress_alerts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_progress_feedback`
--

DROP TABLE IF EXISTS `production_progress_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_progress_feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `production_request_id` bigint(20) unsigned NOT NULL,
  `milestone` enum('started','in_production','quality_check','completed') NOT NULL DEFAULT 'in_production',
  `progress_percentage` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `updated_by_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_feedback_request_created` (`production_request_id`,`created_at`),
  KEY `idx_feedback_milestone` (`milestone`),
  CONSTRAINT `production_progress_feedback_production_request_id_foreign` FOREIGN KEY (`production_request_id`) REFERENCES `production_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_progress_feedback`
--

LOCK TABLES `production_progress_feedback` WRITE;
/*!40000 ALTER TABLE `production_progress_feedback` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_progress_feedback` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_records`
--

DROP TABLE IF EXISTS `production_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `daily_produce_id` bigint(20) unsigned NOT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `produced_by_id` char(36) NOT NULL,
  `produced_by_type` varchar(255) NOT NULL,
  `quantity_produced` decimal(12,2) NOT NULL,
  `quantity_approved` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity_sent_out` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity_for_order` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity_remaining` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15,2) DEFAULT 0.00 COMMENT 'Calculated standard cost per unit',
  `total_production_cost` decimal(15,2) DEFAULT 0.00 COMMENT 'Total cost of production batch',
  `quantity_rejected` decimal(12,2) NOT NULL DEFAULT 0.00,
  `production_time` timestamp NOT NULL,
  `quality_status` enum('excellent','good','acceptable','rejected') NOT NULL DEFAULT 'good',
  `quality_score` decimal(5,2) DEFAULT NULL,
  `weight_deviation` decimal(8,3) DEFAULT NULL,
  `decoration_grade` enum('A','B','C','D') DEFAULT NULL,
  `shelf_life_days` int(11) DEFAULT NULL,
  `overrun_percentage` decimal(5,2) DEFAULT NULL,
  `temperature_alert` tinyint(1) NOT NULL DEFAULT 0,
  `dispatch_status` enum('available','partial','fully_dispatched') NOT NULL DEFAULT 'available',
  `progress_stage` enum('started','ingredients_collected','processing','quality_check','packaging','ready_for_dispatch') NOT NULL DEFAULT 'started',
  `stage_start_time` timestamp NULL DEFAULT NULL,
  `stage_end_time` timestamp NULL DEFAULT NULL,
  `expected_duration_minutes` int(11) DEFAULT NULL,
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_records_daily_produce_id_foreign` (`daily_produce_id`),
  KEY `production_records_recipe_id_foreign` (`recipe_id`),
  CONSTRAINT `production_records_daily_produce_id_foreign` FOREIGN KEY (`daily_produce_id`) REFERENCES `daily_produces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_records_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_records`
--

LOCK TABLES `production_records` WRITE;
/*!40000 ALTER TABLE `production_records` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_records` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `production_requests`
--

DROP TABLE IF EXISTS `production_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `sales_department_id` bigint(20) unsigned DEFAULT NULL,
  `production_department_id` bigint(20) unsigned DEFAULT NULL,
  `item_request_id` bigint(20) unsigned DEFAULT NULL,
  `recipe_id` bigint(20) unsigned DEFAULT NULL,
  `planned_production_quantity` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','approved','in_progress','quality_check','completed','dispatched','accepted','rejected','cancelled') DEFAULT 'pending',
  `priority` enum('normal','urgent') NOT NULL DEFAULT 'normal',
  `created_by_id` char(36) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_requests_recipe_id_foreign` (`recipe_id`),
  KEY `production_requests_production_department_id_index` (`production_department_id`),
  KEY `production_requests_sales_department_id_index` (`sales_department_id`),
  KEY `production_requests_status_index` (`status`),
  KEY `production_requests_production_department_id_status_index` (`production_department_id`,`status`),
  KEY `production_requests_shift_id_foreign` (`shift_id`),
  KEY `production_requests_item_request_id_foreign` (`item_request_id`),
  CONSTRAINT `production_requests_item_request_id_foreign` FOREIGN KEY (`item_request_id`) REFERENCES `item_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_requests_production_department_id_foreign` FOREIGN KEY (`production_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_requests_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_requests_sales_department_id_foreign` FOREIGN KEY (`sales_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_requests_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_requests`
--

LOCK TABLES `production_requests` WRITE;
/*!40000 ALTER TABLE `production_requests` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `production_requests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `uom_id` bigint(20) unsigned DEFAULT NULL,
  `unit_weight` decimal(10,2) DEFAULT NULL COMMENT 'Weight of one unit (in grams/ml)',
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
  KEY `products_uom_id_foreign` (`uom_id`),
  CONSTRAINT `products_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_product_type_id_foreign` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`),
  CONSTRAINT `products_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `units_of_measure` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `products` VALUES
('019c1e64-3746-7337-af33-de19ff585db2','Butter Croissant','PT-BUT-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,'Classic French butter croissant, flaky and golden',3.50,1.20,2,13,100.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"breakfast\",\"french\",\"popular\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-3763-720c-9310-55f745030649','Almond Danish','PT-ALM-002','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,'Sweet Danish pastry topped with sliced almonds',4.00,1.50,2,13,150.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\",\"nuts\"]','[\"breakfast\",\"pastry\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-376d-7041-8424-c7bf61aee980','Sourdough Loaf','BR-SOU-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',2,NULL,'Artisan sourdough bread with tangy flavor',6.50,2.00,4,13,800.00,1,1,NULL,'[\"gluten\"]','[\"artisan\",\"sourdough\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-3784-7343-808d-0a9a36197eaa','Banana Bread','BR-BAN-002','019c1e63-b2e7-70f2-81ab-c6347efd07f0',2,NULL,'Moist banana bread with walnuts',5.99,2.50,7,13,900.00,1,1,NULL,'[\"gluten\",\"eggs\",\"nuts\"]','[\"sweet\",\"popular\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-378f-726c-83b2-eef3a471fecf','Chocolate Cake Slice','CK-CHO-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',3,NULL,'Rich chocolate layer cake with chocolate ganache',7.50,2.80,5,13,200.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"chocolate\",\"dessert\",\"popular\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-379b-72dd-8488-7de003fe2455','Chocolate Chip Cookie','CO-CHI-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',4,NULL,'Classic chocolate chip cookies',2.50,0.80,10,13,50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"popular\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37a6-7070-b493-09c4d7d7673c','Oatmeal Raisin Cookie','CO-OAT-002','019c1e63-b2e7-70f2-81ab-c6347efd07f0',4,NULL,'Wholesome oatmeal cookies with plump raisins',2.50,0.75,10,13,50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"healthy\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37b0-70f7-9685-baba3f531570','Vanilla Gelato Base','GB-VAN-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',9,NULL,'Premium vanilla gelato base mixture',0.00,8.50,3,3,1000.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"base\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37bc-71d4-83ef-a6d51c6a53d6','Chocolate Gelato','GF-CHO-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',10,NULL,'Rich dark chocolate gelato',4.50,1.80,30,2,100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"chocolate\",\"popular\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37c7-7367-956f-fc4b6b738929','Strawberry Gelato','GF-STR-002','019c1e63-b2e7-70f2-81ab-c6347efd07f0',10,NULL,'Fresh strawberry gelato',4.50,1.90,30,2,100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"fruit\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37d2-735a-8546-9961146ab53a','Pistachio Gelato','GF-PIS-003','019c1e63-b2e7-70f2-81ab-c6347efd07f0',10,NULL,'Authentic pistachio gelato from Sicily',5.50,2.50,30,2,100.00,1,1,NULL,'[\"dairy\",\"nuts\"]','[\"gelato\",\"premium\",\"nuts\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37dd-7108-ae30-90f9166313bc','Dark Chocolate Truffle','CH-DAR-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',15,NULL,'Hand-rolled dark chocolate truffle',2.50,0.90,21,13,20.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"premium\",\"truffle\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37e8-72c5-9697-0bb2b5000050','Salted Caramel Chocolate','CH-SAL-002','019c1e63-b2e7-70f2-81ab-c6347efd07f0',15,NULL,'Milk chocolate with salted caramel filling',2.75,1.00,21,13,25.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"caramel\",\"popular\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL),
('019c1e64-37f3-7120-b56b-2c9646c12849','Fruit Gummies','CD-FRU-001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',16,NULL,'Assorted fruit-flavored gummy candies',1.50,0.40,180,2,100.00,1,1,NULL,'[]','[\"candy\",\"fruit\",\"kids\"]','2026-02-02 12:46:50','2026-02-02 12:46:50',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `purchase_approval_requests`
--

DROP TABLE IF EXISTS `purchase_approval_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_approval_requests` (
  `id` char(36) NOT NULL,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `requested_by_id` char(36) NOT NULL,
  `requested_by_type` varchar(255) NOT NULL,
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `request_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_approval_requests_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_approval_requests_status_index` (`status`),
  CONSTRAINT `purchase_approval_requests_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_approval_requests`
--

LOCK TABLES `purchase_approval_requests` WRITE;
/*!40000 ALTER TABLE `purchase_approval_requests` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `purchase_approval_requests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `fob_fc` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fob_ngn` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_costs` decimal(12,2) NOT NULL DEFAULT 0.00,
  `landing_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(12,2) NOT NULL,
  `cost_per_unit` decimal(12,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase_item_item` (`item_id`),
  KEY `idx_purchase_item_purchase` (`purchase_id`),
  CONSTRAINT `purchase_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity_ordered` decimal(15,4) NOT NULL,
  `quantity_received` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `quantity_invoiced` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_quote_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `order_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_order_number_unique` (`order_number`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_purchase_quote_id_foreign` (`purchase_quote_id`),
  KEY `purchase_orders_branch_id_foreign` (`branch_id`),
  KEY `purchase_orders_approved_by_foreign` (`approved_by`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `purchase_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `purchase_orders_purchase_quote_id_foreign` FOREIGN KEY (`purchase_quote_id`) REFERENCES `purchase_quotes` (`id`),
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `purchase_quote_items`
--

DROP TABLE IF EXISTS `purchase_quote_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_quote_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_quote_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_quote_items_purchase_quote_id_foreign` (`purchase_quote_id`),
  KEY `purchase_quote_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_quote_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `purchase_quote_items_purchase_quote_id_foreign` FOREIGN KEY (`purchase_quote_id`) REFERENCES `purchase_quotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_quote_items`
--

LOCK TABLES `purchase_quote_items` WRITE;
/*!40000 ALTER TABLE `purchase_quote_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `purchase_quote_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `purchase_quotes`
--

DROP TABLE IF EXISTS `purchase_quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_quotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quote_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `quote_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_quotes_quote_number_unique` (`quote_number`),
  KEY `purchase_quotes_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_quotes_branch_id_foreign` (`branch_id`),
  KEY `purchase_quotes_created_by_foreign` (`created_by`),
  CONSTRAINT `purchase_quotes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `purchase_quotes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `purchase_quotes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_quotes`
--

LOCK TABLES `purchase_quotes` WRITE;
/*!40000 ALTER TABLE `purchase_quotes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `purchase_quotes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `recorded_by_id` char(36) NOT NULL,
  `recorded_by_type` varchar(255) NOT NULL,
  `purchase_number` varchar(255) NOT NULL,
  `purchase_date` date NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_contact` varchar(255) DEFAULT NULL,
  `total_fob_fc` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_fob_ngn` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_costs` decimal(12,2) NOT NULL DEFAULT 0.00,
  `landing_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('paid','partial','pending') NOT NULL DEFAULT 'pending',
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_account_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_status` enum('pending','posted','failed') NOT NULL DEFAULT 'pending',
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_order_id` bigint(20) unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `three_way_match_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_purchase_number_unique` (`purchase_number`),
  KEY `idx_purchase_supplier` (`supplier_name`),
  KEY `idx_purchase_payment_status` (`payment_status`),
  KEY `idx_purchase_date` (`purchase_date`),
  KEY `idx_purchase_branch_date` (`branch_id`,`purchase_date`),
  KEY `idx_purchase_status_date` (`payment_status`,`purchase_date`),
  KEY `purchases_status_index` (`status`),
  KEY `purchases_bank_account_id_index` (`bank_account_id`),
  KEY `purchases_gl_entry_id_foreign` (`gl_entry_id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_purchase_order_id_foreign` (`purchase_order_id`),
  CONSTRAINT `purchases_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `raw_material_utilizations`
--

DROP TABLE IF EXISTS `raw_material_utilizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `raw_material_utilizations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `receipts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `recipe_ingredients`
--

DROP TABLE IF EXISTS `recipe_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipe_ingredients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `uom_id` bigint(20) unsigned DEFAULT NULL,
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
  KEY `recipe_ingredients_uom_id_foreign` (`uom_id`),
  CONSTRAINT `recipe_ingredients_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `recipe_ingredients_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipe_ingredients_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `units_of_measure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_ingredients`
--

LOCK TABLES `recipe_ingredients` WRITE;
/*!40000 ALTER TABLE `recipe_ingredients` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `recipe_ingredients` VALUES
(1,1,2,500.0000,2,0.0020,5.00,0,'Required for Butter Croissant','Standard preparation','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(2,1,4,300.0000,2,0.0080,2.00,1,'Required for Butter Croissant','Standard preparation','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(3,1,1,50.0000,2,0.0010,3.00,2,'Required for Butter Croissant','Standard preparation','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(4,1,8,150.0000,6,0.0030,1.00,3,'Required for Butter Croissant','Standard preparation','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(5,1,5,0.5000,14,15.0000,0.00,4,'Required for Butter Croissant','Standard preparation','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(6,1,10,15.0000,2,0.0200,0.00,5,'Required for Butter Croissant','Standard preparation','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(7,1,12,10.0000,2,0.0010,0.00,6,'Required for Butter Croissant','Standard preparation','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(8,2,2,280.0000,2,0.0020,5.00,0,'Required for Chocolate Chip Cookie','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(9,2,4,225.0000,2,0.0080,2.00,1,'Required for Chocolate Chip Cookie','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(10,2,1,200.0000,2,0.0010,3.00,2,'Required for Chocolate Chip Cookie','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(11,2,5,0.5000,14,15.0000,0.00,3,'Required for Chocolate Chip Cookie','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(12,2,6,0.0100,7,50.0000,0.00,4,'Required for Chocolate Chip Cookie','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(13,2,7,300.0000,2,0.0150,1.00,5,'Required for Chocolate Chip Cookie','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(14,3,2,250.0000,2,0.0020,5.00,0,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(15,3,3,75.0000,2,0.0250,2.00,1,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(16,3,1,400.0000,2,0.0010,3.00,2,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(17,3,5,0.6700,14,15.0000,0.00,3,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(18,3,11,125.0000,6,0.0050,1.00,4,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(19,3,6,0.0100,7,50.0000,0.00,5,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(20,3,9,200.0000,6,0.0100,1.00,6,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(21,3,7,200.0000,2,0.0150,1.00,7,'Required for Chocolate Cake Slice','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(22,4,8,2.0000,7,3.0000,2.00,0,'Required for Chocolate Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(23,4,9,1.5000,7,10.0000,2.00,1,'Required for Chocolate Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(24,4,1,600.0000,2,0.0010,3.00,2,'Required for Chocolate Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(25,4,3,200.0000,2,0.0250,2.00,3,'Required for Chocolate Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(26,4,5,1.0000,14,15.0000,0.00,4,'Required for Chocolate Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(27,5,8,1.8000,7,3.0000,2.00,0,'Required for Strawberry Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(28,5,9,1.2000,7,10.0000,2.00,1,'Required for Strawberry Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(29,5,1,550.0000,2,0.0010,3.00,2,'Required for Strawberry Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(30,5,5,0.8000,14,15.0000,0.00,3,'Required for Strawberry Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(31,6,8,1.8000,7,3.0000,2.00,0,'Required for Pistachio Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(32,6,9,1.3000,7,10.0000,2.00,1,'Required for Pistachio Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(33,6,1,580.0000,2,0.0010,3.00,2,'Required for Pistachio Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(34,6,5,0.9000,14,15.0000,0.00,3,'Required for Pistachio Gelato','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(35,7,2,450.0000,2,0.0020,5.00,0,'Required for Almond Danish','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(36,7,4,280.0000,2,0.0080,2.00,1,'Required for Almond Danish','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(37,7,1,120.0000,2,0.0010,3.00,2,'Required for Almond Danish','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(38,7,5,0.6000,14,15.0000,0.00,3,'Required for Almond Danish','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(39,7,8,100.0000,6,0.0030,1.00,4,'Required for Almond Danish','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(40,7,6,0.0050,7,50.0000,0.00,5,'Required for Almond Danish','Standard preparation','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(41,8,2,220.0000,2,0.0020,5.00,0,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(42,8,4,200.0000,2,0.0080,2.00,1,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(43,8,1,180.0000,2,0.0010,3.00,2,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(44,8,5,0.5000,14,15.0000,0.00,3,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(45,8,6,0.0080,7,50.0000,0.00,4,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(46,9,2,1000.0000,2,0.0020,5.00,0,'Required for Sourdough Loaf','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(47,9,12,20.0000,2,0.0010,0.00,1,'Required for Sourdough Loaf','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(48,9,10,10.0000,2,0.0200,0.00,2,'Required for Sourdough Loaf','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(49,10,2,280.0000,2,0.0020,5.00,0,'Required for Banana Bread','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(50,10,1,200.0000,2,0.0010,3.00,1,'Required for Banana Bread','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(51,10,4,120.0000,2,0.0080,2.00,2,'Required for Banana Bread','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(52,10,5,0.5000,14,15.0000,0.00,3,'Required for Banana Bread','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(53,10,11,80.0000,6,0.0050,1.00,4,'Required for Banana Bread','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(54,10,6,0.0050,7,50.0000,0.00,5,'Required for Banana Bread','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(55,11,7,500.0000,2,0.0150,2.00,0,'Required for Dark Chocolate Truffle','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(56,11,9,300.0000,6,0.0100,1.00,1,'Required for Dark Chocolate Truffle','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(57,11,4,50.0000,2,0.0080,1.00,2,'Required for Dark Chocolate Truffle','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(58,11,3,100.0000,2,0.0250,2.00,3,'Required for Dark Chocolate Truffle','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(59,11,6,0.0050,7,50.0000,0.00,4,'Required for Dark Chocolate Truffle','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(60,12,7,600.0000,2,0.0150,2.00,0,'Required for Salted Caramel Chocolate','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(61,12,1,200.0000,2,0.0010,3.00,1,'Required for Salted Caramel Chocolate','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(62,12,9,200.0000,6,0.0100,1.00,2,'Required for Salted Caramel Chocolate','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(63,12,4,80.0000,2,0.0080,1.00,3,'Required for Salted Caramel Chocolate','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(64,12,12,5.0000,2,0.0010,0.00,4,'Required for Salted Caramel Chocolate','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(65,13,8,4.0000,7,3.0000,2.00,0,'Required for Vanilla Gelato Base','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(66,13,9,1.0000,7,10.0000,2.00,1,'Required for Vanilla Gelato Base','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(67,13,1,500.0000,2,0.0010,3.00,2,'Required for Vanilla Gelato Base','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(68,13,6,20.0000,6,0.0500,0.00,3,'Required for Vanilla Gelato Base','Standard preparation','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(69,14,1,400.0000,2,0.0010,3.00,0,'Required for Fruit Gummies','Standard preparation','2026-02-02 12:46:53','2026-02-02 12:46:53');
/*!40000 ALTER TABLE `recipe_ingredients` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `product_type_id` bigint(20) unsigned DEFAULT NULL,
  `cost_per_unit` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `uom_id` bigint(20) unsigned DEFAULT NULL,
  `yield_quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `preparation_time` int(11) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `status` enum('active','inactive','testing') NOT NULL DEFAULT 'active',
  `created_by_id` char(36) NOT NULL,
  `created_by_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipes_sku_unique` (`sku`),
  KEY `recipes_branch_id_foreign` (`branch_id`),
  KEY `recipes_department_id_foreign` (`department_id`),
  KEY `recipes_product_id_index` (`product_id`),
  KEY `recipes_product_type_id_foreign` (`product_type_id`),
  KEY `recipes_uom_id_foreign` (`uom_id`),
  CONSTRAINT `recipes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_product_type_id_foreign` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`),
  CONSTRAINT `recipes_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `units_of_measure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `recipes` VALUES
(1,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-3746-7337-af33-de19ff585db2','Butter Croissant','PT-BUT-001-RCP',1,0.4923,13,24.00,240,'[\"Mix flour, sugar, salt, and yeast in a large bowl\",\"Add cold butter pieces and work into the dough\",\"Knead until smooth and elastic\",\"Rest dough in refrigerator for 2 hours\",\"Roll out and fold dough multiple times (lamination)\",\"Cut into triangles and roll into croissant shape\",\"Proof for 1-2 hours until doubled\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes until golden\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:50','2026-02-02 12:46:50'),
(2,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-379b-72dd-8488-7de003fe2455','Chocolate Chip Cookie','CO-CHI-001-RCP',1,0.4215,13,36.00,45,'[\"Cream together butter and sugars\",\"Beat in eggs and vanilla extract\",\"Mix in flour, baking soda, and salt\",\"Fold in chocolate chips\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 10-12 minutes\",\"Cool on baking sheet for 5 minutes before transferring\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:50','2026-02-02 12:46:51'),
(3,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-378f-726c-83b2-eef3a471fecf','Chocolate Cake Slice','CK-CHO-001-RCP',1,1.5901,13,12.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mix dry ingredients: flour, cocoa powder, baking soda, salt\",\"Beat together eggs, sugar, oil, and vanilla\",\"Add hot water and mix until smooth\",\"Pour into greased cake pans\",\"Bake for 30-35 minutes\",\"Cool completely before frosting\",\"Prepare chocolate ganache and frost the cake\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(4,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37bc-71d4-83ef-a6d51c6a53d6','Chocolate Gelato','GF-CHO-001-RCP',1,0.8428,2,50.00,60,'[\"Heat milk and cream to 85\\u00b0C\",\"Whisk cocoa powder with some warm milk\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add cocoa mixture and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(5,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37c7-7367-956f-fc4b6b738929','Strawberry Gelato','GF-STR-002-RCP',1,0.6063,2,50.00,60,'[\"Blend fresh strawberries to puree\",\"Heat milk, cream, and sugar to 85\\u00b0C\",\"Mix with egg yolks\",\"Cool completely\",\"Add strawberry puree\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(6,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37d2-735a-8546-9961146ab53a','Pistachio Gelato','GF-PIS-003-RCP',1,0.6573,2,50.00,70,'[\"Grind pistachios into fine paste\",\"Heat milk and cream to 85\\u00b0C\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add pistachio paste and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(7,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-3763-720c-9310-55f745030649','Almond Danish','PT-ALM-002-RCP',1,0.7170,13,18.00,180,'[\"Prepare puff pastry dough\",\"Roll and fold dough multiple times\",\"Cut into squares\",\"Add almond cream filling\",\"Top with sliced almonds\",\"Proof for 1 hour\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:51','2026-02-02 12:46:51'),
(8,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37a6-7070-b493-09c4d7d7673c','Oatmeal Raisin Cookie','CO-OAT-002-RCP',1,0.2545,13,40.00,40,'[\"Cream butter and sugars together\",\"Beat in eggs and vanilla\",\"Mix in flour, oats, and spices\",\"Fold in raisins\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 12-14 minutes\",\"Cool before serving\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(9,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-376d-7041-8424-c7bf61aee980','Sourdough Loaf','BR-SOU-001-RCP',8,0.5800,13,4.00,1440,'[\"Feed sourdough starter 12 hours before\",\"Mix flour, water, salt, and starter\",\"Autolyse for 30 minutes\",\"Stretch and fold every 30 minutes (4 times)\",\"Bulk ferment for 4-6 hours\",\"Shape into loaves\",\"Cold ferment overnight (12 hours)\",\"Score and bake at 230\\u00b0C for 35-40 minutes\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(10,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-3784-7343-808d-0a9a36197eaa','Banana Bread','BR-BAN-002-RCP',8,4.9636,13,2.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mash ripe bananas\",\"Mix butter, sugar, and eggs\",\"Add mashed bananas\",\"Mix in flour, baking soda, and salt\",\"Pour into greased loaf pans\",\"Bake for 55-60 minutes\",\"Cool before slicing\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(11,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37dd-7108-ae30-90f9166313bc','Dark Chocolate Truffle','CH-DAR-001-RCP',8,0.2777,13,50.00,120,'[\"Chop dark chocolate finely\",\"Heat cream to simmering\",\"Pour over chocolate and let sit\",\"Stir until smooth ganache forms\",\"Cool and refrigerate for 2 hours\",\"Roll into balls\",\"Coat with cocoa powder\",\"Store in refrigerator\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(12,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37e8-72c5-9697-0bb2b5000050','Salted Caramel Chocolate','CH-SAL-002-RCP',8,0.2512,13,48.00,150,'[\"Make caramel with sugar and cream\",\"Add salt to caramel and cool\",\"Temper milk chocolate\",\"Fill molds halfway with chocolate\",\"Add caramel filling\",\"Top with more chocolate\",\"Cool and unmold\",\"Store in cool place\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(13,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37b0-70f7-9685-baba3f531570','Vanilla Gelato Base','GB-VAN-001-RCP',1,2.9944,3,8.00,45,'[\"Heat milk and cream to 85\\u00b0C\",\"Whisk in sugar until dissolved\",\"Add vanilla extract and mix well\",\"Cool to 4\\u00b0C\",\"Age in refrigerator for 4-12 hours\",\"Store at -18\\u00b0C until use\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:52','2026-02-02 12:46:52'),
(14,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,'019c1e64-37f3-7120-b56b-2c9646c12849','Fruit Gummies','CD-FRU-001-RCP',1,0.0041,2,100.00,180,'[\"Heat fruit puree and sugar to 80\\u00b0C\",\"Dissolve gelatin in hot mixture\",\"Add citric acid and mix well\",\"Strain through fine mesh\",\"Pour into molds\",\"Cool at room temperature for 30 minutes\",\"Refrigerate for 2 hours until set\",\"Unmold and store in cool place\"]','active','00418083-571e-396c-85ae-733e3120f146','App\\Models\\Employee','2026-02-02 12:46:52','2026-02-02 12:46:53');
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `report_distributions`
--

DROP TABLE IF EXISTS `report_distributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_distributions` (
  `id` char(36) NOT NULL,
  `branch_id` char(36) NOT NULL,
  `reportable_type` varchar(255) NOT NULL,
  `reportable_id` char(36) NOT NULL,
  `recipient_type` varchar(255) NOT NULL,
  `recipient_id` char(36) DEFAULT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `distribution_method` enum('email','download','notification') NOT NULL DEFAULT 'email',
  `sent_at` timestamp NULL DEFAULT NULL,
  `viewed_at` timestamp NULL DEFAULT NULL,
  `downloaded_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','sent','delivered','failed') NOT NULL DEFAULT 'pending',
  `failure_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_distributions_reportable_type_reportable_id_index` (`reportable_type`,`reportable_id`),
  KEY `rd_branch_reportable_idx` (`branch_id`,`reportable_type`,`reportable_id`),
  KEY `rd_recipient_idx` (`recipient_type`,`recipient_id`),
  KEY `rd_status_sent_idx` (`status`,`sent_at`),
  CONSTRAINT `report_distributions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_distributions`
--

LOCK TABLES `report_distributions` WRITE;
/*!40000 ALTER TABLE `report_distributions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `report_distributions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `report_schedules`
--

DROP TABLE IF EXISTS `report_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_schedules` (
  `id` char(36) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `schedule_name` varchar(255) NOT NULL,
  `report_type` varchar(255) NOT NULL,
  `report_category` varchar(255) NOT NULL,
  `frequency` enum('daily','weekly','biweekly','monthly','quarterly') NOT NULL DEFAULT 'daily',
  `frequency_config` varchar(255) DEFAULT NULL,
  `generation_time` time NOT NULL DEFAULT '23:59:59',
  `auto_compile` tinyint(1) NOT NULL DEFAULT 0,
  `auto_send_md` tinyint(1) NOT NULL DEFAULT 0,
  `recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recipients`)),
  `notification_channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_channels`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_generated_at` timestamp NULL DEFAULT NULL,
  `next_generation_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_schedules_department_id_foreign` (`department_id`),
  KEY `report_schedules_is_active_next_generation_at_index` (`is_active`,`next_generation_at`),
  KEY `report_schedules_branch_id_department_id_index` (`branch_id`,`department_id`),
  CONSTRAINT `report_schedules_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_schedules_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_schedules`
--

LOCK TABLES `report_schedules` WRITE;
/*!40000 ALTER TABLE `report_schedules` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `report_schedules` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `report_templates`
--

DROP TABLE IF EXISTS `report_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_templates` (
  `id` char(36) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `template_name` varchar(255) NOT NULL,
  `report_type` varchar(255) NOT NULL,
  `report_category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `template_structure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`template_structure`)),
  `chart_configurations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`chart_configurations`)),
  `formatting_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`formatting_options`)),
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_id` char(36) DEFAULT NULL,
  `created_by_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_templates_branch_id_foreign` (`branch_id`),
  KEY `report_templates_report_type_report_category_index` (`report_type`,`report_category`),
  KEY `report_templates_is_default_is_active_index` (`is_default`,`is_active`),
  CONSTRAINT `report_templates_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_templates`
--

LOCK TABLES `report_templates` WRITE;
/*!40000 ALTER TABLE `report_templates` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `report_templates` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `role_category_constraints`
--

DROP TABLE IF EXISTS `role_category_constraints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_category_constraints` (
  `id` char(36) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `category_id` char(36) NOT NULL,
  `department_type` enum('specific','category_wide','branch_wide') NOT NULL DEFAULT 'specific',
  `allowed_department_slugs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_department_slugs`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_category` (`role_name`,`category_id`),
  KEY `role_category_constraints_category_id_is_active_index` (`category_id`,`is_active`),
  CONSTRAINT `role_category_constraints_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `department_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_category_constraints`
--

LOCK TABLES `role_category_constraints` WRITE;
/*!40000 ALTER TABLE `role_category_constraints` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `role_category_constraints` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
INSERT INTO `role_has_permissions` VALUES
(119,64),
(120,64),
(121,64),
(122,64),
(123,64),
(124,64),
(125,64),
(126,64),
(127,64),
(128,64),
(129,64),
(130,64),
(131,64),
(132,64),
(133,64),
(134,64),
(135,64),
(136,64),
(137,64),
(138,64),
(139,64),
(140,64),
(141,64),
(142,64),
(143,64),
(144,64),
(145,64),
(146,64),
(147,64),
(148,64),
(149,64),
(150,64),
(151,64),
(152,64),
(153,64),
(154,64),
(155,64),
(156,64),
(157,64),
(158,64),
(159,64),
(160,64),
(161,64),
(162,64),
(163,64),
(164,64),
(165,64),
(166,64),
(167,64),
(168,64),
(169,64),
(170,64),
(171,64),
(172,64),
(173,64),
(174,64),
(175,64),
(176,64),
(177,64),
(178,64),
(179,64),
(180,64),
(181,64),
(182,64),
(183,64),
(184,64),
(185,64),
(186,64),
(187,64),
(188,64),
(189,64),
(190,64),
(191,64),
(192,64),
(193,64),
(194,64),
(195,64),
(196,64),
(197,64),
(198,64),
(199,64),
(200,64),
(201,64),
(202,64),
(203,64),
(204,64),
(205,64),
(206,64),
(207,64),
(208,64),
(209,64),
(210,64),
(211,64),
(212,64),
(213,64),
(214,64),
(215,64),
(216,64),
(217,64),
(218,64),
(219,64),
(220,64),
(221,64),
(222,64),
(223,64),
(224,64),
(225,64),
(226,64),
(227,64),
(228,64),
(229,64),
(230,64),
(231,64),
(232,64),
(233,64),
(234,64),
(235,64),
(236,64),
(119,65),
(120,65),
(121,65),
(122,65),
(123,65),
(124,65),
(125,65),
(126,65),
(127,65),
(128,65),
(129,65),
(130,65),
(131,65),
(132,65),
(133,65),
(134,65),
(135,65),
(136,65),
(137,65),
(138,65),
(139,65),
(140,65),
(141,65),
(142,65),
(143,65),
(144,65),
(145,65),
(146,65),
(147,65),
(148,65),
(149,65),
(150,65),
(151,65),
(152,65),
(153,65),
(154,65),
(155,65),
(156,65),
(157,65),
(158,65),
(159,65),
(160,65),
(161,65),
(162,65),
(163,65),
(164,65),
(165,65),
(166,65),
(167,65),
(168,65),
(169,65),
(170,65),
(171,65),
(172,65),
(173,65),
(174,65),
(175,65),
(176,65),
(177,65),
(178,65),
(179,65),
(180,65),
(181,65),
(182,65),
(183,65),
(184,65),
(185,65),
(186,65),
(187,65),
(188,65),
(189,65),
(190,65),
(191,65),
(192,65),
(193,65),
(194,65),
(195,65),
(196,65),
(197,65),
(198,65),
(199,65),
(200,65),
(201,65),
(202,65),
(203,65),
(204,65),
(205,65),
(206,65),
(207,65),
(208,65),
(209,65),
(210,65),
(211,65),
(212,65),
(213,65),
(214,65),
(215,65),
(216,65),
(217,65),
(218,65),
(219,65),
(220,65),
(221,65),
(222,65),
(223,65),
(224,65),
(225,65),
(226,65),
(227,65),
(228,65),
(229,65),
(230,65),
(231,65),
(232,65),
(233,65),
(234,65),
(235,65),
(236,65),
(119,66),
(120,66),
(121,66),
(122,66),
(123,66),
(124,66),
(125,66),
(126,66),
(127,66),
(128,66),
(129,66),
(130,66),
(131,66),
(132,66),
(133,66),
(134,66),
(135,66),
(136,66),
(137,66),
(138,66),
(139,66),
(140,66),
(141,66),
(142,66),
(143,66),
(144,66),
(145,66),
(146,66),
(147,66),
(148,66),
(149,66),
(150,66),
(151,66),
(152,66),
(153,66),
(154,66),
(155,66),
(156,66),
(157,66),
(158,66),
(159,66),
(160,66),
(161,66),
(162,66),
(163,66),
(164,66),
(165,66),
(166,66),
(167,66),
(168,66),
(169,66),
(170,66),
(171,66),
(172,66),
(173,66),
(174,66),
(175,66),
(176,66),
(177,66),
(178,66),
(179,66),
(180,66),
(181,66),
(182,66),
(183,66),
(184,66),
(185,66),
(186,66),
(187,66),
(188,66),
(189,66),
(190,66),
(191,66),
(192,66),
(193,66),
(194,66),
(195,66),
(196,66),
(197,66),
(198,66),
(199,66),
(200,66),
(201,66),
(202,66),
(203,66),
(204,66),
(205,66),
(206,66),
(207,66),
(208,66),
(209,66),
(210,66),
(211,66),
(212,66),
(213,66),
(214,66),
(215,66),
(216,66),
(217,66),
(218,66),
(219,66),
(220,66),
(221,66),
(222,66),
(223,66),
(224,66),
(225,66),
(226,66),
(227,66),
(228,66),
(229,66),
(230,66),
(231,66),
(232,66),
(233,66),
(234,66),
(235,66),
(236,66),
(119,67),
(120,67),
(121,67),
(122,67),
(123,67),
(124,67),
(125,67),
(126,67),
(127,67),
(128,67),
(129,67),
(130,67),
(131,67),
(132,67),
(133,67),
(134,67),
(135,67),
(136,67),
(137,67),
(138,67),
(139,67),
(140,67),
(141,67),
(142,67),
(143,67),
(144,67),
(145,67),
(146,67),
(147,67),
(148,67),
(149,67),
(150,67),
(151,67),
(152,67),
(153,67),
(154,67),
(155,67),
(156,67),
(157,67),
(158,67),
(159,67),
(160,67),
(161,67),
(162,67),
(163,67),
(164,67),
(165,67),
(166,67),
(167,67),
(168,67),
(169,67),
(170,67),
(171,67),
(172,67),
(173,67),
(174,67),
(175,67),
(176,67),
(177,67),
(178,67),
(179,67),
(180,67),
(181,67),
(182,67),
(183,67),
(184,67),
(185,67),
(186,67),
(187,67),
(188,67),
(189,67),
(190,67),
(191,67),
(192,67),
(193,67),
(194,67),
(195,67),
(196,67),
(197,67),
(198,67),
(199,67),
(200,67),
(201,67),
(202,67),
(203,67),
(204,67),
(205,67),
(206,67),
(207,67),
(208,67),
(209,67),
(210,67),
(211,67),
(212,67),
(213,67),
(214,67),
(215,67),
(216,67),
(217,67),
(218,67),
(219,67),
(220,67),
(221,67),
(222,67),
(223,67),
(224,67),
(225,67),
(226,67),
(227,67),
(228,67),
(229,67),
(230,67),
(231,67),
(232,67),
(233,67),
(234,67),
(235,67),
(236,67),
(132,68),
(134,68),
(138,68),
(142,68),
(143,68),
(144,68),
(147,68),
(150,68),
(151,68),
(152,68),
(153,68),
(154,68),
(155,68),
(156,68),
(157,68),
(158,68),
(159,68),
(160,68),
(161,68),
(162,68),
(163,68),
(164,68),
(170,68),
(209,68),
(210,68),
(211,68),
(214,68),
(215,68),
(218,68),
(220,68),
(230,68),
(132,69),
(134,69),
(138,69),
(142,69),
(164,69),
(183,69),
(184,69),
(185,69),
(186,69),
(187,69),
(188,69),
(189,69),
(190,69),
(191,69),
(192,69),
(193,69),
(194,69),
(209,69),
(210,69),
(211,69),
(212,69),
(214,69),
(215,69),
(216,69),
(218,69),
(221,69),
(230,69),
(126,70),
(132,70),
(134,70),
(135,70),
(136,70),
(137,70),
(138,70),
(139,70),
(140,70),
(141,70),
(142,70),
(143,70),
(144,70),
(145,70),
(146,70),
(147,70),
(148,70),
(149,70),
(209,70),
(210,70),
(211,70),
(212,70),
(214,70),
(215,70),
(216,70),
(218,70),
(223,70),
(226,70),
(228,70),
(230,70),
(132,71),
(150,71),
(164,71),
(165,71),
(166,71),
(167,71),
(168,71),
(169,71),
(170,71),
(171,71),
(172,71),
(173,71),
(174,71),
(175,71),
(176,71),
(177,71),
(178,71),
(179,71),
(180,71),
(181,71),
(182,71),
(190,71),
(209,71),
(210,71),
(211,71),
(212,71),
(214,71),
(215,71),
(218,71),
(219,71),
(230,71),
(132,72),
(134,72),
(138,72),
(142,72),
(143,72),
(144,72),
(150,72),
(152,72),
(153,72),
(156,72),
(157,72),
(164,72),
(190,72),
(209,72),
(210,72),
(214,72),
(218,72),
(220,72),
(132,73),
(142,73),
(143,73),
(144,73),
(183,73),
(184,73),
(185,73),
(186,73),
(187,73),
(188,73),
(190,73),
(191,73),
(193,73),
(194,73),
(209,73),
(210,73),
(211,73),
(214,73),
(221,73),
(150,74),
(152,74),
(153,74),
(154,74),
(155,74),
(156,74),
(157,74),
(158,74),
(159,74),
(164,74),
(209,74),
(214,74),
(220,74),
(134,75),
(142,75),
(150,75),
(151,75),
(152,75),
(153,75),
(154,75),
(155,75),
(156,75),
(157,75),
(158,75),
(159,75),
(164,75),
(209,75),
(214,75),
(220,75),
(150,76),
(151,76),
(152,76),
(153,76),
(154,76),
(155,76),
(156,76),
(157,76),
(158,76),
(159,76),
(164,76),
(150,77),
(152,77),
(153,77),
(156,77),
(157,77),
(159,77),
(164,77),
(220,77),
(150,78),
(152,78),
(153,78),
(156,78),
(157,78),
(159,78),
(164,78),
(220,78),
(150,79),
(152,79),
(153,79),
(156,79),
(164,80),
(183,80),
(184,80),
(186,80),
(188,80),
(190,80),
(194,80),
(221,80),
(132,81),
(134,81),
(142,81),
(164,81),
(165,81),
(166,81),
(167,81),
(170,81),
(181,81),
(183,81),
(184,81),
(185,81),
(186,81),
(187,81),
(188,81),
(190,81),
(191,81),
(193,81),
(194,81),
(209,81),
(210,81),
(211,81),
(214,81),
(219,81),
(221,81),
(222,81),
(164,82),
(183,82),
(184,82),
(186,82),
(190,82),
(194,82),
(214,82),
(132,83),
(134,83),
(138,83),
(142,83),
(143,83),
(144,83),
(164,83),
(183,83),
(184,83),
(185,83),
(186,83),
(187,83),
(188,83),
(189,83),
(190,83),
(191,83),
(193,83),
(194,83),
(209,83),
(210,83),
(211,83),
(214,83),
(218,83),
(221,83),
(164,84),
(183,84),
(184,84),
(186,84),
(188,84),
(190,84),
(194,84),
(209,84),
(214,84),
(164,85),
(183,85),
(184,85),
(186,85),
(190,85),
(194,85),
(164,86),
(165,86),
(166,86),
(167,86),
(170,86),
(172,86),
(176,86),
(178,86),
(179,86),
(181,86),
(209,86),
(210,86),
(214,86),
(164,87),
(165,87),
(166,87),
(170,87),
(219,87),
(126,88),
(132,88),
(134,88),
(136,88),
(138,88),
(139,88),
(140,88),
(143,88),
(144,88),
(145,88),
(147,88),
(149,88),
(209,88),
(210,88),
(214,88),
(223,88),
(226,88),
(228,88),
(230,88),
(134,89),
(143,89),
(164,89),
(214,89),
(218,89),
(147,90),
(170,90),
(188,90),
(209,90),
(210,90),
(212,90),
(214,90),
(216,90),
(218,90),
(230,90);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `role_migration_log`
--

DROP TABLE IF EXISTS `role_migration_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_migration_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `old_role` varchar(255) NOT NULL,
  `new_role` varchar(255) NOT NULL,
  `migrated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_migration_log_user_id_index` (`user_id`),
  KEY `role_migration_log_migrated_at_index` (`migrated_at`),
  CONSTRAINT `role_migration_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_migration_log`
--

LOCK TABLES `role_migration_log` WRITE;
/*!40000 ALTER TABLE `role_migration_log` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `role_migration_log` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `role_permission_audit_logs`
--

DROP TABLE IF EXISTS `role_permission_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `user_id` varchar(36) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `data` longtext NOT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `role_permission_audit_logs_model_type_index` (`model_type`),
  KEY `role_permission_audit_logs_model_id_index` (`model_id`),
  KEY `role_permission_audit_logs_user_id_index` (`user_id`),
  KEY `role_permission_audit_logs_action_index` (`action`),
  KEY `role_permission_audit_logs_is_protected_index` (`is_protected`),
  KEY `role_permission_audit_logs_created_at_index` (`created_at`),
  KEY `role_permission_audit_logs_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=328 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission_audit_logs`
--

LOCK TABLES `role_permission_audit_logs` WRITE;
/*!40000 ALTER TABLE `role_permission_audit_logs` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `role_permission_audit_logs` VALUES
(1,'role_created','Role',1,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access, all branches, all departments\"}',0,'2026-02-02 12:45:30'),
(2,'role_created','Role',2,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Branch-wide access, manages all departments in branch\"}',0,'2026-02-02 12:45:30'),
(3,'role_created','Role',3,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Department manager, full control of their department\"}',0,'2026-02-02 12:45:30'),
(4,'role_created','Role',4,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Department supervisor, limited management\"}',0,'2026-02-02 12:45:30'),
(5,'role_created','Role',5,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Department worker, basic operational access\"}',0,'2026-02-02 12:45:30'),
(6,'permission_created','Permission',1,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:54'),
(7,'permission_created','Permission',2,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:54'),
(8,'permission_created','Permission',3,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:54'),
(9,'permission_created','Permission',4,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:54'),
(10,'permission_created','Permission',5,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"assign-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:54'),
(11,'permission_created','Permission',6,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:54'),
(12,'permission_created','Permission',7,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:54'),
(13,'permission_created','Permission',8,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(14,'permission_created','Permission',9,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(15,'permission_created','Permission',10,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(16,'permission_created','Permission',11,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(17,'permission_created','Permission',12,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-settings\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(18,'permission_created','Permission',13,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-audit-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(19,'permission_created','Permission',14,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(20,'permission_created','Permission',15,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-system\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(21,'permission_created','Permission',16,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(22,'permission_created','Permission',17,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(23,'permission_created','Permission',18,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(24,'permission_created','Permission',19,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(25,'permission_created','Permission',20,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(26,'permission_created','Permission',21,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(27,'permission_created','Permission',22,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(28,'permission_created','Permission',23,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(29,'permission_created','Permission',24,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-staff-schedule\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:55'),
(30,'permission_created','Permission',25,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(31,'permission_created','Permission',26,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(32,'permission_created','Permission',27,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(33,'permission_created','Permission',28,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(34,'permission_created','Permission',29,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-hr-reports\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(35,'permission_created','Permission',30,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-roles-assignments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(36,'permission_created','Permission',31,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employee-details\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(37,'permission_created','Permission',32,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-queue\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(38,'permission_created','Permission',33,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(39,'permission_created','Permission',34,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"start-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(40,'permission_created','Permission',35,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"complete-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(41,'permission_created','Permission',36,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(42,'permission_created','Permission',37,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(43,'permission_created','Permission',38,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(44,'permission_created','Permission',39,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-reports\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:56'),
(45,'permission_created','Permission',40,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-quality-control\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(46,'permission_created','Permission',41,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-batch-history\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(47,'permission_created','Permission',42,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(48,'permission_created','Permission',43,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"cancel-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(49,'permission_created','Permission',44,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-cost\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(50,'permission_created','Permission',45,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-production-settings\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(51,'permission_created','Permission',46,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(52,'permission_created','Permission',47,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"receive-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(53,'permission_created','Permission',48,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"transfer-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(54,'permission_created','Permission',49,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"adjust-inventory\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(55,'permission_created','Permission',50,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(56,'permission_created','Permission',51,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(57,'permission_created','Permission',52,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-inventory-reports\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(58,'permission_created','Permission',53,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:57'),
(59,'permission_created','Permission',54,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(60,'permission_created','Permission',55,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(61,'permission_created','Permission',56,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(62,'permission_created','Permission',57,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(63,'permission_created','Permission',58,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-valuation\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(64,'permission_created','Permission',59,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-stock-categories\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(65,'permission_created','Permission',60,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(66,'permission_created','Permission',61,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(67,'permission_created','Permission',62,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"write-off-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(68,'permission_created','Permission',63,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-history\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:58'),
(69,'permission_created','Permission',64,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-inventory-settings\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(70,'permission_created','Permission',65,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-dashboard\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(71,'permission_created','Permission',66,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process-sale\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(72,'permission_created','Permission',67,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"issue-refund\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(73,'permission_created','Permission',68,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-daily-sales\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(74,'permission_created','Permission',69,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"close-register\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(75,'permission_created','Permission',70,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-reports\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(76,'permission_created','Permission',71,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-sales-discounts\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(77,'permission_created','Permission',72,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(78,'permission_created','Permission',73,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(79,'permission_created','Permission',74,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"void-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(80,'permission_created','Permission',75,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payment-methods\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(81,'permission_created','Permission',76,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-till-records\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 12:45:59'),
(82,'permission_created','Permission',77,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-chart-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(83,'permission_created','Permission',78,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(84,'permission_created','Permission',79,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(85,'permission_created','Permission',80,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(86,'permission_created','Permission',81,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(87,'permission_created','Permission',82,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"post-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(88,'permission_created','Permission',83,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reverse-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(89,'permission_created','Permission',84,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-accounting-reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(90,'permission_created','Permission',85,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(91,'permission_created','Permission',86,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-bank-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(92,'permission_created','Permission',87,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-trial-balance\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(93,'permission_created','Permission',88,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-financial-statements\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:00'),
(94,'permission_created','Permission',89,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-accounting-period\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(95,'permission_created','Permission',90,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-account-reconciliation\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(96,'permission_created','Permission',91,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-analytics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(97,'permission_created','Permission',92,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-department-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(98,'permission_created','Permission',93,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"generate-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(99,'permission_created','Permission',94,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(100,'permission_created','Permission',95,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"schedule-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(101,'permission_created','Permission',96,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-dashboard\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(102,'permission_created','Permission',97,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branch-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:01'),
(103,'permission_created','Permission',98,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-kpi-metrics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(104,'permission_created','Permission',99,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-data\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(105,'permission_created','Permission',100,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-timeline\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(106,'permission_created','Permission',101,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_inventory_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(107,'permission_created','Permission',102,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_production_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(108,'permission_created','Permission',103,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_sales_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(109,'permission_created','Permission',104,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_corner_store_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(110,'permission_created','Permission',105,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_hr_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(111,'permission_created','Permission',106,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 12:46:02'),
(112,'permission_created','Permission',107,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_super_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(113,'permission_created','Permission',108,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_organization\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(114,'permission_created','Permission',109,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_roles\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(115,'permission_created','Permission',110,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_branches\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(116,'permission_created','Permission',111,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_settings\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(117,'permission_created','Permission',112,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_reports\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(118,'permission_created','Permission',113,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"access_accounting\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(119,'permission_created','Permission',114,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_financial_reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(120,'permission_created','Permission',115,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:03'),
(121,'permission_created','Permission',116,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_periods\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:04'),
(122,'permission_created','Permission',117,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_journal_entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:04'),
(123,'permission_created','Permission',118,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile_bank_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 12:46:04'),
(124,'role_created','Role',6,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access with all permissions\"}',0,'2026-02-02 12:46:04'),
(125,'role_created','Role',7,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"MD\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director - Executive level\"}',0,'2026-02-02 12:46:04'),
(126,'role_created','Role',8,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Managing Director\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director with full operational control\"}',0,'2026-02-02 12:46:04'),
(127,'role_created','Role',9,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Administrative access\"}',0,'2026-02-02 12:46:04'),
(128,'role_created','Role',10,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Production\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Head of Production department\"}',0,'2026-02-02 12:46:05'),
(129,'role_created','Role',11,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Manager\"}',0,'2026-02-02 12:46:05'),
(130,'role_created','Role',12,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Human Resources Manager\"}',0,'2026-02-02 12:46:05'),
(131,'role_created','Role',13,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory and Stock Management\"}',0,'2026-02-02 12:46:06'),
(132,'role_created','Role',14,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Team Supervisor\"}',0,'2026-02-02 12:46:06'),
(133,'role_created','Role',15,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Till Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Till\\/Register Supervisor\"}',0,'2026-02-02 12:46:06'),
(134,'role_created','Role',16,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Chef\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Production Chef\"}',0,'2026-02-02 12:46:07'),
(135,'role_created','Role',17,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Gelato\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Lead\"}',0,'2026-02-02 12:46:07'),
(136,'role_created','Role',18,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Manager\"}',0,'2026-02-02 12:46:07'),
(137,'role_created','Role',19,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Kitchen Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Kitchen\\/Production Staff\"}',0,'2026-02-02 12:46:08'),
(138,'role_created','Role',20,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Gelato Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Staff\"}',0,'2026-02-02 12:46:08'),
(139,'role_created','Role',21,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Staff\"}',0,'2026-02-02 12:46:08'),
(140,'role_created','Role',22,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Cashier\"}',0,'2026-02-02 12:46:09'),
(141,'role_created','Role',23,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Manager\"}',0,'2026-02-02 12:46:09'),
(142,'role_created','Role',24,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Staff\"}',0,'2026-02-02 12:46:09'),
(143,'role_created','Role',25,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Department Supervisor\"}',0,'2026-02-02 12:46:10'),
(144,'role_created','Role',26,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Associate\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Associate\"}',0,'2026-02-02 12:46:10'),
(145,'role_created','Role',27,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Junior Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Junior Cashier\"}',0,'2026-02-02 12:46:10'),
(146,'role_created','Role',28,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Stock Controller\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Stock\\/Inventory Controller\"}',0,'2026-02-02 12:46:11'),
(147,'role_created','Role',29,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Keeper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Keeper\\/Warehouse Staff\"}',0,'2026-02-02 12:46:11'),
(148,'role_created','Role',30,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Officer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"HR Officer\"}',0,'2026-02-02 12:46:11'),
(149,'role_created','Role',31,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Employee\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Standard Employee\"}',0,'2026-02-02 12:46:12'),
(150,'role_created','Role',32,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Viewer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Read-only access to reports and dashboards\"}',0,'2026-02-02 12:46:12'),
(151,'role_created','Role',33,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access with all permissions\"}',0,'2026-02-02 12:46:16'),
(152,'role_created','Role',34,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"MD\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director - Executive level\"}',0,'2026-02-02 12:46:16'),
(153,'role_created','Role',35,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Managing Director\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director with full operational control\"}',0,'2026-02-02 12:46:16'),
(154,'role_created','Role',36,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Administrative access\"}',0,'2026-02-02 12:46:16'),
(155,'role_created','Role',37,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Production\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Head of Production department\"}',0,'2026-02-02 12:46:16'),
(156,'role_created','Role',38,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Manager\"}',0,'2026-02-02 12:46:17'),
(157,'role_created','Role',39,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Human Resources Manager\"}',0,'2026-02-02 12:46:17'),
(158,'role_created','Role',40,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory and Stock Management\"}',0,'2026-02-02 12:46:17'),
(159,'role_created','Role',41,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Team Supervisor\"}',0,'2026-02-02 12:46:17'),
(160,'role_created','Role',42,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Till Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Till\\/Register Supervisor\"}',0,'2026-02-02 12:46:18'),
(161,'role_created','Role',43,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Chef\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Production Chef\"}',0,'2026-02-02 12:46:18'),
(162,'role_created','Role',44,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Gelato\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Lead\"}',0,'2026-02-02 12:46:18'),
(163,'role_created','Role',45,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Manager\"}',0,'2026-02-02 12:46:18'),
(164,'role_created','Role',46,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Kitchen Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Kitchen\\/Production Staff\"}',0,'2026-02-02 12:46:19'),
(165,'role_created','Role',47,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Gelato Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Staff\"}',0,'2026-02-02 12:46:19'),
(166,'role_created','Role',48,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Staff\"}',0,'2026-02-02 12:46:19'),
(167,'role_created','Role',49,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Cashier\"}',0,'2026-02-02 12:46:20'),
(168,'role_created','Role',50,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Manager\"}',0,'2026-02-02 12:46:20'),
(169,'role_created','Role',51,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Staff\"}',0,'2026-02-02 12:46:20'),
(170,'role_created','Role',52,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Department Supervisor\"}',0,'2026-02-02 12:46:20'),
(171,'role_created','Role',53,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Associate\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Associate\"}',0,'2026-02-02 12:46:21'),
(172,'role_created','Role',54,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Junior Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Junior Cashier\"}',0,'2026-02-02 12:46:21'),
(173,'role_created','Role',55,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Stock Controller\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Stock\\/Inventory Controller\"}',0,'2026-02-02 12:46:21'),
(174,'role_created','Role',56,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Keeper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Keeper\\/Warehouse Staff\"}',0,'2026-02-02 12:46:22'),
(175,'role_created','Role',57,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Officer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"HR Officer\"}',0,'2026-02-02 12:46:22'),
(176,'role_created','Role',58,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Employee\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Standard Employee\"}',0,'2026-02-02 12:46:22'),
(177,'role_created','Role',59,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Viewer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Read-only access to reports and dashboards\"}',0,'2026-02-02 12:46:23'),
(178,'role_created','Role',60,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Warehouse Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Warehouse Manager with supervisory responsibilities\"}',0,'2026-02-02 12:46:53'),
(179,'role_created','Role',61,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Manager overseeing retail operations\"}',0,'2026-02-02 12:46:54'),
(180,'role_created','Role',62,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Supervisor assisting with daily operations\"}',0,'2026-02-02 12:46:54'),
(181,'role_created','Role',63,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Clerk\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory Clerk for data entry and basic inventory tasks\"}',0,'2026-02-02 12:46:55'),
(182,'permission_created','Permission',119,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:26'),
(183,'permission_created','Permission',120,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:26'),
(184,'permission_created','Permission',121,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(185,'permission_created','Permission',122,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(186,'permission_created','Permission',123,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"assign-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(187,'permission_created','Permission',124,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(188,'permission_created','Permission',125,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(189,'permission_created','Permission',126,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(190,'permission_created','Permission',127,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(191,'permission_created','Permission',128,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(192,'permission_created','Permission',129,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(193,'permission_created','Permission',130,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-settings\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(194,'permission_created','Permission',131,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-audit-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:27'),
(195,'permission_created','Permission',132,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(196,'permission_created','Permission',133,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-system\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(197,'permission_created','Permission',134,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(198,'permission_created','Permission',135,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(199,'permission_created','Permission',136,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(200,'permission_created','Permission',137,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(201,'permission_created','Permission',138,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(202,'permission_created','Permission',139,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(203,'permission_created','Permission',140,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(204,'permission_created','Permission',141,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(205,'permission_created','Permission',142,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-staff-schedule\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(206,'permission_created','Permission',143,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(207,'permission_created','Permission',144,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:28'),
(208,'permission_created','Permission',145,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(209,'permission_created','Permission',146,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(210,'permission_created','Permission',147,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-hr-reports\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(211,'permission_created','Permission',148,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-roles-assignments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(212,'permission_created','Permission',149,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employee-details\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(213,'permission_created','Permission',150,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-queue\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(214,'permission_created','Permission',151,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(215,'permission_created','Permission',152,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"start-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(216,'permission_created','Permission',153,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"complete-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(217,'permission_created','Permission',154,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(218,'permission_created','Permission',155,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:29'),
(219,'permission_created','Permission',156,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(220,'permission_created','Permission',157,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-reports\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(221,'permission_created','Permission',158,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-quality-control\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(222,'permission_created','Permission',159,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-batch-history\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(223,'permission_created','Permission',160,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(224,'permission_created','Permission',161,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"cancel-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(225,'permission_created','Permission',162,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-cost\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(226,'permission_created','Permission',163,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-production-settings\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(227,'permission_created','Permission',164,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(228,'permission_created','Permission',165,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"receive-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(229,'permission_created','Permission',166,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"transfer-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:30'),
(230,'permission_created','Permission',167,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"adjust-inventory\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(231,'permission_created','Permission',168,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(232,'permission_created','Permission',169,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(233,'permission_created','Permission',170,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-inventory-reports\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(234,'permission_created','Permission',171,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(235,'permission_created','Permission',172,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(236,'permission_created','Permission',173,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(237,'permission_created','Permission',174,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(238,'permission_created','Permission',175,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(239,'permission_created','Permission',176,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-valuation\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:31'),
(240,'permission_created','Permission',177,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-stock-categories\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(241,'permission_created','Permission',178,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(242,'permission_created','Permission',179,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(243,'permission_created','Permission',180,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"write-off-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(244,'permission_created','Permission',181,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-history\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(245,'permission_created','Permission',182,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-inventory-settings\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(246,'permission_created','Permission',183,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-dashboard\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(247,'permission_created','Permission',184,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process-sale\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(248,'permission_created','Permission',185,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"issue-refund\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:32'),
(249,'permission_created','Permission',186,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-daily-sales\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(250,'permission_created','Permission',187,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"close-register\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(251,'permission_created','Permission',188,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-reports\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(252,'permission_created','Permission',189,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-sales-discounts\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(253,'permission_created','Permission',190,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(254,'permission_created','Permission',191,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(255,'permission_created','Permission',192,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"void-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(256,'permission_created','Permission',193,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payment-methods\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(257,'permission_created','Permission',194,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-till-records\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(258,'permission_created','Permission',195,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-chart-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:33'),
(259,'permission_created','Permission',196,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(260,'permission_created','Permission',197,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(261,'permission_created','Permission',198,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(262,'permission_created','Permission',199,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(263,'permission_created','Permission',200,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"post-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(264,'permission_created','Permission',201,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reverse-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(265,'permission_created','Permission',202,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-accounting-reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(266,'permission_created','Permission',203,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(267,'permission_created','Permission',204,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-bank-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:34'),
(268,'permission_created','Permission',205,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-trial-balance\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(269,'permission_created','Permission',206,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-financial-statements\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(270,'permission_created','Permission',207,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-accounting-period\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(271,'permission_created','Permission',208,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-account-reconciliation\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(272,'permission_created','Permission',209,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-analytics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(273,'permission_created','Permission',210,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-department-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(274,'permission_created','Permission',211,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"generate-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(275,'permission_created','Permission',212,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(276,'permission_created','Permission',213,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"schedule-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(277,'permission_created','Permission',214,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-dashboard\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:35'),
(278,'permission_created','Permission',215,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branch-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(279,'permission_created','Permission',216,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-kpi-metrics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(280,'permission_created','Permission',217,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-data\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(281,'permission_created','Permission',218,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-timeline\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(282,'permission_created','Permission',219,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_inventory_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(283,'permission_created','Permission',220,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_production_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(284,'permission_created','Permission',221,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_sales_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(285,'permission_created','Permission',222,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_corner_store_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(286,'permission_created','Permission',223,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_hr_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 13:08:36'),
(287,'permission_created','Permission',224,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 13:08:37'),
(288,'permission_created','Permission',225,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_super_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-02-02 13:08:37'),
(289,'permission_created','Permission',226,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_organization\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 13:08:37'),
(290,'permission_created','Permission',227,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_roles\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 13:08:37'),
(291,'permission_created','Permission',228,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_branches\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 13:08:37'),
(292,'permission_created','Permission',229,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_settings\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 13:08:37'),
(293,'permission_created','Permission',230,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_reports\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-02-02 13:08:37'),
(294,'permission_created','Permission',231,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"access_accounting\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:38'),
(295,'permission_created','Permission',232,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_financial_reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:38'),
(296,'permission_created','Permission',233,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:38'),
(297,'permission_created','Permission',234,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_periods\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:38'),
(298,'permission_created','Permission',235,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_journal_entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:38'),
(299,'permission_created','Permission',236,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile_bank_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-02-02 13:08:38'),
(300,'role_created','Role',64,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access with all permissions\"}',0,'2026-02-02 13:08:38'),
(301,'role_created','Role',65,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"MD\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director - Executive level\"}',0,'2026-02-02 13:08:38'),
(302,'role_created','Role',66,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Managing Director\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director with full operational control\"}',0,'2026-02-02 13:08:38'),
(303,'role_created','Role',67,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Administrative access\"}',0,'2026-02-02 13:08:38'),
(304,'role_created','Role',68,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Production\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Head of Production department\"}',0,'2026-02-02 13:08:39'),
(305,'role_created','Role',69,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Manager\"}',0,'2026-02-02 13:08:39'),
(306,'role_created','Role',70,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Human Resources Manager\"}',0,'2026-02-02 13:08:39'),
(307,'role_created','Role',71,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory and Stock Management\"}',0,'2026-02-02 13:08:40'),
(308,'role_created','Role',72,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Team Supervisor\"}',0,'2026-02-02 13:08:40'),
(309,'role_created','Role',73,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Till Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Till\\/Register Supervisor\"}',0,'2026-02-02 13:08:40'),
(310,'role_created','Role',74,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Chef\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Production Chef\"}',0,'2026-02-02 13:08:41'),
(311,'role_created','Role',75,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Gelato\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Lead\"}',0,'2026-02-02 13:08:41'),
(312,'role_created','Role',76,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Manager\"}',0,'2026-02-02 13:08:41'),
(313,'role_created','Role',77,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Kitchen Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Kitchen\\/Production Staff\"}',0,'2026-02-02 13:08:41'),
(314,'role_created','Role',78,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Gelato Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Staff\"}',0,'2026-02-02 13:08:42'),
(315,'role_created','Role',79,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Staff\"}',0,'2026-02-02 13:08:42'),
(316,'role_created','Role',80,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Cashier\"}',0,'2026-02-02 13:08:43'),
(317,'role_created','Role',81,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Manager\"}',0,'2026-02-02 13:08:43'),
(318,'role_created','Role',82,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Staff\"}',0,'2026-02-02 13:08:44'),
(319,'role_created','Role',83,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Department Supervisor\"}',0,'2026-02-02 13:08:44'),
(320,'role_created','Role',84,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Associate\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Associate\"}',0,'2026-02-02 13:08:44'),
(321,'role_created','Role',85,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Junior Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Junior Cashier\"}',0,'2026-02-02 13:08:45'),
(322,'role_created','Role',86,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Stock Controller\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Stock\\/Inventory Controller\"}',0,'2026-02-02 13:08:45'),
(323,'role_created','Role',87,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Keeper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Keeper\\/Warehouse Staff\"}',0,'2026-02-02 13:08:46'),
(324,'role_created','Role',88,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Officer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"HR Officer\"}',0,'2026-02-02 13:08:46'),
(325,'role_created','Role',89,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Employee\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Standard Employee\"}',0,'2026-02-02 13:08:46'),
(326,'role_created','Role',90,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Viewer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Read-only access to reports and dashboards\"}',0,'2026-02-02 13:08:47'),
(327,'role_created','Role',91,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Accounting Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":null}',0,'2026-02-02 14:13:31');
/*!40000 ALTER TABLE `role_permission_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT 1,
  `is_protected` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_is_protected_index` (`is_protected`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles` VALUES
(64,'Super Admin','web',5,0,'Full system access with all permissions',1,'2026-02-02 13:08:38','2026-02-02 13:08:38'),
(65,'MD','web',5,0,'Managing Director - Executive level',2,'2026-02-02 13:08:38','2026-02-02 13:08:38'),
(66,'Managing Director','web',5,0,'Managing Director with full operational control',3,'2026-02-02 13:08:38','2026-02-02 13:08:38'),
(67,'Admin','web',4,0,'Administrative access',4,'2026-02-02 13:08:38','2026-02-02 13:08:38'),
(68,'Head of Production','web',3,0,'Head of Production department',10,'2026-02-02 13:08:39','2026-02-02 13:08:39'),
(69,'Sales Manager','web',3,0,'Sales Manager',11,'2026-02-02 13:08:39','2026-02-02 13:08:39'),
(70,'HR Manager','web',3,0,'Human Resources Manager',12,'2026-02-02 13:08:39','2026-02-02 13:08:39'),
(71,'Inventory Manager','web',3,0,'Inventory and Stock Management',13,'2026-02-02 13:08:40','2026-02-02 13:08:40'),
(72,'Supervisor','web',2,0,'Team Supervisor',20,'2026-02-02 13:08:40','2026-02-02 13:08:40'),
(73,'Till Supervisor','web',2,0,'Till/Register Supervisor',21,'2026-02-02 13:08:40','2026-02-02 13:08:40'),
(74,'Chef','web',3,0,'Production Chef',30,'2026-02-02 13:08:41','2026-02-02 13:08:41'),
(75,'Head of Gelato','web',3,0,'Gelato Production Lead',31,'2026-02-02 13:08:41','2026-02-02 13:08:41'),
(76,'Confectioneries Manager','web',3,0,'Confectioneries Production Manager',32,'2026-02-02 13:08:41','2026-02-02 13:08:41'),
(77,'Kitchen Staff','web',1,0,'Kitchen/Production Staff',40,'2026-02-02 13:08:41','2026-02-02 13:08:41'),
(78,'Gelato Production Staff','web',1,0,'Gelato Production Staff',41,'2026-02-02 13:08:42','2026-02-02 13:08:42'),
(79,'Confectioneries Production Staff','web',1,0,'Confectioneries Production Staff',42,'2026-02-02 13:08:42','2026-02-02 13:08:42'),
(80,'Cashier','web',1,0,'Sales Cashier',43,'2026-02-02 13:08:43','2026-02-02 13:08:43'),
(81,'Corner Store Manager','web',3,0,'Corner Store Manager',44,'2026-02-02 13:08:43','2026-02-02 13:08:43'),
(82,'Corner Store Staff','web',1,0,'Corner Store Staff',45,'2026-02-02 13:08:44','2026-02-02 13:08:44'),
(83,'Sales Supervisor','web',2,0,'Sales Department Supervisor',46,'2026-02-02 13:08:44','2026-02-02 13:08:44'),
(84,'Sales Associate','web',1,0,'Sales Associate',47,'2026-02-02 13:08:44','2026-02-02 13:08:44'),
(85,'Junior Cashier','web',1,0,'Junior Cashier',48,'2026-02-02 13:08:45','2026-02-02 13:08:45'),
(86,'Stock Controller','web',2,0,'Stock/Inventory Controller',49,'2026-02-02 13:08:45','2026-02-02 13:08:45'),
(87,'Store Keeper','web',1,0,'Store Keeper/Warehouse Staff',50,'2026-02-02 13:08:45','2026-02-02 13:08:45'),
(88,'HR Officer','web',1,0,'HR Officer',51,'2026-02-02 13:08:46','2026-02-02 13:08:46'),
(89,'Employee','web',1,0,'Standard Employee',52,'2026-02-02 13:08:46','2026-02-02 13:08:46'),
(90,'Viewer','web',1,0,'Read-only access to reports and dashboards',99,'2026-02-02 13:08:47','2026-02-02 13:08:47'),
(91,'Accounting Manager','web',4,0,NULL,0,'2026-02-02 14:13:31','2026-02-02 14:13:31');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `salary_history`
--

DROP TABLE IF EXISTS `salary_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `previous_salary` decimal(12,2) DEFAULT NULL,
  `new_salary` decimal(12,2) NOT NULL,
  `change_amount` decimal(12,2) NOT NULL,
  `change_percentage` decimal(8,2) NOT NULL,
  `effective_date` date NOT NULL,
  `change_type` enum('initial','increment','promotion','adjustment','bonus','deduction') NOT NULL DEFAULT 'increment',
  `reason` text NOT NULL,
  `approved_by_id` char(36) DEFAULT NULL,
  `approved_by_type` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_history_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_history_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_history`
--

LOCK TABLES `salary_history` WRITE;
/*!40000 ALTER TABLE `salary_history` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `salary_history` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
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
  KEY `sale_items_department_id_foreign` (`department_id`),
  CONSTRAINT `sale_items_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_shift_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `sold_by_id` char(36) NOT NULL,
  `sold_by_type` varchar(255) NOT NULL,
  `sale_number` varchar(255) NOT NULL,
  `sale_time` timestamp NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bank_account_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Link to bank account for cash sales',
  `gl_posting_status` enum('pending','posted','failed') NOT NULL DEFAULT 'pending' COMMENT 'GL posting status for accounting integration',
  `gl_posted_at` timestamp NULL DEFAULT NULL COMMENT 'When the GL entries were posted',
  `gl_posting_error` text DEFAULT NULL COMMENT 'Error message if GL posting failed',
  `status` enum('pending','completed','cancelled','refunded','hold') NOT NULL DEFAULT 'completed',
  `order_type` enum('dine-in','takeaway','delivery','dine_in','glovo','transfer') NOT NULL DEFAULT 'dine_in',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sales_order_id` bigint(20) unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_sale_number_unique` (`sale_number`),
  KEY `sales_sales_shift_id_foreign` (`sales_shift_id`),
  KEY `sales_department_id_foreign` (`department_id`),
  KEY `sales_branch_id_department_id_sale_time_index` (`branch_id`,`department_id`,`sale_time`),
  KEY `sales_table_id_foreign` (`table_id`),
  KEY `sales_sales_order_id_foreign` (`sales_order_id`),
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`),
  CONSTRAINT `sales_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sales_order_items`
--

DROP TABLE IF EXISTS `sales_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity_ordered` decimal(15,4) NOT NULL,
  `quantity_delivered` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `quantity_invoiced` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_items_sales_order_id_foreign` (`sales_order_id`),
  KEY `sales_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sales_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sales_order_items_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_items`
--

LOCK TABLES `sales_order_items` WRITE;
/*!40000 ALTER TABLE `sales_order_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sales_order_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sales_orders`
--

DROP TABLE IF EXISTS `sales_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `sales_quote_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `order_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_orders_order_number_unique` (`order_number`),
  KEY `sales_orders_sales_quote_id_foreign` (`sales_quote_id`),
  KEY `sales_orders_branch_id_foreign` (`branch_id`),
  KEY `sales_orders_created_by_foreign` (`created_by`),
  CONSTRAINT `sales_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `sales_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_orders_sales_quote_id_foreign` FOREIGN KEY (`sales_quote_id`) REFERENCES `sales_quotes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_orders`
--

LOCK TABLES `sales_orders` WRITE;
/*!40000 ALTER TABLE `sales_orders` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sales_orders` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sales_quote_items`
--

DROP TABLE IF EXISTS `sales_quote_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_quote_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_quote_id` bigint(20) unsigned NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_quote_items_sales_quote_id_foreign` (`sales_quote_id`),
  KEY `sales_quote_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sales_quote_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sales_quote_items_sales_quote_id_foreign` FOREIGN KEY (`sales_quote_id`) REFERENCES `sales_quotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_quote_items`
--

LOCK TABLES `sales_quote_items` WRITE;
/*!40000 ALTER TABLE `sales_quote_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sales_quote_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sales_quotes`
--

DROP TABLE IF EXISTS `sales_quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_quotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quote_number` varchar(255) NOT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `quote_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_quotes_quote_number_unique` (`quote_number`),
  KEY `sales_quotes_branch_id_foreign` (`branch_id`),
  KEY `sales_quotes_created_by_foreign` (`created_by`),
  CONSTRAINT `sales_quotes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `sales_quotes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_quotes`
--

LOCK TABLES `sales_quotes` WRITE;
/*!40000 ALTER TABLE `sales_quotes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sales_quotes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sales_shifts`
--

DROP TABLE IF EXISTS `sales_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `verified_by_id` char(36) DEFAULT NULL,
  `verified_by_type` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_shifts_shift_number_unique` (`shift_number`),
  KEY `sales_shifts_branch_id_foreign` (`branch_id`),
  KEY `sales_shifts_department_id_foreign` (`department_id`),
  KEY `sales_shifts_employee_id_foreign` (`employee_id`),
  CONSTRAINT `sales_shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_shifts_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_shifts`
--

LOCK TABLES `sales_shifts` WRITE;
/*!40000 ALTER TABLE `sales_shifts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sales_shifts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sales_to_production_workflow`
--

DROP TABLE IF EXISTS `sales_to_production_workflow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_to_production_workflow` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_to_production_workflow`
--

LOCK TABLES `sales_to_production_workflow` WRITE;
/*!40000 ALTER TABLE `sales_to_production_workflow` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sales_to_production_workflow` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_foreign` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `shift_configurations`
--

DROP TABLE IF EXISTS `shift_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shift_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `shift_type` enum('morning','afternoon','night','full_time') NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `clock_in_start` time NOT NULL,
  `clock_in_end` time NOT NULL,
  `auto_clock_out_minutes` int(11) NOT NULL DEFAULT 15,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `max_overtime_hours` decimal(4,2) NOT NULL DEFAULT 2.00,
  `break_duration_minutes` int(11) NOT NULL DEFAULT 60,
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shift_configurations_branch_id_shift_type_is_active_unique` (`branch_id`,`shift_type`,`is_active`),
  KEY `shift_configurations_branch_id_is_active_index` (`branch_id`,`is_active`),
  KEY `shift_configurations_shift_type_index` (`shift_type`),
  KEY `shift_configurations_timezone_index` (`timezone`),
  CONSTRAINT `shift_configurations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shift_configurations`
--

LOCK TABLES `shift_configurations` WRITE;
/*!40000 ALTER TABLE `shift_configurations` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `shift_configurations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `employee_id` char(36) DEFAULT NULL,
  `shift_number` varchar(255) NOT NULL,
  `shift_date` date NOT NULL,
  `shift_type` enum('morning','afternoon','night') NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `status` enum('active','closed','submitted') NOT NULL DEFAULT 'active',
  `workflow_state` enum('clock_in','stock_opening','pos','clock_out','shift_closing','completed') NOT NULL DEFAULT 'clock_in',
  `notes` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `stock_verified_at` timestamp NULL DEFAULT NULL,
  `shift_closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `auto_clocked_out_at` timestamp NULL DEFAULT NULL,
  `auto_clock_out_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shifts_shift_number_unique` (`shift_number`),
  KEY `shifts_department_id_foreign` (`department_id`),
  KEY `shifts_branch_id_department_id_shift_date_index` (`branch_id`,`department_id`,`shift_date`),
  KEY `shifts_employee_id_index` (`employee_id`),
  KEY `shifts_employee_date_workflow_idx` (`employee_id`,`shift_date`,`workflow_state`),
  CONSTRAINT `shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shifts_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` bigint(20) unsigned NOT NULL,
  `type` enum('in','out','adjustment','transfer','damaged','return') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `quantity_before` decimal(12,2) NOT NULL,
  `quantity_after` decimal(12,2) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `moved_by_type` varchar(255) DEFAULT NULL,
  `moved_by_id` char(36) DEFAULT NULL,
  `shift` varchar(255) DEFAULT NULL COMMENT 'Denormalized from ItemRequest or other reference',
  `department_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Denormalized from ItemRequest or reference',
  `notes` text DEFAULT NULL,
  `movement_date` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_account_id` bigint(20) unsigned DEFAULT NULL,
  `gl_posting_status` enum('pending','posted','failed') NOT NULL DEFAULT 'pending',
  `gl_posting_error` text DEFAULT NULL,
  `gl_posted_at` timestamp NULL DEFAULT NULL,
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_moved_by_type_moved_by_id_index` (`moved_by_type`,`moved_by_id`),
  KEY `idx_movement_shift_date` (`shift`,`movement_date`),
  KEY `idx_movement_department_date` (`department_id`,`movement_date`),
  KEY `idx_movement_stock_id` (`stock_id`),
  KEY `idx_movement_type` (`type`),
  KEY `idx_movement_date` (`movement_date`),
  KEY `idx_movement_moved_by` (`moved_by_id`),
  KEY `idx_movement_reference_type` (`reference_type`),
  KEY `idx_movement_date_type` (`movement_date`,`type`),
  KEY `idx_movement_stock_date` (`stock_id`,`movement_date`),
  KEY `stock_movements_bank_account_id_index` (`bank_account_id`),
  KEY `stock_movements_gl_entry_id_foreign` (`gl_entry_id`),
  KEY `stock_movements_branch_id_index` (`branch_id`),
  CONSTRAINT `stock_movements_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `stock_take_details`
--

DROP TABLE IF EXISTS `stock_take_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
/*!40000 ALTER TABLE `stock_take_details` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `stock_takes`
--

DROP TABLE IF EXISTS `stock_takes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_takes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) NOT NULL,
  `stock_take_number` varchar(255) NOT NULL,
  `stock_take_date` date NOT NULL,
  `type` enum('daily','weekly','monthly','annual','ad_hoc') NOT NULL,
  `conducted_by_id` char(36) NOT NULL,
  `conducted_by_type` varchar(255) NOT NULL,
  `status` enum('in_progress','completed','verified') NOT NULL DEFAULT 'in_progress',
  `verified_by_id` char(36) DEFAULT NULL,
  `verified_by_type` varchar(255) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_takes_stock_take_number_unique` (`stock_take_number`),
  KEY `stock_takes_branch_id_foreign` (`branch_id`),
  CONSTRAINT `stock_takes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_takes`
--

LOCK TABLES `stock_takes` WRITE;
/*!40000 ALTER TABLE `stock_takes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `stock_takes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
set autocommit=0;
INSERT INTO `stocks` VALUES
(1,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,266.00,24.00,12.00,453.1000,'2026-01-09','critical','2026-03-01','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(2,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',2,301.00,23.00,5.00,474.4000,'2026-01-17','good','2026-09-09','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(3,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',3,347.00,8.00,5.00,301.0000,'2026-01-03','critical','2026-03-03','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(4,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',4,313.00,1.00,12.00,479.6000,'2026-01-14','warning','2026-03-12','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(5,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',5,221.00,12.00,3.00,66.8000,'2026-01-12','good','2026-07-21','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(6,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',6,394.00,7.00,10.00,392.6000,'2026-01-28','critical','2026-02-16','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(7,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',7,91.00,5.00,0.00,464.5000,'2026-01-23','good','2026-09-02','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(8,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',8,219.00,0.00,8.00,200.3000,'2026-01-31','good','2026-08-25','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(9,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',9,296.00,7.00,7.00,147.9000,'2026-01-26','warning','2026-03-20','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(10,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',10,358.00,10.00,1.00,457.1000,'2026-01-21','good','2026-10-06','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(11,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',11,173.00,17.00,1.00,136.1000,'2026-01-09','good','2026-12-06','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(12,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',12,193.00,9.00,1.00,264.3000,'2026-01-17','good','2026-08-28','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(13,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',13,621.00,14.00,12.00,34.5000,'2026-01-24','good',NULL,'2026-02-02 12:46:45','2026-02-02 12:46:45'),
(14,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',14,421.00,10.00,3.00,13.3000,'2026-01-12','good',NULL,'2026-02-02 12:46:45','2026-02-02 12:46:45'),
(15,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',15,131.00,12.00,4.00,44.2000,'2026-01-17','warning',NULL,'2026-02-02 12:46:45','2026-02-02 12:46:45'),
(16,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',16,101.00,8.00,5.00,8.5000,'2026-01-24','critical',NULL,'2026-02-02 12:46:45','2026-02-02 12:46:45'),
(17,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',17,75.00,7.00,0.00,281.2000,'2026-01-22','good','2026-12-11','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(18,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',18,140.00,1.00,1.00,98.7000,'2026-01-19','good','2026-07-10','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(19,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',19,105.00,4.00,4.00,285.0000,'2026-01-15','good','2026-08-12','2026-02-02 12:46:45','2026-02-02 12:46:45'),
(20,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',20,24.00,2.00,0.00,1101.1000,'2026-01-14','good',NULL,'2026-02-02 12:46:45','2026-02-02 12:46:45'),
(21,'019c1e63-b2f6-729f-bd59-bbe4340497fe',21,115.00,8.00,5.00,280.1000,'2026-01-09','warning','2026-03-15','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(22,'019c1e63-b2f6-729f-bd59-bbe4340497fe',22,323.00,18.00,0.00,122.2000,'2026-01-11','critical','2026-02-23','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(23,'019c1e63-b2f6-729f-bd59-bbe4340497fe',23,85.00,5.00,4.00,173.6000,'2026-01-04','good','2026-11-01','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(24,'019c1e63-b2f6-729f-bd59-bbe4340497fe',24,150.00,8.00,0.00,296.8000,'2026-01-06','good','2026-09-16','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(25,'019c1e63-b2f6-729f-bd59-bbe4340497fe',25,396.00,13.00,16.00,260.2000,'2026-01-15','good','2026-07-08','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(26,'019c1e63-b2f6-729f-bd59-bbe4340497fe',26,92.00,9.00,2.00,110.5000,'2026-01-18','good','2026-09-05','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(27,'019c1e63-b2f6-729f-bd59-bbe4340497fe',27,103.00,4.00,5.00,425.8000,'2026-01-28','good','2026-07-02','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(28,'019c1e63-b2f6-729f-bd59-bbe4340497fe',28,161.00,0.00,2.00,124.1000,'2026-01-21','good','2026-09-13','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(29,'019c1e63-b2f6-729f-bd59-bbe4340497fe',29,297.00,18.00,0.00,347.6000,'2026-01-05','warning','2026-03-11','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(30,'019c1e63-b2f6-729f-bd59-bbe4340497fe',30,373.00,19.00,6.00,345.7000,'2026-01-19','warning','2026-04-12','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(31,'019c1e63-b2f6-729f-bd59-bbe4340497fe',31,365.00,22.00,3.00,314.5000,'2026-01-18','good','2026-05-22','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(32,'019c1e63-b2f6-729f-bd59-bbe4340497fe',32,356.00,23.00,11.00,373.5000,'2026-01-21','warning','2026-03-10','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(33,'019c1e63-b2f6-729f-bd59-bbe4340497fe',33,232.00,20.00,6.00,16.0000,'2026-01-15','good',NULL,'2026-02-02 12:46:46','2026-02-02 12:46:46'),
(34,'019c1e63-b2f6-729f-bd59-bbe4340497fe',34,198.00,10.00,0.00,43.0000,'2026-01-17','good',NULL,'2026-02-02 12:46:46','2026-02-02 12:46:46'),
(35,'019c1e63-b2f6-729f-bd59-bbe4340497fe',35,514.00,5.00,13.00,32.9000,'2026-01-13','good',NULL,'2026-02-02 12:46:46','2026-02-02 12:46:46'),
(36,'019c1e63-b2f6-729f-bd59-bbe4340497fe',36,478.00,27.00,1.00,18.3000,'2026-01-19','warning',NULL,'2026-02-02 12:46:46','2026-02-02 12:46:46'),
(37,'019c1e63-b2f6-729f-bd59-bbe4340497fe',37,124.00,9.00,6.00,98.3000,'2026-01-17','warning','2026-03-12','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(38,'019c1e63-b2f6-729f-bd59-bbe4340497fe',38,64.00,0.00,3.00,250.1000,'2026-01-05','good','2026-11-16','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(39,'019c1e63-b2f6-729f-bd59-bbe4340497fe',39,32.00,2.00,0.00,203.1000,'2026-01-05','good','2026-08-26','2026-02-02 12:46:46','2026-02-02 12:46:46'),
(40,'019c1e63-b2f6-729f-bd59-bbe4340497fe',40,24.00,2.00,0.00,480.6000,'2026-01-04','warning',NULL,'2026-02-02 12:46:46','2026-02-02 12:46:46'),
(41,'019c1e63-b300-7276-abd5-9a31664cea89',41,192.00,19.00,6.00,287.6000,'2026-01-18','warning','2026-03-13','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(42,'019c1e63-b300-7276-abd5-9a31664cea89',42,335.00,24.00,1.00,434.3000,'2026-01-24','good','2026-08-27','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(43,'019c1e63-b300-7276-abd5-9a31664cea89',43,84.00,3.00,1.00,206.6000,'2026-01-31','critical','2026-02-24','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(44,'019c1e63-b300-7276-abd5-9a31664cea89',44,124.00,10.00,1.00,268.0000,'2026-01-21','good','2026-12-01','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(45,'019c1e63-b300-7276-abd5-9a31664cea89',45,393.00,37.00,19.00,409.8000,'2026-01-13','good','2027-01-01','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(46,'019c1e63-b300-7276-abd5-9a31664cea89',46,381.00,37.00,8.00,183.0000,'2026-01-24','good','2026-10-15','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(47,'019c1e63-b300-7276-abd5-9a31664cea89',47,103.00,6.00,2.00,165.2000,'2026-02-01','good','2026-12-12','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(48,'019c1e63-b300-7276-abd5-9a31664cea89',48,60.00,4.00,3.00,437.6000,'2026-01-03','good','2026-12-20','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(49,'019c1e63-b300-7276-abd5-9a31664cea89',49,291.00,29.00,3.00,190.9000,'2026-01-12','good','2026-11-25','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(50,'019c1e63-b300-7276-abd5-9a31664cea89',50,65.00,4.00,2.00,131.7000,'2026-01-22','warning','2026-04-13','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(51,'019c1e63-b300-7276-abd5-9a31664cea89',51,173.00,3.00,6.00,91.5000,'2026-01-03','critical','2026-02-17','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(52,'019c1e63-b300-7276-abd5-9a31664cea89',52,199.00,19.00,2.00,398.6000,'2026-01-23','good','2026-11-18','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(53,'019c1e63-b300-7276-abd5-9a31664cea89',53,172.00,8.00,4.00,49.0000,'2026-01-30','critical',NULL,'2026-02-02 12:46:47','2026-02-02 12:46:47'),
(54,'019c1e63-b300-7276-abd5-9a31664cea89',54,617.00,45.00,27.00,16.0000,'2026-01-30','good',NULL,'2026-02-02 12:46:47','2026-02-02 12:46:47'),
(55,'019c1e63-b300-7276-abd5-9a31664cea89',55,536.00,35.00,23.00,42.9000,'2026-01-16','good',NULL,'2026-02-02 12:46:47','2026-02-02 12:46:47'),
(56,'019c1e63-b300-7276-abd5-9a31664cea89',56,384.00,22.00,7.00,41.8000,'2026-01-21','good',NULL,'2026-02-02 12:46:47','2026-02-02 12:46:47'),
(57,'019c1e63-b300-7276-abd5-9a31664cea89',57,66.00,5.00,2.00,279.8000,'2026-01-05','good','2026-06-15','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(58,'019c1e63-b300-7276-abd5-9a31664cea89',58,101.00,0.00,1.00,254.6000,'2026-01-26','good','2026-08-17','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(59,'019c1e63-b300-7276-abd5-9a31664cea89',59,90.00,2.00,2.00,52.4000,'2026-01-18','good','2027-01-10','2026-02-02 12:46:47','2026-02-02 12:46:47'),
(60,'019c1e63-b300-7276-abd5-9a31664cea89',60,18.00,0.00,0.00,1264.3000,'2026-01-27','good',NULL,'2026-02-02 12:46:48','2026-02-02 12:46:48'),
(61,'019c1e63-b321-71ba-a153-c8fc9e31bcab',61,362.00,1.00,4.00,272.6000,'2026-01-04','critical','2026-02-20','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(62,'019c1e63-b321-71ba-a153-c8fc9e31bcab',62,348.00,30.00,6.00,264.8000,'2026-01-19','good','2026-06-17','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(63,'019c1e63-b321-71ba-a153-c8fc9e31bcab',63,354.00,24.00,14.00,258.2000,'2026-01-22','warning','2026-03-19','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(64,'019c1e63-b321-71ba-a153-c8fc9e31bcab',64,102.00,3.00,0.00,391.3000,'2026-01-10','good','2026-08-22','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(65,'019c1e63-b321-71ba-a153-c8fc9e31bcab',65,397.00,2.00,10.00,236.0000,'2026-01-13','good','2026-08-10','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(66,'019c1e63-b321-71ba-a153-c8fc9e31bcab',66,87.00,5.00,4.00,422.5000,'2026-01-15','good','2026-09-17','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(67,'019c1e63-b321-71ba-a153-c8fc9e31bcab',67,284.00,5.00,11.00,402.3000,'2026-01-21','good','2026-07-04','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(68,'019c1e63-b321-71ba-a153-c8fc9e31bcab',68,165.00,14.00,6.00,101.7000,'2026-01-21','good','2026-12-29','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(69,'019c1e63-b321-71ba-a153-c8fc9e31bcab',69,372.00,4.00,15.00,259.0000,'2026-01-17','warning','2026-04-02','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(70,'019c1e63-b321-71ba-a153-c8fc9e31bcab',70,313.00,14.00,9.00,247.6000,'2026-01-15','good','2026-09-29','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(71,'019c1e63-b321-71ba-a153-c8fc9e31bcab',71,269.00,25.00,0.00,224.4000,'2026-01-16','good','2026-08-09','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(72,'019c1e63-b321-71ba-a153-c8fc9e31bcab',72,360.00,25.00,0.00,188.8000,'2026-01-24','good','2026-09-03','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(73,'019c1e63-b321-71ba-a153-c8fc9e31bcab',73,612.00,3.00,4.00,26.6000,'2026-02-01','good',NULL,'2026-02-02 12:46:48','2026-02-02 12:46:48'),
(74,'019c1e63-b321-71ba-a153-c8fc9e31bcab',74,152.00,13.00,2.00,20.1000,'2026-01-09','good',NULL,'2026-02-02 12:46:48','2026-02-02 12:46:48'),
(75,'019c1e63-b321-71ba-a153-c8fc9e31bcab',75,619.00,29.00,16.00,49.5000,'2026-01-22','good',NULL,'2026-02-02 12:46:48','2026-02-02 12:46:48'),
(76,'019c1e63-b321-71ba-a153-c8fc9e31bcab',76,607.00,9.00,5.00,12.3000,'2026-01-30','good',NULL,'2026-02-02 12:46:48','2026-02-02 12:46:48'),
(77,'019c1e63-b321-71ba-a153-c8fc9e31bcab',77,95.00,0.00,4.00,184.3000,'2026-01-03','critical','2026-02-27','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(78,'019c1e63-b321-71ba-a153-c8fc9e31bcab',78,22.00,0.00,1.00,249.5000,'2026-01-20','good','2026-12-02','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(79,'019c1e63-b321-71ba-a153-c8fc9e31bcab',79,113.00,1.00,4.00,48.1000,'2026-01-18','warning','2026-03-20','2026-02-02 12:46:48','2026-02-02 12:46:48'),
(80,'019c1e63-b321-71ba-a153-c8fc9e31bcab',80,12.00,1.00,0.00,655.2000,'2026-01-11','good',NULL,'2026-02-02 12:46:48','2026-02-02 12:46:48'),
(81,'019c1e63-b32d-72bd-847a-c1c17e7b8674',81,160.00,2.00,8.00,50.6000,'2026-01-24','good','2026-06-30','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(82,'019c1e63-b32d-72bd-847a-c1c17e7b8674',82,250.00,7.00,0.00,450.6000,'2026-01-27','good','2026-11-26','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(83,'019c1e63-b32d-72bd-847a-c1c17e7b8674',83,265.00,25.00,9.00,275.9000,'2026-01-20','good','2026-06-17','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(84,'019c1e63-b32d-72bd-847a-c1c17e7b8674',84,54.00,4.00,2.00,442.5000,'2026-01-14','good','2026-05-09','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(85,'019c1e63-b32d-72bd-847a-c1c17e7b8674',85,271.00,25.00,13.00,289.3000,'2026-01-09','good','2026-10-29','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(86,'019c1e63-b32d-72bd-847a-c1c17e7b8674',86,374.00,14.00,13.00,128.2000,'2026-01-21','warning','2026-04-11','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(87,'019c1e63-b32d-72bd-847a-c1c17e7b8674',87,393.00,3.00,2.00,246.8000,'2026-01-21','good','2026-09-28','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(88,'019c1e63-b32d-72bd-847a-c1c17e7b8674',88,252.00,6.00,4.00,216.8000,'2026-01-18','good','2026-08-15','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(89,'019c1e63-b32d-72bd-847a-c1c17e7b8674',89,103.00,2.00,2.00,364.6000,'2026-01-31','good','2026-12-21','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(90,'019c1e63-b32d-72bd-847a-c1c17e7b8674',90,381.00,22.00,15.00,438.0000,'2026-01-19','warning','2026-04-03','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(91,'019c1e63-b32d-72bd-847a-c1c17e7b8674',91,177.00,7.00,4.00,287.9000,'2026-01-03','good','2026-11-21','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(92,'019c1e63-b32d-72bd-847a-c1c17e7b8674',92,318.00,17.00,4.00,374.1000,'2026-01-29','critical','2026-02-19','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(93,'019c1e63-b32d-72bd-847a-c1c17e7b8674',93,532.00,20.00,8.00,42.6000,'2026-01-13','critical',NULL,'2026-02-02 12:46:49','2026-02-02 12:46:49'),
(94,'019c1e63-b32d-72bd-847a-c1c17e7b8674',94,260.00,13.00,8.00,22.6000,'2026-01-15','critical',NULL,'2026-02-02 12:46:49','2026-02-02 12:46:49'),
(95,'019c1e63-b32d-72bd-847a-c1c17e7b8674',95,715.00,15.00,3.00,33.9000,'2026-01-08','warning',NULL,'2026-02-02 12:46:49','2026-02-02 12:46:49'),
(96,'019c1e63-b32d-72bd-847a-c1c17e7b8674',96,425.00,14.00,21.00,21.3000,'2026-01-21','good',NULL,'2026-02-02 12:46:49','2026-02-02 12:46:49'),
(97,'019c1e63-b32d-72bd-847a-c1c17e7b8674',97,111.00,4.00,5.00,118.4000,'2026-01-28','good','2026-11-09','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(98,'019c1e63-b32d-72bd-847a-c1c17e7b8674',98,85.00,2.00,3.00,290.9000,'2026-01-18','good','2026-08-16','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(99,'019c1e63-b32d-72bd-847a-c1c17e7b8674',99,85.00,1.00,0.00,259.2000,'2026-01-08','good','2026-11-04','2026-02-02 12:46:49','2026-02-02 12:46:49'),
(100,'019c1e63-b32d-72bd-847a-c1c17e7b8674',100,9.00,0.00,0.00,1501.1000,'2026-01-30','critical',NULL,'2026-02-02 12:46:49','2026-02-02 12:46:49');
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `supplier_bank_accounts`
--

DROP TABLE IF EXISTS `supplier_bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_bank_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `bank_code` varchar(255) DEFAULT NULL,
  `branch_code` varchar(255) DEFAULT NULL,
  `account_type` enum('checking','savings','investment') NOT NULL DEFAULT 'checking',
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_bank_accounts_supplier_id_index` (`supplier_id`),
  KEY `supplier_bank_accounts_is_primary_index` (`is_primary`),
  KEY `supplier_bank_accounts_is_active_index` (`is_active`),
  CONSTRAINT `supplier_bank_accounts_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_bank_accounts`
--

LOCK TABLES `supplier_bank_accounts` WRITE;
/*!40000 ALTER TABLE `supplier_bank_accounts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `supplier_bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `supplier_contacts`
--

DROP TABLE IF EXISTS `supplier_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` enum('procurement','billing','delivery','other') NOT NULL DEFAULT 'other',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_contacts_supplier_id_index` (`supplier_id`),
  KEY `supplier_contacts_is_primary_index` (`is_primary`),
  KEY `supplier_contacts_role_index` (`role`),
  CONSTRAINT `supplier_contacts_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_contacts`
--

LOCK TABLES `supplier_contacts` WRITE;
/*!40000 ALTER TABLE `supplier_contacts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `supplier_contacts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `supplier_documents`
--

DROP TABLE IF EXISTS `supplier_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `document_type` enum('certificate','agreement','tax_doc','insurance','banking','other') NOT NULL DEFAULT 'other',
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_by` char(36) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_documents_verified_by_foreign` (`verified_by`),
  KEY `supplier_documents_supplier_id_index` (`supplier_id`),
  KEY `supplier_documents_document_type_index` (`document_type`),
  KEY `supplier_documents_is_verified_index` (`is_verified`),
  KEY `supplier_documents_expiry_date_index` (`expiry_date`),
  CONSTRAINT `supplier_documents_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_documents`
--

LOCK TABLES `supplier_documents` WRITE;
/*!40000 ALTER TABLE `supplier_documents` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `supplier_documents` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `supplier_performance_history`
--

DROP TABLE IF EXISTS `supplier_performance_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_performance_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `record_date` date NOT NULL,
  `on_time_delivery_percentage` decimal(5,2) DEFAULT NULL,
  `quality_rating` decimal(3,1) DEFAULT NULL,
  `price_competitiveness` decimal(3,1) DEFAULT NULL,
  `communication_rating` decimal(3,1) DEFAULT NULL,
  `lead_time_days` int(11) DEFAULT NULL,
  `overall_score` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_performance_history_supplier_id_record_date_unique` (`supplier_id`,`record_date`),
  KEY `supplier_performance_history_supplier_id_index` (`supplier_id`),
  KEY `supplier_performance_history_record_date_index` (`record_date`),
  CONSTRAINT `supplier_performance_history_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_performance_history`
--

LOCK TABLES `supplier_performance_history` WRITE;
/*!40000 ALTER TABLE `supplier_performance_history` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `supplier_performance_history` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `supplier_terms`
--

DROP TABLE IF EXISTS `supplier_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `payment_terms_days` int(11) NOT NULL DEFAULT 30,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `minimum_order_quantity` decimal(15,4) DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `early_payment_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `early_payment_days` int(11) DEFAULT NULL,
  `delivery_terms` varchar(255) DEFAULT NULL,
  `lead_time_days` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_terms_supplier_id_index` (`supplier_id`),
  KEY `supplier_terms_is_active_index` (`is_active`),
  CONSTRAINT `supplier_terms_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_terms`
--

LOCK TABLES `supplier_terms` WRITE;
/*!40000 ALTER TABLE `supplier_terms` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `supplier_terms` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `bank_account` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','blacklisted') NOT NULL DEFAULT 'active',
  `credit_rating` enum('excellent','good','fair','poor') DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `payment_terms_days` int(11) NOT NULL DEFAULT 30,
  `outstanding_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `avg_delivery_days` int(11) DEFAULT NULL,
  `quality_rating` decimal(3,1) DEFAULT NULL,
  `on_time_delivery_percentage` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `parent_supplier_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_code_unique` (`code`),
  UNIQUE KEY `suppliers_email_unique` (`email`),
  KEY `suppliers_parent_supplier_id_foreign` (`parent_supplier_id`),
  KEY `suppliers_created_by_foreign` (`created_by`),
  KEY `suppliers_code_index` (`code`),
  KEY `suppliers_email_index` (`email`),
  KEY `suppliers_status_index` (`status`),
  KEY `suppliers_branch_id_index` (`branch_id`),
  KEY `suppliers_outstanding_balance_index` (`outstanding_balance`),
  KEY `suppliers_is_verified_index` (`is_verified`),
  CONSTRAINT `suppliers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_parent_supplier_id_foreign` FOREIGN KEY (`parent_supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` char(36) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `table_number` varchar(255) NOT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `status` enum('available','occupied','reserved') NOT NULL DEFAULT 'available',
  `capacity` int(11) NOT NULL DEFAULT 4,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tables_branch_id_table_number_unique` (`branch_id`,`table_number`),
  KEY `tables_department_id_foreign` (`department_id`),
  CONSTRAINT `tables_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tables_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tables`
--

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `units_of_measure`
--

DROP TABLE IF EXISTS `units_of_measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `units_of_measure` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_of_measure_code_unique` (`code`),
  KEY `units_of_measure_category_index` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units_of_measure`
--

LOCK TABLES `units_of_measure` WRITE;
/*!40000 ALTER TABLE `units_of_measure` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `units_of_measure` VALUES
(1,'mg','Milligrams','mg','weight','Milligram',1,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(2,'g','Grams','g','weight','Gram',2,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(3,'kg','Kilograms','kg','weight','Kilogram',3,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(4,'oz','Ounces','oz','weight','Ounce (avoirdupois)',4,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(5,'lb','Pounds','lb','weight','Pound',5,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(6,'ml','Milliliters','ml','volume','Milliliter',6,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(7,'l','Liters','L','volume','Liter',7,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(8,'cl','Centiliters','cl','volume','Centiliter',8,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(9,'fl_oz','Fluid Ounces','fl oz','volume','Fluid Ounce (US)',9,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(10,'cup','Cups','cup','volume','Cup (US)',10,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(11,'tbsp','Tablespoons','tbsp','volume','Tablespoon (US)',11,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(12,'tsp','Teaspoons','tsp','volume','Teaspoon (US)',12,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(13,'pcs','Pieces','pcs','count','Piece',13,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(14,'unit','Units','unit','count','Unit',14,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(15,'dz','Dozens','dz','count','Dozen (12 units)',15,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(16,'mm','Millimeters','mm','length','Millimeter',16,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(17,'cm','Centimeters','cm','length','Centimeter',17,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(18,'m','Meters','m','length','Meter',18,1,'2026-02-02 12:46:33','2026-02-02 12:46:33'),
(19,'in','Inches','in','length','Inch',19,1,'2026-02-02 12:46:33','2026-02-02 12:46:33');
/*!40000 ALTER TABLE `units_of_measure` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `last_accessed_branch_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `employee_number` varchar(50) DEFAULT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `employee_id` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `manager_id` char(36) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `hire_date` timestamp NULL DEFAULT NULL,
  `employment_status` enum('active','suspended','terminated','on_leave') NOT NULL DEFAULT 'active',
  `termination_date` date DEFAULT NULL,
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
  `user_type` enum('admin','employee') NOT NULL DEFAULT 'admin',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  UNIQUE KEY `users_employee_number_unique` (`employee_number`),
  KEY `users_last_accessed_branch_id_foreign` (`last_accessed_branch_id`),
  KEY `users_branch_id_index` (`branch_id`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_manager_id_foreign` (`manager_id`),
  KEY `users_department_id_index` (`department_id`),
  CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_last_accessed_branch_id_foreign` FOREIGN KEY (`last_accessed_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
('00418083-571e-396c-85ae-733e3120f146',NULL,'Amina Okafor','amina.okafor.5@sweettooth.com','EMP-ENU005-0005','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,1,NULL,'+234-823-947-9215','16916 Hodkiewicz Canyon, Enugu, Enugu State, Nigeria','1990-08-19','female','Nigerian','Tunde Adebayo','+234-868-949-3145','2023-08-03 23:00:00','active',NULL,NULL,'afternoon',241918.92,NULL,'TIN-41047042','3529874250',NULL,NULL,'2025-10-03',4.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('019c1e63-b272-7201-9b33-e6a9a238db71','019c1e63-b2e7-70f2-81ab-c6347efd07f0','Super Admin','admin@sweettooth.local',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$TNebacFY1BCELpGNfN6Ywenk6CIv/leYZkZ7k1U5hs.Eta07vCbRC',NULL,NULL,NULL,NULL,NULL,'2026-02-02 12:46:16','2026-02-02 12:49:18'),
('019c1e63-d2d4-70b1-83d7-07c9580d7e8a',NULL,'Zachary Pagac','turner.hansen@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','hQ4vz2OdJR','kH6gUnhvMf','2026-02-02 12:46:24','2026-02-02 12:46:24','GVkfb6grqQ','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d306-736f-be65-fe3d2b03cd2e',NULL,'Cade Wehner','tbeier@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','ZTJjHYslSc','lemnokWLMv','2026-02-02 12:46:24','2026-02-02 12:46:24','gAZRN3Xq5k','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d31c-70f7-8da3-ff4c4580c50d',NULL,'Felix Skiles','howe.nola@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','ALxOxR9lET','95HRDIicuT','2026-02-02 12:46:24','2026-02-02 12:46:24','eNkleSFRip','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d327-73ef-90c9-bd89eaf68620',NULL,'Newton Quitzon','oral.mayer@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','iuGzmkaIZ3','SN5TCLTbXU','2026-02-02 12:46:24','2026-02-02 12:46:24','8Gh2yTWAPD','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d333-7324-8420-2e99c2c7bca9',NULL,'Scot Adams','zwolff@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','c0B2mqKzZK','jxsRne4gb0','2026-02-02 12:46:24','2026-02-02 12:46:24','qAIcX50jk0','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d33d-73bb-bab5-813da912044e',NULL,'Jolie Romaguera','herzog.dell@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','yQOnXMfjZ2','8aUSPJtxQC','2026-02-02 12:46:24','2026-02-02 12:46:24','bGnyLl8RN0','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d349-73e4-9330-177932a91548',NULL,'Misty Harvey','boyer.annetta@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','4HNi1e9QmG','NzlzCfuaSh','2026-02-02 12:46:24','2026-02-02 12:46:24','Y5Ah7biR6k','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d354-7222-9796-31f2d5196700',NULL,'Dr. Eloy Doyle','erling75@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','W754wtYjPs','DPXU5i4CN9','2026-02-02 12:46:24','2026-02-02 12:46:24','JCO3RHdc0i','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d35f-725b-bf0b-5423c9f8adaf',NULL,'Lazaro Will','kamren28@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','7KMOjpDk4Q','aWPAhi5kgG','2026-02-02 12:46:24','2026-02-02 12:46:24','BxPsqW3KQT','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d36b-72d1-b216-32e22d873136',NULL,'Daija Beahan','mann.jessica@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','xJCSiI19tw','2xpsky1fHB','2026-02-02 12:46:24','2026-02-02 12:46:24','JmxQU88ery','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d375-72d5-a5de-21e351244895',NULL,'Kennedy Swift','sanford.hodkiewicz@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','2uyjpKuyoh','8zfXinDfCh','2026-02-02 12:46:24','2026-02-02 12:46:24','l0t9RkSTnP','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d380-716e-9901-93c49c0738e8',NULL,'Ms. Mckayla Abbott','maudie72@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','RAr61vs3QX','gQduf2VE5q','2026-02-02 12:46:24','2026-02-02 12:46:24','GcZr6IL1lI','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d38c-7380-8719-91e5a83ae02e',NULL,'Miss Ariane Wintheiser IV','kacey.lehner@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','Uwi11RnaMD','te5uAbevO3','2026-02-02 12:46:24','2026-02-02 12:46:24','2z1dy0a9UO','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d397-7045-8816-a8e2f15a12b7',NULL,'Robin Rau','nolan.tiana@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','uMfSpbycMG','jnVjrXrWws','2026-02-02 12:46:24','2026-02-02 12:46:24','xADHCiB476','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3a1-72c5-944f-49a21b860695',NULL,'Tracey Collier','zena41@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','oHosLvf7E3','AUKg6eVQ0Y','2026-02-02 12:46:24','2026-02-02 12:46:24','v0uqve3Unu','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3ad-7030-8b26-f44f6a060c26',NULL,'Margret Mueller','geovanny90@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','k3yQjkVjkj','azRpJ0a68w','2026-02-02 12:46:24','2026-02-02 12:46:24','z1MobxUIxg','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3b8-7155-b7ab-d3b5b820de74',NULL,'Rafael Hayes','aric60@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','LpfSeNQydW','iCOGUVMh0W','2026-02-02 12:46:24','2026-02-02 12:46:24','nCehmeaCKn','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3c3-7132-9b53-d96d8e883664',NULL,'Prof. Dallas Kuhn','mcclure.clemens@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','UaHVU1UfGK','jeVwEeQ9jT','2026-02-02 12:46:24','2026-02-02 12:46:24','IYykBo1MdH','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3cf-7140-874d-adadf5a8e2fb',NULL,'Melissa Terry','kim.rippin@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','2KRrskBY7y','DMWZSX1TIK','2026-02-02 12:46:24','2026-02-02 12:46:24','88yKpONRpn','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3da-71d7-8834-209372288829',NULL,'Vilma Hirthe','lynch.stefanie@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','yGlpyIinPk','mY6YLdHYLE','2026-02-02 12:46:24','2026-02-02 12:46:24','zBZLUfViE2','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3e5-7154-9b45-b3ec39963ebd',NULL,'Cornell Champlin DVM','mathias29@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','NoZGG8ihub','6gKryDH1Et','2026-02-02 12:46:24','2026-02-02 12:46:24','650YI35Rqd','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d3fb-72ef-9297-bb8d849d227a',NULL,'Lazaro Wehner','lind.reese@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','4HzEx8HQAj','vmrtVWBsvL','2026-02-02 12:46:24','2026-02-02 12:46:24','qn2ggXrQfq','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d406-729e-ba18-c1f037b182df',NULL,'Prof. Ayden Ernser V','maggio.maeve@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','2jGRHIRiJT','e0d1i5h6YQ','2026-02-02 12:46:24','2026-02-02 12:46:24','t6TzcNbEnR','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d411-70a9-bb0a-070141243d90',NULL,'Dee O\'Keefe','rice.eryn@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','3JjtQc9d28','irl6EBbx87','2026-02-02 12:46:24','2026-02-02 12:46:24','5nc8vM44nX','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d41c-70aa-bd01-7a3e91191be4',NULL,'Jayce Frami','wilhelm.oreilly@example.net',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','0dZQChCeJT','7mCWBZ0vwt','2026-02-02 12:46:24','2026-02-02 12:46:24','r32Q7pPOUn','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d427-732d-9608-0920e7585f3c',NULL,'Amiya Ward','zula.lesch@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','uXjXB5Z5rw','prT0x69sRl','2026-02-02 12:46:24','2026-02-02 12:46:24','Ixb7VLDIlj','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d432-7293-987a-a65c6bfc4442',NULL,'Queenie Spencer','emard.baron@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','XzZTFvNKJc','L3JDM2PIql','2026-02-02 12:46:24','2026-02-02 12:46:24','Zc2RhPCy9O','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d43d-73ff-b28d-c13e9c978fb4',NULL,'Dr. Genevieve King Sr.','unique.boyer@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','pie3eE6NJ9','tZIdoQkQBr','2026-02-02 12:46:24','2026-02-02 12:46:24','leaFQafAhB','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d49a-72cd-844c-c4446319d605',NULL,'Dr. Haylie Bins DDS','cgutkowski@example.com',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','HYfleVxQVm','zkNYepnBGS','2026-02-02 12:46:24','2026-02-02 12:46:24','hZIXDa61AB','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-d4ad-72df-833f-f76761b3d22a',NULL,'Daren Bosco Jr.','hauck.trever@example.org',NULL,'019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$kxB3qYh3y8lPKvhb/i/6S.5V6ORygh686qbuXGbEr9z61WoAJ84tu','qE0kq5NgVv','q6U0faxshc','2026-02-02 12:46:24','2026-02-02 12:46:24','zGq0nNQFRI','2026-02-02 12:46:24','2026-02-02 12:46:24'),
('019c1e63-dae1-7100-994f-30692428d644',NULL,'Dalton Kozey','arturo.lesch@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','crZGk4TTe6','XBbwjzz1tO','2026-02-02 12:46:26','2026-02-02 12:46:26','Tx8wvK7Ex9','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-db5b-73a4-9231-5d03cefa8eaf',NULL,'Miss Kiarra Boehm','sawayn.nina@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','i1M8vq7o7H','RaMeKJlBY0','2026-02-02 12:46:26','2026-02-02 12:46:26','kT5a3a1kkw','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-db65-704b-8713-876e882848c1',NULL,'Prof. America Ritchie','toy.alexandrea@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','989GPyAq06','hFcAASYSpD','2026-02-02 12:46:26','2026-02-02 12:46:26','tQbRztCwdk','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-db71-7018-97b2-894bf0e22629',NULL,'Keanu Bins','uwunsch@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','SMfferDflO','SepJvVWSSS','2026-02-02 12:46:26','2026-02-02 12:46:26','E4OMaQoqu2','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-db7c-733f-958f-d8aad629fa47',NULL,'Miss Carolina Mueller IV','lcummings@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','waRrtynrxS','jratqlr8zo','2026-02-02 12:46:26','2026-02-02 12:46:26','AtrE7Jair3','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-db87-72f0-85bf-e20eedb9084d',NULL,'Prof. Emerald Cassin III','ford.reichert@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','x8Szkj7GFM','189CzuBqLP','2026-02-02 12:46:26','2026-02-02 12:46:26','jITxJH1oqU','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-db9c-72ab-8e60-afa2266e450a',NULL,'Maximilian Lubowitz','rosanna.maggio@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','Ceolshsca7','PUSFOTWCW3','2026-02-02 12:46:26','2026-02-02 12:46:26','jZCCgvFMMm','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dbb5-7119-9a10-d28faf9f4941',NULL,'Zelda Goodwin DDS','klein.lambert@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','anFiFAnCnT','MEJ7wnAYJB','2026-02-02 12:46:26','2026-02-02 12:46:26','4U6xNEecmp','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dbc0-716e-bf65-75a108b57bb1',NULL,'Dr. Gillian Lakin II','hammes.angelo@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','c4lSjsz7GK','cH5X97zw1a','2026-02-02 12:46:26','2026-02-02 12:46:26','zIBJ4Qem7l','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dbcb-731b-a3c6-097b8d95aaca',NULL,'Chandler Botsford','otorp@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','9DGOaZyiAI','PYhggMT4Nx','2026-02-02 12:46:26','2026-02-02 12:46:26','BC1umpRdPy','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dbd7-70ae-a473-0939be6798fd',NULL,'Hoyt Goldner','hayley.yost@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','heQHMM7cJv','D2Au7cF8e8','2026-02-02 12:46:26','2026-02-02 12:46:26','ugYUCapRNg','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dbe1-7240-8a03-f5211701562b',NULL,'Deshawn Jacobson','santina83@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','RKBFq3Njf6','lrVvY594b9','2026-02-02 12:46:26','2026-02-02 12:46:26','iCJbLT7Fac','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc19-72f4-987e-6e5098c46c8a',NULL,'Greyson Emmerich','jschroeder@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','HVOJbgAbIv','1nTTfojsiK','2026-02-02 12:46:26','2026-02-02 12:46:26','hojDu59NjS','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc24-70d1-b758-be5cc06cbf6d',NULL,'Hershel Emard','pfannerstill.savanah@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','eighR0FPJ9','IqCbcc2sHM','2026-02-02 12:46:26','2026-02-02 12:46:26','cjkD8BJ8yi','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc2f-7065-80c3-ffefeed22742',NULL,'Saige Hoeger','isom.leffler@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','dNjOmZmJmH','gXob6OMi3c','2026-02-02 12:46:26','2026-02-02 12:46:26','EDrsoxmTl2','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc3a-705a-a7fc-b8445d93336b',NULL,'Dr. Gabriel Hoppe I','royal33@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','o6KDijRs5M','Oxp4A8blLF','2026-02-02 12:46:26','2026-02-02 12:46:26','8uVwCNJoQG','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc46-7314-ae44-642c04e3e7f1',NULL,'Rosa Pollich DVM','dmosciski@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','8i2YaHQqvl','XO5mKd2LeP','2026-02-02 12:46:26','2026-02-02 12:46:26','IiEws488ZQ','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc50-71ce-b207-406504543efb',NULL,'Elisa Hoeger','kim.muller@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','b1Cztk4FFZ','DR54Z15BjW','2026-02-02 12:46:26','2026-02-02 12:46:26','oqrCHnU0XR','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc5c-72a0-a820-90626fa32fcb',NULL,'Darius Hoeger','xhills@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','S2OFGSs7oS','kH12uN8669','2026-02-02 12:46:26','2026-02-02 12:46:26','2D9UcsdEEG','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc67-72cd-9858-14a0a9d77abe',NULL,'Mrs. Addie Collins','shanny18@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','DXEPeW6Vxd','UNWa3S08pN','2026-02-02 12:46:26','2026-02-02 12:46:26','rtXgh9OZVz','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc72-70cd-99b4-f9cbb5ba565f',NULL,'Drake Runte','amparo35@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','GIdZ6HJl7m','bezxa8UGNw','2026-02-02 12:46:26','2026-02-02 12:46:26','J8GuBbaGQ5','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc7e-7338-bbfb-d25b5a262361',NULL,'Lelah Deckow','gweissnat@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','JHStnSfSk2','fkJe8fJLSX','2026-02-02 12:46:26','2026-02-02 12:46:26','Z4K6U2BBmm','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc88-7382-aa1c-b20fd99ea554',NULL,'Miss Onie Quitzon DVM','elangosh@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','k2bKEcZLMb','QpUEhn8FSi','2026-02-02 12:46:26','2026-02-02 12:46:26','lyWgS60igN','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc93-73e3-bfdb-d5a0adcc6aea',NULL,'Abbie Runolfsdottir','maribel.lang@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','8h4mhoRXvo','TWu60Hw2EK','2026-02-02 12:46:26','2026-02-02 12:46:26','V59baaVYew','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dc9e-7090-9771-73429f72507b',NULL,'Thaddeus Baumbach','maddison36@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','65nuYOck2p','KFdpekWB74','2026-02-02 12:46:26','2026-02-02 12:46:26','WU2qwTCXYa','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dcaa-70c7-81da-a7b8e19b2ad8',NULL,'Haley Stamm','nemmerich@example.org',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','XBYGW9t82R','6lnnpoGEUy','2026-02-02 12:46:26','2026-02-02 12:46:26','IYKMyGgdmD','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dcb5-7054-a0fd-4400b5917b6b',NULL,'Mr. Wilfrid McDermott II','dagmar.breitenberg@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','BEWOF3te4O','uOHg9H2wu3','2026-02-02 12:46:26','2026-02-02 12:46:26','bxKBnLvULl','2026-02-02 12:46:26','2026-02-02 12:46:26'),
('019c1e63-dcc0-7186-88d1-1614cd16bd4d',NULL,'Miss Tiara Bayer','langworth.zoila@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','Z2fWSL6bCB','VIhkU5JC0T','2026-02-02 12:46:26','2026-02-02 12:46:26','v2KybaRbXN','2026-02-02 12:46:27','2026-02-02 12:46:27'),
('019c1e63-dccb-71e1-999f-577d4689ca86',NULL,'Robb Blick','sigurd47@example.com',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','JdhlaDGpSt','5Y8g95ycQP','2026-02-02 12:46:26','2026-02-02 12:46:26','FPEngHFx6P','2026-02-02 12:46:27','2026-02-02 12:46:27'),
('019c1e63-dcd6-7218-a33a-d96de0217522',NULL,'Luis Mitchell','rempel.katarina@example.net',NULL,'019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$4qiSyraJs/aPpnQGrSoYZOk6k3yM6/cJCHtFf21robhuKTQRGS5GW','SuXZPhEt65','3b6pxtHGqi','2026-02-02 12:46:26','2026-02-02 12:46:26','VyYFc4Bdsx','2026-02-02 12:46:27','2026-02-02 12:46:27'),
('019c1e63-e016-72a6-bdc7-f31722887905',NULL,'Brennan Wyman','jettie74@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','KcXiKvj44P','pvuYESy4HZ','2026-02-02 12:46:27','2026-02-02 12:46:27','fd778MSghO','2026-02-02 12:46:27','2026-02-02 12:46:27'),
('019c1e63-e0c0-718e-9365-f3cf772ffa78',NULL,'Opal Stracke','astrid.rodriguez@example.com',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','cXVjHo1aAW','ArGFyBC4Hh','2026-02-02 12:46:27','2026-02-02 12:46:27','7e6yeD0rZn','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e0c9-70a9-93dd-3af488590ff5',NULL,'Dennis Jones','parker.geovanny@example.com',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','v2MXhzPSnY','1Z9K5s7SUJ','2026-02-02 12:46:27','2026-02-02 12:46:27','uCBJRRqOhh','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e0d6-73c5-aa0f-550b82e5efec',NULL,'Elenor Dach','zconroy@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','zW7S9K8qMZ','SQePIBpSz2','2026-02-02 12:46:27','2026-02-02 12:46:27','0lrCOsHLm9','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e0eb-736b-b326-47719a0010bc',NULL,'Louisa Kunze','jtromp@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','Uq5Z2HNNHL','fjDbhj8e2l','2026-02-02 12:46:27','2026-02-02 12:46:27','D12v9yqpm6','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e0f6-737e-88b3-94ea9a693c9d',NULL,'Dr. Braden Nitzsche','haylee58@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','QyMMZyDN09','nX0mhDScD1','2026-02-02 12:46:27','2026-02-02 12:46:27','Ck22S2y9GO','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e101-7328-9288-9c823a095d59',NULL,'Mylene Metz','garry.dickinson@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','vY0zsLx9Hi','3hbn3kGRRX','2026-02-02 12:46:27','2026-02-02 12:46:27','qllIZi8Cwr','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e10d-72cf-824d-eb5f48fbf9ab',NULL,'Roxanne Rolfson','lenore.stroman@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','fp6HvIVVzu','4HsspZcMoG','2026-02-02 12:46:27','2026-02-02 12:46:27','bUtRfPvWIW','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e118-736b-8e38-757c3255cd71',NULL,'Darrick Reinger','dwatsica@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','AorSUCNnqm','5CbfJZ4G6U','2026-02-02 12:46:27','2026-02-02 12:46:27','O367pB8zQ9','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e12e-70a7-b792-485788c76730',NULL,'Dr. Nigel Eichmann','rohan.isom@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','623TD7WK5x','1xbslkCR4B','2026-02-02 12:46:27','2026-02-02 12:46:27','wZWY2S3fxF','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e145-7273-b771-75ea783f7bef',NULL,'Anna Sporer I','einar68@example.com',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','ld8hbxGbGE','YigkLP8MYn','2026-02-02 12:46:27','2026-02-02 12:46:27','kUVrOoGZ0w','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e15b-7276-8cf7-68c51248d32f',NULL,'Timmy Legros','zfadel@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','NqCwkHOP8q','ch9DrKmXjS','2026-02-02 12:46:27','2026-02-02 12:46:27','WgjwNmbdQx','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e171-70c2-8d5d-c3499ad82adf',NULL,'Emelia Wuckert','earmstrong@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','pjjJTysPC5','YG5aHxGuew','2026-02-02 12:46:27','2026-02-02 12:46:27','j0pISJwdmh','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e17c-7015-8ad5-144c3058ef17',NULL,'Broderick Bayer I','rhessel@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','Xb0m5lI500','75l9M8Xp16','2026-02-02 12:46:27','2026-02-02 12:46:27','jWQf0QuFYz','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e192-7141-9f83-bfaf993c8feb',NULL,'Mr. Sigurd Bergnaum','lehner.eulah@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','prOzJt8rEH','kjM0iL7f6L','2026-02-02 12:46:27','2026-02-02 12:46:27','dZq4vv9VHF','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e1a9-71e2-8337-1331394134ba',NULL,'Aimee Davis','karina23@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','W1OySXZHcy','O1BL12FiKi','2026-02-02 12:46:27','2026-02-02 12:46:27','X7Pbwe3JVU','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e1bf-72b8-ba36-f6bda2ded697',NULL,'Hulda Parker','miller.ardith@example.com',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','a0BM9YCxdj','uZXneUREGz','2026-02-02 12:46:27','2026-02-02 12:46:27','cGKIcV2oAL','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e1d5-7080-9a12-4737d5d93929',NULL,'Prof. Alec Hills I','cloyd.hessel@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','SJM5cmZbtw','rOn7iroJuv','2026-02-02 12:46:27','2026-02-02 12:46:27','3uAd3qlaIW','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e1ec-7039-8ee8-63789b4feb6d',NULL,'Geraldine Schoen','laila24@example.com',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','kAnM7BeVBM','iSRMa4vbmi','2026-02-02 12:46:27','2026-02-02 12:46:27','cVx6Ntd7IC','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e202-73a6-a55f-4309ac712a4c',NULL,'Mrs. Annamarie Hilpert Jr.','muller.nya@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','nvDZPr8qIa','5fev5ZWbgT','2026-02-02 12:46:27','2026-02-02 12:46:27','mRVXb0fyd4','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e217-70b7-b1d6-a5b1fca83834',NULL,'Dr. Carol Price II','vmitchell@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','IzxnwdffHE','tauagTzGHY','2026-02-02 12:46:27','2026-02-02 12:46:27','RU7rgoOBw1','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e22e-716e-b063-1b1e1579f7e1',NULL,'Ms. Rosella Torphy II','koepp.lora@example.com',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','U66G63Bgs2','6jgpCg6a8Q','2026-02-02 12:46:27','2026-02-02 12:46:27','g4G8pwe4Av','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e244-709c-918f-32aee7c4d3f6',NULL,'Miss Hortense Mayer','hallie.schamberger@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','pSJbe7gIFB','oFI8NnbHCP','2026-02-02 12:46:27','2026-02-02 12:46:27','dUfuRHjQVw','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e25a-7193-a957-89b39db550e0',NULL,'Sid Schneider','schmitt.pietro@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','zilQiWy5f5','5AHzc0DZba','2026-02-02 12:46:27','2026-02-02 12:46:27','AjdWoflRQk','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e271-73ea-bc94-84a656dcb4b8',NULL,'Arvel Johnson','mosciski.maxie@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','uqJqKiOO5U','3OLldJwYYp','2026-02-02 12:46:27','2026-02-02 12:46:27','HYeKVLeyqf','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e287-7052-ae37-13065065c600',NULL,'Gudrun Zulauf','ferne.boyle@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','TBOAgI86Ck','MieJJb0p1H','2026-02-02 12:46:27','2026-02-02 12:46:27','fDBWhUtmAu','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e29d-7012-b87b-081e776e14d8',NULL,'Eriberto Considine','zkrajcik@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','dmkA9WRioq','juHzc8d8SQ','2026-02-02 12:46:27','2026-02-02 12:46:27','Xp0B5JUBEz','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e2b4-7309-ac96-56089d4cf350',NULL,'Jayda Cremin II','lucy.funk@example.com',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','Ii6RJfhxCB','lirPqY6AWV','2026-02-02 12:46:27','2026-02-02 12:46:27','58bFINS5sR','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e2c9-739e-ba32-445cade32c0f',NULL,'Alisa Swaniawski','hintz.joanie@example.org',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','jcneKG0clo','oKQJGqRyr7','2026-02-02 12:46:27','2026-02-02 12:46:27','5ibiqnLluU','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e2e2-70cd-a3e5-ffa29a5a75e6',NULL,'Tobin Parker','abigale.lockman@example.net',NULL,'019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$DE3b0PTsskbE99X6GjzT5.ltwTNuYXFv9yluT9SUehyxA/2H2d6du','hnfVfwUIdw','mQpqfDuubb','2026-02-02 12:46:27','2026-02-02 12:46:27','D6vDY4NyTc','2026-02-02 12:46:28','2026-02-02 12:46:28'),
('019c1e63-e8ef-701b-9f17-1d2df88cf6ee',NULL,'Mr. Jay Rempel MD','nikolaus.marley@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','qrhCLOqR9p','8hd4gtmOtS','2026-02-02 12:46:30','2026-02-02 12:46:30','6MGci02BBy','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e94b-7289-899c-82b6b0f8689f',NULL,'Darrion Lubowitz Jr.','eryn.medhurst@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','VVSc4YImDK','So9R8Dinrk','2026-02-02 12:46:30','2026-02-02 12:46:30','uAumDRnxF4','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e960-705a-8edb-d1d255e30aa7',NULL,'Misael Bergnaum PhD','toy.lucio@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','CzEfn5FmG9','BvswhLpAtV','2026-02-02 12:46:30','2026-02-02 12:46:30','XUfW9jZJVR','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e96c-72a0-857d-1ca245c5c8f8',NULL,'Jonathon Konopelski','bayer.madonna@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','3JRUmcu7dz','HAMRkZNcB2','2026-02-02 12:46:30','2026-02-02 12:46:30','u0bKXUqnex','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e978-721a-911f-ec6a8b737b36',NULL,'Otis Gleason','yhahn@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','dGY3fyJkZy','a9FCzoghx4','2026-02-02 12:46:30','2026-02-02 12:46:30','DG86HUUB8Q','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e982-727a-b7c1-01e1ffa6133b',NULL,'Conor Hill','mariela.rutherford@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','JQ1PBa5eTg','XkCCwSRT0l','2026-02-02 12:46:30','2026-02-02 12:46:30','ezbHI069IR','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e98d-737b-9b5b-b3f257e55b07',NULL,'Miss Agustina Spencer','dee.crona@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','kOm0Z4KOw4','POfB2K31MB','2026-02-02 12:46:30','2026-02-02 12:46:30','WmZkfRzECp','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e998-7243-93f7-27cf67d9c6b5',NULL,'Green Schmidt IV','nannie.crooks@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','jR1vFxZlPC','XHNLMKeBeY','2026-02-02 12:46:30','2026-02-02 12:46:30','GIRM4aPfAR','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9a3-737f-be61-aec80e9e5554',NULL,'Dr. Reynold Parker DVM','raina64@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','zhiQvZ9iCX','mMoLzbAE5M','2026-02-02 12:46:30','2026-02-02 12:46:30','fej03TxJOE','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9af-70f6-9086-1ff53e04770f',NULL,'Mr. Jackson Ernser','pkirlin@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','KNusQTJVuz','L0KHkxArM2','2026-02-02 12:46:30','2026-02-02 12:46:30','y4pU7LbEYO','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9b9-731a-b3ef-f2f8d3be3001',NULL,'Haskell Konopelski III','corkery.brendon@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','6vpO9UHXkx','c3gBrluN0M','2026-02-02 12:46:30','2026-02-02 12:46:30','nygRWJQZTs','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9c5-72d7-a19c-3f16157a4818',NULL,'Evelyn Roob','kendra.kub@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','fq9mu2r1Wg','w6RSl7sKNn','2026-02-02 12:46:30','2026-02-02 12:46:30','uI6u4mhRXz','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9d0-7313-9c7e-3ff7361ff564',NULL,'Loy Gaylord I','cabshire@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','8GJ8ALMoRL','JfYCxLGpfn','2026-02-02 12:46:30','2026-02-02 12:46:30','oZY0ldNXO7','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9db-70e7-8291-bbb6c6130cfd',NULL,'Torrance Bartell','wilhelm66@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','EQ3DEYpK2x','X3dziZDu2G','2026-02-02 12:46:30','2026-02-02 12:46:30','mHQUlerW53','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9e6-7057-b57e-01540e4e58a3',NULL,'Melany Pfeffer','ksteuber@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','iIO7ZM78We','zV480kNOgy','2026-02-02 12:46:30','2026-02-02 12:46:30','jhf0fmSEjp','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9f1-70bb-94cb-e3c901c286d9',NULL,'Claudie Willms Sr.','laney.turner@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','DwmoNKBSZ4','InCYfotOCE','2026-02-02 12:46:30','2026-02-02 12:46:30','F0P5Vv5xe4','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-e9fc-722e-8832-406d6c29580b',NULL,'Thelma Walsh DVM','tillman.glenda@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','eXTTjo7RaY','Mamb45DMyP','2026-02-02 12:46:30','2026-02-02 12:46:30','7r7k7rflHW','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea08-71aa-9757-859ce9ac1494',NULL,'Karianne Johns','tracy.jakubowski@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','JFYZsIuqF7','hDRbqRir4I','2026-02-02 12:46:30','2026-02-02 12:46:30','6Z9qlmUMsv','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea13-72ba-9800-3fa9e90f90fd',NULL,'Claudine Steuber','uabshire@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','MAK0Q5E6Fs','ouVtrnfi7k','2026-02-02 12:46:30','2026-02-02 12:46:30','dbFNqPoYp1','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea1f-71b1-8de9-c5357f01f964',NULL,'Levi Kulas','swill@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','4UBz5YLoDP','D4EmE0G5e2','2026-02-02 12:46:30','2026-02-02 12:46:30','8Z2BGUJUVW','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea35-715e-ba91-8d4a53d1b643',NULL,'Regan Buckridge','maverick.reynolds@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','TLmIkkezC8','yOsM3W2jD8','2026-02-02 12:46:30','2026-02-02 12:46:30','Sl59nit9SF','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea4b-7132-b310-ae3c8a9f32c4',NULL,'Hallie Kovacek','hickle.adolfo@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','q3UxX5zWkQ','JHTl0qfzCI','2026-02-02 12:46:30','2026-02-02 12:46:30','9qmZnhu5tk','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea61-730f-86e9-0b9e13bcb129',NULL,'Naomi Stamm','freda26@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','6771E5wa8D','mrKC6Jxwv3','2026-02-02 12:46:30','2026-02-02 12:46:30','h9jpUzXrer','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea78-7227-afb0-8bb9595b126e',NULL,'Kimberly Botsford','marilie.champlin@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','Dy1roUIAlG','aE7XJQ3wpL','2026-02-02 12:46:30','2026-02-02 12:46:30','22ipsS7c70','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ea8e-7178-b18b-93fe9b14739f',NULL,'Dr. Michel O\'Conner I','bartoletti.franz@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','UZHaVASNJw','bzEefba0MS','2026-02-02 12:46:30','2026-02-02 12:46:30','kyGtFSVJzQ','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-eaa5-73ab-a5d7-0ba052d3e65b',NULL,'Prof. Melisa Bahringer I','ian.okon@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','A9tqkyMfTd','r5iD3sgg21','2026-02-02 12:46:30','2026-02-02 12:46:30','kxb03r89eq','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-eab9-739b-89ff-a80c9e56238b',NULL,'Jonathan Kertzmann','arch63@example.net',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','J7lAGR38v9','P81b79tkbL','2026-02-02 12:46:30','2026-02-02 12:46:30','9heoCMc2bs','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-eac4-7323-9620-72c67d529c25',NULL,'Ahmed Smith I','rafaela.rohan@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','VQWNR1vyF4','hfRuxY3INx','2026-02-02 12:46:30','2026-02-02 12:46:30','UsZVVVUgTn','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-ead0-71c7-b1e4-12b68a932021',NULL,'Prof. Arvid Hermiston I','haleigh.kirlin@example.com',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','Imhwf62O2m','cyprGZRnNm','2026-02-02 12:46:30','2026-02-02 12:46:30','sI0dC4t8u2','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-eae7-73c3-8ba4-99da26f07c89',NULL,'Mr. Simeon Runte III','thiel.isabel@example.org',NULL,'019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$pyyrjYyXII3Pc5pFoc8oBuGk2gqhFbdqNXWwqDvb2AmrGJQxCsrjG','qKFjrVN2lV','pfE5ZHGmDe','2026-02-02 12:46:30','2026-02-02 12:46:30','zRZjDwKirH','2026-02-02 12:46:30','2026-02-02 12:46:30'),
('019c1e63-eee8-7133-a00c-7e6dda3a4440',NULL,'Allan Lueilwitz DDS','ecrona@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','gyfPIPk1Mw','ZTvCFfDfsy','2026-02-02 12:46:31','2026-02-02 12:46:31','oSOIQUJwXS','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-ef52-7119-90b8-8e0be3cbe83d',NULL,'Bernard Hudson','rachel.robel@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','Pw0eCcUOZm','5ukCiCJpOc','2026-02-02 12:46:31','2026-02-02 12:46:31','FsD8dJCr7t','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-ef69-730c-b890-d3ae53dcf7a9',NULL,'Brielle Cummerata','xpredovic@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','CJSVAfKN0W','KQiaN7SO6T','2026-02-02 12:46:31','2026-02-02 12:46:31','op4M4acyJn','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-ef74-7295-863b-f1f2ba49ee5f',NULL,'Adeline Herman','hubert42@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','o7xxiNSgA8','E32zdk4vJp','2026-02-02 12:46:31','2026-02-02 12:46:31','i2KfsmJocs','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-ef7f-7092-82bb-64e7c6b46531',NULL,'Dr. Hassan Klocko','bridgette83@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','ixS07Hznf8','BwDE5alZ3B','2026-02-02 12:46:31','2026-02-02 12:46:31','bnODFIAuMW','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-ef8a-736a-9581-44cbcb31b423',NULL,'Kaela Stracke V','cparisian@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','EKWppqn1X3','EFcpfkM0bS','2026-02-02 12:46:31','2026-02-02 12:46:31','uNXi70KNnR','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-ef95-716b-8167-13205f59a73f',NULL,'Prof. Kamryn Schuppe II','stanley67@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','lmuzB8nmD2','md91pMgljD','2026-02-02 12:46:31','2026-02-02 12:46:31','I5hnxMi5Xn','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efa0-7296-a9fd-ce2069c9fe57',NULL,'Sunny Wehner','tatyana.davis@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','8lRR2u9EiU','EzwqmkTZOb','2026-02-02 12:46:31','2026-02-02 12:46:31','fIuIV2h9mz','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efab-71c2-be27-cd1801f7ce93',NULL,'Adolfo Towne','willie.rath@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','I4TCVOENKw','yLmQ6B2xhb','2026-02-02 12:46:31','2026-02-02 12:46:31','TYqKt4WKvU','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efb6-72fe-b76c-ef5d05bb9b53',NULL,'Sadye Bogisich','ochristiansen@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','oHsxsxHj4a','MmqUlxIh69','2026-02-02 12:46:31','2026-02-02 12:46:31','K8zhUuA3Zl','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efc1-72de-9ad2-0ec10d5371be',NULL,'Prof. Lurline Schowalter','labadie.telly@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','OMeTIF5lBs','qNTvIGyW0X','2026-02-02 12:46:31','2026-02-02 12:46:31','ZPc3QK0ojN','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efcc-7100-8a64-46475b5ec838',NULL,'Else Hill','oterry@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','gUNgrkOJim','qeAqQrn2Vj','2026-02-02 12:46:31','2026-02-02 12:46:31','qTlEQzRrOK','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efd7-7192-ac51-02fb6c54a80c',NULL,'Britney Rogahn','myron.armstrong@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','e8wKmLexA6','feDKxdyxj5','2026-02-02 12:46:31','2026-02-02 12:46:31','ylCcRimu5h','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efe2-7207-9d08-f78725c0e3b8',NULL,'Burley Howell','celestine94@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','RtrpMGlQNo','nrP6lrRThe','2026-02-02 12:46:31','2026-02-02 12:46:31','YtGbYking6','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-efed-71c5-830e-4177b2d2b635',NULL,'Corrine Huel','michel11@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','UTpCjjpqkl','rjDS9I7Dnr','2026-02-02 12:46:31','2026-02-02 12:46:31','3IanZri47y','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-eff9-7087-89b7-76208ab0cb00',NULL,'Miss Jeanette McKenzie V','graham.hallie@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','fHScLJN3Li','Bgz0OkTFdX','2026-02-02 12:46:31','2026-02-02 12:46:31','SwIBfIYAhk','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-f004-70dc-a2ee-a683f9880b1e',NULL,'Isaac Swift','llowe@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','zkrjLSBCbi','ncqXLw0gs8','2026-02-02 12:46:31','2026-02-02 12:46:31','Z8hjYMPywh','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-f00f-725e-a5b8-4993aed759ac',NULL,'Prof. Corbin Hartmann','joseph.reichel@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','rs3T7qIwRu','P68AbWbtzc','2026-02-02 12:46:31','2026-02-02 12:46:31','j1b9vqauMQ','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-f01a-70a0-bb0b-681129061a97',NULL,'Jeanette Krajcik','allan.abshire@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','fERtjWT6Ua','oeTAvfeI0e','2026-02-02 12:46:31','2026-02-02 12:46:31','C87gEa8Dwu','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-f025-73e5-81a4-b3307fad2bed',NULL,'Princess Keebler Jr.','russ37@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','nVDlosCShM','DNqJWVHtz0','2026-02-02 12:46:31','2026-02-02 12:46:31','aLdynAPBDr','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-f030-73d3-ad70-8eff9f31765f',NULL,'Javier Schuster','crowe@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','WntN668W63','V1aNlUUN8P','2026-02-02 12:46:31','2026-02-02 12:46:31','4pHHobE3po','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-f03b-70d1-ab1e-8b4580a5cf58',NULL,'Brian Cassin II','hudson.linda@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','Q7SVH4AfUO','t261E5gF7U','2026-02-02 12:46:31','2026-02-02 12:46:31','bhhUSjvB4D','2026-02-02 12:46:31','2026-02-02 12:46:31'),
('019c1e63-f046-703a-a5d5-edbd889ac86d',NULL,'Delphia Stark','bode.abbie@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','1V4ivfZCC1','1mtfqe0APd','2026-02-02 12:46:31','2026-02-02 12:46:31','1AiRwQdsc9','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('019c1e63-f051-730a-a527-72a78c388a3a',NULL,'Gordon Bogan','rodriguez.arely@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','w2IfLMeFK4','1qId6iQCA3','2026-02-02 12:46:31','2026-02-02 12:46:31','H3kJDxSDGS','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('019c1e63-f05c-7355-9aee-a8f1e943df5c',NULL,'Nora Reinger','lschroeder@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','knBvYb5zTQ','xlVr7kTMGT','2026-02-02 12:46:31','2026-02-02 12:46:31','AOq6x4DmDg','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('019c1e63-f068-7004-8e41-7870b82892ce',NULL,'Leora Spinka','ewilkinson@example.org',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','yeZEy4pDeJ','a9mgHuqoVS','2026-02-02 12:46:31','2026-02-02 12:46:31','rKAABPOGl4','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('019c1e63-f073-7326-a419-5ecb61c87bbb',NULL,'Jaeden Purdy V','collier.fae@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','28VnOSCv0r','zaws85a4jH','2026-02-02 12:46:31','2026-02-02 12:46:31','N9JCl98kir','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('019c1e63-f0c0-7122-a38a-327cfae30c6e',NULL,'Lera Schmidt','stevie.okeefe@example.com',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','ZCIKAJZAOv','DxhoUyUbAj','2026-02-02 12:46:31','2026-02-02 12:46:31','ckvw0rQysq','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('019c1e63-f0cd-7032-8393-1c57bfe1170d',NULL,'Mrs. Zoila Wunsch','smith.arnulfo@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','wfOKMXyU1S','pQuFfvRwb1','2026-02-02 12:46:31','2026-02-02 12:46:31','pksIWJ9ZA5','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('019c1e63-f0d7-7161-a84a-944464374579',NULL,'Mr. Gustave Anderson','alta.parisian@example.net',NULL,'019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$ujNOivO3q.Ni09gLnu9WlOsA58ZsvG3U.2akrT5M4ljn5j0RgPh4a','07dI10PAzd','ACBeyfE5zH','2026-02-02 12:46:31','2026-02-02 12:46:31','e8vPXzy0Kn','2026-02-02 12:46:32','2026-02-02 12:46:32'),
('02166c38-5b52-3f93-b327-f59ca44aad8e',NULL,'Kemi Okafor','kemi.okafor.3@sweettooth.com','EMP-LAG003-0003','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,1,NULL,'+234-837-689-8504','465 Schuster Radial Suite 617, Lagos, Lagos State, Nigeria','1998-02-27','female','Nigerian','Abubakar Mohammed','+234-886-804-7778','2025-03-27 23:00:00','active',NULL,NULL,'morning',205426.63,NULL,'TIN-77819960','8305347745','Lactose',NULL,'2025-09-12',3.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('0368494f-608e-3afc-bdfd-c95bd55ed739',NULL,'Blessing Okafor','blessing.okafor.59@sweettooth.com','EMP-LAG003-0059','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,1,NULL,'+234-880-479-1443','538 Josh Wall, Lagos, Lagos State, Nigeria','1995-07-19','female','Nigerian','Emeka Chukwu','+234-811-481-4937','2025-04-22 23:00:00','active',NULL,NULL,'morning',259079.16,NULL,'TIN-98658885','7353849246',NULL,NULL,'2025-09-21',4.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('0529d41f-2018-3075-84ef-cf6c30c0180e',NULL,'Chigozie Okoro','chigozie.okoro.66@sweettooth.com','EMP-LAG003-0066','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,4,NULL,'+234-805-665-5446','35651 Terry Groves Apt. 246, Lagos, Lagos State, Nigeria','1992-09-04','male','Nigerian','Fatima Okoro','+234-900-913-6077','2025-10-01 23:00:00','active',NULL,NULL,'afternoon',133605.87,NULL,'TIN-23703385','1392976192',NULL,NULL,'2025-10-01',4.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('076e56ba-50c7-347f-b8a5-b4e39464ebb2',NULL,'Nneka Okafor','nneka.okafor.94@sweettooth.com','EMP-ENU005-0094','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,2,NULL,'+234-881-262-2238','1284 Stracke Landing, Enugu, Enugu State, Nigeria','2003-09-21','female','Nigerian','Yusuf Johnson','+234-890-975-9542','2023-08-16 23:00:00','active',NULL,NULL,'rotating',304039.04,NULL,'TIN-79278412','3024422623',NULL,NULL,'2025-11-19',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('08aa1e7f-4ae9-37a4-8c06-ad3a64103d5a',NULL,'Abubakar Mohammed','abubakar.mohammed.22@sweettooth.com','EMP-PHC002-0022','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,1,NULL,'+234-866-744-1913','4860 Strosin Road Apt. 828, Port Harcourt, Rivers State, Nigeria','1998-05-10','male','Nigerian','Kemi Mohammed','+234-889-157-8978','2024-02-10 23:00:00','active',NULL,NULL,'rotating',121843.69,NULL,'TIN-11055983','9631382033',NULL,NULL,'2025-10-22',4.1,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('139b21c2-9ef9-3834-ab45-74dc6a54ed0c',NULL,'Chigozie Eze','chigozie.eze.51@sweettooth.com','EMP-PHC002-0051','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,4,NULL,'+234-885-569-7049','88854 Maurine Cliff, Port Harcourt, Rivers State, Nigeria','1984-05-12','male','Nigerian','Nneka Johnson','+234-897-637-9274','2023-06-20 23:00:00','active',NULL,NULL,'flexible',90866.14,NULL,'TIN-12547291','6241825190',NULL,NULL,'2026-01-31',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('17d66ef3-95ca-3e42-8a38-10435e89111f',NULL,'Ibrahim Bello','ibrahim.bello.104@sweettooth.com','EMP-ENU005-0104','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,7,NULL,'+234-851-122-1322','797 Dessie Mission, Enugu, Enugu State, Nigeria','1993-06-07','male','Nigerian','Folake Okoro','+234-885-220-2996','2025-11-11 23:00:00','active',NULL,NULL,'morning',307737.64,NULL,'TIN-94499696','6292494591',NULL,NULL,'2025-08-12',3.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('1aed8ef5-0082-3b97-843a-1c6b2ef2a02e',NULL,'Abubakar Okafor','abubakar.okafor.24@sweettooth.com','EMP-ABJ004-0024','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,1,NULL,'+234-902-845-5190','94152 Jaskolski Mountains Apt. 667, Abuja, FCT State, Nigeria','1996-09-03','male','Nigerian','Folake Okafor','+234-823-216-2392','2025-09-15 23:00:00','active',NULL,NULL,'morning',300161.68,NULL,'TIN-46339168','2503623074',NULL,NULL,'2025-09-11',4.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('1bab56ab-b45e-3db7-a01f-9632a0578755',NULL,'Nneka Chukwu','nneka.chukwu.26@sweettooth.com','EMP-CAL001-0026','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,1,NULL,'+234-831-815-5089','7044 Gerhold Ramp, Calabar, Cross River State, Nigeria','2001-09-06','female','Nigerian','Ibrahim Mohammed','+234-828-507-7410','2025-07-24 23:00:00','active',NULL,NULL,'afternoon',132013.83,NULL,'TIN-82451284','7856884339','Shellfish',NULL,'2026-01-30',4.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('1d706d00-4612-3564-b6e8-b03dbb0d720b',NULL,'Ada Eze','ada.eze.93@sweettooth.com','EMP-ENU005-0093','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,1,NULL,'+234-909-132-2472','751 Thompson Squares, Enugu, Enugu State, Nigeria','1989-11-05','female','Nigerian','Oluwaseun Mohammed','+234-867-375-6371','2024-01-22 23:00:00','active',NULL,NULL,'flexible',256133.33,NULL,'TIN-31312192','6385490218',NULL,NULL,'2025-10-19',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('1d74937d-75eb-32f0-adb7-ad3e20dbaee9',NULL,'Folake Nwankwo','folake.nwankwo.64@sweettooth.com','EMP-LAG003-0064','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,2,NULL,'+234-808-317-4010','6265 Durgan Oval Suite 045, Lagos, Lagos State, Nigeria','1999-05-12','female','Nigerian','Emeka Okoro','+234-847-685-2482','2024-06-16 23:00:00','active',NULL,NULL,'morning',345800.27,NULL,'TIN-66520212','2998213302',NULL,NULL,'2025-08-19',4.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('204cd648-21ad-3e5f-90af-2b0d5279ecaa',NULL,'Kunle Okafor','kunle.okafor.40@sweettooth.com','EMP-CAL001-0040','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,7,NULL,'+234-817-317-5097','405 Hilpert Parks, Calabar, Cross River State, Nigeria','1984-10-15','male','Nigerian','Folake Adebayo','+234-814-334-4729','2025-08-10 23:00:00','active',NULL,NULL,'afternoon',332623.07,NULL,'TIN-97100477','9298968734',NULL,NULL,'2025-12-04',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('238e3565-9304-3049-893e-8b547f70a940',NULL,'Folake Chukwu','folake.chukwu.103@sweettooth.com','EMP-ENU005-0103','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,5,NULL,'+234-806-904-2216','613 Veum Spring, Enugu, Enugu State, Nigeria','2000-11-30','female','Nigerian','Tunde Adebayo','+234-880-327-9666','2025-06-08 23:00:00','active',NULL,NULL,'afternoon',231957.44,NULL,'TIN-90491642','2308836544',NULL,NULL,'2025-09-30',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('245b1a46-456b-3670-a0e9-7ee3b3682f42',NULL,'Hauwa Nwankwo','hauwa.nwankwo.102@sweettooth.com','EMP-ENU005-0102','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,5,NULL,'+234-864-119-5346','88364 Gerhold Motorway Suite 474, Enugu, Enugu State, Nigeria','1986-08-07','female','Nigerian','Ibrahim Okafor','+234-803-276-4116','2024-05-25 23:00:00','active',NULL,NULL,'flexible',295608.59,NULL,'TIN-96572675','4571303039',NULL,NULL,'2026-01-10',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('261d373d-1139-3313-a728-e0de0b1d320b',NULL,'Kemi Mohammed','kemi.mohammed.92@sweettooth.com','EMP-ENU005-0092','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,1,NULL,'+234-839-596-2934','82450 Abernathy Coves Suite 437, Enugu, Enugu State, Nigeria','1987-01-23','female','Nigerian','Chukwuemeka Okoro','+234-907-838-5428','2024-11-02 23:00:00','active',NULL,NULL,'morning',261172.18,NULL,'TIN-65418154','3821763737',NULL,NULL,'2026-01-08',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('26d46859-baa8-3aa7-89bd-4e60fe9b5892',NULL,'Kemi Aliyu','kemi.aliyu.88@sweettooth.com','EMP-ABJ004-0088','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,7,NULL,'+234-855-435-3419','2998 Laisha Haven Suite 843, Abuja, FCT State, Nigeria','2000-08-13','female','Nigerian','Obinna Aliyu','+234-808-752-2929','2024-01-05 23:00:00','active',NULL,NULL,'morning',208422.21,NULL,'TIN-21985231','7014854274',NULL,NULL,'2025-08-15',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('272eea60-73fc-3bab-afd3-95d853086d39',NULL,'Nneka Okafor','nneka.okafor.69@sweettooth.com','EMP-LAG003-0069','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,5,NULL,'+234-824-421-1174','33040 Johnson Port, Lagos, Lagos State, Nigeria','1993-07-02','female','Nigerian','Chigozie Eze','+234-891-879-5476','2023-03-29 23:00:00','active',NULL,NULL,'rotating',107981.84,NULL,'TIN-68109434','8827022254',NULL,NULL,'2025-11-09',4.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('2fa819ad-148a-363d-8617-926c9f427fba',NULL,'Ngozi Okoro','ngozi.okoro.44@sweettooth.com','EMP-PHC002-0044','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,1,NULL,'+234-893-342-8712','365 Ivy Islands Apt. 214, Port Harcourt, Rivers State, Nigeria','1994-03-23','female','Nigerian','Obinna Okoro','+234-908-991-5237','2024-04-16 23:00:00','active',NULL,NULL,'rotating',325044.27,NULL,'TIN-46470169','3753962022',NULL,NULL,'2025-09-23',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('34bd1150-1566-333d-9302-21ac62c17eaa',NULL,'Abubakar Adebayo','abubakar.adebayo.9@sweettooth.com','EMP-ABJ004-0009','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,1,NULL,'+234-906-301-6657','99515 Mavis Courts Suite 892, Abuja, FCT State, Nigeria','1991-07-04','male','Nigerian','Ngozi Mohammed','+234-810-596-2392','2023-09-25 23:00:00','active',NULL,NULL,'flexible',148174.55,NULL,'TIN-95104737','1036321834',NULL,NULL,'2025-08-15',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('3644514b-03df-3da6-9707-7d7685bb67bb',NULL,'Kunle Johnson','kunle.johnson.96@sweettooth.com','EMP-ENU005-0096','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,2,NULL,'+234-854-539-5178','242 Dejah Plains, Enugu, Enugu State, Nigeria','1994-01-15','male','Nigerian','Fatima Mohammed','+234-817-807-1605','2023-04-29 23:00:00','active',NULL,NULL,'morning',255465.81,NULL,'TIN-20168209','4021701618',NULL,NULL,'2025-08-21',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('38afa22b-f11c-3e78-87de-130edc2f9bba',NULL,'Obinna Bello','obinna.bello.48@sweettooth.com','EMP-PHC002-0048','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,2,NULL,'+234-861-621-1769','98303 Glennie Mountains Suite 516, Port Harcourt, Rivers State, Nigeria','1989-11-21','male','Nigerian','Folake Okafor','+234-891-390-5308','2025-02-05 23:00:00','active',NULL,NULL,'rotating',133530.00,NULL,'TIN-89959499','3840836742',NULL,NULL,'2025-11-18',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('3a827a3e-e440-3e82-ae15-0c145475040b',NULL,'Fatima Johnson','fatima.johnson.56@sweettooth.com','EMP-PHC002-0056','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,7,NULL,'+234-825-348-2835','621 Hegmann Center, Port Harcourt, Rivers State, Nigeria','1992-03-01','female','Nigerian','Chukwuemeka Nwankwo','+234-816-482-3210','2024-10-05 23:00:00','active',NULL,NULL,'afternoon',85507.34,NULL,'TIN-79986157','4457426061','Lactose',NULL,'2026-01-29',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('3d6ba483-9200-3a8b-8128-afcd03af29e7',NULL,'Fatima Aliyu','fatima.aliyu.28@sweettooth.com','EMP-CAL001-0028','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,1,NULL,'+234-885-974-6102','6540 Laura Station Apt. 704, Calabar, Cross River State, Nigeria','1990-07-20','female','Nigerian','Emeka Williams','+234-880-208-6132','2023-10-18 23:00:00','active',NULL,NULL,'flexible',158848.10,NULL,'TIN-72143958','7852312395',NULL,NULL,'2026-01-09',3.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('3d989def-3a3f-38b7-9064-d33d53e56497',NULL,'Yusuf Ogunleye','yusuf.ogunleye.70@sweettooth.com','EMP-LAG003-0070','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,5,NULL,'+234-861-850-7776','18099 Runte Turnpike, Lagos, Lagos State, Nigeria','1982-10-17','male','Nigerian','Blessing Bello','+234-810-155-7486','2024-02-20 23:00:00','active',NULL,NULL,'afternoon',349657.81,NULL,'TIN-73766522','8859104053',NULL,NULL,'2025-12-07',4.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('40ad6863-710c-32d1-aebb-579fcf494961',NULL,'Folake Mohammed','folake.mohammed.13@sweettooth.com','EMP-LAG003-0013','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,7,NULL,'+234-868-128-7489','38141 Carole Road Suite 112, Lagos, Lagos State, Nigeria','1985-02-24','female','Nigerian','Abubakar Adebayo','+234-877-663-2458','2025-03-05 23:00:00','active',NULL,NULL,'rotating',135098.79,NULL,'TIN-68450097','8847981971',NULL,NULL,'2025-11-07',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('40ec8cc7-3fa0-37b0-b074-ac782c99c385',NULL,'Ngozi Mohammed','ngozi.mohammed.45@sweettooth.com','EMP-PHC002-0045','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,1,NULL,'+234-841-121-7006','509 Toy Turnpike, Port Harcourt, Rivers State, Nigeria','1990-03-04','female','Nigerian','Chigozie Chukwu','+234-823-810-3863','2023-03-22 23:00:00','active',NULL,NULL,'morning',169164.81,NULL,'TIN-36910486','8902854075',NULL,NULL,'2025-09-13',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('420528dd-e794-3f64-9ed2-2707d27efa20',NULL,'Ada Aliyu','ada.aliyu.38@sweettooth.com','EMP-CAL001-0038','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,5,NULL,'+234-869-613-4256','40506 Olson Road Apt. 990, Calabar, Cross River State, Nigeria','2003-04-22','female','Nigerian','Kunle Okafor','+234-878-894-7793','2024-01-10 23:00:00','active',NULL,NULL,'flexible',100908.74,NULL,'TIN-30136155','1797939558',NULL,NULL,'2025-10-13',3.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('44422fd8-eab6-3afa-8683-3530874bf82d',NULL,'Kemi Adebayo','kemi.adebayo.15@sweettooth.com','EMP-ENU005-0015','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,7,NULL,'+234-822-366-7270','31657 Witting Corner, Enugu, Enugu State, Nigeria','1991-05-26','female','Nigerian','Oluwaseun Mohammed','+234-907-558-8704','2023-02-25 23:00:00','active',NULL,NULL,'flexible',82584.53,NULL,'TIN-58299333','8968070433',NULL,NULL,'2026-01-04',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('45b5b9ed-8865-3595-8194-a6087387667c',NULL,'Chigozie Adebayo','chigozie.adebayo.95@sweettooth.com','EMP-ENU005-0095','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,2,NULL,'+234-820-133-9911','15441 Owen Haven Suite 266, Enugu, Enugu State, Nigeria','1998-06-04','male','Nigerian','Hauwa Chukwu','+234-852-654-2626','2024-09-01 23:00:00','active',NULL,NULL,'flexible',138151.88,NULL,'TIN-95406185','6287350079',NULL,NULL,'2025-12-08',4.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('464bad5d-34c0-34f6-8c2f-7828c8b9bb62',NULL,'Chigozie Adebayo','chigozie.adebayo.65@sweettooth.com','EMP-LAG003-0065','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,4,NULL,'+234-846-132-4595','8624 Mckayla Avenue, Lagos, Lagos State, Nigeria','1987-09-28','male','Nigerian','Blessing Okafor','+234-830-333-1174','2024-01-26 23:00:00','active',NULL,NULL,'morning',171802.92,NULL,'TIN-64507522','0290889777','Peanuts',NULL,'2025-08-06',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('4a4d8927-f11a-33ae-97d5-19b712cf318b',NULL,'Hauwa Adebayo','hauwa.adebayo.63@sweettooth.com','EMP-LAG003-0063','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,2,NULL,'+234-854-689-8928','11948 Charlotte Views Suite 964, Lagos, Lagos State, Nigeria','1997-07-17','female','Nigerian','Ibrahim Mohammed','+234-875-173-1508','2023-12-05 23:00:00','active',NULL,NULL,'afternoon',344034.53,NULL,'TIN-40605060','1768625498',NULL,NULL,'2025-10-18',4.2,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('4db015fa-6d1f-3317-beed-8b0276bc26e6',NULL,'Tunde Adebayo','tunde.adebayo.7@sweettooth.com','EMP-PHC002-0007','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,1,NULL,'+234-899-609-5518','593 Elsie Springs Apt. 020, Port Harcourt, Rivers State, Nigeria','1986-09-26','male','Nigerian','Ngozi Adebayo','+234-803-154-7313','2025-05-02 23:00:00','active',NULL,NULL,'morning',311182.19,NULL,'TIN-76357349','8973610973',NULL,NULL,'2025-08-25',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('4e2ab46d-0dd6-3c35-a602-1db1f1d3d82c',NULL,'Chukwuemeka Mohammed','chukwuemeka.mohammed.8@sweettooth.com','EMP-LAG003-0008','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,1,NULL,'+234-841-339-9660','9031 Goyette Springs Apt. 994, Lagos, Lagos State, Nigeria','1985-07-08','male','Nigerian','Kemi Okafor','+234-857-828-5620','2024-08-28 23:00:00','active',NULL,NULL,'flexible',331536.45,NULL,'TIN-60749639','6338611036',NULL,NULL,'2025-12-27',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('4f8c7c83-0cfb-3462-8f82-a7b7d2b408d6',NULL,'Nneka Nwankwo','nneka.nwankwo.67@sweettooth.com','EMP-LAG003-0067','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,4,NULL,'+234-900-245-6999','13394 Jamal Overpass Apt. 926, Lagos, Lagos State, Nigeria','1987-07-21','female','Nigerian','Emeka Johnson','+234-829-193-8849','2023-08-25 23:00:00','active',NULL,NULL,'morning',147028.56,NULL,'TIN-91821410','4464680435',NULL,NULL,'2025-11-14',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('51062e63-1529-38d6-961d-18a9f579bad0',NULL,'Chioma Adebayo','chioma.adebayo.10@sweettooth.com','EMP-ENU005-0010','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,1,NULL,'+234-849-812-8276','6622 Merl Burgs Suite 882, Enugu, Enugu State, Nigeria','2000-08-27','female','Nigerian','Chukwuemeka Mohammed','+234-870-498-3101','2025-03-16 23:00:00','active',NULL,NULL,'rotating',183047.70,NULL,'TIN-51928323','1257225229',NULL,NULL,'2025-11-26',5.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('53ccec4b-0a09-3be4-8207-ff667e1887df',NULL,'Kemi Mohammed','kemi.mohammed.11@sweettooth.com','EMP-CAL001-0011','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,7,NULL,'+234-890-997-3583','9513 Jayme Springs, Calabar, Cross River State, Nigeria','2002-04-03','female','Nigerian','Oluwaseun Adebayo','+234-831-955-6627','2023-12-26 23:00:00','active',NULL,NULL,'afternoon',254959.88,NULL,'TIN-45956648','5419243138',NULL,NULL,'2025-08-14',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('616ae92b-d9b8-3c3d-a22e-92223b27e9c3',NULL,'Folake Ogunleye','folake.ogunleye.75@sweettooth.com','EMP-ABJ004-0075','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,1,NULL,'+234-868-357-7086','8973 Batz Green, Abuja, FCT State, Nigeria','2000-05-08','female','Nigerian','Chukwuemeka Okafor','+234-872-588-8486','2023-05-27 23:00:00','active',NULL,NULL,'rotating',208697.29,NULL,'TIN-98917570','6255530494',NULL,NULL,'2026-01-22',4.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('616b24a8-a1c7-34db-a35f-67968fcbae0d',NULL,'Emeka Adebayo','emeka.adebayo.31@sweettooth.com','EMP-CAL001-0031','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,2,NULL,'+234-905-682-2276','206 Sporer Forest, Calabar, Cross River State, Nigeria','1995-08-28','male','Nigerian','Kemi Aliyu','+234-822-341-1126','2025-08-25 23:00:00','active',NULL,NULL,'afternoon',104136.89,NULL,'TIN-74809078','0065057492',NULL,NULL,'2026-01-08',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('678deb6e-1e92-3623-b038-28f7ccdf6172',NULL,'Emeka Eze','emeka.eze.41@sweettooth.com','EMP-CAL001-0041','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,7,NULL,'+234-869-377-2116','387 Fahey Shore, Calabar, Cross River State, Nigeria','1990-01-22','male','Nigerian','Ada Ogunleye','+234-853-353-9772','2024-12-31 23:00:00','active',NULL,NULL,'rotating',348551.95,NULL,'TIN-32141491','5399658880',NULL,NULL,'2025-09-30',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('6d86654a-0152-36e1-a587-a71caa84973a',NULL,'Obinna Williams','obinna.williams.68@sweettooth.com','EMP-LAG003-0068','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,4,NULL,'+234-822-219-9242','3131 Bayer Manors, Lagos, Lagos State, Nigeria','1986-02-21','male','Nigerian','Folake Okoro','+234-864-116-2746','2024-07-30 23:00:00','active',NULL,NULL,'flexible',175901.15,NULL,'TIN-59103229','0921819151',NULL,NULL,'2026-01-04',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('6ec92775-5d45-33ab-b271-8c63ef5bfb22',NULL,'Amina Adebayo','amina.adebayo.37@sweettooth.com','EMP-CAL001-0037','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,5,NULL,'+234-860-415-7390','441 Schultz Valleys Apt. 540, Calabar, Cross River State, Nigeria','1994-03-26','female','Nigerian','Ibrahim Mohammed','+234-897-712-8993','2024-01-19 23:00:00','active',NULL,NULL,'afternoon',177968.18,NULL,'TIN-75376672','1246526999',NULL,NULL,'2025-09-03',4.2,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('6fdb1848-5d9e-3cd2-8379-206a33133a26',NULL,'Fatima Adebayo','fatima.adebayo.33@sweettooth.com','EMP-CAL001-0033','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,4,NULL,'+234-890-771-6178','5002 Pacocha Motorway, Calabar, Cross River State, Nigeria','1984-08-26','female','Nigerian','Yusuf Okoro','+234-842-806-6968','2023-09-22 23:00:00','active',NULL,NULL,'flexible',203588.26,NULL,'TIN-65203363','7312880258',NULL,NULL,'2025-10-19',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('741c8f01-162e-39ad-9286-5388355d377e',NULL,'Yusuf Aliyu','yusuf.aliyu.79@sweettooth.com','EMP-ABJ004-0079','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,2,NULL,'+234-899-133-4624','378 Huels Station, Abuja, FCT State, Nigeria','1981-11-29','male','Nigerian','Ada Adebayo','+234-850-164-1425','2024-04-28 23:00:00','active',NULL,NULL,'rotating',168866.84,NULL,'TIN-35141627','8226709927',NULL,NULL,'2025-12-31',3.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('772f94c2-02fa-3034-ad10-fc9c5329fc59',NULL,'Yusuf Bello','yusuf.bello.100@sweettooth.com','EMP-ENU005-0100','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,4,NULL,'+234-857-353-5188','6983 Feest Loop, Enugu, Enugu State, Nigeria','1987-02-01','male','Nigerian','Kemi Ogunleye','+234-883-444-3459','2025-12-28 23:00:00','active',NULL,NULL,'morning',327059.90,NULL,'TIN-23398318','6227374314',NULL,NULL,'2025-08-15',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('77ff5796-a534-32c6-aa9c-d50189651422',NULL,'Kemi Okafor','kemi.okafor.16@sweettooth.com','EMP-CAL001-0016','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,4,NULL,'+234-899-800-2783','3483 Haskell Divide Apt. 260, Calabar, Cross River State, Nigeria','2000-04-29','female','Nigerian','Emeka Adebayo','+234-900-312-4896','2024-07-17 23:00:00','active',NULL,NULL,'flexible',100160.69,NULL,'TIN-20736281','8107247930',NULL,NULL,'2026-01-24',4.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('7c9fb706-83ad-3801-8f4a-95c3ea75bc52',NULL,'Blessing Nwankwo','blessing.nwankwo.82@sweettooth.com','EMP-ABJ004-0082','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,4,NULL,'+234-843-939-9849','2842 Karlee Streets Suite 567, Abuja, FCT State, Nigeria','1994-09-16','female','Nigerian','Yusuf Okafor','+234-860-153-2981','2025-07-04 23:00:00','active',NULL,NULL,'rotating',330757.92,NULL,'TIN-14464875','1445379132','Shellfish',NULL,'2025-11-08',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('7d4e488a-6348-325a-a163-2a11d71ff672',NULL,'Emeka Nwankwo','emeka.nwankwo.81@sweettooth.com','EMP-ABJ004-0081','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,4,NULL,'+234-801-938-9354','183 Moen Common Apt. 615, Abuja, FCT State, Nigeria','1986-09-10','male','Nigerian','Amina Aliyu','+234-840-866-3961','2023-10-03 23:00:00','active',NULL,NULL,'afternoon',261092.64,NULL,'TIN-82662431','4786059528',NULL,NULL,'2025-08-08',4.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('7eec6bd2-d402-3c80-917f-819af4f05f91',NULL,'Chioma Okafor','chioma.okafor.14@sweettooth.com','EMP-ABJ004-0014','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,7,NULL,'+234-887-828-8134','41555 Newell Pines Suite 457, Abuja, FCT State, Nigeria','1997-06-18','female','Nigerian','Abubakar Mohammed','+234-810-626-4586','2023-05-20 23:00:00','active',NULL,NULL,'morning',321522.47,NULL,'TIN-87946442','3979418425',NULL,NULL,'2025-10-18',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('7f6b3885-1069-3265-a471-38768fecbbf3',NULL,'Folake Okoro','folake.okoro.58@sweettooth.com','EMP-LAG003-0058','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,1,NULL,'+234-870-654-4618','944 Blanda Place Apt. 449, Lagos, Lagos State, Nigeria','1983-11-18','female','Nigerian','Oluwaseun Johnson','+234-866-477-3763','2024-01-15 23:00:00','active',NULL,NULL,'morning',215212.93,NULL,'TIN-51234577','5687633338',NULL,NULL,'2025-12-14',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('7f8f572d-85a1-3d53-b212-fc6c5b159653',NULL,'Tunde Ogunleye','tunde.ogunleye.80@sweettooth.com','EMP-ABJ004-0080','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,2,NULL,'+234-888-448-3843','2329 Jerrell Squares, Abuja, FCT State, Nigeria','2002-02-06','male','Nigerian','Ngozi Nwankwo','+234-804-390-5563','2025-12-23 23:00:00','active',NULL,NULL,'flexible',214574.41,NULL,'TIN-58378703','0675565309',NULL,NULL,'2025-12-27',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('800a6fd1-7366-3196-ab78-74b781a1fe5b',NULL,'Ngozi Johnson','ngozi.johnson.30@sweettooth.com','EMP-CAL001-0030','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,2,NULL,'+234-903-565-4578','83896 Baumbach Trail Suite 880, Calabar, Cross River State, Nigeria','1986-01-27','female','Nigerian','Abubakar Eze','+234-830-125-4004','2024-02-01 23:00:00','active',NULL,NULL,'flexible',87411.74,NULL,'TIN-98370451','6675108062','Peanuts',NULL,'2025-10-19',4.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('80170dd7-7a24-3ff7-a0de-cb061542eaff',NULL,'Folake Bello','folake.bello.97@sweettooth.com','EMP-ENU005-0097','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,4,NULL,'+234-885-129-8006','9480 Brigitte Island Apt. 665, Enugu, Enugu State, Nigeria','1989-01-03','female','Nigerian','Ibrahim Bello','+234-849-824-7703','2023-11-14 23:00:00','active',NULL,NULL,'flexible',108636.98,NULL,'TIN-87959962','8501040730',NULL,NULL,'2026-02-01',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('81ff45b4-8087-3328-959f-a357e64ca963',NULL,'Obinna Williams','obinna.williams.77@sweettooth.com','EMP-ABJ004-0077','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,1,NULL,'+234-811-800-1835','147 Reginald Turnpike Apt. 057, Abuja, FCT State, Nigeria','1995-10-20','male','Nigerian','Kemi Okoro','+234-883-693-4574','2023-11-15 23:00:00','active',NULL,NULL,'flexible',270168.38,NULL,'TIN-31592754','8999895284',NULL,NULL,'2025-09-12',4.2,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('84ded2e2-2100-3c19-9253-1cbc35ed3d77',NULL,'Obinna Williams','obinna.williams.91@sweettooth.com','EMP-ENU005-0091','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,1,NULL,'+234-857-379-4227','119 Sporer Land Apt. 805, Enugu, Enugu State, Nigeria','1984-03-12','male','Nigerian','Ada Adebayo','+234-840-190-8746','2023-08-02 23:00:00','active',NULL,NULL,'morning',219606.54,NULL,'TIN-49405858','0670254642',NULL,NULL,'2025-08-03',3.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('862994cc-393b-37d4-965d-bd74be6ffee2',NULL,'Tunde Adebayo','tunde.adebayo.99@sweettooth.com','EMP-ENU005-0099','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,4,NULL,'+234-853-205-6395','300 Funk Station, Enugu, Enugu State, Nigeria','1984-08-28','male','Nigerian','Fatima Chukwu','+234-816-436-2914','2024-03-24 23:00:00','active',NULL,NULL,'rotating',219785.91,NULL,'TIN-71161259','3921262641',NULL,NULL,'2025-08-21',4.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('892bd8f9-23cb-3b26-b44b-41cad260ea5f',NULL,'Chukwuemeka Bello','chukwuemeka.bello.98@sweettooth.com','EMP-ENU005-0098','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,4,NULL,'+234-823-483-1371','516 Krajcik Extensions Suite 579, Enugu, Enugu State, Nigeria','1986-07-12','male','Nigerian','Chioma Williams','+234-838-417-5206','2024-03-08 23:00:00','active',NULL,NULL,'flexible',348746.87,NULL,'TIN-85627739','7578194617',NULL,NULL,'2025-11-01',4.2,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('8d1c2dbe-1e9b-35b9-9686-9c7636f801bc',NULL,'Fatima Chukwu','fatima.chukwu.105@sweettooth.com','EMP-ENU005-0105','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,7,NULL,'+234-874-374-6318','80555 Cormier Vista Apt. 081, Enugu, Enugu State, Nigeria','1999-12-24','female','Nigerian','Oluwaseun Mohammed','+234-894-354-1436','2024-10-27 23:00:00','active',NULL,NULL,'morning',244986.44,NULL,'TIN-43038014','3675397802','Peanuts',NULL,'2025-08-28',4.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('8ff5d862-163e-3260-b1f9-9622caf4c730',NULL,'Abubakar Aliyu','abubakar.aliyu.36@sweettooth.com','EMP-CAL001-0036','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,4,NULL,'+234-897-978-5723','215 Keagan Avenue Apt. 545, Calabar, Cross River State, Nigeria','1983-09-18','male','Nigerian','Ada Aliyu','+234-838-346-9695','2025-09-14 23:00:00','active',NULL,NULL,'morning',221440.55,NULL,'TIN-51144511','8255465299',NULL,NULL,'2025-10-22',3.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('903f0a97-fec4-30ef-8054-20acbc202356',NULL,'Abubakar Okoro','abubakar.okoro.61@sweettooth.com','EMP-LAG003-0061','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,1,NULL,'+234-852-103-9077','84904 Mossie Shoals, Lagos, Lagos State, Nigeria','1986-08-04','male','Nigerian','Blessing Aliyu','+234-812-725-2624','2024-05-07 23:00:00','active',NULL,NULL,'morning',159514.31,NULL,'TIN-49167797','2153978227',NULL,NULL,'2025-11-26',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('9120f862-b5f6-3576-8967-1bb3124957e3',NULL,'Ada Ogunleye','ada.ogunleye.76@sweettooth.com','EMP-ABJ004-0076','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,1,NULL,'+234-802-339-7037','3700 Lehner Crest Apt. 165, Abuja, FCT State, Nigeria','1990-01-07','female','Nigerian','Kunle Eze','+234-886-831-7492','2025-05-31 23:00:00','active',NULL,NULL,'afternoon',232808.55,NULL,'TIN-42606747','4378770370','Shellfish',NULL,'2025-10-19',3.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('9155bee1-4cde-3051-b492-e8f71ea161cb',NULL,'Emeka Nwankwo','emeka.nwankwo.29@sweettooth.com','EMP-CAL001-0029','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,1,NULL,'+234-878-696-7044','24738 Kihn Vista Suite 766, Calabar, Cross River State, Nigeria','2001-06-10','male','Nigerian','Ada Eze','+234-815-565-1974','2025-04-26 23:00:00','active',NULL,NULL,'morning',302833.27,NULL,'TIN-68709740','9632693270','Lactose',NULL,'2025-08-14',4.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('92e5b61f-0df8-3c47-a9b4-0f628af3bd3b',NULL,'Yusuf Chukwu','yusuf.chukwu.72@sweettooth.com','EMP-LAG003-0072','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,7,NULL,'+234-848-371-2654','1645 Stark Ford, Lagos, Lagos State, Nigeria','1982-02-06','male','Nigerian','Hauwa Aliyu','+234-858-796-3350','2024-11-20 23:00:00','active',NULL,NULL,'morning',257403.12,NULL,'TIN-56103206','4468958445','Peanuts',NULL,'2025-12-21',4.2,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('933001bd-35ce-3253-bfe8-795c1a9b9fa1',NULL,'Oluwaseun Williams','oluwaseun.williams.89@sweettooth.com','EMP-ABJ004-0089','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,7,NULL,'+234-903-810-8825','6896 Granville Islands, Abuja, FCT State, Nigeria','1989-08-01','male','Nigerian','Hauwa Chukwu','+234-837-936-3265','2025-01-07 23:00:00','active',NULL,NULL,'rotating',106181.86,NULL,'TIN-15118958','3104985038',NULL,NULL,'2026-01-17',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('973607ee-523b-32ce-a8fb-e65085bc7b35',NULL,'Tunde Johnson','tunde.johnson.32@sweettooth.com','EMP-CAL001-0032','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,2,NULL,'+234-903-373-7946','273 Johnston Ports Suite 605, Calabar, Cross River State, Nigeria','1983-04-20','male','Nigerian','Ada Johnson','+234-898-230-6032','2025-06-10 23:00:00','active',NULL,NULL,'afternoon',234209.41,NULL,'TIN-10296053','0594618747',NULL,NULL,'2025-08-22',5.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('9ba216ac-fa68-36f8-96ed-59b7ba33f82a',NULL,'Chukwuemeka Williams','chukwuemeka.williams.35@sweettooth.com','EMP-CAL001-0035','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,4,NULL,'+234-879-866-6536','1681 Hillard Mill, Calabar, Cross River State, Nigeria','1994-04-30','male','Nigerian','Amina Williams','+234-895-143-2236','2024-10-23 23:00:00','active',NULL,NULL,'afternoon',88876.26,NULL,'TIN-67686504','0968831706',NULL,NULL,'2025-09-08',4.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('9be27a9a-0b32-3b51-b289-c279cabb6fe0',NULL,'Ada Nwankwo','ada.nwankwo.52@sweettooth.com','EMP-PHC002-0052','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,4,NULL,'+234-899-190-6268','4124 Kulas Motorway, Port Harcourt, Rivers State, Nigeria','1984-09-14','female','Nigerian','Emeka Williams','+234-904-650-8646','2024-11-13 23:00:00','active',NULL,NULL,'afternoon',184214.40,NULL,'TIN-98875962','9866083209',NULL,NULL,'2026-01-16',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('a118e86d-ab6b-3cb2-9f17-2800a47c319c',NULL,'Emeka Mohammed','emeka.mohammed.60@sweettooth.com','EMP-LAG003-0060','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,1,NULL,'+234-808-228-3461','1504 Linnie Locks Apt. 588, Lagos, Lagos State, Nigeria','1997-05-03','male','Nigerian','Folake Nwankwo','+234-878-341-8506','2025-02-24 23:00:00','active',NULL,NULL,'morning',172376.61,NULL,'TIN-11130560','7602251075',NULL,NULL,'2025-08-25',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('a332ef26-2af7-314b-b314-ad3727f84699',NULL,'Blessing Nwankwo','blessing.nwankwo.43@sweettooth.com','EMP-PHC002-0043','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,1,NULL,'+234-831-173-3629','9427 Lind Courts, Port Harcourt, Rivers State, Nigeria','1993-12-06','female','Nigerian','Kunle Chukwu','+234-882-598-4337','2023-08-11 23:00:00','active',NULL,NULL,'afternoon',165613.09,NULL,'TIN-59991159','3841498591',NULL,NULL,'2025-09-16',4.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('a6c75990-2166-3e02-b0b0-6fa78575eb87',NULL,'Emeka Okafor','emeka.okafor.6@sweettooth.com','EMP-CAL001-0006','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,1,NULL,'+234-843-308-7212','7826 Mertz Ridge Apt. 844, Calabar, Cross River State, Nigeria','1994-04-05','male','Nigerian','Amina Mohammed','+234-824-792-7692','2025-01-02 23:00:00','active',NULL,NULL,'afternoon',204523.16,NULL,'TIN-70004282','8912675716',NULL,NULL,'2026-01-30',4.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('a8bd8824-4ad5-3693-9c47-10d8ef22ee01',NULL,'Tunde Okafor','tunde.okafor.23@sweettooth.com','EMP-LAG003-0023','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,1,NULL,'+234-887-270-5801','998 Velda Throughway Suite 362, Lagos, Lagos State, Nigeria','2003-06-04','male','Nigerian','Amina Mohammed','+234-849-145-2872','2024-09-09 23:00:00','active',NULL,NULL,'flexible',190511.72,NULL,'TIN-69951421','1159048875',NULL,NULL,'2025-11-08',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('a927f87c-901f-361c-b59c-2c1c6ef57dd1',NULL,'Ngozi Adebayo','ngozi.adebayo.12@sweettooth.com','EMP-PHC002-0012','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,7,NULL,'+234-892-646-8329','181 Macejkovic Streets Suite 162, Port Harcourt, Rivers State, Nigeria','2001-11-11','female','Nigerian','Oluwaseun Mohammed','+234-836-905-1278','2024-11-15 23:00:00','active',NULL,NULL,'rotating',297478.11,NULL,'TIN-23377998','9493413306',NULL,NULL,'2025-08-03',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('aada24bd-3632-3379-afcb-b052f74ecb2a',NULL,'Tunde Bello','tunde.bello.101@sweettooth.com','EMP-ENU005-0101','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,5,NULL,'+234-823-897-5819','1507 Cydney Divide, Enugu, Enugu State, Nigeria','1992-01-17','male','Nigerian','Chioma Adebayo','+234-852-817-5408','2025-01-03 23:00:00','active',NULL,NULL,'rotating',122484.79,NULL,'TIN-54729539','5568703999',NULL,NULL,'2025-11-26',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('ab2d4f51-3ea1-3643-a039-793bce8bc173',NULL,'Chigozie Eze','chigozie.eze.84@sweettooth.com','EMP-ABJ004-0084','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,4,NULL,'+234-810-559-5445','87372 Cathrine Extensions, Abuja, FCT State, Nigeria','1998-09-05','male','Nigerian','Ada Mohammed','+234-812-617-4730','2024-11-25 23:00:00','active',NULL,NULL,'afternoon',192359.20,NULL,'TIN-77790725','9790735381',NULL,NULL,'2025-09-06',4.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('ab38e16a-37d6-3ef2-8ab5-2569ced9e2e1',NULL,'Folake Adebayo','folake.adebayo.21@sweettooth.com','EMP-CAL001-0021','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,1,NULL,'+234-832-826-5337','3100 Kaylie Glens, Calabar, Cross River State, Nigeria','2000-06-15','female','Nigerian','Tunde Mohammed','+234-816-157-6539','2024-01-11 23:00:00','active',NULL,NULL,'morning',169841.67,NULL,'TIN-44835788','1565454780',NULL,NULL,'2025-10-21',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('abf80b71-a68c-340a-ba65-cd24f8a81918',NULL,'Fatima Adebayo','fatima.adebayo.78@sweettooth.com','EMP-ABJ004-0078','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,2,NULL,'+234-869-185-6040','692 Von Knolls Apt. 877, Abuja, FCT State, Nigeria','1984-01-08','female','Nigerian','Oluwaseun Chukwu','+234-897-609-1902','2025-01-12 23:00:00','active',NULL,NULL,'afternoon',135768.01,NULL,'TIN-85291865','5302747196',NULL,NULL,'2025-10-19',4.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('ac146832-5af0-3f63-900b-43c34c127717',NULL,'Ada Bello','ada.bello.90@sweettooth.com','EMP-ENU005-0090','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,1,NULL,'+234-804-684-7642','4911 Joe Greens Apt. 580, Enugu, Enugu State, Nigeria','2003-03-15','female','Nigerian','Obinna Aliyu','+234-854-519-8999','2025-06-28 23:00:00','active',NULL,NULL,'morning',103322.96,NULL,'TIN-73668936','6039381693',NULL,NULL,'2025-08-03',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('b41b5850-7463-34f7-a180-a78b3f25364e',NULL,'Chioma Adebayo','chioma.adebayo.1@sweettooth.com','EMP-CAL001-0001','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,1,NULL,'+234-858-426-3591','861 Towne Brook Apt. 603, Calabar, Cross River State, Nigeria','2003-01-23','female','Nigerian','Chukwuemeka Adebayo','+234-896-382-8706','2025-05-11 23:00:00','active',NULL,NULL,'rotating',288446.32,NULL,'TIN-13851783','2365370648',NULL,NULL,'2025-08-09',4.2,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('b4712989-2497-3bfa-a0e4-4f6f66f9cc6e',NULL,'Hauwa Ogunleye','hauwa.ogunleye.42@sweettooth.com','EMP-PHC002-0042','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,1,NULL,'+234-891-648-4852','933 Beatty Spring Suite 995, Port Harcourt, Rivers State, Nigeria','1998-07-16','female','Nigerian','Tunde Johnson','+234-894-304-1673','2023-11-27 23:00:00','active',NULL,NULL,'rotating',187327.89,NULL,'TIN-49798150','1385586609',NULL,NULL,'2025-08-20',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('b4ea499f-42fb-3d72-95e9-94b4d220a91d',NULL,'Abubakar Bello','abubakar.bello.34@sweettooth.com','EMP-CAL001-0034','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,4,NULL,'+234-893-264-7564','2237 Hackett Lights, Calabar, Cross River State, Nigeria','1990-06-27','male','Nigerian','Ngozi Mohammed','+234-838-740-1419','2023-04-25 23:00:00','active',NULL,NULL,'flexible',233565.32,NULL,'TIN-26865183','8023087767',NULL,NULL,'2025-11-27',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('bbb93c6a-f068-3e5c-93ef-4e5c03e80b2b',NULL,'Abubakar Nwankwo','abubakar.nwankwo.62@sweettooth.com','EMP-LAG003-0062','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,2,NULL,'+234-870-675-1365','6355 Andrew Ford Apt. 777, Lagos, Lagos State, Nigeria','1997-05-06','male','Nigerian','Ngozi Aliyu','+234-887-358-4292','2024-02-21 23:00:00','active',NULL,NULL,'flexible',293673.49,NULL,'TIN-78851955','4736471764',NULL,NULL,'2025-11-19',4.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('bf16d7d7-9032-386a-bfec-24957cc698b3',NULL,'Obinna Chukwu','obinna.chukwu.83@sweettooth.com','EMP-ABJ004-0083','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,4,NULL,'+234-893-138-9151','3481 Vernon Coves, Abuja, FCT State, Nigeria','1994-11-17','male','Nigerian','Amina Chukwu','+234-889-575-9669','2024-06-24 23:00:00','active',NULL,NULL,'rotating',321462.57,NULL,'TIN-63444063','2618073854',NULL,NULL,'2025-11-18',3.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('c50afcf1-2591-339a-976a-6f214a7b55bb',NULL,'Yusuf Mohammed','yusuf.mohammed.50@sweettooth.com','EMP-PHC002-0050','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,4,NULL,'+234-869-114-9224','1259 Kulas Ramp Suite 139, Port Harcourt, Rivers State, Nigeria','1984-12-03','male','Nigerian','Blessing Williams','+234-802-110-3454','2025-11-14 23:00:00','active',NULL,NULL,'rotating',134056.10,NULL,'TIN-18251317','3903180923',NULL,NULL,'2025-08-03',4.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('cb746d53-40d4-33da-97d3-fdb0f672bf97',NULL,'Ada Okafor','ada.okafor.73@sweettooth.com','EMP-LAG003-0073','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,7,NULL,'+234-862-185-4179','69121 Bahringer Squares, Lagos, Lagos State, Nigeria','1996-07-11','female','Nigerian','Tunde Okoro','+234-834-105-2544','2023-07-31 23:00:00','active',NULL,NULL,'rotating',341093.73,NULL,'TIN-43098564','1019518949',NULL,NULL,'2025-08-31',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('cec5735d-51f1-361b-ac1c-4ab643e9a1f4',NULL,'Tunde Ogunleye','tunde.ogunleye.54@sweettooth.com','EMP-PHC002-0054','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,5,NULL,'+234-877-833-3860','421 Della Oval Suite 539, Port Harcourt, Rivers State, Nigeria','2001-08-28','male','Nigerian','Amina Okafor','+234-857-431-2372','2023-10-05 23:00:00','active',NULL,NULL,'flexible',149687.44,NULL,'TIN-66893311','5705478527',NULL,NULL,'2025-09-23',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('cf5c519b-17e8-375d-a424-c64b53b7a17f',NULL,'Emeka Williams','emeka.williams.85@sweettooth.com','EMP-ABJ004-0085','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,5,NULL,'+234-899-777-5669','81070 Harley Estates Suite 428, Abuja, FCT State, Nigeria','1995-01-09','male','Nigerian','Kemi Eze','+234-842-657-1748','2025-01-10 23:00:00','active',NULL,NULL,'morning',82242.04,NULL,'TIN-35717162','5539543006',NULL,NULL,'2025-08-09',4.4,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('d11ec69d-355e-3292-9fab-fbbbddfec7cd',NULL,'Ngozi Bello','ngozi.bello.57@sweettooth.com','EMP-PHC002-0057','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,7,NULL,'+234-896-886-7604','98012 Gleichner Shore, Port Harcourt, Rivers State, Nigeria','1985-09-17','female','Nigerian','Kunle Chukwu','+234-873-366-4507','2023-12-05 23:00:00','active',NULL,NULL,'morning',89536.52,NULL,'TIN-39700530','5470155161',NULL,NULL,'2025-11-20',3.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('d5773999-b7ba-3690-8c1f-5b272ac1819e',NULL,'Ngozi Adebayo','ngozi.adebayo.55@sweettooth.com','EMP-PHC002-0055','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,5,NULL,'+234-874-527-6565','2883 Jada Route, Port Harcourt, Rivers State, Nigeria','1987-03-23','female','Nigerian','Oluwaseun Eze','+234-905-817-7258','2024-02-08 23:00:00','active',NULL,NULL,'flexible',165297.23,NULL,'TIN-71480993','0429741153','Lactose',NULL,'2026-01-29',3.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('d9240843-ecf7-34ec-842f-9de2107884bd',NULL,'Ibrahim Nwankwo','ibrahim.nwankwo.49@sweettooth.com','EMP-PHC002-0049','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,4,NULL,'+234-908-652-7426','3297 Wehner Squares, Port Harcourt, Rivers State, Nigeria','1990-02-12','male','Nigerian','Chioma Williams','+234-898-582-2011','2023-03-24 23:00:00','active',NULL,NULL,'flexible',295934.61,NULL,'TIN-61345739','5563459779',NULL,NULL,'2025-11-25',4.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('d9539d9f-f81b-36ea-9e98-fb6b18c697d1',NULL,'Tunde Adebayo','tunde.adebayo.20@sweettooth.com','EMP-ENU005-0020','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,4,NULL,'+234-889-147-6594','8686 Erik Parks, Enugu, Enugu State, Nigeria','2002-08-14','male','Nigerian','Chioma Okafor','+234-840-598-1898','2024-01-28 23:00:00','active',NULL,NULL,'flexible',211565.94,NULL,'TIN-15070141','9487859211',NULL,NULL,'2025-09-03',4.6,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('dd7e65c1-c178-3565-99f2-70d2ed250373',NULL,'Ngozi Mohammed','ngozi.mohammed.25@sweettooth.com','EMP-ENU005-0025','019c1e63-b32d-72bd-847a-c1c17e7b8674',1,NULL,1,NULL,'+234-853-645-6344','6941 Schumm Bridge Apt. 811, Enugu, Enugu State, Nigeria','2001-09-18','female','Nigerian','Oluwaseun Adebayo','+234-898-942-7996','2024-08-12 23:00:00','active',NULL,NULL,'morning',99853.20,NULL,'TIN-45359246','4282753269',NULL,NULL,'2025-12-02',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('de6a6d49-98d3-3989-a192-ec3a5e8c8a41',NULL,'Nneka Eze','nneka.eze.53@sweettooth.com','EMP-PHC002-0053','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,5,NULL,'+234-879-849-6363','864 Champlin Island, Port Harcourt, Rivers State, Nigeria','1999-03-31','female','Nigerian','Kunle Nwankwo','+234-852-832-1333','2024-09-13 23:00:00','active',NULL,NULL,'morning',135886.10,NULL,'TIN-96365237','1740337036',NULL,NULL,'2025-10-05',3.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('decf4493-1c81-38ec-86cd-81d3ca9ea94f',NULL,'Kunle Chukwu','kunle.chukwu.86@sweettooth.com','EMP-ABJ004-0086','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,5,NULL,'+234-888-666-4160','63989 Collins Route Apt. 850, Abuja, FCT State, Nigeria','2000-11-15','male','Nigerian','Fatima Eze','+234-832-859-8679','2024-12-25 23:00:00','active',NULL,NULL,'rotating',135373.16,NULL,'TIN-77913022','4170816403',NULL,NULL,'2025-09-13',4.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('e0df346b-3538-36ca-807b-698db170bad0',NULL,'Amina Ogunleye','amina.ogunleye.47@sweettooth.com','EMP-PHC002-0047','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,2,NULL,'+234-881-105-6523','7206 Kay Forges, Port Harcourt, Rivers State, Nigeria','1991-06-23','female','Nigerian','Obinna Ogunleye','+234-909-664-3458','2024-11-26 23:00:00','active',NULL,NULL,'afternoon',236966.85,NULL,'TIN-69122263','5425819307',NULL,NULL,'2025-11-06',3.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('e3c6337f-4c3e-3c47-9a20-a57e32fe0c8a',NULL,'Ngozi Adebayo','ngozi.adebayo.17@sweettooth.com','EMP-PHC002-0017','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,4,NULL,'+234-826-959-1435','9835 Conor Crossroad, Port Harcourt, Rivers State, Nigeria','1987-06-13','female','Nigerian','Emeka Okafor','+234-847-760-4611','2024-04-12 23:00:00','active',NULL,NULL,'afternoon',292249.95,NULL,'TIN-58089239','8048117729',NULL,NULL,'2025-11-14',4.8,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('e99cba90-91a4-3e94-ac64-279fa397d5eb',NULL,'Hauwa Aliyu','hauwa.aliyu.39@sweettooth.com','EMP-CAL001-0039','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,5,NULL,'+234-802-612-1716','7586 Batz Shoal, Calabar, Cross River State, Nigeria','2001-06-12','female','Nigerian','Oluwaseun Okafor','+234-803-707-3485','2025-03-05 23:00:00','active',NULL,NULL,'rotating',330078.21,NULL,'TIN-43546803','4665580861',NULL,NULL,'2025-12-29',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('ec9c3044-b335-3f6f-abc8-126ff94c9062',NULL,'Chukwuemeka Adebayo','chukwuemeka.adebayo.19@sweettooth.com','EMP-ABJ004-0019','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,4,NULL,'+234-880-545-7255','9938 Shayne Lakes, Abuja, FCT State, Nigeria','1987-01-19','male','Nigerian','Folake Okafor','+234-823-766-3395','2023-04-29 23:00:00','active',NULL,NULL,'afternoon',314691.29,NULL,'TIN-52905916','5108763192',NULL,NULL,'2025-12-07',4.2,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('ee9d5f19-bde9-35bc-8868-f35492db7f2d',NULL,'Kunle Bello','kunle.bello.71@sweettooth.com','EMP-LAG003-0071','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,5,NULL,'+234-903-547-2146','1039 Dicki Groves Suite 719, Lagos, Lagos State, Nigeria','1995-07-14','male','Nigerian','Ngozi Mohammed','+234-802-361-5347','2024-08-03 23:00:00','active',NULL,NULL,'flexible',244718.82,NULL,'TIN-86753950','0776953795',NULL,NULL,'2025-10-07',4.3,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('ef8a91de-2d9d-3bd1-b7ac-6b97effba98d',NULL,'Oluwaseun Adebayo','oluwaseun.adebayo.46@sweettooth.com','EMP-PHC002-0046','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,2,NULL,'+234-892-451-4789','55814 Bradtke Forge Suite 946, Port Harcourt, Rivers State, Nigeria','1985-04-25','male','Nigerian','Fatima Aliyu','+234-858-189-7092','2023-08-20 23:00:00','active',NULL,NULL,'afternoon',131004.51,NULL,'TIN-78034146','5532890209',NULL,NULL,'2025-12-31',4.7,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('f0eb9223-685d-3512-911d-1e63c001de9c',NULL,'Amina Okafor','amina.okafor.18@sweettooth.com','EMP-LAG003-0018','019c1e63-b300-7276-abd5-9a31664cea89',1,NULL,4,NULL,'+234-897-923-9726','4024 Irwin Forges, Lagos, Lagos State, Nigeria','1981-05-14','female','Nigerian','Emeka Mohammed','+234-898-421-1882','2025-02-06 23:00:00','active',NULL,NULL,'rotating',347198.11,NULL,'TIN-62482329','0560944745',NULL,NULL,'2026-01-07',5.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('f15467cd-28a3-3dcc-90a7-7962f96ca108',NULL,'Tunde Mohammed','tunde.mohammed.4@sweettooth.com','EMP-ABJ004-0004','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,1,NULL,'+234-851-943-2888','217 Art Landing Suite 882, Abuja, FCT State, Nigeria','1987-03-24','male','Nigerian','Chioma Adebayo','+234-845-845-9168','2025-01-19 23:00:00','active',NULL,NULL,'flexible',268250.77,NULL,'TIN-50783337','0919873153',NULL,NULL,'2025-08-16',4.5,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('f496d51f-b171-395f-ac4a-8896e8615229',NULL,'Amina Adebayo','amina.adebayo.27@sweettooth.com','EMP-CAL001-0027','019c1e63-b2e7-70f2-81ab-c6347efd07f0',1,NULL,1,NULL,'+234-851-576-2237','96286 Brekke Extensions Suite 473, Calabar, Cross River State, Nigeria','2000-09-15','female','Nigerian','Kunle Okafor','+234-824-205-7741','2025-08-18 23:00:00','active',NULL,NULL,'afternoon',197302.47,NULL,'TIN-31590807','4284678705',NULL,NULL,'2025-12-20',4.1,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('f64488f3-4461-3522-8c8b-da481a1a9c2c',NULL,'Chigozie Adebayo','chigozie.adebayo.74@sweettooth.com','EMP-ABJ004-0074','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,1,NULL,'+234-822-193-6047','72013 Syble Shoal Apt. 223, Abuja, FCT State, Nigeria','2000-07-04','male','Nigerian','Folake Williams','+234-863-780-4147','2024-04-03 23:00:00','active',NULL,NULL,'rotating',138179.00,NULL,'TIN-20411718','5080403888',NULL,NULL,'2025-12-19',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('f77ac672-fb6d-3563-9d61-b55160b4f126',NULL,'Chigozie Adebayo','chigozie.adebayo.87@sweettooth.com','EMP-ABJ004-0087','019c1e63-b321-71ba-a153-c8fc9e31bcab',1,NULL,5,NULL,'+234-892-443-8184','764 Keshaun Shoal Suite 295, Abuja, FCT State, Nigeria','1991-01-03','male','Nigerian','Amina Bello','+234-837-498-4994','2023-05-18 23:00:00','active',NULL,NULL,'afternoon',270816.35,NULL,'TIN-54430869','2738129842',NULL,NULL,'2025-10-13',4.0,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36'),
('f92e3e38-6888-3efc-87e3-9d02bf7ddccf',NULL,'Ngozi Mohammed','ngozi.mohammed.2@sweettooth.com','EMP-PHC002-0002','019c1e63-b2f6-729f-bd59-bbe4340497fe',1,NULL,1,NULL,'+234-804-473-7645','74160 Turcotte Inlet, Port Harcourt, Rivers State, Nigeria','1998-12-29','female','Nigerian','Emeka Mohammed','+234-820-888-6483','2025-01-16 23:00:00','active',NULL,NULL,'flexible',206586.86,NULL,'TIN-67905758','2568534303',NULL,NULL,'2025-11-07',3.9,'employee',NULL,'$2y$12$GobivbzrL.YON46y.YbuuODRQ3T4u/JdCcOkHzAibODMwfKuUiSti',NULL,NULL,NULL,'2026-02-02 12:46:36',NULL,'2026-02-02 12:46:36','2026-02-02 12:46:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `withholding_tax_receipts`
--

DROP TABLE IF EXISTS `withholding_tax_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `withholding_tax_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `withheld_amount` decimal(15,2) NOT NULL,
  `net_amount` decimal(15,2) NOT NULL,
  `receipt_date` date NOT NULL,
  `certificate_number` varchar(255) DEFAULT NULL,
  `gl_posting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `gl_entry_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withholding_tax_receipts_sale_id_foreign` (`sale_id`),
  KEY `withholding_tax_receipts_gl_entry_id_foreign` (`gl_entry_id`),
  CONSTRAINT `withholding_tax_receipts_gl_entry_id_foreign` FOREIGN KEY (`gl_entry_id`) REFERENCES `gl_entries` (`id`),
  CONSTRAINT `withholding_tax_receipts_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withholding_tax_receipts`
--

LOCK TABLES `withholding_tax_receipts` WRITE;
/*!40000 ALTER TABLE `withholding_tax_receipts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `withholding_tax_receipts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `workflow_audit_logs`
--

DROP TABLE IF EXISTS `workflow_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `workflow_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` char(36) NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `branch_id` char(36) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `event_type` varchar(255) NOT NULL,
  `workflow_step` varchar(255) NOT NULL,
  `from_state` varchar(255) DEFAULT NULL,
  `to_state` varchar(255) DEFAULT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `notes` text DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_audit_employee_shift_idx` (`employee_id`,`shift_id`),
  KEY `workflow_audit_event_time_idx` (`event_type`,`created_at`),
  KEY `workflow_audit_branch_dept_idx` (`branch_id`,`department_id`,`created_at`),
  KEY `workflow_audit_logs_shift_id_foreign` (`shift_id`),
  KEY `workflow_audit_logs_department_id_foreign` (`department_id`),
  CONSTRAINT `workflow_audit_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_audit_logs_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `workflow_audit_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_audit_logs_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_audit_logs`
--

LOCK TABLES `workflow_audit_logs` WRITE;
/*!40000 ALTER TABLE `workflow_audit_logs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `workflow_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-02-02 20:54:22
