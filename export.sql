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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_periods`
--

LOCK TABLES `accounting_periods` WRITE;
/*!40000 ALTER TABLE `accounting_periods` DISABLE KEYS */;
set autocommit=0;
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
(1,'first quadrant','2026-01-01','2026-04-30','2026-04-28','active',NULL,'2026-01-03 05:41:01','2026-01-03 06:22:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_audit_requests`
--

LOCK TABLES `approval_audit_requests` WRITE;
/*!40000 ALTER TABLE `approval_audit_requests` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `approval_audit_requests` VALUES
(1,'019b6d6a-191b-7068-817b-94e02d09e859','23b13cd9-6f31-3745-8061-d5f0dd77f130','App\\Models\\User','019b6d6a-18e7-70e0-9aa0-5d56ffd08444','App\\Models\\User','product:create','This action requires approval. It will be reviewed and processed by a super administrator.','{\"id\":null,\"name\":\"testing\",\"sku\":\"GB-TES-1707\",\"product_type_id\":9,\"category_id\":null,\"description\":\"\",\"price\":\"20\",\"cost\":null,\"shelf_life_days\":77,\"uom_id\":17,\"is_active\":true,\"is_available\":true,\"image_url\":\"\",\"allergens\":[],\"tags\":[]}','approved',NULL,'2026-01-01 16:24:33',NULL,'2026-01-01 15:11:42','2026-01-01 15:24:33');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `audit_logs` VALUES
(1,'019b6d6a-191b-7068-817b-94e02d09e859','App\\Models\\User','019b6d6a-18e7-70e0-9aa0-5d56ffd08444','App\\Models\\Employee','01714abf-7822-351f-be69-4581a3545274',NULL,'sync_roles','Updated roles for Obinna Okafor','[46]','[0,1]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-30 05:40:10',NULL,'{\"relationship\":\"roles\",\"attached\":{\"1\":1},\"detached\":[],\"updated\":[0],\"sync_data\":[0,1],\"previous_data\":[46],\"causer\":\"App\\\\Models\\\\User\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-30 04:40:10','2025-12-30 04:40:10'),
(2,'019b6d6a-191b-7068-817b-94e02d09e859','App\\Models\\User','019b6d6a-18e7-70e0-9aa0-5d56ffd08444','App\\Models\\Employee','01714abf-7822-351f-be69-4581a3545274',NULL,'employee:roles_changed','Roles changed for \'Obinna Okafor\' - Added: Sales Manager','{\"id\":\"01714abf-7822-351f-be69-4581a3545274\",\"last_accessed_branch_id\":null,\"name\":\"Obinna Okafor\",\"email\":\"obinna.okafor.39@sweettooth.com\",\"employee_number\":\"EMP-CAL001-0039\",\"branch_id\":\"019b6d6a-191b-7068-817b-94e02d09e859\",\"is_active\":true,\"employee_id\":null,\"department_id\":5,\"manager_id\":null,\"phone\":\"+234-818-972-5454\",\"address\":\"499 Treutel Flats, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"1983-04-25\",\"gender\":\"male\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Hauwa Eze\",\"emergency_contact_phone\":\"+234-884-506-9561\",\"hire_date\":\"2023-01-31T23:00:00.000000Z\",\"employment_status\":\"active\",\"termination_date\":null,\"probation_end_date\":null,\"shift_preference\":\"afternoon\",\"salary\":\"173117.72\",\"hourly_rate\":null,\"tax_id\":\"TIN-53791139\",\"bank_account\":\"5923873281\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-08-05\",\"performance_rating\":\"3.5\",\"user_type\":\"employee\",\"deleted_at\":null,\"password\":\"$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C\",\"two_factor_secret\":null,\"two_factor_recovery_codes\":null,\"two_factor_confirmed_at\":null,\"email_verified_at\":\"2025-12-30T04:00:48.000000Z\",\"remember_token\":null,\"created_at\":\"2025-12-30T04:00:48.000000Z\",\"updated_at\":\"2025-12-30T04:00:48.000000Z\"}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-30 05:40:10',NULL,'{\"old_roles\":[\"Corner Store Staff\"],\"new_roles\":[\"Sales Manager\",\"Corner Store Staff\"],\"added\":[\"Sales Manager\"],\"removed\":[],\"reason\":\"\",\"causer\":\"App\\\\Models\\\\User\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-30 04:40:10','2025-12-30 04:40:10'),
(3,'019b6d6a-191b-7068-817b-94e02d09e859','App\\Models\\User','019b6d6a-18e7-70e0-9aa0-5d56ffd08444','App\\Models\\Employee','23b13cd9-6f31-3745-8061-d5f0dd77f130',NULL,'sync_roles','Updated roles for Ngozi Chukwu','[39]','[0,1]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2026-01-01 16:06:22',NULL,'{\"relationship\":\"roles\",\"attached\":{\"1\":1},\"detached\":[],\"updated\":[0],\"sync_data\":[0,1],\"previous_data\":[39],\"causer\":\"App\\\\Models\\\\User\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2026-01-01 15:06:22','2026-01-01 15:06:22'),
(4,'019b6d6a-191b-7068-817b-94e02d09e859','App\\Models\\User','019b6d6a-18e7-70e0-9aa0-5d56ffd08444','App\\Models\\Employee','23b13cd9-6f31-3745-8061-d5f0dd77f130',NULL,'employee:roles_changed','Roles changed for \'Ngozi Chukwu\' - Added: Gelato Production Staff','{\"id\":\"23b13cd9-6f31-3745-8061-d5f0dd77f130\",\"last_accessed_branch_id\":\"019b6d6a-191b-7068-817b-94e02d09e859\",\"name\":\"Ngozi Chukwu\",\"email\":\"ngozi.chukwu.30@sweettooth.com\",\"employee_number\":\"EMP-CAL001-0030\",\"branch_id\":\"019b6d6a-191b-7068-817b-94e02d09e859\",\"is_active\":true,\"employee_id\":null,\"department_id\":2,\"manager_id\":null,\"phone\":\"+234-812-837-7059\",\"address\":\"58497 Enola Inlet Apt. 590, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"1998-10-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Oluwaseun Okafor\",\"emergency_contact_phone\":\"+234-808-160-7306\",\"hire_date\":\"2023-02-28T23:00:00.000000Z\",\"employment_status\":\"active\",\"termination_date\":null,\"probation_end_date\":null,\"shift_preference\":\"rotating\",\"salary\":\"86994.00\",\"hourly_rate\":null,\"tax_id\":\"TIN-97004199\",\"bank_account\":\"7028456227\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-08-12\",\"performance_rating\":\"4.9\",\"user_type\":\"employee\",\"deleted_at\":null,\"password\":\"$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C\",\"two_factor_secret\":null,\"two_factor_recovery_codes\":null,\"two_factor_confirmed_at\":null,\"email_verified_at\":\"2025-12-30T04:00:48.000000Z\",\"remember_token\":null,\"created_at\":\"2025-12-30T04:00:48.000000Z\",\"updated_at\":\"2026-01-01T14:37:08.000000Z\"}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2026-01-01 16:06:22',NULL,'{\"old_roles\":[\"Head of Gelato\"],\"new_roles\":[\"Head of Gelato\",\"Gelato Production Staff\"],\"added\":{\"1\":\"Gelato Production Staff\"},\"removed\":[],\"reason\":\"\",\"causer\":\"App\\\\Models\\\\User\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2026-01-01 15:06:22','2026-01-01 15:06:22'),
(5,'019b6d6a-191b-7068-817b-94e02d09e859','App\\Models\\User','019b6d6a-18e7-70e0-9aa0-5d56ffd08444','App\\Models\\Product','019b7a29-1efb-73f5-b8a5-0a841c16f377',NULL,'approve_product','Approved by Super Admin','{\"name\":\"testing\",\"sku\":\"GB-TES-1707\",\"product_type_id\":9,\"category_id\":null,\"description\":\"\",\"price\":\"20.00\",\"cost\":null,\"shelf_life_days\":77,\"uom_id\":17,\"is_active\":true,\"is_available\":true,\"image_url\":\"\",\"allergens\":[],\"tags\":[],\"id\":\"019b7a29-1efb-73f5-b8a5-0a841c16f377\",\"updated_at\":\"2026-01-01T15:24:33.000000Z\",\"created_at\":\"2026-01-01T15:24:33.000000Z\"}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2026-01-01 16:24:33',NULL,'{\"causer\":\"App\\\\Models\\\\User\",\"auditable\":\"App\\\\Models\\\\Product\"}','2026-01-01 15:24:33','2026-01-01 15:24:33');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_accounts`
--

LOCK TABLES `bank_accounts` WRITE;
/*!40000 ALTER TABLE `bank_accounts` DISABLE KEYS */;
set autocommit=0;
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
('019b6d6a-191b-7068-817b-94e02d09e859','SweetTooth Calabar','CAL-001','12 Marian Road, Calabar Municipal','+234-809-012-3456','calabar@sweettooth.com','Calabar flagship store with full production and sales',NULL,'Nigeria','Cross River','Calabar','540001','Africa/Lagos',1,0,NULL,'2025-12-30 04:00:28','2025-12-30 04:00:28',NULL),
('019b6d6a-1922-7079-99a9-842d7c4a1fc3','SweetTooth Port Harcourt','PHC-002','78 Trans Amadi Industrial Layout','+234-803-456-7890','portharcourt@sweettooth.com','Port Harcourt main branch',NULL,'Nigeria','Rivers','Port Harcourt','500001','Africa/Lagos',1,0,NULL,'2025-12-30 04:00:28','2025-12-30 04:00:28',NULL),
('019b6d6a-192d-705f-8b98-4e27aaf972b5','SweetTooth Lagos','LAG-003','45 Admiralty Way, Lekki Phase 1','+234-801-234-5678','lagos@sweettooth.com','Lagos head office and production center',NULL,'Nigeria','Lagos','Lagos','101001','Africa/Lagos',1,0,NULL,'2025-12-30 04:00:28','2025-12-30 04:00:28',NULL),
('019b6d6a-1944-725f-9136-b5c40cd21ca0','SweetTooth Abuja','ABJ-004','23 Gimbiya Street, Area 11, Garki','+234-802-345-6789','abuja@sweettooth.com','Abuja branch with gelato specialty',NULL,'Nigeria','FCT','Abuja','900001','Africa/Lagos',1,0,NULL,'2025-12-30 04:00:28','2025-12-30 04:00:28',NULL),
('019b6d6a-194f-71bf-a740-9f45370d3348','SweetTooth Enugu','ENU-005','34 Ogui Road, New Haven','+234-806-789-0123','enugu@sweettooth.com','Enugu branch serving South-East region',NULL,'Nigeria','Enugu','Enugu','400001','Africa/Lagos',1,0,NULL,'2025-12-30 04:00:28','2025-12-30 04:00:28',NULL);
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
('laravel_cache_dept_categories_v3_916eeb6aa09ab292162cdbdea8a536ad','O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":12:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:29:\"App\\Models\\DepartmentCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:21:\"department_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";s:36:\"019b6d6a-614b-72de-928e-759f9055d7b2\";s:4:\"name\";s:5:\"Sales\";s:11:\"description\";s:99:\"Departments focused on selling products and services, customer acquisition, and revenue generation.\";s:10:\"created_at\";s:19:\"2025-12-30 05:00:46\";s:10:\"updated_at\";s:19:\"2025-12-30 05:00:46\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";s:36:\"019b6d6a-614b-72de-928e-759f9055d7b2\";s:4:\"name\";s:5:\"Sales\";s:11:\"description\";s:99:\"Departments focused on selling products and services, customer acquisition, and revenue generation.\";s:10:\"created_at\";s:19:\"2025-12-30 05:00:46\";s:10:\"updated_at\";s:19:\"2025-12-30 05:00:46\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:1;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:8:\"fillable\";a:2:{i:0;s:4:\"name\";i:1;s:11:\"description\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:29:\"App\\Models\\DepartmentCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:21:\"department_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";s:36:\"019b6d6a-6157-706e-8bf4-6295a097695c\";s:4:\"name\";s:10:\"Production\";s:11:\"description\";s:85:\"Departments responsible for manufacturing, production processes, and quality control.\";s:10:\"created_at\";s:19:\"2025-12-30 05:00:46\";s:10:\"updated_at\";s:19:\"2025-12-30 05:00:46\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";s:36:\"019b6d6a-6157-706e-8bf4-6295a097695c\";s:4:\"name\";s:10:\"Production\";s:11:\"description\";s:85:\"Departments responsible for manufacturing, production processes, and quality control.\";s:10:\"created_at\";s:19:\"2025-12-30 05:00:46\";s:10:\"updated_at\";s:19:\"2025-12-30 05:00:46\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:1;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:8:\"fillable\";a:2:{i:0;s:4:\"name\";i:1;s:11:\"description\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:29:\"App\\Models\\DepartmentCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:21:\"department_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";s:36:\"019b6d6a-6162-7399-a43a-d5e02919bf8b\";s:4:\"name\";s:7:\"Support\";s:11:\"description\";s:83:\"Departments providing assistance, customer service, and technical support services.\";s:10:\"created_at\";s:19:\"2025-12-30 05:00:46\";s:10:\"updated_at\";s:19:\"2025-12-30 05:00:46\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";s:36:\"019b6d6a-6162-7399-a43a-d5e02919bf8b\";s:4:\"name\";s:7:\"Support\";s:11:\"description\";s:83:\"Departments providing assistance, customer service, and technical support services.\";s:10:\"created_at\";s:19:\"2025-12-30 05:00:46\";s:10:\"updated_at\";s:19:\"2025-12-30 05:00:46\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:1;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:8:\"fillable\";a:2:{i:0;s:4:\"name\";i:1;s:11:\"description\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:37:\"branch-dashboard/departments/category\";s:8:\"\0*\0query\";a:1:{s:4:\"b_id\";s:36:\"019b6d6a-191b-7068-817b-94e02d09e859\";}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:37:\"branch-dashboard/departments/category\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:3;s:11:\"\0*\0lastPage\";i:1;}',1767418821);
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
('019b6d6a-614b-72de-928e-759f9055d7b2','Sales','Departments focused on selling products and services, customer acquisition, and revenue generation.','2025-12-30 04:00:46','2025-12-30 04:00:46'),
('019b6d6a-6157-706e-8bf4-6295a097695c','Production','Departments responsible for manufacturing, production processes, and quality control.','2025-12-30 04:00:46','2025-12-30 04:00:46'),
('019b6d6a-6162-7399-a43a-d5e02919bf8b','Support','Departments providing assistance, customer service, and technical support services.','2025-12-30 04:00:46','2025-12-30 04:00:46');
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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_pages`
--

LOCK TABLES `department_pages` WRITE;
/*!40000 ALTER TABLE `department_pages` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `department_pages` VALUES
(1,1,'Products','products','branch-dashboard.production.products','cube',1,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(2,1,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(3,1,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(4,1,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(5,1,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(6,1,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(7,1,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(8,1,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(9,1,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(10,1,'Kitchen Module','kitchen-module','branch-dashboard.production.module.index','home',10,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(11,1,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',11,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(12,1,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',12,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(13,1,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',13,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(14,2,'Products','products','branch-dashboard.production.products','cube',1,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(15,2,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(16,2,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(17,2,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(18,2,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(19,2,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(20,2,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(21,2,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(22,2,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(23,2,'Kitchen Module','kitchen-module','branch-dashboard.production.module.index','home',10,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(24,2,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',11,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(25,2,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',12,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(26,2,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',13,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(27,3,'Products','products','branch-dashboard.production.products','cube',1,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(28,3,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(29,3,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(30,3,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(31,3,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(32,3,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2025-12-30 04:00:47','2025-12-30 04:01:08'),
(33,3,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(34,3,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(35,3,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(36,3,'Kitchen Module','kitchen-module','branch-dashboard.production.module.index','home',10,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(37,3,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',11,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(38,3,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',12,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(39,3,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',13,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(40,4,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(41,4,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(42,4,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(43,4,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(44,5,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(45,5,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(46,5,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(47,5,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
(48,6,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
(49,6,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
(50,6,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
(51,6,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
(52,1,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(53,1,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(54,1,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(55,2,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(56,2,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(57,2,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(58,3,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(59,3,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2025-12-30 04:01:08','2025-12-30 04:01:08'),
(60,3,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2025-12-30 04:01:08','2025-12-30 04:01:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_product`
--

LOCK TABLES `department_product` WRITE;
/*!40000 ALTER TABLE `department_product` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `department_product` VALUES
(1,4,'019b6d6a-9e50-72f1-bfc5-7b56148bf06f',1,NULL,0,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(2,4,'019b6d6a-9e57-7229-9ece-da56055b66c1',1,NULL,1,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(3,4,'019b6d6a-9e62-7212-ac6d-e46fef7cdb23',1,NULL,2,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(4,4,'019b6d6a-9e6d-73dd-bc4e-aaba43ea872b',1,NULL,3,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(5,4,'019b6d6a-9e86-7202-b308-776fdc77f9b5',1,NULL,4,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(6,4,'019b6d6a-9e8f-7046-8a79-c759706576a2',1,NULL,5,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(7,5,'019b6d6a-9e50-72f1-bfc5-7b56148bf06f',1,NULL,0,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(8,5,'019b6d6a-9e57-7229-9ece-da56055b66c1',1,NULL,1,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(9,5,'019b6d6a-9e62-7212-ac6d-e46fef7cdb23',1,NULL,2,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(10,5,'019b6d6a-9e6d-73dd-bc4e-aaba43ea872b',1,NULL,3,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(11,5,'019b6d6a-9e79-73e4-8e8f-7d30de810dc2',1,NULL,4,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(12,6,'019b6d6a-9ea5-7161-be08-9a7833a67d45',1,NULL,0,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(13,6,'019b6d6a-9eb0-70d6-a8c0-54d004970cd8',1,NULL,1,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(14,6,'019b6d6a-9ebb-739d-86e0-1a1899bac33a',1,NULL,2,'2025-12-30 04:01:02','2025-12-30 04:01:02'),
(15,6,'019b6d6a-9ec7-725b-a95a-3d5d63660f9f',1,NULL,3,'2025-12-30 04:01:03','2025-12-30 04:01:03'),
(16,6,'019b6d6a-9ed2-7106-9e57-456f38716485',1,NULL,4,'2025-12-30 04:01:03','2025-12-30 04:01:03'),
(17,6,'019b6d6a-9edd-735f-b174-861b12525143',1,NULL,5,'2025-12-30 04:01:03','2025-12-30 04:01:03');
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
  `enable_table_management` tinyint(1) NOT NULL DEFAULT 0,
  `table_management_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_management_settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_name_unique` (`name`),
  UNIQUE KEY `departments_branch_id_slug_unique` (`branch_id`,`slug`),
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
set autocommit=0;
INSERT INTO `departments` VALUES
(1,NULL,'019b6d6a-6157-706e-8bf4-6295a097695c','Kitchen','kitchen','Prepares food for Till, Confectionaries, Corner Store',0,NULL,'2025-12-30 04:00:46','2025-12-30 04:00:46'),
(2,NULL,'019b6d6a-6157-706e-8bf4-6295a097695c','Gelato Production','gelato-production','Makes gelato/ice cream',0,NULL,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(3,NULL,'019b6d6a-6157-706e-8bf4-6295a097695c','Confectionaries Production','confectionaries-production','Makes confectionery items',0,NULL,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(4,NULL,'019b6d6a-614b-72de-928e-759f9055d7b2','Till','till','Sells ready-made snacks',0,NULL,'2025-12-30 04:00:47','2026-01-01 14:35:36'),
(5,NULL,'019b6d6a-614b-72de-928e-759f9055d7b2','Corner Store','corner-store','On-demand food sales',0,NULL,'2025-12-30 04:00:47','2025-12-30 04:00:47'),
(6,NULL,'019b6d6a-614b-72de-928e-759f9055d7b2','Confectionaries Sales','confectionaries-sales','Sells confectionery items',0,NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
(7,NULL,'019b6d6a-6162-7399-a43a-d5e02919bf8b','Inventory/Store','inventorystore','Manages all stock',0,NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
(8,NULL,'019b6d6a-6162-7399-a43a-d5e02919bf8b','HR','hr','Human resources (corporate level)',0,NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gl_accounts`
--

LOCK TABLES `gl_accounts` WRITE;
/*!40000 ALTER TABLE `gl_accounts` DISABLE KEYS */;
set autocommit=0;
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
  `posted_by_id` char(36) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `reversed_by_id` char(36) DEFAULT NULL,
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
  CONSTRAINT `gl_entries_accounting_period_id_foreign` FOREIGN KEY (`accounting_period_id`) REFERENCES `accounting_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gl_entries_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `gl_entries_gl_account_id_foreign` FOREIGN KEY (`gl_account_id`) REFERENCES `gl_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gl_entries`
--

LOCK TABLES `gl_entries` WRITE;
/*!40000 ALTER TABLE `gl_entries` DISABLE KEYS */;
set autocommit=0;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_business_configurations`
--

