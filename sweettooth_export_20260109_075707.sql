/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.3-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: sweettooth
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_periods`
--

LOCK TABLES `accounting_periods` WRITE;
/*!40000 ALTER TABLE `accounting_periods` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `accounting_periods` VALUES
(1,2026,1,'2026-01-01','2026-01-31','open',NULL,NULL,NULL,NULL,'2026-01-09 04:34:19','2026-01-09 04:34:19');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appraisal_cycles`
--

LOCK TABLES `appraisal_cycles` WRITE;
/*!40000 ALTER TABLE `appraisal_cycles` DISABLE KEYS */;
set autocommit=0;
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
('019b9efb-b43d-73c7-95bd-0687cf05c811','SweetTooth Calabar','CAL-001','12 Marian Road, Calabar Municipal','+234-809-012-3456','calabar@sweettooth.com','Calabar flagship store with full production and sales',NULL,'Nigeria','Cross River','Calabar','540001','Africa/Lagos',1,0,NULL,'2026-01-08 19:00:54','2026-01-08 19:00:54',NULL),
('019b9efb-b447-73e3-ac48-56618b9d7edb','SweetTooth Port Harcourt','PHC-002','78 Trans Amadi Industrial Layout','+234-803-456-7890','portharcourt@sweettooth.com','Port Harcourt main branch',NULL,'Nigeria','Rivers','Port Harcourt','500001','Africa/Lagos',1,0,NULL,'2026-01-08 19:00:54','2026-01-08 19:00:54',NULL),
('019b9efb-b453-7326-aefa-4c9b271f158a','SweetTooth Lagos','LAG-003','45 Admiralty Way, Lekki Phase 1','+234-801-234-5678','lagos@sweettooth.com','Lagos head office and production center',NULL,'Nigeria','Lagos','Lagos','101001','Africa/Lagos',1,0,NULL,'2026-01-08 19:00:54','2026-01-08 19:00:54',NULL),
('019b9efb-b469-7219-8b97-cb9f291b4be4','SweetTooth Abuja','ABJ-004','23 Gimbiya Street, Area 11, Garki','+234-802-345-6789','abuja@sweettooth.com','Abuja branch with gelato specialty',NULL,'Nigeria','FCT','Abuja','900001','Africa/Lagos',1,0,NULL,'2026-01-08 19:00:54','2026-01-08 19:00:54',NULL),
('019b9efb-b473-7177-aceb-a5d244ef3f8c','SweetTooth Enugu','ENU-005','34 Ogui Road, New Haven','+234-806-789-0123','enugu@sweettooth.com','Enugu branch serving South-East region',NULL,'Nigeria','Enugu','Enugu','400001','Africa/Lagos',1,0,NULL,'2026-01-08 19:00:54','2026-01-08 19:00:54',NULL);
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
('laravel_cache_dashboard:CornerStoreDashboard:019b9efb-b43d-73c7-95bd-0687cf05c811:corner_store_recent_transactions_10','a:0:{}',1767945411),
('laravel_cache_dashboard:CornerStoreDashboard:019b9efb-b43d-73c7-95bd-0687cf05c811:corner_store_top_items_10','a:0:{}',1767945411),
('laravel_cache_dashboard:CornerStoreDashboard:019b9efb-b43d-73c7-95bd-0687cf05c811:corner_store_total_sales','i:0;',1767945411),
('laravel_cache_dashboard:CornerStoreDashboard:019b9efb-b43d-73c7-95bd-0687cf05c811:corner_store_transaction_count','i:0;',1767945411),
('laravel_cache_settings_model_App\\Models\\BranchCurrencyLocalization','N;',1767942111),
('laravel_cache_settings_model_App\\Models\\GlobalCurrencyLocalization','N;',1767942111);
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
('019b9efc-0684-7030-a1b7-aaf80281ccf2','Sales','Departments focused on selling products and services, customer acquisition, and revenue generation.','2026-01-08 19:01:15','2026-01-08 19:01:15'),
('019b9efc-068d-7285-8752-b315fe89df7d','Production','Departments responsible for manufacturing, production processes, and quality control.','2026-01-08 19:01:15','2026-01-08 19:01:15'),
('019b9efc-06a4-72e5-a6f7-a4facf78a1fc','Support','Departments providing assistance, customer service, and technical support services.','2026-01-08 19:01:15','2026-01-08 19:01:15');
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
(1,1,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(2,1,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(3,1,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(4,1,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2026-01-08 19:01:15','2026-01-08 19:01:38'),
(5,1,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2026-01-08 19:01:15','2026-01-08 19:01:39'),
(6,1,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2026-01-08 19:01:15','2026-01-08 19:01:39'),
(7,1,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(8,1,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(9,1,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(10,1,'Kitchen Module','kitchen-module','branch-dashboard.production.module.index','home',10,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(11,1,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',11,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(12,1,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',12,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(13,1,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',13,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(14,2,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(15,2,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(16,2,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(17,2,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2026-01-08 19:01:16','2026-01-08 19:01:39'),
(18,2,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2026-01-08 19:01:16','2026-01-08 19:01:39'),
(19,2,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2026-01-08 19:01:16','2026-01-08 19:01:39'),
(20,2,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(21,2,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(22,2,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(23,2,'Kitchen Module','kitchen-module','branch-dashboard.production.module.index','home',10,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(24,2,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',11,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(25,2,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',12,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(26,2,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',13,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(27,3,'Products','products','branch-dashboard.production.products','cube',1,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(28,3,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(29,3,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(30,3,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2026-01-08 19:01:16','2026-01-08 19:01:39'),
(31,3,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2026-01-08 19:01:16','2026-01-08 19:01:39'),
(32,3,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2026-01-08 19:01:16','2026-01-08 19:01:39'),
(33,3,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',7,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(34,3,'View Callbacks','callbacks-index','branch-dashboard.production.callbacks.index','arrow-path',8,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(35,3,'Approve Sales Callbacks','callbacks-approve','branch-dashboard.production.callbacks.approve-sales-callbacks','check-circle',9,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(36,3,'Kitchen Module','kitchen-module','branch-dashboard.production.module.index','home',10,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(37,3,'Operations Reports','reports-operations','branch-dashboard.production.reports.operations','chart-bar',11,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(38,3,'Performance Reports','reports-performance','branch-dashboard.production.reports.performance','star',12,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(39,3,'Planning Reports','reports-planning','branch-dashboard.production.reports.planning','server',13,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(40,4,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(41,4,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(42,4,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(43,4,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(44,5,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(45,5,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(46,5,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(47,5,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(48,6,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
(49,6,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
(50,6,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
(51,6,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
(52,1,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2026-01-08 19:01:38','2026-01-08 19:01:38'),
(53,1,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2026-01-08 19:01:38','2026-01-08 19:01:38'),
(54,1,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2026-01-08 19:01:38','2026-01-08 19:01:38'),
(55,2,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2026-01-08 19:01:39','2026-01-08 19:01:39'),
(56,2,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2026-01-08 19:01:39','2026-01-08 19:01:39'),
(57,2,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2026-01-08 19:01:39','2026-01-08 19:01:39'),
(58,3,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2026-01-08 19:01:39','2026-01-08 19:01:39'),
(59,3,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2026-01-08 19:01:39','2026-01-08 19:01:39'),
(60,3,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2026-01-08 19:01:39','2026-01-08 19:01:39');
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
(1,4,'019b9efc-4095-71c4-945b-fce6e608db8f',1,NULL,0,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(2,4,'019b9efc-4100-70ae-b9a2-1b366d55acc6',1,NULL,1,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(3,4,'019b9efc-411d-720a-940c-eae17b48e3b8',1,NULL,2,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(4,4,'019b9efc-412e-720c-a191-17b14717a68e',1,NULL,3,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(5,4,'019b9efc-414c-72f2-ab3c-cb157afccc89',1,NULL,4,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(6,4,'019b9efc-415b-7212-a0f8-c2150414e426',1,NULL,5,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(7,5,'019b9efc-4095-71c4-945b-fce6e608db8f',1,NULL,0,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(8,5,'019b9efc-4100-70ae-b9a2-1b366d55acc6',1,NULL,1,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(9,5,'019b9efc-411d-720a-940c-eae17b48e3b8',1,NULL,2,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(10,5,'019b9efc-412e-720c-a191-17b14717a68e',1,NULL,3,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(11,5,'019b9efc-413e-729d-a4b3-52fd22463621',1,NULL,4,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(12,6,'019b9efc-41e5-728d-8bc9-e9e9d9788c8e',1,NULL,0,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(13,6,'019b9efc-4231-73bb-96e4-f667f957e148',1,NULL,1,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(14,6,'019b9efc-42a0-7200-91a5-4a3e4b72fc32',1,NULL,2,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(15,6,'019b9efc-42ba-71cc-b884-f553e035afc1',1,NULL,3,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(16,6,'019b9efc-42cf-720e-bbc0-2a19e15fbd1f',1,NULL,4,'2026-01-08 19:01:31','2026-01-08 19:01:31'),
(17,6,'019b9efc-42da-705b-af43-c4f26f2d269d',1,NULL,5,'2026-01-08 19:01:31','2026-01-08 19:01:31');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `departments` VALUES
(1,NULL,'019b9efc-068d-7285-8752-b315fe89df7d','Kitchen','kitchen','Prepares food for Till, Confectionaries, Corner Store',1,NULL,0,NULL,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(2,NULL,'019b9efc-068d-7285-8752-b315fe89df7d','Gelato Production','gelato-production','Makes gelato/ice cream',1,NULL,0,NULL,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(3,NULL,'019b9efc-068d-7285-8752-b315fe89df7d','Confectionaries Production','confectionaries-production','Makes confectionery items',1,NULL,0,NULL,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(4,NULL,'019b9efc-0684-7030-a1b7-aaf80281ccf2','Till','till','Sells ready-made snacks',1,NULL,0,NULL,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(5,NULL,'019b9efc-0684-7030-a1b7-aaf80281ccf2','Corner Store','corner-store','On-demand food sales',1,NULL,0,NULL,'2026-01-08 19:01:16','2026-01-08 19:01:16'),
(6,NULL,'019b9efc-0684-7030-a1b7-aaf80281ccf2','Confectionaries Sales','confectionaries-sales','Sells confectionery items',1,NULL,0,NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
(7,NULL,'019b9efc-06a4-72e5-a6f7-a4facf78a1fc','Inventory/Store','inventorystore','Manages all stock',1,NULL,0,NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
(8,NULL,'019b9efc-06a4-72e5-a6f7-a4facf78a1fc','HR','hr','Human resources (corporate level)',1,NULL,0,NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
(9,'019b9efb-b43d-73c7-95bd-0687cf05c811','019b9efc-06a4-72e5-a6f7-a4facf78a1fc','Human Resources','hr','Manages employee records, payroll, leave management, and organizational structure',1,NULL,0,NULL,'2026-01-09 02:55:22','2026-01-09 02:55:22'),
(10,'019b9efb-b43d-73c7-95bd-0687cf05c811','019b9efc-06a4-72e5-a6f7-a4facf78a1fc','Accounting','accounting','Manages financial records and accounting across all departments',1,NULL,0,NULL,'2026-01-09 02:55:22','2026-01-09 02:55:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gl_accounts`
--

LOCK TABLES `gl_accounts` WRITE;
/*!40000 ALTER TABLE `gl_accounts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gl_accounts` VALUES
(3,'3010','Opening Balance Equity','equity','equity','Opening balance equity for initial postings',0.00,4344601.30,'credit',0,NULL,1,1,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(4,'1310','Raw Materials Inventory','asset','current_asset','Raw Materials Inventory',3813533.20,0.00,'debit',0,NULL,1,1,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(5,'1340','Supplies Inventory','asset','current_asset','Supplies Inventory',474467.50,0.00,'debit',0,NULL,1,1,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(6,'1300','Other Inventory','asset','current_asset','Other Inventory',56600.60,0.00,'debit',0,NULL,1,1,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gl_entries`
--

LOCK TABLES `gl_entries` WRITE;
/*!40000 ALTER TABLE `gl_entries` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gl_entries` VALUES
(1,4,1,'inventory_valuation','App\\Models\\Stock',NULL,'INV-VAL-20260109-053842','Inventory Opening Balance - Jan 2026 - Raw Materials',3813533.20,0.00,'2026-01-09 05:38:42','posted',0,NULL,NULL,'019b9efb-b3ff-7394-8cbb-81be9ac84154','App\\Models\\User','019b9efb-b3ff-7394-8cbb-81be9ac84154',NULL,'2026-01-09 04:38:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(2,3,1,'inventory_valuation','App\\Models\\Stock',NULL,'INV-VAL-20260109-053842','Inventory Opening Balance - Jan 2026 - Raw Materials',0.00,3813533.20,'2026-01-09 05:38:42','posted',0,NULL,NULL,'019b9efb-b3ff-7394-8cbb-81be9ac84154','App\\Models\\User','019b9efb-b3ff-7394-8cbb-81be9ac84154',NULL,'2026-01-09 04:38:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(3,5,1,'inventory_valuation','App\\Models\\Stock',NULL,'INV-VAL-20260109-053842','Inventory Opening Balance - Jan 2026 - Supplies',474467.50,0.00,'2026-01-09 05:38:42','posted',0,NULL,NULL,'019b9efb-b3ff-7394-8cbb-81be9ac84154','App\\Models\\User','019b9efb-b3ff-7394-8cbb-81be9ac84154',NULL,'2026-01-09 04:38:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(4,3,1,'inventory_valuation','App\\Models\\Stock',NULL,'INV-VAL-20260109-053842','Inventory Opening Balance - Jan 2026 - Supplies',0.00,474467.50,'2026-01-09 05:38:42','posted',0,NULL,NULL,'019b9efb-b3ff-7394-8cbb-81be9ac84154','App\\Models\\User','019b9efb-b3ff-7394-8cbb-81be9ac84154',NULL,'2026-01-09 04:38:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(5,6,1,'inventory_valuation','App\\Models\\Stock',NULL,'INV-VAL-20260109-053842','Inventory Opening Balance - Jan 2026 - Other Inventory',56600.60,0.00,'2026-01-09 05:38:42','posted',0,NULL,NULL,'019b9efb-b3ff-7394-8cbb-81be9ac84154','App\\Models\\User','019b9efb-b3ff-7394-8cbb-81be9ac84154',NULL,'2026-01-09 04:38:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42'),
(6,3,1,'inventory_valuation','App\\Models\\Stock',NULL,'INV-VAL-20260109-053842','Inventory Opening Balance - Jan 2026 - Other Inventory',0.00,56600.60,'2026-01-09 05:38:42','posted',0,NULL,NULL,'019b9efb-b3ff-7394-8cbb-81be9ac84154','App\\Models\\User','019b9efb-b3ff-7394-8cbb-81be9ac84154',NULL,'2026-01-09 04:38:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-09 04:38:42','2026-01-09 04:38:42');
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
(1,'019b9efb-b43d-73c7-95bd-0687cf05c811','Sugar - White Granulated','CAL-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-01-08 19:01:24','2026-01-08 19:01:24'),
(2,'019b9efb-b43d-73c7-95bd-0687cf05c811','Flour - All Purpose','CAL-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-01-08 19:01:24','2026-01-08 19:01:24'),
(3,'019b9efb-b43d-73c7-95bd-0687cf05c811','Cocoa Powder - Premium Dark','CAL-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-01-08 19:01:24','2026-01-08 19:01:24'),
(4,'019b9efb-b43d-73c7-95bd-0687cf05c811','Butter - Salted','CAL-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-01-08 19:01:24','2026-01-08 19:01:24'),
(5,'019b9efb-b43d-73c7-95bd-0687cf05c811','Eggs - Large Grade A','CAL-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-01-08 19:01:24','2026-01-08 19:01:24'),
(6,'019b9efb-b43d-73c7-95bd-0687cf05c811','Vanilla Extract - Pure','CAL-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(7,'019b9efb-b43d-73c7-95bd-0687cf05c811','Chocolate Chips - Dark','CAL-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(8,'019b9efb-b43d-73c7-95bd-0687cf05c811','Milk - Fresh Whole','CAL-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(9,'019b9efb-b43d-73c7-95bd-0687cf05c811','Cream - Heavy Whipping','CAL-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(10,'019b9efb-b43d-73c7-95bd-0687cf05c811','Yeast - Active Dry','CAL-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(11,'019b9efb-b43d-73c7-95bd-0687cf05c811','Vegetable Oil - Cooking','CAL-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(12,'019b9efb-b43d-73c7-95bd-0687cf05c811','Salt - Table Salt','CAL-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(13,'019b9efb-b43d-73c7-95bd-0687cf05c811','Cake Boxes - 10 inch','CAL-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(14,'019b9efb-b43d-73c7-95bd-0687cf05c811','Pastry Boxes - Small','CAL-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(15,'019b9efb-b43d-73c7-95bd-0687cf05c811','Paper Bags - Brown','CAL-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(16,'019b9efb-b43d-73c7-95bd-0687cf05c811','Plastic Food Containers','CAL-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(17,'019b9efb-b43d-73c7-95bd-0687cf05c811','Dishwashing Liquid - Industrial','CAL-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(18,'019b9efb-b43d-73c7-95bd-0687cf05c811','Paper Towels - Kitchen Roll','CAL-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(19,'019b9efb-b43d-73c7-95bd-0687cf05c811','Garbage Bags - Large','CAL-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(20,'019b9efb-b43d-73c7-95bd-0687cf05c811','Mixing Bowls - Stainless Steel','CAL-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(21,'019b9efb-b447-73e3-ac48-56618b9d7edb','Sugar - White Granulated','PHC-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(22,'019b9efb-b447-73e3-ac48-56618b9d7edb','Flour - All Purpose','PHC-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(23,'019b9efb-b447-73e3-ac48-56618b9d7edb','Cocoa Powder - Premium Dark','PHC-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(24,'019b9efb-b447-73e3-ac48-56618b9d7edb','Butter - Salted','PHC-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(25,'019b9efb-b447-73e3-ac48-56618b9d7edb','Eggs - Large Grade A','PHC-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(26,'019b9efb-b447-73e3-ac48-56618b9d7edb','Vanilla Extract - Pure','PHC-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(27,'019b9efb-b447-73e3-ac48-56618b9d7edb','Chocolate Chips - Dark','PHC-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(28,'019b9efb-b447-73e3-ac48-56618b9d7edb','Milk - Fresh Whole','PHC-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(29,'019b9efb-b447-73e3-ac48-56618b9d7edb','Cream - Heavy Whipping','PHC-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(30,'019b9efb-b447-73e3-ac48-56618b9d7edb','Yeast - Active Dry','PHC-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(31,'019b9efb-b447-73e3-ac48-56618b9d7edb','Vegetable Oil - Cooking','PHC-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(32,'019b9efb-b447-73e3-ac48-56618b9d7edb','Salt - Table Salt','PHC-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(33,'019b9efb-b447-73e3-ac48-56618b9d7edb','Cake Boxes - 10 inch','PHC-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(34,'019b9efb-b447-73e3-ac48-56618b9d7edb','Pastry Boxes - Small','PHC-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(35,'019b9efb-b447-73e3-ac48-56618b9d7edb','Paper Bags - Brown','PHC-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(36,'019b9efb-b447-73e3-ac48-56618b9d7edb','Plastic Food Containers','PHC-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(37,'019b9efb-b447-73e3-ac48-56618b9d7edb','Dishwashing Liquid - Industrial','PHC-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(38,'019b9efb-b447-73e3-ac48-56618b9d7edb','Paper Towels - Kitchen Roll','PHC-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(39,'019b9efb-b447-73e3-ac48-56618b9d7edb','Garbage Bags - Large','PHC-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(40,'019b9efb-b447-73e3-ac48-56618b9d7edb','Mixing Bowls - Stainless Steel','PHC-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(41,'019b9efb-b453-7326-aefa-4c9b271f158a','Sugar - White Granulated','LAG-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(42,'019b9efb-b453-7326-aefa-4c9b271f158a','Flour - All Purpose','LAG-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(43,'019b9efb-b453-7326-aefa-4c9b271f158a','Cocoa Powder - Premium Dark','LAG-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(44,'019b9efb-b453-7326-aefa-4c9b271f158a','Butter - Salted','LAG-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(45,'019b9efb-b453-7326-aefa-4c9b271f158a','Eggs - Large Grade A','LAG-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(46,'019b9efb-b453-7326-aefa-4c9b271f158a','Vanilla Extract - Pure','LAG-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(47,'019b9efb-b453-7326-aefa-4c9b271f158a','Chocolate Chips - Dark','LAG-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(48,'019b9efb-b453-7326-aefa-4c9b271f158a','Milk - Fresh Whole','LAG-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(49,'019b9efb-b453-7326-aefa-4c9b271f158a','Cream - Heavy Whipping','LAG-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(50,'019b9efb-b453-7326-aefa-4c9b271f158a','Yeast - Active Dry','LAG-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(51,'019b9efb-b453-7326-aefa-4c9b271f158a','Vegetable Oil - Cooking','LAG-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(52,'019b9efb-b453-7326-aefa-4c9b271f158a','Salt - Table Salt','LAG-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(53,'019b9efb-b453-7326-aefa-4c9b271f158a','Cake Boxes - 10 inch','LAG-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(54,'019b9efb-b453-7326-aefa-4c9b271f158a','Pastry Boxes - Small','LAG-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(55,'019b9efb-b453-7326-aefa-4c9b271f158a','Paper Bags - Brown','LAG-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(56,'019b9efb-b453-7326-aefa-4c9b271f158a','Plastic Food Containers','LAG-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(57,'019b9efb-b453-7326-aefa-4c9b271f158a','Dishwashing Liquid - Industrial','LAG-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(58,'019b9efb-b453-7326-aefa-4c9b271f158a','Paper Towels - Kitchen Roll','LAG-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(59,'019b9efb-b453-7326-aefa-4c9b271f158a','Garbage Bags - Large','LAG-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(60,'019b9efb-b453-7326-aefa-4c9b271f158a','Mixing Bowls - Stainless Steel','LAG-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(61,'019b9efb-b469-7219-8b97-cb9f291b4be4','Sugar - White Granulated','ABJ-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(62,'019b9efb-b469-7219-8b97-cb9f291b4be4','Flour - All Purpose','ABJ-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(63,'019b9efb-b469-7219-8b97-cb9f291b4be4','Cocoa Powder - Premium Dark','ABJ-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(64,'019b9efb-b469-7219-8b97-cb9f291b4be4','Butter - Salted','ABJ-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(65,'019b9efb-b469-7219-8b97-cb9f291b4be4','Eggs - Large Grade A','ABJ-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(66,'019b9efb-b469-7219-8b97-cb9f291b4be4','Vanilla Extract - Pure','ABJ-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(67,'019b9efb-b469-7219-8b97-cb9f291b4be4','Chocolate Chips - Dark','ABJ-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(68,'019b9efb-b469-7219-8b97-cb9f291b4be4','Milk - Fresh Whole','ABJ-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(69,'019b9efb-b469-7219-8b97-cb9f291b4be4','Cream - Heavy Whipping','ABJ-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(70,'019b9efb-b469-7219-8b97-cb9f291b4be4','Yeast - Active Dry','ABJ-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(71,'019b9efb-b469-7219-8b97-cb9f291b4be4','Vegetable Oil - Cooking','ABJ-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(72,'019b9efb-b469-7219-8b97-cb9f291b4be4','Salt - Table Salt','ABJ-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(73,'019b9efb-b469-7219-8b97-cb9f291b4be4','Cake Boxes - 10 inch','ABJ-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(74,'019b9efb-b469-7219-8b97-cb9f291b4be4','Pastry Boxes - Small','ABJ-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(75,'019b9efb-b469-7219-8b97-cb9f291b4be4','Paper Bags - Brown','ABJ-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(76,'019b9efb-b469-7219-8b97-cb9f291b4be4','Plastic Food Containers','ABJ-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(77,'019b9efb-b469-7219-8b97-cb9f291b4be4','Dishwashing Liquid - Industrial','ABJ-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(78,'019b9efb-b469-7219-8b97-cb9f291b4be4','Paper Towels - Kitchen Roll','ABJ-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(79,'019b9efb-b469-7219-8b97-cb9f291b4be4','Garbage Bags - Large','ABJ-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(80,'019b9efb-b469-7219-8b97-cb9f291b4be4','Mixing Bowls - Stainless Steel','ABJ-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(81,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Sugar - White Granulated','ENU-ITM-00001','raw_material',3,'Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(82,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Flour - All Purpose','ENU-ITM-00002','raw_material',3,'High-quality all-purpose flour for general baking',100.00,1000.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(83,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Cocoa Powder - Premium Dark','ENU-ITM-00003','raw_material',3,'Premium Dutch-processed cocoa powder',20.00,200.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(84,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Butter - Salted','ENU-ITM-00004','raw_material',3,'Fresh salted butter for baking and cooking',30.00,300.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(85,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Eggs - Large Grade A','ENU-ITM-00005','raw_material',14,'Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(86,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Vanilla Extract - Pure','ENU-ITM-00006','raw_material',7,'Pure vanilla extract for flavoring',5.00,30.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(87,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Chocolate Chips - Dark','ENU-ITM-00007','raw_material',3,'70% dark chocolate chips for baking',15.00,150.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(88,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Milk - Fresh Whole','ENU-ITM-00008','raw_material',7,'Fresh whole milk, refrigerated',20.00,100.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(89,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Cream - Heavy Whipping','ENU-ITM-00009','raw_material',7,'Heavy whipping cream, 35% fat content',10.00,50.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(90,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Yeast - Active Dry','ENU-ITM-00010','raw_material',3,'Active dry yeast for bread making',5.00,25.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(91,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Vegetable Oil - Cooking','ENU-ITM-00011','raw_material',7,'Refined vegetable oil for cooking and frying',25.00,200.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(92,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Salt - Table Salt','ENU-ITM-00012','raw_material',3,'Iodized table salt for cooking and seasoning',10.00,100.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(93,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Cake Boxes - 10 inch','ENU-ITM-00013','packaging',13,'White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(94,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Pastry Boxes - Small','ENU-ITM-00014','packaging',13,'Small boxes for pastries and individual servings',200.00,2000.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(95,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Paper Bags - Brown','ENU-ITM-00015','packaging',13,'Eco-friendly brown paper bags',500.00,5000.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(96,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Plastic Food Containers','ENU-ITM-00016','packaging',13,'Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(97,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Dishwashing Liquid - Industrial','ENU-ITM-00017','consumable',7,'Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(98,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Paper Towels - Kitchen Roll','ENU-ITM-00018','consumable',14,'Absorbent paper towels for kitchen use',50.00,200.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(99,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Garbage Bags - Large','ENU-ITM-00019','consumable',13,'Heavy-duty large garbage bags',100.00,500.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(100,'019b9efb-b473-7177-aceb-a5d244ef3f8c','Mixing Bowls - Stainless Steel','ENU-ITM-00020','equipment',13,'Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2026-01-08 19:01:28','2026-01-08 19:01:28');
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
(1,'Annual Leave','ANNUAL','Yearly vacation leave for rest and recreation',21,1,0,14,7,1,1,'#3b82f6','2026-01-08 19:00:53','2026-01-08 19:00:53'),
(2,'Sick Leave','SICK','Medical leave for illness or injury',10,1,1,NULL,0,1,1,'#ef4444','2026-01-08 19:00:53','2026-01-08 19:00:53'),
(3,'Emergency Leave','EMERGENCY','Urgent personal or family emergencies',5,1,0,3,0,1,1,'#f59e0b','2026-01-08 19:00:53','2026-01-08 19:00:53'),
(4,'Maternity Leave','MATERNITY','Leave for childbirth and post-natal care',90,1,1,NULL,30,1,1,'#ec4899','2026-01-08 19:00:53','2026-01-08 19:00:53'),
(5,'Paternity Leave','PATERNITY','Leave for fathers following childbirth',7,1,1,NULL,7,1,1,'#6366f1','2026-01-08 19:00:53','2026-01-08 19:00:53'),
(6,'Bereavement Leave','BEREAVEMENT','Leave for death of a family member',3,1,0,3,0,1,1,'#6b7280','2026-01-08 19:00:53','2026-01-08 19:00:53'),
(7,'Study Leave','STUDY','Leave for educational purposes or examinations',5,1,1,5,14,0,1,'#8b5cf6','2026-01-08 19:00:53','2026-01-08 19:00:53'),
(8,'Unpaid Leave','UNPAID','Additional leave without pay',0,1,0,NULL,14,0,1,'#64748b','2026-01-08 19:00:53','2026-01-08 19:00:53');
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
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(186,'2026_01_09_053531_add_polymorphic_columns_to_gl_entries_table',2);
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
INSERT INTO `model_has_permissions` VALUES
(29,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(39,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(49,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(52,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(64,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(70,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(84,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(89,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(92,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(93,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(94,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(95,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(97,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(101,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(105,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(112,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(113,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(114,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(147,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(156,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(161,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(169,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(170,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(173,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(182,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(200,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(204,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(210,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(213,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(214,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(215,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(216,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(217,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(221,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(240,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(244,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(266,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(267,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(268,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(269,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(270,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(276,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(279,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(281,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(283,'User','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(65,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(68,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(70,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(71,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(72,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(73,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(74,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(76,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(82,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(103,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(104,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(148,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(153,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(154,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(156,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(239,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(240,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(259,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(263,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(264,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(284,'User','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(29,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(39,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(49,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(52,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(64,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(70,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(84,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(89,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(92,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(93,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(94,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(95,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(97,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(101,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(105,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(112,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(113,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(114,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(147,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(156,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(161,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(169,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(170,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(173,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(182,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(200,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(204,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(210,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(213,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(214,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(215,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(216,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(217,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(221,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(240,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(244,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(266,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(267,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(268,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(269,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(270,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(276,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(279,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(281,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(283,'User','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(65,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(68,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(70,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(71,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(72,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(73,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(74,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(76,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(82,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(103,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(104,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(148,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(153,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(154,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(156,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(239,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(240,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(259,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(263,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(264,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(284,'User','04603b59-6357-3210-9bdf-96735900ca08'),
(65,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(68,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(70,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(71,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(72,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(73,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(74,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(76,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(82,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(103,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(104,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(148,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(153,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(154,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(156,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(239,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(240,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(259,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(263,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(264,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(284,'User','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(32,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(33,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(34,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(35,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(36,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(39,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(42,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(43,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(44,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(45,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(102,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(147,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(208,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(228,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(229,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(230,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(231,'User','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(65,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(68,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(70,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(71,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(72,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(73,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(74,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(76,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(82,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(103,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(104,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(148,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(153,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(154,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(156,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(239,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(240,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(259,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(263,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(264,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(284,'User','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(32,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(33,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(34,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(35,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(36,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(39,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(42,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(43,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(44,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(45,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(102,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(147,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(208,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(228,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(229,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(230,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(231,'User','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(29,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(39,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(49,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(52,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(64,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(70,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(84,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(89,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(92,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(93,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(94,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(95,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(97,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(101,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(105,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(112,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(113,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(114,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(147,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(156,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(161,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(169,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(170,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(173,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(182,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(200,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(204,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(210,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(213,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(214,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(215,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(216,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(217,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(221,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(240,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(244,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(266,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(267,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(268,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(269,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(270,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(276,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(279,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(281,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(283,'User','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(32,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(33,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(34,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(35,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(36,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(39,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(42,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(43,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(44,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(45,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(102,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(147,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(208,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(228,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(229,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(230,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(231,'User','12a53718-25a5-3e22-85fb-a113ace1609b'),
(32,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(33,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(34,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(35,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(36,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(39,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(42,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(43,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(44,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(45,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(102,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(147,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(208,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(228,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(229,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(230,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(231,'User','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(65,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(68,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(70,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(71,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(72,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(73,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(74,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(76,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(82,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(103,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(104,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(148,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(153,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(154,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(156,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(239,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(240,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(259,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(263,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(264,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(284,'User','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(65,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(68,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(70,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(71,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(72,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(73,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(74,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(76,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(82,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(103,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(104,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(148,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(153,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(154,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(156,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(239,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(240,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(259,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(263,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(264,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(284,'User','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(32,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(33,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(34,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(35,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(36,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(39,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(42,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(43,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(44,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(45,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(102,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(147,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(208,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(228,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(229,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(230,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(231,'User','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(65,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(68,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(70,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(71,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(72,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(73,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(74,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(76,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(82,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(103,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(104,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(148,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(153,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(154,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(156,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(239,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(240,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(259,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(263,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(264,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(284,'User','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(32,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(33,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(34,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(35,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(36,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(39,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(42,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(43,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(44,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(45,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(102,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(147,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(208,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(228,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(229,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(230,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(231,'User','203caae2-c952-3596-998d-27d2f4943efa'),
(32,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(33,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(34,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(35,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(36,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(39,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(42,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(43,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(44,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(45,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(102,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(147,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(208,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(228,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(229,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(230,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(231,'User','231ab741-da89-354e-b734-a1e2fca48abe'),
(29,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(39,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(49,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(52,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(64,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(70,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(84,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(89,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(92,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(93,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(94,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(95,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(97,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(101,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(105,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(112,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(113,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(114,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(147,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(156,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(161,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(169,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(170,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(173,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(182,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(200,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(204,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(210,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(213,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(214,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(215,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(216,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(217,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(221,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(240,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(244,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(266,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(267,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(268,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(269,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(270,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(276,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(279,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(281,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(283,'User','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(65,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(68,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(70,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(71,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(72,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(73,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(74,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(76,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(82,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(103,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(104,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(148,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(153,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(154,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(156,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(239,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(240,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(259,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(263,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(264,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(284,'User','245e4654-6381-3e83-9191-41fb4725e0b2'),
(65,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(68,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(70,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(71,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(72,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(73,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(74,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(76,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(82,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(103,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(104,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(148,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(153,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(154,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(156,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(239,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(240,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(259,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(263,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(264,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(284,'User','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(32,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(33,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(34,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(35,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(36,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(39,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(42,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(43,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(44,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(45,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(102,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(147,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(208,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(228,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(229,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(230,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(231,'User','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(29,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(39,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(49,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(52,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(64,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(70,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(84,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(89,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(92,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(93,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(94,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(95,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(97,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(101,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(105,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(112,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(113,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(114,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(147,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(156,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(161,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(169,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(170,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(173,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(182,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(200,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(204,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(210,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(213,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(214,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(215,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(216,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(217,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(221,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(240,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(244,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(266,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(267,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(268,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(269,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(270,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(276,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(279,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(281,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(283,'User','323cf339-e303-3dc5-abbe-185a715abe35'),
(32,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(33,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(34,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(35,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(36,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(39,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(42,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(43,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(44,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(45,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(102,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(147,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(208,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(228,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(229,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(230,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(231,'User','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(65,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(68,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(70,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(71,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(72,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(73,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(74,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(76,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(82,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(103,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(104,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(148,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(153,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(154,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(156,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(239,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(240,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(259,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(263,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(264,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(284,'User','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(65,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(68,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(70,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(71,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(72,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(73,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(74,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(76,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(82,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(103,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(104,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(148,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(153,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(154,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(156,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(239,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(240,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(259,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(263,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(264,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(284,'User','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(65,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(68,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(70,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(71,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(72,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(73,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(74,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(76,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(82,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(103,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(104,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(148,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(153,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(154,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(156,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(239,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(240,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(259,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(263,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(264,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(284,'User','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(65,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(68,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(70,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(71,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(72,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(73,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(74,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(76,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(82,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(103,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(104,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(148,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(153,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(154,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(156,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(239,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(240,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(259,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(263,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(264,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(284,'User','4440aa97-63d6-3eca-95dc-7829201924af'),
(65,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(68,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(70,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(71,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(72,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(73,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(74,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(76,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(82,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(103,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(104,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(148,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(153,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(154,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(156,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(239,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(240,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(259,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(263,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(264,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(284,'User','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(65,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(68,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(70,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(71,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(72,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(73,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(74,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(76,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(82,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(103,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(104,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(148,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(153,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(154,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(156,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(239,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(240,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(259,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(263,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(264,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(284,'User','476119ec-b194-38ee-a339-6bd87cf75c64'),
(32,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(33,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(34,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(35,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(36,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(39,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(42,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(43,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(44,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(45,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(102,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(147,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(208,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(228,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(229,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(230,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(231,'User','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(65,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(68,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(70,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(71,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(72,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(73,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(74,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(76,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(82,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(103,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(104,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(148,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(153,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(154,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(156,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(239,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(240,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(259,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(263,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(264,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(284,'User','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(65,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(68,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(70,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(71,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(72,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(73,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(74,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(76,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(82,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(103,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(104,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(148,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(153,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(154,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(156,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(239,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(240,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(259,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(263,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(264,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(284,'User','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(32,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(33,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(34,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(35,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(36,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(39,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(42,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(43,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(44,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(45,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(102,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(147,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(208,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(228,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(229,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(230,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(231,'User','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(32,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(33,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(34,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(35,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(36,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(39,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(42,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(43,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(44,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(45,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(102,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(147,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(208,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(228,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(229,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(230,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(231,'User','588009a8-0825-3a9a-82d4-20e964e49310'),
(32,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(33,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(34,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(35,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(36,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(39,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(42,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(43,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(44,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(45,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(102,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(147,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(208,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(228,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(229,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(230,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(231,'User','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(65,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(68,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(70,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(71,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(72,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(73,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(74,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(76,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(82,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(103,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(104,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(148,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(153,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(154,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(156,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(239,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(240,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(259,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(263,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(264,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(284,'User','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(32,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(33,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(34,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(35,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(36,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(39,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(42,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(43,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(44,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(45,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(102,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(147,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(208,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(228,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(229,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(230,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(231,'User','5a9d4741-0f95-3901-866a-a40d3752148e'),
(32,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(33,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(34,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(35,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(36,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(39,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(42,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(43,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(44,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(45,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(102,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(147,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(208,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(228,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(229,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(230,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(231,'User','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(32,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(33,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(34,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(35,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(36,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(39,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(42,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(43,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(44,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(45,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(102,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(147,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(208,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(228,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(229,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(230,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(231,'User','642757ca-f66d-3d7a-be81-933e986372fe'),
(32,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(33,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(34,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(35,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(36,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(39,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(42,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(43,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(44,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(45,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(102,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(147,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(208,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(228,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(229,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(230,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(231,'User','661e6090-b9f3-3399-89b1-5acda4b94178'),
(29,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(39,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(49,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(52,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(64,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(70,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(84,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(89,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(92,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(93,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(94,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(95,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(97,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(101,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(105,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(112,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(113,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(114,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(147,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(156,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(161,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(169,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(170,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(173,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(182,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(200,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(204,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(210,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(213,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(214,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(215,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(216,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(217,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(221,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(240,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(244,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(266,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(267,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(268,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(269,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(270,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(276,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(279,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(281,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(283,'User','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(32,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(33,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(34,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(35,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(36,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(39,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(42,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(43,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(44,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(45,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(102,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(147,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(208,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(228,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(229,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(230,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(231,'User','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(32,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(33,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(34,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(35,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(36,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(39,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(42,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(43,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(44,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(45,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(102,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(147,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(208,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(228,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(229,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(230,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(231,'User','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(29,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(39,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(49,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(52,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(64,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(70,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(84,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(89,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(92,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(93,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(94,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(95,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(97,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(101,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(105,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(112,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(113,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(114,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(147,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(156,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(161,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(169,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(170,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(173,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(182,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(200,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(204,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(210,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(213,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(214,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(215,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(216,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(217,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(221,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(240,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(244,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(266,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(267,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(268,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(269,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(270,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(276,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(279,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(281,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(283,'User','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(32,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(33,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(34,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(35,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(36,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(39,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(42,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(43,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(44,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(45,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(102,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(147,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(208,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(228,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(229,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(230,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(231,'User','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(65,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(68,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(70,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(71,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(72,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(73,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(74,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(76,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(82,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(103,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(104,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(148,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(153,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(154,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(156,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(239,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(240,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(259,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(263,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(264,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(284,'User','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(32,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(33,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(34,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(35,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(36,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(39,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(42,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(43,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(44,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(45,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(102,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(147,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(208,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(228,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(229,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(230,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(231,'User','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(65,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(68,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(70,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(71,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(72,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(73,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(74,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(76,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(82,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(103,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(104,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(148,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(153,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(154,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(156,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(239,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(240,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(259,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(263,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(264,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(284,'User','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(32,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(33,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(34,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(35,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(36,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(39,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(42,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(43,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(44,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(45,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(102,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(147,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(208,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(228,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(229,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(230,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(231,'User','756b29a0-991f-3bea-a033-3f798057d465'),
(32,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(33,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(34,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(35,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(36,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(39,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(42,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(43,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(44,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(45,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(102,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(147,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(208,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(228,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(229,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(230,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(231,'User','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(65,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(68,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(70,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(71,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(72,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(73,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(74,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(76,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(82,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(103,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(104,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(148,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(153,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(154,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(156,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(239,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(240,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(259,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(263,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(264,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(284,'User','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(65,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(68,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(70,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(71,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(72,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(73,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(74,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(76,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(82,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(103,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(104,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(148,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(153,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(154,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(156,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(239,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(240,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(259,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(263,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(264,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(284,'User','799aba19-8edd-359b-87cd-101ee3ac292e'),
(65,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(68,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(70,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(71,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(72,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(73,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(74,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(76,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(82,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(103,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(104,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(148,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(153,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(154,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(156,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(239,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(240,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(259,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(263,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(264,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(284,'User','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(29,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(39,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(49,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(52,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(64,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(70,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(84,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(89,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(92,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(93,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(94,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(95,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(97,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(101,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(105,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(112,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(113,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(114,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(147,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(156,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(161,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(169,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(170,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(173,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(182,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(200,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(204,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(210,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(213,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(214,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(215,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(216,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(217,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(221,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(240,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(244,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(266,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(267,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(268,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(269,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(270,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(276,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(279,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(281,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(283,'User','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(65,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(68,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(70,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(71,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(72,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(73,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(74,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(76,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(82,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(103,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(104,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(148,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(153,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(154,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(156,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(239,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(240,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(259,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(263,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(264,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(284,'User','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(29,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(39,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(49,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(52,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(64,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(70,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(84,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(89,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(92,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(93,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(94,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(95,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(97,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(101,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(105,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(112,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(113,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(114,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(147,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(156,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(161,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(169,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(170,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(173,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(182,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(200,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(204,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(210,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(213,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(214,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(215,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(216,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(217,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(221,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(240,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(244,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(266,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(267,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(268,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(269,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(270,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(276,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(279,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(281,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(283,'User','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(32,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(33,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(34,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(35,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(36,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(39,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(42,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(43,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(44,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(45,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(102,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(147,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(208,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(228,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(229,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(230,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(231,'User','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(29,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(39,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(49,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(52,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(64,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(70,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(84,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(89,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(92,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(93,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(94,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(95,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(97,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(101,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(105,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(112,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(113,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(114,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(147,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(156,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(161,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(169,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(170,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(173,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(182,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(200,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(204,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(210,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(213,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(214,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(215,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(216,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(217,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(221,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(240,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(244,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(266,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(267,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(268,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(269,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(270,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(276,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(279,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(281,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(283,'User','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(65,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(68,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(70,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(71,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(72,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(73,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(74,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(76,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(82,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(103,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(104,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(148,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(153,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(154,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(156,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(239,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(240,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(259,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(263,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(264,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(284,'User','89eaf009-d299-338c-bc92-369a8f7bd948'),
(32,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(33,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(34,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(35,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(36,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(39,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(42,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(43,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(44,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(45,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(102,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(147,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(208,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(228,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(229,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(230,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(231,'User','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(32,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(33,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(34,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(35,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(36,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(39,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(42,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(43,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(44,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(45,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(102,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(147,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(208,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(228,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(229,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(230,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(231,'User','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(65,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(68,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(70,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(71,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(72,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(73,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(74,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(76,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(82,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(103,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(104,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(148,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(153,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(154,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(156,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(239,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(240,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(259,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(263,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(264,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(284,'User','90824f40-840f-3036-afce-fd60e034e10b'),
(32,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(33,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(34,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(35,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(36,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(39,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(42,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(43,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(44,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(45,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(102,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(147,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(208,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(228,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(229,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(230,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(231,'User','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(32,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(33,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(34,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(35,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(36,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(39,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(42,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(43,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(44,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(45,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(102,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(147,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(208,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(228,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(229,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(230,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(231,'User','94f35001-ab9a-3948-83a6-bd0320b28032'),
(32,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(33,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(34,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(35,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(36,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(39,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(42,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(43,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(44,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(45,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(102,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(147,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(208,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(228,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(229,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(230,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(231,'User','9faf1788-6107-332a-a966-d7eff85fc592'),
(32,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(33,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(34,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(35,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(36,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(39,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(42,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(43,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(44,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(45,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(102,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(147,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(208,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(228,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(229,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(230,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(231,'User','a4a7593d-b897-39d3-8576-152d22e928d7'),
(32,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(33,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(34,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(35,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(36,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(39,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(42,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(43,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(44,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(45,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(102,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(147,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(208,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(228,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(229,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(230,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(231,'User','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(29,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(39,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(49,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(52,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(64,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(70,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(84,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(89,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(92,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(93,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(94,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(95,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(97,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(101,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(105,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(112,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(113,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(114,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(147,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(156,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(161,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(169,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(170,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(173,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(182,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(200,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(204,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(210,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(213,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(214,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(215,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(216,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(217,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(221,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(240,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(244,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(266,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(267,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(268,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(269,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(270,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(276,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(279,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(281,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(283,'User','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(65,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(68,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(70,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(71,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(72,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(73,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(74,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(76,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(82,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(103,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(104,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(148,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(153,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(154,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(156,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(239,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(240,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(259,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(263,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(264,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(284,'User','aa15f570-9070-3f01-b277-c9af7a34f556'),
(32,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(33,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(34,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(35,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(36,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(39,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(42,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(43,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(44,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(45,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(102,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(147,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(208,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(228,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(229,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(230,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(231,'User','aafb621b-b0ec-39ab-99b5-54648b460094'),
(29,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(39,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(49,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(52,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(64,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(70,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(84,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(89,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(92,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(93,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(94,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(95,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(97,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(101,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(105,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(112,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(113,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(114,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(147,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(156,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(161,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(169,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(170,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(173,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(182,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(200,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(204,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(210,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(213,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(214,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(215,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(216,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(217,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(221,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(240,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(244,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(266,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(267,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(268,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(269,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(270,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(276,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(279,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(281,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(283,'User','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(32,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(33,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(34,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(35,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(36,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(39,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(42,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(43,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(44,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(45,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(102,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(147,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(208,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(228,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(229,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(230,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(231,'User','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(65,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(68,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(70,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(71,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(72,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(73,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(74,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(76,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(82,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(103,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(104,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(148,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(153,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(154,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(156,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(239,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(240,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(259,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(263,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(264,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(284,'User','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(29,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(39,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(49,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(52,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(64,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(70,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(84,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(89,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(92,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(93,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(94,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(95,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(97,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(101,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(105,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(112,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(113,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(114,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(147,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(156,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(161,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(169,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(170,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(173,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(182,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(200,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(204,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(210,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(213,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(214,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(215,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(216,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(217,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(221,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(240,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(244,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(266,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(267,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(268,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(269,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(270,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(276,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(279,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(281,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(283,'User','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(32,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(33,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(34,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(35,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(36,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(39,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(42,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(43,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(44,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(45,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(102,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(147,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(208,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(228,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(229,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(230,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(231,'User','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(29,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(39,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(49,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(52,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(64,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(70,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(84,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(89,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(92,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(93,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(94,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(95,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(97,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(101,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(105,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(112,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(113,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(114,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(147,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(156,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(161,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(169,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(170,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(173,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(182,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(200,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(204,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(210,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(213,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(214,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(215,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(216,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(217,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(221,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(240,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(244,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(266,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(267,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(268,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(269,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(270,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(276,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(279,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(281,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(283,'User','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(29,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(39,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(49,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(52,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(64,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(70,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(84,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(89,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(92,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(93,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(94,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(95,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(97,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(101,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(105,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(112,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(113,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(114,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(147,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(156,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(161,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(169,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(170,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(173,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(182,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(200,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(204,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(210,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(213,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(214,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(215,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(216,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(217,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(221,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(240,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(244,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(266,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(267,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(268,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(269,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(270,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(276,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(279,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(281,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(283,'User','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(32,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(33,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(34,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(35,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(36,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(39,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(42,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(43,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(44,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(45,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(102,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(147,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(208,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(228,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(229,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(230,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(231,'User','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(32,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(33,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(34,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(35,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(36,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(39,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(42,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(43,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(44,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(45,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(102,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(147,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(208,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(228,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(229,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(230,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(231,'User','bed3959d-f13c-367c-ac10-89422d423aef'),
(65,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(68,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(70,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(71,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(72,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(73,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(74,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(76,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(82,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(103,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(104,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(148,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(153,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(154,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(156,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(239,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(240,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(259,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(263,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(264,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(284,'User','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(32,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(33,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(34,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(35,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(36,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(39,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(42,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(43,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(44,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(45,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(102,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(147,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(208,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(228,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(229,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(230,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(231,'User','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(65,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(68,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(70,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(71,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(72,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(73,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(74,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(76,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(82,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(103,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(104,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(148,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(153,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(154,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(156,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(239,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(240,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(259,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(263,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(264,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(284,'User','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(32,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(33,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(34,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(35,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(36,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(39,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(42,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(43,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(44,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(45,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(102,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(147,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(208,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(228,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(229,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(230,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(231,'User','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(65,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(68,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(70,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(71,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(72,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(73,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(74,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(76,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(82,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(103,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(104,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(148,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(153,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(154,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(156,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(239,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(240,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(259,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(263,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(264,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(284,'User','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(32,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(33,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(34,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(35,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(36,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(39,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(42,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(43,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(44,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(45,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(102,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(147,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(208,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(228,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(229,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(230,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(231,'User','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(32,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(33,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(34,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(35,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(36,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(39,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(42,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(43,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(44,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(45,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(102,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(147,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(208,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(228,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(229,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(230,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(231,'User','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(32,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(33,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(34,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(35,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(36,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(39,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(42,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(43,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(44,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(45,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(102,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(147,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(208,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(228,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(229,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(230,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(231,'User','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(32,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(33,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(34,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(35,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(36,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(39,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(42,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(43,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(44,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(45,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(102,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(147,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(208,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(228,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(229,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(230,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(231,'User','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(65,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(68,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(70,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(71,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(72,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(73,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(74,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(76,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(82,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(103,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(104,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(148,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(153,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(154,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(156,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(239,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(240,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(259,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(263,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(264,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(284,'User','d2d72092-8e3e-3015-ad8b-14def999d093'),
(65,'User','d38f6118-1921-319e-b294-32743f78c426'),
(68,'User','d38f6118-1921-319e-b294-32743f78c426'),
(70,'User','d38f6118-1921-319e-b294-32743f78c426'),
(71,'User','d38f6118-1921-319e-b294-32743f78c426'),
(72,'User','d38f6118-1921-319e-b294-32743f78c426'),
(73,'User','d38f6118-1921-319e-b294-32743f78c426'),
(74,'User','d38f6118-1921-319e-b294-32743f78c426'),
(76,'User','d38f6118-1921-319e-b294-32743f78c426'),
(82,'User','d38f6118-1921-319e-b294-32743f78c426'),
(103,'User','d38f6118-1921-319e-b294-32743f78c426'),
(104,'User','d38f6118-1921-319e-b294-32743f78c426'),
(148,'User','d38f6118-1921-319e-b294-32743f78c426'),
(153,'User','d38f6118-1921-319e-b294-32743f78c426'),
(154,'User','d38f6118-1921-319e-b294-32743f78c426'),
(156,'User','d38f6118-1921-319e-b294-32743f78c426'),
(239,'User','d38f6118-1921-319e-b294-32743f78c426'),
(240,'User','d38f6118-1921-319e-b294-32743f78c426'),
(259,'User','d38f6118-1921-319e-b294-32743f78c426'),
(263,'User','d38f6118-1921-319e-b294-32743f78c426'),
(264,'User','d38f6118-1921-319e-b294-32743f78c426'),
(284,'User','d38f6118-1921-319e-b294-32743f78c426'),
(29,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(39,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(49,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(52,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(64,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(70,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(84,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(89,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(92,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(93,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(94,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(95,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(97,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(101,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(105,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(112,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(113,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(114,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(147,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(156,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(161,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(169,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(170,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(173,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(182,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(200,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(204,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(210,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(213,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(214,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(215,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(216,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(217,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(221,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(240,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(244,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(266,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(267,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(268,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(269,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(270,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(276,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(279,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(281,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(283,'User','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(32,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(33,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(34,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(35,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(36,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(39,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(42,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(43,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(44,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(45,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(102,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(147,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(208,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(228,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(229,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(230,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(231,'User','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(32,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(33,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(34,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(35,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(36,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(39,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(42,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(43,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(44,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(45,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(102,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(147,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(208,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(228,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(229,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(230,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(231,'User','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(65,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(68,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(70,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(71,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(72,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(73,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(74,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(76,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(82,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(103,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(104,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(148,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(153,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(154,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(156,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(239,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(240,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(259,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(263,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(264,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(284,'User','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(65,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(68,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(70,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(71,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(72,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(73,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(74,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(76,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(82,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(103,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(104,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(148,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(153,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(154,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(156,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(239,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(240,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(259,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(263,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(264,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(284,'User','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(32,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(33,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(34,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(35,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(36,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(39,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(42,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(43,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(44,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(45,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(102,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(147,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(208,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(228,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(229,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(230,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(231,'User','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(32,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(33,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(34,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(35,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(36,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(39,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(42,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(43,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(44,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(45,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(102,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(147,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(208,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(228,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(229,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(230,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(231,'User','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(29,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(39,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(49,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(52,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(64,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(70,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(84,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(89,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(92,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(93,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(94,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(95,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(97,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(101,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(105,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(112,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(113,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(114,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(147,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(156,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(161,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(169,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(170,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(173,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(182,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(200,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(204,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(210,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(213,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(214,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(215,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(216,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(217,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(221,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(240,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(244,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(266,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(267,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(268,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(269,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(270,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(276,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(279,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(281,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(283,'User','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(32,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(33,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(34,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(35,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(36,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(39,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(42,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(43,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(44,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(45,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(102,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(147,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(208,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(228,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(229,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(230,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(231,'User','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(29,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(39,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(49,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(52,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(64,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(70,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(84,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(89,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(92,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(93,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(94,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(95,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(97,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(101,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(105,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(112,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(113,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(114,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(147,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(156,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(161,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(169,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(170,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(173,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(182,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(200,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(204,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(210,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(213,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(214,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(215,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(216,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(217,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(221,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(240,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(244,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(266,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(267,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(268,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(269,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(270,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(276,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(279,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(281,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(283,'User','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(29,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(39,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(49,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(52,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(64,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(70,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(84,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(89,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(92,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(93,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(94,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(95,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(97,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(101,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(105,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(112,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(113,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(114,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(147,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(156,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(161,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(169,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(170,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(173,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(182,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(200,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(204,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(210,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(213,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(214,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(215,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(216,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(217,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(221,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(240,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(244,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(266,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(267,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(268,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(269,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(270,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(276,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(279,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(281,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(283,'User','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(32,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(33,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(34,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(35,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(36,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(39,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(42,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(43,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(44,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(45,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(102,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(147,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(208,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(228,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(229,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(230,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(231,'User','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(32,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(33,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(34,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(35,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(36,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(39,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(42,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(43,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(44,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(45,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(102,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(147,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(208,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(228,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(229,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(230,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(231,'User','edebbd60-00aa-347f-8480-077ea06e48e6'),
(29,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(39,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(49,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(52,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(64,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(70,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(84,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(89,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(92,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(93,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(94,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(95,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(97,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(101,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(105,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(112,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(113,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(114,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(147,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(156,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(161,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(169,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(170,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(173,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(182,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(200,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(204,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(210,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(213,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(214,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(215,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(216,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(217,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(221,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(240,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(244,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(266,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(267,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(268,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(269,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(270,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(276,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(279,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(281,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(283,'User','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(65,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(68,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(70,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(71,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(72,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(73,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(74,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(76,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(82,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(103,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(104,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(148,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(153,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(154,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(156,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(239,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(240,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(259,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(263,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(264,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(284,'User','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(65,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(68,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(70,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(71,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(72,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(73,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(74,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(76,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(82,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(103,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(104,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(148,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(153,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(154,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(156,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(239,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(240,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(259,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(263,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(264,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(284,'User','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(65,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(68,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(70,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(71,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(72,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(73,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(74,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(76,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(82,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(103,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(104,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(148,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(153,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(154,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(156,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(239,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(240,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(259,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(263,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(264,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(284,'User','f8583630-8f77-3229-936a-b667bedb97fa'),
(32,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(33,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(34,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(35,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(36,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(39,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(42,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(43,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(44,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(45,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(102,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(147,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(208,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(228,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(229,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(230,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(231,'User','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(65,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(68,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(70,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(71,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(72,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(73,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(74,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(76,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(82,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(103,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(104,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(148,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(153,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(154,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(156,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(239,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(240,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(259,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(263,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(264,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(284,'User','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(65,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(68,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(70,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(71,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(72,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(73,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(74,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(76,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(82,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(103,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(104,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(148,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(153,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(154,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(156,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(239,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(240,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(259,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(263,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(264,'User','ffe79fca-3076-3151-a954-a1c1660b067d'),
(284,'User','ffe79fca-3076-3151-a954-a1c1660b067d');
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
(55,'user','0084f5fc-dada-3f07-98de-fa253e3686e9'),
(33,'App\\Models\\User','019b9efb-b3ff-7394-8cbb-81be9ac84154'),
(33,'user','019b9efb-b3ff-7394-8cbb-81be9ac84154'),
(58,'user','019b9efb-e130-73b8-8b36-b79ac90b24a2'),
(58,'user','019b9efb-e140-70f0-a849-fc952500b44b'),
(58,'user','019b9efb-e156-72e5-9e9c-2db4def280f7'),
(58,'user','019b9efb-e17a-7031-a370-9aa6c1f8f18d'),
(58,'user','019b9efb-e190-710a-85f9-4a0d1a71fc0b'),
(58,'user','019b9efb-e19b-7077-ac8a-b5454a813066'),
(58,'user','019b9efb-e1a6-70eb-9091-c1405473ab5a'),
(58,'user','019b9efb-e1b1-71ce-b8bb-2e9799b9594a'),
(58,'user','019b9efb-e1bc-706b-ac99-f713893b3afe'),
(58,'user','019b9efb-e1c8-72fd-8cbd-9e8e6dc89839'),
(58,'user','019b9efb-e1d3-7238-803a-a2fbb1d65588'),
(58,'user','019b9efb-e1df-732f-ade1-ec953be84571'),
(58,'user','019b9efb-e1eb-70ad-a7e0-3ca5b8ced301'),
(58,'user','019b9efb-e1f4-7222-ba0c-ef28c8e1f3f6'),
(58,'user','019b9efb-e1ff-71c0-b54d-ece332df7591'),
(58,'user','019b9efb-e20a-71e2-aeb5-9f8ea7dff02e'),
(58,'user','019b9efb-e216-71c6-b127-94fce3cd77a4'),
(58,'user','019b9efb-e221-739e-855e-7f333631cdc4'),
(58,'user','019b9efb-e22d-7346-aec6-d98b25b38217'),
(58,'user','019b9efb-e237-72ac-99e8-e9ecfe25cdb4'),
(58,'user','019b9efb-e241-7142-babc-a141f17b26ec'),
(58,'user','019b9efb-e24f-737c-87e7-ceff3baa1da4'),
(58,'user','019b9efb-e259-7305-aaef-e9159b4b1592'),
(58,'user','019b9efb-e26e-7311-8b80-5dd05f393522'),
(58,'user','019b9efb-e2c0-727e-bbc0-8cb674a3ae05'),
(58,'user','019b9efb-e2d2-7351-aa19-b27d8bbdf689'),
(58,'user','019b9efb-e2dd-71ba-be7a-7d25c23221c0'),
(58,'user','019b9efb-e2e9-722e-b1d9-874ef69b8447'),
(58,'user','019b9efb-e2f3-738c-9a58-6702513da1c5'),
(58,'user','019b9efb-e2ff-73c9-819f-2c10f9b8f617'),
(58,'user','019b9efb-e81b-73aa-86c8-9638fe490f29'),
(58,'user','019b9efb-e88b-7286-ac73-86a3414bcf7c'),
(58,'user','019b9efb-e8a4-707c-8363-76e1d41cf7c8'),
(58,'user','019b9efb-e8b0-7217-937a-971673a46804'),
(58,'user','019b9efb-e8b9-72ec-86f7-12a69e050067'),
(58,'user','019b9efb-e8c4-726f-9a44-ce4638ac5ccf'),
(58,'user','019b9efb-e8d1-73f4-9488-92301c3c3d40'),
(58,'user','019b9efb-e8db-7190-99f2-6432e6b16954'),
(58,'user','019b9efb-e8e6-7201-9e22-8d98faff6ce4'),
(58,'user','019b9efb-e8f1-73d9-9ea3-84521ca31d93'),
(58,'user','019b9efb-e8fc-70f3-acf1-c7c79810ad54'),
(58,'user','019b9efb-e907-73b5-9182-9952eafa16ab'),
(58,'user','019b9efb-e913-71f2-a396-91b3bccbd7fb'),
(58,'user','019b9efb-e91d-72e5-85f2-912e03d9c1ff'),
(58,'user','019b9efb-e928-70cf-b576-5b6862daf5b9'),
(58,'user','019b9efb-e935-7099-8bf1-298073ba8d93'),
(58,'user','019b9efb-e94a-72b9-80c4-0652566eeef6'),
(58,'user','019b9efb-e956-73e4-9e27-b541ede9767b'),
(58,'user','019b9efb-e96c-73b7-8074-73860b28db90'),
(58,'user','019b9efb-e977-71c9-b8e1-515f8018468d'),
(58,'user','019b9efb-e982-7035-84be-c21728e85f18'),
(58,'user','019b9efb-e98d-705b-b05e-537abd74079d'),
(58,'user','019b9efb-e998-72bf-9ffd-8ed2ad4370d0'),
(58,'user','019b9efb-e9a3-72e4-8348-c083a0d3b780'),
(58,'user','019b9efb-e9ae-70a0-9a37-709b1b2b0208'),
(58,'user','019b9efb-e9b9-7113-a2bf-a9bdf3430655'),
(58,'user','019b9efb-e9c4-7321-aeae-72152ce598bc'),
(58,'user','019b9efb-e9cf-737e-a671-2626e39d9d41'),
(58,'user','019b9efb-e9dd-7185-9fd8-cf44eec69dce'),
(58,'user','019b9efb-e9e6-707d-b6be-8e99e40cdf30'),
(58,'user','019b9efb-eea1-7258-bf34-03d18ae444dc'),
(58,'user','019b9efb-eeb8-70be-97ad-db124e3ff74c'),
(58,'user','019b9efb-eec3-7204-ae65-62d1af85ebd8'),
(58,'user','019b9efb-eed2-73c7-9644-fc03e0fd79cb'),
(58,'user','019b9efb-ef35-72d7-b4d5-5c2f07906153'),
(58,'user','019b9efb-ef48-721a-9928-4044fd4ff8f1'),
(58,'user','019b9efb-ef54-718a-a64c-2bd5bd5dff82'),
(58,'user','019b9efb-ef5e-702f-86c4-36708c4ff9c2'),
(58,'user','019b9efb-ef6a-7034-9863-424c160b176c'),
(58,'user','019b9efb-ef75-71b0-b869-784cec1a7f53'),
(58,'user','019b9efb-ef7f-72a4-9d8d-cf486e2efc1d'),
(58,'user','019b9efb-ef8b-719f-a51f-8014c14de97a'),
(58,'user','019b9efb-ef96-7269-ad48-ab3c0b15bab2'),
(58,'user','019b9efb-efa5-70fb-86c5-bcfba286d227'),
(58,'user','019b9efb-efb7-7334-8ad2-e04909e3a7ab'),
(58,'user','019b9efb-efc2-704b-8fd9-d6753a42fac3'),
(58,'user','019b9efb-f014-73a2-b564-00ffa9d57a97'),
(58,'user','019b9efb-f026-7223-9a89-fdd3956fd97d'),
(58,'user','019b9efb-f031-73b2-8672-399cc1607fee'),
(58,'user','019b9efb-f03c-735f-b3c2-2607a9985a02'),
(58,'user','019b9efb-f047-7323-be3d-293b76ad6356'),
(58,'user','019b9efb-f053-7138-9cfd-cdd921ae8a7a'),
(58,'user','019b9efb-f05e-71f8-b199-e61b11bb7ddb'),
(58,'user','019b9efb-f069-71e1-874d-8683f73f9158'),
(58,'user','019b9efb-f074-7099-9b24-7f3ff31364d3'),
(58,'user','019b9efb-f07f-7281-8b19-78b9d6d793a6'),
(58,'user','019b9efb-f08a-72b2-ae8e-a8d727d5a543'),
(58,'user','019b9efb-f095-71bc-b0c5-1f44d53a91c8'),
(58,'user','019b9efb-f0fe-737b-8e9c-25bad61b688c'),
(58,'user','019b9efb-f110-70e1-8b77-4a619100dcbe'),
(58,'user','019b9efb-f5a5-7391-91ef-218fa807d7b1'),
(58,'user','019b9efb-f63d-738d-84c1-de734b237735'),
(58,'user','019b9efb-f650-7331-981b-63f9b1cab818'),
(58,'user','019b9efb-f65b-7157-81a3-62c6860a07bf'),
(58,'user','019b9efb-f667-72c2-9828-af8d9a421e9c'),
(58,'user','019b9efb-f671-7202-8f2c-211cb6b4fa03'),
(58,'user','019b9efb-f67d-704d-b95b-2a81ada309c5'),
(58,'user','019b9efb-f687-722e-bf19-2d8224d253f2'),
(58,'user','019b9efb-f6d8-726f-88e9-b658c977c892'),
(58,'user','019b9efb-f6ef-73bb-aa29-465cc67c205d'),
(58,'user','019b9efb-f6f7-739c-92c3-869253a248f9'),
(58,'user','019b9efb-f703-7035-86f9-9ed18a25b587'),
(58,'user','019b9efb-f70d-7345-b873-f02b03d3ffd2'),
(58,'user','019b9efb-f718-715e-97b3-3e478b3899ef'),
(58,'user','019b9efb-f723-7114-bec7-6b874dcefc75'),
(58,'user','019b9efb-f78a-725e-b86b-d2004e5a592e'),
(58,'user','019b9efb-f79d-72fe-a226-f9f37f3d429f'),
(58,'user','019b9efb-f7a9-71dc-9671-6d1aa6029e3d'),
(58,'user','019b9efb-f7b3-7177-8bba-7d8c00ee2855'),
(58,'user','019b9efb-f7bf-72d4-982a-5b0284dc6924'),
(58,'user','019b9efb-f7ca-7187-ac47-4cbae9a432da'),
(58,'user','019b9efb-f81a-7154-ad91-5dfb2436bd65'),
(58,'user','019b9efb-f832-735f-a50b-9fbef8063b1f'),
(58,'user','019b9efb-f844-71d4-b391-6e5c62d8e8c3'),
(58,'user','019b9efb-f85a-7196-bd74-2a18c2aa8150'),
(58,'user','019b9efb-f8b6-7217-985b-6b37522c7b1e'),
(58,'user','019b9efb-f8c9-72f5-8c62-e9c936fd4da2'),
(58,'user','019b9efb-f8d6-735a-a1f6-cf67a0b28888'),
(58,'user','019b9efb-f8e0-712b-a960-f2f4bbd32a67'),
(58,'user','019b9efb-f8ef-731d-a8a1-5e37a2cf099e'),
(58,'user','019b9efc-0104-703d-90b8-e5a75d9083dd'),
(58,'user','019b9efc-01b7-7287-91c3-5323fbd53f0e'),
(58,'user','019b9efc-01f5-7089-95e1-edf8fa0e39c1'),
(58,'user','019b9efc-01fe-71a5-a7bc-6824472baa12'),
(58,'user','019b9efc-0208-7143-9632-ab50c4f22d21'),
(58,'user','019b9efc-0213-73a5-8eaf-1339b2612490'),
(58,'user','019b9efc-021f-724b-8ccc-7c370cc0b2cb'),
(58,'user','019b9efc-022a-7208-b747-ea6c714fa002'),
(58,'user','019b9efc-0237-71ef-9b84-b9897fef1eb5'),
(58,'user','019b9efc-023f-7374-90e4-1e1396e920fc'),
(58,'user','019b9efc-024b-73fb-a350-c367afb8b4f7'),
(58,'user','019b9efc-0256-73e1-8d51-3466e83282c7'),
(58,'user','019b9efc-0261-7221-8b7f-e26cd66ecb90'),
(58,'user','019b9efc-026f-70b6-a964-13a5ec76be6a'),
(58,'user','019b9efc-0277-7080-aefa-a72d54c2caea'),
(58,'user','019b9efc-0283-71e1-84cc-0147ec7137b3'),
(58,'user','019b9efc-028e-71d9-82b4-e566803a38b8'),
(58,'user','019b9efc-0299-734a-a187-2da6ba253e64'),
(58,'user','019b9efc-02a4-73ff-a94a-89d975377a8e'),
(58,'user','019b9efc-02af-7178-bfb6-67f7805178df'),
(58,'user','019b9efc-02ba-72f1-9b26-adbc657bfeaa'),
(58,'user','019b9efc-02c6-7219-a23a-0da614bc954c'),
(58,'user','019b9efc-02d0-71ff-9be7-d0c5c97c19b2'),
(58,'user','019b9efc-02dc-72fd-a70d-05219252d8d6'),
(58,'user','019b9efc-02e7-703f-b28e-6f799a38643e'),
(58,'user','019b9efc-02f2-703d-93c8-20a5b73e0387'),
(58,'user','019b9efc-02fe-72ac-8877-e9e9ac68093d'),
(58,'user','019b9efc-0308-7075-9da0-1155e0a49c91'),
(58,'user','019b9efc-0315-715f-a655-c446e2540a8b'),
(58,'user','019b9efc-031e-7148-b43f-185bc1a25aaa'),
(49,'user','0298a060-0ffd-3ed7-8de3-f67c9b73387b'),
(40,'user','0450a605-3fe9-3544-bd2b-11de6dffdcb5'),
(51,'user','04603b59-6357-3210-9bdf-96735900ca08'),
(51,'user','0560a0c9-7c0e-3657-9a3f-18babef41281'),
(46,'user','09c44002-b9bb-3b8a-8ece-4dd0f567dbdc'),
(38,'user','0b40dd6e-3927-347e-95ff-979c7309fbdb'),
(46,'user','0c0026ee-5d98-3062-9d4f-8555c194e9fe'),
(40,'user','120e5c95-101e-308d-bee0-8ecee40b50b8'),
(46,'user','12a53718-25a5-3e22-85fb-a113ace1609b'),
(46,'user','16627d87-bc1c-3ac4-9c4a-8fdb276a4625'),
(51,'user','1899ff6b-4423-3227-a81f-ab5ae1d322af'),
(49,'user','1ab23065-c6bc-3a74-bd45-163dbcaac1d7'),
(47,'user','1acb22d8-42bf-3399-9db8-516f483beb2c'),
(49,'user','1b2d809b-867d-36ea-b1f0-a02c46b8025e'),
(47,'user','203caae2-c952-3596-998d-27d2f4943efa'),
(37,'user','231ab741-da89-354e-b734-a1e2fca48abe'),
(40,'user','23c2b7bc-3e80-38fa-be51-a9a77b74ce50'),
(42,'user','245e4654-6381-3e83-9191-41fb4725e0b2'),
(38,'user','30bc858b-26b2-38c2-b3f3-3ea4855e208a'),
(44,'user','30fa2b01-dcbb-30b6-ad5b-37570f9d818e'),
(56,'user','323cf339-e303-3dc5-abbe-185a715abe35'),
(46,'user','338cc47c-71ba-3b4d-a955-9389ad6c093e'),
(49,'user','3f777f4f-7363-3c4b-87db-7f4827a89a00'),
(49,'user','3f9601f1-eed8-3ec6-8a3f-7a3663442d88'),
(38,'user','41c78a2e-7985-3ce1-8a2b-7dc3b525fd32'),
(51,'user','4440aa97-63d6-3eca-95dc-7829201924af'),
(38,'user','4549ac22-e4d7-33e2-a93b-5fc0f0f58884'),
(51,'user','476119ec-b194-38ee-a339-6bd87cf75c64'),
(43,'user','523bbd41-7705-3b7b-a591-b09ca92fbc68'),
(51,'user','52b2a8c7-f232-34a0-ad04-de44a2f96779'),
(51,'user','5336bb2d-8b6f-3280-b525-a5fea9c841dc'),
(44,'user','567493b1-36e9-3e5f-a32b-f445346a45bb'),
(47,'user','588009a8-0825-3a9a-82d4-20e964e49310'),
(43,'user','58acf95b-247d-3bc3-bfca-eb1c97651f23'),
(51,'user','58d67707-7a7b-3160-8f2f-52aa848e94cf'),
(46,'user','5a9d4741-0f95-3901-866a-a40d3752148e'),
(39,'user','5f353b0a-46aa-301c-a9aa-d1a0ff9e650d'),
(46,'user','642757ca-f66d-3d7a-be81-933e986372fe'),
(37,'user','661e6090-b9f3-3399-89b1-5acda4b94178'),
(55,'user','666bf6ad-0541-33e5-a2b1-ff634b92e59f'),
(37,'user','67d7ea47-5d83-38df-af87-0c1645bce6b7'),
(47,'user','69b0da84-786d-31d2-9d9b-de10ca3a6a6b'),
(55,'user','69e43641-2eaf-37f0-8e0d-38151c1ba81b'),
(47,'user','6a40afb0-4758-39e4-8387-3504ffc3c0aa'),
(38,'user','6a5368c9-bca2-3a8b-821b-467e48b93108'),
(47,'user','73947e86-2211-3c4e-ac4c-6de0ef92c0ca'),
(42,'user','73a0b23d-113b-3867-9055-1aed32d0ca32'),
(36,'user','756b29a0-991f-3bea-a033-3f798057d465'),
(47,'user','76bd9ec7-a2d9-3d19-8fcb-534ed520dd92'),
(49,'user','77a6b52b-52fd-3dae-898c-2b8683f2ac68'),
(49,'user','799aba19-8edd-359b-87cd-101ee3ac292e'),
(49,'user','7dab76d9-8a4e-33cd-92cf-dcca2050f91d'),
(66,'user','7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb'),
(49,'user','8063f4b9-e97c-38ba-b458-d91cd875ed07'),
(55,'user','82f6ea3b-8ea2-3d1a-b223-1d716369b0e4'),
(39,'user','8894c20d-36fe-336c-8c8a-83fdc2cae96d'),
(67,'user','89bfc778-8f23-4a5e-bca8-7784b5bd1190'),
(50,'user','89eaf009-d299-338c-bc92-369a8f7bd948'),
(37,'user','8de35c4f-7de8-3079-b8cd-afda1bdb21c9'),
(44,'user','8e056c0c-57d3-3224-86b6-a197c2b0522b'),
(42,'user','90824f40-840f-3036-afce-fd60e034e10b'),
(47,'user','91e0199b-8062-31a6-9ea3-e0a6a3be37db'),
(46,'user','94f35001-ab9a-3948-83a6-bd0320b28032'),
(39,'user','9faf1788-6107-332a-a966-d7eff85fc592'),
(46,'user','a4a7593d-b897-39d3-8576-152d22e928d7'),
(44,'user','a7c465de-a86b-3dd0-b7b9-ce4820300b10'),
(39,'user','a8585d83-81ef-4493-bfcb-e2ba257387b0'),
(49,'user','aa15f570-9070-3f01-b277-c9af7a34f556'),
(36,'user','aafb621b-b0ec-39ab-99b5-54648b460094'),
(40,'user','ab1eb589-71f8-34a3-86d9-77440bacfc6f'),
(44,'user','ac24d8ab-607a-3807-8062-cb41d964f9aa'),
(49,'user','ae372a5c-7f11-3c77-925f-d0822ab0b72e'),
(55,'user','b1e88d65-8a0f-3d24-aa60-32dd2847dc49'),
(47,'user','b39e07ec-1ca2-359e-98e9-7efb557e5e04'),
(36,'user','b5025d53-64f2-4238-9531-f81cf0840f2c'),
(56,'user','bb9b4f88-fd05-3a59-afc5-edf6346f5d4d'),
(39,'user','bda65430-525b-3952-8bba-2aba9c14a3cb'),
(46,'user','bed3959d-f13c-367c-ac10-89422d423aef'),
(49,'user','bfdf11ce-0d29-3cf2-8c32-fd77a1066f68'),
(46,'user','c4f44c34-a2f5-3a3a-9216-6fb46eed0eea'),
(50,'user','c5e6a70f-e1a7-3863-aa8d-a1887f256af2'),
(46,'user','c6afe082-1744-3f07-9ea1-cbb17e8a9e05'),
(50,'user','c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1'),
(39,'user','c947ef5d-4b9b-3fab-850f-538c72c47dd1'),
(36,'user','cb680141-e4d8-3113-9b22-2314598f1fb6'),
(46,'user','cd88fc77-2791-300a-b1b6-3a8077dc0de1'),
(43,'user','d22feddd-c547-3633-a0e7-e3c62328f26a'),
(50,'user','d2d72092-8e3e-3015-ad8b-14def999d093'),
(42,'user','d38f6118-1921-319e-b294-32743f78c426'),
(56,'user','d3df3ca0-c239-3828-a331-f97d71fc0b8b'),
(36,'user','d6793bc3-c1b4-350e-8123-f18ff6258ef0'),
(43,'user','d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb'),
(49,'user','d9a3cf9e-8e16-35ac-b437-387e412f148d'),
(49,'user','db809f7c-9862-379a-babe-3ed2aa72e4ce'),
(46,'user','dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70'),
(47,'user','dec48d4e-030a-3422-8d24-ccac1b9f904e'),
(56,'user','dffd6369-14e8-33e4-bf8e-72416d72a9a4'),
(36,'user','e3e53915-e9c8-3496-bc9c-a43ec860e82c'),
(40,'user','e5e408cd-5186-394e-8856-bbfed4d5292d'),
(56,'user','e94cc382-2daa-3f67-b05b-d8a96f6750a8'),
(46,'user','eb291dee-6787-3f0a-b454-d20c7d3e16a2'),
(37,'user','edebbd60-00aa-347f-8480-077ea06e48e6'),
(57,'user','ee368c08-bfeb-47a2-aa3e-12990b2aec0a'),
(42,'user','efb7bedd-60c8-37d5-895c-10435fa688ba'),
(50,'user','f1e80959-4086-35dd-a23d-aa1efd7cc653'),
(51,'user','f8583630-8f77-3229-936a-b667bedb97fa'),
(43,'user','fab700d2-77d7-38c3-b15b-5772dd7bbdb9'),
(49,'user','fba7afbc-b56f-3385-958b-ca58ffba8738'),
(51,'user','ffe79fca-3076-3151-a954-a1c1660b067d');
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
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `permissions` VALUES
(1,'view-roles','web',0,'View all roles','system','2026-01-08 19:00:26','2026-01-08 19:00:26'),
(2,'create-roles','web',0,'Create new roles','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(3,'edit-roles','web',0,'Edit roles','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(4,'delete-roles','web',0,'Delete roles','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(5,'assign-roles','web',0,'Assign roles to users','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(6,'view-permissions','web',0,'View all permissions','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(7,'manage-permissions','web',0,'Manage permissions','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(8,'view-branches','web',0,'View all branches','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(9,'create-branches','web',0,'Create new branches','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(10,'edit-branches','web',0,'Edit branch information','system','2026-01-08 19:00:27','2026-01-08 19:00:27'),
(11,'delete-branches','web',0,'Delete branches','system','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(12,'manage-settings','web',0,'Manage system settings','system','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(13,'view-audit-logs','web',0,'View audit logs','system','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(14,'view-activity-logs','web',0,'View activity logs','system','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(15,'manage-system','web',0,'Full system management','system','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(16,'view-employees','web',0,'View employee list','hr','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(17,'create-employees','web',0,'Create new employees','hr','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(18,'edit-employees','web',0,'Edit employee information','hr','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(19,'delete-employees','web',0,'Delete employee records','hr','2026-01-08 19:00:28','2026-01-08 19:00:28'),
(20,'view-departments','web',0,'View departments','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(21,'create-departments','web',0,'Create departments','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(22,'edit-departments','web',0,'Edit departments','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(23,'delete-departments','web',0,'Delete departments','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(24,'manage-staff-schedule','web',0,'Manage employee schedules','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(25,'manage-leave','web',0,'Manage employee leave','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(26,'approve-leave','web',0,'Approve leave requests','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(27,'view-payroll','web',0,'View payroll information','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(28,'manage-payroll','web',0,'Manage payroll','hr','2026-01-08 19:00:29','2026-01-08 19:00:29'),
(29,'view-hr-reports','web',0,'View HR reports','hr','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(30,'manage-roles-assignments','web',0,'Manage employee role assignments','hr','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(31,'view-employee-details','web',0,'View detailed employee information','hr','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(32,'view-production-queue','web',0,'View production queue','production','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(33,'create-production-order','web',0,'Create production orders','production','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(34,'start-production','web',0,'Start production batches','production','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(35,'complete-production','web',0,'Complete production batches','production','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(36,'approve-production','web',0,'Approve production','production','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(37,'manage-recipes','web',0,'Create and edit recipes','production','2026-01-08 19:00:30','2026-01-08 19:00:30'),
(38,'view-recipes','web',0,'View recipes','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(39,'view-production-reports','web',0,'View production analytics','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(40,'manage-quality-control','web',0,'Manage quality control','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(41,'view-batch-history','web',0,'View production batch history','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(42,'edit-production-order','web',0,'Edit production orders','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(43,'cancel-production','web',0,'Cancel production orders','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(44,'view-production-cost','web',0,'View production costs','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(45,'manage-production-settings','web',0,'Manage production settings','production','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(46,'view-stock-levels','web',0,'View stock levels','inventory','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(47,'receive-stock','web',0,'Receive inventory','inventory','2026-01-08 19:00:31','2026-01-08 19:00:31'),
(48,'transfer-stock','web',0,'Transfer stock between locations','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(49,'adjust-inventory','web',0,'Adjust inventory counts','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(50,'create-purchase-order','web',0,'Create purchase orders','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(51,'approve-purchase-order','web',0,'Approve purchase orders','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(52,'view-inventory-reports','web',0,'View inventory reports','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(53,'manage-suppliers','web',0,'Full supplier management','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(54,'view-suppliers','web',0,'View suppliers list','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(55,'create-suppliers','web',0,'Create new suppliers','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(56,'edit-suppliers','web',0,'Edit supplier information','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(57,'delete-suppliers','web',0,'Delete suppliers','inventory','2026-01-08 19:00:32','2026-01-08 19:00:32'),
(58,'view-stock-valuation','web',0,'View stock valuation','inventory','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(59,'manage-stock-categories','web',0,'Manage stock categories','inventory','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(60,'view-reorder-levels','web',0,'View reorder levels','inventory','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(61,'manage-reorder-levels','web',0,'Manage reorder levels','inventory','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(62,'write-off-stock','web',0,'Write off stock items','inventory','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(63,'view-stock-history','web',0,'View stock transaction history','inventory','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(64,'manage-inventory-settings','web',0,'Manage inventory settings','inventory','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(65,'view-sales-dashboard','web',0,'Access sales dashboard','sales','2026-01-08 19:00:33','2026-01-08 19:00:33'),
(66,'process-sale','web',0,'Process sales transactions','sales','2026-01-08 19:00:34','2026-01-08 19:00:34'),
(67,'issue-refund','web',0,'Issue refunds','sales','2026-01-08 19:00:34','2026-01-08 19:00:34'),
(68,'view-daily-sales','web',0,'View daily sales','sales','2026-01-08 19:00:34','2026-01-08 19:00:34'),
(69,'close-register','web',0,'Close cash registers','sales','2026-01-08 19:00:34','2026-01-08 19:00:34'),
(70,'view-sales-reports','web',0,'View sales analytics','sales','2026-01-08 19:00:34','2026-01-08 19:00:34'),
(71,'manage-sales-discounts','web',0,'Manage sales discounts','sales','2026-01-08 19:00:34','2026-01-08 19:00:34'),
(72,'view-sales-transactions','web',0,'View sales transactions','sales','2026-01-08 19:00:34','2026-01-08 19:00:34'),
(73,'edit-sales-transactions','web',0,'Edit sales transactions','sales','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(74,'void-sales-transactions','web',0,'Void sales transactions','sales','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(75,'manage-payment-methods','web',0,'Manage payment methods','sales','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(76,'view-till-records','web',0,'View till/register records','sales','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(77,'view-chart-accounts','web',0,'View chart of accounts','accounting','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(78,'create-accounts','web',0,'Create general ledger accounts','accounting','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(79,'edit-accounts','web',0,'Edit general ledger accounts','accounting','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(80,'view-gl-entries','web',0,'View general ledger entries','accounting','2026-01-08 19:00:35','2026-01-08 19:00:35'),
(81,'create-gl-entries','web',0,'Create GL entries','accounting','2026-01-08 19:00:36','2026-01-08 19:00:36'),
(82,'post-gl-entries','web',0,'Post GL entries','accounting','2026-01-08 19:00:36','2026-01-08 19:00:36'),
(83,'reverse-gl-entries','web',0,'Reverse GL entries','accounting','2026-01-08 19:00:36','2026-01-08 19:00:36'),
(84,'view-accounting-reports','web',0,'View accounting reports','accounting','2026-01-08 19:00:36','2026-01-08 19:00:36'),
(85,'reconcile-accounts','web',0,'Reconcile bank accounts','accounting','2026-01-08 19:00:36','2026-01-08 19:00:36'),
(86,'manage-bank-accounts','web',0,'Manage bank accounts','accounting','2026-01-08 19:00:36','2026-01-08 19:00:36'),
(87,'view-trial-balance','web',0,'View trial balance','accounting','2026-01-08 19:00:37','2026-01-08 19:00:37'),
(88,'view-financial-statements','web',0,'View financial statements','accounting','2026-01-08 19:00:37','2026-01-08 19:00:37'),
(89,'manage-accounting-period','web',0,'Manage accounting periods','accounting','2026-01-08 19:00:37','2026-01-08 19:00:37'),
(90,'view-account-reconciliation','web',0,'View account reconciliation','accounting','2026-01-08 19:00:37','2026-01-08 19:00:37'),
(91,'view-analytics','web',0,'View analytics dashboard','reporting','2026-01-08 19:00:37','2026-01-08 19:00:37'),
(92,'view-department-reports','web',0,'View department reports','reporting','2026-01-08 19:00:37','2026-01-08 19:00:37'),
(93,'generate-reports','web',0,'Generate custom reports','reporting','2026-01-08 19:00:38','2026-01-08 19:00:38'),
(94,'export-reports','web',0,'Export reports to files','reporting','2026-01-08 19:00:38','2026-01-08 19:00:38'),
(95,'schedule-reports','web',0,'Schedule automated reports','reporting','2026-01-08 19:00:38','2026-01-08 19:00:38'),
(96,'view-dashboard','web',0,'View main dashboard','reporting','2026-01-08 19:00:38','2026-01-08 19:00:38'),
(97,'view-branch-reports','web',0,'View branch-specific reports','reporting','2026-01-08 19:00:38','2026-01-08 19:00:38'),
(98,'view-kpi-metrics','web',0,'View KPI metrics','reporting','2026-01-08 19:00:38','2026-01-08 19:00:38'),
(99,'export-data','web',0,'Export system data','reporting','2026-01-08 19:00:38','2026-01-08 19:00:38'),
(100,'view-activity-timeline','web',0,'View activity timeline','reporting','2026-01-08 19:00:39','2026-01-08 19:00:39'),
(101,'view_inventory_dashboard','web',0,'Access inventory dashboard','dashboard','2026-01-08 19:00:39','2026-01-08 19:00:39'),
(102,'view_production_dashboard','web',0,'Access production dashboard','dashboard','2026-01-08 19:00:39','2026-01-08 19:00:39'),
(103,'view_sales_dashboard','web',0,'Access sales dashboard','dashboard','2026-01-08 19:00:39','2026-01-08 19:00:39'),
(104,'view_corner_store_dashboard','web',0,'Access corner store dashboard','dashboard','2026-01-08 19:00:39','2026-01-08 19:00:39'),
(105,'view_hr_dashboard','web',0,'Access HR dashboard','dashboard','2026-01-08 19:00:40','2026-01-08 19:00:40'),
(106,'view_admin_dashboard','web',0,'Access admin dashboard','dashboard','2026-01-08 19:00:40','2026-01-08 19:00:40'),
(107,'view_super_admin_dashboard','web',0,'Access super admin dashboard','dashboard','2026-01-08 19:00:40','2026-01-08 19:00:40'),
(108,'manage_organization','web',0,'Manage organization (employees, departments)','organization','2026-01-08 19:00:40','2026-01-08 19:00:40'),
(109,'manage_roles','web',0,'Manage user roles and permissions','organization','2026-01-08 19:00:40','2026-01-08 19:00:40'),
(110,'manage_branches','web',0,'Manage branches','organization','2026-01-08 19:00:40','2026-01-08 19:00:40'),
(111,'manage_settings','web',0,'Manage system settings','organization','2026-01-08 19:00:40','2026-01-08 19:00:40'),
(112,'view_reports','web',0,'View system reports','organization','2026-01-08 19:00:41','2026-01-08 19:00:41'),
(113,'access_accounting','web',0,'Access accounting module','accounting','2026-01-08 19:00:41','2026-01-08 19:00:41'),
(114,'view_financial_reports','web',0,'View financial reports','accounting','2026-01-08 19:00:41','2026-01-08 19:00:41'),
(115,'manage_accounts','web',0,'Manage chart of accounts','accounting','2026-01-08 19:00:41','2026-01-08 19:00:41'),
(116,'manage_periods','web',0,'Manage accounting periods','accounting','2026-01-08 19:00:41','2026-01-08 19:00:41'),
(117,'create_journal_entries','web',0,'Create journal entries','accounting','2026-01-08 19:00:41','2026-01-08 19:00:41'),
(118,'reconcile_bank_accounts','web',0,'Reconcile bank accounts','accounting','2026-01-08 19:00:41','2026-01-08 19:00:41'),
(119,'manage.users','web',0,NULL,NULL,'2026-01-08 19:01:46','2026-01-08 19:01:46'),
(120,'manage.roles','web',0,NULL,NULL,'2026-01-08 19:01:46','2026-01-08 19:01:46'),
(121,'manage.permissions','web',0,NULL,NULL,'2026-01-08 19:01:46','2026-01-08 19:01:46'),
(122,'manage.branches','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(123,'manage.departments','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(124,'manage.categories','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(125,'view.audit_trail','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(126,'manage.settings','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(127,'manage.system_config','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(128,'view.daily_produce','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(129,'record.daily_produce','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(130,'edit.daily_produce','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(131,'delete.daily_produce','web',0,NULL,NULL,'2026-01-08 19:01:47','2026-01-08 19:01:47'),
(132,'view.recipes','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(133,'edit.recipes','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(134,'delete.recipes','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(135,'manage.recipes','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(136,'view.products','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(137,'edit.products','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(138,'delete.products','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(139,'view.requests','web',0,NULL,NULL,'2026-01-08 19:01:48','2026-01-08 19:01:48'),
(140,'create.requests','web',0,NULL,NULL,'2026-01-08 19:01:49','2026-01-08 19:01:49'),
(141,'edit.requests','web',0,NULL,NULL,'2026-01-08 19:01:49','2026-01-08 19:01:49'),
(142,'approve.requests','web',0,NULL,NULL,'2026-01-08 19:01:49','2026-01-08 19:01:49'),
(143,'reject.requests','web',0,NULL,NULL,'2026-01-08 19:01:49','2026-01-08 19:01:49'),
(144,'view.stock_takes','web',0,NULL,NULL,'2026-01-08 19:01:49','2026-01-08 19:01:49'),
(145,'conduct.stock_take','web',0,NULL,NULL,'2026-01-08 19:01:49','2026-01-08 19:01:49'),
(146,'verify.stock_take','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(147,'report.production','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(148,'view.pos','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(149,'process.sale','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(150,'edit.sale','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(151,'void.sale','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(152,'refund.sale','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(153,'view.my_sales','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(154,'view.sales_analytics','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(155,'manage.price_override','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(156,'report.sales','web',0,NULL,NULL,'2026-01-08 19:01:50','2026-01-08 19:01:50'),
(157,'view.stock_levels','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(158,'record.stock_movement','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(159,'adjust.stock','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(160,'approve.stock_movement','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(161,'report.inventory','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(162,'manage.stock_items','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(163,'manage.employees','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(164,'view.employees','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(165,'edit.employee_profile','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(166,'manage.leave','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(167,'approve.leave','web',0,NULL,NULL,'2026-01-08 19:01:51','2026-01-08 19:01:51'),
(168,'view.attendance','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(169,'report.hr','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(170,'view.financial_reports','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(171,'manage.accounts','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(172,'approve.transactions','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(173,'report.accounting','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(174,'view.callbacks','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(175,'record.callback','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(176,'manage.callbacks','web',0,NULL,NULL,'2026-01-08 19:01:52','2026-01-08 19:01:52'),
(177,'view.dispatches','web',0,NULL,NULL,'2026-01-08 19:01:53','2026-01-08 19:01:53'),
(178,'manage.dispatches','web',0,NULL,NULL,'2026-01-08 19:01:53','2026-01-08 19:01:53'),
(179,'view.users','web',0,NULL,NULL,'2026-01-08 19:01:53','2026-01-08 19:01:53'),
(180,'edit.users','web',0,NULL,NULL,'2026-01-08 19:01:53','2026-01-08 19:01:53'),
(181,'view.dashboard','web',0,NULL,NULL,'2026-01-08 19:01:55','2026-01-08 19:01:55'),
(182,'view.reports','web',0,NULL,NULL,'2026-01-08 19:01:57','2026-01-08 19:01:57'),
(183,'view_organization','web',0,'View organization',NULL,'2026-01-09 02:56:02','2026-01-09 02:56:02'),
(184,'edit_organization','web',0,'Edit organization',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(185,'create_organization','web',0,'Create organization',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(186,'delete_organization','web',0,'Delete organization',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(187,'view_employees','web',0,'View employees',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(188,'create_employee','web',0,'Create employee',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(189,'edit_employee','web',0,'Edit employee',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(190,'delete_employee','web',0,'Delete employee',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(191,'manage_employee_roles','web',0,'Manage employee roles',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(192,'manage_employee_shifts','web',0,'Manage employee shifts',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(193,'view_leave_applications','web',0,'View leave applications',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(194,'approve_leave','web',0,'Approve leave',NULL,'2026-01-09 02:56:03','2026-01-09 02:56:03'),
(195,'reject_leave','web',0,'Reject leave',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(196,'create_leave_type','web',0,'Create leave type',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(197,'edit_leave_type','web',0,'Edit leave type',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(198,'delete_leave_type','web',0,'Delete leave type',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(199,'view_leave_balance','web',0,'View leave balance',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(200,'view_all_accounting','web',0,'View all accounting',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(201,'view_gl_accounts','web',0,'View gl accounts',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(202,'create_gl_entry','web',0,'Create gl entry',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(203,'view_transactions','web',0,'View transactions',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(204,'export_accounting_data','web',0,'Export accounting data',NULL,'2026-01-09 02:56:04','2026-01-09 02:56:04'),
(205,'view_payroll','web',0,'View payroll',NULL,'2026-01-09 02:56:05','2026-01-09 02:56:05'),
(206,'process_payroll','web',0,'Process payroll',NULL,'2026-01-09 02:56:05','2026-01-09 02:56:05'),
(207,'view_salary_history','web',0,'View salary history',NULL,'2026-01-09 02:56:05','2026-01-09 02:56:05'),
(208,'view_production_callbacks','web',0,'View production callbacks',NULL,'2026-01-09 02:56:05','2026-01-09 02:56:05'),
(209,'view_dispatch_callbacks','web',0,'View dispatch callbacks',NULL,'2026-01-09 02:56:05','2026-01-09 02:56:05'),
(210,'view_inventory_callbacks','web',0,'View inventory callbacks',NULL,'2026-01-09 02:56:05','2026-01-09 02:56:05'),
(211,'view_employee_details','web',0,'View employee details',NULL,'2026-01-09 03:05:15','2026-01-09 03:05:15'),
(212,'allocate_leave','web',0,'Allocate leave',NULL,'2026-01-09 03:05:15','2026-01-09 03:05:15'),
(213,'view_inventory','web',0,'View inventory',NULL,'2026-01-09 03:05:15','2026-01-09 03:05:15'),
(214,'create_inventory_item','web',0,'Create inventory item',NULL,'2026-01-09 03:05:15','2026-01-09 03:05:15'),
(215,'edit_inventory_item','web',0,'Edit inventory item',NULL,'2026-01-09 03:05:16','2026-01-09 03:05:16'),
(216,'delete_inventory_item','web',0,'Delete inventory item',NULL,'2026-01-09 03:05:16','2026-01-09 03:05:16'),
(217,'adjust_inventory','web',0,'Adjust inventory',NULL,'2026-01-09 03:05:16','2026-01-09 03:05:16'),
(218,'view_stock_levels','web',0,'View stock levels',NULL,'2026-01-09 03:05:16','2026-01-09 03:05:16'),
(219,'receive_stock','web',0,'Receive stock',NULL,'2026-01-09 03:05:16','2026-01-09 03:05:16'),
(220,'transfer_stock','web',0,'Transfer stock',NULL,'2026-01-09 03:05:16','2026-01-09 03:05:16'),
(221,'view_accounting_own_dept','web',0,'View accounting own dept',NULL,'2026-01-09 03:05:17','2026-01-09 03:05:17'),
(222,'edit_gl_entry','web',0,'Edit gl entry',NULL,'2026-01-09 03:05:17','2026-01-09 03:05:17'),
(223,'delete_gl_entry','web',0,'Delete gl entry',NULL,'2026-01-09 03:05:17','2026-01-09 03:05:17'),
(224,'process_invoices','web',0,'Process invoices',NULL,'2026-01-09 03:05:17','2026-01-09 03:05:17'),
(225,'manage_payments','web',0,'Manage payments',NULL,'2026-01-09 03:05:17','2026-01-09 03:05:17'),
(226,'edit_salary','web',0,'Edit salary',NULL,'2026-01-09 03:05:17','2026-01-09 03:05:17'),
(227,'approve_payroll','web',0,'Approve payroll',NULL,'2026-01-09 03:05:17','2026-01-09 03:05:17'),
(228,'view_production_queue','web',0,'View production queue',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(229,'start_production','web',0,'Start production',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(230,'complete_production','web',0,'Complete production',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(231,'create_production_callback','web',0,'Create production callback',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(232,'create_dispatch_callback','web',0,'Create dispatch callback',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(233,'manage_recipes','web',0,'Manage recipes',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(234,'view_recipes','web',0,'View recipes',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(235,'create_recipe','web',0,'Create recipe',NULL,'2026-01-09 03:05:18','2026-01-09 03:05:18'),
(236,'edit_recipe','web',0,'Edit recipe',NULL,'2026-01-09 03:05:19','2026-01-09 03:05:19'),
(237,'delete_recipe','web',0,'Delete recipe',NULL,'2026-01-09 03:05:19','2026-01-09 03:05:19'),
(238,'process_sale','web',0,'Process sale',NULL,'2026-01-09 03:05:19','2026-01-09 03:05:19'),
(239,'view_daily_sales','web',0,'View daily sales',NULL,'2026-01-09 03:05:19','2026-01-09 03:05:19'),
(240,'view_sales_reports','web',0,'View sales reports',NULL,'2026-01-09 03:05:19','2026-01-09 03:05:19'),
(241,'manage_prices','web',0,'Manage prices',NULL,'2026-01-09 03:05:19','2026-01-09 03:05:19'),
(242,'view_invoices','web',0,'View invoices',NULL,'2026-01-09 03:05:20','2026-01-09 03:05:20'),
(243,'create_invoice','web',0,'Create invoice',NULL,'2026-01-09 03:05:20','2026-01-09 03:05:20'),
(244,'generate_reports','web',0,'Generate reports',NULL,'2026-01-09 03:05:20','2026-01-09 03:05:20'),
(245,'view_analytics','web',0,'View analytics',NULL,'2026-01-09 03:05:20','2026-01-09 03:05:20'),
(246,'export_data','web',0,'Export data',NULL,'2026-01-09 03:05:20','2026-01-09 03:05:20'),
(247,'view_branch_metrics','web',0,'View branch metrics',NULL,'2026-01-09 03:05:21','2026-01-09 03:05:21'),
(248,'view_settings','web',0,'View settings',NULL,'2026-01-09 03:05:21','2026-01-09 03:05:21'),
(249,'edit_settings','web',0,'Edit settings',NULL,'2026-01-09 03:05:21','2026-01-09 03:05:21'),
(250,'view_system_settings','web',0,'View system settings',NULL,'2026-01-09 03:05:21','2026-01-09 03:05:21'),
(251,'manage_users','web',0,'Manage users',NULL,'2026-01-09 03:05:21','2026-01-09 03:05:21'),
(252,'manage_permissions','web',0,'Manage permissions',NULL,'2026-01-09 03:05:21','2026-01-09 03:05:21'),
(253,'create_gl_accounts','web',0,'Create GL accounts',NULL,'2026-01-09 03:37:00','2026-01-09 03:37:00'),
(254,'edit_gl_accounts','web',0,'Edit GL accounts',NULL,'2026-01-09 03:37:00','2026-01-09 03:37:00'),
(255,'delete_gl_accounts','web',0,'Delete GL accounts',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(256,'view_gl_entries','web',0,'View GL entries',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(257,'approve_gl_entries','web',0,'Approve pending journal entries',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(258,'reverse_gl_entries','web',0,'Reverse posted journal entries',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(259,'post_gl_entries','web',0,'Post journal entries',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(260,'view_bank_accounts','web',0,'View bank accounts',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(261,'create_bank_accounts','web',0,'Create bank accounts',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(262,'edit_bank_accounts','web',0,'Edit bank accounts',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(263,'view_daily_bank_positions','web',0,'View daily bank positions',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(264,'view_cash_positions','web',0,'View cash positions',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(265,'record_cash_count','web',0,'Record physical cash counts',NULL,'2026-01-09 03:37:01','2026-01-09 03:37:01'),
(266,'view_accounting_periods','web',0,'View accounting periods',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(267,'create_accounting_periods','web',0,'Create accounting periods',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(268,'close_accounting_periods','web',0,'Close accounting periods',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(269,'lock_accounting_periods','web',0,'Lock accounting periods',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(270,'reopen_accounting_periods','web',0,'Reopen closed periods',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(271,'view_general_ledger','web',0,'View general ledger report',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(272,'view_trial_balance','web',0,'View trial balance report',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(273,'view_balance_sheet','web',0,'View balance sheet report',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(274,'view_income_statement','web',0,'View income statement report',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(275,'view_cash_flow_statement','web',0,'View cash flow statement',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(276,'export_financial_reports','web',0,'Export financial reports to PDF/Excel',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(277,'view_bank_reconciliation','web',0,'View bank reconciliation reports',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(278,'upload_bank_statements','web',0,'Upload bank statements',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(279,'view_accounting_dashboard','web',0,'View accounting dashboard',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(280,'view_financial_summary','web',0,'View financial summary',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(281,'view_accounting_reports','web',0,'View accounting reports',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(282,'view_variance_analysis','web',0,'View variance analysis',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(283,'view_aging_reports','web',0,'View aging reports (AR/AP)',NULL,'2026-01-09 03:37:02','2026-01-09 03:37:02'),
(284,'link_sales_to_gl','web',0,'Link sales transactions to GL',NULL,'2026-01-09 03:37:03','2026-01-09 03:37:03'),
(285,'link_purchases_to_gl','web',0,'Link purchases to GL',NULL,'2026-01-09 03:37:03','2026-01-09 03:37:03'),
(286,'link_payments_to_gl','web',0,'Link payments to GL',NULL,'2026-01-09 03:37:03','2026-01-09 03:37:03'),
(287,'view-inventory-management','web',0,NULL,NULL,NULL,NULL);
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
(1,1,'Pastries','PT','Baked pastries including croissants, danishes, and puff pastries','active',1,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(2,1,'Breads','BR','Fresh baked breads, loaves, and rolls','active',2,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(3,1,'Cakes','CK','Layer cakes, sponge cakes, and celebration cakes','active',3,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(4,1,'Cookies','CO','Baked cookies and biscuits','active',4,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(5,1,'Muffins','MF','Sweet and savory muffins','active',5,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(6,1,'Pies & Tarts','PIT','Fruit pies, cream pies, and tarts','active',6,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(7,1,'Sandwiches','SW','Fresh sandwiches and wraps','active',7,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(8,1,'Hot Kitchen','HK','Cooked meals and hot food items','active',8,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(9,2,'Gelato Base','GB','Base gelato mixtures before flavoring','active',1,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(10,2,'Gelato Flavors','GF','Finished gelato in various flavors','active',2,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(11,2,'Sorbet','SB','Fruit-based frozen desserts without dairy','active',3,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(12,2,'Ice Cream','IC','Traditional ice cream products','active',4,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(13,2,'Frozen Yogurt','FY','Frozen yogurt in various flavors','active',5,'2026-01-08 19:01:29','2026-01-08 19:01:29',NULL),
(14,2,'Gelato Toppings','GT','House-made toppings and mix-ins for gelato','active',6,'2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
(15,3,'Chocolates','CH','Handcrafted chocolates and truffles','active',1,'2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
(16,3,'Candies','CD','Hard and soft candies','active',2,'2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
(17,3,'Fudge','FG','Traditional and flavored fudge','active',3,'2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
(18,3,'Caramels','CR','Soft and hard caramels','active',4,'2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
(19,3,'Marshmallows','MM','Gourmet marshmallows in various flavors','active',5,'2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
(20,3,'Nougat','NG','Traditional nougat confections','active',6,'2026-01-08 19:01:30','2026-01-08 19:01:30',NULL);
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
('019b9efc-4095-71c4-945b-fce6e608db8f','Butter Croissant','PT-BUT-001','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,'Classic French butter croissant, flaky and golden',3.50,1.20,2,13,100.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"breakfast\",\"french\",\"popular\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-4100-70ae-b9a2-1b366d55acc6','Almond Danish','PT-ALM-002','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,'Sweet Danish pastry topped with sliced almonds',4.00,1.50,2,13,150.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\",\"nuts\"]','[\"breakfast\",\"pastry\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-411d-720a-940c-eae17b48e3b8','Sourdough Loaf','BR-SOU-001','019b9efb-b43d-73c7-95bd-0687cf05c811',2,NULL,'Artisan sourdough bread with tangy flavor',6.50,2.00,4,13,800.00,1,1,NULL,'[\"gluten\"]','[\"artisan\",\"sourdough\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-412e-720c-a191-17b14717a68e','Banana Bread','BR-BAN-002','019b9efb-b43d-73c7-95bd-0687cf05c811',2,NULL,'Moist banana bread with walnuts',5.99,2.50,7,13,900.00,1,1,NULL,'[\"gluten\",\"eggs\",\"nuts\"]','[\"sweet\",\"popular\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-413e-729d-a4b3-52fd22463621','Chocolate Cake Slice','CK-CHO-001','019b9efb-b43d-73c7-95bd-0687cf05c811',3,NULL,'Rich chocolate layer cake with chocolate ganache',7.50,2.80,5,13,200.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"chocolate\",\"dessert\",\"popular\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-414c-72f2-ab3c-cb157afccc89','Chocolate Chip Cookie','CO-CHI-001','019b9efb-b43d-73c7-95bd-0687cf05c811',4,NULL,'Classic chocolate chip cookies',2.50,0.80,10,13,50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"popular\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-415b-7212-a0f8-c2150414e426','Oatmeal Raisin Cookie','CO-OAT-002','019b9efb-b43d-73c7-95bd-0687cf05c811',4,NULL,'Wholesome oatmeal cookies with plump raisins',2.50,0.75,10,13,50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"healthy\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-41c9-72e2-9236-af03dff26491','Vanilla Gelato Base','GB-VAN-001','019b9efb-b43d-73c7-95bd-0687cf05c811',9,NULL,'Premium vanilla gelato base mixture',0.00,8.50,3,3,1000.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"base\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-41e5-728d-8bc9-e9e9d9788c8e','Chocolate Gelato','GF-CHO-001','019b9efb-b43d-73c7-95bd-0687cf05c811',10,NULL,'Rich dark chocolate gelato',4.50,1.80,30,2,100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"chocolate\",\"popular\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-4231-73bb-96e4-f667f957e148','Strawberry Gelato','GF-STR-002','019b9efb-b43d-73c7-95bd-0687cf05c811',10,NULL,'Fresh strawberry gelato',4.50,1.90,30,2,100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"fruit\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-42a0-7200-91a5-4a3e4b72fc32','Pistachio Gelato','GF-PIS-003','019b9efb-b43d-73c7-95bd-0687cf05c811',10,NULL,'Authentic pistachio gelato from Sicily',5.50,2.50,30,2,100.00,1,1,NULL,'[\"dairy\",\"nuts\"]','[\"gelato\",\"premium\",\"nuts\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-42ba-71cc-b884-f553e035afc1','Dark Chocolate Truffle','CH-DAR-001','019b9efb-b43d-73c7-95bd-0687cf05c811',15,NULL,'Hand-rolled dark chocolate truffle',2.50,0.90,21,13,20.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"premium\",\"truffle\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-42cf-720e-bbc0-2a19e15fbd1f','Salted Caramel Chocolate','CH-SAL-002','019b9efb-b43d-73c7-95bd-0687cf05c811',15,NULL,'Milk chocolate with salted caramel filling',2.75,1.00,21,13,25.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"caramel\",\"popular\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL),
('019b9efc-42da-705b-af43-c4f26f2d269d','Fruit Gummies','CD-FRU-001','019b9efb-b43d-73c7-95bd-0687cf05c811',16,NULL,'Assorted fruit-flavored gummy candies',1.50,0.40,180,2,100.00,1,1,NULL,'[]','[\"candy\",\"fruit\",\"kids\"]','2026-01-08 19:01:30','2026-01-08 19:01:30',NULL);
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
(1,1,2,500.0000,2,0.0020,5.00,0,'Required for Butter Croissant','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(2,1,4,300.0000,2,0.0080,2.00,1,'Required for Butter Croissant','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(3,1,1,50.0000,2,0.0010,3.00,2,'Required for Butter Croissant','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(4,1,8,150.0000,6,0.0030,1.00,3,'Required for Butter Croissant','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(5,1,5,0.5000,14,15.0000,0.00,4,'Required for Butter Croissant','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(6,1,10,15.0000,2,0.0200,0.00,5,'Required for Butter Croissant','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(7,1,12,10.0000,2,0.0010,0.00,6,'Required for Butter Croissant','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(8,2,2,280.0000,2,0.0020,5.00,0,'Required for Chocolate Chip Cookie','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(9,2,4,225.0000,2,0.0080,2.00,1,'Required for Chocolate Chip Cookie','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(10,2,1,200.0000,2,0.0010,3.00,2,'Required for Chocolate Chip Cookie','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(11,2,5,0.5000,14,15.0000,0.00,3,'Required for Chocolate Chip Cookie','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(12,2,6,0.0100,7,50.0000,0.00,4,'Required for Chocolate Chip Cookie','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(13,2,7,300.0000,2,0.0150,1.00,5,'Required for Chocolate Chip Cookie','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(14,3,2,250.0000,2,0.0020,5.00,0,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(15,3,3,75.0000,2,0.0250,2.00,1,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(16,3,1,400.0000,2,0.0010,3.00,2,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(17,3,5,0.6700,14,15.0000,0.00,3,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(18,3,11,125.0000,6,0.0050,1.00,4,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(19,3,6,0.0100,7,50.0000,0.00,5,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(20,3,9,200.0000,6,0.0100,1.00,6,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(21,3,7,200.0000,2,0.0150,1.00,7,'Required for Chocolate Cake Slice','Standard preparation','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(22,4,8,2.0000,7,3.0000,2.00,0,'Required for Chocolate Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(23,4,9,1.5000,7,10.0000,2.00,1,'Required for Chocolate Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(24,4,1,600.0000,2,0.0010,3.00,2,'Required for Chocolate Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(25,4,3,200.0000,2,0.0250,2.00,3,'Required for Chocolate Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(26,4,5,1.0000,14,15.0000,0.00,4,'Required for Chocolate Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(27,5,8,1.8000,7,3.0000,2.00,0,'Required for Strawberry Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(28,5,9,1.2000,7,10.0000,2.00,1,'Required for Strawberry Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(29,5,1,550.0000,2,0.0010,3.00,2,'Required for Strawberry Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(30,5,5,0.8000,14,15.0000,0.00,3,'Required for Strawberry Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(31,6,8,1.8000,7,3.0000,2.00,0,'Required for Pistachio Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(32,6,9,1.3000,7,10.0000,2.00,1,'Required for Pistachio Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(33,6,1,580.0000,2,0.0010,3.00,2,'Required for Pistachio Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(34,6,5,0.9000,14,15.0000,0.00,3,'Required for Pistachio Gelato','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(35,7,2,450.0000,2,0.0020,5.00,0,'Required for Almond Danish','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(36,7,4,280.0000,2,0.0080,2.00,1,'Required for Almond Danish','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(37,7,1,120.0000,2,0.0010,3.00,2,'Required for Almond Danish','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(38,7,5,0.6000,14,15.0000,0.00,3,'Required for Almond Danish','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(39,7,8,100.0000,6,0.0030,1.00,4,'Required for Almond Danish','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(40,7,6,0.0050,7,50.0000,0.00,5,'Required for Almond Danish','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(41,8,2,220.0000,2,0.0020,5.00,0,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(42,8,4,200.0000,2,0.0080,2.00,1,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(43,8,1,180.0000,2,0.0010,3.00,2,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(44,8,5,0.5000,14,15.0000,0.00,3,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(45,8,6,0.0080,7,50.0000,0.00,4,'Required for Oatmeal Raisin Cookie','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(46,9,2,1000.0000,2,0.0020,5.00,0,'Required for Sourdough Loaf','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(47,9,12,20.0000,2,0.0010,0.00,1,'Required for Sourdough Loaf','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(48,9,10,10.0000,2,0.0200,0.00,2,'Required for Sourdough Loaf','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(49,10,2,280.0000,2,0.0020,5.00,0,'Required for Banana Bread','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(50,10,1,200.0000,2,0.0010,3.00,1,'Required for Banana Bread','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(51,10,4,120.0000,2,0.0080,2.00,2,'Required for Banana Bread','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(52,10,5,0.5000,14,15.0000,0.00,3,'Required for Banana Bread','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(53,10,11,80.0000,6,0.0050,1.00,4,'Required for Banana Bread','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(54,10,6,0.0050,7,50.0000,0.00,5,'Required for Banana Bread','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(55,11,7,500.0000,2,0.0150,2.00,0,'Required for Dark Chocolate Truffle','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(56,11,9,300.0000,6,0.0100,1.00,1,'Required for Dark Chocolate Truffle','Standard preparation','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(57,11,4,50.0000,2,0.0080,1.00,2,'Required for Dark Chocolate Truffle','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(58,11,3,100.0000,2,0.0250,2.00,3,'Required for Dark Chocolate Truffle','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(59,11,6,0.0050,7,50.0000,0.00,4,'Required for Dark Chocolate Truffle','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(60,12,7,600.0000,2,0.0150,2.00,0,'Required for Salted Caramel Chocolate','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(61,12,1,200.0000,2,0.0010,3.00,1,'Required for Salted Caramel Chocolate','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(62,12,9,200.0000,6,0.0100,1.00,2,'Required for Salted Caramel Chocolate','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(63,12,4,80.0000,2,0.0080,1.00,3,'Required for Salted Caramel Chocolate','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(64,12,12,5.0000,2,0.0010,0.00,4,'Required for Salted Caramel Chocolate','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(65,13,8,4.0000,7,3.0000,2.00,0,'Required for Vanilla Gelato Base','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(66,13,9,1.0000,7,10.0000,2.00,1,'Required for Vanilla Gelato Base','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(67,13,1,500.0000,2,0.0010,3.00,2,'Required for Vanilla Gelato Base','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(68,13,6,20.0000,6,0.0500,0.00,3,'Required for Vanilla Gelato Base','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(69,14,1,400.0000,2,0.0010,3.00,0,'Required for Fruit Gummies','Standard preparation','2026-01-08 19:01:34','2026-01-08 19:01:34');
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
(1,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-4095-71c4-945b-fce6e608db8f','Butter Croissant','PT-BUT-001-RCP',1,0.4923,13,24.00,240,'[\"Mix flour, sugar, salt, and yeast in a large bowl\",\"Add cold butter pieces and work into the dough\",\"Knead until smooth and elastic\",\"Rest dough in refrigerator for 2 hours\",\"Roll out and fold dough multiple times (lamination)\",\"Cut into triangles and roll into croissant shape\",\"Proof for 1-2 hours until doubled\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes until golden\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:31','2026-01-08 19:01:32'),
(2,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-414c-72f2-ab3c-cb157afccc89','Chocolate Chip Cookie','CO-CHI-001-RCP',1,0.4215,13,36.00,45,'[\"Cream together butter and sugars\",\"Beat in eggs and vanilla extract\",\"Mix in flour, baking soda, and salt\",\"Fold in chocolate chips\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 10-12 minutes\",\"Cool on baking sheet for 5 minutes before transferring\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(3,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-413e-729d-a4b3-52fd22463621','Chocolate Cake Slice','CK-CHO-001-RCP',1,1.5901,13,12.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mix dry ingredients: flour, cocoa powder, baking soda, salt\",\"Beat together eggs, sugar, oil, and vanilla\",\"Add hot water and mix until smooth\",\"Pour into greased cake pans\",\"Bake for 30-35 minutes\",\"Cool completely before frosting\",\"Prepare chocolate ganache and frost the cake\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:32','2026-01-08 19:01:32'),
(4,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-41e5-728d-8bc9-e9e9d9788c8e','Chocolate Gelato','GF-CHO-001-RCP',1,0.8428,2,50.00,60,'[\"Heat milk and cream to 85\\u00b0C\",\"Whisk cocoa powder with some warm milk\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add cocoa mixture and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:32','2026-01-08 19:01:33'),
(5,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-4231-73bb-96e4-f667f957e148','Strawberry Gelato','GF-STR-002-RCP',1,0.6063,2,50.00,60,'[\"Blend fresh strawberries to puree\",\"Heat milk, cream, and sugar to 85\\u00b0C\",\"Mix with egg yolks\",\"Cool completely\",\"Add strawberry puree\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(6,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-42a0-7200-91a5-4a3e4b72fc32','Pistachio Gelato','GF-PIS-003-RCP',1,0.6573,2,50.00,70,'[\"Grind pistachios into fine paste\",\"Heat milk and cream to 85\\u00b0C\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add pistachio paste and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(7,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-4100-70ae-b9a2-1b366d55acc6','Almond Danish','PT-ALM-002-RCP',1,0.7170,13,18.00,180,'[\"Prepare puff pastry dough\",\"Roll and fold dough multiple times\",\"Cut into squares\",\"Add almond cream filling\",\"Top with sliced almonds\",\"Proof for 1 hour\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(8,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-415b-7212-a0f8-c2150414e426','Oatmeal Raisin Cookie','CO-OAT-002-RCP',1,0.2545,13,40.00,40,'[\"Cream butter and sugars together\",\"Beat in eggs and vanilla\",\"Mix in flour, oats, and spices\",\"Fold in raisins\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 12-14 minutes\",\"Cool before serving\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(9,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-411d-720a-940c-eae17b48e3b8','Sourdough Loaf','BR-SOU-001-RCP',8,0.5800,13,4.00,1440,'[\"Feed sourdough starter 12 hours before\",\"Mix flour, water, salt, and starter\",\"Autolyse for 30 minutes\",\"Stretch and fold every 30 minutes (4 times)\",\"Bulk ferment for 4-6 hours\",\"Shape into loaves\",\"Cold ferment overnight (12 hours)\",\"Score and bake at 230\\u00b0C for 35-40 minutes\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(10,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-412e-720c-a191-17b14717a68e','Banana Bread','BR-BAN-002-RCP',8,4.9636,13,2.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mash ripe bananas\",\"Mix butter, sugar, and eggs\",\"Add mashed bananas\",\"Mix in flour, baking soda, and salt\",\"Pour into greased loaf pans\",\"Bake for 55-60 minutes\",\"Cool before slicing\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:33','2026-01-08 19:01:33'),
(11,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-42ba-71cc-b884-f553e035afc1','Dark Chocolate Truffle','CH-DAR-001-RCP',8,0.2777,13,50.00,120,'[\"Chop dark chocolate finely\",\"Heat cream to simmering\",\"Pour over chocolate and let sit\",\"Stir until smooth ganache forms\",\"Cool and refrigerate for 2 hours\",\"Roll into balls\",\"Coat with cocoa powder\",\"Store in refrigerator\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:33','2026-01-08 19:01:34'),
(12,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-42cf-720e-bbc0-2a19e15fbd1f','Salted Caramel Chocolate','CH-SAL-002-RCP',8,0.2512,13,48.00,150,'[\"Make caramel with sugar and cream\",\"Add salt to caramel and cool\",\"Temper milk chocolate\",\"Fill molds halfway with chocolate\",\"Add caramel filling\",\"Top with more chocolate\",\"Cool and unmold\",\"Store in cool place\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(13,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-41c9-72e2-9236-af03dff26491','Vanilla Gelato Base','GB-VAN-001-RCP',1,2.9944,3,8.00,45,'[\"Heat milk and cream to 85\\u00b0C\",\"Whisk in sugar until dissolved\",\"Add vanilla extract and mix well\",\"Cool to 4\\u00b0C\",\"Age in refrigerator for 4-12 hours\",\"Store at -18\\u00b0C until use\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:34','2026-01-08 19:01:34'),
(14,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'019b9efc-42da-705b-af43-c4f26f2d269d','Fruit Gummies','CD-FRU-001-RCP',1,0.0041,2,100.00,180,'[\"Heat fruit puree and sugar to 80\\u00b0C\",\"Dissolve gelatin in hot mixture\",\"Add citric acid and mix well\",\"Strain through fine mesh\",\"Pour into molds\",\"Cool at room temperature for 30 minutes\",\"Refrigerate for 2 hours until set\",\"Unmold and store in cool place\"]','active','0084f5fc-dada-3f07-98de-fa253e3686e9','App\\Models\\Employee','2026-01-08 19:01:34','2026-01-08 19:01:34');
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
(113,33),
(114,33),
(115,33),
(116,33),
(117,33),
(118,33),
(201,33),
(253,33),
(254,33),
(255,33),
(256,33),
(257,33),
(258,33),
(259,33),
(260,33),
(261,33),
(262,33),
(263,33),
(264,33),
(265,33),
(266,33),
(267,33),
(268,33),
(269,33),
(270,33),
(271,33),
(272,33),
(273,33),
(274,33),
(275,33),
(276,33),
(277,33),
(278,33),
(279,33),
(280,33),
(281,33),
(282,33),
(283,33),
(284,33),
(285,33),
(286,33),
(113,34),
(114,34),
(115,34),
(116,34),
(117,34),
(118,34),
(201,34),
(253,34),
(254,34),
(255,34),
(256,34),
(257,34),
(258,34),
(259,34),
(260,34),
(261,34),
(262,34),
(263,34),
(264,34),
(265,34),
(266,34),
(267,34),
(268,34),
(269,34),
(270,34),
(271,34),
(272,34),
(273,34),
(274,34),
(275,34),
(276,34),
(277,34),
(278,34),
(279,34),
(280,34),
(281,34),
(282,34),
(283,34),
(284,34),
(285,34),
(286,34),
(109,35),
(112,35),
(183,35),
(184,35),
(185,35),
(186,35),
(187,35),
(188,35),
(189,35),
(190,35),
(191,35),
(192,35),
(193,35),
(194,35),
(195,35),
(196,35),
(197,35),
(198,35),
(199,35),
(200,35),
(201,35),
(202,35),
(203,35),
(204,35),
(205,35),
(206,35),
(207,35),
(208,35),
(209,35),
(210,35),
(211,35),
(212,35),
(213,35),
(214,35),
(215,35),
(216,35),
(217,35),
(218,35),
(219,35),
(220,35),
(221,35),
(222,35),
(223,35),
(224,35),
(225,35),
(226,35),
(227,35),
(228,35),
(229,35),
(230,35),
(231,35),
(232,35),
(233,35),
(234,35),
(235,35),
(236,35),
(237,35),
(238,35),
(239,35),
(240,35),
(241,35),
(242,35),
(243,35),
(244,35),
(245,35),
(246,35),
(247,35),
(248,35),
(249,35),
(250,35),
(251,35),
(252,35),
(113,36),
(114,36),
(115,36),
(116,36),
(117,36),
(118,36),
(201,36),
(271,36),
(272,36),
(273,36),
(274,36),
(275,36),
(279,36),
(280,36),
(32,37),
(33,37),
(34,37),
(35,37),
(36,37),
(39,37),
(42,37),
(43,37),
(44,37),
(45,37),
(102,37),
(112,37),
(147,37),
(208,37),
(209,37),
(213,37),
(218,37),
(228,37),
(229,37),
(230,37),
(231,37),
(232,37),
(233,37),
(234,37),
(235,37),
(236,37),
(237,37),
(240,37),
(65,38),
(68,38),
(70,38),
(71,38),
(72,38),
(73,38),
(74,38),
(76,38),
(82,38),
(103,38),
(104,38),
(112,38),
(148,38),
(153,38),
(154,38),
(156,38),
(187,38),
(192,38),
(213,38),
(218,38),
(238,38),
(239,38),
(240,38),
(241,38),
(242,38),
(243,38),
(245,38),
(246,38),
(259,38),
(263,38),
(264,38),
(284,38),
(29,39),
(39,39),
(49,39),
(52,39),
(64,39),
(70,39),
(84,39),
(89,39),
(92,39),
(93,39),
(94,39),
(95,39),
(97,39),
(101,39),
(105,39),
(112,39),
(113,39),
(114,39),
(147,39),
(156,39),
(161,39),
(169,39),
(170,39),
(173,39),
(182,39),
(183,39),
(184,39),
(185,39),
(186,39),
(187,39),
(188,39),
(189,39),
(190,39),
(191,39),
(192,39),
(193,39),
(194,39),
(195,39),
(196,39),
(197,39),
(198,39),
(199,39),
(200,39),
(204,39),
(205,39),
(207,39),
(210,39),
(211,39),
(212,39),
(213,39),
(214,39),
(215,39),
(216,39),
(217,39),
(221,39),
(240,39),
(244,39),
(266,39),
(267,39),
(268,39),
(269,39),
(270,39),
(276,39),
(279,39),
(281,39),
(283,39),
(287,39),
(112,40),
(210,40),
(213,40),
(214,40),
(215,40),
(216,40),
(217,40),
(218,40),
(219,40),
(220,40),
(245,40),
(246,40),
(112,41),
(228,41),
(239,41),
(65,42),
(68,42),
(70,42),
(71,42),
(72,42),
(73,42),
(74,42),
(76,42),
(82,42),
(103,42),
(104,42),
(148,42),
(153,42),
(154,42),
(156,42),
(238,42),
(239,42),
(240,42),
(242,42),
(259,42),
(263,42),
(264,42),
(284,42),
(32,43),
(33,43),
(34,43),
(35,43),
(36,43),
(39,43),
(42,43),
(43,43),
(44,43),
(45,43),
(102,43),
(147,43),
(208,43),
(213,43),
(218,43),
(228,43),
(229,43),
(230,43),
(231,43),
(234,43),
(32,44),
(33,44),
(34,44),
(35,44),
(36,44),
(39,44),
(42,44),
(43,44),
(44,44),
(45,44),
(102,44),
(147,44),
(208,44),
(213,44),
(218,44),
(228,44),
(229,44),
(230,44),
(231,44),
(234,44),
(32,45),
(33,45),
(34,45),
(35,45),
(36,45),
(39,45),
(42,45),
(43,45),
(44,45),
(45,45),
(102,45),
(112,45),
(147,45),
(187,45),
(192,45),
(208,45),
(213,45),
(217,45),
(218,45),
(228,45),
(229,45),
(230,45),
(231,45),
(238,45),
(239,45),
(240,45),
(32,46),
(33,46),
(34,46),
(35,46),
(36,46),
(39,46),
(42,46),
(43,46),
(44,46),
(45,46),
(102,46),
(147,46),
(208,46),
(228,46),
(229,46),
(230,46),
(231,46),
(234,46),
(32,47),
(33,47),
(34,47),
(35,47),
(36,47),
(39,47),
(42,47),
(43,47),
(44,47),
(45,47),
(102,47),
(147,47),
(208,47),
(228,47),
(229,47),
(230,47),
(231,47),
(234,47),
(32,48),
(33,48),
(34,48),
(35,48),
(36,48),
(39,48),
(42,48),
(43,48),
(44,48),
(45,48),
(102,48),
(147,48),
(208,48),
(228,48),
(229,48),
(230,48),
(231,48),
(234,48),
(65,49),
(68,49),
(70,49),
(71,49),
(72,49),
(73,49),
(74,49),
(76,49),
(82,49),
(103,49),
(104,49),
(148,49),
(153,49),
(154,49),
(156,49),
(238,49),
(239,49),
(240,49),
(259,49),
(263,49),
(264,49),
(284,49),
(65,50),
(68,50),
(70,50),
(71,50),
(72,50),
(73,50),
(74,50),
(76,50),
(82,50),
(103,50),
(104,50),
(112,50),
(148,50),
(153,50),
(154,50),
(156,50),
(187,50),
(192,50),
(213,50),
(217,50),
(218,50),
(238,50),
(239,50),
(240,50),
(259,50),
(263,50),
(264,50),
(284,50),
(65,51),
(68,51),
(70,51),
(71,51),
(72,51),
(73,51),
(74,51),
(76,51),
(82,51),
(103,51),
(104,51),
(148,51),
(153,51),
(154,51),
(156,51),
(213,51),
(218,51),
(238,51),
(239,51),
(240,51),
(259,51),
(263,51),
(264,51),
(284,51),
(65,52),
(68,52),
(70,52),
(71,52),
(72,52),
(73,52),
(74,52),
(76,52),
(82,52),
(103,52),
(104,52),
(148,52),
(153,52),
(154,52),
(156,52),
(238,52),
(239,52),
(240,52),
(241,52),
(242,52),
(243,52),
(259,52),
(263,52),
(264,52),
(284,52),
(65,53),
(68,53),
(70,53),
(71,53),
(72,53),
(73,53),
(74,53),
(76,53),
(82,53),
(103,53),
(104,53),
(148,53),
(153,53),
(154,53),
(156,53),
(238,53),
(239,53),
(240,53),
(259,53),
(263,53),
(264,53),
(284,53),
(65,54),
(68,54),
(70,54),
(71,54),
(72,54),
(73,54),
(74,54),
(76,54),
(82,54),
(103,54),
(104,54),
(148,54),
(153,54),
(154,54),
(156,54),
(238,54),
(239,54),
(240,54),
(259,54),
(263,54),
(264,54),
(284,54),
(112,55),
(210,55),
(213,55),
(217,55),
(218,55),
(219,55),
(220,55),
(287,55),
(213,56),
(218,56),
(219,56),
(220,56),
(187,57),
(188,57),
(193,57),
(199,57),
(211,57),
(212,57),
(199,58),
(112,59),
(245,59),
(29,60),
(39,60),
(49,60),
(52,60),
(64,60),
(70,60),
(84,60),
(89,60),
(92,60),
(93,60),
(94,60),
(95,60),
(97,60),
(101,60),
(105,60),
(112,60),
(113,60),
(114,60),
(147,60),
(156,60),
(161,60),
(169,60),
(170,60),
(173,60),
(182,60),
(200,60),
(204,60),
(210,60),
(213,60),
(214,60),
(215,60),
(216,60),
(217,60),
(218,60),
(219,60),
(220,60),
(221,60),
(240,60),
(244,60),
(266,60),
(267,60),
(268,60),
(269,60),
(270,60),
(276,60),
(279,60),
(281,60),
(283,60),
(287,60),
(29,61),
(39,61),
(49,61),
(52,61),
(64,61),
(70,61),
(84,61),
(89,61),
(92,61),
(93,61),
(94,61),
(95,61),
(97,61),
(101,61),
(105,61),
(112,61),
(113,61),
(114,61),
(147,61),
(156,61),
(161,61),
(169,61),
(170,61),
(173,61),
(182,61),
(187,61),
(192,61),
(200,61),
(204,61),
(210,61),
(213,61),
(214,61),
(215,61),
(216,61),
(217,61),
(218,61),
(221,61),
(238,61),
(239,61),
(240,61),
(244,61),
(245,61),
(266,61),
(267,61),
(268,61),
(269,61),
(270,61),
(276,61),
(279,61),
(281,61),
(283,61),
(287,61),
(213,62),
(218,62),
(238,62),
(239,62),
(29,63),
(39,63),
(49,63),
(52,63),
(64,63),
(70,63),
(84,63),
(89,63),
(92,63),
(93,63),
(94,63),
(95,63),
(97,63),
(101,63),
(105,63),
(112,63),
(113,63),
(114,63),
(147,63),
(156,63),
(161,63),
(169,63),
(170,63),
(173,63),
(182,63),
(200,63),
(204,63),
(210,63),
(213,63),
(214,63),
(215,63),
(216,63),
(217,63),
(218,63),
(221,63),
(240,63),
(244,63),
(266,63),
(267,63),
(268,63),
(269,63),
(270,63),
(276,63),
(279,63),
(281,63),
(283,63),
(287,63),
(112,64),
(213,64),
(228,64),
(239,64),
(245,64),
(228,65),
(234,65),
(29,66),
(39,66),
(49,66),
(52,66),
(64,66),
(70,66),
(84,66),
(89,66),
(92,66),
(93,66),
(94,66),
(95,66),
(97,66),
(101,66),
(105,66),
(112,66),
(113,66),
(114,66),
(147,66),
(156,66),
(161,66),
(169,66),
(170,66),
(173,66),
(182,66),
(200,66),
(201,66),
(202,66),
(203,66),
(204,66),
(205,66),
(206,66),
(207,66),
(210,66),
(213,66),
(214,66),
(215,66),
(216,66),
(217,66),
(221,66),
(222,66),
(223,66),
(224,66),
(225,66),
(227,66),
(240,66),
(244,66),
(245,66),
(246,66),
(247,66),
(266,66),
(267,66),
(268,66),
(269,66),
(270,66),
(276,66),
(279,66),
(281,66),
(283,66),
(287,66),
(208,67),
(209,67),
(210,67);
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
) ENGINE=InnoDB AUTO_INCREMENT=354 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission_audit_logs`
--

LOCK TABLES `role_permission_audit_logs` WRITE;
/*!40000 ALTER TABLE `role_permission_audit_logs` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `role_permission_audit_logs` VALUES
(1,'role_created','Role',1,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access, all branches, all departments\"}',0,'2026-01-08 19:00:25'),
(2,'role_created','Role',2,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Branch-wide access, manages all departments in branch\"}',0,'2026-01-08 19:00:25'),
(3,'role_created','Role',3,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Department manager, full control of their department\"}',0,'2026-01-08 19:00:25'),
(4,'role_created','Role',4,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Department supervisor, limited management\"}',0,'2026-01-08 19:00:25'),
(5,'role_created','Role',5,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Department worker, basic operational access\"}',0,'2026-01-08 19:00:25'),
(6,'permission_created','Permission',1,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:26'),
(7,'permission_created','Permission',2,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(8,'permission_created','Permission',3,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(9,'permission_created','Permission',4,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(10,'permission_created','Permission',5,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"assign-roles\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(11,'permission_created','Permission',6,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(12,'permission_created','Permission',7,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-permissions\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(13,'permission_created','Permission',8,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(14,'permission_created','Permission',9,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(15,'permission_created','Permission',10,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:27'),
(16,'permission_created','Permission',11,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-branches\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(17,'permission_created','Permission',12,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-settings\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(18,'permission_created','Permission',13,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-audit-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(19,'permission_created','Permission',14,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-logs\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(20,'permission_created','Permission',15,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-system\",\"guard_name\":\"web\",\"category\":\"system\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(21,'permission_created','Permission',16,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(22,'permission_created','Permission',17,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(23,'permission_created','Permission',18,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(24,'permission_created','Permission',19,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-employees\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:28'),
(25,'permission_created','Permission',20,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(26,'permission_created','Permission',21,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(27,'permission_created','Permission',22,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(28,'permission_created','Permission',23,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-departments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(29,'permission_created','Permission',24,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-staff-schedule\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(30,'permission_created','Permission',25,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(31,'permission_created','Permission',26,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-leave\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(32,'permission_created','Permission',27,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(33,'permission_created','Permission',28,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payroll\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:29'),
(34,'permission_created','Permission',29,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-hr-reports\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(35,'permission_created','Permission',30,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-roles-assignments\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(36,'permission_created','Permission',31,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-employee-details\",\"guard_name\":\"web\",\"category\":\"hr\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(37,'permission_created','Permission',32,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-queue\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(38,'permission_created','Permission',33,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(39,'permission_created','Permission',34,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"start-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(40,'permission_created','Permission',35,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"complete-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(41,'permission_created','Permission',36,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:30'),
(42,'permission_created','Permission',37,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(43,'permission_created','Permission',38,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-recipes\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(44,'permission_created','Permission',39,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-reports\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(45,'permission_created','Permission',40,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-quality-control\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(46,'permission_created','Permission',41,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-batch-history\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(47,'permission_created','Permission',42,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-production-order\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(48,'permission_created','Permission',43,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"cancel-production\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(49,'permission_created','Permission',44,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-production-cost\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(50,'permission_created','Permission',45,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-production-settings\",\"guard_name\":\"web\",\"category\":\"production\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(51,'permission_created','Permission',46,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(52,'permission_created','Permission',47,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"receive-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:31'),
(53,'permission_created','Permission',48,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"transfer-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(54,'permission_created','Permission',49,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"adjust-inventory\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(55,'permission_created','Permission',50,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(56,'permission_created','Permission',51,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve-purchase-order\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(57,'permission_created','Permission',52,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-inventory-reports\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(58,'permission_created','Permission',53,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(59,'permission_created','Permission',54,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(60,'permission_created','Permission',55,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(61,'permission_created','Permission',56,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(62,'permission_created','Permission',57,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete-suppliers\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:32'),
(63,'permission_created','Permission',58,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-valuation\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(64,'permission_created','Permission',59,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-stock-categories\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(65,'permission_created','Permission',60,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(66,'permission_created','Permission',61,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-reorder-levels\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(67,'permission_created','Permission',62,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"write-off-stock\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(68,'permission_created','Permission',63,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-stock-history\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(69,'permission_created','Permission',64,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-inventory-settings\",\"guard_name\":\"web\",\"category\":\"inventory\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(70,'permission_created','Permission',65,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-dashboard\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:33'),
(71,'permission_created','Permission',66,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process-sale\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:34'),
(72,'permission_created','Permission',67,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"issue-refund\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:34'),
(73,'permission_created','Permission',68,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-daily-sales\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:34'),
(74,'permission_created','Permission',69,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"close-register\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:34'),
(75,'permission_created','Permission',70,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-reports\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:34'),
(76,'permission_created','Permission',71,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-sales-discounts\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:34'),
(77,'permission_created','Permission',72,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:34'),
(78,'permission_created','Permission',73,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(79,'permission_created','Permission',74,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"void-sales-transactions\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(80,'permission_created','Permission',75,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-payment-methods\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(81,'permission_created','Permission',76,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-till-records\",\"guard_name\":\"web\",\"category\":\"sales\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(82,'permission_created','Permission',77,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-chart-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(83,'permission_created','Permission',78,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(84,'permission_created','Permission',79,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(85,'permission_created','Permission',80,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:35'),
(86,'permission_created','Permission',81,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:36'),
(87,'permission_created','Permission',82,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"post-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:36'),
(88,'permission_created','Permission',83,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reverse-gl-entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:36'),
(89,'permission_created','Permission',84,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-accounting-reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:36'),
(90,'permission_created','Permission',85,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:36'),
(91,'permission_created','Permission',86,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-bank-accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:36'),
(92,'permission_created','Permission',87,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-trial-balance\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:37'),
(93,'permission_created','Permission',88,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-financial-statements\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:37'),
(94,'permission_created','Permission',89,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage-accounting-period\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:37'),
(95,'permission_created','Permission',90,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-account-reconciliation\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:37'),
(96,'permission_created','Permission',91,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-analytics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:37'),
(97,'permission_created','Permission',92,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-department-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:37'),
(98,'permission_created','Permission',93,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"generate-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:38'),
(99,'permission_created','Permission',94,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:38'),
(100,'permission_created','Permission',95,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"schedule-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:38'),
(101,'permission_created','Permission',96,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-dashboard\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:38'),
(102,'permission_created','Permission',97,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-branch-reports\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:38'),
(103,'permission_created','Permission',98,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-kpi-metrics\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:38'),
(104,'permission_created','Permission',99,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export-data\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:38'),
(105,'permission_created','Permission',100,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view-activity-timeline\",\"guard_name\":\"web\",\"category\":\"reporting\",\"is_protected\":false}',0,'2026-01-08 19:00:39'),
(106,'permission_created','Permission',101,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_inventory_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-01-08 19:00:39'),
(107,'permission_created','Permission',102,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_production_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-01-08 19:00:39'),
(108,'permission_created','Permission',103,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_sales_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-01-08 19:00:39'),
(109,'permission_created','Permission',104,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_corner_store_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-01-08 19:00:39'),
(110,'permission_created','Permission',105,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_hr_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-01-08 19:00:40'),
(111,'permission_created','Permission',106,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-01-08 19:00:40'),
(112,'permission_created','Permission',107,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_super_admin_dashboard\",\"guard_name\":\"web\",\"category\":\"dashboard\",\"is_protected\":false}',0,'2026-01-08 19:00:40'),
(113,'permission_created','Permission',108,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_organization\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-01-08 19:00:40'),
(114,'permission_created','Permission',109,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_roles\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-01-08 19:00:40'),
(115,'permission_created','Permission',110,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_branches\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-01-08 19:00:40'),
(116,'permission_created','Permission',111,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_settings\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(117,'permission_created','Permission',112,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_reports\",\"guard_name\":\"web\",\"category\":\"organization\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(118,'permission_created','Permission',113,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"access_accounting\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(119,'permission_created','Permission',114,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_financial_reports\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(120,'permission_created','Permission',115,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(121,'permission_created','Permission',116,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_periods\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(122,'permission_created','Permission',117,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_journal_entries\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(123,'permission_created','Permission',118,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reconcile_bank_accounts\",\"guard_name\":\"web\",\"category\":\"accounting\",\"is_protected\":false}',0,'2026-01-08 19:00:41'),
(124,'role_created','Role',6,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access with all permissions\"}',0,'2026-01-08 19:00:41'),
(125,'role_created','Role',7,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"MD\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director - Executive level\"}',0,'2026-01-08 19:00:41'),
(126,'role_created','Role',8,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Managing Director\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director with full operational control\"}',0,'2026-01-08 19:00:41'),
(127,'role_created','Role',9,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Administrative access\"}',0,'2026-01-08 19:00:41'),
(128,'role_created','Role',10,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Production\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Head of Production department\"}',0,'2026-01-08 19:00:42'),
(129,'role_created','Role',11,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Manager\"}',0,'2026-01-08 19:00:42'),
(130,'role_created','Role',12,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Human Resources Manager\"}',0,'2026-01-08 19:00:42'),
(131,'role_created','Role',13,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory and Stock Management\"}',0,'2026-01-08 19:00:43'),
(132,'role_created','Role',14,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Team Supervisor\"}',0,'2026-01-08 19:00:43'),
(133,'role_created','Role',15,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Till Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Till\\/Register Supervisor\"}',0,'2026-01-08 19:00:44'),
(134,'role_created','Role',16,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Chef\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Production Chef\"}',0,'2026-01-08 19:00:44'),
(135,'role_created','Role',17,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Gelato\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Lead\"}',0,'2026-01-08 19:00:44'),
(136,'role_created','Role',18,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Manager\"}',0,'2026-01-08 19:00:45'),
(137,'role_created','Role',19,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Kitchen Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Kitchen\\/Production Staff\"}',0,'2026-01-08 19:00:45'),
(138,'role_created','Role',20,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Gelato Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Staff\"}',0,'2026-01-08 19:00:45'),
(139,'role_created','Role',21,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Staff\"}',0,'2026-01-08 19:00:46'),
(140,'role_created','Role',22,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Cashier\"}',0,'2026-01-08 19:00:46'),
(141,'role_created','Role',23,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Manager\"}',0,'2026-01-08 19:00:46'),
(142,'role_created','Role',24,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Staff\"}',0,'2026-01-08 19:00:47'),
(143,'role_created','Role',25,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Department Supervisor\"}',0,'2026-01-08 19:00:47'),
(144,'role_created','Role',26,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Associate\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Associate\"}',0,'2026-01-08 19:00:47'),
(145,'role_created','Role',27,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Junior Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Junior Cashier\"}',0,'2026-01-08 19:00:48'),
(146,'role_created','Role',28,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Stock Controller\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Stock\\/Inventory Controller\"}',0,'2026-01-08 19:00:48'),
(147,'role_created','Role',29,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Keeper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Keeper\\/Warehouse Staff\"}',0,'2026-01-08 19:00:49'),
(148,'role_created','Role',30,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Officer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"HR Officer\"}',0,'2026-01-08 19:00:49'),
(149,'role_created','Role',31,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Employee\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Standard Employee\"}',0,'2026-01-08 19:00:50'),
(150,'role_created','Role',32,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Viewer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Read-only access to reports and dashboards\"}',0,'2026-01-08 19:00:50'),
(151,'role_created','Role',33,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Super Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Full system access with all permissions\"}',0,'2026-01-08 19:00:54'),
(152,'role_created','Role',34,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"MD\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director - Executive level\"}',0,'2026-01-08 19:00:54'),
(153,'role_created','Role',35,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Managing Director\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Managing Director with full operational control\"}',0,'2026-01-08 19:00:54'),
(154,'role_created','Role',36,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Admin\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Administrative access\"}',0,'2026-01-08 19:00:54'),
(155,'role_created','Role',37,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Production\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Head of Production department\"}',0,'2026-01-08 19:00:55'),
(156,'role_created','Role',38,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Manager\"}',0,'2026-01-08 19:00:55'),
(157,'role_created','Role',39,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Human Resources Manager\"}',0,'2026-01-08 19:00:55'),
(158,'role_created','Role',40,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory and Stock Management\"}',0,'2026-01-08 19:00:56'),
(159,'role_created','Role',41,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Team Supervisor\"}',0,'2026-01-08 19:00:56'),
(160,'role_created','Role',42,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Till Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Till\\/Register Supervisor\"}',0,'2026-01-08 19:00:56'),
(161,'role_created','Role',43,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Chef\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Production Chef\"}',0,'2026-01-08 19:00:57'),
(162,'role_created','Role',44,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Head of Gelato\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Lead\"}',0,'2026-01-08 19:00:58'),
(163,'role_created','Role',45,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Manager\"}',0,'2026-01-08 19:00:58'),
(164,'role_created','Role',46,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Kitchen Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Kitchen\\/Production Staff\"}',0,'2026-01-08 19:00:59'),
(165,'role_created','Role',47,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Gelato Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Gelato Production Staff\"}',0,'2026-01-08 19:00:59'),
(166,'role_created','Role',48,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Confectioneries Production Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Confectioneries Production Staff\"}',0,'2026-01-08 19:00:59'),
(167,'role_created','Role',49,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Cashier\"}',0,'2026-01-08 19:01:00'),
(168,'role_created','Role',50,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Manager\"}',0,'2026-01-08 19:01:00'),
(169,'role_created','Role',51,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Corner Store Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Corner Store Staff\"}',0,'2026-01-08 19:01:01'),
(170,'role_created','Role',52,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Department Supervisor\"}',0,'2026-01-08 19:01:01'),
(171,'role_created','Role',53,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Sales Associate\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Sales Associate\"}',0,'2026-01-08 19:01:01'),
(172,'role_created','Role',54,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Junior Cashier\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Junior Cashier\"}',0,'2026-01-08 19:01:02'),
(173,'role_created','Role',55,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Stock Controller\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Stock\\/Inventory Controller\"}',0,'2026-01-08 19:01:02'),
(174,'role_created','Role',56,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Keeper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Keeper\\/Warehouse Staff\"}',0,'2026-01-08 19:01:03'),
(175,'role_created','Role',57,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"HR Officer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"HR Officer\"}',0,'2026-01-08 19:01:03'),
(176,'role_created','Role',58,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Employee\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Standard Employee\"}',0,'2026-01-08 19:01:04'),
(177,'role_created','Role',59,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Viewer\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Read-only access to reports and dashboards\"}',0,'2026-01-08 19:01:04'),
(178,'role_created','Role',60,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Warehouse Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Warehouse Manager with supervisory responsibilities\"}',0,'2026-01-08 19:01:35'),
(179,'role_created','Role',61,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Manager overseeing retail operations\"}',0,'2026-01-08 19:01:36'),
(180,'role_created','Role',62,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Store Supervisor\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Store Supervisor assisting with daily operations\"}',0,'2026-01-08 19:01:36'),
(181,'role_created','Role',63,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Inventory Clerk\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Inventory Clerk for data entry and basic inventory tasks\"}',0,'2026-01-08 19:01:37'),
(182,'role_created','Role',64,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":null}',0,'2026-01-08 19:01:46'),
(183,'role_created','Role',65,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Staff\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":null}',0,'2026-01-08 19:01:46'),
(184,'permission_created','Permission',119,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.users\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:46'),
(185,'permission_created','Permission',120,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.roles\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:46'),
(186,'permission_created','Permission',121,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.permissions\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:46'),
(187,'permission_created','Permission',122,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.branches\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(188,'permission_created','Permission',123,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.departments\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(189,'permission_created','Permission',124,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.categories\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(190,'permission_created','Permission',125,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.audit_trail\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(191,'permission_created','Permission',126,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.settings\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(192,'permission_created','Permission',127,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.system_config\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(193,'permission_created','Permission',128,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.daily_produce\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(194,'permission_created','Permission',129,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"record.daily_produce\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(195,'permission_created','Permission',130,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit.daily_produce\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(196,'permission_created','Permission',131,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete.daily_produce\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:47'),
(197,'permission_created','Permission',132,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.recipes\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:48'),
(198,'permission_created','Permission',133,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit.recipes\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:48'),
(199,'permission_created','Permission',134,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete.recipes\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:48'),
(200,'permission_created','Permission',135,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.recipes\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:48'),
(201,'permission_created','Permission',136,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.products\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:48'),
(202,'permission_created','Permission',137,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit.products\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:48'),
(203,'permission_created','Permission',138,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete.products\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:48'),
(204,'permission_created','Permission',139,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.requests\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:49'),
(205,'permission_created','Permission',140,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create.requests\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:49'),
(206,'permission_created','Permission',141,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit.requests\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:49'),
(207,'permission_created','Permission',142,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve.requests\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:49'),
(208,'permission_created','Permission',143,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reject.requests\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:49'),
(209,'permission_created','Permission',144,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.stock_takes\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:49'),
(210,'permission_created','Permission',145,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"conduct.stock_take\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:49'),
(211,'permission_created','Permission',146,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"verify.stock_take\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(212,'permission_created','Permission',147,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"report.production\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(213,'permission_created','Permission',148,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.pos\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(214,'permission_created','Permission',149,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process.sale\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(215,'permission_created','Permission',150,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit.sale\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(216,'permission_created','Permission',151,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"void.sale\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(217,'permission_created','Permission',152,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"refund.sale\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(218,'permission_created','Permission',153,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.my_sales\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(219,'permission_created','Permission',154,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.sales_analytics\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(220,'permission_created','Permission',155,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.price_override\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(221,'permission_created','Permission',156,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"report.sales\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:50'),
(222,'permission_created','Permission',157,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.stock_levels\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(223,'permission_created','Permission',158,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"record.stock_movement\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(224,'permission_created','Permission',159,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"adjust.stock\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(225,'permission_created','Permission',160,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve.stock_movement\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(226,'permission_created','Permission',161,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"report.inventory\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(227,'permission_created','Permission',162,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.stock_items\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(228,'permission_created','Permission',163,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.employees\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(229,'permission_created','Permission',164,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.employees\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(230,'permission_created','Permission',165,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit.employee_profile\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(231,'permission_created','Permission',166,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.leave\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(232,'permission_created','Permission',167,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve.leave\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:51'),
(233,'permission_created','Permission',168,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.attendance\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(234,'permission_created','Permission',169,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"report.hr\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(235,'permission_created','Permission',170,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.financial_reports\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(236,'permission_created','Permission',171,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(237,'permission_created','Permission',172,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve.transactions\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(238,'permission_created','Permission',173,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"report.accounting\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(239,'permission_created','Permission',174,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.callbacks\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(240,'permission_created','Permission',175,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"record.callback\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:52'),
(241,'permission_created','Permission',176,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.callbacks\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:53'),
(242,'permission_created','Permission',177,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.dispatches\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:53'),
(243,'permission_created','Permission',178,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage.dispatches\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:53'),
(244,'permission_created','Permission',179,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.users\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:53'),
(245,'permission_created','Permission',180,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit.users\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:53'),
(246,'permission_created','Permission',181,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.dashboard\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:55'),
(247,'permission_created','Permission',182,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view.reports\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-08 19:01:57'),
(248,'role_created','Role',66,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Accounting Manager\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Manages financial records and accounting\"}',0,'2026-01-09 02:56:02'),
(249,'role_created','Role',67,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"Production Helper\",\"guard_name\":\"web\",\"is_protected\":null,\"description\":\"Views production callbacks and inventory callbacks (filtered by department)\"}',0,'2026-01-09 02:56:02'),
(250,'permission_created','Permission',183,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_organization\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(251,'permission_created','Permission',184,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_organization\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(252,'permission_created','Permission',185,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_organization\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(253,'permission_created','Permission',186,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete_organization\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(254,'permission_created','Permission',187,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_employees\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(255,'permission_created','Permission',188,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_employee\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(256,'permission_created','Permission',189,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_employee\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(257,'permission_created','Permission',190,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete_employee\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(258,'permission_created','Permission',191,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_employee_roles\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(259,'permission_created','Permission',192,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_employee_shifts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(260,'permission_created','Permission',193,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_leave_applications\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(261,'permission_created','Permission',194,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve_leave\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:03'),
(262,'permission_created','Permission',195,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reject_leave\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(263,'permission_created','Permission',196,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_leave_type\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(264,'permission_created','Permission',197,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_leave_type\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(265,'permission_created','Permission',198,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete_leave_type\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(266,'permission_created','Permission',199,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_leave_balance\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(267,'permission_created','Permission',200,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_all_accounting\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(268,'permission_created','Permission',201,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_gl_accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(269,'permission_created','Permission',202,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_gl_entry\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(270,'permission_created','Permission',203,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_transactions\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(271,'permission_created','Permission',204,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export_accounting_data\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:04'),
(272,'permission_created','Permission',205,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_payroll\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:05'),
(273,'permission_created','Permission',206,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process_payroll\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:05'),
(274,'permission_created','Permission',207,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_salary_history\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:05'),
(275,'permission_created','Permission',208,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_production_callbacks\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:05'),
(276,'permission_created','Permission',209,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_dispatch_callbacks\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:05'),
(277,'permission_created','Permission',210,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_inventory_callbacks\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 02:56:05'),
(278,'permission_created','Permission',211,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_employee_details\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:15'),
(279,'permission_created','Permission',212,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"allocate_leave\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:15'),
(280,'permission_created','Permission',213,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_inventory\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:15'),
(281,'permission_created','Permission',214,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_inventory_item\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:15'),
(282,'permission_created','Permission',215,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_inventory_item\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:16'),
(283,'permission_created','Permission',216,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete_inventory_item\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:16'),
(284,'permission_created','Permission',217,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"adjust_inventory\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:16'),
(285,'permission_created','Permission',218,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_stock_levels\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:16'),
(286,'permission_created','Permission',219,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"receive_stock\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:16'),
(287,'permission_created','Permission',220,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"transfer_stock\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(288,'permission_created','Permission',221,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_accounting_own_dept\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(289,'permission_created','Permission',222,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_gl_entry\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(290,'permission_created','Permission',223,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete_gl_entry\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(291,'permission_created','Permission',224,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process_invoices\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(292,'permission_created','Permission',225,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_payments\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(293,'permission_created','Permission',226,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_salary\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(294,'permission_created','Permission',227,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve_payroll\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:17'),
(295,'permission_created','Permission',228,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_production_queue\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:18'),
(296,'permission_created','Permission',229,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"start_production\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:18'),
(297,'permission_created','Permission',230,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"complete_production\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:18'),
(298,'permission_created','Permission',231,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_production_callback\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:18'),
(299,'permission_created','Permission',232,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_dispatch_callback\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:18'),
(300,'permission_created','Permission',233,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_recipes\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:18'),
(301,'permission_created','Permission',234,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_recipes\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:18'),
(302,'permission_created','Permission',235,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_recipe\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:19'),
(303,'permission_created','Permission',236,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_recipe\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:19'),
(304,'permission_created','Permission',237,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete_recipe\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:19'),
(305,'permission_created','Permission',238,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"process_sale\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:19'),
(306,'permission_created','Permission',239,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_daily_sales\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:19'),
(307,'permission_created','Permission',240,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_sales_reports\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:19'),
(308,'permission_created','Permission',241,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_prices\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:20'),
(309,'permission_created','Permission',242,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_invoices\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:20'),
(310,'permission_created','Permission',243,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_invoice\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:20'),
(311,'permission_created','Permission',244,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"generate_reports\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:20'),
(312,'permission_created','Permission',245,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_analytics\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:20'),
(313,'permission_created','Permission',246,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export_data\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:20'),
(314,'permission_created','Permission',247,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_branch_metrics\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:21'),
(315,'permission_created','Permission',248,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_settings\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:21'),
(316,'permission_created','Permission',249,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_settings\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:21'),
(317,'permission_created','Permission',250,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_system_settings\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:21'),
(318,'permission_created','Permission',251,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_users\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:21'),
(319,'permission_created','Permission',252,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"manage_permissions\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:05:22'),
(320,'permission_created','Permission',253,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_gl_accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:00'),
(321,'permission_created','Permission',254,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_gl_accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:00'),
(322,'permission_created','Permission',255,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"delete_gl_accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(323,'permission_created','Permission',256,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_gl_entries\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(324,'permission_created','Permission',257,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"approve_gl_entries\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(325,'permission_created','Permission',258,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reverse_gl_entries\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(326,'permission_created','Permission',259,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"post_gl_entries\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(327,'permission_created','Permission',260,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_bank_accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(328,'permission_created','Permission',261,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_bank_accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(329,'permission_created','Permission',262,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"edit_bank_accounts\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(330,'permission_created','Permission',263,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_daily_bank_positions\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(331,'permission_created','Permission',264,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_cash_positions\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(332,'permission_created','Permission',265,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"record_cash_count\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:01'),
(333,'permission_created','Permission',266,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_accounting_periods\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(334,'permission_created','Permission',267,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"create_accounting_periods\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(335,'permission_created','Permission',268,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"close_accounting_periods\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(336,'permission_created','Permission',269,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"lock_accounting_periods\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(337,'permission_created','Permission',270,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"reopen_accounting_periods\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(338,'permission_created','Permission',271,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_general_ledger\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(339,'permission_created','Permission',272,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_trial_balance\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(340,'permission_created','Permission',273,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_balance_sheet\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(341,'permission_created','Permission',274,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_income_statement\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(342,'permission_created','Permission',275,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_cash_flow_statement\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(343,'permission_created','Permission',276,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"export_financial_reports\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(344,'permission_created','Permission',277,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_bank_reconciliation\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(345,'permission_created','Permission',278,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"upload_bank_statements\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(346,'permission_created','Permission',279,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_accounting_dashboard\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(347,'permission_created','Permission',280,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_financial_summary\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(348,'permission_created','Permission',281,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_accounting_reports\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(349,'permission_created','Permission',282,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_variance_analysis\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:02'),
(350,'permission_created','Permission',283,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"view_aging_reports\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:03'),
(351,'permission_created','Permission',284,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"link_sales_to_gl\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:03'),
(352,'permission_created','Permission',285,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"link_purchases_to_gl\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:03'),
(353,'permission_created','Permission',286,NULL,NULL,'127.0.0.1','Symfony','{\"name\":\"link_payments_to_gl\",\"guard_name\":\"web\",\"category\":\"general\",\"is_protected\":false}',0,'2026-01-09 03:37:03');
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
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles` VALUES
(33,'Super Admin','web',5,0,'Full system access with all permissions',1,'2026-01-08 19:00:54','2026-01-08 19:00:54'),
(34,'MD','web',5,0,'Managing Director - Executive level',2,'2026-01-08 19:00:54','2026-01-08 19:00:54'),
(35,'Managing Director','web',5,0,'Managing Director with full operational control',3,'2026-01-08 19:00:54','2026-01-08 19:00:54'),
(36,'Admin','web',4,0,'Administrative access',4,'2026-01-08 19:00:54','2026-01-08 19:00:54'),
(37,'Head of Production','web',3,0,'Head of Production department',10,'2026-01-08 19:00:55','2026-01-08 19:00:55'),
(38,'Sales Manager','web',3,0,'Sales Manager',11,'2026-01-08 19:00:55','2026-01-08 19:00:55'),
(39,'HR Manager','web',1,0,'Human Resources Manager',12,'2026-01-08 19:00:55','2026-01-08 19:00:55'),
(40,'Inventory Manager','web',1,0,'Inventory and Stock Management',13,'2026-01-08 19:00:56','2026-01-08 19:00:56'),
(41,'Supervisor','web',2,0,'Team Supervisor',20,'2026-01-08 19:00:56','2026-01-08 19:00:56'),
(42,'Till Supervisor','web',2,0,'Till/Register Supervisor',21,'2026-01-08 19:00:56','2026-01-08 19:00:56'),
(43,'Chef','web',1,0,'Production Chef',30,'2026-01-08 19:00:57','2026-01-08 19:00:57'),
(44,'Head of Gelato','web',1,0,'Gelato Production Lead',31,'2026-01-08 19:00:58','2026-01-08 19:00:58'),
(45,'Confectioneries Manager','web',1,0,'Confectioneries Production Manager',32,'2026-01-08 19:00:58','2026-01-08 19:00:58'),
(46,'Kitchen Staff','web',1,0,'Kitchen/Production Staff',40,'2026-01-08 19:00:59','2026-01-08 19:00:59'),
(47,'Gelato Production Staff','web',1,0,'Gelato Production Staff',41,'2026-01-08 19:00:59','2026-01-08 19:00:59'),
(48,'Confectioneries Production Staff','web',1,0,'Confectioneries Production Staff',42,'2026-01-08 19:00:59','2026-01-08 19:00:59'),
(49,'Cashier','web',1,0,'Sales Cashier',43,'2026-01-08 19:01:00','2026-01-08 19:01:00'),
(50,'Corner Store Manager','web',1,0,'Corner Store Manager',44,'2026-01-08 19:01:00','2026-01-08 19:01:00'),
(51,'Corner Store Staff','web',1,0,'Corner Store Staff',45,'2026-01-08 19:01:01','2026-01-08 19:01:01'),
(52,'Sales Supervisor','web',2,0,'Sales Department Supervisor',46,'2026-01-08 19:01:01','2026-01-08 19:01:01'),
(53,'Sales Associate','web',1,0,'Sales Associate',47,'2026-01-08 19:01:01','2026-01-08 19:01:01'),
(54,'Junior Cashier','web',1,0,'Junior Cashier',48,'2026-01-08 19:01:02','2026-01-08 19:01:02'),
(55,'Stock Controller','web',2,0,'Stock/Inventory Controller',49,'2026-01-08 19:01:02','2026-01-08 19:01:02'),
(56,'Store Keeper','web',1,0,'Store Keeper/Warehouse Staff',50,'2026-01-08 19:01:03','2026-01-08 19:01:03'),
(57,'HR Officer','web',1,0,'HR Officer',51,'2026-01-08 19:01:03','2026-01-08 19:01:03'),
(58,'Employee','web',1,0,'Standard Employee',52,'2026-01-08 19:01:04','2026-01-08 19:01:04'),
(59,'Viewer','web',1,0,'Read-only access to reports and dashboards',99,'2026-01-08 19:01:04','2026-01-08 19:01:04'),
(60,'Warehouse Manager','web',1,0,'Warehouse Manager with supervisory responsibilities',14,'2026-01-08 19:01:35','2026-01-08 19:01:35'),
(61,'Store Manager','web',1,0,'Store Manager overseeing retail operations',15,'2026-01-08 19:01:36','2026-01-08 19:01:36'),
(62,'Store Supervisor','web',1,0,'Store Supervisor assisting with daily operations',35,'2026-01-08 19:01:36','2026-01-08 19:01:36'),
(63,'Inventory Clerk','web',1,0,'Inventory Clerk for data entry and basic inventory tasks',48,'2026-01-08 19:01:37','2026-01-08 19:01:37'),
(64,'Manager','web',3,0,NULL,0,'2026-01-08 19:01:46','2026-01-08 19:01:46'),
(65,'Staff','web',1,0,NULL,0,'2026-01-08 19:01:46','2026-01-08 19:01:46'),
(66,'Accounting Manager','web',1,0,'Manages financial records and accounting',0,'2026-01-09 02:56:02','2026-01-09 02:56:02'),
(67,'Production Helper','web',1,0,'Views production callbacks and inventory callbacks (filtered by department)',0,'2026-01-09 02:56:02','2026-01-09 02:56:02');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `shifts` VALUES
(1,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,'09c44002-b9bb-3b8a-8ece-4dd0f567dbdc','SHFT-20260108-0001','2026-01-08','morning','2026-01-08 19:52:34',NULL,'active','clock_in',NULL,NULL,NULL,NULL,'2026-01-08 19:52:34','2026-01-08 19:52:34',NULL,NULL),
(2,'019b9efb-b43d-73c7-95bd-0687cf05c811',4,'1ab23065-c6bc-3a74-bd45-163dbcaac1d7','SHFT-20260108-0002','2026-01-08','morning','2026-01-08 20:21:34',NULL,'active','clock_in',NULL,NULL,NULL,NULL,'2026-01-08 20:21:34','2026-01-08 20:21:34',NULL,NULL),
(3,'019b9efb-b43d-73c7-95bd-0687cf05c811',7,'323cf339-e303-3dc5-abbe-185a715abe35','SHFT-20260109-0001','2026-01-09','morning','2026-01-09 02:46:05',NULL,'active','clock_in',NULL,NULL,NULL,NULL,'2026-01-09 02:46:05','2026-01-09 02:46:05',NULL,NULL),
(4,'019b9efb-b43d-73c7-95bd-0687cf05c811',5,'58d67707-7a7b-3160-8f2f-52aa848e94cf','SHFT-20260109-0002','2026-01-09','morning','2026-01-09 06:42:31',NULL,'active','clock_in',NULL,NULL,NULL,NULL,'2026-01-09 06:42:31','2026-01-09 06:42:31',NULL,NULL);
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
(1,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,261.00,15.00,3.00,264.1000,'2025-12-31','good','2027-01-02','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(2,'019b9efb-b43d-73c7-95bd-0687cf05c811',2,186.00,3.00,5.00,494.1000,'2026-01-07','good','2026-10-14','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(3,'019b9efb-b43d-73c7-95bd-0687cf05c811',3,231.00,20.00,11.00,303.9000,'2025-12-17','critical','2026-01-31','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(4,'019b9efb-b43d-73c7-95bd-0687cf05c811',4,55.00,5.00,0.00,397.0000,'2025-12-22','critical','2026-01-30','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(5,'019b9efb-b43d-73c7-95bd-0687cf05c811',5,395.00,4.00,1.00,197.9000,'2025-12-09','good','2026-06-18','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(6,'019b9efb-b43d-73c7-95bd-0687cf05c811',6,293.00,0.00,5.00,382.0000,'2025-12-16','critical','2026-01-31','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(7,'019b9efb-b43d-73c7-95bd-0687cf05c811',7,301.00,28.00,8.00,371.5000,'2026-01-07','good','2026-11-11','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(8,'019b9efb-b43d-73c7-95bd-0687cf05c811',8,313.00,27.00,13.00,385.9000,'2026-01-07','good','2026-07-15','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(9,'019b9efb-b43d-73c7-95bd-0687cf05c811',9,340.00,13.00,7.00,473.7000,'2025-12-11','good','2026-09-06','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(10,'019b9efb-b43d-73c7-95bd-0687cf05c811',10,88.00,8.00,4.00,267.1000,'2026-01-01','good','2026-05-21','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(11,'019b9efb-b43d-73c7-95bd-0687cf05c811',11,334.00,32.00,11.00,165.5000,'2025-12-13','good','2026-07-06','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(12,'019b9efb-b43d-73c7-95bd-0687cf05c811',12,345.00,23.00,16.00,199.2000,'2025-12-17','warning','2026-02-17','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(13,'019b9efb-b43d-73c7-95bd-0687cf05c811',13,292.00,1.00,9.00,37.3000,'2025-12-30','good',NULL,'2026-01-08 19:01:25','2026-01-08 19:01:25'),
(14,'019b9efb-b43d-73c7-95bd-0687cf05c811',14,491.00,8.00,16.00,25.4000,'2025-12-14','good',NULL,'2026-01-08 19:01:25','2026-01-08 19:01:25'),
(15,'019b9efb-b43d-73c7-95bd-0687cf05c811',15,601.00,21.00,26.00,6.8000,'2025-12-29','critical',NULL,'2026-01-08 19:01:25','2026-01-08 19:01:25'),
(16,'019b9efb-b43d-73c7-95bd-0687cf05c811',16,541.00,53.00,1.00,36.5000,'2025-12-14','good',NULL,'2026-01-08 19:01:25','2026-01-08 19:01:25'),
(17,'019b9efb-b43d-73c7-95bd-0687cf05c811',17,149.00,4.00,0.00,245.2000,'2025-12-16','critical','2026-01-15','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(18,'019b9efb-b43d-73c7-95bd-0687cf05c811',18,82.00,0.00,3.00,156.8000,'2025-12-30','critical','2026-01-19','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(19,'019b9efb-b43d-73c7-95bd-0687cf05c811',19,97.00,1.00,3.00,70.9000,'2025-12-28','good','2026-12-08','2026-01-08 19:01:25','2026-01-08 19:01:25'),
(20,'019b9efb-b43d-73c7-95bd-0687cf05c811',20,12.00,1.00,0.00,586.2000,'2026-01-04','critical',NULL,'2026-01-08 19:01:25','2026-01-08 19:01:25'),
(21,'019b9efb-b447-73e3-ac48-56618b9d7edb',21,377.00,17.00,16.00,329.2000,'2025-12-09','good','2026-05-25','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(22,'019b9efb-b447-73e3-ac48-56618b9d7edb',22,278.00,3.00,8.00,461.8000,'2025-12-27','warning','2026-03-22','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(23,'019b9efb-b447-73e3-ac48-56618b9d7edb',23,368.00,7.00,4.00,130.5000,'2025-12-31','good','2026-05-06','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(24,'019b9efb-b447-73e3-ac48-56618b9d7edb',24,397.00,29.00,8.00,155.6000,'2025-12-27','good','2026-06-02','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(25,'019b9efb-b447-73e3-ac48-56618b9d7edb',25,171.00,14.00,4.00,144.6000,'2025-12-15','warning','2026-03-18','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(26,'019b9efb-b447-73e3-ac48-56618b9d7edb',26,332.00,16.00,4.00,205.1000,'2025-12-11','good','2026-05-11','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(27,'019b9efb-b447-73e3-ac48-56618b9d7edb',27,369.00,35.00,8.00,420.9000,'2025-12-16','warning','2026-02-09','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(28,'019b9efb-b447-73e3-ac48-56618b9d7edb',28,224.00,14.00,8.00,329.4000,'2025-12-22','good','2026-04-27','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(29,'019b9efb-b447-73e3-ac48-56618b9d7edb',29,178.00,8.00,8.00,170.9000,'2025-12-09','good','2026-11-03','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(30,'019b9efb-b447-73e3-ac48-56618b9d7edb',30,60.00,2.00,1.00,311.3000,'2026-01-07','good','2026-11-10','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(31,'019b9efb-b447-73e3-ac48-56618b9d7edb',31,90.00,5.00,4.00,205.8000,'2025-12-22','critical','2026-01-31','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(32,'019b9efb-b447-73e3-ac48-56618b9d7edb',32,328.00,17.00,15.00,226.0000,'2025-12-20','good','2026-11-04','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(33,'019b9efb-b447-73e3-ac48-56618b9d7edb',33,128.00,12.00,6.00,31.7000,'2026-01-06','warning',NULL,'2026-01-08 19:01:26','2026-01-08 19:01:26'),
(34,'019b9efb-b447-73e3-ac48-56618b9d7edb',34,486.00,17.00,12.00,13.4000,'2025-12-15','warning',NULL,'2026-01-08 19:01:26','2026-01-08 19:01:26'),
(35,'019b9efb-b447-73e3-ac48-56618b9d7edb',35,664.00,17.00,0.00,26.3000,'2026-01-05','good',NULL,'2026-01-08 19:01:26','2026-01-08 19:01:26'),
(36,'019b9efb-b447-73e3-ac48-56618b9d7edb',36,716.00,16.00,0.00,37.2000,'2025-12-21','critical',NULL,'2026-01-08 19:01:26','2026-01-08 19:01:26'),
(37,'019b9efb-b447-73e3-ac48-56618b9d7edb',37,46.00,3.00,2.00,77.4000,'2025-12-30','warning','2026-02-24','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(38,'019b9efb-b447-73e3-ac48-56618b9d7edb',38,145.00,5.00,6.00,143.9000,'2025-12-20','good','2026-05-22','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(39,'019b9efb-b447-73e3-ac48-56618b9d7edb',39,68.00,6.00,1.00,281.3000,'2025-12-13','warning','2026-03-28','2026-01-08 19:01:26','2026-01-08 19:01:26'),
(40,'019b9efb-b447-73e3-ac48-56618b9d7edb',40,15.00,1.00,0.00,762.5000,'2025-12-14','warning',NULL,'2026-01-08 19:01:26','2026-01-08 19:01:26'),
(41,'019b9efb-b453-7326-aefa-4c9b271f158a',41,147.00,13.00,0.00,302.9000,'2025-12-29','good','2026-07-21','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(42,'019b9efb-b453-7326-aefa-4c9b271f158a',42,189.00,1.00,0.00,303.7000,'2025-12-31','warning','2026-03-27','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(43,'019b9efb-b453-7326-aefa-4c9b271f158a',43,216.00,14.00,10.00,251.9000,'2025-12-09','good','2026-05-13','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(44,'019b9efb-b453-7326-aefa-4c9b271f158a',44,322.00,0.00,6.00,373.3000,'2025-12-26','critical','2026-01-27','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(45,'019b9efb-b453-7326-aefa-4c9b271f158a',45,190.00,0.00,4.00,293.6000,'2025-12-12','good','2026-07-27','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(46,'019b9efb-b453-7326-aefa-4c9b271f158a',46,389.00,30.00,2.00,53.6000,'2025-12-13','good','2026-11-03','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(47,'019b9efb-b453-7326-aefa-4c9b271f158a',47,75.00,4.00,0.00,134.3000,'2025-12-19','good','2026-10-13','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(48,'019b9efb-b453-7326-aefa-4c9b271f158a',48,326.00,1.00,9.00,349.1000,'2025-12-19','good','2026-11-18','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(49,'019b9efb-b453-7326-aefa-4c9b271f158a',49,280.00,20.00,1.00,161.3000,'2025-12-21','warning','2026-02-14','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(50,'019b9efb-b453-7326-aefa-4c9b271f158a',50,346.00,30.00,17.00,104.7000,'2025-12-31','good','2026-12-04','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(51,'019b9efb-b453-7326-aefa-4c9b271f158a',51,100.00,0.00,5.00,54.7000,'2025-12-28','good','2026-12-10','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(52,'019b9efb-b453-7326-aefa-4c9b271f158a',52,135.00,9.00,4.00,400.7000,'2026-01-06','good','2026-07-06','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(53,'019b9efb-b453-7326-aefa-4c9b271f158a',53,798.00,54.00,31.00,23.3000,'2025-12-13','good',NULL,'2026-01-08 19:01:27','2026-01-08 19:01:27'),
(54,'019b9efb-b453-7326-aefa-4c9b271f158a',54,150.00,5.00,5.00,11.9000,'2025-12-13','good',NULL,'2026-01-08 19:01:27','2026-01-08 19:01:27'),
(55,'019b9efb-b453-7326-aefa-4c9b271f158a',55,257.00,6.00,1.00,44.6000,'2025-12-14','good',NULL,'2026-01-08 19:01:27','2026-01-08 19:01:27'),
(56,'019b9efb-b453-7326-aefa-4c9b271f158a',56,520.00,27.00,14.00,24.3000,'2025-12-30','good',NULL,'2026-01-08 19:01:27','2026-01-08 19:01:27'),
(57,'019b9efb-b453-7326-aefa-4c9b271f158a',57,136.00,4.00,4.00,108.3000,'2025-12-25','good','2026-06-10','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(58,'019b9efb-b453-7326-aefa-4c9b271f158a',58,56.00,2.00,2.00,135.2000,'2025-12-20','critical','2026-01-24','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(59,'019b9efb-b453-7326-aefa-4c9b271f158a',59,23.00,1.00,1.00,227.3000,'2025-12-30','warning','2026-03-13','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(60,'019b9efb-b453-7326-aefa-4c9b271f158a',60,7.00,0.00,0.00,1992.5000,'2025-12-18','critical',NULL,'2026-01-08 19:01:27','2026-01-08 19:01:27'),
(61,'019b9efb-b469-7219-8b97-cb9f291b4be4',61,177.00,14.00,2.00,390.8000,'2025-12-17','good','2026-10-20','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(62,'019b9efb-b469-7219-8b97-cb9f291b4be4',62,113.00,8.00,1.00,327.2000,'2025-12-26','good','2026-10-18','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(63,'019b9efb-b469-7219-8b97-cb9f291b4be4',63,149.00,7.00,5.00,317.3000,'2025-12-26','good','2026-04-29','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(64,'019b9efb-b469-7219-8b97-cb9f291b4be4',64,366.00,24.00,5.00,353.5000,'2025-12-21','good','2026-08-16','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(65,'019b9efb-b469-7219-8b97-cb9f291b4be4',65,347.00,12.00,11.00,482.7000,'2025-12-13','good','2026-07-20','2026-01-08 19:01:27','2026-01-08 19:01:27'),
(66,'019b9efb-b469-7219-8b97-cb9f291b4be4',66,325.00,7.00,15.00,123.4000,'2025-12-25','good','2026-07-19','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(67,'019b9efb-b469-7219-8b97-cb9f291b4be4',67,308.00,25.00,10.00,235.7000,'2025-12-21','critical','2026-01-26','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(68,'019b9efb-b469-7219-8b97-cb9f291b4be4',68,307.00,11.00,2.00,376.3000,'2026-01-02','good','2026-07-10','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(69,'019b9efb-b469-7219-8b97-cb9f291b4be4',69,73.00,3.00,0.00,398.8000,'2025-12-17','good','2026-07-14','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(70,'019b9efb-b469-7219-8b97-cb9f291b4be4',70,201.00,1.00,9.00,451.9000,'2025-12-15','warning','2026-02-24','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(71,'019b9efb-b469-7219-8b97-cb9f291b4be4',71,162.00,10.00,8.00,390.5000,'2025-12-14','good','2026-10-21','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(72,'019b9efb-b469-7219-8b97-cb9f291b4be4',72,280.00,18.00,1.00,159.8000,'2025-12-15','warning','2026-03-01','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(73,'019b9efb-b469-7219-8b97-cb9f291b4be4',73,144.00,5.00,2.00,41.8000,'2025-12-21','critical',NULL,'2026-01-08 19:01:28','2026-01-08 19:01:28'),
(74,'019b9efb-b469-7219-8b97-cb9f291b4be4',74,634.00,5.00,7.00,17.1000,'2025-12-15','critical',NULL,'2026-01-08 19:01:28','2026-01-08 19:01:28'),
(75,'019b9efb-b469-7219-8b97-cb9f291b4be4',75,176.00,0.00,5.00,23.4000,'2026-01-02','good',NULL,'2026-01-08 19:01:28','2026-01-08 19:01:28'),
(76,'019b9efb-b469-7219-8b97-cb9f291b4be4',76,687.00,42.00,26.00,37.3000,'2026-01-06','warning',NULL,'2026-01-08 19:01:28','2026-01-08 19:01:28'),
(77,'019b9efb-b469-7219-8b97-cb9f291b4be4',77,117.00,4.00,2.00,126.1000,'2025-12-15','good','2026-07-09','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(78,'019b9efb-b469-7219-8b97-cb9f291b4be4',78,83.00,5.00,0.00,99.9000,'2025-12-13','good','2026-08-14','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(79,'019b9efb-b469-7219-8b97-cb9f291b4be4',79,141.00,14.00,3.00,49.7000,'2026-01-05','good','2026-07-25','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(80,'019b9efb-b469-7219-8b97-cb9f291b4be4',80,10.00,0.00,0.00,1426.8000,'2025-12-25','good',NULL,'2026-01-08 19:01:28','2026-01-08 19:01:28'),
(81,'019b9efb-b473-7177-aceb-a5d244ef3f8c',81,64.00,2.00,1.00,113.9000,'2025-12-19','good','2026-06-21','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(82,'019b9efb-b473-7177-aceb-a5d244ef3f8c',82,239.00,19.00,10.00,330.8000,'2026-01-05','good','2026-11-29','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(83,'019b9efb-b473-7177-aceb-a5d244ef3f8c',83,307.00,25.00,1.00,97.0000,'2025-12-19','good','2026-10-09','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(84,'019b9efb-b473-7177-aceb-a5d244ef3f8c',84,188.00,18.00,2.00,197.0000,'2025-12-23','good','2026-05-04','2026-01-08 19:01:28','2026-01-08 19:01:28'),
(85,'019b9efb-b473-7177-aceb-a5d244ef3f8c',85,249.00,6.00,7.00,326.4000,'2025-12-10','good','2026-12-26','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(86,'019b9efb-b473-7177-aceb-a5d244ef3f8c',86,310.00,28.00,8.00,162.8000,'2025-12-23','warning','2026-03-27','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(87,'019b9efb-b473-7177-aceb-a5d244ef3f8c',87,78.00,5.00,2.00,477.1000,'2025-12-31','good','2026-10-27','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(88,'019b9efb-b473-7177-aceb-a5d244ef3f8c',88,281.00,18.00,9.00,62.0000,'2025-12-15','good','2026-09-27','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(89,'019b9efb-b473-7177-aceb-a5d244ef3f8c',89,152.00,9.00,2.00,175.7000,'2026-01-01','good','2026-12-21','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(90,'019b9efb-b473-7177-aceb-a5d244ef3f8c',90,164.00,14.00,0.00,51.7000,'2025-12-14','critical','2026-02-05','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(91,'019b9efb-b473-7177-aceb-a5d244ef3f8c',91,138.00,0.00,5.00,283.0000,'2025-12-22','warning','2026-04-07','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(92,'019b9efb-b473-7177-aceb-a5d244ef3f8c',92,348.00,15.00,10.00,187.3000,'2025-12-17','good','2026-11-15','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(93,'019b9efb-b473-7177-aceb-a5d244ef3f8c',93,548.00,16.00,7.00,39.2000,'2025-12-20','critical',NULL,'2026-01-08 19:01:29','2026-01-08 19:01:29'),
(94,'019b9efb-b473-7177-aceb-a5d244ef3f8c',94,129.00,2.00,4.00,37.5000,'2025-12-10','good',NULL,'2026-01-08 19:01:29','2026-01-08 19:01:29'),
(95,'019b9efb-b473-7177-aceb-a5d244ef3f8c',95,387.00,8.00,19.00,23.2000,'2026-01-07','critical',NULL,'2026-01-08 19:01:29','2026-01-08 19:01:29'),
(96,'019b9efb-b473-7177-aceb-a5d244ef3f8c',96,778.00,62.00,28.00,31.4000,'2025-12-21','critical',NULL,'2026-01-08 19:01:29','2026-01-08 19:01:29'),
(97,'019b9efb-b473-7177-aceb-a5d244ef3f8c',97,128.00,6.00,0.00,229.9000,'2025-12-26','warning','2026-02-26','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(98,'019b9efb-b473-7177-aceb-a5d244ef3f8c',98,90.00,9.00,3.00,36.0000,'2025-12-11','good','2026-07-29','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(99,'019b9efb-b473-7177-aceb-a5d244ef3f8c',99,144.00,0.00,2.00,220.3000,'2025-12-12','good','2026-10-30','2026-01-08 19:01:29','2026-01-08 19:01:29'),
(100,'019b9efb-b473-7177-aceb-a5d244ef3f8c',100,6.00,0.00,0.00,1652.2000,'2025-12-20','good',NULL,'2026-01-08 19:01:29','2026-01-08 19:01:29');
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
(1,'mg','Milligrams','mg','weight','Milligram',1,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(2,'g','Grams','g','weight','Gram',2,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(3,'kg','Kilograms','kg','weight','Kilogram',3,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(4,'oz','Ounces','oz','weight','Ounce (avoirdupois)',4,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(5,'lb','Pounds','lb','weight','Pound',5,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(6,'ml','Milliliters','ml','volume','Milliliter',6,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(7,'l','Liters','L','volume','Liter',7,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(8,'cl','Centiliters','cl','volume','Centiliter',8,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(9,'fl_oz','Fluid Ounces','fl oz','volume','Fluid Ounce (US)',9,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(10,'cup','Cups','cup','volume','Cup (US)',10,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(11,'tbsp','Tablespoons','tbsp','volume','Tablespoon (US)',11,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(12,'tsp','Teaspoons','tsp','volume','Teaspoon (US)',12,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(13,'pcs','Pieces','pcs','count','Piece',13,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(14,'unit','Units','unit','count','Unit',14,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(15,'dz','Dozens','dz','count','Dozen (12 units)',15,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(16,'mm','Millimeters','mm','length','Millimeter',16,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(17,'cm','Centimeters','cm','length','Centimeter',17,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(18,'m','Meters','m','length','Meter',18,1,'2026-01-08 19:01:15','2026-01-08 19:01:15'),
(19,'in','Inches','in','length','Inch',19,1,'2026-01-08 19:01:15','2026-01-08 19:01:15');
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
('0084f5fc-dada-3f07-98de-fa253e3686e9',NULL,'Ngozi Nwankwo','ngozi.nwankwo.105@sweettooth.com','EMP-ENU005-0105','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,7,NULL,'+234-884-310-4756','90521 Vallie Islands, Enugu, Enugu State, Nigeria','2001-11-04','female','Nigerian','Oluwaseun Chukwu','+234-880-196-4599','2024-04-13 23:00:00','active',NULL,NULL,'morning',258020.42,NULL,'TIN-49005761','2772066007',NULL,NULL,'2025-10-16',4.3,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('019b9efb-b3ff-7394-8cbb-81be9ac84154','019b9efb-b43d-73c7-95bd-0687cf05c811','Super Admin','admin@sweettooth.local',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$t94r.mNNkVSqYGQu6XNuX.e/BERwWRSoYw.XIToK4o/oiJW7EeLau',NULL,NULL,NULL,NULL,NULL,'2026-01-08 19:00:54','2026-01-08 19:12:50'),
('019b9efb-e130-73b8-8b36-b79ac90b24a2',NULL,'Liliane Cole','toy.bernadette@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','Vuf0fKZtiY','ySNRNt5Irp','2026-01-08 19:01:05','2026-01-08 19:01:05','fprrVpHnVs','2026-01-08 19:01:05','2026-01-08 19:01:05'),
('019b9efb-e140-70f0-a849-fc952500b44b',NULL,'Dr. Tomas Cruickshank','collins.jaden@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','hfwpUvKREO','8LI2bc9WG8','2026-01-08 19:01:05','2026-01-08 19:01:05','2rlGM9oF1c','2026-01-08 19:01:05','2026-01-08 19:01:05'),
('019b9efb-e156-72e5-9e9c-2db4def280f7',NULL,'Ms. Jackeline Quigley V','kieran.harris@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','y1x14QJGz4','Gkj1e5IWMc','2026-01-08 19:01:05','2026-01-08 19:01:05','mPCJMZg3jX','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e17a-7031-a370-9aa6c1f8f18d',NULL,'Icie Schumm','tpfannerstill@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','Nh8Cx6N2yW','HJ1HjsGCWN','2026-01-08 19:01:05','2026-01-08 19:01:05','9jkXCsrU6e','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e190-710a-85f9-4a0d1a71fc0b',NULL,'Prof. Shyann Hamill','creola.stanton@example.org',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','z35O6Z47z1','nkU2ggkNZu','2026-01-08 19:01:05','2026-01-08 19:01:05','Vjo8boo0D6','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e19b-7077-ac8a-b5454a813066',NULL,'Santos Marks','heber90@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','H7eChfdcnS','AOnqWoPvcK','2026-01-08 19:01:05','2026-01-08 19:01:05','6msXRGelOW','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1a6-70eb-9091-c1405473ab5a',NULL,'Prof. Emilie Ziemann III','ybogan@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','RzImJQpcXo','gTMPD18vZi','2026-01-08 19:01:05','2026-01-08 19:01:05','7TGct550Wo','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1b1-71ce-b8bb-2e9799b9594a',NULL,'Prof. Jaren Kilback','rosella33@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','c9p4xk52Vy','Z9C9gu8wJU','2026-01-08 19:01:05','2026-01-08 19:01:05','oRNzanBefT','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1bc-706b-ac99-f713893b3afe',NULL,'Dr. Peggie Douglas','jeanne52@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','zkFLzTmMsW','oRIkp7NeJM','2026-01-08 19:01:05','2026-01-08 19:01:05','gyQZSmHwy0','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1c8-72fd-8cbd-9e8e6dc89839',NULL,'Cassidy Morissette','langosh.jacinto@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','bxLYQPj0tY','zkoJPatZlK','2026-01-08 19:01:05','2026-01-08 19:01:05','I24zjWgB9d','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1d3-7238-803a-a2fbb1d65588',NULL,'Prof. Monserrate Dickinson','helen.gusikowski@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','2iXHeDwQ5h','aEaC4Q4PsU','2026-01-08 19:01:05','2026-01-08 19:01:05','cdiAHZ2eHc','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1df-732f-ade1-ec953be84571',NULL,'Ricky Kunde','lkuvalis@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','sLSUFiEAo6','fS1LHqgPut','2026-01-08 19:01:05','2026-01-08 19:01:05','E2JJzaN4uP','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1eb-70ad-a7e0-3ca5b8ced301',NULL,'Mr. Braxton Mann','fborer@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','0wUYyPGQUK','70O8uKHPCK','2026-01-08 19:01:05','2026-01-08 19:01:05','mJTVHVpFSk','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1f4-7222-ba0c-ef28c8e1f3f6',NULL,'Candida Schaefer','yvette.wyman@example.org',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','cBhTDWTJwd','T1zWF7MmCN','2026-01-08 19:01:05','2026-01-08 19:01:05','cIaot8LcVz','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e1ff-71c0-b54d-ece332df7591',NULL,'Cierra Frami','reed88@example.org',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','pIr8dI6Ne1','p5L1jKm6qj','2026-01-08 19:01:05','2026-01-08 19:01:05','yMTxq0Ihl0','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e20a-71e2-aeb5-9f8ea7dff02e',NULL,'Mr. Conor McClure','pdamore@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','IULunawtk3','ZbWQXsL4N4','2026-01-08 19:01:05','2026-01-08 19:01:05','B9K3xQy3uf','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e216-71c6-b127-94fce3cd77a4',NULL,'Krystal Weber','cmaggio@example.org',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','rkr5JWlDDe','kjr2PgOQ4b','2026-01-08 19:01:05','2026-01-08 19:01:05','rlYzD40zo5','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e221-739e-855e-7f333631cdc4',NULL,'Mr. Winston O\'Keefe DVM','oberbrunner.katlyn@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','ugPlwsAMmK','HMWqq0QGMu','2026-01-08 19:01:05','2026-01-08 19:01:05','4yxLhuRqzP','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e22d-7346-aec6-d98b25b38217',NULL,'Nathaniel Emard','jake95@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','kbAImfQMeV','WBmHdnmFLN','2026-01-08 19:01:05','2026-01-08 19:01:05','emkgSKVuFZ','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e237-72ac-99e8-e9ecfe25cdb4',NULL,'Mabel Rogahn MD','kristy.bayer@example.org',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','lKR1gtzvr9','V9V6CHBT2L','2026-01-08 19:01:05','2026-01-08 19:01:05','5a7N1V2bZ4','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e241-7142-babc-a141f17b26ec',NULL,'Mr. Kane Schmidt DDS','carroll.candice@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','NGUxYCn0kD','6uyDwnyhXi','2026-01-08 19:01:05','2026-01-08 19:01:05','On1ZEN8Wet','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e24f-737c-87e7-ceff3baa1da4',NULL,'Houston Ledner','pkutch@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','rMqjRJGj1I','WFgFVJlJOQ','2026-01-08 19:01:05','2026-01-08 19:01:05','rp9RkHnFdU','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e259-7305-aaef-e9159b4b1592',NULL,'Emmanuelle Sanford','heber.schulist@example.org',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','GGPhOQMNJ1','7Ri3o0D2g8','2026-01-08 19:01:05','2026-01-08 19:01:05','a7II7mGC7e','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e26e-7311-8b80-5dd05f393522',NULL,'Sylvester Osinski','zmetz@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','oN1gsoqfuJ','iFPArA6ACm','2026-01-08 19:01:05','2026-01-08 19:01:05','h1McBWuKlu','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e2c0-727e-bbc0-8cb674a3ae05',NULL,'Prof. Emilie Kautzer PhD','garth08@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','IcjU6sx2Ll','Zvgnm6R3H3','2026-01-08 19:01:05','2026-01-08 19:01:05','WDl10heLvW','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e2d2-7351-aa19-b27d8bbdf689',NULL,'Mozelle Nikolaus','gladyce46@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','cIq8JhnlGA','zWnBgXZKzY','2026-01-08 19:01:05','2026-01-08 19:01:05','ezDsFom5Tv','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e2dd-71ba-be7a-7d25c23221c0',NULL,'Ms. Mazie Mitchell PhD','abbott.dulce@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','FSRATvyAqS','leK6O8xlZK','2026-01-08 19:01:05','2026-01-08 19:01:05','lM0PtR9zzh','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e2e9-722e-b1d9-874ef69b8447',NULL,'Bianka Williamson','vcrist@example.net',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','AD78ptTY08','qwa8IznAyJ','2026-01-08 19:01:05','2026-01-08 19:01:05','MhIjXWe41W','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e2f3-738c-9a58-6702513da1c5',NULL,'Dr. Serena Padberg','hane.brandt@example.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','9vRlxkApoh','NvPFtNV4mY','2026-01-08 19:01:05','2026-01-08 19:01:05','Mj5RBxwq1A','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e2ff-73c9-819f-2c10f9b8f617',NULL,'Hector Grimes','towne.raleigh@example.org',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$.MwgS59g/P8Jd7rjRwjACO9AK1mKlbGfjVxtqnVUBD3xlkfrLqne.','zh08JfFMjk','zXHuEV91LQ','2026-01-08 19:01:05','2026-01-08 19:01:05','bya2BP4O0z','2026-01-08 19:01:06','2026-01-08 19:01:06'),
('019b9efb-e81b-73aa-86c8-9638fe490f29',NULL,'Gloria Hane','whayes@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','p2jN4YjBig','kZKX6KIADj','2026-01-08 19:01:07','2026-01-08 19:01:07','GXHy8Xjk1O','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e88b-7286-ac73-86a3414bcf7c',NULL,'Emmie Ankunding','johns.ludwig@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','7ZE7pQ1biT','4mTxYdp6yC','2026-01-08 19:01:07','2026-01-08 19:01:07','v2b4Ss72H2','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8a4-707c-8363-76e1d41cf7c8',NULL,'Ike Beer','giovani23@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','h6gUvRoMKO','hL7JG8sSpY','2026-01-08 19:01:07','2026-01-08 19:01:07','fRZTWMu5tD','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8b0-7217-937a-971673a46804',NULL,'Shea Schmidt','nia17@example.com',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','xtbQgV1ixh','SWpTGVm8QN','2026-01-08 19:01:07','2026-01-08 19:01:07','Th3uJNcI0D','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8b9-72ec-86f7-12a69e050067',NULL,'Rebeca Balistreri','sawayn.orin@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','JxY12TIh9u','veShqlJv5o','2026-01-08 19:01:07','2026-01-08 19:01:07','pAmlbjErcy','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8c4-726f-9a44-ce4638ac5ccf',NULL,'Reginald Lehner PhD','terence59@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','9I5W0tWpmu','OcaoDWXwni','2026-01-08 19:01:07','2026-01-08 19:01:07','XpcyFOAxgF','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8d1-73f4-9488-92301c3c3d40',NULL,'Hilton Hane','otho44@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','MAdsuL2ew6','XpF4rsTV3t','2026-01-08 19:01:07','2026-01-08 19:01:07','RxpfoVBj3s','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8db-7190-99f2-6432e6b16954',NULL,'Jamison Torp DDS','lesch.leora@example.com',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','pOCtiS1cRx','PoZVBxDVsL','2026-01-08 19:01:07','2026-01-08 19:01:07','Joc8KRJNt0','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8e6-7201-9e22-8d98faff6ce4',NULL,'Delaney Bartoletti','devon.runte@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','OMUCqe39F8','7ffkDqlvSU','2026-01-08 19:01:07','2026-01-08 19:01:07','AHf2mhRE24','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8f1-73d9-9ea3-84521ca31d93',NULL,'Ona Wisozk','ambrose95@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','WzTQFbyKOh','SisqgsKUKp','2026-01-08 19:01:07','2026-01-08 19:01:07','K0RrpJmwMN','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e8fc-70f3-acf1-c7c79810ad54',NULL,'Paxton Schaefer','bartoletti.randy@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','BwX24mNzP6','tsFBDiavwu','2026-01-08 19:01:07','2026-01-08 19:01:07','vL7DrGFMt6','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e907-73b5-9182-9952eafa16ab',NULL,'Lamar DuBuque I','graham.melissa@example.com',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','Bny9CcG3dm','GgBTjjCsQV','2026-01-08 19:01:07','2026-01-08 19:01:07','DvZqEYPUy9','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e913-71f2-a396-91b3bccbd7fb',NULL,'Jo DuBuque','bergnaum.vince@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','lpPkK3Omde','21rH8k6ib9','2026-01-08 19:01:07','2026-01-08 19:01:07','9XgISarH0K','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e91d-72e5-85f2-912e03d9c1ff',NULL,'Mustafa Legros','carolina81@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','Yy6Gdde9Av','M5vXYTmLGq','2026-01-08 19:01:07','2026-01-08 19:01:07','wfNvJx9ieB','2026-01-08 19:01:07','2026-01-08 19:01:07'),
('019b9efb-e928-70cf-b576-5b6862daf5b9',NULL,'Mr. Foster Farrell Sr.','litzy49@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','kR0irEuhf5','h34eZHXIwQ','2026-01-08 19:01:07','2026-01-08 19:01:07','YO5SxUJyYO','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e935-7099-8bf1-298073ba8d93',NULL,'Ms. Aniyah Greenfelder','vjacobs@example.com',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','ikXPDBge3e','4bJR0fccF0','2026-01-08 19:01:07','2026-01-08 19:01:07','WkdyKkmc0T','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e94a-72b9-80c4-0652566eeef6',NULL,'Mrs. Aurelie Trantow V','pgusikowski@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','8XY2W5hBh1','EbZqsAEVlc','2026-01-08 19:01:07','2026-01-08 19:01:07','0R4qbXzvUX','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e956-73e4-9e27-b541ede9767b',NULL,'Jenifer Ernser','godfrey.kozey@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','NmZCeVYEw0','y08YK9npsV','2026-01-08 19:01:07','2026-01-08 19:01:07','v7CJreUfja','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e96c-73b7-8074-73860b28db90',NULL,'Miss Katelynn Hickle DDS','tmetz@example.com',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','us2E9kLYWu','ga1Y6E2AYS','2026-01-08 19:01:07','2026-01-08 19:01:07','Qkc3UBjpAH','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e977-71c9-b8e1-515f8018468d',NULL,'Norbert Herman','gabriel29@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','oyJzJRs05a','TvyS2P42pj','2026-01-08 19:01:07','2026-01-08 19:01:07','4Ke9bguS6h','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e982-7035-84be-c21728e85f18',NULL,'Noble Koch','hmoore@example.com',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','er1eGQqMw6','Zj7FqsRfG8','2026-01-08 19:01:07','2026-01-08 19:01:07','HSBDEeUVm0','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e98d-705b-b05e-537abd74079d',NULL,'Miss Jolie Mraz I','adela.reynolds@example.com',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','21NBskdd27','GZqw6Q5BxE','2026-01-08 19:01:07','2026-01-08 19:01:07','w40aOL616K','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e998-72bf-9ffd-8ed2ad4370d0',NULL,'Genesis Runolfsson','whomenick@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','piobhsRTed','z1o9X62776','2026-01-08 19:01:07','2026-01-08 19:01:07','HyBnVOicIN','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e9a3-72e4-8348-c083a0d3b780',NULL,'Rogers Kris','nelda.beahan@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','LSAvMKzqcC','QbzKbcAwUg','2026-01-08 19:01:07','2026-01-08 19:01:07','CXt0xX4VRk','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e9ae-70a0-9a37-709b1b2b0208',NULL,'Mr. Maverick Franecki IV','estracke@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','T047F2Jloe','b6jqqLXIft','2026-01-08 19:01:07','2026-01-08 19:01:07','PrRa4Qtdb4','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e9b9-7113-a2bf-a9bdf3430655',NULL,'Eliza Mann','clarissa.breitenberg@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','xMaJ2GE50h','bL0hay9qTZ','2026-01-08 19:01:07','2026-01-08 19:01:07','jaXfIzxif6','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e9c4-7321-aeae-72152ce598bc',NULL,'Randy Hintz','stoltenberg.kameron@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','bmUjhify15','wRAIYSI8Ta','2026-01-08 19:01:07','2026-01-08 19:01:07','cVoCBATdPv','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e9cf-737e-a671-2626e39d9d41',NULL,'Roberta Schamberger Jr.','yreichert@example.org',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','1bPpLSZEDN','bSJWniZFhQ','2026-01-08 19:01:07','2026-01-08 19:01:07','Raw5khsFhE','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e9dd-7185-9fd8-cf44eec69dce',NULL,'Dr. Domenick Bode','ykeebler@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','j67vyn7Va8','hwy6FYP4V9','2026-01-08 19:01:07','2026-01-08 19:01:07','8YZ3h5aG6Q','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-e9e6-707d-b6be-8e99e40cdf30',NULL,'Kaleigh Jakubowski Jr.','tmosciski@example.net',NULL,'019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$BkSkGY./zUfUCUlfTDPY3.A70Wt.rADGoPBAJZeuQwo/1D8IAhpGK','DZvSjUu1QB','supQVW1qFt','2026-01-08 19:01:07','2026-01-08 19:01:07','vLLwQXogGe','2026-01-08 19:01:08','2026-01-08 19:01:08'),
('019b9efb-eea1-7258-bf34-03d18ae444dc',NULL,'Betty Ernser','cyril95@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','EOwjPeeHVK','uAXG2VGcaE','2026-01-08 19:01:09','2026-01-08 19:01:09','KSJaSAim8c','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-eeb8-70be-97ad-db124e3ff74c',NULL,'Fannie Hyatt','jett42@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','ZQolvmktos','FxigwQvY85','2026-01-08 19:01:09','2026-01-08 19:01:09','psxODXv75g','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-eec3-7204-ae65-62d1af85ebd8',NULL,'Eulah O\'Kon','hlangworth@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','jzipb2LNgU','Zg8cplP6y9','2026-01-08 19:01:09','2026-01-08 19:01:09','PYVwGZ8HVK','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-eed2-73c7-9644-fc03e0fd79cb',NULL,'Aida Boyle','johnson.luther@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','KvTVp58nUG','O479MTPKc0','2026-01-08 19:01:09','2026-01-08 19:01:09','j9Wilxo5Vu','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef35-72d7-b4d5-5c2f07906153',NULL,'Edyth Conn','dbatz@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','jdD2KcuS2R','W4wQVyKx05','2026-01-08 19:01:09','2026-01-08 19:01:09','aBkKW6WUOs','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef48-721a-9928-4044fd4ff8f1',NULL,'Dorris Barton MD','walker37@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','0n67c3EfOG','OoKYRQONZm','2026-01-08 19:01:09','2026-01-08 19:01:09','Ab08hlH9B2','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef54-718a-a64c-2bd5bd5dff82',NULL,'Miss Marcella Gaylord','qhilpert@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','daW5ZtDOlB','2ixMflJtTJ','2026-01-08 19:01:09','2026-01-08 19:01:09','cb9luSqLKn','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef5e-702f-86c4-36708c4ff9c2',NULL,'Gavin Klocko II','phyllis27@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','HJtqLdhbCq','zXaXi4imeo','2026-01-08 19:01:09','2026-01-08 19:01:09','yt7YtAwfHD','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef6a-7034-9863-424c160b176c',NULL,'Elbert Kohler','cskiles@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','nVuly8OKUm','1Hvd44SiZx','2026-01-08 19:01:09','2026-01-08 19:01:09','xWnLWtoGlK','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef75-71b0-b869-784cec1a7f53',NULL,'Elisha Runolfsson','jayda53@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','QwQeeGEg8P','AfohkntAht','2026-01-08 19:01:09','2026-01-08 19:01:09','4eq43eTuHq','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef7f-72a4-9d8d-cf486e2efc1d',NULL,'Mrs. Halie Grant','ydenesik@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','sqIUoXE066','eWciRKyBfj','2026-01-08 19:01:09','2026-01-08 19:01:09','nutJOKiNlF','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef8b-719f-a51f-8014c14de97a',NULL,'Daniela Heathcote','lehner.joesph@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','B5vixOFGoF','uxFl9ZQyEb','2026-01-08 19:01:09','2026-01-08 19:01:09','ebiTPg2OVH','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-ef96-7269-ad48-ab3c0b15bab2',NULL,'Deja Champlin','usipes@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','JPbxT8TeK5','0lfcFNTqfz','2026-01-08 19:01:09','2026-01-08 19:01:09','hOftHMz1F4','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-efa5-70fb-86c5-bcfba286d227',NULL,'Madonna Mayert','krajcik.seamus@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','viMoC6UJc4','rQyYD21juY','2026-01-08 19:01:09','2026-01-08 19:01:09','GovfQWy1lL','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-efb7-7334-8ad2-e04909e3a7ab',NULL,'Braxton Wisoky V','arne.johns@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','5f0vFcuHfx','oJ2O37pTkB','2026-01-08 19:01:09','2026-01-08 19:01:09','HSivzD1N9A','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-efc2-704b-8fd9-d6753a42fac3',NULL,'Eleanore Keebler','leone95@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','bRhLjX142w','6cAHXbQJT5','2026-01-08 19:01:09','2026-01-08 19:01:09','aTDBVPbjKb','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f014-73a2-b564-00ffa9d57a97',NULL,'Ms. Estel Stanton','veronica09@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','q8Rk5fiZUm','mxh9v5kfjL','2026-01-08 19:01:09','2026-01-08 19:01:09','THTtEVf0Hv','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f026-7223-9a89-fdd3956fd97d',NULL,'Arne Schuppe V','wendy.schoen@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','RYZCGd8gCw','J7JZVYY8tU','2026-01-08 19:01:09','2026-01-08 19:01:09','l533uxzRDX','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f031-73b2-8672-399cc1607fee',NULL,'Lily Bins','casper65@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','ZGgGKIgDfK','r0Hp9YfQwo','2026-01-08 19:01:09','2026-01-08 19:01:09','xDNzGXWHBj','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f03c-735f-b3c2-2607a9985a02',NULL,'Darian Doyle','lwill@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','MhtgKbWxsN','4132SDxXgV','2026-01-08 19:01:09','2026-01-08 19:01:09','IpM69J0ok2','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f047-7323-be3d-293b76ad6356',NULL,'Lourdes Hoppe','wilderman.anahi@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','yM6rSPlE7k','56q4JQf3Q8','2026-01-08 19:01:09','2026-01-08 19:01:09','UtSFVJJs8C','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f053-7138-9cfd-cdd921ae8a7a',NULL,'Jaime Rice','goldner.greyson@example.org',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','7kKhFRgOnT','xlf788tpit','2026-01-08 19:01:09','2026-01-08 19:01:09','0fWZSJLvGc','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f05e-71f8-b199-e61b11bb7ddb',NULL,'Lina Donnelly V','robbie97@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','dKNi55NzwC','0DdZuQWABI','2026-01-08 19:01:09','2026-01-08 19:01:09','ucl7150LNL','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f069-71e1-874d-8683f73f9158',NULL,'Alaina Hessel','ujaskolski@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','uvgsveS5XY','7qkQCGTeCZ','2026-01-08 19:01:09','2026-01-08 19:01:09','arJJOBLzLY','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f074-7099-9b24-7f3ff31364d3',NULL,'Norris Donnelly','tokon@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','tqWUPqXqvt','MmxH7KPW0Q','2026-01-08 19:01:09','2026-01-08 19:01:09','1GrLpohULp','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f07f-7281-8b19-78b9d6d793a6',NULL,'Tito Bauch','deondre.parker@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','uIpY0NBFJb','7RzCTdU06a','2026-01-08 19:01:09','2026-01-08 19:01:09','8Y6AcHZhH4','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f08a-72b2-ae8e-a8d727d5a543',NULL,'Mr. Jaron Sipes','torphy.luisa@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','0nTT8BSmEP','jJTX0IsWAa','2026-01-08 19:01:09','2026-01-08 19:01:09','v4Gik1LbeR','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f095-71bc-b0c5-1f44d53a91c8',NULL,'Alexzander Bogisich III','lowe.noemi@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','S6h680t2E2','GyEstqQsXA','2026-01-08 19:01:09','2026-01-08 19:01:09','mvSHpnqdRR','2026-01-08 19:01:09','2026-01-08 19:01:09'),
('019b9efb-f0fe-737b-8e9c-25bad61b688c',NULL,'Ms. Alaina Durgan III','alysha61@example.net',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','gjR28EW8m5','b6eY3UWPos','2026-01-08 19:01:09','2026-01-08 19:01:09','bacumBf5N2','2026-01-08 19:01:10','2026-01-08 19:01:10'),
('019b9efb-f110-70e1-8b77-4a619100dcbe',NULL,'Alena Heller','yrolfson@example.com',NULL,'019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$YdeG5KIJkVsa8aAAWcNxDuxFbpkEt/jsTcAKGZ1R/gtH684hP4Xb.','2CKEMroY6l','AGhR6U4buU','2026-01-08 19:01:09','2026-01-08 19:01:09','epGJBaQPDQ','2026-01-08 19:01:10','2026-01-08 19:01:10'),
('019b9efb-f5a5-7391-91ef-218fa807d7b1',NULL,'Priscilla Flatley','hand.abagail@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','HyShDKw8rf','RG5LxOzHJz','2026-01-08 19:01:11','2026-01-08 19:01:11','Z6jz6RDDot','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f63d-738d-84c1-de734b237735',NULL,'Cristobal Crist','kay36@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','0QPYLYUiuR','rZ993TTuZu','2026-01-08 19:01:11','2026-01-08 19:01:11','1KjYklW0n4','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f650-7331-981b-63f9b1cab818',NULL,'Elva Lang','jacinthe.kiehn@example.org',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','lHFiIEx30P','lHPkaX3K8H','2026-01-08 19:01:11','2026-01-08 19:01:11','zrvsoBYTxf','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f65b-7157-81a3-62c6860a07bf',NULL,'Dr. Angela Hodkiewicz','dock.lindgren@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','Dg5Mk0GB6O','4qq5eTmI8x','2026-01-08 19:01:11','2026-01-08 19:01:11','FUsBUqRnoz','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f667-72c2-9828-af8d9a421e9c',NULL,'Alejandrin Weissnat IV','legros.coty@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','ZV8Z1Q7OhD','dblYQLP6xF','2026-01-08 19:01:11','2026-01-08 19:01:11','bbycMW0qgk','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f671-7202-8f2c-211cb6b4fa03',NULL,'Lourdes Klocko','abraham.mcdermott@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','g2d5gGxrGG','QJZuNn8wrr','2026-01-08 19:01:11','2026-01-08 19:01:11','MnvnDHIF92','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f67d-704d-b95b-2a81ada309c5',NULL,'Gabriel Kuphal','april.ward@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','xaz6FvpEKX','Vgs9JQZQxs','2026-01-08 19:01:11','2026-01-08 19:01:11','dzHPhLuFpB','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f687-722e-bf19-2d8224d253f2',NULL,'Gavin Barton','buford03@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','DATShPcDVQ','ocLc65UDII','2026-01-08 19:01:11','2026-01-08 19:01:11','7qyoalIOGJ','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f6d8-726f-88e9-b658c977c892',NULL,'Layne Kub','xgrimes@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','Qg8HLBMyGd','umDNxeo4VA','2026-01-08 19:01:11','2026-01-08 19:01:11','o3ZboDZ5PS','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f6ef-73bb-aa29-465cc67c205d',NULL,'Daron Friesen','abernathy.jarod@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','t0PksS5xxH','4oSeAVszWN','2026-01-08 19:01:11','2026-01-08 19:01:11','wnGoMSrlbR','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f6f7-739c-92c3-869253a248f9',NULL,'Lavinia Ruecker','gay43@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','qXSfbNmw1t','So3banp6Mv','2026-01-08 19:01:11','2026-01-08 19:01:11','qJCfLXeD9A','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f703-7035-86f9-9ed18a25b587',NULL,'Giovanny Langosh','keeling.christine@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','fMGliZhqi5','hvt3yvTv88','2026-01-08 19:01:11','2026-01-08 19:01:11','5Kf6qER0iF','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f70d-7345-b873-f02b03d3ffd2',NULL,'Hallie Hessel','heaney.jane@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','SxELl7vsdp','oy0jeQszP1','2026-01-08 19:01:11','2026-01-08 19:01:11','uriBGAiVct','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f718-715e-97b3-3e478b3899ef',NULL,'Jerel Corkery','joana01@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','zcg5MIaMyc','lmUOM0clsp','2026-01-08 19:01:11','2026-01-08 19:01:11','WnY8hok8I5','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f723-7114-bec7-6b874dcefc75',NULL,'Jeffery Farrell','neha26@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','IRMxoa2cDz','C7e8ouwoxb','2026-01-08 19:01:11','2026-01-08 19:01:11','BQM9PXE4kN','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f78a-725e-b86b-d2004e5a592e',NULL,'Dr. Lucious Funk','rlangworth@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','OqjajmoObn','xvLrWlSX0A','2026-01-08 19:01:11','2026-01-08 19:01:11','p5SOrdaas7','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f79d-72fe-a226-f9f37f3d429f',NULL,'Mrs. Rosella Gleason DDS','jbrakus@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','lxNlPKKKnJ','jt5TZXJGX8','2026-01-08 19:01:11','2026-01-08 19:01:11','3TlULEmMBw','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f7a9-71dc-9671-6d1aa6029e3d',NULL,'Jeanie Bogan','raleigh.bode@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','mPiL2vdaQy','R0G2u8IyWZ','2026-01-08 19:01:11','2026-01-08 19:01:11','yLmwvm2jMV','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f7b3-7177-8bba-7d8c00ee2855',NULL,'Mr. Cesar Quitzon DVM','bstamm@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','qwB9jks0AT','UAJjIiAOir','2026-01-08 19:01:11','2026-01-08 19:01:11','g39lxqDaYU','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f7bf-72d4-982a-5b0284dc6924',NULL,'Mrs. Jayda Johnston','nstanton@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','EqOXY2EdMH','A2AGw8KGQl','2026-01-08 19:01:11','2026-01-08 19:01:11','BrlOryczut','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f7ca-7187-ac47-4cbae9a432da',NULL,'Marcellus Dare III','ewiza@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','r1wMDuLIoy','y8hZQ3L4SV','2026-01-08 19:01:11','2026-01-08 19:01:11','LN1rL7FRaX','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f81a-7154-ad91-5dfb2436bd65',NULL,'Marcos Stehr','thomas.mckenzie@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','RuPVB2oRpn','1jL2rn6HNP','2026-01-08 19:01:11','2026-01-08 19:01:11','KpKCx9s4dM','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f832-735f-a50b-9fbef8063b1f',NULL,'Brando Hand MD','alindgren@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','QFyKDRVxil','DQ4T6BwzmU','2026-01-08 19:01:11','2026-01-08 19:01:11','fhz5ejfFmT','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f844-71d4-b391-6e5c62d8e8c3',NULL,'Austen Bogisich','hegmann.kenneth@example.com',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','YNrRDQXuYM','spcbAzKUAd','2026-01-08 19:01:11','2026-01-08 19:01:11','20iAiBAtkb','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f85a-7196-bd74-2a18c2aa8150',NULL,'Dorthy Lind','chanel.oconner@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','DnzVcNODxw','NvY0uiRNr0','2026-01-08 19:01:11','2026-01-08 19:01:11','r9IgAHa1DF','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f8b6-7217-985b-6b37522c7b1e',NULL,'Paul Marvin','johnson.forest@example.org',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','eZ4kezAJzB','WKM4KLrGC3','2026-01-08 19:01:11','2026-01-08 19:01:11','yj5ss3pGc8','2026-01-08 19:01:11','2026-01-08 19:01:11'),
('019b9efb-f8c9-72f5-8c62-e9c936fd4da2',NULL,'Prof. Gregg Moore I','ischultz@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','4B8CwWZV0u','d7C8jyFHg8','2026-01-08 19:01:11','2026-01-08 19:01:11','RqN3ybUKZ5','2026-01-08 19:01:12','2026-01-08 19:01:12'),
('019b9efb-f8d6-735a-a1f6-cf67a0b28888',NULL,'Lisette Schumm','kiley.conn@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','2fPUjWAipZ','Izvm2WCWqZ','2026-01-08 19:01:11','2026-01-08 19:01:11','YhkNA4LPoO','2026-01-08 19:01:12','2026-01-08 19:01:12'),
('019b9efb-f8e0-712b-a960-f2f4bbd32a67',NULL,'Mr. Judah Hoeger','felipe83@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','lNrsZw1ibK','P1C6N1pZiT','2026-01-08 19:01:11','2026-01-08 19:01:11','pIjQxXUht5','2026-01-08 19:01:12','2026-01-08 19:01:12'),
('019b9efb-f8ef-731d-a8a1-5e37a2cf099e',NULL,'Mrs. Shany Kulas Jr.','ettie96@example.net',NULL,'019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$brewCRXEd8z1aTw2aaXGneWGEDxR9kBTdzeqziqjgnIuTT9SJ8Oyy','VcOpccU89e','efLmqIOyfW','2026-01-08 19:01:11','2026-01-08 19:01:11','Q0zGNp8F1A','2026-01-08 19:01:12','2026-01-08 19:01:12'),
('019b9efc-0104-703d-90b8-e5a75d9083dd',NULL,'Dr. Roxane Christiansen','jtoy@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','XO9nr02qEc','ORgYw5srN0','2026-01-08 19:01:14','2026-01-08 19:01:14','VrI6h1T5Am','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-01b7-7287-91c3-5323fbd53f0e',NULL,'Hardy Dibbert','dibbert.estella@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','kBRCOow6Hm','d2ZfhMgBQP','2026-01-08 19:01:14','2026-01-08 19:01:14','TlUBGJn6sy','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-01f5-7089-95e1-edf8fa0e39c1',NULL,'Miguel Rosenbaum','gerhold.jacklyn@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','kRsHienUwz','khjtxzbgCk','2026-01-08 19:01:14','2026-01-08 19:01:14','nRmEdXoonc','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-01fe-71a5-a7bc-6824472baa12',NULL,'Ms. Karianne Mayer DVM','jerrell98@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','7IND9wJuQV','ZNgYoTjtpQ','2026-01-08 19:01:14','2026-01-08 19:01:14','ZVGiwDWsWZ','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0208-7143-9632-ab50c4f22d21',NULL,'Prof. Henri Goodwin Sr.','rubye.bosco@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','Xlo726O6dq','3VUxYd7MKo','2026-01-08 19:01:14','2026-01-08 19:01:14','9WqnGFFNsa','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0213-73a5-8eaf-1339b2612490',NULL,'Ruben Balistreri','jdickinson@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','IePv4ZZpd6','NGJTs6F2mf','2026-01-08 19:01:14','2026-01-08 19:01:14','6t90nsjUDo','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-021f-724b-8ccc-7c370cc0b2cb',NULL,'Ms. Monique Moen DVM','josephine.spinka@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','URxWzKKAWA','i8TGV2thd9','2026-01-08 19:01:14','2026-01-08 19:01:14','Xns27EjdKE','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-022a-7208-b747-ea6c714fa002',NULL,'Dr. Pearl Greenfelder','deven.mraz@example.net',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','k2k2EbXpR8','0sn3LdutCC','2026-01-08 19:01:14','2026-01-08 19:01:14','OeBKvVGfxq','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0237-71ef-9b84-b9897fef1eb5',NULL,'Mariela Willms','johnny.treutel@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','u6nzkc7n3l','r2rX4kyqLH','2026-01-08 19:01:14','2026-01-08 19:01:14','vmWjb6EEaR','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-023f-7374-90e4-1e1396e920fc',NULL,'Carolina Rath Sr.','mortimer41@example.net',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','VI03F3dIJ3','CDCTLEsiqA','2026-01-08 19:01:14','2026-01-08 19:01:14','AYtaB5P7Ek','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-024b-73fb-a350-c367afb8b4f7',NULL,'Cyrus Robel','harvey.elbert@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','XjE9C3x9fK','hpspva3OPU','2026-01-08 19:01:14','2026-01-08 19:01:14','FeZszcglx8','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0256-73e1-8d51-3466e83282c7',NULL,'Celestino Cormier','littel.noble@example.net',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','GXeGXC6Fzd','HMYzVFSTT7','2026-01-08 19:01:14','2026-01-08 19:01:14','iHfcEP1AHH','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0261-7221-8b7f-e26cd66ecb90',NULL,'Jean Hartmann','fstehr@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','cx2TIgJojA','HPNIONpYXR','2026-01-08 19:01:14','2026-01-08 19:01:14','X52XMZxxhE','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-026f-70b6-a964-13a5ec76be6a',NULL,'Johnson Rippin','qkutch@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','sPjRwSJcjn','zZm2jn0Xgt','2026-01-08 19:01:14','2026-01-08 19:01:14','xA9R0lqB6P','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0277-7080-aefa-a72d54c2caea',NULL,'Antonina Quigley','stiedemann.conner@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','kE6EokwRIk','PbKCzpoWFh','2026-01-08 19:01:14','2026-01-08 19:01:14','yGl9F9kLay','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0283-71e1-84cc-0147ec7137b3',NULL,'Vern Johnson','tremblay.kaela@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','sArclQUkI2','8AtFDdC5Lu','2026-01-08 19:01:14','2026-01-08 19:01:14','xSYIEtLhUD','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-028e-71d9-82b4-e566803a38b8',NULL,'Terry Bradtke','dale93@example.net',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','L11VBNLaaB','DuS2KB69la','2026-01-08 19:01:14','2026-01-08 19:01:14','gBkUwwrqA8','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0299-734a-a187-2da6ba253e64',NULL,'Dr. Deven Casper I','skiles.lucas@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','p8fVT9cKlu','VwyKrNnwI3','2026-01-08 19:01:14','2026-01-08 19:01:14','8yOa9mvJLH','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02a4-73ff-a94a-89d975377a8e',NULL,'Dell Mitchell','cummerata.brandyn@example.net',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','jOEjjh7a8t','soyp2denTh','2026-01-08 19:01:14','2026-01-08 19:01:14','HFO2aY8ogW','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02af-7178-bfb6-67f7805178df',NULL,'Eric Bosco V','vance.haag@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','HbdY5cDKJt','xdSvu8Xar5','2026-01-08 19:01:14','2026-01-08 19:01:14','ILO2PSPpK2','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02ba-72f1-9b26-adbc657bfeaa',NULL,'Ms. Hosea Fay','summer.fadel@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','8AUyosGSOz','SnB5MAPcNA','2026-01-08 19:01:14','2026-01-08 19:01:14','rNxlvA9rw8','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02c6-7219-a23a-0da614bc954c',NULL,'Braulio White','jessy.lueilwitz@example.net',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','9cGL5dt7GX','mp2ZE63uBI','2026-01-08 19:01:14','2026-01-08 19:01:14','QPLbbnLKbD','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02d0-71ff-9be7-d0c5c97c19b2',NULL,'Delaney Runte','viva.farrell@example.net',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','1r7Q2QUhwM','lXbXcTcImf','2026-01-08 19:01:14','2026-01-08 19:01:14','trYT5wh9bS','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02dc-72fd-a70d-05219252d8d6',NULL,'Ms. Myriam Muller V','strosin.boyd@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','64eD5PKYHY','CLvL0UzVhu','2026-01-08 19:01:14','2026-01-08 19:01:14','2v6Sxg66DM','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02e7-703f-b28e-6f799a38643e',NULL,'Augustine Yost PhD','kilback.jackeline@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','5XIH0E6w0R','16NM2kq86x','2026-01-08 19:01:14','2026-01-08 19:01:14','JxdCxOooZF','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02f2-703d-93c8-20a5b73e0387',NULL,'Prof. Eliza Oberbrunner','tdenesik@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','yB3TYOd6Me','2PsTyARRrZ','2026-01-08 19:01:14','2026-01-08 19:01:14','UuYWRftklJ','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-02fe-72ac-8877-e9e9ac68093d',NULL,'Waino Olson DDS','winnifred48@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','pmEf340Jj0','4CBoBSgR9w','2026-01-08 19:01:14','2026-01-08 19:01:14','wWv9Ei2sBt','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0308-7075-9da0-1155e0a49c91',NULL,'Miss Aiyana Lemke III','keichmann@example.com',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','GXEjItTC1L','ul2iL5t5JM','2026-01-08 19:01:14','2026-01-08 19:01:14','8Pgf07Ul1M','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-0315-715f-a655-c446e2540a8b',NULL,'Modesto Davis','syble13@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','g6r2ROaa0Y','YuavH9r2r6','2026-01-08 19:01:14','2026-01-08 19:01:14','htQHOM63QM','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('019b9efc-031e-7148-b43f-185bc1a25aaa',NULL,'Delia Beer V','ukovacek@example.org',NULL,'019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'employee',NULL,'$2y$12$jZaPsaPRqnT3QbcpnB9cZ.V7wKgApVtz0tWNKzbFrwRC9XyQidAhK','S8zlFZqLx2','sXuod5zHru','2026-01-08 19:01:14','2026-01-08 19:01:14','0qc2g4TRSf','2026-01-08 19:01:14','2026-01-08 19:01:14'),
('0298a060-0ffd-3ed7-8de3-f67c9b73387b',NULL,'Emeka Nwankwo','emeka.nwankwo.51@sweettooth.com','EMP-PHC002-0051','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,4,NULL,'+234-800-495-5186','322 Nikko Shoal, Port Harcourt, Rivers State, Nigeria','1992-09-15','male','Nigerian','Hauwa Johnson','+234-802-653-4184','2025-08-12 23:00:00','active',NULL,NULL,'afternoon',299694.55,NULL,'TIN-91963023','5843239406',NULL,NULL,'2025-09-14',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('0450a605-3fe9-3544-bd2b-11de6dffdcb5',NULL,'Kemi Mohammed','kemi.mohammed.14@sweettooth.com','EMP-ABJ004-0014','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,7,NULL,'+234-901-340-2780','67671 Sofia Mountain, Abuja, FCT State, Nigeria','1996-09-17','female','Nigerian','Emeka Mohammed','+234-811-748-9580','2025-06-15 23:00:00','active',NULL,NULL,'flexible',202378.35,NULL,'TIN-10381317','0545402131',NULL,NULL,'2025-11-18',4.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('04603b59-6357-3210-9bdf-96735900ca08',NULL,'Blessing Johnson','blessing.johnson.103@sweettooth.com','EMP-ENU005-0103','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,5,NULL,'+234-813-185-6929','3718 Dolly Green Suite 684, Enugu, Enugu State, Nigeria','1999-12-25','female','Nigerian','Tunde Eze','+234-831-338-3118','2025-03-15 23:00:00','active',NULL,NULL,'flexible',149230.19,NULL,'TIN-54653943','8733269806',NULL,NULL,'2025-07-14',3.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('0560a0c9-7c0e-3657-9a3f-18babef41281',NULL,'Yusuf Mohammed','yusuf.mohammed.70@sweettooth.com','EMP-LAG003-0070','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,5,NULL,'+234-805-208-6843','6759 Eda Prairie Suite 189, Lagos, Lagos State, Nigeria','1988-05-15','male','Nigerian','Chioma Okoro','+234-890-307-7740','2023-12-19 23:00:00','active',NULL,NULL,'afternoon',136896.08,NULL,'TIN-54111530','2066659232',NULL,NULL,'2025-09-17',4.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('09c44002-b9bb-3b8a-8ece-4dd0f567dbdc','019b9efb-b43d-73c7-95bd-0687cf05c811','Folake Okafor','folake.okafor.27@sweettooth.com','EMP-CAL001-0027','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,1,NULL,'+234-819-219-1711','315 Burley Rue, Calabar, Cross River State, Nigeria','1991-01-01','female','Nigerian','Ibrahim Mohammed','+234-891-140-1100','2023-09-12 23:00:00','active',NULL,NULL,'rotating',347601.81,NULL,'TIN-15791998','1158423635','Shellfish',NULL,'2025-07-23',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:52:13'),
('0b40dd6e-3927-347e-95ff-979c7309fbdb',NULL,'Oluwaseun Okafor','oluwaseun.okafor.19@sweettooth.com','EMP-ABJ004-0019','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,6,NULL,'+234-836-255-8669','6816 Claire Meadow Apt. 391, Abuja, FCT State, Nigeria','1986-02-22','male','Nigerian','Folake Mohammed','+234-813-677-5192','2024-11-23 23:00:00','active',NULL,NULL,'flexible',223018.90,NULL,'TIN-38801553','7721346567',NULL,NULL,'2025-11-18',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('0c0026ee-5d98-3062-9d4f-8555c194e9fe',NULL,'Chukwuemeka Okoro','chukwuemeka.okoro.77@sweettooth.com','EMP-ABJ004-0077','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,1,NULL,'+234-850-910-1566','756 Karen Dam Apt. 198, Abuja, FCT State, Nigeria','1998-07-16','male','Nigerian','Amina Bello','+234-836-650-6711','2024-03-10 23:00:00','active',NULL,NULL,'afternoon',234433.71,NULL,'TIN-31945418','1269800802','Lactose',NULL,'2025-07-24',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('120e5c95-101e-308d-bee0-8ecee40b50b8',NULL,'Abubakar Adebayo','abubakar.adebayo.15@sweettooth.com','EMP-ENU005-0015','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,7,NULL,'+234-846-912-7758','92395 Sigrid Village, Enugu, Enugu State, Nigeria','1983-05-13','male','Nigerian','Kemi Mohammed','+234-811-749-3922','2023-12-22 23:00:00','active',NULL,NULL,'flexible',311536.17,NULL,'TIN-80559546','9419089445',NULL,NULL,'2025-07-24',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('12a53718-25a5-3e22-85fb-a113ace1609b',NULL,'Kemi Mohammed','kemi.mohammed.75@sweettooth.com','EMP-ABJ004-0075','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,1,NULL,'+234-828-377-8330','807 Berniece Coves, Abuja, FCT State, Nigeria','1995-09-14','female','Nigerian','Ibrahim Williams','+234-904-435-5927','2025-01-15 23:00:00','active',NULL,NULL,'flexible',262272.52,NULL,'TIN-16635159','4507666233',NULL,NULL,'2025-10-19',4.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('16627d87-bc1c-3ac4-9c4a-8fdb276a4625',NULL,'Amina Chukwu','amina.chukwu.60@sweettooth.com','EMP-LAG003-0060','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,1,NULL,'+234-821-994-7414','636 Cummings Station, Lagos, Lagos State, Nigeria','1985-03-06','female','Nigerian','Kunle Adebayo','+234-841-908-1789','2023-10-10 23:00:00','active',NULL,NULL,'morning',346433.82,NULL,'TIN-22607415','0578535045',NULL,NULL,'2026-01-06',4.4,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('1899ff6b-4423-3227-a81f-ab5ae1d322af',NULL,'Abubakar Eze','abubakar.eze.87@sweettooth.com','EMP-ABJ004-0087','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,5,NULL,'+234-807-799-4697','778 Kertzmann Mews, Abuja, FCT State, Nigeria','1995-05-09','male','Nigerian','Ada Okoro','+234-827-403-6983','2025-07-26 23:00:00','active',NULL,NULL,'morning',346089.73,NULL,'TIN-76393846','9279030718',NULL,NULL,'2025-10-01',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('1ab23065-c6bc-3a74-bd45-163dbcaac1d7','019b9efb-b43d-73c7-95bd-0687cf05c811','Fatima Adebayo','fatima.adebayo.36@sweettooth.com','EMP-CAL001-0036','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,4,NULL,'+234-901-849-5921','354 Wuckert Crest Suite 594, Calabar, Cross River State, Nigeria','1983-04-28','female','Nigerian','Kunle Adebayo','+234-871-375-5754','2025-06-23 23:00:00','active',NULL,NULL,'rotating',100394.79,NULL,'TIN-83644632','3690102791',NULL,NULL,'2025-10-17',4.1,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 20:21:28'),
('1acb22d8-42bf-3399-9db8-516f483beb2c',NULL,'Oluwaseun Aliyu','oluwaseun.aliyu.80@sweettooth.com','EMP-ABJ004-0080','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,2,NULL,'+234-812-603-5632','40052 Baumbach Port Apt. 693, Abuja, FCT State, Nigeria','1987-07-26','male','Nigerian','Kemi Aliyu','+234-897-112-2067','2024-04-01 23:00:00','active',NULL,NULL,'flexible',108877.02,NULL,'TIN-27106639','3881629698',NULL,NULL,'2025-08-05',3.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('1b2d809b-867d-36ea-b1f0-a02c46b8025e',NULL,'Ada Williams','ada.williams.68@sweettooth.com','EMP-LAG003-0068','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,4,NULL,'+234-813-770-5860','8457 Wyatt Mountains Apt. 195, Lagos, Lagos State, Nigeria','1987-03-02','female','Nigerian','Chukwuemeka Aliyu','+234-830-126-2563','2025-02-24 23:00:00','active',NULL,NULL,'flexible',116159.67,NULL,'TIN-61468446','8524041053',NULL,NULL,'2026-01-06',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('203caae2-c952-3596-998d-27d2f4943efa',NULL,'Ngozi Eze','ngozi.eze.79@sweettooth.com','EMP-ABJ004-0079','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,2,NULL,'+234-849-435-2720','87193 Kyla Trail Suite 606, Abuja, FCT State, Nigeria','2000-10-29','female','Nigerian','Chukwuemeka Eze','+234-879-305-9735','2024-06-21 23:00:00','active',NULL,NULL,'flexible',336549.22,NULL,'TIN-51329493','3944794572',NULL,NULL,'2026-01-08',3.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('231ab741-da89-354e-b734-a1e2fca48abe',NULL,'Folake Okafor','folake.okafor.24@sweettooth.com','EMP-ABJ004-0024','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,2,NULL,'+234-860-900-5488','6840 Nader Haven Suite 896, Abuja, FCT State, Nigeria','2001-05-27','female','Nigerian','Chukwuemeka Adebayo','+234-898-411-4383','2023-10-17 23:00:00','active',NULL,NULL,'afternoon',327067.23,NULL,'TIN-62987734','8954999016',NULL,NULL,'2025-11-13',3.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('23c2b7bc-3e80-38fa-be51-a9a77b74ce50',NULL,'Chioma Adebayo','chioma.adebayo.13@sweettooth.com','EMP-LAG003-0013','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,7,NULL,'+234-836-851-2026','49446 Huels Mountain, Lagos, Lagos State, Nigeria','1997-07-20','female','Nigerian','Abubakar Adebayo','+234-881-881-4030','2025-08-06 23:00:00','active',NULL,NULL,'afternoon',244682.04,NULL,'TIN-24635908','1427195212',NULL,NULL,'2025-10-09',4.3,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('245e4654-6381-3e83-9191-41fb4725e0b2',NULL,'Emeka Adebayo','emeka.adebayo.97@sweettooth.com','EMP-ENU005-0097','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,4,NULL,'+234-865-594-3100','999 Gusikowski Coves, Enugu, Enugu State, Nigeria','1981-09-12','male','Nigerian','Amina Johnson','+234-823-406-5995','2023-06-12 23:00:00','active',NULL,NULL,'rotating',223248.24,NULL,'TIN-78728312','9767848306',NULL,NULL,'2025-08-23',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('30bc858b-26b2-38c2-b3f3-3ea4855e208a',NULL,'Abubakar Adebayo','abubakar.adebayo.20@sweettooth.com','EMP-ENU005-0020','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,4,NULL,'+234-906-188-4861','44047 Tyrique Park, Enugu, Enugu State, Nigeria','1990-09-09','male','Nigerian','Ngozi Adebayo','+234-812-672-9650','2025-03-18 23:00:00','active',NULL,NULL,'morning',86536.56,NULL,'TIN-44728359','5555568354',NULL,NULL,'2025-08-28',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('30fa2b01-dcbb-30b6-ad5b-37570f9d818e',NULL,'Tunde Chukwu','tunde.chukwu.78@sweettooth.com','EMP-ABJ004-0078','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,2,NULL,'+234-865-235-7059','2496 O\'Connell Squares Apt. 392, Abuja, FCT State, Nigeria','1999-01-06','male','Nigerian','Hauwa Aliyu','+234-906-646-2689','2024-05-19 23:00:00','active',NULL,NULL,'morning',241008.14,NULL,'TIN-22945257','5996687711',NULL,NULL,'2025-11-22',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('323cf339-e303-3dc5-abbe-185a715abe35','019b9efb-b43d-73c7-95bd-0687cf05c811','Obinna Okafor','obinna.okafor.40@sweettooth.com','EMP-CAL001-0040','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,7,NULL,'+234-890-710-9966','4756 Ransom Spurs, Calabar, Cross River State, Nigeria','1996-06-04','male','Nigerian','Amina Okoro','+234-850-670-9648','2025-09-27 23:00:00','active',NULL,NULL,'flexible',337423.01,NULL,'TIN-86882835','6661366685',NULL,NULL,'2025-09-10',4.2,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-09 02:45:58'),
('338cc47c-71ba-3b4d-a955-9389ad6c093e',NULL,'Chigozie Nwankwo','chigozie.nwankwo.28@sweettooth.com','EMP-CAL001-0028','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,1,NULL,'+234-856-780-8258','25342 Rippin Spurs Apt. 613, Calabar, Cross River State, Nigeria','1982-04-01','male','Nigerian','Folake Nwankwo','+234-821-499-3546','2023-04-24 23:00:00','active',NULL,NULL,'afternoon',231182.95,NULL,'TIN-53191629','4502745963',NULL,NULL,'2025-11-23',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('3f777f4f-7363-3c4b-87db-7f4827a89a00',NULL,'Tunde Johnson','tunde.johnson.34@sweettooth.com','EMP-CAL001-0034','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,4,NULL,'+234-807-136-7882','194 Arielle Parks Suite 953, Calabar, Cross River State, Nigeria','2002-11-09','male','Nigerian','Fatima Ogunleye','+234-827-202-5530','2023-07-03 23:00:00','active',NULL,NULL,'rotating',127162.54,NULL,'TIN-34154155','4994412459',NULL,NULL,'2025-07-25',4.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('3f9601f1-eed8-3ec6-8a3f-7a3663442d88',NULL,'Emeka Bello','emeka.bello.98@sweettooth.com','EMP-ENU005-0098','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,4,NULL,'+234-887-809-5554','23494 Block Plain, Enugu, Enugu State, Nigeria','1981-08-13','male','Nigerian','Amina Eze','+234-825-639-9455','2023-06-17 23:00:00','active',NULL,NULL,'afternoon',140432.23,NULL,'TIN-62268746','3748508683',NULL,NULL,'2025-09-16',4.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('41c78a2e-7985-3ce1-8a2b-7dc3b525fd32',NULL,'Oluwaseun Mohammed','oluwaseun.mohammed.16@sweettooth.com','EMP-CAL001-0016','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,4,NULL,'+234-909-325-6237','133 Kozey Route Apt. 736, Calabar, Cross River State, Nigeria','2000-12-15','male','Nigerian','Kemi Adebayo','+234-896-728-1978','2025-06-16 23:00:00','active',NULL,NULL,'flexible',344714.96,NULL,'TIN-65309598','5211977885',NULL,NULL,'2025-11-09',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('4440aa97-63d6-3eca-95dc-7829201924af',NULL,'Chioma Aliyu','chioma.aliyu.55@sweettooth.com','EMP-PHC002-0055','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,5,NULL,'+234-802-449-2474','38916 Dessie Plains Apt. 987, Port Harcourt, Rivers State, Nigeria','1992-05-24','female','Nigerian','Abubakar Okafor','+234-876-360-3333','2023-10-07 23:00:00','active',NULL,NULL,'rotating',264674.07,NULL,'TIN-91041184','2045685520',NULL,NULL,'2025-09-10',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('4549ac22-e4d7-33e2-a93b-5fc0f0f58884',NULL,'Oluwaseun Adebayo','oluwaseun.adebayo.18@sweettooth.com','EMP-LAG003-0018','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,4,NULL,'+234-853-282-6933','72276 Cole Parks Apt. 598, Lagos, Lagos State, Nigeria','1992-07-22','male','Nigerian','Chioma Mohammed','+234-841-877-8500','2025-04-11 23:00:00','active',NULL,NULL,'rotating',178767.77,NULL,'TIN-57258445','7802851156',NULL,NULL,'2025-10-01',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('476119ec-b194-38ee-a339-6bd87cf75c64',NULL,'Hauwa Adebayo','hauwa.adebayo.71@sweettooth.com','EMP-LAG003-0071','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,5,NULL,'+234-900-889-8771','4660 Heathcote Place Apt. 039, Lagos, Lagos State, Nigeria','1990-11-18','female','Nigerian','Obinna Ogunleye','+234-839-770-3679','2024-10-08 23:00:00','active',NULL,NULL,'afternoon',291921.28,NULL,'TIN-12391605','6223295443',NULL,NULL,'2025-08-05',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('523bbd41-7705-3b7b-a591-b09ca92fbc68',NULL,'Abubakar Aliyu','abubakar.aliyu.26@sweettooth.com','EMP-CAL001-0026','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,1,NULL,'+234-814-318-9694','77429 Torp Views Apt. 164, Calabar, Cross River State, Nigeria','1983-02-25','male','Nigerian','Kemi Okafor','+234-861-790-7997','2023-03-21 23:00:00','active',NULL,NULL,'morning',242697.73,NULL,'TIN-40301481','2442294948',NULL,NULL,'2025-12-16',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('52b2a8c7-f232-34a0-ad04-de44a2f96779',NULL,'Abubakar Eze','abubakar.eze.86@sweettooth.com','EMP-ABJ004-0086','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,5,NULL,'+234-894-985-4910','6045 Roosevelt View Suite 087, Abuja, FCT State, Nigeria','1987-08-14','male','Nigerian','Ada Nwankwo','+234-884-742-6110','2023-08-07 23:00:00','active',NULL,NULL,'afternoon',131891.41,NULL,'TIN-66182957','0610882768',NULL,NULL,'2025-08-10',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('5336bb2d-8b6f-3280-b525-a5fea9c841dc',NULL,'Kunle Aliyu','kunle.aliyu.38@sweettooth.com','EMP-CAL001-0038','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,5,NULL,'+234-862-975-3744','83817 Kayla Road Apt. 670, Calabar, Cross River State, Nigeria','1988-11-19','male','Nigerian','Fatima Eze','+234-809-871-6305','2023-09-14 23:00:00','active',NULL,NULL,'morning',188094.87,NULL,'TIN-49093604','9394681894',NULL,NULL,'2025-11-24',4.1,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('567493b1-36e9-3e5f-a32b-f445346a45bb',NULL,'Ngozi Nwankwo','ngozi.nwankwo.94@sweettooth.com','EMP-ENU005-0094','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,2,NULL,'+234-813-362-9198','65812 Stehr Centers, Enugu, Enugu State, Nigeria','2000-12-06','female','Nigerian','Emeka Johnson','+234-899-507-7316','2025-01-12 23:00:00','active',NULL,NULL,'rotating',101690.75,NULL,'TIN-29359138','5550656399',NULL,NULL,'2025-09-24',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('588009a8-0825-3a9a-82d4-20e964e49310',NULL,'Kemi Adebayo','kemi.adebayo.47@sweettooth.com','EMP-PHC002-0047','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,2,NULL,'+234-826-421-1483','2165 Jerde Village Apt. 949, Port Harcourt, Rivers State, Nigeria','1996-12-01','female','Nigerian','Chigozie Okafor','+234-814-960-1710','2025-11-06 23:00:00','active',NULL,NULL,'rotating',133912.44,NULL,'TIN-50640384','3427297571',NULL,NULL,'2025-10-17',3.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('58acf95b-247d-3bc3-bfca-eb1c97651f23',NULL,'Chukwuemeka Adebayo','chukwuemeka.adebayo.58@sweettooth.com','EMP-LAG003-0058','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,1,NULL,'+234-891-990-5369','19963 Runolfsson Street, Lagos, Lagos State, Nigeria','1988-12-25','male','Nigerian','Nneka Williams','+234-841-172-9987','2025-10-01 23:00:00','active',NULL,NULL,'afternoon',146714.33,NULL,'TIN-64925376','4109500114',NULL,NULL,'2025-11-27',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('58d67707-7a7b-3160-8f2f-52aa848e94cf','019b9efb-b43d-73c7-95bd-0687cf05c811','Obinna Bello','obinna.bello.39@sweettooth.com','EMP-CAL001-0039','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,5,NULL,'+234-895-221-8228','2350 Florencio Center, Calabar, Cross River State, Nigeria','1993-06-17','male','Nigerian','Kemi Johnson','+234-850-754-6584','2023-10-29 23:00:00','active',NULL,NULL,'flexible',82377.07,NULL,'TIN-80583476','0624259121',NULL,NULL,'2025-12-04',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-09 06:41:52'),
('5a9d4741-0f95-3901-866a-a40d3752148e',NULL,'Ada Williams','ada.williams.91@sweettooth.com','EMP-ENU005-0091','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,1,NULL,'+234-890-652-6539','78579 Mario Creek, Enugu, Enugu State, Nigeria','1999-08-19','female','Nigerian','Oluwaseun Nwankwo','+234-876-243-6954','2023-09-05 23:00:00','active',NULL,NULL,'afternoon',157003.23,NULL,'TIN-98054040','4022806594',NULL,NULL,'2025-12-20',3.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('5f353b0a-46aa-301c-a9aa-d1a0ff9e650d',NULL,'Ngozi Mohammed','ngozi.mohammed.9@sweettooth.com','EMP-ABJ004-0009','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,1,NULL,'+234-852-517-3009','2000 Gerardo Skyway Apt. 523, Abuja, FCT State, Nigeria','1986-08-13','female','Nigerian','Oluwaseun Mohammed','+234-847-640-9305','2023-05-15 23:00:00','active',NULL,NULL,'morning',297098.89,NULL,'TIN-24699420','0777012825',NULL,NULL,'2025-12-03',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('642757ca-f66d-3d7a-be81-933e986372fe',NULL,'Abubakar Mohammed','abubakar.mohammed.61@sweettooth.com','EMP-LAG003-0061','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,1,NULL,'+234-803-768-7227','8100 Hettinger Fork Apt. 322, Lagos, Lagos State, Nigeria','2003-05-31','male','Nigerian','Folake Eze','+234-835-384-1664','2024-05-08 23:00:00','active',NULL,NULL,'morning',252235.66,NULL,'TIN-72419580','3500450718',NULL,NULL,'2025-09-17',3.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('661e6090-b9f3-3399-89b1-5acda4b94178',NULL,'Kemi Okafor','kemi.okafor.25@sweettooth.com','EMP-ENU005-0025','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,1,NULL,'+234-863-995-9542','501 Treutel Prairie, Enugu, Enugu State, Nigeria','2002-07-26','female','Nigerian','Tunde Adebayo','+234-909-540-3917','2024-03-29 23:00:00','active',NULL,NULL,'afternoon',143536.25,NULL,'TIN-44381422','7509881693',NULL,NULL,'2025-08-13',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('666bf6ad-0541-33e5-a2b1-ff634b92e59f',NULL,'Tunde Ogunleye','tunde.ogunleye.89@sweettooth.com','EMP-ABJ004-0089','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,7,NULL,'+234-838-232-9578','62481 Bode Drive Suite 293, Abuja, FCT State, Nigeria','1993-11-09','male','Nigerian','Ada Mohammed','+234-883-845-3098','2023-09-27 23:00:00','active',NULL,NULL,'flexible',309739.37,NULL,'TIN-75760111','8729650819','Peanuts',NULL,'2025-09-28',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('67d7ea47-5d83-38df-af87-0c1645bce6b7',NULL,'Chioma Adebayo','chioma.adebayo.22@sweettooth.com','EMP-PHC002-0022','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,1,NULL,'+234-884-518-8045','9041 Kirsten Greens, Port Harcourt, Rivers State, Nigeria','1998-12-03','female','Nigerian','Chukwuemeka Mohammed','+234-828-147-8354','2023-05-16 23:00:00','active',NULL,NULL,'afternoon',282073.91,NULL,'TIN-78476330','7760214301',NULL,NULL,'2025-09-06',4.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('69b0da84-786d-31d2-9d9b-de10ca3a6a6b',NULL,'Chukwuemeka Adebayo','chukwuemeka.adebayo.96@sweettooth.com','EMP-ENU005-0096','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,2,NULL,'+234-836-866-4733','284 Rowe Brook Apt. 010, Enugu, Enugu State, Nigeria','2001-07-08','male','Nigerian','Ngozi Ogunleye','+234-878-531-9035','2024-10-04 23:00:00','active',NULL,NULL,'flexible',125636.46,NULL,'TIN-56261313','8009906211',NULL,NULL,'2025-12-15',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('69e43641-2eaf-37f0-8e0d-38151c1ba81b',NULL,'Amina Ogunleye','amina.ogunleye.73@sweettooth.com','EMP-LAG003-0073','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,7,NULL,'+234-805-954-6902','252 Heaney Grove Suite 691, Lagos, Lagos State, Nigeria','1988-06-02','female','Nigerian','Emeka Adebayo','+234-818-682-8959','2023-08-01 23:00:00','active',NULL,NULL,'rotating',225093.07,NULL,'TIN-89976397','0621216879',NULL,NULL,'2025-07-19',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('6a40afb0-4758-39e4-8387-3504ffc3c0aa',NULL,'Oluwaseun Bello','oluwaseun.bello.31@sweettooth.com','EMP-CAL001-0031','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,2,NULL,'+234-886-529-9708','79856 Harber Port Apt. 385, Calabar, Cross River State, Nigeria','1988-06-24','male','Nigerian','Folake Okafor','+234-810-620-9126','2023-09-23 23:00:00','active',NULL,NULL,'rotating',126347.99,NULL,'TIN-99940638','6196091371',NULL,NULL,'2025-10-30',3.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('6a5368c9-bca2-3a8b-821b-467e48b93108',NULL,'Oluwaseun Mohammed','oluwaseun.mohammed.17@sweettooth.com','EMP-PHC002-0017','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,4,NULL,'+234-819-698-1593','583 Lynch Street Apt. 017, Port Harcourt, Rivers State, Nigeria','1998-04-04','male','Nigerian','Folake Adebayo','+234-862-895-3454','2023-02-21 23:00:00','active',NULL,NULL,'afternoon',259500.72,NULL,'TIN-48202311','0085344831',NULL,NULL,'2025-09-07',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('73947e86-2211-3c4e-ac4c-6de0ef92c0ca',NULL,'Ada Okoro','ada.okoro.95@sweettooth.com','EMP-ENU005-0095','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,2,NULL,'+234-853-223-3416','351 Stehr Pass, Enugu, Enugu State, Nigeria','1999-08-07','female','Nigerian','Chukwuemeka Okafor','+234-844-497-1218','2024-07-21 23:00:00','active',NULL,NULL,'morning',251844.59,NULL,'TIN-53425492','1947603257','Shellfish',NULL,'2025-08-23',4.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('73a0b23d-113b-3867-9055-1aed32d0ca32',NULL,'Ada Mohammed','ada.mohammed.49@sweettooth.com','EMP-PHC002-0049','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,4,NULL,'+234-884-327-4309','23881 Kunde Crossroad, Port Harcourt, Rivers State, Nigeria','1990-01-23','female','Nigerian','Emeka Chukwu','+234-853-378-9873','2025-08-02 23:00:00','active',NULL,NULL,'afternoon',187920.15,NULL,'TIN-53976458','9272666453',NULL,NULL,'2025-07-26',4.1,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('756b29a0-991f-3bea-a033-3f798057d465',NULL,'Chioma Mohammed','chioma.mohammed.5@sweettooth.com','EMP-ENU005-0005','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,1,NULL,'+234-845-816-4714','6120 Mertz Ramp Suite 545, Enugu, Enugu State, Nigeria','1989-02-27','female','Nigerian','Oluwaseun Adebayo','+234-842-921-7559','2024-02-24 23:00:00','active',NULL,NULL,'afternoon',82287.72,NULL,'TIN-44330507','7942352216',NULL,NULL,'2025-09-02',3.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('76bd9ec7-a2d9-3d19-8fcb-534ed520dd92',NULL,'Chigozie Ogunleye','chigozie.ogunleye.64@sweettooth.com','EMP-LAG003-0064','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,2,NULL,'+234-864-267-3672','6876 Corwin Locks Apt. 179, Lagos, Lagos State, Nigeria','1998-05-28','male','Nigerian','Amina Adebayo','+234-899-453-5458','2025-01-06 23:00:00','active',NULL,NULL,'rotating',311834.71,NULL,'TIN-36382682','1540143208',NULL,NULL,'2025-09-27',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('77a6b52b-52fd-3dae-898c-2b8683f2ac68',NULL,'Chigozie Nwankwo','chigozie.nwankwo.84@sweettooth.com','EMP-ABJ004-0084','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,4,NULL,'+234-837-483-7694','429 Schiller Squares Suite 996, Abuja, FCT State, Nigeria','1988-11-14','male','Nigerian','Chioma Williams','+234-822-271-1390','2023-11-18 23:00:00','active',NULL,NULL,'morning',176290.60,NULL,'TIN-58274790','3736891532','Peanuts',NULL,'2025-10-21',3.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('799aba19-8edd-359b-87cd-101ee3ac292e',NULL,'Ada Williams','ada.williams.82@sweettooth.com','EMP-ABJ004-0082','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,4,NULL,'+234-886-347-4291','7594 Prosacco Ways, Abuja, FCT State, Nigeria','1981-01-25','female','Nigerian','Emeka Bello','+234-877-847-4441','2025-10-18 23:00:00','active',NULL,NULL,'morning',141839.87,NULL,'TIN-56860653','4634711486',NULL,NULL,'2025-10-29',4.2,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('7dab76d9-8a4e-33cd-92cf-dcca2050f91d',NULL,'Kemi Johnson','kemi.johnson.35@sweettooth.com','EMP-CAL001-0035','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,4,NULL,'+234-895-419-1851','971 Jevon Ford, Calabar, Cross River State, Nigeria','2001-11-16','female','Nigerian','Chukwuemeka Johnson','+234-819-444-5045','2025-03-29 23:00:00','active',NULL,NULL,'morning',242599.19,NULL,'TIN-65972930','6451085877',NULL,NULL,'2025-10-15',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('7f2442ff-2128-4cfa-b8aa-ab8e5a8514cb','019b9efb-b43d-73c7-95bd-0687cf05c811','Chioma Adeyemi','accounting.manager@sweettooth.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,10,NULL,'08145678901',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$cj/J2QFymX4/f8HhG0191eWUu.3Kk5Ft0Bk4HK1IORjZ1nUblKXou',NULL,NULL,NULL,'2026-01-09 03:10:38',NULL,'2026-01-09 03:10:38','2026-01-09 03:30:57'),
('8063f4b9-e97c-38ba-b458-d91cd875ed07',NULL,'Kemi Williams','kemi.williams.99@sweettooth.com','EMP-ENU005-0099','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,4,NULL,'+234-841-575-4313','10282 Anderson Views, Enugu, Enugu State, Nigeria','2001-06-17','female','Nigerian','Emeka Bello','+234-842-622-7163','2023-09-04 23:00:00','active',NULL,NULL,'rotating',208555.61,NULL,'TIN-74072192','9627057065',NULL,NULL,'2025-07-11',3.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('82f6ea3b-8ea2-3d1a-b223-1d716369b0e4',NULL,'Folake Aliyu','folake.aliyu.41@sweettooth.com','EMP-CAL001-0041','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,7,NULL,'+234-830-322-9420','2650 Okuneva Freeway Apt. 519, Calabar, Cross River State, Nigeria','1995-06-17','female','Nigerian','Ibrahim Johnson','+234-818-191-3857','2025-04-03 23:00:00','active',NULL,NULL,'flexible',184963.60,NULL,'TIN-34877708','9957190951',NULL,NULL,'2025-11-11',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('8894c20d-36fe-336c-8c8a-83fdc2cae96d',NULL,'Folake Mohammed','folake.mohammed.8@sweettooth.com','EMP-LAG003-0008','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,1,NULL,'+234-848-395-3889','68946 Chaya Garden Suite 880, Lagos, Lagos State, Nigeria','1997-10-27','female','Nigerian','Abubakar Adebayo','+234-849-421-6626','2024-04-15 23:00:00','active',NULL,NULL,'rotating',125659.33,NULL,'TIN-66650150','1941157496',NULL,NULL,'2025-09-15',4.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('89bfc778-8f23-4a5e-bca8-7784b5bd1190',NULL,'Amara Nwosu','production.helper1@sweettooth.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,10,NULL,'08187654321',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$ouDp5xysm6J8EaWHNsIuu.SwkGPmxh.KCKQ7RTjzeXkdnom87metO',NULL,NULL,NULL,'2026-01-09 03:10:42',NULL,'2026-01-09 03:10:42','2026-01-09 03:10:42'),
('89eaf009-d299-338c-bc92-369a8f7bd948',NULL,'Tunde Nwankwo','tunde.nwankwo.85@sweettooth.com','EMP-ABJ004-0085','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,5,NULL,'+234-812-124-5723','274 Reva Stream, Abuja, FCT State, Nigeria','1985-01-22','male','Nigerian','Amina Adebayo','+234-823-706-1794','2024-06-12 23:00:00','active',NULL,NULL,'rotating',143435.31,NULL,'TIN-39201210','7171139661',NULL,NULL,'2025-10-29',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('8de35c4f-7de8-3079-b8cd-afda1bdb21c9',NULL,'Amina Okafor','amina.okafor.21@sweettooth.com','EMP-CAL001-0021','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,1,NULL,'+234-837-531-8799','957 Mariela Road Apt. 360, Calabar, Cross River State, Nigeria','1989-11-28','female','Nigerian','Abubakar Okafor','+234-846-656-9989','2025-11-15 23:00:00','active',NULL,NULL,'afternoon',145510.22,NULL,'TIN-64580131','2456009871','Peanuts',NULL,'2025-08-31',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('8e056c0c-57d3-3224-86b6-a197c2b0522b',NULL,'Folake Williams','folake.williams.62@sweettooth.com','EMP-LAG003-0062','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,2,NULL,'+234-825-827-4609','4612 Mina Road, Lagos, Lagos State, Nigeria','1999-01-29','female','Nigerian','Kunle Johnson','+234-823-747-1641','2023-09-14 23:00:00','active',NULL,NULL,'rotating',281896.58,NULL,'TIN-47950738','9241834588',NULL,NULL,'2026-01-05',3.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('90824f40-840f-3036-afce-fd60e034e10b',NULL,'Blessing Okoro','blessing.okoro.65@sweettooth.com','EMP-LAG003-0065','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,4,NULL,'+234-848-770-2905','862 McClure Brook, Lagos, Lagos State, Nigeria','1986-04-12','female','Nigerian','Tunde Mohammed','+234-807-528-2746','2023-01-08 23:00:00','active',NULL,NULL,'rotating',89807.94,NULL,'TIN-35604332','6209429766',NULL,NULL,'2025-11-12',4.2,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('91e0199b-8062-31a6-9ea3-e0a6a3be37db',NULL,'Abubakar Eze','abubakar.eze.63@sweettooth.com','EMP-LAG003-0063','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,2,NULL,'+234-850-300-8404','153 O\'Reilly Streets Apt. 641, Lagos, Lagos State, Nigeria','1983-07-28','male','Nigerian','Hauwa Nwankwo','+234-892-491-6078','2025-08-29 23:00:00','active',NULL,NULL,'morning',297070.10,NULL,'TIN-97547286','4621643936',NULL,NULL,'2025-12-17',4.3,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('94f35001-ab9a-3948-83a6-bd0320b28032',NULL,'Kemi Eze','kemi.eze.59@sweettooth.com','EMP-LAG003-0059','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,1,NULL,'+234-893-670-1234','53373 Orland Wall, Lagos, Lagos State, Nigeria','1996-09-29','female','Nigerian','Chukwuemeka Ogunleye','+234-819-142-9535','2025-11-26 23:00:00','active',NULL,NULL,'rotating',345212.86,NULL,'TIN-81374657','8249859722',NULL,NULL,'2025-09-11',5.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('9faf1788-6107-332a-a966-d7eff85fc592',NULL,'Chukwuemeka Okafor','chukwuemeka.okafor.6@sweettooth.com','EMP-CAL001-0006','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,1,NULL,'+234-821-799-9206','564 Joel Forge Apt. 845, Calabar, Cross River State, Nigeria','1983-09-07','male','Nigerian','Amina Mohammed','+234-800-973-1083','2023-02-26 23:00:00','active',NULL,NULL,'rotating',327685.32,NULL,'TIN-63284698','1026213094',NULL,NULL,'2025-08-24',4.2,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('a4a7593d-b897-39d3-8576-152d22e928d7',NULL,'Ada Adebayo','ada.adebayo.29@sweettooth.com','EMP-CAL001-0029','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,1,NULL,'+234-851-377-2286','288 Bernhard Way, Calabar, Cross River State, Nigeria','1982-10-07','female','Nigerian','Yusuf Chukwu','+234-877-252-3196','2025-06-28 23:00:00','active',NULL,NULL,'rotating',202380.62,NULL,'TIN-43553062','3046935569',NULL,NULL,'2025-11-04',3.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('a7c465de-a86b-3dd0-b7b9-ce4820300b10',NULL,'Obinna Eze','obinna.eze.30@sweettooth.com','EMP-CAL001-0030','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,2,NULL,'+234-817-867-4786','2717 Powlowski Creek Apt. 093, Calabar, Cross River State, Nigeria','1990-06-11','male','Nigerian','Blessing Aliyu','+234-827-166-1855','2023-12-31 23:00:00','active',NULL,NULL,'afternoon',162106.62,NULL,'TIN-10287759','4924414642',NULL,NULL,'2025-11-16',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('a8585d83-81ef-4493-bfcb-e2ba257387b0',NULL,'Sarah Johnson','hr.manager@sweettooth.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,9,NULL,'08123456789',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$m8fvi6lp6pYhi8CRkI67euvnzyNl.UHLAb2uE198H1Yrzp6Q0HFay',NULL,NULL,NULL,'2026-01-09 03:10:34',NULL,'2026-01-09 03:10:34','2026-01-09 03:10:34'),
('aa15f570-9070-3f01-b277-c9af7a34f556',NULL,'Abubakar Okafor','abubakar.okafor.66@sweettooth.com','EMP-LAG003-0066','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,4,NULL,'+234-802-633-3759','2699 Ettie Junctions Suite 059, Lagos, Lagos State, Nigeria','2001-08-28','male','Nigerian','Ada Okoro','+234-826-269-1085','2023-12-30 23:00:00','active',NULL,NULL,'rotating',328108.86,NULL,'TIN-86914480','9869897559',NULL,NULL,'2025-10-22',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('aafb621b-b0ec-39ab-99b5-54648b460094',NULL,'Chukwuemeka Adebayo','chukwuemeka.adebayo.2@sweettooth.com','EMP-PHC002-0002','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,1,NULL,'+234-907-210-9215','41180 Johnston Track, Port Harcourt, Rivers State, Nigeria','1998-08-23','male','Nigerian','Chioma Okafor','+234-890-719-2032','2025-06-27 23:00:00','active',NULL,NULL,'flexible',129155.03,NULL,'TIN-87635869','6839558329',NULL,NULL,'2025-11-22',4.2,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('ab1eb589-71f8-34a3-86d9-77440bacfc6f',NULL,'Chioma Mohammed','chioma.mohammed.11@sweettooth.com','EMP-CAL001-0011','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,7,NULL,'+234-909-366-8379','93599 Barbara Stravenue, Calabar, Cross River State, Nigeria','2003-04-21','female','Nigerian','Chukwuemeka Adebayo','+234-809-232-1783','2025-05-23 23:00:00','active',NULL,NULL,'morning',187851.95,NULL,'TIN-71164777','5505419750',NULL,NULL,'2025-12-02',5.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('ac24d8ab-607a-3807-8062-cb41d964f9aa',NULL,'Kunle Chukwu','kunle.chukwu.46@sweettooth.com','EMP-PHC002-0046','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,2,NULL,'+234-847-105-3732','3070 Daniella Locks Apt. 725, Port Harcourt, Rivers State, Nigeria','1988-12-30','male','Nigerian','Kemi Nwankwo','+234-901-177-4180','2023-06-22 23:00:00','active',NULL,NULL,'rotating',116133.07,NULL,'TIN-23929005','0424707320','Shellfish',NULL,'2025-11-01',4.4,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('ae372a5c-7f11-3c77-925f-d0822ab0b72e',NULL,'Fatima Chukwu','fatima.chukwu.100@sweettooth.com','EMP-ENU005-0100','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,4,NULL,'+234-810-159-5472','3151 Eldridge Vista Suite 710, Enugu, Enugu State, Nigeria','1987-09-30','female','Nigerian','Oluwaseun Okoro','+234-849-992-3461','2023-09-30 23:00:00','active',NULL,NULL,'flexible',284257.03,NULL,'TIN-24308479','2864083507',NULL,NULL,'2025-07-29',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('b1e88d65-8a0f-3d24-aa60-32dd2847dc49',NULL,'Yusuf Ogunleye','yusuf.ogunleye.57@sweettooth.com','EMP-PHC002-0057','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,7,NULL,'+234-812-161-5133','9554 Clementine Rapids, Port Harcourt, Rivers State, Nigeria','1992-01-21','male','Nigerian','Amina Nwankwo','+234-867-523-9784','2023-01-28 23:00:00','active',NULL,NULL,'afternoon',297706.87,NULL,'TIN-77018393','6819655180',NULL,NULL,'2025-11-11',3.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('b39e07ec-1ca2-359e-98e9-7efb557e5e04',NULL,'Blessing Eze','blessing.eze.48@sweettooth.com','EMP-PHC002-0048','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,2,NULL,'+234-836-339-8664','3847 Wunsch Stream Suite 260, Port Harcourt, Rivers State, Nigeria','1995-05-18','female','Nigerian','Chukwuemeka Johnson','+234-883-424-7427','2024-05-01 23:00:00','active',NULL,NULL,'rotating',284338.70,NULL,'TIN-36646689','1710972779',NULL,NULL,'2025-09-29',4.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('b5025d53-64f2-4238-9531-f81cf0840f2c','019b9efb-b43d-73c7-95bd-0687cf05c811','Tunde Oluwaseun','accounting.clerk@sweettooth.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,10,NULL,'08176543210',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$J8EsobimWR7S82qdzbymk.D7YDHgbbOjGciJ4c3tasJj1KYqPyFHC',NULL,NULL,NULL,'2026-01-09 03:10:39',NULL,'2026-01-09 03:10:39','2026-01-09 03:27:50'),
('bb9b4f88-fd05-3a59-afc5-edf6346f5d4d',NULL,'Emeka Okoro','emeka.okoro.56@sweettooth.com','EMP-PHC002-0056','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,7,NULL,'+234-909-293-2598','24796 Magnolia Tunnel, Port Harcourt, Rivers State, Nigeria','1983-06-12','male','Nigerian','Blessing Aliyu','+234-866-658-1582','2023-02-05 23:00:00','active',NULL,NULL,'afternoon',81158.37,NULL,'TIN-20700331','9498154385',NULL,NULL,'2025-08-09',4.4,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('bda65430-525b-3952-8bba-2aba9c14a3cb',NULL,'Chukwuemeka Adebayo','chukwuemeka.adebayo.10@sweettooth.com','EMP-ENU005-0010','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,1,NULL,'+234-864-739-9308','79813 Angelo Island, Enugu, Enugu State, Nigeria','1991-08-23','male','Nigerian','Chioma Okafor','+234-845-351-2470','2025-05-01 23:00:00','active',NULL,NULL,'rotating',234194.64,NULL,'TIN-44056839','2378427531',NULL,NULL,'2025-12-27',4.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('bed3959d-f13c-367c-ac10-89422d423aef',NULL,'Kunle Johnson','kunle.johnson.45@sweettooth.com','EMP-PHC002-0045','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,1,NULL,'+234-902-498-8358','943 Cassandre Drive Apt. 729, Port Harcourt, Rivers State, Nigeria','2000-05-01','male','Nigerian','Nneka Mohammed','+234-854-291-7264','2023-12-26 23:00:00','active',NULL,NULL,'afternoon',97567.72,NULL,'TIN-68283152','1152421761','None',NULL,'2025-11-29',4.1,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('bfdf11ce-0d29-3cf2-8c32-fd77a1066f68',NULL,'Abubakar Okoro','abubakar.okoro.52@sweettooth.com','EMP-PHC002-0052','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,4,NULL,'+234-908-471-5995','588 Rosetta Mill, Port Harcourt, Rivers State, Nigeria','1995-08-08','male','Nigerian','Kemi Ogunleye','+234-871-134-3349','2023-01-25 23:00:00','active',NULL,NULL,'flexible',114739.66,NULL,'TIN-93640672','3565834560',NULL,NULL,'2025-11-14',3.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('c4f44c34-a2f5-3a3a-9216-6fb46eed0eea',NULL,'Ibrahim Mohammed','ibrahim.mohammed.92@sweettooth.com','EMP-ENU005-0092','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,1,NULL,'+234-901-652-5094','27267 Pauline Mountain, Enugu, Enugu State, Nigeria','1985-03-26','male','Nigerian','Fatima Chukwu','+234-856-446-4201','2023-01-13 23:00:00','active',NULL,NULL,'afternoon',220345.26,NULL,'TIN-53595308','9401793159',NULL,NULL,'2025-11-06',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('c5e6a70f-e1a7-3863-aa8d-a1887f256af2',NULL,'Ibrahim Williams','ibrahim.williams.69@sweettooth.com','EMP-LAG003-0069','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,5,NULL,'+234-873-432-8791','3015 Mertz Brook, Lagos, Lagos State, Nigeria','2002-03-30','male','Nigerian','Folake Chukwu','+234-869-521-2046','2024-06-01 23:00:00','active',NULL,NULL,'morning',205903.23,NULL,'TIN-14547726','7993938345',NULL,NULL,'2025-07-28',4.3,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('c6afe082-1744-3f07-9ea1-cbb17e8a9e05',NULL,'Yusuf Williams','yusuf.williams.93@sweettooth.com','EMP-ENU005-0093','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,1,NULL,'+234-827-367-4255','410 Ledner Lake, Enugu, Enugu State, Nigeria','2003-06-12','male','Nigerian','Hauwa Okoro','+234-854-207-9045','2024-04-11 23:00:00','active',NULL,NULL,'rotating',243321.43,NULL,'TIN-30221521','4483379596',NULL,NULL,'2025-11-28',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('c7cd7bf1-4b1a-3b58-b9e2-93ffd4a4feb1',NULL,'Chigozie Ogunleye','chigozie.ogunleye.101@sweettooth.com','EMP-ENU005-0101','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,5,NULL,'+234-804-216-3524','47706 Darion Mission Apt. 813, Enugu, Enugu State, Nigeria','1992-02-03','male','Nigerian','Hauwa Okafor','+234-836-674-1312','2023-11-29 23:00:00','active',NULL,NULL,'rotating',235262.79,NULL,'TIN-79675330','7178468510',NULL,NULL,'2025-12-29',5.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('c947ef5d-4b9b-3fab-850f-538c72c47dd1',NULL,'Chioma Okafor','chioma.okafor.7@sweettooth.com','EMP-PHC002-0007','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,1,NULL,'+234-823-105-4538','39125 Anderson Turnpike, Port Harcourt, Rivers State, Nigeria','1983-06-27','female','Nigerian','Emeka Adebayo','+234-850-592-1806','2024-10-30 23:00:00','active',NULL,NULL,'morning',206728.80,NULL,'TIN-85843642','1565416029',NULL,NULL,'2025-11-25',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('cb680141-e4d8-3113-9b22-2314598f1fb6',NULL,'Ngozi Mohammed','ngozi.mohammed.4@sweettooth.com','EMP-ABJ004-0004','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,1,NULL,'+234-875-814-5690','3301 Phoebe Stravenue, Abuja, FCT State, Nigeria','1982-06-05','female','Nigerian','Tunde Mohammed','+234-905-283-3023','2024-09-28 23:00:00','active',NULL,NULL,'afternoon',129173.04,NULL,'TIN-83346035','3955455219','Peanuts',NULL,'2025-09-02',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('cd88fc77-2791-300a-b1b6-3a8077dc0de1',NULL,'Emeka Chukwu','emeka.chukwu.76@sweettooth.com','EMP-ABJ004-0076','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,1,NULL,'+234-889-112-2314','295 Gerhold Grove, Abuja, FCT State, Nigeria','1985-10-11','male','Nigerian','Nneka Nwankwo','+234-846-338-4840','2025-07-28 23:00:00','active',NULL,NULL,'flexible',236445.92,NULL,'TIN-58458519','0674222201',NULL,NULL,'2025-11-26',3.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('d22feddd-c547-3633-a0e7-e3c62328f26a',NULL,'Ibrahim Nwankwo','ibrahim.nwankwo.42@sweettooth.com','EMP-PHC002-0042','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,1,NULL,'+234-851-533-8883','17235 Theresa Manor, Port Harcourt, Rivers State, Nigeria','1993-10-21','male','Nigerian','Ada Ogunleye','+234-825-881-4844','2024-08-17 23:00:00','active',NULL,NULL,'rotating',178985.63,NULL,'TIN-77142179','9472669131',NULL,NULL,'2025-12-31',4.1,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('d2d72092-8e3e-3015-ad8b-14def999d093',NULL,'Ada Aliyu','ada.aliyu.37@sweettooth.com','EMP-CAL001-0037','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,5,NULL,'+234-872-989-1384','7897 Lexi Plaza Suite 610, Calabar, Cross River State, Nigeria','1987-01-01','female','Nigerian','Chukwuemeka Aliyu','+234-897-117-5358','2024-11-05 23:00:00','active',NULL,NULL,'morning',91545.43,NULL,'TIN-80591336','3769432036',NULL,NULL,'2025-08-28',4.2,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('d38f6118-1921-319e-b294-32743f78c426',NULL,'Amina Chukwu','amina.chukwu.33@sweettooth.com','EMP-CAL001-0033','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,4,NULL,'+234-893-725-9125','99846 Wuckert Fields Suite 064, Calabar, Cross River State, Nigeria','1982-01-12','female','Nigerian','Tunde Johnson','+234-858-986-8482','2025-08-06 23:00:00','active',NULL,NULL,'flexible',327366.69,NULL,'TIN-50763065','5458411962',NULL,NULL,'2025-11-21',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('d3df3ca0-c239-3828-a331-f97d71fc0b8b',NULL,'Ada Aliyu','ada.aliyu.104@sweettooth.com','EMP-ENU005-0104','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,7,NULL,'+234-850-808-9501','8803 Wilfred Trail, Enugu, Enugu State, Nigeria','1990-01-07','female','Nigerian','Chigozie Mohammed','+234-880-918-5404','2023-10-11 23:00:00','active',NULL,NULL,'afternoon',125123.42,NULL,'TIN-15599458','7349825741',NULL,NULL,'2025-08-24',4.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('d6793bc3-c1b4-350e-8123-f18ff6258ef0',NULL,'Emeka Mohammed','emeka.mohammed.1@sweettooth.com','EMP-CAL001-0001','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,1,NULL,'+234-882-116-1140','321 Padberg Flat Apt. 901, Calabar, Cross River State, Nigeria','1989-11-02','male','Nigerian','Amina Adebayo','+234-825-918-6553','2024-08-01 23:00:00','active',NULL,NULL,'afternoon',265825.65,NULL,'TIN-38874981','8491507261',NULL,NULL,'2025-07-21',4.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('d71d2b72-1bd0-3d22-ae70-6bb37aa22dcb',NULL,'Chioma Johnson','chioma.johnson.74@sweettooth.com','EMP-ABJ004-0074','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,1,NULL,'+234-908-956-1602','42291 Guiseppe Creek Apt. 965, Abuja, FCT State, Nigeria','1989-04-12','female','Nigerian','Ibrahim Johnson','+234-803-116-6707','2023-04-07 23:00:00','active',NULL,NULL,'morning',149814.90,NULL,'TIN-95333413','5503482219',NULL,NULL,'2025-10-04',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('d9a3cf9e-8e16-35ac-b437-387e412f148d',NULL,'Kunle Nwankwo','kunle.nwankwo.83@sweettooth.com','EMP-ABJ004-0083','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,4,NULL,'+234-807-435-2179','9885 Vaughn Loaf Apt. 286, Abuja, FCT State, Nigeria','1992-10-18','male','Nigerian','Kemi Adebayo','+234-803-384-7161','2025-10-08 23:00:00','active',NULL,NULL,'flexible',317190.40,NULL,'TIN-72524597','2893456849',NULL,NULL,'2025-11-28',4.3,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('db809f7c-9862-379a-babe-3ed2aa72e4ce',NULL,'Emeka Eze','emeka.eze.50@sweettooth.com','EMP-PHC002-0050','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,4,NULL,'+234-890-702-3036','410 Jennings Square Suite 888, Port Harcourt, Rivers State, Nigeria','1986-11-21','male','Nigerian','Folake Williams','+234-905-590-5028','2025-04-14 23:00:00','active',NULL,NULL,'morning',120912.95,NULL,'TIN-35335104','0172948827',NULL,NULL,'2025-12-12',3.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('dcb6e0a6-99cd-3d5c-80c5-dd5d4e3dab70',NULL,'Chigozie Adebayo','chigozie.adebayo.43@sweettooth.com','EMP-PHC002-0043','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,1,NULL,'+234-812-627-1422','44995 Greenfelder Passage, Port Harcourt, Rivers State, Nigeria','1994-06-07','male','Nigerian','Chioma Chukwu','+234-905-423-5963','2025-07-27 23:00:00','active',NULL,NULL,'rotating',214036.00,NULL,'TIN-90272915','6601081540',NULL,NULL,'2025-07-21',4.4,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('dec48d4e-030a-3422-8d24-ccac1b9f904e',NULL,'Abubakar Aliyu','abubakar.aliyu.32@sweettooth.com','EMP-CAL001-0032','019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,2,NULL,'+234-907-215-3180','29480 Zemlak Alley Apt. 271, Calabar, Cross River State, Nigeria','1987-11-02','male','Nigerian','Kemi Bello','+234-817-727-6543','2025-06-08 23:00:00','active',NULL,NULL,'afternoon',151348.58,NULL,'TIN-57405606','8891987481',NULL,NULL,'2025-11-05',4.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('dffd6369-14e8-33e4-bf8e-72416d72a9a4',NULL,'Emeka Eze','emeka.eze.72@sweettooth.com','EMP-LAG003-0072','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,7,NULL,'+234-904-342-8624','36558 Vidal Common, Lagos, Lagos State, Nigeria','1984-08-30','male','Nigerian','Amina Chukwu','+234-891-598-7359','2025-03-31 23:00:00','active',NULL,NULL,'morning',165306.04,NULL,'TIN-78140667','7618406325',NULL,NULL,'2025-11-21',4.9,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('e3e53915-e9c8-3496-bc9c-a43ec860e82c',NULL,'Chukwuemeka Mohammed','chukwuemeka.mohammed.3@sweettooth.com','EMP-LAG003-0003','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,1,NULL,'+234-810-528-1643','45740 Fritsch Keys, Lagos, Lagos State, Nigeria','1995-07-03','male','Nigerian','Kemi Mohammed','+234-826-962-8114','2025-06-26 23:00:00','active',NULL,NULL,'afternoon',209890.73,NULL,'TIN-30379766','6572654689','Lactose',NULL,'2025-12-19',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('e5e408cd-5186-394e-8856-bbfed4d5292d',NULL,'Chioma Okafor','chioma.okafor.12@sweettooth.com','EMP-PHC002-0012','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,7,NULL,'+234-819-905-2996','798 Brakus Rue, Port Harcourt, Rivers State, Nigeria','1996-09-05','female','Nigerian','Abubakar Okafor','+234-897-816-6254','2023-04-29 23:00:00','active',NULL,NULL,'rotating',130049.72,NULL,'TIN-33831486','3192416069',NULL,NULL,'2025-09-15',4.1,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('e94cc382-2daa-3f67-b05b-d8a96f6750a8',NULL,'Ibrahim Nwankwo','ibrahim.nwankwo.88@sweettooth.com','EMP-ABJ004-0088','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,7,NULL,'+234-852-800-9867','8109 Lakin Oval, Abuja, FCT State, Nigeria','1994-10-03','male','Nigerian','Blessing Okafor','+234-860-121-7188','2024-06-08 23:00:00','active',NULL,NULL,'afternoon',80155.82,NULL,'TIN-64904401','9963116366',NULL,NULL,'2025-07-19',4.4,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('eb291dee-6787-3f0a-b454-d20c7d3e16a2',NULL,'Abubakar Chukwu','abubakar.chukwu.44@sweettooth.com','EMP-PHC002-0044','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,1,NULL,'+234-875-793-3974','9958 Keaton Lodge, Port Harcourt, Rivers State, Nigeria','2002-12-09','male','Nigerian','Blessing Williams','+234-832-497-2631','2025-11-01 23:00:00','active',NULL,NULL,'morning',323507.38,NULL,'TIN-80318747','8467579086',NULL,NULL,'2025-08-05',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('edebbd60-00aa-347f-8480-077ea06e48e6',NULL,'Amina Adebayo','amina.adebayo.23@sweettooth.com','EMP-LAG003-0023','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,1,NULL,'+234-868-474-5525','2918 Gardner Throughway Suite 895, Lagos, Lagos State, Nigeria','1996-07-15','female','Nigerian','Chukwuemeka Adebayo','+234-843-694-6447','2025-07-13 23:00:00','active',NULL,NULL,'rotating',82330.71,NULL,'TIN-96808961','9989032286',NULL,NULL,'2025-10-11',4.0,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('ee368c08-bfeb-47a2-aa3e-12990b2aec0a',NULL,'David Okafor','hr.officer@sweettooth.com',NULL,'019b9efb-b43d-73c7-95bd-0687cf05c811',1,NULL,9,NULL,'08198765432',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$CqV7LV2r3o8xsY4vjLJf3.BVMZXhl4i03YzYLHbyOmvw7uTnBSD8W',NULL,NULL,NULL,'2026-01-09 03:10:36',NULL,'2026-01-09 03:10:36','2026-01-09 03:10:36'),
('efb7bedd-60c8-37d5-895c-10435fa688ba',NULL,'Abubakar Adebayo','abubakar.adebayo.81@sweettooth.com','EMP-ABJ004-0081','019b9efb-b469-7219-8b97-cb9f291b4be4',1,NULL,4,NULL,'+234-833-124-4257','1682 Ariel Bypass Apt. 674, Abuja, FCT State, Nigeria','1986-12-29','male','Nigerian','Fatima Bello','+234-830-945-8975','2025-07-28 23:00:00','active',NULL,NULL,'rotating',281010.21,NULL,'TIN-28082431','0944683221',NULL,NULL,'2025-10-29',3.8,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('f1e80959-4086-35dd-a23d-aa1efd7cc653',NULL,'Ibrahim Bello','ibrahim.bello.53@sweettooth.com','EMP-PHC002-0053','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,5,NULL,'+234-835-769-1558','6263 Granville Ways, Port Harcourt, Rivers State, Nigeria','1994-03-18','male','Nigerian','Ngozi Bello','+234-846-413-9336','2024-10-18 23:00:00','active',NULL,NULL,'afternoon',297569.73,NULL,'TIN-47214221','2915067818',NULL,NULL,'2025-11-23',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('f8583630-8f77-3229-936a-b667bedb97fa',NULL,'Fatima Okafor','fatima.okafor.102@sweettooth.com','EMP-ENU005-0102','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,5,NULL,'+234-809-100-5370','68861 Ashlee Rue Suite 818, Enugu, Enugu State, Nigeria','1991-12-13','female','Nigerian','Chigozie Chukwu','+234-860-658-9559','2023-02-17 23:00:00','active',NULL,NULL,'afternoon',197851.86,NULL,'TIN-23178084','4764923883',NULL,NULL,'2025-09-17',4.5,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('fab700d2-77d7-38c3-b15b-5772dd7bbdb9',NULL,'Emeka Aliyu','emeka.aliyu.90@sweettooth.com','EMP-ENU005-0090','019b9efb-b473-7177-aceb-a5d244ef3f8c',1,NULL,1,NULL,'+234-870-386-3119','8662 Howe Junction Apt. 777, Enugu, Enugu State, Nigeria','1982-07-25','male','Nigerian','Kemi Bello','+234-870-503-7797','2023-01-30 23:00:00','active',NULL,NULL,'rotating',197067.54,NULL,'TIN-97440873','7412175635',NULL,NULL,'2025-10-30',3.7,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('fba7afbc-b56f-3385-958b-ca58ffba8738',NULL,'Folake Nwankwo','folake.nwankwo.67@sweettooth.com','EMP-LAG003-0067','019b9efb-b453-7326-aefa-4c9b271f158a',1,NULL,4,NULL,'+234-857-256-9855','73347 Liliane Avenue Suite 277, Lagos, Lagos State, Nigeria','2001-09-09','female','Nigerian','Tunde Bello','+234-817-432-1014','2025-07-08 23:00:00','active',NULL,NULL,'flexible',176743.90,NULL,'TIN-85959789','5627156519',NULL,NULL,'2025-09-23',4.3,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17'),
('ffe79fca-3076-3151-a954-a1c1660b067d',NULL,'Hauwa Ogunleye','hauwa.ogunleye.54@sweettooth.com','EMP-PHC002-0054','019b9efb-b447-73e3-ac48-56618b9d7edb',1,NULL,5,NULL,'+234-851-688-4490','1189 Wendy Union Apt. 103, Port Harcourt, Rivers State, Nigeria','2002-03-07','female','Nigerian','Chukwuemeka Nwankwo','+234-816-722-3777','2024-01-22 23:00:00','active',NULL,NULL,'rotating',338967.13,NULL,'TIN-43410920','6868944904',NULL,NULL,'2025-08-21',4.6,'employee',NULL,'$2y$12$AE7DZwjfSFWZGwsChexroe.zweBjzl/HtLLAZmvl61n2uyhwvTtfa',NULL,NULL,NULL,'2026-01-08 19:01:17',NULL,'2026-01-08 19:01:17','2026-01-08 19:01:17');
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

-- Dump completed on 2026-01-09  7:57:09