LOCK TABLES `global_business_configurations` WRITE;
/*!40000 ALTER TABLE `global_business_configurations` DISABLE KEYS */;
set autocommit=0;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_currency_localizations`
--

LOCK TABLES `global_currency_localizations` WRITE;
/*!40000 ALTER TABLE `global_currency_localizations` DISABLE KEYS */;
set autocommit=0;
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
(1,'019b6d6a-191b-7068-817b-94e02d09e859','Sugar - White Granulated','CAL-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(2,'019b6d6a-191b-7068-817b-94e02d09e859','Flour - All Purpose','CAL-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(3,'019b6d6a-191b-7068-817b-94e02d09e859','Cocoa Powder - Premium Dark','CAL-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(4,'019b6d6a-191b-7068-817b-94e02d09e859','Butter - Salted','CAL-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(5,'019b6d6a-191b-7068-817b-94e02d09e859','Eggs - Large Grade A','CAL-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(6,'019b6d6a-191b-7068-817b-94e02d09e859','Vanilla Extract - Pure','CAL-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(7,'019b6d6a-191b-7068-817b-94e02d09e859','Chocolate Chips - Dark','CAL-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(8,'019b6d6a-191b-7068-817b-94e02d09e859','Milk - Fresh Whole','CAL-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(9,'019b6d6a-191b-7068-817b-94e02d09e859','Cream - Heavy Whipping','CAL-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(10,'019b6d6a-191b-7068-817b-94e02d09e859','Yeast - Active Dry','CAL-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(11,'019b6d6a-191b-7068-817b-94e02d09e859','Vegetable Oil - Cooking','CAL-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(12,'019b6d6a-191b-7068-817b-94e02d09e859','Salt - Table Salt','CAL-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(13,'019b6d6a-191b-7068-817b-94e02d09e859','Cake Boxes - 10 inch','CAL-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(14,'019b6d6a-191b-7068-817b-94e02d09e859','Pastry Boxes - Small','CAL-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(15,'019b6d6a-191b-7068-817b-94e02d09e859','Paper Bags - Brown','CAL-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(16,'019b6d6a-191b-7068-817b-94e02d09e859','Plastic Food Containers','CAL-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(17,'019b6d6a-191b-7068-817b-94e02d09e859','Dishwashing Liquid - Industrial','CAL-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(18,'019b6d6a-191b-7068-817b-94e02d09e859','Paper Towels - Kitchen Roll','CAL-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(19,'019b6d6a-191b-7068-817b-94e02d09e859','Garbage Bags - Large','CAL-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(20,'019b6d6a-191b-7068-817b-94e02d09e859','Mixing Bowls - Stainless Steel','CAL-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-30 04:00:55','2025-12-30 04:00:55'),
(21,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Sugar - White Granulated','PHC-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(22,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Flour - All Purpose','PHC-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(23,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Cocoa Powder - Premium Dark','PHC-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(24,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Butter - Salted','PHC-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(25,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Eggs - Large Grade A','PHC-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(26,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Vanilla Extract - Pure','PHC-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(27,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Chocolate Chips - Dark','PHC-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(28,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Milk - Fresh Whole','PHC-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(29,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Cream - Heavy Whipping','PHC-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(30,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Yeast - Active Dry','PHC-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(31,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Vegetable Oil - Cooking','PHC-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(32,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Salt - Table Salt','PHC-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(33,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Cake Boxes - 10 inch','PHC-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(34,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Pastry Boxes - Small','PHC-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(35,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Paper Bags - Brown','PHC-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(36,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Plastic Food Containers','PHC-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(37,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Dishwashing Liquid - Industrial','PHC-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(38,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Paper Towels - Kitchen Roll','PHC-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(39,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Garbage Bags - Large','PHC-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(40,'019b6d6a-1922-7079-99a9-842d7c4a1fc3','Mixing Bowls - Stainless Steel','PHC-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(41,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Sugar - White Granulated','LAG-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(42,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Flour - All Purpose','LAG-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(43,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Cocoa Powder - Premium Dark','LAG-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(44,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Butter - Salted','LAG-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(45,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Eggs - Large Grade A','LAG-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(46,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Vanilla Extract - Pure','LAG-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(47,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Chocolate Chips - Dark','LAG-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(48,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Milk - Fresh Whole','LAG-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(49,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Cream - Heavy Whipping','LAG-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(50,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Yeast - Active Dry','LAG-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(51,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Vegetable Oil - Cooking','LAG-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(52,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Salt - Table Salt','LAG-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(53,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Cake Boxes - 10 inch','LAG-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(54,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Pastry Boxes - Small','LAG-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(55,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Paper Bags - Brown','LAG-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(56,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Plastic Food Containers','LAG-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(57,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Dishwashing Liquid - Industrial','LAG-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(58,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Paper Towels - Kitchen Roll','LAG-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(59,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Garbage Bags - Large','LAG-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(60,'019b6d6a-192d-705f-8b98-4e27aaf972b5','Mixing Bowls - Stainless Steel','LAG-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(61,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Sugar - White Granulated','ABJ-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(62,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Flour - All Purpose','ABJ-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(63,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Cocoa Powder - Premium Dark','ABJ-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(64,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Butter - Salted','ABJ-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(65,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Eggs - Large Grade A','ABJ-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(66,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Vanilla Extract - Pure','ABJ-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(67,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Chocolate Chips - Dark','ABJ-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(68,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Milk - Fresh Whole','ABJ-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(69,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Cream - Heavy Whipping','ABJ-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(70,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Yeast - Active Dry','ABJ-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(71,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Vegetable Oil - Cooking','ABJ-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(72,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Salt - Table Salt','ABJ-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(73,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Cake Boxes - 10 inch','ABJ-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(74,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Pastry Boxes - Small','ABJ-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(75,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Paper Bags - Brown','ABJ-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(76,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Plastic Food Containers','ABJ-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(77,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Dishwashing Liquid - Industrial','ABJ-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(78,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Paper Towels - Kitchen Roll','ABJ-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(79,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Garbage Bags - Large','ABJ-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(80,'019b6d6a-1944-725f-9136-b5c40cd21ca0','Mixing Bowls - Stainless Steel','ABJ-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(81,'019b6d6a-194f-71bf-a740-9f45370d3348','Sugar - White Granulated','ENU-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(82,'019b6d6a-194f-71bf-a740-9f45370d3348','Flour - All Purpose','ENU-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(83,'019b6d6a-194f-71bf-a740-9f45370d3348','Cocoa Powder - Premium Dark','ENU-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(84,'019b6d6a-194f-71bf-a740-9f45370d3348','Butter - Salted','ENU-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(85,'019b6d6a-194f-71bf-a740-9f45370d3348','Eggs - Large Grade A','ENU-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(86,'019b6d6a-194f-71bf-a740-9f45370d3348','Vanilla Extract - Pure','ENU-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(87,'019b6d6a-194f-71bf-a740-9f45370d3348','Chocolate Chips - Dark','ENU-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(88,'019b6d6a-194f-71bf-a740-9f45370d3348','Milk - Fresh Whole','ENU-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(89,'019b6d6a-194f-71bf-a740-9f45370d3348','Cream - Heavy Whipping','ENU-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(90,'019b6d6a-194f-71bf-a740-9f45370d3348','Yeast - Active Dry','ENU-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(91,'019b6d6a-194f-71bf-a740-9f45370d3348','Vegetable Oil - Cooking','ENU-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(92,'019b6d6a-194f-71bf-a740-9f45370d3348','Salt - Table Salt','ENU-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(93,'019b6d6a-194f-71bf-a740-9f45370d3348','Cake Boxes - 10 inch','ENU-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(94,'019b6d6a-194f-71bf-a740-9f45370d3348','Pastry Boxes - Small','ENU-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(95,'019b6d6a-194f-71bf-a740-9f45370d3348','Paper Bags - Brown','ENU-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(96,'019b6d6a-194f-71bf-a740-9f45370d3348','Plastic Food Containers','ENU-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(97,'019b6d6a-194f-71bf-a740-9f45370d3348','Dishwashing Liquid - Industrial','ENU-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(98,'019b6d6a-194f-71bf-a740-9f45370d3348','Paper Towels - Kitchen Roll','ENU-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(99,'019b6d6a-194f-71bf-a740-9f45370d3348','Garbage Bags - Large','ENU-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(100,'019b6d6a-194f-71bf-a740-9f45370d3348','Mixing Bowls - Stainless Steel','ENU-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-30 04:01:01','2025-12-30 04:01:01');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(1,'Annual Leave','ANNUAL','Yearly vacation leave for rest and recreation',21,1,0,14,7,1,1,'#3b82f6','2025-12-30 04:00:27','2025-12-30 04:00:27'),
(2,'Sick Leave','SICK','Medical leave for illness or injury',10,1,1,NULL,0,1,1,'#ef4444','2025-12-30 04:00:27','2025-12-30 04:00:27'),
(3,'Emergency Leave','EMERGENCY','Urgent personal or family emergencies',5,1,0,3,0,1,1,'#f59e0b','2025-12-30 04:00:27','2025-12-30 04:00:27'),
(4,'Maternity Leave','MATERNITY','Leave for childbirth and post-natal care',90,1,1,NULL,30,1,1,'#ec4899','2025-12-30 04:00:27','2025-12-30 04:00:27'),
(5,'Paternity Leave','PATERNITY','Leave for fathers following childbirth',7,1,1,NULL,7,1,1,'#6366f1','2025-12-30 04:00:27','2025-12-30 04:00:27'),
(6,'Bereavement Leave','BEREAVEMENT','Leave for death of a family member',3,1,0,3,0,1,1,'#6b7280','2025-12-30 04:00:27','2025-12-30 04:00:27'),
(7,'Study Leave','STUDY','Leave for educational purposes or examinations',5,1,1,5,14,0,1,'#8b5cf6','2025-12-30 04:00:27','2025-12-30 04:00:27'),
(8,'Unpaid Leave','UNPAID','Additional leave without pay',0,1,0,NULL,14,0,1,'#64748b','2025-12-30 04:00:27','2025-12-30 04:00:27');
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
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(180,'2026_01_02_131657_fix_role_permission_audit_logs_user_id_column',2);
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
(33,'user','01714abf-7822-351f-be69-4581a3545274'),
(46,'user','01714abf-7822-351f-be69-4581a3545274'),
(28,'user','019b6d6a-18e7-70e0-9aa0-5d56ffd08444'),
(53,'user','019b6d6a-3ae1-7073-9f6f-0db27de3c1df'),
(53,'user','019b6d6a-3b70-71f3-bb0a-30407024355d'),
(53,'user','019b6d6a-3b87-7088-ab47-cc99a111c482'),
(53,'user','019b6d6a-3b92-708f-9bb5-42a7892c7c9d'),
(53,'user','019b6d6a-3b9e-7234-b1cc-63aa6dcc2b8c'),
(53,'user','019b6d6a-3baa-706e-9d6b-03e332ab79c7'),
(53,'user','019b6d6a-3bbf-717d-84e6-fd266f0cb484'),
(53,'user','019b6d6a-3bca-72fc-b826-ae61cabb109a'),
(53,'user','019b6d6a-3bd5-71d9-b85e-ef830dab29cc'),
(53,'user','019b6d6a-3be0-7126-b610-2e2dec239b9a'),
(53,'user','019b6d6a-3bec-7386-a20a-8ad435e867b2'),
(53,'user','019b6d6a-3bf8-7181-8370-a7311d24b3e4'),
(53,'user','019b6d6a-3c03-73fa-b8b0-e9af98b8dde4'),
(53,'user','019b6d6a-3c0e-737f-abfc-deba0e247b16'),
(53,'user','019b6d6a-3c18-71c2-ad0b-ac8580d613da'),
(53,'user','019b6d6a-3c23-70b6-877f-9c40a62d3e95'),
(53,'user','019b6d6a-3c2f-70ab-a7a4-1afeff84a760'),
(53,'user','019b6d6a-3c46-7075-90cb-c55958b3c0c3'),
(53,'user','019b6d6a-3c50-70d7-aed0-723f241fb01a'),
(53,'user','019b6d6a-3c5c-73ff-ab34-7e2b86c837d2'),
(53,'user','019b6d6a-3c67-7214-b545-cc29fca3e869'),
(53,'user','019b6d6a-3c72-7235-8be4-f758f54ba077'),
(53,'user','019b6d6a-3c7c-71ec-814f-fa7683633e7b'),
(53,'user','019b6d6a-3c88-724d-9b1c-7118590d82da'),
(53,'user','019b6d6a-3c92-73f7-b0b2-47a989c684c8'),
(53,'user','019b6d6a-3c9d-7230-ab2d-59ab73f67468'),
(53,'user','019b6d6a-3ca8-7176-883b-4a98eb930dd8'),
(53,'user','019b6d6a-3cb3-7377-802d-b838b2feede1'),
(53,'user','019b6d6a-3cbf-7152-ac2d-c726b107530f'),
(53,'user','019b6d6a-3cca-7250-ac2f-86034809e8cb'),
(53,'user','019b6d6a-41c5-7264-91e5-54379c8c981b'),
(53,'user','019b6d6a-41de-72fe-84af-f0d274317d47'),
(53,'user','019b6d6a-41e9-73dc-817d-14465416360c'),
(53,'user','019b6d6a-41f4-7277-833b-d7130a12cd66'),
(53,'user','019b6d6a-41ff-7199-8653-0a0ac8446b41'),
(53,'user','019b6d6a-420e-72c8-ba6d-6989bfac9b9c'),
(53,'user','019b6d6a-4215-71c3-acd3-0b59a81a1ab9'),
(53,'user','019b6d6a-4220-700b-9202-cad01f109b6b'),
(53,'user','019b6d6a-422c-7040-9e73-b110f50991a2'),
(53,'user','019b6d6a-4236-7298-bde0-18c32c86ce13'),
(53,'user','019b6d6a-4242-73fc-9766-1b45deb1fb9f'),
(53,'user','019b6d6a-424d-73af-b3e9-956b068f3d9d'),
(53,'user','019b6d6a-4258-70a4-982b-db6b3abad934'),
(53,'user','019b6d6a-4263-7330-9d00-2227da28efd5'),
(53,'user','019b6d6a-426e-7030-b752-1af9a6a638b2'),
(53,'user','019b6d6a-4279-7040-a4a6-f981589352ca'),
(53,'user','019b6d6a-4284-7276-b4c3-70cd932dc5ce'),
(53,'user','019b6d6a-428f-700a-9508-d27179ff7f37'),
(53,'user','019b6d6a-429b-72e0-8e50-f31caa34d39b'),
(53,'user','019b6d6a-42a6-7339-965d-2550f18b776a'),
(53,'user','019b6d6a-42b1-706f-9179-7b6345af82fa'),
(53,'user','019b6d6a-42bc-70d1-a112-e0fe3b903fd4'),
(53,'user','019b6d6a-42c7-707e-8cf5-c6fe54f0dc63'),
(53,'user','019b6d6a-42d4-70ad-841f-f11c465d3d71'),
(53,'user','019b6d6a-42de-7217-b42e-3014a39e63f1'),
(53,'user','019b6d6a-42e8-715a-8612-96c9e63563c1'),
(53,'user','019b6d6a-42f4-72c7-abd8-743dfe85a5d3'),
(53,'user','019b6d6a-4300-72a5-b50c-661119bea32d'),
(53,'user','019b6d6a-430a-73b1-acf0-5312f326a620'),
(53,'user','019b6d6a-4315-70e8-8c2a-36d97bb20ccc'),
(53,'user','019b6d6a-4783-72c3-887a-e2f4ece51fe0'),
(53,'user','019b6d6a-4799-7138-9f80-6c37ed306886'),
(53,'user','019b6d6a-47a4-70b2-9870-d67bfba64940'),
(53,'user','019b6d6a-47af-734e-8b18-f7bf61be4435'),
(53,'user','019b6d6a-47bb-70e8-8913-ceb8d47dc521'),
(53,'user','019b6d6a-47c5-70b9-a6bc-d740920e0f70'),
(53,'user','019b6d6a-483d-70fc-8111-d95fa21d4053'),
(53,'user','019b6d6a-484b-700f-8dfc-75e2f886a501'),
(53,'user','019b6d6a-4856-703a-bacd-b4a1bdb99017'),
(53,'user','019b6d6a-4862-71f0-88d8-66a0642bc6d4'),
(53,'user','019b6d6a-486d-7119-8911-0053c9927a05'),
(53,'user','019b6d6a-4877-71c8-872e-c7e14be4ac25'),
(53,'user','019b6d6a-4882-7215-8ca6-2ad91e66e6ed'),
(53,'user','019b6d6a-488d-72ef-9885-c449eb94bfb9'),
(53,'user','019b6d6a-4899-7258-86f0-911d5747cfa6'),
(53,'user','019b6d6a-48a4-7330-986a-9968755e071a'),
(53,'user','019b6d6a-48af-72e4-ae52-04be56bd0dc6'),
(53,'user','019b6d6a-48ba-73d6-9e75-46b930c8483b'),
(53,'user','019b6d6a-48c6-7237-9416-76401bd5fcfe'),
(53,'user','019b6d6a-48d0-715c-917c-79af4bc8d41e'),
(53,'user','019b6d6a-48dc-725e-81d3-e057b93cdd89'),
(53,'user','019b6d6a-4948-738d-9ce1-0830d550d7e8'),
(53,'user','019b6d6a-4956-7230-b33e-935c49c5b471'),
(53,'user','019b6d6a-4961-73c7-ba1a-cdb847cf9d13'),
(53,'user','019b6d6a-496c-72b7-b785-1cd4539dc7af'),
(53,'user','019b6d6a-4978-726d-92fa-9d9bbbb13710'),
(53,'user','019b6d6a-4983-7361-b703-85f220e6667f'),
(53,'user','019b6d6a-498e-723b-b5ae-1e9d665e9f7c'),
(53,'user','019b6d6a-49a3-7369-8864-13ea16e86360'),
(53,'user','019b6d6a-49af-7394-84af-3fb0ac1cf334'),
(53,'user','019b6d6a-5043-7373-8c66-887bde77c289'),
(53,'user','019b6d6a-509e-7220-83a9-4639cf43722a'),
(53,'user','019b6d6a-50ac-7310-80af-245a69aada1e'),
(53,'user','019b6d6a-50b7-727d-9681-5835f0ae5675'),
(53,'user','019b6d6a-50c2-70ac-8b57-b7e6dc18e456'),
(53,'user','019b6d6a-50cd-7265-913e-32c2421a6195'),
(53,'user','019b6d6a-50d9-72f9-890d-cdb5c1a6e228'),
(53,'user','019b6d6a-50e3-7239-a694-7cd1eae0875f'),
(53,'user','019b6d6a-50ee-7391-ba4a-481bf333806c'),
(53,'user','019b6d6a-50fa-713c-a289-e0c07972346a'),
(53,'user','019b6d6a-5104-7053-abd0-3a79779c1bf5'),
(53,'user','019b6d6a-510f-721b-8dcf-c6eee66c7cf6'),
(53,'user','019b6d6a-5166-7017-8b85-5eb55295ea39'),
(53,'user','019b6d6a-5174-71ab-acb2-06ac89482941'),
(53,'user','019b6d6a-517e-7361-b92a-09a7737dadf3'),
(53,'user','019b6d6a-518a-73ba-852b-911607b9c03d'),
(53,'user','019b6d6a-5196-71e0-a377-30c4f30eb732'),
(53,'user','019b6d6a-51a0-7141-9053-494cc5406fd4'),
(53,'user','019b6d6a-51ac-7265-841c-eaf48c695d92'),
(53,'user','019b6d6a-51b7-73d3-b61a-819ac23dca56'),
(53,'user','019b6d6a-51c2-7269-8c11-7913337e90bc'),
(53,'user','019b6d6a-51cc-7157-a6b6-0521a80bd78e'),
(53,'user','019b6d6a-51d7-7271-8dc2-929f4d4a2046'),
(53,'user','019b6d6a-5222-7005-b98e-5fe84270f3ba'),
(53,'user','019b6d6a-5231-70ef-a7f1-34a7720a109a'),
(53,'user','019b6d6a-523c-7067-9587-5077a7e6206f'),
(53,'user','019b6d6a-5247-71b9-8b68-1bbce6bbf6c3'),
(53,'user','019b6d6a-5252-7025-aaf0-da78cb31a2dd'),
(53,'user','019b6d6a-525e-70cb-9755-22e94cd5bd28'),
(53,'user','019b6d6a-5268-72bb-b3e2-64ca3e4829c5'),
(53,'user','019b6d6a-578c-705c-bdf4-f908604d58f9'),
(53,'user','019b6d6a-57de-707c-8fac-4dd6533c9470'),
(53,'user','019b6d6a-57ec-7019-8758-cb9f54eb77d7'),
(53,'user','019b6d6a-57f7-706f-9b28-1e5f458cf825'),
(53,'user','019b6d6a-5802-73bb-bbc1-857f9a0bddbd'),
(53,'user','019b6d6a-580e-71ef-aa2d-ddf782e17ed9'),
(53,'user','019b6d6a-5819-7340-ba40-df446afbcb13'),
(53,'user','019b6d6a-587d-724f-bf29-7a90f1083974'),
(53,'user','019b6d6a-5888-730d-b2b1-f8125431fbb6'),
(53,'user','019b6d6a-5894-7087-97d8-3806c9e18669'),
(53,'user','019b6d6a-589e-7241-b413-27158a748942'),
(53,'user','019b6d6a-58a9-70a7-8f8d-4bb037897273'),
(53,'user','019b6d6a-58b5-72b2-b28d-35869d5aff78'),
(53,'user','019b6d6a-590a-735d-87f2-95a3913ebb1a'),
(53,'user','019b6d6a-5923-738a-821f-0ca37dd54fb2'),
(53,'user','019b6d6a-592f-712f-b291-2c30d303d7dd'),
(53,'user','019b6d6a-5939-72fd-9a2d-c8ed4212c0e1'),
(53,'user','019b6d6a-5990-719c-9b09-50a6904ef607'),
(53,'user','019b6d6a-59aa-722a-b1ea-96a766f0f55e'),
(53,'user','019b6d6a-59c0-709c-b1ba-a75ff8e165c1'),
(53,'user','019b6d6a-5a20-71ee-a303-3ac11f694003'),
(53,'user','019b6d6a-5a39-709f-a6b2-499b809d4e29'),
(53,'user','019b6d6a-5a44-7330-b7c0-4d48dcfc5626'),
(53,'user','019b6d6a-5a4f-722c-ab1e-14b1bbcdd6f8'),
(53,'user','019b6d6a-5a9a-73fa-b1b9-b5b2f75b6bb3'),
(53,'user','019b6d6a-5ab3-70ef-976c-a4ece5051bce'),
(53,'user','019b6d6a-5aca-715d-9276-a0f701a6af1d'),
(53,'user','019b6d6a-5b2b-7299-8160-aa7642dadfaf'),
(53,'user','019b6d6a-5b45-71bb-9903-550550b31b84'),
(53,'user','019b6d6a-5b99-710f-b389-ebceda01b864'),
(34,'user','06c39b01-8d7d-3dc5-b738-07cad0aaf0e9'),
(33,'user','070ed6a0-5d5e-3b50-aee2-79cff57d2873'),
(38,'user','0bff6289-dd51-3114-aa31-87cf1f954087'),
(44,'user','0ce855e9-000d-356f-946a-af16cd4b9db0'),
(44,'user','0ff96be5-7e72-331b-9ebd-e52d1c607ea5'),
(32,'user','1455aa2f-8b57-3839-a7ea-d64a0129ce1d'),
(34,'user','15c80da1-8059-3665-bf5a-690170791407'),
(41,'user','18218b53-64bc-3735-8d7e-ad25b8a3bd37'),
(33,'user','186cb680-ca9f-3d01-9ba1-c86de5243466'),
(45,'user','1a3e8f9c-5830-34b1-892a-bbe2725dbbc0'),
(46,'user','1a628953-5f9e-39f3-b85e-87d406ffb474'),
(42,'user','1eb3adf7-55ae-3694-93fc-9c60da92ebbb'),
(35,'user','2392dab5-53e7-3a12-88b5-e28daebfcbe5'),
(39,'user','23b13cd9-6f31-3745-8061-d5f0dd77f130'),
(42,'user','23b13cd9-6f31-3745-8061-d5f0dd77f130'),
(44,'user','2595e4c6-34e9-3d99-a1e2-638e3b626439'),
(44,'user','26409d0c-0f8c-38b3-9715-6050d6d56017'),
(35,'user','274aed69-6b4b-370e-b86b-bf72ebbd8a36'),
(44,'user','27837c68-0ad8-3344-8495-5efd41354aef'),
(45,'user','2b0252b3-5afa-33e0-99c4-50a15dc3876c'),
(41,'user','2b169c96-7357-3475-91df-a606ce520497'),
(51,'user','2caff012-199e-305f-adbb-c9390cd45222'),
(51,'user','2d1b7001-a4c8-3723-b6d4-91670b3d7657'),
(31,'user','2f54369a-d00a-322f-aed3-3d2c2b258f81'),
(42,'user','30ba7f40-f8bc-3eee-9a1d-40d20a673f6c'),
(34,'user','31199703-55e7-3746-a17c-9d4e2b1a8384'),
(37,'user','33ed81d2-c331-3e6d-a248-a63fda82b8da'),
(31,'user','3540cf43-7bf7-306c-8040-4b8552c61f61'),
(46,'user','367c6adc-d085-3312-be53-badfde6a0d79'),
(45,'user','38eb26d5-c6f3-38f5-96cc-77532ac50c8b'),
(35,'user','3b866466-ec94-3176-babf-8baf843aba33'),
(46,'user','3ce07f1c-fbbb-38ad-87f3-718626a3233c'),
(44,'user','4133497a-8d56-372d-a5fb-9dfaa452574e'),
(41,'user','4568e478-f0c0-3b29-ab90-21e6f2d86051'),
(31,'user','45abd0f5-fc1e-3c9a-882f-f9ca82669a44'),
(38,'user','470de4db-992b-3be1-9a94-84579fb258f8'),
(32,'user','4e67324c-3687-3be0-b526-b338fba12748'),
(46,'user','5282faf3-cb0c-3967-a09d-92c139b749cf'),
(39,'user','55fa27a3-dc2e-314a-8e5d-d99bfd341ec0'),
(45,'user','563542b8-71d6-3f5b-8def-07dad391cc80'),
(42,'user','58952267-3f16-34a6-976d-ad425049ecd7'),
(46,'user','61b929af-6f22-3aa9-86a4-466259d7c090'),
(42,'user','64671e7f-39a7-3677-8275-79f7b848c230'),
(42,'user','64e1b365-4bba-36d9-a0e9-0db4ce80224f'),
(31,'user','6bc9140c-aca5-34b5-999b-44530f370ec0'),
(39,'user','6daea7be-0da3-35b8-b003-b66fa95c273f'),
(44,'user','6de509aa-e42e-3922-9dbc-9b92c206f853'),
(45,'user','6e2153b1-39e0-3151-ade4-c1d49e5cb894'),
(44,'user','6e42e052-0341-361b-bcdd-860d7757fd06'),
(41,'user','6e92b76c-3d00-3df7-9613-9ab5eaee8338'),
(50,'user','705e4415-134a-3737-91ef-e7b86884bb5e'),
(37,'user','72359c50-1603-3616-93ea-b9b851d8623f'),
(38,'user','7b518e23-aa14-3752-9dff-cfab9417a704'),
(44,'user','7f53ba7c-e9bf-339e-b165-b2aec469e1a2'),
(31,'user','8172bc43-8725-3898-84d6-8425942a49ba'),
(33,'user','8200cf43-0379-3ac6-88f7-140eb0742b38'),
(35,'user','82403e8e-9c4d-3772-9b6f-596813c258b7'),
(46,'user','83b895cb-83e3-3aed-9c73-487627497791'),
(33,'user','85e5094e-60d9-37c6-9bcd-a0b2cb999e48'),
(32,'user','8bdc6cea-5422-397e-b84a-68065ad17bf6'),
(37,'user','8c2d0efe-174c-316c-9c67-c25f6c62b66c'),
(39,'user','924fe9a5-d092-3e63-b0df-e46c92b036d2'),
(37,'user','99807fc1-da43-3ed3-a178-e719f8c0e124'),
(41,'user','9a1ed35c-3e93-3b48-bf7a-57c7601fc5ca'),
(51,'user','9e8bd907-63ff-3725-b6a6-5f532d4acfc2'),
(44,'user','a65543a1-6460-39e7-9a3e-6ea33810b8a5'),
(41,'user','a717db71-9253-3954-a617-f9a0a3a49263'),
(41,'user','b32e9c70-7c0a-3af6-a003-05052959ad00'),
(41,'user','b3a9af48-233f-3337-803f-05b3a3bc1eb8'),
(39,'user','b48e2fe7-724b-30d4-8d28-45834cd96c3d'),
(34,'user','b4e76365-9665-34ff-b271-55ca7249aabc'),
(33,'user','b5bc19a6-ea8f-399d-a779-ec200ebf3cc7'),
(50,'user','b69eea6e-ea45-331b-9b13-3d33a4a1dd9b'),
(44,'user','b768adb3-05b3-3442-a36b-66b2d4aaac30'),
(44,'user','b79c2690-9a25-3c70-bd4b-b575bea73cb6'),
(37,'user','b9806d0e-2cba-34b3-b232-ba0172a13b9a'),
(34,'user','bc19e214-3daf-359a-beca-f28918110739'),
(46,'user','bdbc5752-938c-35c6-b41d-a5c900252b9c'),
(32,'user','bed11508-7f85-3920-9225-b1d754b59369'),
(42,'user','c0ad7a09-5959-3bbc-a59c-e0c5c658b6b0'),
(41,'user','c19ef4e3-3b77-3259-97bf-b625b45e285f'),
(42,'user','c6be9043-8066-394d-9d05-9d9585c16ea0'),
(42,'user','caa36c55-d5fd-34d1-9f1d-0972938ed718'),
(41,'user','cb29e3c5-e8f9-3b1d-8b2c-71a5e78bbcc9'),
(41,'user','d206fc44-7038-314d-a866-e589a5f8cb9c'),
(42,'user','d2f8919c-5463-3de1-a5dd-d9ae0e0634df'),
(50,'user','d311efcc-4ade-3ca1-9ab0-1b1ff56b5d82'),
(50,'user','d406a8a6-a347-3814-9821-98774d969671'),
(41,'user','d82f4d55-91cc-30b6-8818-652c44e34260'),
(51,'user','d84cdb00-2f5e-3ef4-982c-76c06c930df2'),
(41,'user','d908c1fa-a982-3421-8f63-06468001d6e0'),
(46,'user','de5d2ba5-19ef-3993-b262-f4eb02c1d35d'),
(44,'user','e657f8fb-6b49-30e6-801e-e31b7f6ad1e9'),
(46,'user','e661c7b3-2125-36d4-aa57-bcd810372c62'),
(38,'user','e91615a0-9451-3708-9365-f45a8c130410'),
(44,'user','e988b397-81c0-3a7d-abe4-5861d7a0c7e1'),
(50,'user','ebea4a0c-f4dd-3d83-bd48-f790abd49d36'),
(38,'user','ec548e0f-52c6-3df0-bfd8-edd754a199b7'),
(42,'user','ef25fc4d-f2e8-3257-8f2a-742d60f81878'),
(51,'user','f6360c99-6102-31c5-a82f-8d2db81e06f0'),
(41,'user','f91b915c-fea4-38f2-9e43-a24f1fa8a851'),
(35,'user','fa020d09-2655-3943-8afc-15bd8a9d68f1'),
(44,'user','fbdff02f-0753-3575-9388-a315ba483036'),
(32,'user','fef5c27b-70cd-38ce-a3c8-fd46f966c382'),
(41,'user','ffc81e79-0851-3df5-9b26-5c48dbacc2ea');
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
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `permissions` VALUES
(1,'view-roles','web',0,'View all roles','system','2025-12-30 04:00:08','2025-12-30 04:00:08'),
(2,'create-roles','web',0,'Create new roles','system','2025-12-30 04:00:08','2025-12-30 04:00:08'),
(3,'edit-roles','web',0,'Edit roles','system','2025-12-30 04:00:08','2025-12-30 04:00:08'),
(4,'delete-roles','web',0,'Delete roles','system','2025-12-30 04:00:08','2025-12-30 04:00:08'),
(5,'assign-roles','web',0,'Assign roles to users','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(6,'view-permissions','web',0,'View all permissions','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(7,'manage-permissions','web',0,'Manage permissions','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(8,'view-branches','web',0,'View all branches','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(9,'create-branches','web',0,'Create new branches','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(10,'edit-branches','web',0,'Edit branch information','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(11,'delete-branches','web',0,'Delete branches','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(12,'manage-settings','web',0,'Manage system settings','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(13,'view-audit-logs','web',0,'View audit logs','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(14,'view-activity-logs','web',0,'View activity logs','system','2025-12-30 04:00:09','2025-12-30 04:00:09'),
(15,'manage-system','web',0,'Full system management','system','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(16,'view-employees','web',0,'View employee list','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(17,'create-employees','web',0,'Create new employees','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(18,'edit-employees','web',0,'Edit employee information','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(19,'delete-employees','web',0,'Delete employee records','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(20,'view-departments','web',0,'View departments','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(21,'create-departments','web',0,'Create departments','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(22,'edit-departments','web',0,'Edit departments','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(23,'delete-departments','web',0,'Delete departments','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(24,'manage-staff-schedule','web',0,'Manage employee schedules','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(25,'manage-leave','web',0,'Manage employee leave','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(26,'approve-leave','web',0,'Approve leave requests','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(27,'view-payroll','web',0,'View payroll information','hr','2025-12-30 04:00:10','2025-12-30 04:00:10'),
(28,'manage-payroll','web',0,'Manage payroll','hr','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(29,'view-hr-reports','web',0,'View HR reports','hr','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(30,'manage-roles-assignments','web',0,'Manage employee role assignments','hr','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(31,'view-employee-details','web',0,'View detailed employee information','hr','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(32,'view-production-queue','web',0,'View production queue','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(33,'create-production-order','web',0,'Create production orders','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(34,'start-production','web',0,'Start production batches','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(35,'complete-production','web',0,'Complete production batches','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(36,'approve-production','web',0,'Approve production','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(37,'manage-recipes','web',0,'Create and edit recipes','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(38,'view-recipes','web',0,'View recipes','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(39,'view-production-reports','web',0,'View production analytics','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(40,'manage-quality-control','web',0,'Manage quality control','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(41,'view-batch-history','web',0,'View production batch history','production','2025-12-30 04:00:11','2025-12-30 04:00:11'),
(42,'edit-production-order','web',0,'Edit production orders','production','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(43,'cancel-production','web',0,'Cancel production orders','production','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(44,'view-production-cost','web',0,'View production costs','production','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(45,'manage-production-settings','web',0,'Manage production settings','production','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(46,'view-stock-levels','web',0,'View stock levels','inventory','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(47,'receive-stock','web',0,'Receive inventory','inventory','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(48,'transfer-stock','web',0,'Transfer stock between locations','inventory','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(49,'adjust-inventory','web',0,'Adjust inventory counts','inventory','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(50,'create-purchase-order','web',0,'Create purchase orders','inventory','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(51,'approve-purchase-order','web',0,'Approve purchase orders','inventory','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(52,'view-inventory-reports','web',0,'View inventory reports','inventory','2025-12-30 04:00:12','2025-12-30 04:00:12'),
(53,'manage-suppliers','web',0,'Full supplier management','inventory','2025-12-30 04:00:13','2025-12-30 04:00:13'),
(54,'view-suppliers','web',0,'View suppliers list','inventory','2025-12-30 04:00:13','2025-12-30 04:00:13'),
(55,'create-suppliers','web',0,'Create new suppliers','inventory','2025-12-30 04:00:13','2025-12-30 04:00:13'),
(56,'edit-suppliers','web',0,'Edit supplier information','inventory','2025-12-30 04:00:13','2025-12-30 04:00:13'),
(57,'delete-suppliers','web',0,'Delete suppliers','inventory','2025-12-30 04:00:13','2025-12-30 04:00:13'),
(58,'view-stock-valuation','web',0,'View stock valuation','inventory','2025-12-30 04:00:13','2025-12-30 04:00:13'),
(59,'manage-stock-categories','web',0,'Manage stock categories','inventory','2025-12-30 04:00:13','2025-12-30 04:00:13'),
(60,'view-reorder-levels','web',0,'View reorder levels','inventory','2025-12-30 04:00:14','2025-12-30 04:00:14'),
(61,'manage-reorder-levels','web',0,'Manage reorder levels','inventory','2025-12-30 04:00:14','2025-12-30 04:00:14'),
(62,'write-off-stock','web',0,'Write off stock items','inventory','2025-12-30 04:00:14','2025-12-30 04:00:14'),
(63,'view-stock-history','web',0,'View stock transaction history','inventory','2025-12-30 04:00:14','2025-12-30 04:00:14'),
(64,'manage-inventory-settings','web',0,'Manage inventory settings','inventory','2025-12-30 04:00:14','2025-12-30 04:00:14'),
(65,'view-sales-dashboard','web',0,'Access sales dashboard','sales','2025-12-30 04:00:14','2025-12-30 04:00:14'),
(66,'process-sale','web',0,'Process sales transactions','sales','2025-12-30 04:00:14','2025-12-30 04:00:14'),
(67,'issue-refund','web',0,'Issue refunds','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(68,'view-daily-sales','web',0,'View daily sales','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(69,'close-register','web',0,'Close cash registers','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(70,'view-sales-reports','web',0,'View sales analytics','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(71,'manage-sales-discounts','web',0,'Manage sales discounts','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(72,'view-sales-transactions','web',0,'View sales transactions','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(73,'edit-sales-transactions','web',0,'Edit sales transactions','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(74,'void-sales-transactions','web',0,'Void sales transactions','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(75,'manage-payment-methods','web',0,'Manage payment methods','sales','2025-12-30 04:00:15','2025-12-30 04:00:15'),
(76,'view-till-records','web',0,'View till/register records','sales','2025-12-30 04:00:16','2025-12-30 04:00:16'),
(77,'view-chart-accounts','web',0,'View chart of accounts','accounting','2025-12-30 04:00:16','2025-12-30 04:00:16'),
(78,'create-accounts','web',0,'Create general ledger accounts','accounting','2025-12-30 04:00:16','2025-12-30 04:00:16'),
(79,'edit-accounts','web',0,'Edit general ledger accounts','accounting','2025-12-30 04:00:16','2025-12-30 04:00:16'),
(80,'view-gl-entries','web',0,'View general ledger entries','accounting','2025-12-30 04:00:16','2025-12-30 04:00:16'),
(81,'create-gl-entries','web',0,'Create GL entries','accounting','2025-12-30 04:00:16','2025-12-30 04:00:16'),
(82,'post-gl-entries','web',0,'Post GL entries','accounting','2025-12-30 04:00:16','2025-12-30 04:00:16'),
(83,'reverse-gl-entries','web',0,'Reverse GL entries','accounting','2025-12-30 04:00:17','2025-12-30 04:00:17'),
(84,'view-accounting-reports','web',0,'View accounting reports','accounting','2025-12-30 04:00:17','2025-12-30 04:00:17'),
(85,'reconcile-accounts','web',0,'Reconcile bank accounts','accounting','2025-12-30 04:00:17','2025-12-30 04:00:17'),
(86,'manage-bank-accounts','web',0,'Manage bank accounts','accounting','2025-12-30 04:00:17','2025-12-30 04:00:17'),
(87,'view-trial-balance','web',0,'View trial balance','accounting','2025-12-30 04:00:17','2025-12-30 04:00:17'),
(88,'view-financial-statements','web',0,'View financial statements','accounting','2025-12-30 04:00:17','2025-12-30 04:00:17'),
(89,'manage-accounting-period','web',0,'Manage accounting periods','accounting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(90,'view-account-reconciliation','web',0,'View account reconciliation','accounting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(91,'view-analytics','web',0,'View analytics dashboard','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(92,'view-department-reports','web',0,'View department reports','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(93,'generate-reports','web',0,'Generate custom reports','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(94,'export-reports','web',0,'Export reports to files','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(95,'schedule-reports','web',0,'Schedule automated reports','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(96,'view-dashboard','web',0,'View main dashboard','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(97,'view-branch-reports','web',0,'View branch-specific reports','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(98,'view-kpi-metrics','web',0,'View KPI metrics','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(99,'export-data','web',0,'Export system data','reporting','2025-12-30 04:00:18','2025-12-30 04:00:18'),
(100,'view-activity-timeline','web',0,'View activity timeline','reporting','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(101,'view_inventory_dashboard','web',0,'Access inventory dashboard','dashboard','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(102,'view_production_dashboard','web',0,'Access production dashboard','dashboard','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(103,'view_sales_dashboard','web',0,'Access sales dashboard','dashboard','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(104,'view_corner_store_dashboard','web',0,'Access corner store dashboard','dashboard','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(105,'view_hr_dashboard','web',0,'Access HR dashboard','dashboard','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(106,'view_admin_dashboard','web',0,'Access admin dashboard','dashboard','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(107,'view_super_admin_dashboard','web',0,'Access super admin dashboard','dashboard','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(108,'manage_organization','web',0,'Manage organization (employees, departments)','organization','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(109,'manage_roles','web',0,'Manage user roles and permissions','organization','2025-12-30 04:00:19','2025-12-30 04:00:19'),
(110,'manage_branches','web',0,'Manage branches','organization','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(111,'manage_settings','web',0,'Manage system settings','organization','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(112,'view_reports','web',0,'View system reports','organization','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(113,'access_accounting','web',0,'Access accounting module','accounting','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(114,'view_financial_reports','web',0,'View financial reports','accounting','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(115,'manage_accounts','web',0,'Manage chart of accounts','accounting','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(116,'manage_periods','web',0,'Manage accounting periods','accounting','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(117,'create_journal_entries','web',0,'Create journal entries','accounting','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(118,'reconcile_bank_accounts','web',0,'Reconcile bank accounts','accounting','2025-12-30 04:00:20','2025-12-30 04:00:20'),
(119,'view-audit','web',0,NULL,NULL,'2026-01-02 12:13:43','2026-01-02 12:13:43');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_stocks`
--

LOCK TABLES `product_stocks` WRITE;
/*!40000 ALTER TABLE `product_stocks` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `product_stocks` VALUES
(1,NULL,'019b6d6a-9e57-7229-9ece-da56055b66c1','2026-01-01','morning',42.00,0.00,'2026-01-01','2026-01-03',0.00,0.00,42.00,0.00,0.00,0.00,42.00,0.00,'',0,NULL,NULL,'opening_pending','2026-01-01 08:09:54','2026-01-01 09:07:24'),
(2,NULL,'019b6d6a-9e6d-73dd-bc4e-aaba43ea872b','2026-01-01','morning',21.00,0.00,'2026-01-01','2026-01-08',0.00,0.00,21.00,0.00,0.00,0.00,21.00,0.00,'',0,NULL,NULL,'opening_pending','2026-01-01 08:09:54','2026-01-01 09:07:24'),
(3,NULL,'019b6d6a-9e50-72f1-bfc5-7b56148bf06f','2026-01-01','morning',47.00,0.00,'2026-01-01','2026-01-03',0.00,0.00,47.00,0.00,0.00,0.00,47.00,0.00,'',0,NULL,NULL,'opening_pending','2026-01-01 08:09:54','2026-01-01 09:07:24'),
(4,NULL,'019b6d6a-9e86-7202-b308-776fdc77f9b5','2026-01-01','morning',18.00,0.00,'2026-01-01','2026-01-11',0.00,0.00,18.00,0.00,0.00,0.00,18.00,0.00,'',0,NULL,NULL,'opening_pending','2026-01-01 08:09:54','2026-01-01 09:07:24'),
(5,NULL,'019b6d6a-9e8f-7046-8a79-c759706576a2','2026-01-01','morning',44.00,0.00,'2026-01-01','2026-01-11',0.00,0.00,44.00,0.00,0.00,0.00,44.00,0.00,'',0,NULL,NULL,'opening_pending','2026-01-01 08:09:54','2026-01-01 09:07:24'),
(6,NULL,'019b6d6a-9e62-7212-ac6d-e46fef7cdb23','2026-01-01','morning',36.00,0.00,'2026-01-01','2026-01-05',0.00,0.00,36.00,0.00,0.00,0.00,36.00,0.00,'',0,NULL,NULL,'opening_pending','2026-01-01 08:09:54','2026-01-01 09:07:24'),
(7,NULL,'019b6d6a-9e79-73e4-8e8f-7d30de810dc2','2026-01-01','morning',40.00,0.00,NULL,NULL,0.00,0.00,40.00,0.00,0.00,0.00,40.00,0.00,NULL,0,NULL,NULL,'opening_pending','2026-01-01 09:04:29','2026-01-01 09:07:24'),
(8,NULL,'019b6d6a-9ea5-7161-be08-9a7833a67d45','2026-01-01','morning',50.00,0.00,NULL,NULL,0.00,0.00,50.00,0.00,0.00,0.00,50.00,0.00,NULL,0,NULL,NULL,'opening_pending','2026-01-01 09:04:29','2026-01-01 09:07:24'),
(9,NULL,'019b6d6a-9eb0-70d6-a8c0-54d004970cd8','2026-01-01','morning',24.00,0.00,NULL,NULL,0.00,0.00,24.00,0.00,0.00,0.00,24.00,0.00,NULL,0,NULL,NULL,'opening_pending','2026-01-01 09:04:29','2026-01-01 09:07:24'),
(10,NULL,'019b6d6a-9ebb-739d-86e0-1a1899bac33a','2026-01-01','morning',21.00,0.00,NULL,NULL,0.00,0.00,21.00,0.00,0.00,0.00,21.00,0.00,NULL,0,NULL,NULL,'opening_pending','2026-01-01 09:04:29','2026-01-01 09:07:24'),
(11,NULL,'019b6d6a-9ec7-725b-a95a-3d5d63660f9f','2026-01-01','morning',36.00,0.00,NULL,NULL,0.00,0.00,36.00,0.00,0.00,0.00,36.00,0.00,NULL,0,NULL,NULL,'opening_pending','2026-01-01 09:04:30','2026-01-01 09:07:24'),
(12,NULL,'019b6d6a-9ed2-7106-9e57-456f38716485','2026-01-01','morning',18.00,0.00,NULL,NULL,0.00,0.00,18.00,0.00,0.00,0.00,18.00,0.00,NULL,0,NULL,NULL,'opening_pending','2026-01-01 09:04:30','2026-01-01 09:07:24'),
(13,NULL,'019b6d6a-9edd-735f-b174-861b12525143','2026-01-01','morning',36.00,0.00,NULL,NULL,0.00,0.00,36.00,0.00,0.00,0.00,36.00,0.00,NULL,0,NULL,NULL,'opening_pending','2026-01-01 09:04:30','2026-01-01 09:07:24');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_types`
--

LOCK TABLES `product_types` WRITE;
/*!40000 ALTER TABLE `product_types` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `product_types` VALUES
(1,1,'Pastries','PT','Baked pastries including croissants, danishes, and puff pastries','active',1,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(2,1,'Breads','BR','Fresh baked breads, loaves, and rolls','active',2,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(3,1,'Cakes','CK','Layer cakes, sponge cakes, and celebration cakes','active',3,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(4,1,'Cookies','CO','Baked cookies and biscuits','active',4,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(5,1,'Muffins','MF','Sweet and savory muffins','active',5,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(6,1,'Pies & Tarts','PIT','Fruit pies, cream pies, and tarts','active',6,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(7,1,'Sandwiches','SW','Fresh sandwiches and wraps','active',7,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(8,1,'Hot Kitchen','HK','Cooked meals and hot food items','active',8,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(9,2,'Gelato Base','GB','Base gelato mixtures before flavoring','active',1,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(10,2,'Gelato Flavors','GF','Finished gelato in various flavors','active',2,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(11,2,'Sorbet','SB','Fruit-based frozen desserts without dairy','active',3,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(12,2,'Ice Cream','IC','Traditional ice cream products','active',4,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(13,2,'Frozen Yogurt','FY','Frozen yogurt in various flavors','active',5,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(14,2,'Gelato Toppings','GT','House-made toppings and mix-ins for gelato','active',6,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(15,3,'Chocolates','CH','Handcrafted chocolates and truffles','active',1,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(16,3,'Candies','CD','Hard and soft candies','active',2,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(17,3,'Fudge','FG','Traditional and flavored fudge','active',3,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(18,3,'Caramels','CR','Soft and hard caramels','active',4,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(19,3,'Marshmallows','MM','Gourmet marshmallows in various flavors','active',5,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
(20,3,'Nougat','NG','Traditional nougat confections','active',6,'2025-12-30 04:01:02','2025-12-30 04:01:02',NULL);
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
('019b6d6a-9e50-72f1-bfc5-7b56148bf06f','Butter Croissant','PT-BUT-001','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,'Classic French butter croissant, flaky and golden',3.50,1.20,2,13,100.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"breakfast\",\"french\",\"popular\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9e57-7229-9ece-da56055b66c1','Almond Danish','PT-ALM-002','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,'Sweet Danish pastry topped with sliced almonds',4.00,1.50,2,13,150.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\",\"nuts\"]','[\"breakfast\",\"pastry\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9e62-7212-ac6d-e46fef7cdb23','Sourdough Loaf','BR-SOU-001','019b6d6a-191b-7068-817b-94e02d09e859',2,NULL,'Artisan sourdough bread with tangy flavor',6.50,2.00,4,13,800.00,1,1,NULL,'[\"gluten\"]','[\"artisan\",\"sourdough\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9e6d-73dd-bc4e-aaba43ea872b','Banana Bread','BR-BAN-002','019b6d6a-191b-7068-817b-94e02d09e859',2,NULL,'Moist banana bread with walnuts',5.99,2.50,7,13,900.00,1,1,NULL,'[\"gluten\",\"eggs\",\"nuts\"]','[\"sweet\",\"popular\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9e79-73e4-8e8f-7d30de810dc2','Chocolate Cake Slice','CK-CHO-001','019b6d6a-191b-7068-817b-94e02d09e859',3,NULL,'Rich chocolate layer cake with chocolate ganache',7.50,2.80,5,13,200.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"chocolate\",\"dessert\",\"popular\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9e86-7202-b308-776fdc77f9b5','Chocolate Chip Cookie','CO-CHI-001','019b6d6a-191b-7068-817b-94e02d09e859',4,NULL,'Classic chocolate chip cookies',2.50,0.80,10,13,50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"popular\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9e8f-7046-8a79-c759706576a2','Oatmeal Raisin Cookie','CO-OAT-002','019b6d6a-191b-7068-817b-94e02d09e859',4,NULL,'Wholesome oatmeal cookies with plump raisins',2.50,0.75,10,13,50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"healthy\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9e9a-724e-b18f-ce50c9670f52','Vanilla Gelato Base','GB-VAN-001','019b6d6a-191b-7068-817b-94e02d09e859',9,NULL,'Premium vanilla gelato base mixture',0.00,8.50,3,3,1000.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"base\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9ea5-7161-be08-9a7833a67d45','Chocolate Gelato','GF-CHO-001','019b6d6a-191b-7068-817b-94e02d09e859',10,NULL,'Rich dark chocolate gelato',4.50,1.80,30,2,100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"chocolate\",\"popular\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9eb0-70d6-a8c0-54d004970cd8','Strawberry Gelato','GF-STR-002','019b6d6a-191b-7068-817b-94e02d09e859',10,NULL,'Fresh strawberry gelato',4.50,1.90,30,2,100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"fruit\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9ebb-739d-86e0-1a1899bac33a','Pistachio Gelato','GF-PIS-003','019b6d6a-191b-7068-817b-94e02d09e859',10,NULL,'Authentic pistachio gelato from Sicily',5.50,2.50,30,2,100.00,1,1,NULL,'[\"dairy\",\"nuts\"]','[\"gelato\",\"premium\",\"nuts\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9ec7-725b-a95a-3d5d63660f9f','Dark Chocolate Truffle','CH-DAR-001','019b6d6a-191b-7068-817b-94e02d09e859',15,NULL,'Hand-rolled dark chocolate truffle',2.50,0.90,21,13,20.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"premium\",\"truffle\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9ed2-7106-9e57-456f38716485','Salted Caramel Chocolate','CH-SAL-002','019b6d6a-191b-7068-817b-94e02d09e859',15,NULL,'Milk chocolate with salted caramel filling',2.75,1.00,21,13,25.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"caramel\",\"popular\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b6d6a-9edd-735f-b174-861b12525143','Fruit Gummies','CD-FRU-001','019b6d6a-191b-7068-817b-94e02d09e859',16,NULL,'Assorted fruit-flavored gummy candies',1.50,0.40,180,2,100.00,1,1,NULL,'[]','[\"candy\",\"fruit\",\"kids\"]','2025-12-30 04:01:02','2025-12-30 04:01:02',NULL),
('019b7a29-1efb-73f5-b8a5-0a841c16f377','testing','GB-TES-1707',NULL,9,NULL,'',20.00,NULL,77,17,NULL,1,1,'','[]','[]','2026-01-01 15:24:33','2026-01-01 15:24:33',NULL);
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
(1,1,2,500.0000,2,0.0020,5.00,0,'Required for Butter Croissant','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(2,1,4,300.0000,2,0.0080,2.00,1,'Required for Butter Croissant','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(3,1,1,50.0000,2,0.0010,3.00,2,'Required for Butter Croissant','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(4,1,8,150.0000,6,0.0030,1.00,3,'Required for Butter Croissant','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(5,1,5,0.5000,14,15.0000,0.00,4,'Required for Butter Croissant','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(6,1,10,15.0000,2,0.0200,0.00,5,'Required for Butter Croissant','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(7,1,12,10.0000,2,0.0010,0.00,6,'Required for Butter Croissant','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(8,2,2,280.0000,2,0.0020,5.00,0,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(9,2,4,225.0000,2,0.0080,2.00,1,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(10,2,1,200.0000,2,0.0010,3.00,2,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(11,2,5,0.5000,14,15.0000,0.00,3,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(12,2,6,0.0100,7,50.0000,0.00,4,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(13,2,7,300.0000,2,0.0150,1.00,5,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(14,3,2,250.0000,2,0.0020,5.00,0,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(15,3,3,75.0000,2,0.0250,2.00,1,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(16,3,1,400.0000,2,0.0010,3.00,2,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(17,3,5,0.6700,14,15.0000,0.00,3,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(18,3,11,125.0000,6,0.0050,1.00,4,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(19,3,6,0.0100,7,50.0000,0.00,5,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(20,3,9,200.0000,6,0.0100,1.00,6,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(21,3,7,200.0000,2,0.0150,1.00,7,'Required for Chocolate Cake Slice','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(22,4,8,2.0000,7,3.0000,2.00,0,'Required for Chocolate Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(23,4,9,1.5000,7,10.0000,2.00,1,'Required for Chocolate Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(24,4,1,600.0000,2,0.0010,3.00,2,'Required for Chocolate Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(25,4,3,200.0000,2,0.0250,2.00,3,'Required for Chocolate Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(26,4,5,1.0000,14,15.0000,0.00,4,'Required for Chocolate Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(27,5,8,1.8000,7,3.0000,2.00,0,'Required for Strawberry Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(28,5,9,1.2000,7,10.0000,2.00,1,'Required for Strawberry Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(29,5,1,550.0000,2,0.0010,3.00,2,'Required for Strawberry Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(30,5,5,0.8000,14,15.0000,0.00,3,'Required for Strawberry Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(31,6,8,1.8000,7,3.0000,2.00,0,'Required for Pistachio Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(32,6,9,1.3000,7,10.0000,2.00,1,'Required for Pistachio Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(33,6,1,580.0000,2,0.0010,3.00,2,'Required for Pistachio Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(34,6,5,0.9000,14,15.0000,0.00,3,'Required for Pistachio Gelato','Standard preparation','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(35,7,2,450.0000,2,0.0020,5.00,0,'Required for Almond Danish','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(36,7,4,280.0000,2,0.0080,2.00,1,'Required for Almond Danish','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(37,7,1,120.0000,2,0.0010,3.00,2,'Required for Almond Danish','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(38,7,5,0.6000,14,15.0000,0.00,3,'Required for Almond Danish','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(39,7,8,100.0000,6,0.0030,1.00,4,'Required for Almond Danish','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(40,7,6,0.0050,7,50.0000,0.00,5,'Required for Almond Danish','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(41,8,2,220.0000,2,0.0020,5.00,0,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(42,8,4,200.0000,2,0.0080,2.00,1,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(43,8,1,180.0000,2,0.0010,3.00,2,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(44,8,5,0.5000,14,15.0000,0.00,3,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(45,8,6,0.0080,7,50.0000,0.00,4,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(46,9,2,1000.0000,2,0.0020,5.00,0,'Required for Sourdough Loaf','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(47,9,12,20.0000,2,0.0010,0.00,1,'Required for Sourdough Loaf','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(48,9,10,10.0000,2,0.0200,0.00,2,'Required for Sourdough Loaf','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(49,10,2,280.0000,2,0.0020,5.00,0,'Required for Banana Bread','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(50,10,1,200.0000,2,0.0010,3.00,1,'Required for Banana Bread','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(51,10,4,120.0000,2,0.0080,2.00,2,'Required for Banana Bread','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(52,10,5,0.5000,14,15.0000,0.00,3,'Required for Banana Bread','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(53,10,11,80.0000,6,0.0050,1.00,4,'Required for Banana Bread','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(54,10,6,0.0050,7,50.0000,0.00,5,'Required for Banana Bread','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(55,11,7,500.0000,2,0.0150,2.00,0,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(56,11,9,300.0000,6,0.0100,1.00,1,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(57,11,4,50.0000,2,0.0080,1.00,2,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(58,11,3,100.0000,2,0.0250,2.00,3,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(59,11,6,0.0050,7,50.0000,0.00,4,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(60,12,7,600.0000,2,0.0150,2.00,0,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(61,12,1,200.0000,2,0.0010,3.00,1,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(62,12,9,200.0000,6,0.0100,1.00,2,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(63,12,4,80.0000,2,0.0080,1.00,3,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(64,12,12,5.0000,2,0.0010,0.00,4,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(65,13,8,4.0000,7,3.0000,2.00,0,'Required for Vanilla Gelato Base','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(66,13,9,1.0000,7,10.0000,2.00,1,'Required for Vanilla Gelato Base','Standard preparation','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(67,13,1,500.0000,2,0.0010,3.00,2,'Required for Vanilla Gelato Base','Standard preparation','2025-12-30 04:01:05','2025-12-30 04:01:05'),
(68,13,6,20.0000,6,0.0500,0.00,3,'Required for Vanilla Gelato Base','Standard preparation','2025-12-30 04:01:05','2025-12-30 04:01:05'),
(69,14,1,400.0000,2,0.0010,3.00,0,'Required for Fruit Gummies','Standard preparation','2025-12-30 04:01:05','2025-12-30 04:01:05');
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
(1,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e50-72f1-bfc5-7b56148bf06f','Butter Croissant','PT-BUT-001-RCP',1,0.4923,13,24.00,240,'[\"Mix flour, sugar, salt, and yeast in a large bowl\",\"Add cold butter pieces and work into the dough\",\"Knead until smooth and elastic\",\"Rest dough in refrigerator for 2 hours\",\"Roll out and fold dough multiple times (lamination)\",\"Cut into triangles and roll into croissant shape\",\"Proof for 1-2 hours until doubled\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes until golden\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(2,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e86-7202-b308-776fdc77f9b5','Chocolate Chip Cookie','CO-CHI-001-RCP',1,0.4215,13,36.00,45,'[\"Cream together butter and sugars\",\"Beat in eggs and vanilla extract\",\"Mix in flour, baking soda, and salt\",\"Fold in chocolate chips\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 10-12 minutes\",\"Cool on baking sheet for 5 minutes before transferring\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(3,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e79-73e4-8e8f-7d30de810dc2','Chocolate Cake Slice','CK-CHO-001-RCP',1,1.5901,13,12.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mix dry ingredients: flour, cocoa powder, baking soda, salt\",\"Beat together eggs, sugar, oil, and vanilla\",\"Add hot water and mix until smooth\",\"Pour into greased cake pans\",\"Bake for 30-35 minutes\",\"Cool completely before frosting\",\"Prepare chocolate ganache and frost the cake\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(4,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9ea5-7161-be08-9a7833a67d45','Chocolate Gelato','GF-CHO-001-RCP',1,0.8428,2,50.00,60,'[\"Heat milk and cream to 85\\u00b0C\",\"Whisk cocoa powder with some warm milk\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add cocoa mixture and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(5,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9eb0-70d6-a8c0-54d004970cd8','Strawberry Gelato','GF-STR-002-RCP',1,0.6063,2,50.00,60,'[\"Blend fresh strawberries to puree\",\"Heat milk, cream, and sugar to 85\\u00b0C\",\"Mix with egg yolks\",\"Cool completely\",\"Add strawberry puree\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:03','2025-12-30 04:01:03'),
(6,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9ebb-739d-86e0-1a1899bac33a','Pistachio Gelato','GF-PIS-003-RCP',1,0.6573,2,50.00,70,'[\"Grind pistachios into fine paste\",\"Heat milk and cream to 85\\u00b0C\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add pistachio paste and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:03','2025-12-30 04:01:04'),
(7,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e57-7229-9ece-da56055b66c1','Almond Danish','PT-ALM-002-RCP',1,0.7170,13,18.00,180,'[\"Prepare puff pastry dough\",\"Roll and fold dough multiple times\",\"Cut into squares\",\"Add almond cream filling\",\"Top with sliced almonds\",\"Proof for 1 hour\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(8,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e8f-7046-8a79-c759706576a2','Oatmeal Raisin Cookie','CO-OAT-002-RCP',1,0.2545,13,40.00,40,'[\"Cream butter and sugars together\",\"Beat in eggs and vanilla\",\"Mix in flour, oats, and spices\",\"Fold in raisins\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 12-14 minutes\",\"Cool before serving\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(9,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e62-7212-ac6d-e46fef7cdb23','Sourdough Loaf','BR-SOU-001-RCP',8,0.5800,13,4.00,1440,'[\"Feed sourdough starter 12 hours before\",\"Mix flour, water, salt, and starter\",\"Autolyse for 30 minutes\",\"Stretch and fold every 30 minutes (4 times)\",\"Bulk ferment for 4-6 hours\",\"Shape into loaves\",\"Cold ferment overnight (12 hours)\",\"Score and bake at 230\\u00b0C for 35-40 minutes\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(10,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e6d-73dd-bc4e-aaba43ea872b','Banana Bread','BR-BAN-002-RCP',8,4.9636,13,2.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mash ripe bananas\",\"Mix butter, sugar, and eggs\",\"Add mashed bananas\",\"Mix in flour, baking soda, and salt\",\"Pour into greased loaf pans\",\"Bake for 55-60 minutes\",\"Cool before slicing\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(11,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9ec7-725b-a95a-3d5d63660f9f','Dark Chocolate Truffle','CH-DAR-001-RCP',8,0.2777,13,50.00,120,'[\"Chop dark chocolate finely\",\"Heat cream to simmering\",\"Pour over chocolate and let sit\",\"Stir until smooth ganache forms\",\"Cool and refrigerate for 2 hours\",\"Roll into balls\",\"Coat with cocoa powder\",\"Store in refrigerator\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(12,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9ed2-7106-9e57-456f38716485','Salted Caramel Chocolate','CH-SAL-002-RCP',8,0.2512,13,48.00,150,'[\"Make caramel with sugar and cream\",\"Add salt to caramel and cool\",\"Temper milk chocolate\",\"Fill molds halfway with chocolate\",\"Add caramel filling\",\"Top with more chocolate\",\"Cool and unmold\",\"Store in cool place\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:04','2025-12-30 04:01:04'),
(13,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9e9a-724e-b18f-ce50c9670f52','Vanilla Gelato Base','GB-VAN-001-RCP',1,2.9944,3,8.00,45,'[\"Heat milk and cream to 85\\u00b0C\",\"Whisk in sugar until dissolved\",\"Add vanilla extract and mix well\",\"Cool to 4\\u00b0C\",\"Age in refrigerator for 4-12 hours\",\"Store at -18\\u00b0C until use\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:04','2025-12-30 04:01:05'),
(14,'019b6d6a-191b-7068-817b-94e02d09e859',1,'019b6d6a-9edd-735f-b174-861b12525143','Fruit Gummies','CD-FRU-001-RCP',1,0.0041,2,100.00,180,'[\"Heat fruit puree and sugar to 80\\u00b0C\",\"Dissolve gelatin in hot mixture\",\"Add citric acid and mix well\",\"Strain through fine mesh\",\"Pour into molds\",\"Cool at room temperature for 30 minutes\",\"Refrigerate for 2 hours until set\",\"Unmold and store in cool place\"]','active','01714abf-7822-351f-be69-4581a3545274','App\\Models\\Employee','2025-12-30 04:01:05','2025-12-30 04:01:05');
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
(1,28),
(2,28),
(3,28),
(4,28),
(5,28),
(6,28),
(7,28),
(8,28),
(9,28),
(10,28),
(11,28),
(12,28),
(13,28),
(14,28),
(15,28),
(16,28),
(17,28),
(18,28),
(19,28),
(20,28),
(21,28),
(22,28),
(23,28),
(24,28),
(25,28),
(26,28),
(27,28),
(28,28),
(29,28),
(30,28),
(31,28),
(32,28),
(33,28),
(34,28),
(35,28),
(36,28),
(37,28),
(38,28),
(39,28),
(40,28),
(41,28),
(42,28),
(43,28),
(44,28),
(45,28),
(46,28),
(47,28),
(48,28),
(49,28),
(50,28),
(51,28),
(52,28),
(53,28),
(54,28),
(55,28),
(56,28),
(57,28),
(58,28),
(59,28),
(60,28),
(61,28),
(62,28),
(63,28),
(64,28),
(65,28),
(66,28),
(67,28),
(68,28),
(69,28),
(70,28),
(71,28),
(72,28),
(73,28),
(74,28),
(75,28),
(76,28),
(77,28),
(78,28),
(79,28),
(80,28),
(81,28),
(82,28),
(83,28),
(84,28),
(85,28),
(86,28),
(87,28),
(88,28),
(89,28),
(90,28),
(91,28),
(92,28),
(93,28),
(94,28),
(95,28),
(96,28),
(97,28),
(98,28),
(99,28),
(100,28),
(101,28),
(102,28),
(103,28),
(104,28),
(105,28),
(106,28),
(107,28),
(108,28),
(109,28),
(110,28),
(111,28),
(112,28),
(113,28),
(114,28),
(115,28),
(116,28),
(117,28),
(118,28),
(1,29),
(2,29),
(3,29),
(4,29),
(5,29),
(6,29),
(7,29),
(8,29),
(9,29),
(10,29),
(11,29),
(12,29),
(13,29),
(14,29),
(15,29),
(16,29),
(17,29),
(18,29),
(19,29),
(20,29),
(21,29),
(22,29),
(23,29),
(24,29),
(25,29),
(26,29),
(27,29),
(28,29),
(29,29),
(30,29),
(31,29),
(32,29),
(33,29),
(34,29),
(35,29),
(36,29),
(37,29),
(38,29),
(39,29),
(40,29),
(41,29),
(42,29),
(43,29),
(44,29),
(45,29),
(46,29),
(47,29),
(48,29),
(49,29),
(50,29),
(51,29),
(52,29),
(53,29),
(54,29),
(55,29),
(56,29),
(57,29),
(58,29),
(59,29),
(60,29),
(61,29),
(62,29),
(63,29),
(64,29),
(65,29),
(66,29),
(67,29),
(68,29),
(69,29),
(70,29),
(71,29),
(72,29),
(73,29),
(74,29),
(75,29),
(76,29),
(77,29),
(78,29),
(79,29),
(80,29),
(81,29),
(82,29),
(83,29),
(84,29),
(85,29),
(86,29),
(87,29),
(88,29),
(89,29),
(90,29),
(91,29),
(92,29),
(93,29),
(94,29),
(95,29),
(96,29),
(97,29),
(98,29),
(99,29),
(100,29),
(101,29),
(102,29),
(103,29),
(104,29),
(105,29),
(106,29),
(107,29),
(108,29),
(109,29),
(110,29),
(111,29),
(112,29),
(113,29),
(114,29),
(115,29),
(116,29),
(117,29),
(118,29),
(1,30),
(2,30),
(3,30),
(4,30),
(5,30),
(6,30),
(7,30),
(8,30),
(9,30),
(10,30),
(11,30),
(12,30),
(13,30),
(14,30),
(15,30),
(16,30),
(17,30),
(18,30),
(19,30),
(20,30),
(21,30),
(22,30),
(23,30),
(24,30),
(25,30),
(26,30),
(27,30),
(28,30),
(29,30),
(30,30),
(31,30),
(32,30),
(33,30),
(34,30),
(35,30),
(36,30),
(37,30),
(38,30),
(39,30),
(40,30),
(41,30),
(42,30),
(43,30),
(44,30),
(45,30),
(46,30),
(47,30),
(48,30),
(49,30),
(50,30),
(51,30),
(52,30),
(53,30),
(54,30),
(55,30),
(56,30),
(57,30),
(58,30),
(59,30),
(60,30),
(61,30),
(62,30),
(63,30),
(64,30),
(65,30),
(66,30),
(67,30),
(68,30),
(69,30),
(70,30),
(71,30),
(72,30),
(73,30),
(74,30),
(75,30),
(76,30),
(77,30),
(78,30),
(79,30),
(80,30),
(81,30),
(82,30),
(83,30),
(84,30),
(85,30),
(86,30),
(87,30),
(88,30),
(89,30),
(90,30),
(91,30),
(92,30),
(93,30),
(94,30),
(95,30),
(96,30),
(97,30),
(98,30),
(99,30),
(100,30),
(101,30),
(102,30),
(103,30),
(104,30),
(105,30),
(106,30),
(107,30),
(108,30),
(109,30),
(110,30),
(111,30),
(112,30),
(113,30),
(114,30),
(115,30),
(116,30),
(117,30),
(118,30),
(1,31),
(2,31),
(3,31),
(4,31),
(5,31),
(6,31),
(7,31),
(8,31),
(9,31),
(10,31),
(11,31),
(12,31),
(13,31),
(14,31),
(15,31),
(16,31),
(17,31),
(18,31),
(19,31),
(20,31),
(21,31),
(22,31),
(23,31),
(24,31),
(25,31),
(26,31),
(27,31),
(28,31),
(29,31),
(30,31),
(31,31),
(32,31),
(33,31),
(34,31),
(35,31),
(36,31),
(37,31),
(38,31),
(39,31),
(40,31),
(41,31),
(42,31),
(43,31),
(44,31),
(45,31),
(46,31),
(47,31),
(48,31),
(49,31),
(50,31),
(51,31),
(52,31),
(53,31),
(54,31),
(55,31),
(56,31),
(57,31),
(58,31),
(59,31),
(60,31),
(61,31),
(62,31),
(63,31),
(64,31),
(65,31),
(66,31),
(67,31),
(68,31),
(69,31),
(70,31),
(71,31),
(72,31),
(73,31),
(74,31),
(75,31),
(76,31),
(77,31),
(78,31),
(79,31),
(80,31),
(81,31),
(82,31),
(83,31),
(84,31),
(85,31),
(86,31),
(87,31),
(88,31),
(89,31),
(90,31),
(91,31),
(92,31),
(93,31),
(94,31),
(95,31),
(96,31),
(97,31),
(98,31),
(99,31),
(100,31),
(101,31),
(102,31),
(103,31),
(104,31),
(105,31),
(106,31),
(107,31),
(108,31),
(109,31),
(110,31),
(111,31),
(112,31),
(113,31),
(114,31),
(115,31),
(116,31),
(117,31),
(118,31),
(16,32),
(20,32),
(24,32),
(29,32),
(32,32),
(33,32),
(34,32),
(35,32),
(36,32),
(37,32),
(38,32),
(39,32),
(40,32),
(41,32),
(42,32),
(43,32),
(44,32),
(46,32),
(91,32),
(92,32),
(96,32),
(16,33),
(20,33),
(24,33),
(29,33),
(46,33),
(65,33),
(66,33),
(67,33),
(68,33),
(69,33),
(70,33),
(71,33),
(72,33),
(73,33),
(74,33),
(75,33),
(76,33),
(91,33),
(92,33),
(96,33),
(8,34),
(16,34),
(17,34),
(18,34),
(19,34),
(20,34),
(21,34),
(22,34),
(23,34),
(24,34),
(25,34),
(26,34),
(27,34),
(28,34),
(29,34),
(30,34),
(31,34),
(91,34),
(92,34),
(96,34),
(108,34),
(32,35),
(46,35),
(47,35),
(48,35),
(49,35),
(50,35),
(51,35),
(52,35),
(53,35),
(54,35),
(55,35),
(56,35),
(57,35),
(58,35),
(59,35),
(60,35),
(61,35),
(62,35),
(63,35),
(72,35),
(91,35),
(92,35),
(96,35),
(16,36),
(20,36),
(24,36),
(32,36),
(34,36),
(35,36),
(38,36),
(46,36),
(72,36),
(91,36),
(92,36),
(96,36),
(65,37),
(66,37),
(67,37),
(68,37),
(69,37),
(72,37),
(75,37),
(76,37),
(91,37),
(96,37),
(32,38),
(34,38),
(35,38),
(37,38),
(38,38),
(40,38),
(41,38),
(46,38),
(32,39),
(33,39),
(34,39),
(35,39),
(36,39),
(37,39),
(38,39),
(39,39),
(40,39),
(41,39),
(46,39),
(32,40),
(33,40),
(34,40),
(35,40),
(36,40),
(37,40),
(38,40),
(39,40),
(40,40),
(41,40),
(46,40),
(32,41),
(34,41),
(35,41),
(38,41),
(32,42),
(34,42),
(35,42),
(38,42),
(102,42),
(32,43),
(34,43),
(35,43),
(38,43),
(46,44),
(65,44),
(66,44),
(68,44),
(72,44),
(76,44),
(96,44),
(46,45),
(47,45),
(65,45),
(66,45),
(67,45),
(68,45),
(69,45),
(70,45),
(72,45),
(75,45),
(76,45),
(46,46),
(65,46),
(66,46),
(68,46),
(24,47),
(46,47),
(65,47),
(66,47),
(67,47),
(68,47),
(69,47),
(70,47),
(72,47),
(75,47),
(76,47),
(91,47),
(96,47),
(46,48),
(65,48),
(66,48),
(68,48),
(72,48),
(76,48),
(46,49),
(65,49),
(66,49),
(68,49),
(46,50),
(47,50),
(48,50),
(49,50),
(52,50),
(54,50),
(58,50),
(60,50),
(63,50),
(46,51),
(47,51),
(48,51),
(52,51),
(16,52),
(20,52),
(25,52),
(27,52),
(29,52),
(31,52),
(96,53),
(100,53),
(29,54),
(52,54),
(70,54),
(91,54),
(92,54),
(94,54),
(96,54),
(98,54),
(46,55),
(47,55),
(48,55),
(49,55),
(50,55),
(52,55),
(60,55),
(63,55),
(91,55),
(92,55),
(96,55),
(46,56),
(48,56),
(52,56),
(72,56),
(91,56),
(92,56),
(96,56),
(46,57),
(48,57),
(52,57),
(72,57),
(92,57),
(96,57),
(46,58),
(47,58),
(52,58),
(60,58),
(63,58);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission_audit_logs`
--

LOCK TABLES `role_permission_audit_logs` WRITE;
/*!40000 ALTER TABLE `role_permission_audit_logs` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `role_permission_audit_logs` VALUES
(1,'permission_created','Permission',1,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:08'),
(2,'permission_created','Permission',2,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:08'),
(3,'permission_created','Permission',3,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:08'),
(4,'permission_created','Permission',4,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(5,'permission_created','Permission',5,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"assign-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(6,'permission_created','Permission',6,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(7,'permission_created','Permission',7,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(8,'permission_created','Permission',8,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(9,'permission_created','Permission',9,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(10,'permission_created','Permission',10,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(11,'permission_created','Permission',11,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(12,'permission_created','Permission',12,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-settings\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(13,'permission_created','Permission',13,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-audit-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:09'),
(14,'permission_created','Permission',14,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(15,'permission_created','Permission',15,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-system\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(16,'permission_created','Permission',16,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(17,'permission_created','Permission',17,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(18,'permission_created','Permission',18,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(19,'permission_created','Permission',19,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(20,'permission_created','Permission',20,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(21,'permission_created','Permission',21,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(22,'permission_created','Permission',22,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(23,'permission_created','Permission',23,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(24,'permission_created','Permission',24,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-staff-schedule\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(25,'permission_created','Permission',25,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(26,'permission_created','Permission',26,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(27,'permission_created','Permission',27,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:10'),
(28,'permission_created','Permission',28,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(29,'permission_created','Permission',29,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-hr-reports\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(30,'permission_created','Permission',30,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-roles-assignments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(31,'permission_created','Permission',31,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employee-details\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(32,'permission_created','Permission',32,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-queue\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(33,'permission_created','Permission',33,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(34,'permission_created','Permission',34,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"start-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(35,'permission_created','Permission',35,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"complete-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(36,'permission_created','Permission',36,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(37,'permission_created','Permission',37,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(38,'permission_created','Permission',38,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(39,'permission_created','Permission',39,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-reports\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(40,'permission_created','Permission',40,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-quality-control\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(41,'permission_created','Permission',41,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-batch-history\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:11'),
(42,'permission_created','Permission',42,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(43,'permission_created','Permission',43,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"cancel-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(44,'permission_created','Permission',44,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-cost\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(45,'permission_created','Permission',45,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-production-settings\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(46,'permission_created','Permission',46,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(47,'permission_created','Permission',47,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"receive-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(48,'permission_created','Permission',48,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"transfer-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(49,'permission_created','Permission',49,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"adjust-inventory\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(50,'permission_created','Permission',50,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(51,'permission_created','Permission',51,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(52,'permission_created','Permission',52,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-inventory-reports\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:12'),
(53,'permission_created','Permission',53,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:13'),
(54,'permission_created','Permission',54,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:13'),
(55,'permission_created','Permission',55,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:13'),
(56,'permission_created','Permission',56,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:13'),
(57,'permission_created','Permission',57,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:13'),
(58,'permission_created','Permission',58,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-valuation\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:13'),
(59,'permission_created','Permission',59,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-stock-categories\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(60,'permission_created','Permission',60,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(61,'permission_created','Permission',61,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(62,'permission_created','Permission',62,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"write-off-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(63,'permission_created','Permission',63,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-history\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(64,'permission_created','Permission',64,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-inventory-settings\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(65,'permission_created','Permission',65,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-dashboard\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(66,'permission_created','Permission',66,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process-sale\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:14'),
(67,'permission_created','Permission',67,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"issue-refund\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(68,'permission_created','Permission',68,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-daily-sales\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(69,'permission_created','Permission',69,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"close-register\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(70,'permission_created','Permission',70,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-reports\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(71,'permission_created','Permission',71,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-sales-discounts\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(72,'permission_created','Permission',72,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(73,'permission_created','Permission',73,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(74,'permission_created','Permission',74,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"void-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:15'),
(75,'permission_created','Permission',75,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payment-methods\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:16'),
(76,'permission_created','Permission',76,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-till-records\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2025-12-30 04:00:16'),
(77,'permission_created','Permission',77,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-chart-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:16'),
(78,'permission_created','Permission',78,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:16'),
(79,'permission_created','Permission',79,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:16'),
(80,'permission_created','Permission',80,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:16'),
(81,'permission_created','Permission',81,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:16'),
(82,'permission_created','Permission',82,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"post-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:17'),
(83,'permission_created','Permission',83,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reverse-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:17'),
(84,'permission_created','Permission',84,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-accounting-reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:17'),
(85,'permission_created','Permission',85,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:17'),
(86,'permission_created','Permission',86,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-bank-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:17'),
(87,'permission_created','Permission',87,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-trial-balance\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:17'),
(88,'permission_created','Permission',88,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-financial-statements\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:17'),
(89,'permission_created','Permission',89,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-accounting-period\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(90,'permission_created','Permission',90,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-account-reconciliation\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(91,'permission_created','Permission',91,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-analytics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(92,'permission_created','Permission',92,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-department-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(93,'permission_created','Permission',93,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"generate-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(94,'permission_created','Permission',94,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(95,'permission_created','Permission',95,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"schedule-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(96,'permission_created','Permission',96,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-dashboard\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(97,'permission_created','Permission',97,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branch-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(98,'permission_created','Permission',98,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-kpi-metrics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(99,'permission_created','Permission',99,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-data\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:18'),
(100,'permission_created','Permission',100,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-timeline\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(101,'permission_created','Permission',101,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_inventory_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(102,'permission_created','Permission',102,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_production_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(103,'permission_created','Permission',103,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_sales_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(104,'permission_created','Permission',104,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_corner_store_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(105,'permission_created','Permission',105,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_hr_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(106,'permission_created','Permission',106,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(107,'permission_created','Permission',107,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_super_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(108,'permission_created','Permission',108,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_organization\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2025-12-30 04:00:19'),
(109,'permission_created','Permission',109,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_roles\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(110,'permission_created','Permission',110,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_branches\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(111,'permission_created','Permission',111,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_settings\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(112,'permission_created','Permission',112,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_reports\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(113,'permission_created','Permission',113,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"access_accounting\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(114,'permission_created','Permission',114,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_financial_reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(115,'permission_created','Permission',115,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(116,'permission_created','Permission',116,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_periods\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(117,'permission_created','Permission',117,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_journal_entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(118,'permission_created','Permission',118,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile_bank_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2025-12-30 04:00:20'),
(119,'role_created','Role',1,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access with all permissions\"}',0,'2025-12-30 04:00:20'),
(120,'role_created','Role',2,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"MD\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director - Executive level\"}',0,'2025-12-30 04:00:20'),
(121,'role_created','Role',3,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Managing Director\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director with full operational control\"}',0,'2025-12-30 04:00:20'),
(122,'role_created','Role',4,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Administrative access\"}',0,'2025-12-30 04:00:20'),
(123,'role_created','Role',5,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Production\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Head of Production department\"}',0,'2025-12-30 04:00:21'),
(124,'role_created','Role',6,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Manager\"}',0,'2025-12-30 04:00:21'),
(125,'role_created','Role',7,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Human Resources Manager\"}',0,'2025-12-30 04:00:21'),
(126,'role_created','Role',8,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory and Stock Management\"}',0,'2025-12-30 04:00:21'),
(127,'role_created','Role',9,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Team Supervisor\"}',0,'2025-12-30 04:00:22'),
(128,'role_created','Role',10,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Till Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Till\\/Register Supervisor\"}',0,'2025-12-30 04:00:22'),
(129,'role_created','Role',11,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Chef\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Production Chef\"}',0,'2025-12-30 04:00:22'),
(130,'role_created','Role',12,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Gelato\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Lead\"}',0,'2025-12-30 04:00:23'),
(131,'role_created','Role',13,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Manager\"}',0,'2025-12-30 04:00:23'),
(132,'role_created','Role',14,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Kitchen Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Kitchen\\/Production Staff\"}',0,'2025-12-30 04:00:23'),
(133,'role_created','Role',15,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Gelato Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Staff\"}',0,'2025-12-30 04:00:23'),
(134,'role_created','Role',16,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Staff\"}',0,'2025-12-30 04:00:24'),
(135,'role_created','Role',17,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Cashier\"}',0,'2025-12-30 04:00:24'),
(136,'role_created','Role',18,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Manager\"}',0,'2025-12-30 04:00:24'),
(137,'role_created','Role',19,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Staff\"}',0,'2025-12-30 04:00:25'),
(138,'role_created','Role',20,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Department Supervisor\"}',0,'2025-12-30 04:00:25'),
(139,'role_created','Role',21,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Associate\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Associate\"}',0,'2025-12-30 04:00:25'),
(140,'role_created','Role',22,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Junior Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Junior Cashier\"}',0,'2025-12-30 04:00:25'),
(141,'role_created','Role',23,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Stock Controller\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Stock\\/Inventory Controller\"}',0,'2025-12-30 04:00:26'),
(142,'role_created','Role',24,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Keeper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Keeper\\/Warehouse Staff\"}',0,'2025-12-30 04:00:26'),
(143,'role_created','Role',25,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Officer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"HR Officer\"}',0,'2025-12-30 04:00:26'),
(144,'role_created','Role',26,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Employee\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Standard Employee\"}',0,'2025-12-30 04:00:27'),
(145,'role_created','Role',27,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Viewer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Read-only access to reports and dashboards\"}',0,'2025-12-30 04:00:27'),
(146,'role_created','Role',28,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access with all permissions\"}',0,'2025-12-30 04:00:28'),
(147,'role_created','Role',29,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"MD\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director - Executive level\"}',0,'2025-12-30 04:00:28'),
(148,'role_created','Role',30,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Managing Director\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director with full operational control\"}',0,'2025-12-30 04:00:28'),
(149,'role_created','Role',31,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Administrative access\"}',0,'2025-12-30 04:00:28'),
(150,'role_created','Role',32,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Production\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Head of Production department\"}',0,'2025-12-30 04:00:29'),
(151,'role_created','Role',33,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Manager\"}',0,'2025-12-30 04:00:29'),
(152,'role_created','Role',34,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Human Resources Manager\"}',0,'2025-12-30 04:00:29'),
(153,'role_created','Role',35,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory and Stock Management\"}',0,'2025-12-30 04:00:29'),
(154,'role_created','Role',36,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Team Supervisor\"}',0,'2025-12-30 04:00:30'),
(155,'role_created','Role',37,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Till Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Till\\/Register Supervisor\"}',0,'2025-12-30 04:00:30'),
(156,'role_created','Role',38,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Chef\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Production Chef\"}',0,'2025-12-30 04:00:30'),
(157,'role_created','Role',39,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Gelato\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Lead\"}',0,'2025-12-30 04:00:31'),
(158,'role_created','Role',40,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Manager\"}',0,'2025-12-30 04:00:31'),
(159,'role_created','Role',41,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Kitchen Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Kitchen\\/Production Staff\"}',0,'2025-12-30 04:00:31'),
(160,'role_created','Role',42,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Gelato Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Staff\"}',0,'2025-12-30 04:00:32'),
(161,'role_created','Role',43,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Staff\"}',0,'2025-12-30 04:00:32'),
(162,'role_created','Role',44,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Cashier\"}',0,'2025-12-30 04:00:32'),
(163,'role_created','Role',45,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Manager\"}',0,'2025-12-30 04:00:32'),
(164,'role_created','Role',46,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Staff\"}',0,'2025-12-30 04:00:33'),
(165,'role_created','Role',47,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Department Supervisor\"}',0,'2025-12-30 04:00:33'),
(166,'role_created','Role',48,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Associate\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Associate\"}',0,'2025-12-30 04:00:33'),
(167,'role_created','Role',49,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Junior Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Junior Cashier\"}',0,'2025-12-30 04:00:34'),
(168,'role_created','Role',50,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Stock Controller\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Stock\\/Inventory Controller\"}',0,'2025-12-30 04:00:34'),
(169,'role_created','Role',51,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Keeper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Keeper\\/Warehouse Staff\"}',0,'2025-12-30 04:00:34'),
(170,'role_created','Role',52,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Officer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"HR Officer\"}',0,'2025-12-30 04:00:35'),
(171,'role_created','Role',53,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Employee\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Standard Employee\"}',0,'2025-12-30 04:00:35'),
(172,'role_created','Role',54,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Viewer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Read-only access to reports and dashboards\"}',0,'2025-12-30 04:00:35'),
(173,'role_created','Role',55,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Warehouse Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Warehouse Manager with supervisory responsibilities\"}',0,'2025-12-30 04:01:05'),
(174,'role_created','Role',56,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Manager overseeing retail operations\"}',0,'2025-12-30 04:01:05'),
(175,'role_created','Role',57,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Supervisor assisting with daily operations\"}',0,'2025-12-30 04:01:06'),
(176,'role_created','Role',58,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Clerk\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory Clerk for data entry and basic inventory tasks\"}',0,'2025-12-30 04:01:06');
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
  `is_protected` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_is_protected_index` (`is_protected`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles` VALUES
(28,'Super Admin','web',0,'Full system access with all permissions',1,'2025-12-30 04:00:28','2025-12-30 04:00:28'),
(29,'MD','web',0,'Managing Director - Executive level',2,'2025-12-30 04:00:28','2025-12-30 04:00:28'),
(30,'Managing Director','web',0,'Managing Director with full operational control',3,'2025-12-30 04:00:28','2025-12-30 04:00:28'),
(31,'Admin','web',0,'Administrative access',4,'2025-12-30 04:00:28','2025-12-30 04:00:28'),
(32,'Head of Production','web',0,'Head of Production department',10,'2025-12-30 04:00:29','2025-12-30 04:00:29'),
(33,'Sales Manager','web',0,'Sales Manager',11,'2025-12-30 04:00:29','2025-12-30 04:00:29'),
(34,'HR Manager','web',0,'Human Resources Manager',12,'2025-12-30 04:00:29','2025-12-30 04:00:29'),
(35,'Inventory Manager','web',0,'Inventory and Stock Management',13,'2025-12-30 04:00:29','2025-12-30 04:00:29'),
(36,'Supervisor','web',0,'Team Supervisor',20,'2025-12-30 04:00:30','2025-12-30 04:00:30'),
(37,'Till Supervisor','web',0,'Till/Register Supervisor',21,'2025-12-30 04:00:30','2025-12-30 04:00:30'),
(38,'Chef','web',0,'Production Chef',30,'2025-12-30 04:00:30','2025-12-30 04:00:30'),
(39,'Head of Gelato','web',0,'Gelato Production Lead',31,'2025-12-30 04:00:31','2025-12-30 04:00:31'),
(40,'Confectioneries Manager','web',0,'Confectioneries Production Manager',32,'2025-12-30 04:00:31','2025-12-30 04:00:31'),
(41,'Kitchen Staff','web',0,'Kitchen/Production Staff',40,'2025-12-30 04:00:31','2025-12-30 04:00:31'),
(42,'Gelato Production Staff','web',0,'Gelato Production Staff',41,'2025-12-30 04:00:32','2025-12-30 04:00:32'),
(43,'Confectioneries Production Staff','web',0,'Confectioneries Production Staff',42,'2025-12-30 04:00:32','2025-12-30 04:00:32'),
(44,'Cashier','web',0,'Sales Cashier',43,'2025-12-30 04:00:32','2025-12-30 04:00:32'),
(45,'Corner Store Manager','web',0,'Corner Store Manager',44,'2025-12-30 04:00:32','2025-12-30 04:00:32'),
(46,'Corner Store Staff','web',0,'Corner Store Staff',45,'2025-12-30 04:00:33','2025-12-30 04:00:33'),
(47,'Sales Supervisor','web',0,'Sales Department Supervisor',46,'2025-12-30 04:00:33','2025-12-30 04:00:33'),
(48,'Sales Associate','web',0,'Sales Associate',47,'2025-12-30 04:00:33','2025-12-30 04:00:33'),
(49,'Junior Cashier','web',0,'Junior Cashier',48,'2025-12-30 04:00:34','2025-12-30 04:00:34'),
(50,'Stock Controller','web',0,'Stock/Inventory Controller',49,'2025-12-30 04:00:34','2025-12-30 04:00:34'),
(51,'Store Keeper','web',0,'Store Keeper/Warehouse Staff',50,'2025-12-30 04:00:34','2025-12-30 04:00:34'),
(52,'HR Officer','web',0,'HR Officer',51,'2025-12-30 04:00:35','2025-12-30 04:00:35'),
(53,'Employee','web',0,'Standard Employee',52,'2025-12-30 04:00:35','2025-12-30 04:00:35'),
(54,'Viewer','web',0,'Read-only access to reports and dashboards',99,'2025-12-30 04:00:35','2025-12-30 04:00:35'),
(55,'Warehouse Manager','web',0,'Warehouse Manager with supervisory responsibilities',14,'2025-12-30 04:01:05','2025-12-30 04:01:05'),
(56,'Store Manager','web',0,'Store Manager overseeing retail operations',15,'2025-12-30 04:01:05','2025-12-30 04:01:05'),
(57,'Store Supervisor','web',0,'Store Supervisor assisting with daily operations',35,'2025-12-30 04:01:06','2025-12-30 04:01:06'),
(58,'Inventory Clerk','web',0,'Inventory Clerk for data entry and basic inventory tasks',48,'2025-12-30 04:01:06','2025-12-30 04:01:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `shifts` VALUES
(1,'019b6d6a-191b-7068-817b-94e02d09e859',4,'0ce855e9-000d-356f-946a-af16cd4b9db0','SHFT-20260101-0001','2026-01-01','morning','2026-01-01 08:09:42','2026-01-01 08:10:07','closed','clock_in',NULL,'{\"workflow_steps\":{\"stock_opening\":{\"completed_by\":\"0ce855e9-000d-356f-946a-af16cd4b9db0\",\"completed_at\":\"2026-01-01T09:09:54+01:00\"}}}','2026-01-01 08:09:54',NULL,'2026-01-01 08:09:42','2026-01-01 08:10:07',NULL,NULL),
(2,'019b6d6a-191b-7068-817b-94e02d09e859',4,'0ce855e9-000d-356f-946a-af16cd4b9db0','SHFT-20260101-0002','2026-01-01','morning','2026-01-01 08:20:02','2026-01-01 08:20:14','closed','clock_in',NULL,NULL,NULL,NULL,'2026-01-01 08:20:02','2026-01-01 08:20:14',NULL,NULL),
(3,'019b6d6a-191b-7068-817b-94e02d09e859',4,'0ce855e9-000d-356f-946a-af16cd4b9db0','SHFT-20260101-0003','2026-01-01','morning','2026-01-01 08:23:27','2026-01-01 08:23:41','closed','clock_in',NULL,NULL,NULL,NULL,'2026-01-01 08:23:27','2026-01-01 08:23:41',NULL,NULL),
(4,'019b6d6a-191b-7068-817b-94e02d09e859',4,'0ce855e9-000d-356f-946a-af16cd4b9db0','SHFT-20260101-0004','2026-01-01','morning','2026-01-01 08:34:30','2026-01-01 08:34:51','active','shift_closing',NULL,'{\"clock_out_at\":\"2026-01-01T09:34:51+01:00\"}',NULL,NULL,'2026-01-01 08:34:30','2026-01-01 08:34:51',NULL,NULL),
(5,'019b6d6a-191b-7068-817b-94e02d09e859',2,'23b13cd9-6f31-3745-8061-d5f0dd77f130','SHFT-20260101-0005','2026-01-01','afternoon','2026-01-01 14:37:16',NULL,'active','clock_in',NULL,NULL,NULL,NULL,'2026-01-01 14:37:16','2026-01-01 14:37:16',NULL,NULL);
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
(1,'019b6d6a-191b-7068-817b-94e02d09e859',1,186.00,15.00,1.00,312.4000,'2025-12-21','warning','2026-02-28','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(2,'019b6d6a-191b-7068-817b-94e02d09e859',2,68.00,2.00,3.00,443.3000,'2025-12-17','warning','2026-03-19','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(3,'019b6d6a-191b-7068-817b-94e02d09e859',3,199.00,5.00,1.00,105.9000,'2025-12-27','warning','2026-03-06','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(4,'019b6d6a-191b-7068-817b-94e02d09e859',4,71.00,0.00,0.00,137.8000,'2025-12-10','warning','2026-02-17','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(5,'019b6d6a-191b-7068-817b-94e02d09e859',5,320.00,1.00,7.00,210.6000,'2025-12-18','warning','2026-02-25','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(6,'019b6d6a-191b-7068-817b-94e02d09e859',6,131.00,12.00,2.00,233.9000,'2025-12-01','warning','2026-01-30','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(7,'019b6d6a-191b-7068-817b-94e02d09e859',7,140.00,4.00,3.00,399.2000,'2025-12-28','critical','2026-01-23','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(8,'019b6d6a-191b-7068-817b-94e02d09e859',8,135.00,5.00,6.00,214.8000,'2025-12-06','good','2026-07-04','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(9,'019b6d6a-191b-7068-817b-94e02d09e859',9,76.00,0.00,1.00,117.5000,'2025-12-11','good','2026-12-24','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(10,'019b6d6a-191b-7068-817b-94e02d09e859',10,196.00,14.00,0.00,124.0000,'2025-12-04','warning','2026-02-25','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(11,'019b6d6a-191b-7068-817b-94e02d09e859',11,215.00,15.00,4.00,465.1000,'2025-12-16','good','2026-07-21','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(12,'019b6d6a-191b-7068-817b-94e02d09e859',12,284.00,23.00,7.00,483.8000,'2025-12-20','good','2026-04-10','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(13,'019b6d6a-191b-7068-817b-94e02d09e859',13,748.00,21.00,21.00,29.7000,'2025-12-24','good',NULL,'2025-12-30 04:00:56','2025-12-30 04:00:56'),
(14,'019b6d6a-191b-7068-817b-94e02d09e859',14,332.00,3.00,10.00,46.8000,'2025-12-25','critical',NULL,'2025-12-30 04:00:56','2025-12-30 04:00:56'),
(15,'019b6d6a-191b-7068-817b-94e02d09e859',15,654.00,19.00,17.00,35.3000,'2025-12-24','warning',NULL,'2025-12-30 04:00:56','2025-12-30 04:00:56'),
(16,'019b6d6a-191b-7068-817b-94e02d09e859',16,253.00,4.00,0.00,20.9000,'2025-12-01','good',NULL,'2025-12-30 04:00:56','2025-12-30 04:00:56'),
(17,'019b6d6a-191b-7068-817b-94e02d09e859',17,135.00,8.00,4.00,133.7000,'2025-12-02','good','2026-05-24','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(18,'019b6d6a-191b-7068-817b-94e02d09e859',18,28.00,1.00,0.00,290.3000,'2025-12-05','good','2026-11-20','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(19,'019b6d6a-191b-7068-817b-94e02d09e859',19,49.00,4.00,1.00,46.0000,'2025-12-27','good','2026-11-24','2025-12-30 04:00:56','2025-12-30 04:00:56'),
(20,'019b6d6a-191b-7068-817b-94e02d09e859',20,14.00,0.00,0.00,1589.2000,'2025-12-24','warning',NULL,'2025-12-30 04:00:56','2025-12-30 04:00:56'),
(21,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',21,253.00,17.00,10.00,339.5000,'2025-12-04','warning','2026-02-12','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(22,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',22,355.00,24.00,9.00,329.9000,'2025-12-20','good','2026-07-14','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(23,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',23,199.00,18.00,8.00,203.1000,'2025-12-02','good','2026-08-15','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(24,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',24,151.00,2.00,4.00,263.9000,'2025-12-19','critical','2026-01-20','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(25,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',25,309.00,26.00,2.00,60.2000,'2025-12-29','critical','2026-01-18','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(26,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',26,218.00,8.00,6.00,302.3000,'2025-12-22','good','2026-09-25','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(27,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',27,372.00,1.00,0.00,347.4000,'2025-12-01','critical','2026-01-09','2025-12-30 04:00:57','2025-12-30 04:00:57'),
(28,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',28,129.00,3.00,4.00,401.3000,'2025-12-24','good','2026-04-21','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(29,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',29,280.00,17.00,14.00,423.0000,'2025-12-03','good','2026-11-26','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(30,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',30,92.00,2.00,0.00,399.9000,'2025-12-04','good','2026-09-23','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(31,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',31,362.00,14.00,16.00,324.1000,'2025-12-10','critical','2026-01-17','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(32,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',32,315.00,26.00,10.00,141.9000,'2025-12-20','good','2026-05-04','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(33,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',33,701.00,67.00,12.00,39.4000,'2025-12-22','good',NULL,'2025-12-30 04:00:58','2025-12-30 04:00:58'),
(34,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',34,787.00,46.00,15.00,44.3000,'2025-12-16','good',NULL,'2025-12-30 04:00:58','2025-12-30 04:00:58'),
(35,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',35,367.00,4.00,14.00,8.4000,'2025-12-02','good',NULL,'2025-12-30 04:00:58','2025-12-30 04:00:58'),
(36,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',36,385.00,29.00,2.00,24.4000,'2025-12-05','good',NULL,'2025-12-30 04:00:58','2025-12-30 04:00:58'),
(37,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',37,114.00,9.00,5.00,292.8000,'2025-12-18','good','2026-06-03','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(38,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',38,80.00,4.00,1.00,217.8000,'2025-12-10','critical','2026-01-22','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(39,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',39,135.00,5.00,4.00,181.9000,'2025-12-27','good','2026-10-31','2025-12-30 04:00:58','2025-12-30 04:00:58'),
(40,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',40,21.00,2.00,0.00,1010.4000,'2025-12-23','good',NULL,'2025-12-30 04:00:58','2025-12-30 04:00:58'),
(41,'019b6d6a-192d-705f-8b98-4e27aaf972b5',41,387.00,8.00,12.00,434.7000,'2025-12-28','warning','2026-02-11','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(42,'019b6d6a-192d-705f-8b98-4e27aaf972b5',42,139.00,10.00,0.00,293.5000,'2025-12-18','good','2026-10-18','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(43,'019b6d6a-192d-705f-8b98-4e27aaf972b5',43,211.00,2.00,3.00,382.1000,'2025-12-05','critical','2026-01-14','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(44,'019b6d6a-192d-705f-8b98-4e27aaf972b5',44,232.00,20.00,1.00,448.7000,'2025-12-16','good','2026-10-08','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(45,'019b6d6a-192d-705f-8b98-4e27aaf972b5',45,309.00,22.00,1.00,190.8000,'2025-12-25','good','2026-10-22','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(46,'019b6d6a-192d-705f-8b98-4e27aaf972b5',46,348.00,10.00,0.00,237.9000,'2025-12-04','critical','2026-01-06','2025-12-30 04:00:59','2025-12-30 04:00:59'),
(47,'019b6d6a-192d-705f-8b98-4e27aaf972b5',47,121.00,6.00,1.00,496.4000,'2025-12-25','good','2026-09-08','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(48,'019b6d6a-192d-705f-8b98-4e27aaf972b5',48,275.00,9.00,3.00,379.0000,'2025-12-07','warning','2026-02-27','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(49,'019b6d6a-192d-705f-8b98-4e27aaf972b5',49,94.00,8.00,4.00,138.4000,'2025-12-26','critical','2026-01-25','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(50,'019b6d6a-192d-705f-8b98-4e27aaf972b5',50,184.00,1.00,1.00,204.6000,'2025-12-07','good','2026-11-28','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(51,'019b6d6a-192d-705f-8b98-4e27aaf972b5',51,84.00,2.00,3.00,171.1000,'2025-12-02','good','2026-08-01','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(52,'019b6d6a-192d-705f-8b98-4e27aaf972b5',52,359.00,32.00,16.00,389.4000,'2025-12-21','good','2026-06-20','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(53,'019b6d6a-192d-705f-8b98-4e27aaf972b5',53,422.00,17.00,1.00,39.5000,'2025-12-22','good',NULL,'2025-12-30 04:01:00','2025-12-30 04:01:00'),
(54,'019b6d6a-192d-705f-8b98-4e27aaf972b5',54,538.00,42.00,3.00,34.6000,'2025-12-11','good',NULL,'2025-12-30 04:01:00','2025-12-30 04:01:00'),
(55,'019b6d6a-192d-705f-8b98-4e27aaf972b5',55,125.00,5.00,5.00,30.1000,'2025-12-26','warning',NULL,'2025-12-30 04:01:00','2025-12-30 04:01:00'),
(56,'019b6d6a-192d-705f-8b98-4e27aaf972b5',56,425.00,31.00,17.00,12.4000,'2025-12-15','good',NULL,'2025-12-30 04:01:00','2025-12-30 04:01:00'),
(57,'019b6d6a-192d-705f-8b98-4e27aaf972b5',57,87.00,7.00,1.00,57.5000,'2025-12-04','good','2026-04-08','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(58,'019b6d6a-192d-705f-8b98-4e27aaf972b5',58,132.00,4.00,6.00,285.7000,'2025-12-04','good','2026-10-23','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(59,'019b6d6a-192d-705f-8b98-4e27aaf972b5',59,150.00,4.00,6.00,187.6000,'2025-12-14','good','2026-04-17','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(60,'019b6d6a-192d-705f-8b98-4e27aaf972b5',60,18.00,0.00,0.00,1026.7000,'2025-12-29','good',NULL,'2025-12-30 04:01:00','2025-12-30 04:01:00'),
(61,'019b6d6a-1944-725f-9136-b5c40cd21ca0',61,335.00,2.00,8.00,380.2000,'2025-12-03','good','2026-06-15','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(62,'019b6d6a-1944-725f-9136-b5c40cd21ca0',62,288.00,20.00,10.00,399.0000,'2025-12-02','good','2026-09-13','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(63,'019b6d6a-1944-725f-9136-b5c40cd21ca0',63,132.00,13.00,1.00,383.3000,'2025-12-24','good','2026-11-03','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(64,'019b6d6a-1944-725f-9136-b5c40cd21ca0',64,225.00,5.00,11.00,336.4000,'2025-12-16','warning','2026-02-27','2025-12-30 04:01:00','2025-12-30 04:01:00'),
(65,'019b6d6a-1944-725f-9136-b5c40cd21ca0',65,299.00,10.00,6.00,146.1000,'2025-12-21','critical','2026-01-08','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(66,'019b6d6a-1944-725f-9136-b5c40cd21ca0',66,249.00,19.00,9.00,383.5000,'2025-12-29','good','2026-12-22','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(67,'019b6d6a-1944-725f-9136-b5c40cd21ca0',67,98.00,4.00,3.00,490.2000,'2025-12-15','critical','2026-01-27','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(68,'019b6d6a-1944-725f-9136-b5c40cd21ca0',68,184.00,16.00,6.00,262.6000,'2025-12-22','good','2026-11-10','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(69,'019b6d6a-1944-725f-9136-b5c40cd21ca0',69,89.00,2.00,3.00,458.3000,'2025-12-16','good','2026-04-19','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(70,'019b6d6a-1944-725f-9136-b5c40cd21ca0',70,327.00,9.00,6.00,56.5000,'2025-12-28','good','2026-10-26','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(71,'019b6d6a-1944-725f-9136-b5c40cd21ca0',71,153.00,14.00,0.00,339.0000,'2025-12-01','good','2026-06-02','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(72,'019b6d6a-1944-725f-9136-b5c40cd21ca0',72,159.00,14.00,6.00,104.9000,'2025-12-06','good','2026-12-26','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(73,'019b6d6a-1944-725f-9136-b5c40cd21ca0',73,169.00,1.00,7.00,16.3000,'2025-12-10','critical',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(74,'019b6d6a-1944-725f-9136-b5c40cd21ca0',74,416.00,37.00,5.00,29.0000,'2025-11-30','good',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(75,'019b6d6a-1944-725f-9136-b5c40cd21ca0',75,414.00,20.00,3.00,46.9000,'2025-12-24','warning',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(76,'019b6d6a-1944-725f-9136-b5c40cd21ca0',76,314.00,20.00,5.00,26.2000,'2025-12-18','critical',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(77,'019b6d6a-1944-725f-9136-b5c40cd21ca0',77,127.00,4.00,2.00,35.2000,'2025-11-30','good','2026-12-17','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(78,'019b6d6a-1944-725f-9136-b5c40cd21ca0',78,45.00,4.00,2.00,79.8000,'2025-12-14','good','2026-09-24','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(79,'019b6d6a-1944-725f-9136-b5c40cd21ca0',79,129.00,9.00,6.00,234.7000,'2025-12-10','good','2026-12-12','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(80,'019b6d6a-1944-725f-9136-b5c40cd21ca0',80,14.00,0.00,0.00,1588.7000,'2025-12-23','good',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(81,'019b6d6a-194f-71bf-a740-9f45370d3348',81,325.00,5.00,15.00,61.5000,'2025-12-15','good','2026-04-14','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(82,'019b6d6a-194f-71bf-a740-9f45370d3348',82,247.00,22.00,0.00,89.1000,'2025-12-16','critical','2026-01-08','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(83,'019b6d6a-194f-71bf-a740-9f45370d3348',83,56.00,0.00,1.00,377.3000,'2025-12-07','warning','2026-02-21','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(84,'019b6d6a-194f-71bf-a740-9f45370d3348',84,129.00,0.00,2.00,103.0000,'2025-12-04','good','2026-06-24','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(85,'019b6d6a-194f-71bf-a740-9f45370d3348',85,277.00,25.00,13.00,69.5000,'2025-12-19','good','2026-10-11','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(86,'019b6d6a-194f-71bf-a740-9f45370d3348',86,330.00,19.00,10.00,231.5000,'2025-12-02','good','2026-06-10','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(87,'019b6d6a-194f-71bf-a740-9f45370d3348',87,51.00,5.00,0.00,355.3000,'2025-12-14','good','2026-08-17','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(88,'019b6d6a-194f-71bf-a740-9f45370d3348',88,322.00,18.00,14.00,265.9000,'2025-12-17','critical','2026-01-21','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(89,'019b6d6a-194f-71bf-a740-9f45370d3348',89,273.00,6.00,7.00,235.0000,'2025-12-25','good','2026-09-14','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(90,'019b6d6a-194f-71bf-a740-9f45370d3348',90,196.00,1.00,7.00,77.2000,'2025-11-30','good','2026-04-09','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(91,'019b6d6a-194f-71bf-a740-9f45370d3348',91,227.00,12.00,7.00,341.3000,'2025-12-13','good','2026-10-03','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(92,'019b6d6a-194f-71bf-a740-9f45370d3348',92,105.00,5.00,1.00,499.0000,'2025-12-05','critical','2026-01-12','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(93,'019b6d6a-194f-71bf-a740-9f45370d3348',93,143.00,4.00,0.00,43.8000,'2025-12-14','good',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(94,'019b6d6a-194f-71bf-a740-9f45370d3348',94,122.00,0.00,6.00,6.6000,'2025-12-13','critical',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(95,'019b6d6a-194f-71bf-a740-9f45370d3348',95,739.00,17.00,34.00,29.7000,'2025-12-10','good',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(96,'019b6d6a-194f-71bf-a740-9f45370d3348',96,399.00,4.00,9.00,17.8000,'2025-12-14','warning',NULL,'2025-12-30 04:01:01','2025-12-30 04:01:01'),
(97,'019b6d6a-194f-71bf-a740-9f45370d3348',97,95.00,0.00,3.00,161.7000,'2025-12-18','good','2026-08-19','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(98,'019b6d6a-194f-71bf-a740-9f45370d3348',98,107.00,6.00,1.00,36.8000,'2025-12-10','good','2026-10-06','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(99,'019b6d6a-194f-71bf-a740-9f45370d3348',99,125.00,8.00,5.00,204.4000,'2025-12-07','warning','2026-02-18','2025-12-30 04:01:01','2025-12-30 04:01:01'),
(100,'019b6d6a-194f-71bf-a740-9f45370d3348',100,22.00,2.00,0.00,1979.2000,'2025-12-26','good',NULL,'2025-12-30 04:01:02','2025-12-30 04:01:02');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tables`
--

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `tables` VALUES
(1,'019b6d6a-191b-7068-817b-94e02d09e859',4,'1','Table 1','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14'),
(2,'019b6d6a-191b-7068-817b-94e02d09e859',4,'2','Table 2','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14'),
(3,'019b6d6a-191b-7068-817b-94e02d09e859',4,'3','Table 3','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14'),
(4,'019b6d6a-191b-7068-817b-94e02d09e859',4,'4','Table 4','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14'),
(5,'019b6d6a-191b-7068-817b-94e02d09e859',4,'5','Table 5','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14'),
(6,'019b6d6a-191b-7068-817b-94e02d09e859',4,'6','Table 6','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14'),
(7,'019b6d6a-191b-7068-817b-94e02d09e859',4,'7','Table 7','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14'),
(8,'019b6d6a-191b-7068-817b-94e02d09e859',4,'8','Table 8','available',4,1,NULL,'2026-01-01 09:27:14','2026-01-01 09:27:14');
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
(1,'mg','Milligrams','mg','weight','Milligram',1,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(2,'g','Grams','g','weight','Gram',2,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(3,'kg','Kilograms','kg','weight','Kilogram',3,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(4,'oz','Ounces','oz','weight','Ounce (avoirdupois)',4,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(5,'lb','Pounds','lb','weight','Pound',5,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(6,'ml','Milliliters','ml','volume','Milliliter',6,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(7,'l','Liters','L','volume','Liter',7,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(8,'cl','Centiliters','cl','volume','Centiliter',8,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(9,'fl_oz','Fluid Ounces','fl oz','volume','Fluid Ounce (US)',9,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(10,'cup','Cups','cup','volume','Cup (US)',10,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(11,'tbsp','Tablespoons','tbsp','volume','Tablespoon (US)',11,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(12,'tsp','Teaspoons','tsp','volume','Teaspoon (US)',12,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(13,'pcs','Pieces','pcs','count','Piece',13,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(14,'unit','Units','unit','count','Unit',14,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(15,'dz','Dozens','dz','count','Dozen (12 units)',15,1,'2026-01-01 08:59:36','2026-01-01 08:59:36'),
(16,'mm','Millimeters','mm','length','Millimeter',16,1,'2026-01-01 08:59:37','2026-01-01 08:59:37'),
(17,'cm','Centimeters','cm','length','Centimeter',17,1,'2026-01-01 08:59:37','2026-01-01 08:59:37'),
(18,'m','Meters','m','length','Meter',18,1,'2026-01-01 08:59:37','2026-01-01 08:59:37'),
(19,'in','Inches','in','length','Inch',19,1,'2026-01-01 08:59:37','2026-01-01 08:59:37');
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
  KEY `users_department_id_foreign` (`department_id`),
  KEY `users_manager_id_foreign` (`manager_id`),
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
('01714abf-7822-351f-be69-4581a3545274',NULL,'Obinna Okafor','obinna.okafor.39@sweettooth.com','EMP-CAL001-0039','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,5,NULL,'+234-818-972-5454','499 Treutel Flats, Calabar, Cross River State, Nigeria','1983-04-25','male','Nigerian','Hauwa Eze','+234-884-506-9561','2023-01-31 23:00:00','active',NULL,NULL,'afternoon',173117.72,NULL,'TIN-53791139','5923873281',NULL,NULL,'2025-08-05',3.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('019b6d6a-18e7-70e0-9aa0-5d56ffd08444','019b6d6a-191b-7068-817b-94e02d09e859','Super Admin','admin@sweettooth.local',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$/jjbmdU0YMlMtsJtaNMwPulkEQg9u/6WR9mu6UVFRF87kvYaDeuKq',NULL,NULL,NULL,NULL,'Ha9UyGoakJLMX7zADFRKuPQubSVSASLsXQxt9wfj2uS8LbJT8GOFOPVligqF','2025-12-30 04:00:28','2026-01-02 10:01:39'),
('019b6d6a-3ae1-7073-9f6f-0db27de3c1df',NULL,'Prof. Gonzalo Wisoky','destin.nienow@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','ZBDluQKPow','wfJBI7WjL3','2025-12-30 04:00:37','2025-12-30 04:00:36','lT4qxTk6b9','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3b70-71f3-bb0a-30407024355d',NULL,'Eldred Kohler DVM','eugene.metz@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','zpYKM6kuH1','JhALn0czbv','2025-12-30 04:00:37','2025-12-30 04:00:36','i8x1VzJ9ec','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3b87-7088-ab47-cc99a111c482',NULL,'Janet Wilkinson','stracke.malvina@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','KoTPLLz3iV','6MwQYpebjv','2025-12-30 04:00:37','2025-12-30 04:00:36','yMDOjZsMHa','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3b92-708f-9bb5-42a7892c7c9d',NULL,'Petra Ritchie','bernie88@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','owdT0zp103','RSt56ZJXsi','2025-12-30 04:00:37','2025-12-30 04:00:36','ig5moJhYTn','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3b9e-7234-b1cc-63aa6dcc2b8c',NULL,'Orion O\'Reilly','vern94@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','ZK2CPjgcUd','AqV6w1XHJf','2025-12-30 04:00:37','2025-12-30 04:00:36','271fbiyUX6','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3baa-706e-9d6b-03e332ab79c7',NULL,'Kassandra Blanda','inienow@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','N6aBPcf50a','eFl0Yz7rzU','2025-12-30 04:00:37','2025-12-30 04:00:36','YEPVcY8o47','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3bbf-717d-84e6-fd266f0cb484',NULL,'Ashton Kohler','mante.lilla@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','N1gM9I6yMb','ag0hMo5HsG','2025-12-30 04:00:37','2025-12-30 04:00:36','USkMdDUNas','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3bca-72fc-b826-ae61cabb109a',NULL,'Myles Swaniawski MD','davin55@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','fPk1U5pZVK','XgDTyxYmBJ','2025-12-30 04:00:37','2025-12-30 04:00:36','VIYJ28Tgft','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3bd5-71d9-b85e-ef830dab29cc',NULL,'Mrs. Carolyn Treutel','cortney.hettinger@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','fmIxPsBrOj','tqlBqDXppT','2025-12-30 04:00:37','2025-12-30 04:00:36','GcvTLlkIiU','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3be0-7126-b610-2e2dec239b9a',NULL,'Jamar Jacobi','gianni03@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','0Sv8rnmw0d','XV1lVnaaMh','2025-12-30 04:00:37','2025-12-30 04:00:36','CHzCQTCcc6','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3bec-7386-a20a-8ad435e867b2',NULL,'Ebba Bogan','gaylord.mireya@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','FD8FcLBE6P','FwNtvVWcVi','2025-12-30 04:00:37','2025-12-30 04:00:36','fhHLjdIcGR','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3bf8-7181-8370-a7311d24b3e4',NULL,'Hugh D\'Amore','whackett@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','mxfASx2xgr','JZ9eBCZXiD','2025-12-30 04:00:37','2025-12-30 04:00:36','6YPbnpZHRk','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c03-73fa-b8b0-e9af98b8dde4',NULL,'Loyce Ferry','martine.brekke@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','CtSAakEXmS','OwmjqRjkg4','2025-12-30 04:00:37','2025-12-30 04:00:36','RY26WHK2d3','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c0e-737f-abfc-deba0e247b16',NULL,'Isabell Hill','armstrong.gene@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','jlP8jPitQZ','UcTLjeGMMc','2025-12-30 04:00:37','2025-12-30 04:00:36','PYUB1RG98j','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c18-71c2-ad0b-ac8580d613da',NULL,'Mr. Hoyt Oberbrunner V','elta.heaney@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','jiPp5DETgx','z7JGNeNfbc','2025-12-30 04:00:37','2025-12-30 04:00:36','IWz0DJrZLl','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c23-70b6-877f-9c40a62d3e95',NULL,'Ms. Melyssa Medhurst Sr.','terence64@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','IOaJtIm0tb','CE3DvEZoW6','2025-12-30 04:00:37','2025-12-30 04:00:36','fPeQlniMVS','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c2f-70ab-a7a4-1afeff84a760',NULL,'Annalise Conn','akuphal@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','lVZTSr91IE','u9GzTJpjJd','2025-12-30 04:00:37','2025-12-30 04:00:36','klwmcEyqB5','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c46-7075-90cb-c55958b3c0c3',NULL,'Prof. Holly Swift','maynard19@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','HEzXgAHDiR','DpjENquJeA','2025-12-30 04:00:37','2025-12-30 04:00:36','7povIllVw8','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c50-70d7-aed0-723f241fb01a',NULL,'Evie Sanford Sr.','thurman.predovic@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','1QP3EN2vsz','GPuvitzXI8','2025-12-30 04:00:37','2025-12-30 04:00:36','SEUET1fUaU','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c5c-73ff-ab34-7e2b86c837d2',NULL,'Gina Stroman III','madelynn.wolf@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','Td8M7ogooc','Bmfi4t03vZ','2025-12-30 04:00:37','2025-12-30 04:00:36','RjCrLaWcbp','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c67-7214-b545-cc29fca3e869',NULL,'Miss Ivory Ferry Sr.','pcrist@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','qI9UtPxRIn','5ZqmFO8FRp','2025-12-30 04:00:37','2025-12-30 04:00:36','uCWTA3EoXU','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c72-7235-8be4-f758f54ba077',NULL,'Ebba Ryan','gunnar.yundt@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','F0301cMUQq','UjAu4hLNb0','2025-12-30 04:00:37','2025-12-30 04:00:36','KrLF9YnJwO','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c7c-71ec-814f-fa7683633e7b',NULL,'Kristian Medhurst','ilangosh@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','wygmLOo4UY','S9yzTmGbgr','2025-12-30 04:00:37','2025-12-30 04:00:36','rvAQhCsj4U','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c88-724d-9b1c-7118590d82da',NULL,'Estelle Hoeger V','haley.monserrat@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','VWShZA4ICZ','3CFoZwphH8','2025-12-30 04:00:37','2025-12-30 04:00:36','5RK7c1JOgT','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c92-73f7-b0b2-47a989c684c8',NULL,'Murphy Monahan Sr.','pschulist@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','LwiFonoC4f','T4zwre0gMJ','2025-12-30 04:00:37','2025-12-30 04:00:36','G2HriWICSw','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3c9d-7230-ab2d-59ab73f67468',NULL,'Denis Lind','jedediah33@example.net',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','LzNkEVSSus','uSfSv2FCNR','2025-12-30 04:00:37','2025-12-30 04:00:36','Bmyjwn4NiI','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3ca8-7176-883b-4a98eb930dd8',NULL,'Merlin Smitham Jr.','tyree.tremblay@example.com',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','KyGiJW71Mx','u3KXtf4lAS','2025-12-30 04:00:37','2025-12-30 04:00:36','6AxtUtHjj8','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3cb3-7377-802d-b838b2feede1',NULL,'Dr. Zachery Kunze I','candice58@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','U1ISPp0axg','ugIvJTfQYs','2025-12-30 04:00:37','2025-12-30 04:00:36','ym4FN7Goa3','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3cbf-7152-ac2d-c726b107530f',NULL,'Camille Leuschke','gdenesik@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','bRWsTh3VXr','5r8506umwD','2025-12-30 04:00:37','2025-12-30 04:00:36','zcHcxNWxBa','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-3cca-7250-ac2f-86034809e8cb',NULL,'Shyann Adams','hane.georgette@example.org',NULL,'019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$VMFcs9JCDU.mcWnYUxbc1ug3bDTfHK3lJhZhnFfUKOJdA7/arvrdC','XKxzZzbBmR','I4BKJSyr2O','2025-12-30 04:00:37','2025-12-30 04:00:36','ZrKFU0F9kU','2025-12-30 04:00:37','2025-12-30 04:00:37'),
('019b6d6a-41c5-7264-91e5-54379c8c981b',NULL,'Stephan Denesik','hegmann.marco@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','JRz9vMYS4k','fVCEd9Arh1','2025-12-30 04:00:38','2025-12-30 04:00:38','plLvCzm1bN','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-41de-72fe-84af-f0d274317d47',NULL,'Joanny Price','mclaughlin.hiram@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','VOYOonNIDg','xAXXV5KUo3','2025-12-30 04:00:38','2025-12-30 04:00:38','pwM8fFwmU0','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-41e9-73dc-817d-14465416360c',NULL,'Constance Ward','kyleigh46@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','wsNrClXc0s','k8PMmekwdj','2025-12-30 04:00:38','2025-12-30 04:00:38','Uj3k2ERpyT','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-41f4-7277-833b-d7130a12cd66',NULL,'Chelsey Beatty','kenneth.daugherty@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','Yksgcx39Cs','9lOoA7vR99','2025-12-30 04:00:38','2025-12-30 04:00:38','duFGeSpQGJ','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-41ff-7199-8653-0a0ac8446b41',NULL,'Prof. Asa Haag','selina42@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','5G8KwQylma','PqeMhyn15q','2025-12-30 04:00:38','2025-12-30 04:00:38','vMiutwjwDA','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-420e-72c8-ba6d-6989bfac9b9c',NULL,'Prof. Rita Yost','tressie70@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','firhdhviwL','vnhRmzDhr4','2025-12-30 04:00:38','2025-12-30 04:00:38','EloAYWYtBf','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-4215-71c3-acd3-0b59a81a1ab9',NULL,'Justyn Mante','dbarton@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','e6zFhWkeSC','TzvR2meocw','2025-12-30 04:00:38','2025-12-30 04:00:38','i4JVtHI1Qq','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-4220-700b-9202-cad01f109b6b',NULL,'Lela Crooks','mmacejkovic@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','rFICYLwRfB','HjWrrZ1Izp','2025-12-30 04:00:38','2025-12-30 04:00:38','B7rpu2tAph','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-422c-7040-9e73-b110f50991a2',NULL,'Dr. Merlin Steuber','larkin.vicente@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','KArBSFyJVy','dWlhjhw5sk','2025-12-30 04:00:38','2025-12-30 04:00:38','Qhckl1Jm6x','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-4236-7298-bde0-18c32c86ce13',NULL,'Prof. Jailyn Schmidt','turcotte.amalia@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','rnixRZjprZ','ny4oLAX222','2025-12-30 04:00:38','2025-12-30 04:00:38','qSnfvNHxTc','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-4242-73fc-9766-1b45deb1fb9f',NULL,'Robert Kris','gregg.huel@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','BiNaUHKhri','DH1cuH1pOb','2025-12-30 04:00:38','2025-12-30 04:00:38','oV0OgHSVsq','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-424d-73af-b3e9-956b068f3d9d',NULL,'Mrs. Beatrice Herzog','hbrown@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','w1IRdEUvhq','gtSMxHEBVe','2025-12-30 04:00:38','2025-12-30 04:00:38','6EAlWGZsds','2025-12-30 04:00:38','2025-12-30 04:00:38'),
('019b6d6a-4258-70a4-982b-db6b3abad934',NULL,'Prof. Crawford Daniel','arne.koss@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','UkUEqAAIEI','CJA03eAXLi','2025-12-30 04:00:38','2025-12-30 04:00:38','fX3z3Rnzpj','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-4263-7330-9d00-2227da28efd5',NULL,'Fae Feest Jr.','howell.giovanny@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','95WpMA5HsU','6M418ZX7dn','2025-12-30 04:00:38','2025-12-30 04:00:38','TWlMm12g3j','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-426e-7030-b752-1af9a6a638b2',NULL,'Ceasar Tremblay III','wilhelmine48@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','0PTAEka1SG','fLraPaHBEb','2025-12-30 04:00:38','2025-12-30 04:00:38','Vzu6f6nOnP','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-4279-7040-a4a6-f981589352ca',NULL,'Haskell Witting','orin.hyatt@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','aCVUdkoybX','1BHlMLQecA','2025-12-30 04:00:38','2025-12-30 04:00:38','RZ0qXvbpZ4','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-4284-7276-b4c3-70cd932dc5ce',NULL,'Miss Joanne Hermiston','gnitzsche@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','xNyOA66Cta','0j6i3ukLBA','2025-12-30 04:00:38','2025-12-30 04:00:38','l4uvW7yuYe','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-428f-700a-9508-d27179ff7f37',NULL,'Margie Bechtelar','jwhite@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','dAI866x4u7','ajXo2lQTl6','2025-12-30 04:00:38','2025-12-30 04:00:38','YiUmWDbFum','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-429b-72e0-8e50-f31caa34d39b',NULL,'Prof. Pinkie Homenick','holly33@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','M5eqYXtZRU','rbdIksz77d','2025-12-30 04:00:38','2025-12-30 04:00:38','NIEOmBbmkO','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42a6-7339-965d-2550f18b776a',NULL,'Theodore Herman MD','hjast@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','pCIV5cpJ1h','69nfFf7sJ5','2025-12-30 04:00:38','2025-12-30 04:00:38','GHEIPLVj5i','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42b1-706f-9179-7b6345af82fa',NULL,'Dr. Winston Wolff III','ppouros@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','G2ZVMZi6rs','SuIIVvStD4','2025-12-30 04:00:38','2025-12-30 04:00:38','KAs04td6eT','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42bc-70d1-a112-e0fe3b903fd4',NULL,'Haleigh Kertzmann','rodriguez.hector@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','H8aRDBRAnh','rA9yh2UCrw','2025-12-30 04:00:38','2025-12-30 04:00:38','mbY8sRxRXZ','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42c7-707e-8cf5-c6fe54f0dc63',NULL,'Prof. Mathias Torp','elisha77@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','VwcIPLWR9j','ltHb0vVMMB','2025-12-30 04:00:38','2025-12-30 04:00:38','nL0UBoEKZP','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42d4-70ad-841f-f11c465d3d71',NULL,'Miss Ashleigh Reichel','mclaughlin.charlie@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','j5VY6Tca85','og2hROUooL','2025-12-30 04:00:38','2025-12-30 04:00:38','9XmYp86mI0','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42de-7217-b42e-3014a39e63f1',NULL,'Misael Pfeffer III','zokuneva@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','CJzmuI3rNZ','JIp8VoonuP','2025-12-30 04:00:38','2025-12-30 04:00:38','M8Xbs0LjhY','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42e8-715a-8612-96c9e63563c1',NULL,'April Bailey','arolfson@example.com',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','RwvuV0oLlM','xPl1ul9hbu','2025-12-30 04:00:38','2025-12-30 04:00:38','7CC2imKikR','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-42f4-72c7-abd8-743dfe85a5d3',NULL,'Blaze Jacobs','moberbrunner@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','w2DPYjjVsj','nH67nmxiHW','2025-12-30 04:00:38','2025-12-30 04:00:38','9dZHYP303c','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-4300-72a5-b50c-661119bea32d',NULL,'Gay Davis','elmer31@example.org',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','7M4kmW5rkO','YavtovoQ3V','2025-12-30 04:00:38','2025-12-30 04:00:38','3TPCw2q277','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-430a-73b1-acf0-5312f326a620',NULL,'Lavina Upton','vicky47@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','omqGgMpiGg','tO6dXHCiwr','2025-12-30 04:00:38','2025-12-30 04:00:38','htD0u2ABvj','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-4315-70e8-8c2a-36d97bb20ccc',NULL,'Mr. Hershel Keeling','rasheed.rodriguez@example.net',NULL,'019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$rg/GztgBCkv0F.Vs/4FatuBuMFKhqNR0AcJ03ENdNbrk3g3SZAh96','cIHU4oeFJ1','EtEpzojUtR','2025-12-30 04:00:38','2025-12-30 04:00:38','M1NDUG8fWp','2025-12-30 04:00:39','2025-12-30 04:00:39'),
('019b6d6a-4783-72c3-887a-e2f4ece51fe0',NULL,'Gerda Cummerata','lwaelchi@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','XyLu3Wkw03','ZdN7vwgjwy','2025-12-30 04:00:40','2025-12-30 04:00:40','bukWt8CiJd','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4799-7138-9f80-6c37ed306886',NULL,'Alanis Kerluke','napoleon81@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','aUCQ7nb9UN','fYK0jcWnrp','2025-12-30 04:00:40','2025-12-30 04:00:40','FHFJ6RBGXL','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-47a4-70b2-9870-d67bfba64940',NULL,'Mr. Trace Rutherford','junius.bartoletti@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','0DHNb8DnG2','vww0F1NXgW','2025-12-30 04:00:40','2025-12-30 04:00:40','2LmeBljfZn','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-47af-734e-8b18-f7bf61be4435',NULL,'Jayda Lindgren Sr.','xschowalter@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','pR3okCWie9','UTnQhsMkik','2025-12-30 04:00:40','2025-12-30 04:00:40','fqOKXNX11V','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-47bb-70e8-8913-ceb8d47dc521',NULL,'Mrs. Shanon VonRueden','javier36@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','n4yrvuEupS','MukyHUbvnj','2025-12-30 04:00:40','2025-12-30 04:00:40','b8m9L0AfDR','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-47c5-70b9-a6bc-d740920e0f70',NULL,'Miss Ardella Ward IV','brolfson@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','899HI57kbV','spSiJfPuF7','2025-12-30 04:00:40','2025-12-30 04:00:40','ts6WczYfWb','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-483d-70fc-8111-d95fa21d4053',NULL,'Magdalena Walker Sr.','kemmer.durward@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','Ivw0TTgLxy','YrOYcXxmBy','2025-12-30 04:00:40','2025-12-30 04:00:40','JzzQyzVfDs','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-484b-700f-8dfc-75e2f886a501',NULL,'Tabitha Friesen','gilberto99@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','es7T7vunma','SeoZQQxH5C','2025-12-30 04:00:40','2025-12-30 04:00:40','pWBgKqFSS3','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4856-703a-bacd-b4a1bdb99017',NULL,'Brenna Cormier','lysanne.prohaska@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','HfW4SrkG9i','mVjBl7rIoQ','2025-12-30 04:00:40','2025-12-30 04:00:40','YVWqyM9dbR','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4862-71f0-88d8-66a0642bc6d4',NULL,'Rosendo Davis','sboehm@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','C3lg7euvTX','nHqIsemOre','2025-12-30 04:00:40','2025-12-30 04:00:40','7He5B7eG5u','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-486d-7119-8911-0053c9927a05',NULL,'Harmon Considine','ygrady@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','eplUfRXs7H','tZCyngUzpL','2025-12-30 04:00:40','2025-12-30 04:00:40','eHUPIicO1j','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4877-71c8-872e-c7e14be4ac25',NULL,'Houston Schmidt IV','raegan.bradtke@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','xezkOv7DO1','rBhbHGBmyg','2025-12-30 04:00:40','2025-12-30 04:00:40','IhA7u24oXu','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4882-7215-8ca6-2ad91e66e6ed',NULL,'Dr. Dedrick Greenholt Sr.','ottis.kessler@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','5PFQ5PU0xp','MNUuMviv3p','2025-12-30 04:00:40','2025-12-30 04:00:40','fwBtoVvecK','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-488d-72ef-9885-c449eb94bfb9',NULL,'Lura Purdy','dax.lakin@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','ZTa9JobwcK','gSmrfBaJcp','2025-12-30 04:00:40','2025-12-30 04:00:40','nLQutXrCun','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4899-7258-86f0-911d5747cfa6',NULL,'Easton Hamill MD','tthiel@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','ZYbDFPaEqh','G8BH6QAM1g','2025-12-30 04:00:40','2025-12-30 04:00:40','sBYRYwxhcd','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-48a4-7330-986a-9968755e071a',NULL,'Dr. Torey Wilkinson','sincere.bechtelar@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','jzxMAk0mob','lu9PisETHN','2025-12-30 04:00:40','2025-12-30 04:00:40','98KWdXzUbv','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-48af-72e4-ae52-04be56bd0dc6',NULL,'Miss Everette Hackett Jr.','hwehner@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','wyFNK7iyqK','qXpUwucFcq','2025-12-30 04:00:40','2025-12-30 04:00:40','IIAVpG5uRB','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-48ba-73d6-9e75-46b930c8483b',NULL,'Skye Thompson','turner34@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','AQAPi4tCjh','aFUxfIOt4s','2025-12-30 04:00:40','2025-12-30 04:00:40','90CHg2ALC8','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-48c6-7237-9416-76401bd5fcfe',NULL,'Harrison Wiegand','barton.june@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','Bnu32rMOeK','XlQsHynNuj','2025-12-30 04:00:40','2025-12-30 04:00:40','eTThRVgIR1','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-48d0-715c-917c-79af4bc8d41e',NULL,'Marcia Daniel MD','loyce.gislason@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','inaoE44WBE','MxKHXJPX9p','2025-12-30 04:00:40','2025-12-30 04:00:40','O54AyuqS8V','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-48dc-725e-81d3-e057b93cdd89',NULL,'Estevan Dickens','cleveland63@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','HKv0poThWX','1DAlXpOxC1','2025-12-30 04:00:40','2025-12-30 04:00:40','KF11KR4JGX','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4948-738d-9ce1-0830d550d7e8',NULL,'Retha Blanda','jboyle@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','tuwTSAOhfZ','UBLBxaxZ7p','2025-12-30 04:00:40','2025-12-30 04:00:40','IXktIqcwIP','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4956-7230-b33e-935c49c5b471',NULL,'Susie Eichmann','erick.muller@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','wVbhba9W3T','FKrD9CbBI5','2025-12-30 04:00:40','2025-12-30 04:00:40','eueJxKVgTo','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4961-73c7-ba1a-cdb847cf9d13',NULL,'Angeline Welch','joesph62@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','TDU2If50br','jVOpytbvad','2025-12-30 04:00:40','2025-12-30 04:00:40','dQTNDNblGr','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-496c-72b7-b785-1cd4539dc7af',NULL,'Darian Mills','shanel98@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','iu9xNJQ96J','iE27KJQEDW','2025-12-30 04:00:40','2025-12-30 04:00:40','FNbvZs9kgC','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4978-726d-92fa-9d9bbbb13710',NULL,'Dr. Alanis Pouros','wolff.lucile@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','2fRUsl8lRc','eYL3E2KcLA','2025-12-30 04:00:40','2025-12-30 04:00:40','MfUzSUkstT','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-4983-7361-b703-85f220e6667f',NULL,'Dr. Rebekah Schmidt','nswift@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','Q35G70U0YB','MIhjMMzzN0','2025-12-30 04:00:40','2025-12-30 04:00:40','fQCxTSGKQN','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-498e-723b-b5ae-1e9d665e9f7c',NULL,'Nicholaus Torp','albina.osinski@example.net',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','GfCkDENIBx','aO1HMNBGGl','2025-12-30 04:00:40','2025-12-30 04:00:40','uPnV8uNTwI','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-49a3-7369-8864-13ea16e86360',NULL,'Wanda Moen','ncasper@example.com',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','lx5v7wizck','ZGFlelL7Ji','2025-12-30 04:00:40','2025-12-30 04:00:40','RIibVzaSpv','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-49af-7394-84af-3fb0ac1cf334',NULL,'Marcellus Fahey','turner.glover@example.org',NULL,'019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BufVH3bcwi5hWNVrOrkmY.vwQm/JMWoWnAsZfS.GJTkR4Udn94lSa','LiCDzFkf49','YvVjjxSAke','2025-12-30 04:00:40','2025-12-30 04:00:40','f252d3RaA6','2025-12-30 04:00:40','2025-12-30 04:00:40'),
('019b6d6a-5043-7373-8c66-887bde77c289',NULL,'Ms. Patricia Kiehn Sr.','elmore23@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','OTwVPfe2dj','UNVKcPSDbN','2025-12-30 04:00:42','2025-12-30 04:00:42','ZVSQhzm81O','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-509e-7220-83a9-4639cf43722a',NULL,'Prof. Trenton Botsford','kathryn03@example.com',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','TJbSbTaisQ','4lIpGugxPu','2025-12-30 04:00:42','2025-12-30 04:00:42','FzEQYNUiMB','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50ac-7310-80af-245a69aada1e',NULL,'Julien Lubowitz','bosco.rolando@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','VkREkCcCi3','MNzOtGjSqz','2025-12-30 04:00:42','2025-12-30 04:00:42','qZTIgcn86J','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50b7-727d-9681-5835f0ae5675',NULL,'Kassandra Crooks','candida.hoppe@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','82Bc3bJvQD','HcizPDFKTb','2025-12-30 04:00:42','2025-12-30 04:00:42','5JgIYre4H5','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50c2-70ac-8b57-b7e6dc18e456',NULL,'Jairo Weber','carlos.schulist@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','FhfoLypFYi','RdrkZOoPPk','2025-12-30 04:00:42','2025-12-30 04:00:42','bLWKEBPuMj','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50cd-7265-913e-32c2421a6195',NULL,'Amani Boehm','kpurdy@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','pRC4yMlg6j','is7MmfvrIP','2025-12-30 04:00:42','2025-12-30 04:00:42','AJXaSNtBg1','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50d9-72f9-890d-cdb5c1a6e228',NULL,'Bette Kemmer','rrowe@example.com',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','s15T8DMjQE','fWcbzRDkO8','2025-12-30 04:00:42','2025-12-30 04:00:42','63KauENC2W','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50e3-7239-a694-7cd1eae0875f',NULL,'Leola Brekke V','lottie.tromp@example.com',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','MxZvoHRU0R','zYhJPs2uwU','2025-12-30 04:00:42','2025-12-30 04:00:42','VYqUTZJyka','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50ee-7391-ba4a-481bf333806c',NULL,'Miss Eulah Schowalter MD','tyree42@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','swsjroHO7n','CUz53if6ZP','2025-12-30 04:00:42','2025-12-30 04:00:42','Y2dJFBAFLe','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-50fa-713c-a289-e0c07972346a',NULL,'Eveline Kuhlman','floy.yundt@example.com',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','gJjOGvyQ4n','gieC5mrPkn','2025-12-30 04:00:42','2025-12-30 04:00:42','NLG2evrw0J','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-5104-7053-abd0-3a79779c1bf5',NULL,'Josefina Ullrich','kmohr@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','HWvOkJkXgn','7dOszBontx','2025-12-30 04:00:42','2025-12-30 04:00:42','RgL6KAbQQ3','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-510f-721b-8dcf-c6eee66c7cf6',NULL,'Prof. Nellie Renner IV','fredy77@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','AhT4imo40d','h7WN5KWRiF','2025-12-30 04:00:42','2025-12-30 04:00:42','5Tvm20BxgA','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-5166-7017-8b85-5eb55295ea39',NULL,'Torrey Langworth','leone.bailey@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','mQjq8404t0','ffCF46myyy','2025-12-30 04:00:42','2025-12-30 04:00:42','NEfTeBe8ON','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-5174-71ab-acb2-06ac89482941',NULL,'Jettie Stehr','hoppe.cali@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','f98yIHqHZ5','cigEEPVaDi','2025-12-30 04:00:42','2025-12-30 04:00:42','iaR7tQXgg6','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-517e-7361-b92a-09a7737dadf3',NULL,'Shanelle Cremin','mbruen@example.com',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','NNFJ1zK3Dr','MGeaLb2iRv','2025-12-30 04:00:42','2025-12-30 04:00:42','3TFVcTuND8','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-518a-73ba-852b-911607b9c03d',NULL,'Emilie Powlowski V','thaddeus24@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','UX7Cx5Qwly','WjNGAwsutt','2025-12-30 04:00:42','2025-12-30 04:00:42','GAN8AXtA7L','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-5196-71e0-a377-30c4f30eb732',NULL,'Prof. Jess O\'Kon II','hailee.osinski@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','DfF0f8DViR','vgxdcZDIG3','2025-12-30 04:00:42','2025-12-30 04:00:42','JWfLflswsL','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-51a0-7141-9053-494cc5406fd4',NULL,'Hanna Hand PhD','general04@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','tQrWVUqZ3j','k8UpLam42k','2025-12-30 04:00:42','2025-12-30 04:00:42','1P7NlEiVNl','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-51ac-7265-841c-eaf48c695d92',NULL,'Mr. Dante Fahey PhD','senger.bradly@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','bu6YBPxZRe','izNtBi5d6b','2025-12-30 04:00:42','2025-12-30 04:00:42','mSuPvit6pR','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-51b7-73d3-b61a-819ac23dca56',NULL,'Mr. Florian Willms','ldibbert@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','QA5VSQI2d3','wz5rtFa9nw','2025-12-30 04:00:42','2025-12-30 04:00:42','g4BkHnWwbX','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-51c2-7269-8c11-7913337e90bc',NULL,'Ms. Ettie Towne','ardith36@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','DzsK1E8t1z','C3vU0fj9Ca','2025-12-30 04:00:42','2025-12-30 04:00:42','kAQFz9e2kU','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-51cc-7157-a6b6-0521a80bd78e',NULL,'Korbin Larson DDS','stark.zita@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','Pt8Iwda7ao','c0b16F8cHf','2025-12-30 04:00:42','2025-12-30 04:00:42','RXjcMA9TU6','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-51d7-7271-8dc2-929f4d4a2046',NULL,'Megane Pfeffer','genevieve85@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','Tufx2zOfnr','J5BjeUVvqP','2025-12-30 04:00:42','2025-12-30 04:00:42','QmAohMHHzi','2025-12-30 04:00:42','2025-12-30 04:00:42'),
('019b6d6a-5222-7005-b98e-5fe84270f3ba',NULL,'Dr. Elisabeth Schoen','kbeahan@example.com',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','jazrcYNMtL','eihYU8H0s3','2025-12-30 04:00:42','2025-12-30 04:00:42','gm4bKSCyNv','2025-12-30 04:00:43','2025-12-30 04:00:43'),
('019b6d6a-5231-70ef-a7f1-34a7720a109a',NULL,'Erica Tromp','fadel.alisha@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','c1EGYjtfYy','VBB1efwqXL','2025-12-30 04:00:42','2025-12-30 04:00:42','TlLcK4ws62','2025-12-30 04:00:43','2025-12-30 04:00:43'),
('019b6d6a-523c-7067-9587-5077a7e6206f',NULL,'Dallas Volkman DDS','fabiola.mcclure@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','o7tHRU0YNc','KHrQvEXucT','2025-12-30 04:00:42','2025-12-30 04:00:42','31O8TkUpq9','2025-12-30 04:00:43','2025-12-30 04:00:43'),
('019b6d6a-5247-71b9-8b68-1bbce6bbf6c3',NULL,'Daron Keeling','orath@example.net',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','WPLzdTnUOj','NDu9uDHASp','2025-12-30 04:00:42','2025-12-30 04:00:42','fVrE1wJH4G','2025-12-30 04:00:43','2025-12-30 04:00:43'),
('019b6d6a-5252-7025-aaf0-da78cb31a2dd',NULL,'Aylin Hahn','arielle00@example.com',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','PVC93Z9m9V','HEKvmRNoEf','2025-12-30 04:00:42','2025-12-30 04:00:42','XBbOfonIro','2025-12-30 04:00:43','2025-12-30 04:00:43'),
('019b6d6a-525e-70cb-9755-22e94cd5bd28',NULL,'Larissa Grant','clinton81@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','2imx3GvNjv','ONvgG2S9ip','2025-12-30 04:00:42','2025-12-30 04:00:42','sVCamYf7Kg','2025-12-30 04:00:43','2025-12-30 04:00:43'),
('019b6d6a-5268-72bb-b3e2-64ca3e4829c5',NULL,'Mr. Rhett Block','jocelyn30@example.org',NULL,'019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$0OpX7f.T2OaEdU4de3N87u0zfJRbFdZ.ak2Ivxdz7KFdF.KT//VOS','SK0qBcJPo5','JBpdA1mcyX','2025-12-30 04:00:42','2025-12-30 04:00:42','swUQprYWFe','2025-12-30 04:00:43','2025-12-30 04:00:43'),
('019b6d6a-578c-705c-bdf4-f908604d58f9',NULL,'Garnet Johns','brandon36@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','lhcAc1rAr0','dTK54CBEwr','2025-12-30 04:00:44','2025-12-30 04:00:44','ZWld7C7uhR','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-57de-707c-8fac-4dd6533c9470',NULL,'Rashad Bernier','marlon30@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','gcJ2JHpZbC','UPfoQMpOGZ','2025-12-30 04:00:44','2025-12-30 04:00:44','D4cOXE4CXn','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-57ec-7019-8758-cb9f54eb77d7',NULL,'Ebba Koepp','americo70@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','R6Y4CfjmkO','qri5jKknW8','2025-12-30 04:00:44','2025-12-30 04:00:44','9i6RqOmWJp','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-57f7-706f-9b28-1e5f458cf825',NULL,'Pearline Sawayn','katelin18@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','v4TANsKUiK','b6TIXc5qIx','2025-12-30 04:00:44','2025-12-30 04:00:44','xFQvseyuH1','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5802-73bb-bbc1-857f9a0bddbd',NULL,'Nicola Kuhic','lawson.okeefe@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','oRLiJY9auz','eUTAjC4RXd','2025-12-30 04:00:44','2025-12-30 04:00:44','8dr8baF4cS','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-580e-71ef-aa2d-ddf782e17ed9',NULL,'Nels Gottlieb','yschiller@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','IULF0v46ez','ReVfYadd4q','2025-12-30 04:00:44','2025-12-30 04:00:44','LcKYG3Pz4r','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5819-7340-ba40-df446afbcb13',NULL,'Zack Bednar','whudson@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','0mmc7eJxgs','uVN51HskGb','2025-12-30 04:00:44','2025-12-30 04:00:44','MHyyjohnVT','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-587d-724f-bf29-7a90f1083974',NULL,'Veda Watsica','rhianna21@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','VUqm0rMC2p','xMO48EYLoL','2025-12-30 04:00:44','2025-12-30 04:00:44','QXs2CkQjU0','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5888-730d-b2b1-f8125431fbb6',NULL,'Carmela Bogan','gusikowski.aditya@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','LObbviuOcD','GASAWAX4Vq','2025-12-30 04:00:44','2025-12-30 04:00:44','pXP6ZTWKPN','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5894-7087-97d8-3806c9e18669',NULL,'Jamel Muller','ubaldo.schuppe@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','BaHXuNwOxd','XIbkrxC902','2025-12-30 04:00:44','2025-12-30 04:00:44','ej6eeltTRV','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-589e-7241-b413-27158a748942',NULL,'Miss Gail Abernathy','holden36@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','McM7j9gAm2','6fRDZOVK0K','2025-12-30 04:00:44','2025-12-30 04:00:44','eF4CsqCusa','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-58a9-70a7-8f8d-4bb037897273',NULL,'Audrey Mills','spencer.brekke@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','X5qfQqIRMM','kAUs6S2zqn','2025-12-30 04:00:44','2025-12-30 04:00:44','oaDWKPDjbC','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-58b5-72b2-b28d-35869d5aff78',NULL,'Arlene Carroll','keeley74@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','3dqxsNGPzd','qSgKiE8tBw','2025-12-30 04:00:44','2025-12-30 04:00:44','fayhfvAPrg','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-590a-735d-87f2-95a3913ebb1a',NULL,'Kenya Ortiz','daniel.dameon@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','brYQDC9x6v','vJC656AjiO','2025-12-30 04:00:44','2025-12-30 04:00:44','tY4IOc1TNw','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5923-738a-821f-0ca37dd54fb2',NULL,'Bettie Raynor','fabiola16@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','u2s0UQRX0R','4W7BxTwMVv','2025-12-30 04:00:44','2025-12-30 04:00:44','EpziXWbr57','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-592f-712f-b291-2c30d303d7dd',NULL,'Amya Zulauf','fcollier@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','BVry45Gw1h','Bh1j7pI1SI','2025-12-30 04:00:44','2025-12-30 04:00:44','JHvhB6vYXk','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5939-72fd-9a2d-c8ed4212c0e1',NULL,'Prof. Richmond Kozey III','grant.effertz@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','pGSNWepEzp','bAqRzvfosY','2025-12-30 04:00:44','2025-12-30 04:00:44','doqrdLEPJa','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5990-719c-9b09-50a6904ef607',NULL,'Green Walsh','rita83@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','rdsCbzNMPl','ZYkARZhM40','2025-12-30 04:00:44','2025-12-30 04:00:44','zZne1u3pCt','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-59aa-722a-b1ea-96a766f0f55e',NULL,'Khalid Weber','carter.vicente@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','Vg7ojpzC5L','T1eNN9ECD4','2025-12-30 04:00:44','2025-12-30 04:00:44','tP3Wojmumd','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-59c0-709c-b1ba-a75ff8e165c1',NULL,'Loraine Jones','eyost@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','qQSdnWHTdq','PulPrqTPp7','2025-12-30 04:00:44','2025-12-30 04:00:44','RJufDFqXT1','2025-12-30 04:00:44','2025-12-30 04:00:44'),
('019b6d6a-5a20-71ee-a303-3ac11f694003',NULL,'Vidal Quitzon','jermaine68@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','YTlKit8dfn','2WxdrEpoc9','2025-12-30 04:00:44','2025-12-30 04:00:44','qxRS7rC17o','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5a39-709f-a6b2-499b809d4e29',NULL,'Dr. Lawson Block I','cassie18@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','GvtWkfbc95','vO2S25A758','2025-12-30 04:00:44','2025-12-30 04:00:44','0Veedk5Lte','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5a44-7330-b7c0-4d48dcfc5626',NULL,'Josh Mertz','shad81@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','Lj6ZrC8Rll','5kdeNcmIgT','2025-12-30 04:00:44','2025-12-30 04:00:44','23PfxYj5d0','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5a4f-722c-ab1e-14b1bbcdd6f8',NULL,'Prof. Dennis Ebert I','thomas17@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','t3wprGOthu','URydxK7DpL','2025-12-30 04:00:44','2025-12-30 04:00:44','rBAszIf46H','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5a9a-73fa-b1b9-b5b2f75b6bb3',NULL,'Josie Rempel','dledner@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','9JPfEREhsv','H6COZjjuvC','2025-12-30 04:00:44','2025-12-30 04:00:44','ANNpTzKAYz','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5ab3-70ef-976c-a4ece5051bce',NULL,'Joesph Morar','elakin@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','asqc1FO9Dj','j91ZTyMzHX','2025-12-30 04:00:44','2025-12-30 04:00:44','SEqy6KZXcN','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5aca-715d-9276-a0f701a6af1d',NULL,'Dr. Madie Hettinger IV','oconner.aletha@example.org',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','pbBPTUfmyV','E0DZT0deTT','2025-12-30 04:00:44','2025-12-30 04:00:44','ZmMDq1dzKo','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5b2b-7299-8160-aa7642dadfaf',NULL,'Brooks Leannon II','mckenzie.trevor@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','I0QbSh81Uz','ZdHFhy1Hfc','2025-12-30 04:00:44','2025-12-30 04:00:44','NLdOdUj69c','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5b45-71bb-9903-550550b31b84',NULL,'Mrs. Helga Deckow IV','streich.dorthy@example.net',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','OUriQsO916','gfbwRTdcvN','2025-12-30 04:00:44','2025-12-30 04:00:44','mipGXUvEGm','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('019b6d6a-5b99-710f-b389-ebceda01b864',NULL,'Leonora Balistreri','uupton@example.com',NULL,'019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$tqVNeRijj9/N4ZjlAIdw/.lwGNBnvWZTP9hCl76R0gTmQjSJb511.','Dz6cxXIRrS','cnioYuKD4S','2025-12-30 04:00:44','2025-12-30 04:00:44','1PNRuKwGem','2025-12-30 04:00:45','2025-12-30 04:00:45'),
('06c39b01-8d7d-3dc5-b738-07cad0aaf0e9',NULL,'Amina Mohammed','amina.mohammed.10@sweettooth.com','EMP-ENU005-0010','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,1,NULL,'+234-806-512-1812','91455 Prohaska Squares Apt. 325, Enugu, Enugu State, Nigeria','1989-12-03','female','Nigerian','Oluwaseun Adebayo','+234-847-793-1883','2023-02-14 23:00:00','active',NULL,NULL,'morning',191076.12,NULL,'TIN-31048068','7992160114',NULL,NULL,'2025-10-14',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('070ed6a0-5d5e-3b50-aee2-79cff57d2873',NULL,'Chioma Okafor','chioma.okafor.20@sweettooth.com','EMP-ENU005-0020','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,4,NULL,'+234-900-986-7886','5355 Wintheiser Highway Suite 679, Enugu, Enugu State, Nigeria','1982-03-23','female','Nigerian','Tunde Mohammed','+234-833-330-5444','2024-10-27 23:00:00','active',NULL,NULL,'rotating',124519.78,NULL,'TIN-81597596','5261258844',NULL,NULL,'2025-11-30',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('0bff6289-dd51-3114-aa31-87cf1f954087',NULL,'Chigozie Johnson','chigozie.johnson.42@sweettooth.com','EMP-PHC002-0042','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,1,NULL,'+234-894-144-3508','831 Hodkiewicz Walks Apt. 172, Port Harcourt, Rivers State, Nigeria','1985-02-15','male','Nigerian','Ngozi Bello','+234-908-171-6079','2025-09-08 23:00:00','active',NULL,NULL,'rotating',157176.01,NULL,'TIN-42396626','8198822001',NULL,NULL,'2025-07-30',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('0ce855e9-000d-356f-946a-af16cd4b9db0','019b6d6a-191b-7068-817b-94e02d09e859','Blessing Okafor','blessing.okafor.35@sweettooth.com','EMP-CAL001-0035','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,4,NULL,'+234-830-410-4801','8529 Tania Isle, Calabar, Cross River State, Nigeria','1985-09-27','female','Nigerian','Tunde Okoro','+234-837-506-8089','2023-09-26 23:00:00','active',NULL,NULL,'flexible',87532.84,NULL,'TIN-85803095','3943593648',NULL,NULL,'2025-12-15',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2026-01-01 08:09:37'),
('0ff96be5-7e72-331b-9ebd-e52d1c607ea5',NULL,'Abubakar Adebayo','abubakar.adebayo.36@sweettooth.com','EMP-CAL001-0036','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,4,NULL,'+234-851-903-9530','853 Asa Key Suite 423, Calabar, Cross River State, Nigeria','1990-06-19','male','Nigerian','Hauwa Adebayo','+234-885-641-2487','2024-12-03 23:00:00','active',NULL,NULL,'flexible',243708.07,NULL,'TIN-95176893','5284523423',NULL,NULL,'2025-09-14',4.4,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('1455aa2f-8b57-3839-a7ea-d64a0129ce1d',NULL,'Chukwuemeka Adebayo','chukwuemeka.adebayo.23@sweettooth.com','EMP-LAG003-0023','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,1,NULL,'+234-881-687-2908','643 Klocko Ridges, Lagos, Lagos State, Nigeria','1994-06-25','male','Nigerian','Chioma Okafor','+234-803-841-8592','2025-09-06 23:00:00','active',NULL,NULL,'morning',346179.42,NULL,'TIN-52463144','4000282825',NULL,NULL,'2025-12-13',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('15c80da1-8059-3665-bf5a-690170791407',NULL,'Tunde Mohammed','tunde.mohammed.9@sweettooth.com','EMP-ABJ004-0009','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,1,NULL,'+234-840-784-1650','94614 Halvorson Springs Suite 900, Abuja, FCT State, Nigeria','1983-06-07','male','Nigerian','Amina Okafor','+234-813-470-2873','2023-08-23 23:00:00','active',NULL,NULL,'afternoon',190071.44,NULL,'TIN-24076583','9486614521',NULL,NULL,'2025-07-01',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('18218b53-64bc-3735-8d7e-ad25b8a3bd37',NULL,'Folake Adebayo','folake.adebayo.27@sweettooth.com','EMP-CAL001-0027','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,1,NULL,'+234-863-752-8179','69668 Schmitt Avenue Apt. 894, Calabar, Cross River State, Nigeria','1981-01-18','female','Nigerian','Tunde Okoro','+234-829-885-9561','2024-01-28 23:00:00','active',NULL,NULL,'rotating',244765.32,NULL,'TIN-40740528','7325995040',NULL,NULL,'2025-10-12',4.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('186cb680-ca9f-3d01-9ba1-c86de5243466',NULL,'Amina Okafor','amina.okafor.18@sweettooth.com','EMP-LAG003-0018','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,4,NULL,'+234-852-151-9883','547 Morissette Tunnel Suite 622, Lagos, Lagos State, Nigeria','1994-02-13','female','Nigerian','Abubakar Okafor','+234-846-314-9975','2024-03-14 23:00:00','active',NULL,NULL,'rotating',322157.05,NULL,'TIN-63685204','5043890583','Peanuts',NULL,'2025-10-11',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('1a3e8f9c-5830-34b1-892a-bbe2725dbbc0',NULL,'Chukwuemeka Williams','chukwuemeka.williams.69@sweettooth.com','EMP-LAG003-0069','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,5,NULL,'+234-868-133-3667','173 Isabella Squares Apt. 713, Lagos, Lagos State, Nigeria','1983-11-02','male','Nigerian','Ada Williams','+234-810-683-7267','2023-11-26 23:00:00','active',NULL,NULL,'afternoon',195287.43,NULL,'TIN-44422230','4968948074',NULL,NULL,'2025-12-13',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('1a628953-5f9e-39f3-b85e-87d406ffb474',NULL,'Ada Mohammed','ada.mohammed.71@sweettooth.com','EMP-LAG003-0071','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,5,NULL,'+234-897-677-4240','1280 Lilian Fort, Lagos, Lagos State, Nigeria','2001-08-04','female','Nigerian','Obinna Okafor','+234-818-339-9845','2023-11-04 23:00:00','active',NULL,NULL,'flexible',106442.46,NULL,'TIN-75490221','4389648991',NULL,NULL,'2025-07-23',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('1eb3adf7-55ae-3694-93fc-9c60da92ebbb',NULL,'Blessing Chukwu','blessing.chukwu.95@sweettooth.com','EMP-ENU005-0095','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,2,NULL,'+234-815-550-4859','17679 Wilfred Lodge, Enugu, Enugu State, Nigeria','1997-11-23','female','Nigerian','Obinna Ogunleye','+234-874-321-7612','2023-06-18 23:00:00','active',NULL,NULL,'afternoon',191291.16,NULL,'TIN-69036157','6596324476',NULL,NULL,'2025-10-18',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('2392dab5-53e7-3a12-88b5-e28daebfcbe5',NULL,'Kemi Adebayo','kemi.adebayo.11@sweettooth.com','EMP-CAL001-0011','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,7,NULL,'+234-852-219-6461','12057 Bartell Stravenue Apt. 322, Calabar, Cross River State, Nigeria','1993-10-20','female','Nigerian','Emeka Adebayo','+234-812-815-2092','2025-02-07 23:00:00','active',NULL,NULL,'rotating',327396.95,NULL,'TIN-67487399','6377926655',NULL,NULL,'2025-12-16',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('23b13cd9-6f31-3745-8061-d5f0dd77f130','019b6d6a-191b-7068-817b-94e02d09e859','Ngozi Chukwu','ngozi.chukwu.30@sweettooth.com','EMP-CAL001-0030','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,2,NULL,'+234-812-837-7059','58497 Enola Inlet Apt. 590, Calabar, Cross River State, Nigeria','1998-10-21','female','Nigerian','Oluwaseun Okafor','+234-808-160-7306','2023-02-28 23:00:00','active',NULL,NULL,'rotating',86994.00,NULL,'TIN-97004199','7028456227',NULL,NULL,'2025-08-12',4.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48','MdIjHM8zDCWjwKHIuZ3UoqigU5mA0hDDlkJrUsXFFWue1NxrkwmqXgumdN8Z','2025-12-30 04:00:48','2026-01-01 14:37:08'),
('2595e4c6-34e9-3d99-a1e2-638e3b626439',NULL,'Chigozie Williams','chigozie.williams.50@sweettooth.com','EMP-PHC002-0050','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,4,NULL,'+234-840-147-9734','462 Ruecker Stream, Port Harcourt, Rivers State, Nigeria','1993-06-27','male','Nigerian','Ngozi Okafor','+234-846-862-4194','2025-01-04 23:00:00','active',NULL,NULL,'flexible',189702.53,NULL,'TIN-44236398','0295393826',NULL,NULL,'2025-12-19',4.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('26409d0c-0f8c-38b3-9715-6050d6d56017',NULL,'Chigozie Nwankwo','chigozie.nwankwo.67@sweettooth.com','EMP-LAG003-0067','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,4,NULL,'+234-891-322-5865','420 Delphia Neck, Lagos, Lagos State, Nigeria','2003-05-14','male','Nigerian','Fatima Johnson','+234-849-411-8229','2023-05-13 23:00:00','active',NULL,NULL,'afternoon',281290.40,NULL,'TIN-91625256','1597103652',NULL,NULL,'2025-11-26',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('274aed69-6b4b-370e-b86b-bf72ebbd8a36',NULL,'Tunde Okafor','tunde.okafor.14@sweettooth.com','EMP-ABJ004-0014','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,7,NULL,'+234-906-662-8199','902 Kilback Corners, Abuja, FCT State, Nigeria','1986-06-15','male','Nigerian','Folake Mohammed','+234-833-961-3490','2023-11-26 23:00:00','active',NULL,NULL,'flexible',94923.34,NULL,'TIN-56658547','0647122314',NULL,NULL,'2025-08-21',4.2,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('27837c68-0ad8-3344-8495-5efd41354aef',NULL,'Kunle Bello','kunle.bello.83@sweettooth.com','EMP-ABJ004-0083','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,4,NULL,'+234-885-989-8389','90802 Reilly Circles, Abuja, FCT State, Nigeria','1998-12-20','male','Nigerian','Hauwa Aliyu','+234-812-867-5512','2024-11-07 23:00:00','active',NULL,NULL,'afternoon',310828.72,NULL,'TIN-83182226','1030968489',NULL,NULL,'2025-10-25',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('2b0252b3-5afa-33e0-99c4-50a15dc3876c',NULL,'Ngozi Aliyu','ngozi.aliyu.101@sweettooth.com','EMP-ENU005-0101','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,5,NULL,'+234-903-530-8251','761 Batz Shoal Suite 732, Enugu, Enugu State, Nigeria','2001-07-09','female','Nigerian','Emeka Ogunleye','+234-818-126-8754','2023-02-05 23:00:00','active',NULL,NULL,'rotating',344348.58,NULL,'TIN-46361536','4943888107',NULL,NULL,'2025-07-13',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('2b169c96-7357-3475-91df-a606ce520497',NULL,'Oluwaseun Bello','oluwaseun.bello.92@sweettooth.com','EMP-ENU005-0092','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,1,NULL,'+234-877-547-5870','1951 Larson Lakes Apt. 016, Enugu, Enugu State, Nigeria','1984-01-21','male','Nigerian','Hauwa Chukwu','+234-809-517-9654','2025-04-03 23:00:00','active',NULL,NULL,'morning',122993.29,NULL,'TIN-30739546','4475798284',NULL,NULL,'2025-10-05',4.2,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('2caff012-199e-305f-adbb-c9390cd45222',NULL,'Emeka Ogunleye','emeka.ogunleye.88@sweettooth.com','EMP-ABJ004-0088','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,7,NULL,'+234-902-717-8185','289 Ally Landing Suite 881, Abuja, FCT State, Nigeria','1985-08-24','male','Nigerian','Blessing Okafor','+234-839-240-4843','2024-09-18 23:00:00','active',NULL,NULL,'rotating',166291.31,NULL,'TIN-70628758','0782851004',NULL,NULL,'2025-11-17',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('2d1b7001-a4c8-3723-b6d4-91670b3d7657',NULL,'Kemi Ogunleye','kemi.ogunleye.40@sweettooth.com','EMP-CAL001-0040','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,7,NULL,'+234-840-956-2433','55652 Evie Camp Apt. 337, Calabar, Cross River State, Nigeria','1987-09-30','female','Nigerian','Abubakar Williams','+234-854-969-6691','2023-07-09 23:00:00','active',NULL,NULL,'morning',286996.67,NULL,'TIN-32106147','9179456123','Shellfish',NULL,'2025-07-22',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('2f54369a-d00a-322f-aed3-3d2c2b258f81',NULL,'Amina Adebayo','amina.adebayo.2@sweettooth.com','EMP-PHC002-0002','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,1,NULL,'+234-829-737-6523','1324 Sydnee Fields, Port Harcourt, Rivers State, Nigeria','1988-03-03','female','Nigerian','Tunde Adebayo','+234-814-633-4766','2025-06-07 23:00:00','active',NULL,NULL,'rotating',145942.09,NULL,'TIN-48198417','3602038007',NULL,NULL,'2025-11-09',3.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('30ba7f40-f8bc-3eee-9a1d-40d20a673f6c',NULL,'Blessing Okafor','blessing.okafor.96@sweettooth.com','EMP-ENU005-0096','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,2,NULL,'+234-889-646-1334','866 Haley Oval Suite 186, Enugu, Enugu State, Nigeria','1987-10-02','female','Nigerian','Obinna Eze','+234-820-185-6074','2025-05-01 23:00:00','active',NULL,NULL,'flexible',236508.58,NULL,'TIN-53354215','5582417277',NULL,NULL,'2025-08-19',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('31199703-55e7-3746-a17c-9d4e2b1a8384',NULL,'Chukwuemeka Okafor','chukwuemeka.okafor.6@sweettooth.com','EMP-CAL001-0006','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,1,NULL,'+234-893-377-8959','47379 Satterfield Corners Suite 963, Calabar, Cross River State, Nigeria','1988-11-26','male','Nigerian','Amina Adebayo','+234-859-991-6189','2023-07-07 23:00:00','active',NULL,NULL,'morning',240464.35,NULL,'TIN-86969388','0490464069',NULL,NULL,'2025-09-16',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('33ed81d2-c331-3e6d-a248-a63fda82b8da',NULL,'Yusuf Bello','yusuf.bello.49@sweettooth.com','EMP-PHC002-0049','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,4,NULL,'+234-899-833-9784','29570 Raleigh Inlet Suite 395, Port Harcourt, Rivers State, Nigeria','1998-08-17','male','Nigerian','Ngozi Nwankwo','+234-866-945-8096','2023-11-26 23:00:00','active',NULL,NULL,'morning',113801.93,NULL,'TIN-53144843','1098708895',NULL,NULL,'2025-11-07',4.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('3540cf43-7bf7-306c-8040-4b8552c61f61',NULL,'Amina Adebayo','amina.adebayo.1@sweettooth.com','EMP-CAL001-0001','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,1,NULL,'+234-801-993-6959','33687 Heller Course, Calabar, Cross River State, Nigeria','1998-07-23','female','Nigerian','Chukwuemeka Mohammed','+234-804-807-7569','2023-08-29 23:00:00','active',NULL,NULL,'flexible',146313.95,NULL,'TIN-99473736','1654715280',NULL,NULL,'2025-08-23',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('367c6adc-d085-3312-be53-badfde6a0d79',NULL,'Nneka Bello','nneka.bello.86@sweettooth.com','EMP-ABJ004-0086','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,5,NULL,'+234-817-199-6671','311 Brandi Garden Apt. 265, Abuja, FCT State, Nigeria','1994-04-06','female','Nigerian','Emeka Ogunleye','+234-832-349-3834','2023-02-24 23:00:00','active',NULL,NULL,'rotating',197685.82,NULL,'TIN-81952793','7730081988','Shellfish',NULL,'2025-11-10',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('38eb26d5-c6f3-38f5-96cc-77532ac50c8b',NULL,'Emeka Adebayo','emeka.adebayo.85@sweettooth.com','EMP-ABJ004-0085','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,5,NULL,'+234-858-236-4322','218 Ethyl Lock, Abuja, FCT State, Nigeria','1996-11-11','male','Nigerian','Blessing Okafor','+234-806-974-6300','2024-04-22 23:00:00','active',NULL,NULL,'flexible',311157.53,NULL,'TIN-42218283','2789234948',NULL,NULL,'2025-10-03',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('3b866466-ec94-3176-babf-8baf843aba33',NULL,'Abubakar Adebayo','abubakar.adebayo.13@sweettooth.com','EMP-LAG003-0013','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,7,NULL,'+234-871-254-6034','31905 Lexus Shoal Apt. 509, Lagos, Lagos State, Nigeria','1987-03-11','male','Nigerian','Amina Adebayo','+234-882-866-9936','2024-12-31 23:00:00','active',NULL,NULL,'flexible',105075.56,NULL,'TIN-51541223','8713956715',NULL,NULL,'2025-12-21',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('3ce07f1c-fbbb-38ad-87f3-718626a3233c',NULL,'Abubakar Ogunleye','abubakar.ogunleye.102@sweettooth.com','EMP-ENU005-0102','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,5,NULL,'+234-806-199-1146','4969 Bennett Streets Apt. 131, Enugu, Enugu State, Nigeria','1988-09-22','male','Nigerian','Ada Mohammed','+234-870-837-4016','2025-06-18 23:00:00','active',NULL,NULL,'morning',307717.00,NULL,'TIN-98436408','0036854531',NULL,NULL,'2025-08-19',4.2,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('4133497a-8d56-372d-a5fb-9dfaa452574e',NULL,'Tunde Ogunleye','tunde.ogunleye.52@sweettooth.com','EMP-PHC002-0052','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,4,NULL,'+234-836-866-2510','6835 Hudson Row Apt. 589, Port Harcourt, Rivers State, Nigeria','1982-07-10','male','Nigerian','Nneka Williams','+234-809-752-1865','2025-04-30 23:00:00','active',NULL,NULL,'morning',162255.06,NULL,'TIN-81485738','7162027494',NULL,NULL,'2025-10-03',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('4568e478-f0c0-3b29-ab90-21e6f2d86051',NULL,'Blessing Adebayo','blessing.adebayo.77@sweettooth.com','EMP-ABJ004-0077','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,1,NULL,'+234-875-995-8473','9406 August Stravenue, Abuja, FCT State, Nigeria','1990-07-14','female','Nigerian','Tunde Chukwu','+234-859-201-4479','2025-07-15 23:00:00','active',NULL,NULL,'afternoon',129817.46,NULL,'TIN-29862261','7464506847',NULL,NULL,'2025-11-26',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('45abd0f5-fc1e-3c9a-882f-f9ca82669a44',NULL,'Amina Adebayo','amina.adebayo.3@sweettooth.com','EMP-LAG003-0003','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,1,NULL,'+234-859-597-6466','284 Yundt Radial Suite 863, Lagos, Lagos State, Nigeria','1991-06-17','female','Nigerian','Abubakar Mohammed','+234-814-271-5724','2024-09-20 23:00:00','active',NULL,NULL,'morning',212463.45,NULL,'TIN-64775646','1407754829',NULL,NULL,'2025-10-04',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('470de4db-992b-3be1-9a94-84579fb258f8',NULL,'Emeka Bello','emeka.bello.58@sweettooth.com','EMP-LAG003-0058','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,1,NULL,'+234-868-751-1292','5740 Johnson Avenue Apt. 795, Lagos, Lagos State, Nigeria','1987-05-03','male','Nigerian','Blessing Johnson','+234-855-981-6180','2025-06-24 23:00:00','active',NULL,NULL,'afternoon',288690.88,NULL,'TIN-86119919','9455016166',NULL,NULL,'2025-12-21',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('4e67324c-3687-3be0-b526-b338fba12748',NULL,'Kemi Adebayo','kemi.adebayo.21@sweettooth.com','EMP-CAL001-0021','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,1,NULL,'+234-875-841-8660','24028 Lavon Vista, Calabar, Cross River State, Nigeria','1986-06-23','female','Nigerian','Emeka Okafor','+234-860-566-1282','2025-11-28 23:00:00','active',NULL,NULL,'morning',211889.85,NULL,'TIN-82253789','9413513937',NULL,NULL,'2025-07-15',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('5282faf3-cb0c-3967-a09d-92c139b749cf',NULL,'Abubakar Johnson','abubakar.johnson.87@sweettooth.com','EMP-ABJ004-0087','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,5,NULL,'+234-890-916-9450','513 Olson Plains Suite 765, Abuja, FCT State, Nigeria','1988-09-27','male','Nigerian','Blessing Nwankwo','+234-832-969-3974','2025-07-29 23:00:00','active',NULL,NULL,'morning',236784.22,NULL,'TIN-71150062','3230013990',NULL,NULL,'2025-08-10',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('55fa27a3-dc2e-314a-8e5d-d99bfd341ec0',NULL,'Kemi Johnson','kemi.johnson.94@sweettooth.com','EMP-ENU005-0094','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,2,NULL,'+234-891-253-3975','959 Sam Islands Suite 154, Enugu, Enugu State, Nigeria','1981-11-07','female','Nigerian','Obinna Ogunleye','+234-888-661-9688','2023-08-27 23:00:00','active',NULL,NULL,'morning',154032.86,NULL,'TIN-63746949','0252653762',NULL,NULL,'2025-08-18',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('563542b8-71d6-3f5b-8def-07dad391cc80',NULL,'Ada Williams','ada.williams.53@sweettooth.com','EMP-PHC002-0053','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,5,NULL,'+234-819-369-7575','4971 Brook Prairie, Port Harcourt, Rivers State, Nigeria','2001-06-25','female','Nigerian','Kunle Ogunleye','+234-898-240-1442','2024-07-15 23:00:00','active',NULL,NULL,'flexible',82747.07,NULL,'TIN-40556465','7276557793','Peanuts',NULL,'2025-09-29',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('58952267-3f16-34a6-976d-ad425049ecd7',NULL,'Kemi Okafor','kemi.okafor.80@sweettooth.com','EMP-ABJ004-0080','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,2,NULL,'+234-807-839-1801','933 Jaclyn Pass, Abuja, FCT State, Nigeria','1989-03-10','female','Nigerian','Oluwaseun Williams','+234-842-214-8321','2024-11-10 23:00:00','active',NULL,NULL,'morning',341221.19,NULL,'TIN-20942331','7179251829',NULL,NULL,'2025-09-19',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('61b929af-6f22-3aa9-86a4-466259d7c090',NULL,'Chigozie Adebayo','chigozie.adebayo.38@sweettooth.com','EMP-CAL001-0038','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,5,NULL,'+234-903-117-9272','831 Dach Course Apt. 584, Calabar, Cross River State, Nigeria','1995-04-21','male','Nigerian','Ada Ogunleye','+234-861-723-6747','2025-07-29 23:00:00','active',NULL,NULL,'flexible',175717.07,NULL,'TIN-61463493','3902113866',NULL,NULL,'2025-10-18',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('64671e7f-39a7-3677-8275-79f7b848c230',NULL,'Emeka Eze','emeka.eze.64@sweettooth.com','EMP-LAG003-0064','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,2,NULL,'+234-817-635-7762','304 Jakubowski Drive Apt. 641, Lagos, Lagos State, Nigeria','1985-02-19','male','Nigerian','Hauwa Mohammed','+234-898-282-2257','2024-01-28 23:00:00','active',NULL,NULL,'morning',124237.89,NULL,'TIN-12387565','8280828861',NULL,NULL,'2025-08-17',3.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('64e1b365-4bba-36d9-a0e9-0db4ce80224f',NULL,'Hauwa Adebayo','hauwa.adebayo.48@sweettooth.com','EMP-PHC002-0048','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,2,NULL,'+234-890-236-2979','2839 Kirlin Shores, Port Harcourt, Rivers State, Nigeria','2001-03-18','female','Nigerian','Kunle Aliyu','+234-878-752-8168','2025-02-18 23:00:00','active',NULL,NULL,'rotating',190100.60,NULL,'TIN-13026989','5105038104',NULL,NULL,'2025-11-14',3.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('6bc9140c-aca5-34b5-999b-44530f370ec0',NULL,'Abubakar Adebayo','abubakar.adebayo.5@sweettooth.com','EMP-ENU005-0005','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,1,NULL,'+234-880-625-2236','7844 Nyasia Locks, Enugu, Enugu State, Nigeria','1984-05-23','male','Nigerian','Kemi Mohammed','+234-833-852-3585','2024-08-19 23:00:00','active',NULL,NULL,'morning',211350.75,NULL,'TIN-51215909','2842865381',NULL,NULL,'2025-11-11',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('6daea7be-0da3-35b8-b003-b66fa95c273f',NULL,'Chukwuemeka Johnson','chukwuemeka.johnson.78@sweettooth.com','EMP-ABJ004-0078','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,2,NULL,'+234-803-965-3181','7981 Bahringer Estate Suite 692, Abuja, FCT State, Nigeria','1982-06-28','male','Nigerian','Ngozi Aliyu','+234-847-670-5239','2023-10-11 23:00:00','active',NULL,NULL,'morning',146766.04,NULL,'TIN-27834356','9826899949',NULL,NULL,'2025-11-09',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('6de509aa-e42e-3922-9dbc-9b92c206f853',NULL,'Yusuf Okafor','yusuf.okafor.99@sweettooth.com','EMP-ENU005-0099','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,4,NULL,'+234-858-100-6111','93450 Lemke Union, Enugu, Enugu State, Nigeria','1983-05-29','male','Nigerian','Chioma Eze','+234-860-100-6396','2023-11-29 23:00:00','active',NULL,NULL,'flexible',319355.78,NULL,'TIN-29537362','3656156583',NULL,NULL,'2025-09-08',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('6e2153b1-39e0-3151-ade4-c1d49e5cb894',NULL,'Chigozie Okafor','chigozie.okafor.37@sweettooth.com','EMP-CAL001-0037','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,5,NULL,'+234-856-467-8032','75790 Kaitlyn Plaza Apt. 200, Calabar, Cross River State, Nigeria','1985-10-19','male','Nigerian','Fatima Johnson','+234-902-455-5626','2023-11-23 23:00:00','active',NULL,NULL,'morning',225922.00,NULL,'TIN-56558312','6819087203',NULL,NULL,'2025-08-03',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('6e42e052-0341-361b-bcdd-860d7757fd06',NULL,'Emeka Nwankwo','emeka.nwankwo.51@sweettooth.com','EMP-PHC002-0051','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,4,NULL,'+234-899-189-4171','33400 Nikolaus Lakes Apt. 897, Port Harcourt, Rivers State, Nigeria','1996-08-16','male','Nigerian','Hauwa Mohammed','+234-832-659-2500','2024-08-06 23:00:00','active',NULL,NULL,'rotating',321473.09,NULL,'TIN-74434958','8574811491',NULL,NULL,'2025-08-08',4.3,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('6e92b76c-3d00-3df7-9613-9ab5eaee8338',NULL,'Ngozi Williams','ngozi.williams.29@sweettooth.com','EMP-CAL001-0029','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,1,NULL,'+234-886-415-2307','10283 Elliott Grove Suite 922, Calabar, Cross River State, Nigeria','1990-10-22','female','Nigerian','Ibrahim Johnson','+234-896-249-4573','2023-12-02 23:00:00','active',NULL,NULL,'afternoon',315780.75,NULL,'TIN-82259824','1640920786',NULL,NULL,'2025-07-15',4.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('705e4415-134a-3737-91ef-e7b86884bb5e',NULL,'Fatima Okoro','fatima.okoro.73@sweettooth.com','EMP-LAG003-0073','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,7,NULL,'+234-892-900-5270','113 Eva Ville Apt. 748, Lagos, Lagos State, Nigeria','1996-05-22','female','Nigerian','Ibrahim Aliyu','+234-905-282-3111','2024-12-22 23:00:00','active',NULL,NULL,'afternoon',341761.98,NULL,'TIN-16255539','4401463962','Lactose',NULL,'2025-07-07',3.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('72359c50-1603-3616-93ea-b9b851d8623f',NULL,'Amina Bello','amina.bello.97@sweettooth.com','EMP-ENU005-0097','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,4,NULL,'+234-898-546-7814','21647 Oma Avenue Suite 971, Enugu, Enugu State, Nigeria','1985-05-20','female','Nigerian','Abubakar Okoro','+234-842-430-1309','2024-03-08 23:00:00','active',NULL,NULL,'afternoon',204895.45,NULL,'TIN-36936986','2370589420',NULL,NULL,'2025-12-11',4.3,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('7b518e23-aa14-3752-9dff-cfab9417a704',NULL,'Chigozie Adebayo','chigozie.adebayo.74@sweettooth.com','EMP-ABJ004-0074','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,1,NULL,'+234-824-185-4392','684 Wiegand Manor, Abuja, FCT State, Nigeria','1996-07-24','male','Nigerian','Ngozi Adebayo','+234-909-125-4216','2025-10-13 23:00:00','active',NULL,NULL,'rotating',118424.45,NULL,'TIN-39008452','1114205676',NULL,NULL,'2025-11-09',4.4,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('7f53ba7c-e9bf-339e-b165-b2aec469e1a2',NULL,'Emeka Chukwu','emeka.chukwu.84@sweettooth.com','EMP-ABJ004-0084','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,4,NULL,'+234-890-163-5952','9400 Feest Shores, Abuja, FCT State, Nigeria','1988-04-18','male','Nigerian','Fatima Ogunleye','+234-827-320-9452','2025-11-23 23:00:00','active',NULL,NULL,'rotating',296940.53,NULL,'TIN-26795600','2918958842',NULL,NULL,'2025-10-21',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('8172bc43-8725-3898-84d6-8425942a49ba',NULL,'Tunde Okafor','tunde.okafor.4@sweettooth.com','EMP-ABJ004-0004','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,1,NULL,'+234-808-332-3070','867 Reinhold Fork, Abuja, FCT State, Nigeria','2003-03-06','male','Nigerian','Kemi Okafor','+234-893-654-2588','2023-04-25 23:00:00','active',NULL,NULL,'morning',316165.03,NULL,'TIN-89616800','0218152591',NULL,NULL,'2025-11-11',3.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('8200cf43-0379-3ac6-88f7-140eb0742b38',NULL,'Kemi Okafor','kemi.okafor.17@sweettooth.com','EMP-PHC002-0017','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,4,NULL,'+234-849-139-5260','1455 Nash Street Suite 745, Port Harcourt, Rivers State, Nigeria','1992-11-26','female','Nigerian','Oluwaseun Okafor','+234-871-510-2234','2025-06-03 23:00:00','active',NULL,NULL,'morning',112365.70,NULL,'TIN-94821186','9315342942',NULL,NULL,'2025-10-13',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('82403e8e-9c4d-3772-9b6f-596813c258b7',NULL,'Folake Mohammed','folake.mohammed.15@sweettooth.com','EMP-ENU005-0015','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,7,NULL,'+234-884-206-4753','7065 Francesco Estate, Enugu, Enugu State, Nigeria','2001-08-03','female','Nigerian','Emeka Adebayo','+234-853-850-7117','2024-09-28 23:00:00','active',NULL,NULL,'rotating',163368.67,NULL,'TIN-41669319','1793067941','Shellfish',NULL,'2025-12-28',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('83b895cb-83e3-3aed-9c73-487627497791',NULL,'Tunde Mohammed','tunde.mohammed.55@sweettooth.com','EMP-PHC002-0055','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,5,NULL,'+234-888-613-8887','7113 Block Fords Apt. 862, Port Harcourt, Rivers State, Nigeria','1984-01-12','male','Nigerian','Nneka Chukwu','+234-801-901-2354','2023-10-30 23:00:00','active',NULL,NULL,'flexible',153001.17,NULL,'TIN-54370286','2190699553',NULL,NULL,'2025-10-25',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('85e5094e-60d9-37c6-9bcd-a0b2cb999e48',NULL,'Ngozi Mohammed','ngozi.mohammed.19@sweettooth.com','EMP-ABJ004-0019','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,4,NULL,'+234-883-187-5472','68727 Gust Rest Suite 405, Abuja, FCT State, Nigeria','1994-02-12','female','Nigerian','Oluwaseun Okafor','+234-872-677-7849','2023-07-22 23:00:00','active',NULL,NULL,'afternoon',211762.80,NULL,'TIN-75963770','4962123879','Lactose',NULL,'2025-12-14',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('8bdc6cea-5422-397e-b84a-68065ad17bf6',NULL,'Abubakar Mohammed','abubakar.mohammed.25@sweettooth.com','EMP-ENU005-0025','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,1,NULL,'+234-817-309-5007','985 Torey Keys, Enugu, Enugu State, Nigeria','2003-11-05','male','Nigerian','Kemi Mohammed','+234-861-789-6549','2023-06-12 23:00:00','active',NULL,NULL,'morning',239262.64,NULL,'TIN-69697535','1379953556',NULL,NULL,'2025-12-21',4.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('8c2d0efe-174c-316c-9c67-c25f6c62b66c',NULL,'Ada Mohammed','ada.mohammed.65@sweettooth.com','EMP-LAG003-0065','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,4,NULL,'+234-833-735-3782','2650 Grant Field Apt. 958, Lagos, Lagos State, Nigeria','1991-01-17','female','Nigerian','Emeka Adebayo','+234-849-586-2430','2023-09-24 23:00:00','active',NULL,NULL,'morning',228384.27,NULL,'TIN-60055875','0781191378',NULL,NULL,'2025-11-06',5.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('924fe9a5-d092-3e63-b0df-e46c92b036d2',NULL,'Blessing Adebayo','blessing.adebayo.62@sweettooth.com','EMP-LAG003-0062','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,2,NULL,'+234-830-399-2180','82885 Irving Locks Apt. 023, Lagos, Lagos State, Nigeria','1990-10-16','female','Nigerian','Yusuf Nwankwo','+234-868-381-2442','2025-07-18 23:00:00','active',NULL,NULL,'rotating',266375.24,NULL,'TIN-10265920','4073847611',NULL,NULL,'2025-10-24',3.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('99807fc1-da43-3ed3-a178-e719f8c0e124',NULL,'Tunde Adebayo','tunde.adebayo.33@sweettooth.com','EMP-CAL001-0033','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,4,NULL,'+234-891-580-9129','168 Kub Throughway, Calabar, Cross River State, Nigeria','1999-12-03','male','Nigerian','Ada Chukwu','+234-903-753-6756','2025-01-07 23:00:00','active',NULL,NULL,'morning',261734.36,NULL,'TIN-63149115','4174127564',NULL,NULL,'2025-12-07',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('9a1ed35c-3e93-3b48-bf7a-57c7601fc5ca',NULL,'Chigozie Eze','chigozie.eze.91@sweettooth.com','EMP-ENU005-0091','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,1,NULL,'+234-839-825-9431','5924 Greenfelder Causeway Suite 488, Enugu, Enugu State, Nigeria','1982-08-19','male','Nigerian','Kemi Adebayo','+234-843-821-8619','2023-01-11 23:00:00','active',NULL,NULL,'morning',222760.52,NULL,'TIN-13274220','5576125923',NULL,NULL,'2025-07-05',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('9e8bd907-63ff-3725-b6a6-5f532d4acfc2',NULL,'Nneka Ogunleye','nneka.ogunleye.104@sweettooth.com','EMP-ENU005-0104','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,7,NULL,'+234-806-856-4272','120 Altenwerth Crescent, Enugu, Enugu State, Nigeria','1982-10-03','female','Nigerian','Ibrahim Nwankwo','+234-884-942-7771','2025-05-28 23:00:00','active',NULL,NULL,'rotating',144193.37,NULL,'TIN-34057019','3621898867','Peanuts',NULL,'2025-08-26',4.4,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('a65543a1-6460-39e7-9a3e-6ea33810b8a5',NULL,'Ngozi Chukwu','ngozi.chukwu.82@sweettooth.com','EMP-ABJ004-0082','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,4,NULL,'+234-802-162-3604','319 Sauer Road, Abuja, FCT State, Nigeria','1994-05-27','female','Nigerian','Tunde Aliyu','+234-856-118-3436','2025-11-19 23:00:00','active',NULL,NULL,'rotating',194093.77,NULL,'TIN-51841141','7317926575',NULL,NULL,'2025-09-18',3.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('a717db71-9253-3954-a617-f9a0a3a49263',NULL,'Blessing Nwankwo','blessing.nwankwo.76@sweettooth.com','EMP-ABJ004-0076','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,1,NULL,'+234-835-949-7760','73137 Ahmed Brook, Abuja, FCT State, Nigeria','1996-09-13','female','Nigerian','Oluwaseun Chukwu','+234-861-328-3632','2025-11-05 23:00:00','active',NULL,NULL,'morning',85176.25,NULL,'TIN-33828164','0664741252',NULL,NULL,'2025-12-18',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b32e9c70-7c0a-3af6-a003-05052959ad00',NULL,'Chioma Ogunleye','chioma.ogunleye.60@sweettooth.com','EMP-LAG003-0060','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,1,NULL,'+234-866-181-1198','14756 Corkery Trail Apt. 926, Lagos, Lagos State, Nigeria','1991-11-03','female','Nigerian','Emeka Okafor','+234-856-223-2969','2023-02-14 23:00:00','active',NULL,NULL,'afternoon',348351.27,NULL,'TIN-95649878','2940247178',NULL,NULL,'2025-08-01',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b3a9af48-233f-3337-803f-05b3a3bc1eb8',NULL,'Ngozi Chukwu','ngozi.chukwu.75@sweettooth.com','EMP-ABJ004-0075','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,1,NULL,'+234-842-882-2632','2894 Sauer Orchard, Abuja, FCT State, Nigeria','1994-07-25','female','Nigerian','Chukwuemeka Mohammed','+234-838-188-6038','2024-07-21 23:00:00','active',NULL,NULL,'rotating',253443.68,NULL,'TIN-61327019','9772296505',NULL,NULL,'2025-12-24',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b48e2fe7-724b-30d4-8d28-45834cd96c3d',NULL,'Tunde Nwankwo','tunde.nwankwo.46@sweettooth.com','EMP-PHC002-0046','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,2,NULL,'+234-823-569-1448','6598 Ruecker Junctions, Port Harcourt, Rivers State, Nigeria','1992-11-30','male','Nigerian','Amina Nwankwo','+234-830-840-4034','2024-02-09 23:00:00','active',NULL,NULL,'rotating',186087.34,NULL,'TIN-12219253','2118051746',NULL,NULL,'2025-10-21',4.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b4e76365-9665-34ff-b271-55ca7249aabc',NULL,'Kemi Mohammed','kemi.mohammed.7@sweettooth.com','EMP-PHC002-0007','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,1,NULL,'+234-864-205-7229','9386 Annabel Causeway, Port Harcourt, Rivers State, Nigeria','1992-09-21','female','Nigerian','Emeka Adebayo','+234-851-640-7156','2023-08-02 23:00:00','active',NULL,NULL,'flexible',203630.49,NULL,'TIN-75725828','8870276560',NULL,NULL,'2025-10-08',4.3,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b5bc19a6-ea8f-399d-a779-ec200ebf3cc7',NULL,'Chioma Adebayo','chioma.adebayo.16@sweettooth.com','EMP-CAL001-0016','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,4,NULL,'+234-810-996-3292','78249 Baumbach Turnpike, Calabar, Cross River State, Nigeria','1984-03-04','female','Nigerian','Tunde Okafor','+234-889-908-9948','2023-11-06 23:00:00','active',NULL,NULL,'flexible',134363.49,NULL,'TIN-75249224','8856262237','None',NULL,'2025-07-31',4.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b69eea6e-ea45-331b-9b13-3d33a4a1dd9b',NULL,'Kemi Eze','kemi.eze.89@sweettooth.com','EMP-ABJ004-0089','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,7,NULL,'+234-909-195-4906','85686 Kreiger Roads Suite 809, Abuja, FCT State, Nigeria','2000-06-04','female','Nigerian','Tunde Okoro','+234-836-311-4906','2024-05-04 23:00:00','active',NULL,NULL,'afternoon',264797.34,NULL,'TIN-37809984','8945297367',NULL,NULL,'2025-11-14',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b768adb3-05b3-3442-a36b-66b2d4aaac30',NULL,'Chukwuemeka Mohammed','chukwuemeka.mohammed.98@sweettooth.com','EMP-ENU005-0098','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,4,NULL,'+234-841-725-4039','320 Rachelle Neck Suite 310, Enugu, Enugu State, Nigeria','1994-06-17','male','Nigerian','Fatima Okoro','+234-853-785-1567','2023-11-29 23:00:00','active',NULL,NULL,'afternoon',80589.23,NULL,'TIN-56429982','3309673319',NULL,NULL,'2025-09-09',3.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b79c2690-9a25-3c70-bd4b-b575bea73cb6',NULL,'Blessing Chukwu','blessing.chukwu.34@sweettooth.com','EMP-CAL001-0034','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,4,NULL,'+234-887-705-1540','99045 Angelina Forest, Calabar, Cross River State, Nigeria','1997-01-14','female','Nigerian','Chigozie Okafor','+234-830-794-7655','2024-04-29 23:00:00','active',NULL,NULL,'morning',256747.09,NULL,'TIN-90839253','4343763025',NULL,NULL,'2025-07-19',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('b9806d0e-2cba-34b3-b232-ba0172a13b9a',NULL,'Ngozi Ogunleye','ngozi.ogunleye.81@sweettooth.com','EMP-ABJ004-0081','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,4,NULL,'+234-907-475-3261','127 Sylvester Station Apt. 161, Abuja, FCT State, Nigeria','1988-04-25','female','Nigerian','Chukwuemeka Okafor','+234-893-440-8944','2025-05-08 23:00:00','active',NULL,NULL,'rotating',126189.38,NULL,'TIN-44247700','5143534582','Shellfish',NULL,'2025-12-12',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('bc19e214-3daf-359a-beca-f28918110739',NULL,'Amina Okafor','amina.okafor.8@sweettooth.com','EMP-LAG003-0008','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,1,NULL,'+234-800-566-9358','156 Graham Trafficway, Lagos, Lagos State, Nigeria','1998-01-12','female','Nigerian','Emeka Adebayo','+234-814-895-7427','2023-07-26 23:00:00','active',NULL,NULL,'afternoon',307300.62,NULL,'TIN-19576140','6128649874',NULL,NULL,'2025-11-14',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('bdbc5752-938c-35c6-b41d-a5c900252b9c',NULL,'Chigozie Chukwu','chigozie.chukwu.103@sweettooth.com','EMP-ENU005-0103','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,5,NULL,'+234-840-806-4130','795 Fahey Forge Suite 337, Enugu, Enugu State, Nigeria','1999-09-27','male','Nigerian','Chioma Chukwu','+234-823-332-4624','2024-04-26 23:00:00','active',NULL,NULL,'flexible',144241.51,NULL,'TIN-99825186','0663985633',NULL,NULL,'2025-08-05',3.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('bed11508-7f85-3920-9225-b1d754b59369',NULL,'Emeka Adebayo','emeka.adebayo.24@sweettooth.com','EMP-ABJ004-0024','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,1,NULL,'+234-825-282-9670','1520 Zieme Curve, Abuja, FCT State, Nigeria','1994-03-15','male','Nigerian','Chioma Okafor','+234-878-487-9383','2024-02-14 23:00:00','active',NULL,NULL,'flexible',318474.60,NULL,'TIN-53871210','1939565433','None',NULL,'2025-10-22',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('c0ad7a09-5959-3bbc-a59c-e0c5c658b6b0',NULL,'Nneka Adebayo','nneka.adebayo.63@sweettooth.com','EMP-LAG003-0063','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,2,NULL,'+234-877-956-2240','430 Cary Curve, Lagos, Lagos State, Nigeria','1991-04-19','female','Nigerian','Chigozie Okoro','+234-803-237-4792','2023-01-11 23:00:00','active',NULL,NULL,'rotating',286239.48,NULL,'TIN-16291101','6837340355','Peanuts',NULL,'2025-11-27',5.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('c19ef4e3-3b77-3259-97bf-b625b45e285f',NULL,'Blessing Eze','blessing.eze.43@sweettooth.com','EMP-PHC002-0043','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,1,NULL,'+234-820-446-5479','765 Stracke Lock, Port Harcourt, Rivers State, Nigeria','1993-08-08','female','Nigerian','Ibrahim Chukwu','+234-878-479-4494','2024-03-26 23:00:00','active',NULL,NULL,'morning',269054.42,NULL,'TIN-99666934','7953136599',NULL,NULL,'2025-09-09',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('c6be9043-8066-394d-9d05-9d9585c16ea0',NULL,'Tunde Johnson','tunde.johnson.79@sweettooth.com','EMP-ABJ004-0079','019b6d6a-1944-725f-9136-b5c40cd21ca0',1,NULL,2,NULL,'+234-844-981-1001','82011 Izabella Valley, Abuja, FCT State, Nigeria','2003-07-29','male','Nigerian','Fatima Nwankwo','+234-810-591-4521','2024-01-11 23:00:00','active',NULL,NULL,'rotating',212393.66,NULL,'TIN-63834924','5133048500',NULL,NULL,'2025-09-27',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('caa36c55-d5fd-34d1-9f1d-0972938ed718',NULL,'Nneka Ogunleye','nneka.ogunleye.32@sweettooth.com','EMP-CAL001-0032','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,2,NULL,'+234-827-307-8124','3453 Ward Inlet, Calabar, Cross River State, Nigeria','1994-03-06','female','Nigerian','Ibrahim Bello','+234-806-539-4658','2025-03-21 23:00:00','active',NULL,NULL,'afternoon',178388.90,NULL,'TIN-19011696','2960941528',NULL,NULL,'2025-09-20',4.2,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('cb29e3c5-e8f9-3b1d-8b2c-71a5e78bbcc9',NULL,'Ngozi Williams','ngozi.williams.93@sweettooth.com','EMP-ENU005-0093','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,1,NULL,'+234-873-806-3636','128 Roberts Rapid Apt. 153, Enugu, Enugu State, Nigeria','1998-03-10','female','Nigerian','Emeka Johnson','+234-875-196-4073','2025-11-17 23:00:00','active',NULL,NULL,'rotating',325948.56,NULL,'TIN-35767897','8487827724',NULL,NULL,'2025-09-02',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('d206fc44-7038-314d-a866-e589a5f8cb9c',NULL,'Ngozi Aliyu','ngozi.aliyu.61@sweettooth.com','EMP-LAG003-0061','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,1,NULL,'+234-851-222-5494','388 Duane Cove Suite 232, Lagos, Lagos State, Nigeria','1994-11-05','female','Nigerian','Ibrahim Okafor','+234-834-112-1227','2025-03-06 23:00:00','active',NULL,NULL,'morning',321739.44,NULL,'TIN-20380675','9776623709',NULL,NULL,'2025-09-23',4.3,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('d2f8919c-5463-3de1-a5dd-d9ae0e0634df',NULL,'Abubakar Chukwu','abubakar.chukwu.31@sweettooth.com','EMP-CAL001-0031','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,2,NULL,'+234-891-640-3189','215 Wilkinson Plain Suite 484, Calabar, Cross River State, Nigeria','1984-03-03','male','Nigerian','Fatima Chukwu','+234-811-803-7047','2022-12-29 23:00:00','active',NULL,NULL,'afternoon',108026.54,NULL,'TIN-81113951','3360739676',NULL,NULL,'2025-12-11',4.4,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('d311efcc-4ade-3ca1-9ab0-1b1ff56b5d82',NULL,'Tunde Mohammed','tunde.mohammed.57@sweettooth.com','EMP-PHC002-0057','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,7,NULL,'+234-835-754-1405','3770 Larkin Mountains Suite 563, Port Harcourt, Rivers State, Nigeria','1990-12-01','male','Nigerian','Hauwa Okoro','+234-862-836-8041','2025-09-18 23:00:00','active',NULL,NULL,'morning',247576.49,NULL,'TIN-37148093','4368861285',NULL,NULL,'2025-08-16',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('d406a8a6-a347-3814-9821-98774d969671',NULL,'Amina Chukwu','amina.chukwu.105@sweettooth.com','EMP-ENU005-0105','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,7,NULL,'+234-863-307-2090','43266 Haylie Trace Suite 549, Enugu, Enugu State, Nigeria','1999-08-03','female','Nigerian','Kunle Eze','+234-838-852-3852','2024-11-15 23:00:00','active',NULL,NULL,'rotating',186169.58,NULL,'TIN-72282257','5956007950',NULL,NULL,'2025-11-03',4.4,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('d82f4d55-91cc-30b6-8818-652c44e34260',NULL,'Chigozie Ogunleye','chigozie.ogunleye.44@sweettooth.com','EMP-PHC002-0044','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,1,NULL,'+234-813-574-9801','469 Annabel Shoal, Port Harcourt, Rivers State, Nigeria','1996-02-13','male','Nigerian','Chioma Nwankwo','+234-833-771-7108','2025-05-02 23:00:00','active',NULL,NULL,'afternoon',92908.14,NULL,'TIN-86354204','0586896610',NULL,NULL,'2025-11-24',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('d84cdb00-2f5e-3ef4-982c-76c06c930df2',NULL,'Fatima Nwankwo','fatima.nwankwo.72@sweettooth.com','EMP-LAG003-0072','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,7,NULL,'+234-810-191-5699','81283 Howe Cape Suite 611, Lagos, Lagos State, Nigeria','2003-09-23','female','Nigerian','Ibrahim Aliyu','+234-800-251-3863','2023-08-18 23:00:00','active',NULL,NULL,'afternoon',142725.33,NULL,'TIN-84081293','7512349483',NULL,NULL,'2025-12-13',4.4,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('d908c1fa-a982-3421-8f63-06468001d6e0',NULL,'Folake Eze','folake.eze.28@sweettooth.com','EMP-CAL001-0028','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,1,NULL,'+234-906-814-7038','417 Goyette Village, Calabar, Cross River State, Nigeria','1994-02-02','female','Nigerian','Oluwaseun Bello','+234-880-281-8030','2024-11-22 23:00:00','active',NULL,NULL,'morning',226305.35,NULL,'TIN-12481819','3165195875','Peanuts',NULL,'2025-11-10',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('de5d2ba5-19ef-3993-b262-f4eb02c1d35d',NULL,'Chigozie Ogunleye','chigozie.ogunleye.70@sweettooth.com','EMP-LAG003-0070','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,5,NULL,'+234-868-966-3309','894 Crawford Plains Suite 742, Lagos, Lagos State, Nigeria','1999-03-02','male','Nigerian','Nneka Adebayo','+234-891-820-2945','2025-09-21 23:00:00','active',NULL,NULL,'morning',281267.57,NULL,'TIN-58496499','0063333597','Shellfish',NULL,'2025-08-08',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('e657f8fb-6b49-30e6-801e-e31b7f6ad1e9',NULL,'Hauwa Mohammed','hauwa.mohammed.100@sweettooth.com','EMP-ENU005-0100','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,4,NULL,'+234-830-886-3083','686 Botsford Trail Apt. 429, Enugu, Enugu State, Nigeria','1991-06-23','female','Nigerian','Oluwaseun Johnson','+234-880-866-1020','2025-11-16 23:00:00','active',NULL,NULL,'rotating',191544.26,NULL,'TIN-17447942','4718387779',NULL,NULL,'2025-12-01',3.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('e661c7b3-2125-36d4-aa57-bcd810372c62',NULL,'Oluwaseun Johnson','oluwaseun.johnson.54@sweettooth.com','EMP-PHC002-0054','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,5,NULL,'+234-817-968-1912','754 Gottlieb Plaza, Port Harcourt, Rivers State, Nigeria','1989-03-02','male','Nigerian','Kemi Nwankwo','+234-902-225-4387','2023-08-24 23:00:00','active',NULL,NULL,'flexible',332326.41,NULL,'TIN-68548798','2888981279',NULL,NULL,'2025-07-19',4.9,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('e91615a0-9451-3708-9365-f45a8c130410',NULL,'Kunle Nwankwo','kunle.nwankwo.26@sweettooth.com','EMP-CAL001-0026','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,1,NULL,'+234-808-962-4483','7067 Kathlyn Glen Apt. 867, Calabar, Cross River State, Nigeria','1998-07-17','male','Nigerian','Chioma Johnson','+234-906-677-6811','2025-11-16 23:00:00','active',NULL,NULL,'rotating',93516.31,NULL,'TIN-95762998','1752870207',NULL,NULL,'2025-07-18',4.7,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('e988b397-81c0-3a7d-abe4-5861d7a0c7e1',NULL,'Blessing Adebayo','blessing.adebayo.66@sweettooth.com','EMP-LAG003-0066','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,4,NULL,'+234-857-737-6107','9879 Cassin Wall, Lagos, Lagos State, Nigeria','1985-10-21','female','Nigerian','Chukwuemeka Williams','+234-850-476-3767','2023-07-13 23:00:00','active',NULL,NULL,'afternoon',318798.18,NULL,'TIN-75326145','4597686476',NULL,NULL,'2025-09-23',5.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('ebea4a0c-f4dd-3d83-bd48-f790abd49d36',NULL,'Kunle Okoro','kunle.okoro.41@sweettooth.com','EMP-CAL001-0041','019b6d6a-191b-7068-817b-94e02d09e859',1,NULL,7,NULL,'+234-813-484-1570','383 Cristobal Drive, Calabar, Cross River State, Nigeria','1995-05-24','male','Nigerian','Ada Williams','+234-875-702-6460','2025-04-28 23:00:00','active',NULL,NULL,'morning',258936.73,NULL,'TIN-80358548','2619344142',NULL,NULL,'2025-12-01',3.6,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('ec548e0f-52c6-3df0-bfd8-edd754a199b7',NULL,'Yusuf Johnson','yusuf.johnson.90@sweettooth.com','EMP-ENU005-0090','019b6d6a-194f-71bf-a740-9f45370d3348',1,NULL,1,NULL,'+234-862-923-7877','3610 Lesch Grove Apt. 563, Enugu, Enugu State, Nigeria','1999-10-21','male','Nigerian','Kemi Bello','+234-858-845-3982','2024-03-24 23:00:00','active',NULL,NULL,'morning',285179.81,NULL,'TIN-49509787','6123924348',NULL,NULL,'2025-09-27',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('ef25fc4d-f2e8-3257-8f2a-742d60f81878',NULL,'Ada Okoro','ada.okoro.47@sweettooth.com','EMP-PHC002-0047','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,2,NULL,'+234-832-597-7979','8505 Balistreri Park, Port Harcourt, Rivers State, Nigeria','1986-09-10','female','Nigerian','Yusuf Chukwu','+234-804-356-1637','2025-04-13 23:00:00','active',NULL,NULL,'morning',109843.24,NULL,'TIN-73116447','7915941037',NULL,NULL,'2025-09-20',4.0,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('f6360c99-6102-31c5-a82f-8d2db81e06f0',NULL,'Tunde Bello','tunde.bello.56@sweettooth.com','EMP-PHC002-0056','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,7,NULL,'+234-884-938-7770','1863 Rempel Haven, Port Harcourt, Rivers State, Nigeria','1993-08-18','male','Nigerian','Folake Okafor','+234-908-590-5231','2025-04-20 23:00:00','active',NULL,NULL,'flexible',340597.91,NULL,'TIN-30831128','5548016822','Shellfish',NULL,'2025-08-07',4.8,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('f91b915c-fea4-38f2-9e43-a24f1fa8a851',NULL,'Kunle Eze','kunle.eze.59@sweettooth.com','EMP-LAG003-0059','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,1,NULL,'+234-805-153-8984','81792 Stehr Village Suite 035, Lagos, Lagos State, Nigeria','1998-11-20','male','Nigerian','Ngozi Eze','+234-815-588-6176','2025-02-09 23:00:00','active',NULL,NULL,'afternoon',106562.74,NULL,'TIN-80598489','8106724931',NULL,NULL,'2025-12-01',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('fa020d09-2655-3943-8afc-15bd8a9d68f1',NULL,'Folake Mohammed','folake.mohammed.12@sweettooth.com','EMP-PHC002-0012','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,7,NULL,'+234-867-220-6687','665 Xzavier Courts Apt. 621, Port Harcourt, Rivers State, Nigeria','1987-10-01','female','Nigerian','Abubakar Okafor','+234-855-277-4203','2024-03-31 23:00:00','active',NULL,NULL,'flexible',306933.49,NULL,'TIN-14619457','1952468489',NULL,NULL,'2025-11-27',4.3,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('fbdff02f-0753-3575-9388-a315ba483036',NULL,'Ada Okoro','ada.okoro.68@sweettooth.com','EMP-LAG003-0068','019b6d6a-192d-705f-8b98-4e27aaf972b5',1,NULL,4,NULL,'+234-887-379-2041','6620 Pollich Corner, Lagos, Lagos State, Nigeria','1993-02-27','female','Nigerian','Chukwuemeka Williams','+234-871-744-8084','2023-10-22 23:00:00','active',NULL,NULL,'rotating',289056.83,NULL,'TIN-44681837','1613387740',NULL,NULL,'2025-08-27',4.1,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('fef5c27b-70cd-38ce-a3c8-fd46f966c382',NULL,'Chioma Mohammed','chioma.mohammed.22@sweettooth.com','EMP-PHC002-0022','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,1,NULL,'+234-837-375-8901','33245 Shaina Meadow Suite 723, Port Harcourt, Rivers State, Nigeria','1987-10-21','female','Nigerian','Emeka Mohammed','+234-897-714-1470','2023-05-01 23:00:00','active',NULL,NULL,'rotating',141611.56,NULL,'TIN-14536340','7439848440',NULL,NULL,'2025-08-06',4.3,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48'),
('ffc81e79-0851-3df5-9b26-5c48dbacc2ea',NULL,'Yusuf Chukwu','yusuf.chukwu.45@sweettooth.com','EMP-PHC002-0045','019b6d6a-1922-7079-99a9-842d7c4a1fc3',1,NULL,1,NULL,'+234-892-864-3764','2657 Maureen Fords Apt. 062, Port Harcourt, Rivers State, Nigeria','1996-03-17','male','Nigerian','Folake Mohammed','+234-897-194-5480','2024-12-23 23:00:00','active',NULL,NULL,'afternoon',293530.94,NULL,'TIN-97189477','4206726740',NULL,NULL,'2025-09-16',4.5,'employee',NULL,'$2y$12$gNIAOQfpWfm.qabroGn3xe8nt8TpiIeFtMWBqN89sKeXxeGP.sk9C',NULL,NULL,NULL,'2025-12-30 04:00:48',NULL,'2025-12-30 04:00:48','2025-12-30 04:00:48');
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

-- Dump completed on 2026-01-03  7:42:16
