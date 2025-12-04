/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-12.1.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: sweettooth
-- ------------------------------------------------------
-- Server version	12.1.2-MariaDB-deb13

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_audit_requests`
--

LOCK TABLES `approval_audit_requests` WRITE;
/*!40000 ALTER TABLE `approval_audit_requests` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `approval_audit_requests` VALUES
(1,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','create:App\\Models\\Department','Please provide a reason for creating this department. This will be reviewed by an administrator.','{\"id\":null,\"name\":\"Department Name\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"category_id\":\"019adbd4-0b66-7293-b25e-96037d61320f\",\"description\":\"Department NameDepartment NameDepartment NameDepartment NameDepartment NameDepartment Name\"}','approved',NULL,'2025-12-01 22:54:58',NULL,'2025-12-01 21:54:38','2025-12-01 21:54:58'),
(2,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','update:App\\Models\\Department','Please provide a reason for updating this department. This will be reviewed by an administrator.','{\"id\":\"9\",\"name\":\"Department Name\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"category_id\":\"019adbd4-0b31-7149-947f-ff24ebc602f8\",\"description\":\"Department NameDepartment NameDepartment NameDepartment NameDepartment NameDepartment Name\"}','approved',NULL,'2025-12-01 22:56:03',NULL,'2025-12-01 21:55:39','2025-12-01 21:56:03'),
(3,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','update:roles:15b9b0d3-b46a-3793-92c3-a31bdee6db5d','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles.','{\"selectedRoles\":[\"Cashier\",\"Sales Manager\",\"Head of Gelato\"]}','approved',NULL,'2025-12-01 23:38:53',NULL,'2025-12-01 22:14:34','2025-12-01 22:38:53'),
(4,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','update:roles:37b32b0e-e9fa-3436-924b-9b59eca605d6','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles.','{\"selectedRoles\":[\"Gelato Production Staff\",\"HR Manager\"]}','approved',NULL,'2025-12-01 23:34:12',NULL,'2025-12-01 22:33:56','2025-12-01 22:34:12'),
(5,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','update:roles:0357d01c-7205-374e-b9e8-7c72d8f158ae','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles','{\"selectedRoles\":[\"Confectionaries Production Staff\",\"HR Manager\"]}','approved',NULL,'2025-12-01 23:44:47',NULL,'2025-12-01 22:44:31','2025-12-01 22:44:47'),
(6,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','sync:App\\Models\\Employee:roles:0357d01c-7205-374e-b9e8-7c72d8f158ae','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles.','{\"id\":\"0357d01c-7205-374e-b9e8-7c72d8f158ae\",\"sync_data\":[\"Confectionaries Production Staff\",\"Sales Manager\"]}','approved',NULL,'2025-12-01 23:57:40',NULL,'2025-12-01 22:49:41','2025-12-01 22:57:40'),
(7,NULL,'3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee',NULL,NULL,'update:Spatie\\Permission\\Models\\Role:1','testung this','{\"isEditing\":true,\"selectedRoleId\":1,\"roleName\":\"Admin\",\"roleGuard\":\"web\",\"selectedPermissions\":[48,49,50,52,56,60,61,62,\"58\",\"59\"]}','pending',NULL,NULL,NULL,'2025-12-01 23:11:32','2025-12-01 23:11:32'),
(8,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','sync:App\\Models\\Employee:roles:15b9b0d3-b46a-3793-92c3-a31bdee6db5d','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles.','{\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"sync_data\":[\"6\"]}','approved',NULL,'2025-12-02 09:24:30',NULL,'2025-12-02 08:15:13','2025-12-02 08:24:30'),
(9,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','update:App\\Models\\Employee:15b9b0d3-b46a-3793-92c3-a31bdee6db5d','Provide Reason for Employee Update\nAs a non-admin user, please provide a reason for updating this employee.','{\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":\"4\",\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okoro\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":218354.12,\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":3.8,\"profile_photo\":\"employee-photos\\/h0eeC2e1kqEsyEetAV7BQg9H4AqH74IZBoaERa8M.jpg\",\"selectedRoles\":[\"HR Manager\"]}','approved',NULL,'2025-12-02 09:26:44',NULL,'2025-12-02 08:26:28','2025-12-02 08:26:44'),
(10,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','update:App\\Models\\Employee:15b9b0d3-b46a-3793-92c3-a31bdee6db5d','Provide Reason for Employee Update\nAs a non-admin user, please provide a reason for updating this employee.','{\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":\"4\",\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okoro\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":218354.12,\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":3.8,\"profile_photo\":\"employee-photos\\/b4PXHi9P0QZAmeKIJnO8uCMCTsQ8HG5lzjPPHDYC.jpg\",\"selectedRoles\":[\"HR Manager\"]}','approved',NULL,'2025-12-02 09:35:44',NULL,'2025-12-02 08:35:04','2025-12-02 08:35:44'),
(11,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','update:App\\Models\\Employee:15b9b0d3-b46a-3793-92c3-a31bdee6db5d','Provide Reason for Employee Update\nAs a non-admin user, please provide a reason for updating this employee.','{\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":\"4\",\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okor\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":218354.12,\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":3.8,\"profile_photo\":\"employee-photos\\/PopMZv4EJtTdqOQhNJ0DQ1tTN1sG1d8pSW6saIDB.jpg\",\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"selectedRoles\":[\"HR Manager\"]}','approved',NULL,'2025-12-02 12:17:10',NULL,'2025-12-02 11:09:59','2025-12-02 11:17:10'),
(12,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','create:App\\Models\\Employee','\nProvide Reason for Employee Creation\nAs a non-admin user, please provide a reason for creating this employee.','{\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":\"3\",\"employee_number\":\"EMP-CAL001-0022\",\"name\":\"ILem Testing\",\"email\":\"ilem1@gmail.com\",\"phone\":\"+2349896572633\",\"address\":\"mount zion lane\",\"date_of_birth\":\"2018-06-14\",\"gender\":\"male\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":null,\"emergency_contact_phone\":null,\"hire_date\":\"2025-12-02\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"morning\",\"salary\":null,\"hourly_rate\":null,\"tax_id\":null,\"bank_account\":null,\"allergies\":null,\"last_performance_review_date\":null,\"performance_rating\":null,\"password\":\"$2y$12$\\/0Ox9lNEm4.9vNgzKb2vxObcWC1rpaTPjsu8CqTAoc4HUv3PLrFOa\",\"email_verified_at\":\"2025-12-02T12:39:36.230535Z\",\"profile_photo\":\"employee-photos\\/1d8dx6WyIJodR9RXFWTDKCSAK4HV93dhHIpnBJpA.jpg\",\"selectedRoles\":[\"Confectionaries Production Staff\"]}','approved',NULL,'2025-12-02 12:39:53',NULL,'2025-12-02 11:39:36','2025-12-02 11:39:53');
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
  `description` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `audit_logs` VALUES
(1,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80',NULL,NULL,NULL,'create','Please provide a reason for creating this department. This will be reviewed by an administrator.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-01 22:54:38',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":null}','2025-12-01 21:54:38','2025-12-01 21:54:38'),
(2,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Department',NULL,NULL,'approve_create_App\\Models\\Department','Approved by Ibrahim Johnson','{\"name\":\"Department Name\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"category_id\":\"019adbd4-0b66-7293-b25e-96037d61320f\",\"description\":\"Department NameDepartment NameDepartment NameDepartment NameDepartment NameDepartment Name\",\"slug\":\"department-name\",\"updated_at\":\"2025-12-01T22:54:58.000000Z\",\"created_at\":\"2025-12-01T22:54:58.000000Z\",\"id\":9}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-01 22:54:58',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Department\"}','2025-12-01 21:54:58','2025-12-01 21:54:58'),
(3,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Department',NULL,NULL,'update','Please provide a reason for updating this department. This will be reviewed by an administrator.','{\"id\":9,\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"category_id\":\"019adbd4-0b66-7293-b25e-96037d61320f\",\"name\":\"Department Name\",\"slug\":\"department-name\",\"description\":\"Department NameDepartment NameDepartment NameDepartment NameDepartment NameDepartment Name\",\"enable_table_management\":false,\"table_management_settings\":null,\"created_at\":\"2025-12-01T22:54:58.000000Z\",\"updated_at\":\"2025-12-01T22:54:58.000000Z\"}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-01 22:55:39',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Department\"}','2025-12-01 21:55:39','2025-12-01 21:55:39'),
(4,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Department',NULL,NULL,'approve_update_App\\Models\\Department','Approved by Ibrahim Johnson','{\"id\":9,\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"category_id\":\"019adbd4-0b31-7149-947f-ff24ebc602f8\",\"name\":\"Department Name\",\"slug\":\"department-name\",\"description\":\"Department NameDepartment NameDepartment NameDepartment NameDepartment NameDepartment Name\",\"enable_table_management\":false,\"table_management_settings\":null,\"created_at\":\"2025-12-01T22:54:58.000000Z\",\"updated_at\":\"2025-12-01T22:56:03.000000Z\"}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-01 22:56:03',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Department\"}','2025-12-01 21:56:03','2025-12-01 21:56:03'),
(5,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','37b32b0e-e9fa-3436-924b-9b59eca605d6',NULL,'update','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles.','{\"id\":\"37b32b0e-e9fa-3436-924b-9b59eca605d6\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":2,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0007\",\"name\":\"Kunle Mohammed\",\"email\":\"kunle.mohammed.7@sweettooth.com\",\"phone\":\"+234-862-845-7226\",\"address\":\"184 Deion Hills Suite 361, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"1997-10-15\",\"gender\":\"male\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Amina Bello\",\"emergency_contact_phone\":\"+234-828-665-7375\",\"hire_date\":\"2024-02-24\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"morning\",\"salary\":\"290339.66\",\"hourly_rate\":null,\"tax_id\":\"TIN-29056773\",\"bank_account\":\"9874099685\",\"allergies\":\"Peanuts\",\"profile_photo\":null,\"last_performance_review_date\":\"2025-10-13\",\"performance_rating\":\"4.8\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-01 23:33:56',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-01 22:33:56','2025-12-01 22:33:56'),
(6,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80',NULL,NULL,NULL,'approve_update','Approved by Ibrahim Johnson',NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-01 23:38:53',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":null}','2025-12-01 22:38:53','2025-12-01 22:38:53'),
(7,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae',NULL,'update','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles','{\"id\":\"0357d01c-7205-374e-b9e8-7c72d8f158ae\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":3,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0010\",\"name\":\"Chigozie Eze\",\"email\":\"chigozie.eze.10@sweettooth.com\",\"phone\":\"+234-885-624-7857\",\"address\":\"2643 Godfrey Parkway Apt. 976, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-11-13\",\"gender\":\"male\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ngozi Ogunleye\",\"emergency_contact_phone\":\"+234-856-686-9450\",\"hire_date\":\"2023-06-25\",\"termination_date\":null,\"status\":\"on_probation\",\"probation_end_date\":\"2025-12-08\",\"shift_preference\":\"afternoon\",\"salary\":\"227060.95\",\"hourly_rate\":null,\"tax_id\":\"TIN-22351552\",\"bank_account\":\"6261881000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-08-16\",\"performance_rating\":\"4.0\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-01 23:44:31',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-01 22:44:32','2025-12-01 22:44:32'),
(8,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80',NULL,NULL,NULL,'approve_update','Approved by Ibrahim Johnson',NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-01 23:44:47',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":null}','2025-12-01 22:44:47','2025-12-01 22:44:47'),
(9,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae',NULL,'update','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles.','{\"id\":\"0357d01c-7205-374e-b9e8-7c72d8f158ae\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":3,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0010\",\"name\":\"Chigozie Eze\",\"email\":\"chigozie.eze.10@sweettooth.com\",\"phone\":\"+234-885-624-7857\",\"address\":\"2643 Godfrey Parkway Apt. 976, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-11-13\",\"gender\":\"male\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ngozi Ogunleye\",\"emergency_contact_phone\":\"+234-856-686-9450\",\"hire_date\":\"2023-06-25\",\"termination_date\":null,\"status\":\"on_probation\",\"probation_end_date\":\"2025-12-08\",\"shift_preference\":\"afternoon\",\"salary\":\"227060.95\",\"hourly_rate\":null,\"tax_id\":\"TIN-22351552\",\"bank_account\":\"6261881000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-08-16\",\"performance_rating\":\"4.0\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-01 23:49:41',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-01 22:49:41','2025-12-01 22:49:41'),
(10,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','3e1289ba-e385-3b84-8243-58093af0fe80','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae',NULL,'approve_sync','Approved by Ibrahim Johnson','{\"id\":\"0357d01c-7205-374e-b9e8-7c72d8f158ae\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":3,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0010\",\"name\":\"Chigozie Eze\",\"email\":\"chigozie.eze.10@sweettooth.com\",\"phone\":\"+234-885-624-7857\",\"address\":\"2643 Godfrey Parkway Apt. 976, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-11-13\",\"gender\":\"male\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ngozi Ogunleye\",\"emergency_contact_phone\":\"+234-856-686-9450\",\"hire_date\":\"2023-06-25\",\"termination_date\":null,\"status\":\"on_probation\",\"probation_end_date\":\"2025-12-08\",\"shift_preference\":\"afternoon\",\"salary\":\"227060.95\",\"hourly_rate\":null,\"tax_id\":\"TIN-22351552\",\"bank_account\":\"6261881000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-08-16\",\"performance_rating\":\"4.0\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-01 23:57:40',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-01 22:57:40','2025-12-01 22:57:40'),
(11,NULL,'App\\Models\\Employee','01acda54-c639-3075-9150-37f5eed06ddb','Spatie\\Permission\\Models\\Role','24',NULL,'create','Test role creation from audit service','{\"guard_name\":\"employee\",\"name\":\"test_role2_1764634084\",\"updated_at\":\"2025-12-02T00:08:04.000000Z\",\"created_at\":\"2025-12-02T00:08:04.000000Z\",\"id\":24}','[]','127.0.0.1','Symfony','completed',NULL,'2025-12-02 00:08:04',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"Spatie\\\\Permission\\\\Models\\\\Role\"}','2025-12-01 23:08:04','2025-12-01 23:08:04'),
(12,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','15b9b0d3-b46a-3793-92c3-a31bdee6db5d',NULL,'update','\nProvide Reason for Role Update\nAs a non-admin user, please provide a reason for updating this employee\'s roles.','{\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":4,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okoro\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":\"218354.12\",\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":\"3.8\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-02 09:15:13',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-02 08:15:13','2025-12-02 08:15:13'),
(13,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','15b9b0d3-b46a-3793-92c3-a31bdee6db5d',NULL,'approve_sync','Approved by Chigozie Eze','{\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":4,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okoro\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":\"218354.12\",\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":\"3.8\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-02 09:24:30',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-02 08:24:30','2025-12-02 08:24:30'),
(14,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','15b9b0d3-b46a-3793-92c3-a31bdee6db5d',NULL,'update','Provide Reason for Employee Update\nAs a non-admin user, please provide a reason for updating this employee.','{\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":4,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okoro\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":\"218354.12\",\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":\"3.8\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-02 09:26:28',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-02 08:26:28','2025-12-02 08:26:28'),
(15,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae',NULL,NULL,NULL,'approve_update','Approved by Chigozie Eze',NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-02 09:26:44',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":null}','2025-12-02 08:26:44','2025-12-02 08:26:44'),
(16,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','15b9b0d3-b46a-3793-92c3-a31bdee6db5d',NULL,'update','Provide Reason for Employee Update\nAs a non-admin user, please provide a reason for updating this employee.','{\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":4,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okoro\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":\"218354.12\",\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":\"3.8\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-02 09:35:04',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-02 08:35:04','2025-12-02 08:35:04'),
(17,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae',NULL,NULL,NULL,'approve_update','Approved by Chigozie Eze',NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-02 09:35:44',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":null}','2025-12-02 08:35:44','2025-12-02 08:35:44'),
(18,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','15b9b0d3-b46a-3793-92c3-a31bdee6db5d',NULL,'update','Provide Reason for Employee Update\nAs a non-admin user, please provide a reason for updating this employee.','{\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":4,\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okoro\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":\"218354.12\",\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"profile_photo\":null,\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":\"3.8\",\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-01T21:31:40.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-02 12:09:59',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-02 11:09:59','2025-12-02 11:09:59'),
(19,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','15b9b0d3-b46a-3793-92c3-a31bdee6db5d',NULL,'approve_update','Approved by Chigozie Eze','{\"id\":\"15b9b0d3-b46a-3793-92c3-a31bdee6db5d\",\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":\"4\",\"manager_id\":null,\"employee_number\":\"EMP-CAL001-0014\",\"name\":\"Hauwa Okor\",\"email\":\"hauwa.okoro.14@sweettooth.com\",\"phone\":\"+234-867-523-5116\",\"address\":\"970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria\",\"date_of_birth\":\"2001-02-21\",\"gender\":\"female\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":\"Ibrahim Okoro\",\"emergency_contact_phone\":\"+234-816-837-9379\",\"hire_date\":\"2024-02-08\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"flexible\",\"salary\":218354.12,\"hourly_rate\":null,\"tax_id\":\"TIN-52853539\",\"bank_account\":\"8278549000\",\"allergies\":null,\"profile_photo\":\"employee-photos\\/PopMZv4EJtTdqOQhNJ0DQ1tTN1sG1d8pSW6saIDB.jpg\",\"last_performance_review_date\":\"2025-07-18\",\"performance_rating\":3.8,\"created_at\":\"2025-12-01T21:31:40.000000Z\",\"updated_at\":\"2025-12-02T12:17:10.000000Z\",\"deleted_at\":null,\"password\":\"$2y$12$3NwpX6YOt9t\\/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy\",\"email_verified_at\":\"2025-12-01T21:31:40.000000Z\",\"remember_token\":null,\"is_superadmin\":0}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-02 12:17:10',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-02 11:17:10','2025-12-02 11:17:10'),
(20,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\LeaveApplication','1',NULL,'create','Submitted leave application: Annual from 2025-12-05 to 2025-12-07 (1 working days). Reason: Reason for Leave *Reason for Leave *Reason for Leave *Reason for Leave *','{\"application_number\":\"LA-2025-001\",\"employee_id\":\"0357d01c-7205-374e-b9e8-7c72d8f158ae\",\"leave_type_id\":\"1\",\"start_date\":\"2025-12-05T00:00:00.000000Z\",\"end_date\":\"2025-12-07T00:00:00.000000Z\",\"total_days\":\"1.00\",\"reason\":\"Reason for Leave *Reason for Leave *Reason for Leave *Reason for Leave *\",\"emergency_contact\":\"98764849494\",\"supporting_document\":null,\"status\":\"pending\",\"updated_at\":\"2025-12-02T12:31:42.000000Z\",\"created_at\":\"2025-12-02T12:31:42.000000Z\",\"id\":1}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-02 12:31:42',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\LeaveApplication\"}','2025-12-02 11:31:42','2025-12-02 11:31:42'),
(21,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae',NULL,NULL,NULL,'create','\nProvide Reason for Employee Creation\nAs a non-admin user, please provide a reason for creating this employee.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','pending',NULL,'2025-12-02 12:39:36',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":null}','2025-12-02 11:39:36','2025-12-02 11:39:36'),
(22,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','App\\Models\\Employee','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','019adf13-924f-714f-8352-5cd70791074e',NULL,'approve_create','Approved by Chigozie Eze','{\"branch_id\":\"019adbd4-0ac6-73c1-aab2-48530e36a7fc\",\"department_id\":\"3\",\"employee_number\":\"EMP-CAL001-0022\",\"name\":\"ILem Testing\",\"email\":\"ilem1@gmail.com\",\"phone\":\"+2349896572633\",\"address\":\"mount zion lane\",\"date_of_birth\":\"2018-06-14\",\"gender\":\"male\",\"nationality\":\"Nigerian\",\"emergency_contact_name\":null,\"emergency_contact_phone\":null,\"hire_date\":\"2025-12-02\",\"termination_date\":null,\"status\":\"active\",\"probation_end_date\":null,\"shift_preference\":\"morning\",\"salary\":null,\"hourly_rate\":null,\"tax_id\":null,\"bank_account\":null,\"allergies\":null,\"last_performance_review_date\":null,\"performance_rating\":null,\"password\":\"$2y$12$\\/0Ox9lNEm4.9vNgzKb2vxObcWC1rpaTPjsu8CqTAoc4HUv3PLrFOa\",\"profile_photo\":\"employee-photos\\/1d8dx6WyIJodR9RXFWTDKCSAK4HV93dhHIpnBJpA.jpg\",\"id\":\"019adf13-924f-714f-8352-5cd70791074e\",\"updated_at\":\"2025-12-02T12:39:53.000000Z\",\"created_at\":\"2025-12-02T12:39:53.000000Z\"}','[]','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','completed',NULL,'2025-12-02 12:39:53',NULL,'{\"causer\":\"App\\\\Models\\\\Employee\",\"auditable\":\"App\\\\Models\\\\Employee\"}','2025-12-02 11:39:53','2025-12-02 11:39:53');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
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
('019adbd4-0ac6-73c1-aab2-48530e36a7fc','SweetTooth Calabar','CAL-001','12 Marian Road, Calabar Municipal','+234-809-012-3456','calabar@sweettooth.com','Calabar flagship store with full production and sales',NULL,'Nigeria','Cross River','Calabar','540001','Africa/Lagos',1,0,NULL,'2025-12-01 20:31:38','2025-12-01 20:31:38',NULL),
('019adbd4-0aec-707f-b4e0-ea5ba04b7612','SweetTooth Port Harcourt','PHC-002','78 Trans Amadi Industrial Layout','+234-803-456-7890','portharcourt@sweettooth.com','Port Harcourt main branch',NULL,'Nigeria','Rivers','Port Harcourt','500001','Africa/Lagos',1,0,NULL,'2025-12-01 20:31:38','2025-12-01 20:31:38',NULL),
('019adbd4-0b07-7018-928c-e49d13068b9b','SweetTooth Lagos','LAG-003','45 Admiralty Way, Lekki Phase 1','+234-801-234-5678','lagos@sweettooth.com','Lagos head office and production center',NULL,'Nigeria','Lagos','Lagos','101001','Africa/Lagos',1,0,NULL,'2025-12-01 20:31:38','2025-12-01 20:31:38',NULL),
('019adbd4-0b13-726e-9aa8-3f26e5acec1b','SweetTooth Abuja','ABJ-004','23 Gimbiya Street, Area 11, Garki','+234-802-345-6789','abuja@sweettooth.com','Abuja branch with gelato specialty',NULL,'Nigeria','FCT','Abuja','900001','Africa/Lagos',1,0,NULL,'2025-12-01 20:31:38','2025-12-01 20:31:38',NULL),
('019adbd4-0b1e-7356-8544-47bf94c8ffed','SweetTooth Enugu','ENU-005','34 Ogui Road, New Haven','+234-806-789-0123','enugu@sweettooth.com','Enugu branch serving South-East region',NULL,'Nigeria','Enugu','Enugu','400001','Africa/Lagos',1,0,NULL,'2025-12-01 20:31:38','2025-12-01 20:31:38',NULL);
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
  CONSTRAINT `clock_ins_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
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
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_produces_shift_id_recipe_id_unique` (`shift_id`,`recipe_id`),
  KEY `daily_produces_recipe_id_foreign` (`recipe_id`),
  CONSTRAINT `daily_produces_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_produces_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_produces`
--

LOCK TABLES `daily_produces` WRITE;
/*!40000 ALTER TABLE `daily_produces` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `daily_produces` VALUES
(1,1,31,'2025-11-30','afternoon',38.01,49.35,27.48,35.61,2.10,6.63,7.43,34.84,0.00,'in_progress','Maxime et et saepe harum iste unde.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(2,1,20,'2025-11-30','morning',21.10,0.68,27.54,13.86,11.86,5.45,25.54,18.49,5.48,'in_progress','Dicta sequi numquam in quaerat odit architecto.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(3,1,28,'2025-11-30','morning',88.53,17.33,3.85,26.29,24.78,1.44,49.56,0.92,6.19,'in_progress','Rerum qui est iusto laudantium ad minus.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(4,1,22,'2025-11-30','afternoon',70.51,26.95,29.45,15.67,4.21,8.84,26.51,21.00,7.80,'in_progress','Asperiores aut animi ducimus earum vitae nisi.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(5,1,32,'2025-11-30','morning',49.88,2.06,38.88,21.18,13.98,6.34,33.37,35.72,2.35,'in_progress',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(6,1,15,'2025-11-30','afternoon',29.05,24.48,44.88,10.59,5.32,1.49,45.20,1.78,-4.18,'in_progress',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10');
/*!40000 ALTER TABLE `daily_produces` ENABLE KEYS */;
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
('019adbd4-0b31-7149-947f-ff24ebc602f8','Sales','Departments focused on selling products and services, customer acquisition, and revenue generation.','2025-12-01 20:31:38','2025-12-01 20:31:38'),
('019adbd4-0b40-7139-b097-ca2c46d48f55','Production','Departments responsible for manufacturing, production processes, and quality control.','2025-12-01 20:31:38','2025-12-01 20:31:38'),
('019adbd4-0b66-7293-b25e-96037d61320f','Support','Departments providing assistance, customer service, and technical support services.','2025-12-01 20:31:38','2025-12-01 20:31:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_pages`
--

LOCK TABLES `department_pages` WRITE;
/*!40000 ALTER TABLE `department_pages` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `department_pages` VALUES
(1,1,'Products','products','branch-dashboard.production.products','cube',1,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(2,1,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(3,1,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(4,1,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(5,1,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(6,1,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(7,1,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(8,1,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(9,1,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(10,1,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',10,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(11,1,'Production Efficiency Report','report-efficiency','branch-dashboard.production.reports.efficiency','chart-bar',11,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(12,1,'Quality Metrics Report','report-quality','branch-dashboard.production.reports.quality','shield-check',12,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(13,1,'Waste Analysis Report','report-waste','branch-dashboard.production.reports.waste','trash',13,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(14,1,'Cost Analysis Report','report-cost','branch-dashboard.production.reports.cost','currency-dollar',14,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(15,1,'Recipe Performance Report','report-recipe-performance','branch-dashboard.production.reports.recipe-performance','star',15,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(16,1,'Shift Summary Report','report-shift-summary','branch-dashboard.production.reports.shift-summary','clock',16,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(17,1,'Ingredient Utilization Report','report-ingredient-utilization','branch-dashboard.production.reports.ingredient-utilization','beaker',17,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(18,1,'Pipeline Status Report','report-pipeline','branch-dashboard.production.reports.pipeline','arrow-right-circle',18,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(19,1,'Capacity Planning Report','report-capacity','branch-dashboard.production.reports.capacity','server',19,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(20,2,'Products','products','branch-dashboard.production.products','cube',1,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(21,2,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(22,2,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(23,2,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(24,2,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(25,2,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(26,2,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(27,2,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(28,2,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(29,2,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',10,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(30,2,'Production Efficiency Report','report-efficiency','branch-dashboard.production.reports.efficiency','chart-bar',11,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(31,2,'Quality Metrics Report','report-quality','branch-dashboard.production.reports.quality','shield-check',12,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(32,2,'Waste Analysis Report','report-waste','branch-dashboard.production.reports.waste','trash',13,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(33,2,'Cost Analysis Report','report-cost','branch-dashboard.production.reports.cost','currency-dollar',14,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(34,2,'Recipe Performance Report','report-recipe-performance','branch-dashboard.production.reports.recipe-performance','star',15,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(35,2,'Shift Summary Report','report-shift-summary','branch-dashboard.production.reports.shift-summary','clock',16,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(36,2,'Ingredient Utilization Report','report-ingredient-utilization','branch-dashboard.production.reports.ingredient-utilization','beaker',17,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(37,2,'Pipeline Status Report','report-pipeline','branch-dashboard.production.reports.pipeline','arrow-right-circle',18,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(38,2,'Capacity Planning Report','report-capacity','branch-dashboard.production.reports.capacity','server',19,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(39,3,'Products','products','branch-dashboard.production.products','cube',1,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(40,3,'Product Types','product-types','branch-dashboard.production.product-types','tag',2,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(41,3,'Recipes','recipes','branch-dashboard.production.recipes.index','book-open',3,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(42,3,'Add Recipe','recipes-add','branch-dashboard.production.recipes.add','plus-circle',4,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(43,3,'Edit Recipe','recipes-edit','branch-dashboard.production.recipes.edit','pencil',5,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(44,3,'Recipe Detail','recipes-detail','branch-dashboard.production.recipes.detail','document-text',6,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(45,3,'Production Requests','production-requests','branch-dashboard.production.request.index','clipboard',7,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(46,3,'Daily Produce','daily-produce','branch-dashboard.production.daily-produce.index','calendar',8,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(47,3,'Raw Material Tracking','raw-material-tracking','branch-dashboard.production.raw-material-tracking','chart-bar',9,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(48,3,'Shift Closing','shift-closing','branch-dashboard.production.shift-closing.index','clock',10,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(49,3,'Production Efficiency Report','report-efficiency','branch-dashboard.production.reports.efficiency','chart-bar',11,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(50,3,'Quality Metrics Report','report-quality','branch-dashboard.production.reports.quality','shield-check',12,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(51,3,'Waste Analysis Report','report-waste','branch-dashboard.production.reports.waste','trash',13,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(52,3,'Cost Analysis Report','report-cost','branch-dashboard.production.reports.cost','currency-dollar',14,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(53,3,'Recipe Performance Report','report-recipe-performance','branch-dashboard.production.reports.recipe-performance','star',15,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(54,3,'Shift Summary Report','report-shift-summary','branch-dashboard.production.reports.shift-summary','clock',16,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(55,3,'Ingredient Utilization Report','report-ingredient-utilization','branch-dashboard.production.reports.ingredient-utilization','beaker',17,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(56,3,'Pipeline Status Report','report-pipeline','branch-dashboard.production.reports.pipeline','arrow-right-circle',18,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(57,3,'Capacity Planning Report','report-capacity','branch-dashboard.production.reports.capacity','server',19,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(58,4,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(59,4,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(60,4,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(61,4,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(62,5,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(63,5,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(64,5,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(65,5,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(66,6,'POS','pos','branch-dashboard.sales-dashboard.pos.index','shopping-cart',1,1,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(67,6,'My Sales','my-sales','branch-dashboard.sales-dashboard.my-sales.index','user-circle',2,1,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(68,6,'Sales Analytics','sales-analytics','branch-dashboard.sales-dashboard.analytics.index','chart-bar',3,1,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(69,6,'Shift Closing','shift-closing','branch-dashboard.sales-dashboard.shift-closing.index','clock',4,1,'2025-12-01 20:31:40','2025-12-01 20:31:40');
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
(1,4,'019adbd4-4c76-7278-a5e2-5c8a441adf88',1,NULL,0,'2025-12-01 20:31:55','2025-12-01 20:31:55'),
(2,4,'019adbd4-4ce9-7308-a696-1fe039e78981',1,NULL,1,'2025-12-01 20:31:55','2025-12-01 20:31:55'),
(3,4,'019adbd4-4cf9-70d6-9f6c-a9fb27078b31',1,NULL,2,'2025-12-01 20:31:55','2025-12-01 20:31:55'),
(4,4,'019adbd4-4daa-7214-afe9-03776588ca95',1,NULL,3,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(5,4,'019adbd4-4dce-7299-89d1-f4448ff4d0ca',1,NULL,4,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(6,4,'019adbd4-4e2d-7309-915b-901ffa859a11',1,NULL,5,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(7,5,'019adbd4-4c76-7278-a5e2-5c8a441adf88',1,NULL,0,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(8,5,'019adbd4-4ce9-7308-a696-1fe039e78981',1,NULL,1,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(9,5,'019adbd4-4cf9-70d6-9f6c-a9fb27078b31',1,NULL,2,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(10,5,'019adbd4-4daa-7214-afe9-03776588ca95',1,NULL,3,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(11,5,'019adbd4-4dc3-7382-b92e-5605467ba98a',1,NULL,4,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(12,6,'019adbd4-4e69-73ce-a6f1-66d2413dbd6d',1,NULL,0,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(13,6,'019adbd4-4ec9-7212-9283-70157bce003e',1,NULL,1,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(14,6,'019adbd4-4ee5-72bf-bb4c-bf3db6824547',1,NULL,2,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(15,6,'019adbd4-4efb-7075-8306-8554887d95e0',1,NULL,3,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(16,6,'019adbd4-4f11-70a4-b4cb-38457db08736',1,NULL,4,'2025-12-01 20:31:56','2025-12-01 20:31:56'),
(17,6,'019adbd4-4fa1-7097-94e0-223b12e5b3ee',1,NULL,5,'2025-12-01 20:31:56','2025-12-01 20:31:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `departments` VALUES
(1,NULL,'019adbd4-0b40-7139-b097-ca2c46d48f55','Kitchen','kitchen','Prepares food for Till, Confectionaries, Corner Store',0,NULL,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(2,NULL,'019adbd4-0b40-7139-b097-ca2c46d48f55','Gelato Production','gelato-production','Makes gelato/ice cream',0,NULL,'2025-12-01 20:31:38','2025-12-01 20:31:38'),
(3,NULL,'019adbd4-0b40-7139-b097-ca2c46d48f55','Confectionaries Production','confectionaries-production','Makes confectionery items',0,NULL,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(4,NULL,'019adbd4-0b31-7149-947f-ff24ebc602f8','Till','till','Sells ready-made snacks',0,NULL,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(5,NULL,'019adbd4-0b31-7149-947f-ff24ebc602f8','Corner Store','corner-store','On-demand food sales',0,NULL,'2025-12-01 20:31:39','2025-12-01 20:31:39'),
(6,NULL,'019adbd4-0b31-7149-947f-ff24ebc602f8','Confectionaries Sales','confectionaries-sales','Sells confectionery items',0,NULL,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(7,NULL,'019adbd4-0b66-7293-b25e-96037d61320f','Inventory/Store','inventorystore','Manages all stock',0,NULL,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(8,NULL,'019adbd4-0b66-7293-b25e-96037d61320f','HR','hr','Human resources (corporate level)',0,NULL,'2025-12-01 20:31:40','2025-12-01 20:31:40'),
(9,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','019adbd4-0b31-7149-947f-ff24ebc602f8','Department Name','department-name','Department NameDepartment NameDepartment NameDepartment NameDepartment NameDepartment Name',0,NULL,'2025-12-01 21:54:58','2025-12-01 21:56:03');
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
  CONSTRAINT `employee_leave_balances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_leave_balances`
--

LOCK TABLES `employee_leave_balances` WRITE;
/*!40000 ALTER TABLE `employee_leave_balances` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `employee_leave_balances` VALUES
(1,'0357d01c-7205-374e-b9e8-7c72d8f158ae',1,2025,5.00,1.00,0.00,4.00,0.00,'2025-12-02 11:31:09','2025-12-02 11:32:14');
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
  CONSTRAINT `employee_stepouts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
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
  `is_superadmin` tinyint(1) DEFAULT 0,
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
set autocommit=0;
INSERT INTO `employees` VALUES
('019adf13-924f-714f-8352-5cd70791074e','019adbd4-0ac6-73c1-aab2-48530e36a7fc',3,NULL,'EMP-CAL001-0022','ILem Testing','ilem1@gmail.com','+2349896572633','mount zion lane','2018-06-14','male','Nigerian',NULL,NULL,'2025-12-02',NULL,'active',NULL,'morning',NULL,NULL,NULL,NULL,NULL,'employee-photos/1d8dx6WyIJodR9RXFWTDKCSAK4HV93dhHIpnBJpA.jpg',NULL,NULL,'2025-12-02 11:39:53','2025-12-02 11:39:53',NULL,'$2y$12$/0Ox9lNEm4.9vNgzKb2vxObcWC1rpaTPjsu8CqTAoc4HUv3PLrFOa',NULL,NULL,0),
('01acda54-c639-3075-9150-37f5eed06ddb','019adbd4-0b07-7018-928c-e49d13068b9b',1,NULL,'EMP-LAG003-0043','Kunle Eze','kunle.eze.43@sweettooth.com','+234-868-433-2502','5345 Jabari Land Suite 055, Lagos, Lagos State, Nigeria','1984-07-26','male','Nigerian','Ngozi Okoro','+234-902-547-2535','2023-12-02',NULL,'on_probation','2026-02-08','morning',256731.79,NULL,'TIN-77421271','9917513847',NULL,NULL,'2025-10-16',3.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('0357d01c-7205-374e-b9e8-7c72d8f158ae','019adbd4-0ac6-73c1-aab2-48530e36a7fc',3,NULL,'EMP-CAL001-0010','Chigozie Eze','chigozie.eze.10@sweettooth.com','+234-885-624-7857','2643 Godfrey Parkway Apt. 976, Calabar, Cross River State, Nigeria','2001-11-13','male','Nigerian','Ngozi Ogunleye','+234-856-686-9450','2023-06-25',NULL,'on_probation','2025-12-08','afternoon',227060.95,NULL,'TIN-22351552','6261881000',NULL,NULL,'2025-08-16',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40','aISCx0FeStdeQW7aySbWMVRAzlYfmj16bedg68B8ufcV6HRPQr24QenjWqab',0),
('0f46b4ad-2658-34b4-aa9a-1cdc6112d84a','019adbd4-0b13-726e-9aa8-3f26e5acec1b',1,NULL,'EMP-ABJ004-0065','Abubakar Eze','abubakar.eze.65@sweettooth.com','+234-896-370-3326','8020 Adrien Center Apt. 248, Abuja, FCT State, Nigeria','1996-08-22','male','Nigerian','Kemi Nwankwo','+234-852-265-6821','2023-06-13',NULL,'active',NULL,'flexible',100600.41,NULL,'TIN-86856757','9546889671',NULL,NULL,'2025-11-14',4.2,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('100752ac-d21c-3dcc-907f-4b478a3b9733','019adbd4-0aec-707f-b4e0-ea5ba04b7612',5,NULL,'EMP-PHC002-0036','Fatima Adebayo','fatima.adebayo.36@sweettooth.com','+234-900-562-7428','2336 Monique Ridges, Port Harcourt, Rivers State, Nigeria','1981-04-13','female','Nigerian','Abubakar Bello','+234-883-251-8744','2023-06-12',NULL,'active',NULL,'morning',87729.56,NULL,'TIN-86420266','6837825796',NULL,NULL,'2025-06-18',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('15b9b0d3-b46a-3793-92c3-a31bdee6db5d','019adbd4-0ac6-73c1-aab2-48530e36a7fc',4,NULL,'EMP-CAL001-0014','Hauwa Okor','hauwa.okoro.14@sweettooth.com','+234-867-523-5116','970 Beer Lock Apt. 820, Calabar, Cross River State, Nigeria','2001-02-21','female','Nigerian','Ibrahim Okoro','+234-816-837-9379','2024-02-08',NULL,'active',NULL,'flexible',218354.12,NULL,'TIN-52853539','8278549000',NULL,'employee-photos/PopMZv4EJtTdqOQhNJ0DQ1tTN1sG1d8pSW6saIDB.jpg','2025-07-18',3.8,'2025-12-01 20:31:40','2025-12-02 11:17:10',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('1745cbb7-10cd-3f25-a238-94529ae5ca7d','019adbd4-0aec-707f-b4e0-ea5ba04b7612',5,NULL,'EMP-PHC002-0037','Amina Johnson','amina.johnson.37@sweettooth.com','+234-809-213-4400','28397 Terrance Light, Port Harcourt, Rivers State, Nigeria','1985-12-03','female','Nigerian','Emeka Adebayo','+234-870-499-7777','2024-06-06',NULL,'active',NULL,'flexible',196698.12,NULL,'TIN-16653271','7313330454',NULL,NULL,'2025-06-17',4.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('1f58d5d0-c31f-375c-a6b2-03e4a5f599ff','019adbd4-0b07-7018-928c-e49d13068b9b',3,NULL,'EMP-LAG003-0051','Chigozie Aliyu','chigozie.aliyu.51@sweettooth.com','+234-902-197-3729','5355 Murphy Avenue, Lagos, Lagos State, Nigeria','1990-02-07','male','Nigerian','Ada Adebayo','+234-821-716-9250','2025-03-11',NULL,'active',NULL,'afternoon',98347.17,NULL,'TIN-35125361','2224384382',NULL,NULL,'2025-08-03',4.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('220d8237-39a2-3537-8c57-7b56280eda39','019adbd4-0b07-7018-928c-e49d13068b9b',4,NULL,'EMP-LAG003-0056','Ibrahim Ogunleye','ibrahim.ogunleye.56@sweettooth.com','+234-818-992-1802','78879 Larry Burg Suite 438, Lagos, Lagos State, Nigeria','1984-08-27','male','Nigerian','Nneka Ogunleye','+234-830-181-9258','2023-02-18',NULL,'active',NULL,'morning',193550.23,NULL,'TIN-68459173','9376787611',NULL,NULL,'2025-06-16',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('2702b844-5912-32e3-aff2-44ab1c3b417b','019adbd4-0b13-726e-9aa8-3f26e5acec1b',3,NULL,'EMP-ABJ004-0073','Kunle Williams','kunle.williams.73@sweettooth.com','+234-806-403-1201','5879 Denesik Crescent, Abuja, FCT State, Nigeria','1991-05-09','male','Nigerian','Nneka Mohammed','+234-856-902-1490','2024-05-29',NULL,'active',NULL,'morning',314969.23,NULL,'TIN-30013980','6453281087',NULL,NULL,'2025-06-08',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('27c3e77a-72c8-30a1-98c5-380d8a039bd3','019adbd4-0b07-7018-928c-e49d13068b9b',7,NULL,'EMP-LAG003-0063','Nneka Williams','nneka.williams.63@sweettooth.com','+234-893-392-1321','1298 Ward Shore, Lagos, Lagos State, Nigeria','1991-11-16','female','Nigerian','Obinna Adebayo','+234-881-228-6440','2025-10-01',NULL,'on_probation','2025-12-24','rotating',182203.61,NULL,'TIN-11412305','4667755548',NULL,NULL,'2025-07-31',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('2af46333-91e5-36d9-83da-c41ac07171cd','019adbd4-0b07-7018-928c-e49d13068b9b',2,NULL,'EMP-LAG003-0048','Emeka Okafor','emeka.okafor.48@sweettooth.com','+234-811-208-8911','77706 Schuster Stream, Lagos, Lagos State, Nigeria','1984-01-10','male','Nigerian','Amina Ogunleye','+234-906-792-3428','2025-04-06',NULL,'active',NULL,'flexible',145325.35,NULL,'TIN-73684894','8866778170','Lactose',NULL,'2025-07-15',4.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('2c8e6f2a-0526-3fc7-9496-ad7741db226d','019adbd4-0b1e-7356-8544-47bf94c8ffed',5,NULL,'EMP-ENU005-0100','Ibrahim Okafor','ibrahim.okafor.100@sweettooth.com','+234-859-640-9612','26461 Bruen Landing, Enugu, Enugu State, Nigeria','1994-02-03','male','Nigerian','Nneka Okafor','+234-840-538-8280','2025-03-06',NULL,'on_probation','2026-02-22','morning',139668.96,NULL,'TIN-82294351','1757695858','Shellfish',NULL,'2025-06-23',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('2d142047-7ddd-35f6-b9a6-3fd3727460c8','019adbd4-0b13-726e-9aa8-3f26e5acec1b',4,NULL,'EMP-ABJ004-0076','Oluwaseun Nwankwo','oluwaseun.nwankwo.76@sweettooth.com','+234-872-589-1338','85046 Beth Manors, Abuja, FCT State, Nigeria','1985-02-06','male','Nigerian','Ngozi Eze','+234-823-417-3843','2024-12-25',NULL,'active',NULL,'rotating',206039.32,NULL,'TIN-29104423','7645201082',NULL,NULL,'2025-09-22',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('2ef92350-2a39-3cfd-8a40-0d5007c310b4','019adbd4-0ac6-73c1-aab2-48530e36a7fc',3,NULL,'EMP-CAL001-0008','Abubakar Bello','abubakar.bello.8@sweettooth.com','+234-884-897-6100','6422 Litzy Glens Apt. 007, Calabar, Cross River State, Nigeria','1982-09-12','male','Nigerian','Folake Aliyu','+234-875-727-7869','2025-03-03',NULL,'active',NULL,'rotating',200945.87,NULL,'TIN-71400560','2887923349',NULL,NULL,'2025-11-06',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('31d3f5ea-87b2-364b-8e75-ed5452953a3a','019adbd4-0aec-707f-b4e0-ea5ba04b7612',2,NULL,'EMP-PHC002-0028','Folake Aliyu','folake.aliyu.28@sweettooth.com','+234-839-313-5916','411 Cronin Track Suite 354, Port Harcourt, Rivers State, Nigeria','1981-11-16','female','Nigerian','Tunde Adebayo','+234-872-833-7196','2024-05-24',NULL,'active',NULL,'rotating',116473.14,NULL,'TIN-50361179','2957062725','Peanuts',NULL,'2025-09-07',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('35eac18f-0c5e-3aa8-8c30-4a160a42dfae','019adbd4-0aec-707f-b4e0-ea5ba04b7612',6,NULL,'EMP-PHC002-0040','Fatima Adebayo','fatima.adebayo.40@sweettooth.com','+234-899-319-8140','3015 Balistreri Station Suite 670, Port Harcourt, Rivers State, Nigeria','1991-08-20','female','Nigerian','Kunle Okafor','+234-828-374-1284','2024-11-02',NULL,'on_probation','2025-12-25','rotating',136647.96,NULL,'TIN-65214905','4301033962',NULL,NULL,'2025-06-27',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('3607fa20-0222-33a6-a1f9-da8a386152a0','019adbd4-0ac6-73c1-aab2-48530e36a7fc',6,NULL,'EMP-CAL001-0018','Kunle Chukwu','kunle.chukwu.18@sweettooth.com','+234-823-724-6560','68743 Aglae Flats, Calabar, Cross River State, Nigeria','1984-01-14','male','Nigerian','Hauwa Okafor','+234-866-937-3501','2023-06-22',NULL,'on_probation','2026-01-09','afternoon',295472.05,NULL,'TIN-60702518','5189035918',NULL,NULL,'2025-11-25',3.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('37b32b0e-e9fa-3436-924b-9b59eca605d6','019adbd4-0ac6-73c1-aab2-48530e36a7fc',2,NULL,'EMP-CAL001-0007','Kunle Mohammed','kunle.mohammed.7@sweettooth.com','+234-862-845-7226','184 Deion Hills Suite 361, Calabar, Cross River State, Nigeria','1997-10-15','male','Nigerian','Amina Bello','+234-828-665-7375','2024-02-24',NULL,'active',NULL,'morning',290339.66,NULL,'TIN-29056773','9874099685','Peanuts',NULL,'2025-10-13',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('3bf1f838-e106-30bb-ac75-c9c2b8b0cc0c','019adbd4-0aec-707f-b4e0-ea5ba04b7612',1,NULL,'EMP-PHC002-0023','Chigozie Bello','chigozie.bello.23@sweettooth.com','+234-840-870-8498','124 Julius Manor Suite 801, Port Harcourt, Rivers State, Nigeria','1999-10-10','male','Nigerian','Amina Bello','+234-839-111-2801','2024-12-12',NULL,'active',NULL,'morning',256449.76,NULL,'TIN-39039573','7849167275',NULL,NULL,'2025-11-13',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('3d989e41-b8ba-314e-98da-4dd0e0c5b7aa','019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,NULL,'EMP-CAL001-0003','Kemi Mohammed','kemi.mohammed.3@sweettooth.com','+234-870-369-2041','1635 Ewald Manor, Calabar, Cross River State, Nigeria','2003-08-17','female','Nigerian','Chigozie Johnson','+234-816-857-9984','2023-02-12',NULL,'active',NULL,'rotating',167308.34,NULL,'TIN-45026557','0444122702',NULL,NULL,'2025-10-14',3.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('3e1289ba-e385-3b84-8243-58093af0fe80','019adbd4-0ac6-73c1-aab2-48530e36a7fc',2,NULL,'EMP-CAL001-0006','Ibrahim Johnson','ibrahim.johnson.6@sweettooth.com','+234-875-774-3040','763 Orland Path Apt. 781, Calabar, Cross River State, Nigeria','1985-06-17','male','Nigerian','Ada Adebayo','+234-885-460-4632','2023-12-06',NULL,'on_probation','2026-01-21','afternoon',183156.34,NULL,'TIN-51182872','9340654246',NULL,NULL,'2025-11-11',3.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('415e62d9-1aa2-3a62-a0c1-3b0f55ef5c15','019adbd4-0b07-7018-928c-e49d13068b9b',1,NULL,'EMP-LAG003-0045','Yusuf Aliyu','yusuf.aliyu.45@sweettooth.com','+234-823-270-2426','8191 Wyatt Fort Apt. 083, Lagos, Lagos State, Nigeria','1985-10-27','male','Nigerian','Amina Johnson','+234-900-380-6589','2024-02-16',NULL,'active',NULL,'flexible',251459.04,NULL,'TIN-69026633','3497436751',NULL,NULL,'2025-09-18',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('41cfcb11-97d0-37ed-850a-94fc992a8c49','019adbd4-0aec-707f-b4e0-ea5ba04b7612',6,NULL,'EMP-PHC002-0039','Chioma Johnson','chioma.johnson.39@sweettooth.com','+234-887-353-7539','5207 Wendy Square Apt. 957, Port Harcourt, Rivers State, Nigeria','1988-10-07','female','Nigerian','Obinna Adebayo','+234-820-843-1676','2023-12-14',NULL,'on_probation','2026-02-26','afternoon',338087.61,NULL,'TIN-72584495','4759146652',NULL,NULL,'2025-07-28',5.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('42503b0d-68c3-374a-8f73-3dcc1b392ab8','019adbd4-0b1e-7356-8544-47bf94c8ffed',1,NULL,'EMP-ENU005-0088','Oluwaseun Aliyu','oluwaseun.aliyu.88@sweettooth.com','+234-822-607-6548','2347 Orville Burg Apt. 197, Enugu, Enugu State, Nigeria','2002-10-18','male','Nigerian','Blessing Johnson','+234-875-118-3558','2023-01-22',NULL,'active',NULL,'afternoon',148615.18,NULL,'TIN-43309288','6896392679',NULL,NULL,'2025-07-10',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('439937b1-48ac-3b35-84d8-bf17cd5a21cf','019adbd4-0aec-707f-b4e0-ea5ba04b7612',1,NULL,'EMP-PHC002-0025','Kunle Nwankwo','kunle.nwankwo.25@sweettooth.com','+234-843-369-7415','1098 Dickinson Spring, Port Harcourt, Rivers State, Nigeria','1989-06-04','male','Nigerian','Blessing Okafor','+234-890-130-9932','2023-08-04',NULL,'active',NULL,'rotating',165754.29,NULL,'TIN-45819801','8032646030','None',NULL,'2025-10-15',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('441b94c6-ef2e-3a2e-b81c-37c42eb5383c','019adbd4-0b13-726e-9aa8-3f26e5acec1b',5,NULL,'EMP-ABJ004-0079','Abubakar Mohammed','abubakar.mohammed.79@sweettooth.com','+234-890-493-1061','61366 Blick Course Apt. 639, Abuja, FCT State, Nigeria','2003-04-12','male','Nigerian','Kemi Okafor','+234-834-970-2401','2025-03-26',NULL,'on_probation','2026-02-16','rotating',170241.57,NULL,'TIN-40223884','9761333834',NULL,NULL,'2025-08-28',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('44639c67-51ca-32d8-8f0f-3c75863a3ffa','019adbd4-0aec-707f-b4e0-ea5ba04b7612',3,NULL,'EMP-PHC002-0031','Ngozi Williams','ngozi.williams.31@sweettooth.com','+234-849-627-6215','59789 Imogene Isle, Port Harcourt, Rivers State, Nigeria','2002-05-18','female','Nigerian','Kunle Mohammed','+234-830-257-5611','2025-03-14',NULL,'on_probation','2026-02-08','rotating',129969.01,NULL,'TIN-71147186','3401011139',NULL,NULL,'2025-06-26',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('45e4db87-284b-3870-8979-04e1fa9aaae8','019adbd4-0ac6-73c1-aab2-48530e36a7fc',4,NULL,'EMP-CAL001-0012','Obinna Mohammed','obinna.mohammed.12@sweettooth.com','+234-851-593-1681','8495 Kulas Bridge Apt. 191, Calabar, Cross River State, Nigeria','1992-10-03','male','Nigerian','Nneka Nwankwo','+234-829-201-4560','2025-03-29',NULL,'active',NULL,'rotating',310964.90,NULL,'TIN-17126425','9471467541',NULL,NULL,'2025-12-01',4.4,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('4acd63a3-cac5-39ca-abb3-0920a9cd0b4c','019adbd4-0b1e-7356-8544-47bf94c8ffed',7,NULL,'EMP-ENU005-0105','Kemi Okafor','kemi.okafor.105@sweettooth.com','+234-903-823-3068','3488 Johnston Hills Suite 548, Enugu, Enugu State, Nigeria','1988-03-03','female','Nigerian','Abubakar Eze','+234-819-452-8557','2023-12-04',NULL,'active',NULL,'flexible',107659.22,NULL,'TIN-82903806','9083906414','Peanuts',NULL,'2025-10-28',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('4e224a26-9875-373a-b82d-b82e3fd758c9','019adbd4-0aec-707f-b4e0-ea5ba04b7612',4,NULL,'EMP-PHC002-0033','Kunle Chukwu','kunle.chukwu.33@sweettooth.com','+234-814-689-6929','18665 Hermiston Vista, Port Harcourt, Rivers State, Nigeria','1984-07-14','male','Nigerian','Nneka Okoro','+234-802-411-5167','2023-05-25',NULL,'active',NULL,'morning',108989.63,NULL,'TIN-68162044','5263523638',NULL,NULL,'2025-11-19',4.5,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('4ea977c8-735d-3936-b040-b643393378b6','019adbd4-0aec-707f-b4e0-ea5ba04b7612',3,NULL,'EMP-PHC002-0030','Amina Chukwu','amina.chukwu.30@sweettooth.com','+234-888-127-5030','4783 Nina Plains Apt. 025, Port Harcourt, Rivers State, Nigeria','1987-02-05','female','Nigerian','Abubakar Williams','+234-805-348-6162','2025-04-23',NULL,'active',NULL,'rotating',92609.54,NULL,'TIN-25804697','5152451020',NULL,NULL,'2025-06-15',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('4f2efabd-14d8-36de-96ec-94cd48751e85','019adbd4-0b07-7018-928c-e49d13068b9b',3,NULL,'EMP-LAG003-0050','Kemi Chukwu','kemi.chukwu.50@sweettooth.com','+234-876-147-7173','793 Jeramy Meadow Suite 902, Lagos, Lagos State, Nigeria','2001-01-10','female','Nigerian','Obinna Eze','+234-904-118-7707','2025-07-01',NULL,'active',NULL,'flexible',338234.90,NULL,'TIN-21757840','8613443082',NULL,NULL,'2025-07-18',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('518ea6dc-8459-3c3f-93a2-95bb67d9e61c','019adbd4-0b1e-7356-8544-47bf94c8ffed',1,NULL,'EMP-ENU005-0086','Nneka Johnson','nneka.johnson.86@sweettooth.com','+234-845-960-8334','21652 Hagenes Via Apt. 743, Enugu, Enugu State, Nigeria','1985-06-19','female','Nigerian','Obinna Bello','+234-872-224-8730','2024-06-10',NULL,'active',NULL,'rotating',274302.68,NULL,'TIN-82829729','0830702179','None',NULL,'2025-07-13',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('51af9f3b-bf3d-3fb1-bf04-4e39a1a059fa','019adbd4-0b07-7018-928c-e49d13068b9b',5,NULL,'EMP-LAG003-0057','Yusuf Nwankwo','yusuf.nwankwo.57@sweettooth.com','+234-825-157-5641','6264 Weber Plain, Lagos, Lagos State, Nigeria','1986-01-19','male','Nigerian','Chioma Okafor','+234-814-888-2111','2023-02-03',NULL,'active',NULL,'morning',123703.15,NULL,'TIN-49941454','7236233223',NULL,NULL,'2025-11-07',3.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('522b8454-e175-3cea-aeab-c6c8717a1728','019adbd4-0aec-707f-b4e0-ea5ba04b7612',4,NULL,'EMP-PHC002-0035','Tunde Ogunleye','tunde.ogunleye.35@sweettooth.com','+234-901-344-4425','58354 Wilhelmine Inlet, Port Harcourt, Rivers State, Nigeria','1984-09-30','male','Nigerian','Chioma Mohammed','+234-894-843-4304','2023-08-31',NULL,'active',NULL,'rotating',279666.08,NULL,'TIN-48297620','2968270625',NULL,NULL,'2025-09-22',4.4,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('52a5f02d-2ef6-360b-a1b8-27615f406647','019adbd4-0ac6-73c1-aab2-48530e36a7fc',5,NULL,'EMP-CAL001-0016','Emeka Aliyu','emeka.aliyu.16@sweettooth.com','+234-901-370-5938','58105 Rutherford Ramp, Calabar, Cross River State, Nigeria','1986-07-28','male','Nigerian','Kemi Adebayo','+234-864-991-5709','2024-12-12',NULL,'on_probation','2026-01-29','flexible',124573.32,NULL,'TIN-95061075','4445852453',NULL,NULL,'2025-06-27',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('53af11df-f62e-3a07-b8cc-e05368efa7a5','019adbd4-0b1e-7356-8544-47bf94c8ffed',6,NULL,'EMP-ENU005-0102','Abubakar Okoro','abubakar.okoro.102@sweettooth.com','+234-833-980-6107','175 Hirthe Forge, Enugu, Enugu State, Nigeria','1997-02-27','male','Nigerian','Ngozi Mohammed','+234-830-866-3874','2025-02-12',NULL,'active',NULL,'afternoon',229980.61,NULL,'TIN-61584058','7312279923',NULL,NULL,'2025-11-13',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('549e3510-564d-3f1f-b1cb-e93a49800b21','019adbd4-0b1e-7356-8544-47bf94c8ffed',1,NULL,'EMP-ENU005-0085','Oluwaseun Eze','oluwaseun.eze.85@sweettooth.com','+234-830-773-4492','2372 Audreanne Point Suite 419, Enugu, Enugu State, Nigeria','2002-06-03','male','Nigerian','Ada Nwankwo','+234-876-793-8079','2023-05-05',NULL,'active',NULL,'flexible',295142.94,NULL,'TIN-80181505','9001112278',NULL,NULL,'2025-07-15',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('553313ad-62b0-375d-8fed-fced4be7cc4a','019adbd4-0ac6-73c1-aab2-48530e36a7fc',3,NULL,'EMP-CAL001-0009','Amina Okoro','amina.okoro.9@sweettooth.com','+234-879-874-5183','663 Alfredo Underpass, Calabar, Cross River State, Nigeria','1996-06-20','female','Nigerian','Yusuf Eze','+234-834-603-4568','2023-06-16',NULL,'on_probation','2026-01-07','rotating',345138.52,NULL,'TIN-50823579','3431850409',NULL,NULL,'2025-06-28',3.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('57e06f9f-0b19-3c60-9101-43464cebf324','019adbd4-0b07-7018-928c-e49d13068b9b',4,NULL,'EMP-LAG003-0053','Kunle Ogunleye','kunle.ogunleye.53@sweettooth.com','+234-858-881-3991','12718 Kemmer Extensions, Lagos, Lagos State, Nigeria','1981-10-01','male','Nigerian','Amina Okoro','+234-831-417-2537','2023-09-04',NULL,'active',NULL,'flexible',127088.90,NULL,'TIN-26193306','7296998501',NULL,NULL,'2025-11-17',4.4,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('5880f31f-b4b3-3b42-b4fc-39343bbc55f7','019adbd4-0b13-726e-9aa8-3f26e5acec1b',4,NULL,'EMP-ABJ004-0077','Kunle Aliyu','kunle.aliyu.77@sweettooth.com','+234-824-167-3750','1216 Christopher Lodge Apt. 315, Abuja, FCT State, Nigeria','2001-01-20','male','Nigerian','Amina Ogunleye','+234-846-966-2217','2024-02-08',NULL,'on_probation','2025-12-31','flexible',183774.23,NULL,'TIN-84784898','5200833032',NULL,NULL,'2025-11-21',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('59cf7d06-4cc7-380e-9b17-ef929d9e6474','019adbd4-0b07-7018-928c-e49d13068b9b',5,NULL,'EMP-LAG003-0058','Ibrahim Okoro','ibrahim.okoro.58@sweettooth.com','+234-832-462-2020','4726 Hegmann Port, Lagos, Lagos State, Nigeria','1992-09-20','male','Nigerian','Hauwa Adebayo','+234-870-724-7984','2024-11-23',NULL,'active',NULL,'rotating',174800.65,NULL,'TIN-31431611','6120750986',NULL,NULL,'2025-06-27',5.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('5d034879-b9fc-3245-a6c7-4c579871b873','019adbd4-0aec-707f-b4e0-ea5ba04b7612',2,NULL,'EMP-PHC002-0027','Fatima Bello','fatima.bello.27@sweettooth.com','+234-850-161-1832','17823 Walton Groves, Port Harcourt, Rivers State, Nigeria','1999-05-10','female','Nigerian','Yusuf Okafor','+234-819-167-7188','2023-02-21',NULL,'on_probation','2026-02-05','morning',263013.93,NULL,'TIN-31125979','0571603161','Peanuts',NULL,'2025-07-31',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('61312a1f-20ea-34dc-b8af-61c351557968','019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,NULL,'EMP-CAL001-0001','Folake Eze','folake.eze.1@sweettooth.com','+234-876-866-7492','3757 Kemmer Turnpike, Calabar, Cross River State, Nigeria','1989-12-10','female','Nigerian','Obinna Adebayo','+234-877-184-1694','2023-06-27',NULL,'on_probation','2026-02-22','morning',223431.34,NULL,'TIN-69477012','2434094634',NULL,NULL,'2025-07-12',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('6201481e-e27c-360e-9362-78c6405dc52a','019adbd4-0b07-7018-928c-e49d13068b9b',2,NULL,'EMP-LAG003-0047','Emeka Johnson','emeka.johnson.47@sweettooth.com','+234-843-830-1925','5048 Rice Drives, Lagos, Lagos State, Nigeria','2001-02-07','male','Nigerian','Nneka Okafor','+234-858-355-6440','2024-01-09',NULL,'on_probation','2026-01-02','morning',327398.94,NULL,'TIN-70691789','0292735400',NULL,NULL,'2025-09-18',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('66991f66-cd04-388e-8ced-1688f9acdbd0','019adbd4-0aec-707f-b4e0-ea5ba04b7612',2,NULL,'EMP-PHC002-0026','Obinna Williams','obinna.williams.26@sweettooth.com','+234-838-237-7272','6416 Kshlerin Ramp, Port Harcourt, Rivers State, Nigeria','1992-10-23','male','Nigerian','Ada Okafor','+234-870-761-1298','2024-04-04',NULL,'active',NULL,'flexible',174090.46,NULL,'TIN-75026404','9732193874',NULL,NULL,'2025-10-11',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('694b5484-3aa1-304d-ae57-0eed2a57fd74','019adbd4-0b07-7018-928c-e49d13068b9b',1,NULL,'EMP-LAG003-0046','Chukwuemeka Mohammed','chukwuemeka.mohammed.46@sweettooth.com','+234-891-440-4854','486 Jonatan Skyway, Lagos, Lagos State, Nigeria','1998-01-30','male','Nigerian','Folake Nwankwo','+234-873-806-7212','2025-02-28',NULL,'on_probation','2025-12-20','flexible',324904.70,NULL,'TIN-51083270','1847462622',NULL,NULL,'2025-08-25',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('6d0f0772-072f-32f1-9f03-127c61a8829f','019adbd4-0b13-726e-9aa8-3f26e5acec1b',3,NULL,'EMP-ABJ004-0072','Ibrahim Bello','ibrahim.bello.72@sweettooth.com','+234-819-725-7862','454 Dashawn Crossing Apt. 941, Abuja, FCT State, Nigeria','1988-02-25','male','Nigerian','Fatima Okafor','+234-889-405-3827','2024-07-24',NULL,'active',NULL,'rotating',228527.13,NULL,'TIN-85811090','0769276975',NULL,NULL,'2025-09-04',3.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('6f7047f4-f24a-3808-b8f5-c292942dccac','019adbd4-0b07-7018-928c-e49d13068b9b',6,NULL,'EMP-LAG003-0060','Ngozi Johnson','ngozi.johnson.60@sweettooth.com','+234-856-212-6442','6875 Reichert Fort Apt. 186, Lagos, Lagos State, Nigeria','1988-10-02','female','Nigerian','Kunle Mohammed','+234-819-888-7798','2023-03-28',NULL,'active',NULL,'rotating',169247.09,NULL,'TIN-90383860','7999646904',NULL,NULL,'2025-06-04',4.2,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('72812433-e623-3d39-b488-214e34f99e23','019adbd4-0b1e-7356-8544-47bf94c8ffed',1,NULL,'EMP-ENU005-0087','Kunle Mohammed','kunle.mohammed.87@sweettooth.com','+234-852-706-2525','91973 Ashleigh Bridge, Enugu, Enugu State, Nigeria','1984-03-27','male','Nigerian','Amina Okoro','+234-873-490-1450','2024-03-15',NULL,'active',NULL,'flexible',144254.07,NULL,'TIN-95752722','6371175849',NULL,NULL,'2025-08-05',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('73265c6a-6c4b-3660-8de9-8b1aa4c80979','019adbd4-0b13-726e-9aa8-3f26e5acec1b',2,NULL,'EMP-ABJ004-0068','Folake Johnson','folake.johnson.68@sweettooth.com','+234-854-608-8484','792 Toy Cliff Suite 609, Abuja, FCT State, Nigeria','1983-03-28','female','Nigerian','Tunde Aliyu','+234-860-552-5600','2024-04-14',NULL,'active',NULL,'flexible',274994.73,NULL,'TIN-27897128','9427311124',NULL,NULL,'2025-08-27',4.2,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('7f967123-0a10-3715-9539-e14a7e3caf61','019adbd4-0b1e-7356-8544-47bf94c8ffed',2,NULL,'EMP-ENU005-0089','Yusuf Okafor','yusuf.okafor.89@sweettooth.com','+234-871-576-4372','4037 Camren Roads, Enugu, Enugu State, Nigeria','2000-03-05','male','Nigerian','Nneka Chukwu','+234-831-484-2689','2024-09-28',NULL,'active',NULL,'afternoon',192787.17,NULL,'TIN-23947140','1229485292',NULL,NULL,'2025-11-05',4.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('7ff6be45-1b65-3813-98fe-a38f7bc4fbbd','019adbd4-0b13-726e-9aa8-3f26e5acec1b',7,NULL,'EMP-ABJ004-0083','Ada Eze','ada.eze.83@sweettooth.com','+234-859-486-4702','61798 Erick Track, Abuja, FCT State, Nigeria','1983-01-01','female','Nigerian','Chigozie Williams','+234-862-256-6448','2022-12-06',NULL,'active',NULL,'rotating',246104.30,NULL,'TIN-62399337','4706559538',NULL,NULL,'2025-10-25',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('83925e11-9245-30a5-823a-ae02b23c52cf','019adbd4-0b07-7018-928c-e49d13068b9b',5,NULL,'EMP-LAG003-0059','Folake Eze','folake.eze.59@sweettooth.com','+234-828-729-1564','1979 Gisselle Ridge Apt. 316, Lagos, Lagos State, Nigeria','1998-12-17','female','Nigerian','Tunde Bello','+234-866-307-6953','2024-11-19',NULL,'active',NULL,'rotating',305397.06,NULL,'TIN-98529847','6804382298','Peanuts',NULL,'2025-06-08',4.5,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('8efe9b66-7c30-388b-867e-8d4342e53b21','019adbd4-0b13-726e-9aa8-3f26e5acec1b',5,NULL,'EMP-ABJ004-0080','Amina Mohammed','amina.mohammed.80@sweettooth.com','+234-897-364-1513','2855 Senger Mountain, Abuja, FCT State, Nigeria','1996-12-07','female','Nigerian','Oluwaseun Johnson','+234-909-449-2312','2023-04-12',NULL,'active',NULL,'rotating',186476.73,NULL,'TIN-49383743','6271627421',NULL,NULL,'2025-10-26',4.5,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('900cdc0b-ee25-3cef-a560-91fe3c7a002e','019adbd4-0b13-726e-9aa8-3f26e5acec1b',1,NULL,'EMP-ABJ004-0067','Obinna Adebayo','obinna.adebayo.67@sweettooth.com','+234-888-154-4640','542 Alf Walk, Abuja, FCT State, Nigeria','1995-10-10','male','Nigerian','Amina Chukwu','+234-831-239-4665','2023-01-26',NULL,'active',NULL,'afternoon',310707.59,NULL,'TIN-62118086','3064641071',NULL,NULL,'2025-06-19',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('90e042f1-2913-3af7-9fbe-ca7958bfcc40','019adbd4-0ac6-73c1-aab2-48530e36a7fc',4,NULL,'EMP-CAL001-0011','Chioma Williams','chioma.williams.11@sweettooth.com','+234-883-695-2519','304 Don Loaf, Calabar, Cross River State, Nigeria','1982-08-04','female','Nigerian','Chigozie Eze','+234-851-594-7952','2024-07-17',NULL,'on_probation','2026-01-20','flexible',96080.02,NULL,'TIN-76127764','0675690977',NULL,NULL,'2025-10-22',3.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('9b663fb5-a2c1-3142-8d3b-4e9ea7865dd2','019adbd4-0ac6-73c1-aab2-48530e36a7fc',2,NULL,'EMP-CAL001-0005','Yusuf Eze','yusuf.eze.5@sweettooth.com','+234-804-504-3751','58610 Wehner Track, Calabar, Cross River State, Nigeria','1987-04-20','male','Nigerian','Hauwa Ogunleye','+234-876-790-4733','2025-05-01',NULL,'active',NULL,'flexible',149165.16,NULL,'TIN-45352397','2608427462',NULL,NULL,'2025-11-10',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('9e498b7f-a6f1-3264-9776-467b905e9bc1','019adbd4-0b07-7018-928c-e49d13068b9b',3,NULL,'EMP-LAG003-0052','Folake Okafor','folake.okafor.52@sweettooth.com','+234-908-796-4073','990 Klocko Knolls Apt. 298, Lagos, Lagos State, Nigeria','1983-05-28','female','Nigerian','Kunle Bello','+234-865-518-5318','2023-05-28',NULL,'on_probation','2026-02-02','flexible',221014.82,NULL,'TIN-37061476','9111302634',NULL,NULL,'2025-11-18',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('a0582faf-9586-3ecf-b5d0-f6cd07058e64','019adbd4-0b13-726e-9aa8-3f26e5acec1b',4,NULL,'EMP-ABJ004-0074','Chigozie Okafor','chigozie.okafor.74@sweettooth.com','+234-854-244-2536','7181 Flossie Fall Apt. 458, Abuja, FCT State, Nigeria','1984-09-09','male','Nigerian','Amina Aliyu','+234-867-850-6781','2023-02-06',NULL,'active',NULL,'afternoon',83649.29,NULL,'TIN-87632855','8398876735',NULL,NULL,'2025-06-17',4.4,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('a3f73790-38ea-396c-b9e2-66df082296b1','019adbd4-0b07-7018-928c-e49d13068b9b',7,NULL,'EMP-LAG003-0062','Hauwa Mohammed','hauwa.mohammed.62@sweettooth.com','+234-801-831-1011','68257 Ondricka Place Suite 478, Lagos, Lagos State, Nigeria','1983-01-13','female','Nigerian','Abubakar Mohammed','+234-859-515-6375','2025-04-14',NULL,'active',NULL,'morning',311181.52,NULL,'TIN-78245356','0161735865',NULL,NULL,'2025-08-19',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('a481af38-64a4-3f86-beea-256ebfec10ac','019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,NULL,'EMP-CAL001-0021','Yusuf Williams','yusuf.williams.21@sweettooth.com','+234-820-983-2480','1788 McClure Squares, Calabar, Cross River State, Nigeria','1982-10-24','male','Nigerian','Folake Williams','+234-886-774-1061','2025-09-12',NULL,'on_probation','2025-12-10','morning',167902.51,NULL,'TIN-59959270','2354778843',NULL,NULL,'2025-07-23',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('a5f9a6d5-edb4-36ea-9d81-87042220de40','019adbd4-0ac6-73c1-aab2-48530e36a7fc',4,NULL,'EMP-CAL001-0013','Ibrahim Johnson','ibrahim.johnson.13@sweettooth.com','+234-864-998-9884','86581 Lupe Valley Apt. 905, Calabar, Cross River State, Nigeria','1981-08-24','male','Nigerian','Blessing Ogunleye','+234-861-143-8303','2025-04-15',NULL,'on_probation','2026-02-13','morning',267276.89,NULL,'TIN-11851010','4075544775',NULL,NULL,'2025-07-04',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('a809e16d-e423-36c2-bb00-68f41bb4a5b6','019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,NULL,'EMP-CAL001-0002','Chukwuemeka Okafor','chukwuemeka.okafor.2@sweettooth.com','+234-881-828-4879','979 Sabrina Club, Calabar, Cross River State, Nigeria','1983-12-20','male','Nigerian','Hauwa Okafor','+234-821-967-8310','2023-04-28',NULL,'active',NULL,'rotating',169468.12,NULL,'TIN-30408813','2100956223',NULL,NULL,'2025-08-08',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('a96b155e-227b-3e99-ab66-aabd7df7dab2','019adbd4-0aec-707f-b4e0-ea5ba04b7612',3,NULL,'EMP-PHC002-0029','Oluwaseun Johnson','oluwaseun.johnson.29@sweettooth.com','+234-820-139-5620','1899 Mann Crossing, Port Harcourt, Rivers State, Nigeria','2002-05-26','male','Nigerian','Kemi Okafor','+234-836-899-4888','2024-04-01',NULL,'on_probation','2026-03-01','morning',179026.24,NULL,'TIN-13523198','9408457171','Shellfish',NULL,'2025-08-28',3.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('a9f468ba-2865-3277-a5fb-9d4f46034317','019adbd4-0b07-7018-928c-e49d13068b9b',4,NULL,'EMP-LAG003-0055','Fatima Mohammed','fatima.mohammed.55@sweettooth.com','+234-861-468-2967','830 Wuckert Underpass, Lagos, Lagos State, Nigeria','2003-06-09','female','Nigerian','Chukwuemeka Mohammed','+234-826-812-6173','2025-10-25',NULL,'active',NULL,'flexible',96866.70,NULL,'TIN-71295745','8737215048',NULL,NULL,'2025-08-05',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('abab6f75-74ad-3d01-aa32-06517e0232a7','019adbd4-0aec-707f-b4e0-ea5ba04b7612',1,NULL,'EMP-PHC002-0022','Emeka Eze','emeka.eze.22@sweettooth.com','+234-858-427-5433','18030 Purdy Squares Suite 622, Port Harcourt, Rivers State, Nigeria','1990-09-24','male','Nigerian','Nneka Okafor','+234-891-677-2124','2025-07-21',NULL,'active',NULL,'morning',276109.08,NULL,'TIN-17190584','9726875304',NULL,NULL,'2025-10-22',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('adcf9dae-510a-372d-bbc4-df91b7d182b2','019adbd4-0b13-726e-9aa8-3f26e5acec1b',1,NULL,'EMP-ABJ004-0066','Blessing Okafor','blessing.okafor.66@sweettooth.com','+234-818-647-7676','1655 Lavina Parkways, Abuja, FCT State, Nigeria','2001-01-16','female','Nigerian','Emeka Aliyu','+234-897-850-1605','2024-05-11',NULL,'active',NULL,'morning',214440.87,NULL,'TIN-51783938','0705636738',NULL,NULL,'2025-06-30',3.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('aeb7f0df-3bea-3513-ad12-98239232ec2c','019adbd4-0b07-7018-928c-e49d13068b9b',2,NULL,'EMP-LAG003-0049','Hauwa Okafor','hauwa.okafor.49@sweettooth.com','+234-848-608-1387','1766 Jast Canyon Suite 568, Lagos, Lagos State, Nigeria','1989-07-01','female','Nigerian','Chukwuemeka Nwankwo','+234-801-763-8633','2025-06-07',NULL,'on_probation','2026-01-24','flexible',203737.36,NULL,'TIN-25658494','8309594588',NULL,NULL,'2025-07-16',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('af0cfc8f-c0fa-3a32-980e-438ce0457dbd','019adbd4-0b1e-7356-8544-47bf94c8ffed',3,NULL,'EMP-ENU005-0094','Obinna Ogunleye','obinna.ogunleye.94@sweettooth.com','+234-841-713-5519','7577 Sienna Summit Apt. 423, Enugu, Enugu State, Nigeria','1997-08-03','male','Nigerian','Fatima Mohammed','+234-802-339-6013','2024-10-04',NULL,'active',NULL,'flexible',292872.25,NULL,'TIN-46050106','2909534031',NULL,NULL,'2025-10-15',3.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('b00cadcd-c208-3f1b-8032-253755f9794f','019adbd4-0b13-726e-9aa8-3f26e5acec1b',2,NULL,'EMP-ABJ004-0070','Hauwa Johnson','hauwa.johnson.70@sweettooth.com','+234-818-566-7968','815 Nienow Locks, Abuja, FCT State, Nigeria','1994-07-17','female','Nigerian','Yusuf Ogunleye','+234-831-639-7719','2024-11-16',NULL,'on_probation','2025-12-02','afternoon',257532.30,NULL,'TIN-74236494','3401302600',NULL,NULL,'2025-09-09',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('b0e45146-6a68-34ef-8ab6-66a310f0d637','019adbd4-0ac6-73c1-aab2-48530e36a7fc',5,NULL,'EMP-CAL001-0015','Ngozi Aliyu','ngozi.aliyu.15@sweettooth.com','+234-806-896-4126','313 Jeffrey Fall, Calabar, Cross River State, Nigeria','1996-02-28','female','Nigerian','Chigozie Okoro','+234-801-354-9584','2024-06-18',NULL,'active',NULL,'flexible',270832.26,NULL,'TIN-60055360','0078224785',NULL,NULL,'2025-10-05',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('b3d72d23-3da8-3342-9296-7d8e7f8f52d1','019adbd4-0b13-726e-9aa8-3f26e5acec1b',5,NULL,'EMP-ABJ004-0078','Ada Aliyu','ada.aliyu.78@sweettooth.com','+234-865-224-4468','59638 Tatyana Common, Abuja, FCT State, Nigeria','1987-12-26','female','Nigerian','Chigozie Bello','+234-810-872-3600','2023-04-30',NULL,'on_probation','2026-02-18','morning',258496.55,NULL,'TIN-64311678','8679248866','None',NULL,'2025-09-03',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('b6b33dfc-d65f-3faa-9f3b-e9d785780d28','019adbd4-0b1e-7356-8544-47bf94c8ffed',6,NULL,'EMP-ENU005-0103','Ngozi Okafor','ngozi.okafor.103@sweettooth.com','+234-822-926-8236','73882 Gusikowski Bridge, Enugu, Enugu State, Nigeria','1988-04-15','female','Nigerian','Abubakar Eze','+234-806-436-7492','2025-01-10',NULL,'active',NULL,'flexible',111821.39,NULL,'TIN-25396130','0038212662',NULL,NULL,'2025-09-20',4.4,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('be6c2086-10ea-3c84-b636-c2d9af010b33','019adbd4-0b1e-7356-8544-47bf94c8ffed',3,NULL,'EMP-ENU005-0093','Amina Ogunleye','amina.ogunleye.93@sweettooth.com','+234-803-351-4791','366 Reanna Mission Suite 928, Enugu, Enugu State, Nigeria','1989-12-25','female','Nigerian','Tunde Mohammed','+234-909-116-2356','2025-01-25',NULL,'active',NULL,'rotating',231401.56,NULL,'TIN-36578071','5626971069',NULL,NULL,'2025-09-26',3.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('beedeb93-582a-3ed2-aa22-19960d76ac72','019adbd4-0b1e-7356-8544-47bf94c8ffed',4,NULL,'EMP-ENU005-0095','Amina Johnson','amina.johnson.95@sweettooth.com','+234-814-117-4432','994 Legros Square Suite 716, Enugu, Enugu State, Nigeria','1989-07-26','female','Nigerian','Tunde Johnson','+234-859-247-2821','2025-04-11',NULL,'active',NULL,'rotating',339266.80,NULL,'TIN-97117666','7526295276',NULL,NULL,'2025-08-25',3.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('bf967152-e312-3f86-af62-6a1517646941','019adbd4-0b1e-7356-8544-47bf94c8ffed',5,NULL,'EMP-ENU005-0099','Oluwaseun Okoro','oluwaseun.okoro.99@sweettooth.com','+234-903-628-1800','3576 Nader Locks, Enugu, Enugu State, Nigeria','1999-06-24','male','Nigerian','Fatima Aliyu','+234-859-788-9450','2023-10-31',NULL,'active',NULL,'afternoon',124453.51,NULL,'TIN-53773214','9863566737',NULL,NULL,'2025-09-19',4.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('c226ddf5-5fc5-3b3a-b891-4cc9f284552e','019adbd4-0b1e-7356-8544-47bf94c8ffed',4,NULL,'EMP-ENU005-0097','Blessing Johnson','blessing.johnson.97@sweettooth.com','+234-868-362-3889','83575 Treutel Burgs Suite 907, Enugu, Enugu State, Nigeria','1992-04-25','female','Nigerian','Ibrahim Adebayo','+234-810-826-8512','2023-07-14',NULL,'on_probation','2026-01-17','morning',170190.69,NULL,'TIN-89479169','4001983628',NULL,NULL,'2025-07-13',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('c48854eb-13e4-3af0-a232-b074780d79a7','019adbd4-0aec-707f-b4e0-ea5ba04b7612',7,NULL,'EMP-PHC002-0042','Obinna Okoro','obinna.okoro.42@sweettooth.com','+234-812-325-3133','281 Raina Village, Port Harcourt, Rivers State, Nigeria','1991-03-20','male','Nigerian','Fatima Bello','+234-841-649-2334','2025-01-10',NULL,'active',NULL,'afternoon',207112.77,NULL,'TIN-56260587','0628161612',NULL,NULL,'2025-09-28',4.2,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('c4f75bc9-d800-3b3a-beba-add1c08c27e2','019adbd4-0aec-707f-b4e0-ea5ba04b7612',4,NULL,'EMP-PHC002-0032','Nneka Aliyu','nneka.aliyu.32@sweettooth.com','+234-892-450-1073','400 Dare Motorway Suite 589, Port Harcourt, Rivers State, Nigeria','1988-08-14','female','Nigerian','Tunde Adebayo','+234-835-263-5153','2023-08-20',NULL,'active',NULL,'morning',287186.63,NULL,'TIN-65468100','8085084415',NULL,NULL,'2025-10-08',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('c743c078-08ad-3df2-9df0-44bcef654c13','019adbd4-0b13-726e-9aa8-3f26e5acec1b',2,NULL,'EMP-ABJ004-0069','Blessing Chukwu','blessing.chukwu.69@sweettooth.com','+234-814-353-8659','7589 Juanita Circle Apt. 113, Abuja, FCT State, Nigeria','1997-07-26','female','Nigerian','Abubakar Williams','+234-888-956-8480','2023-01-06',NULL,'on_probation','2025-12-24','flexible',235889.15,NULL,'TIN-34462305','3435817427',NULL,NULL,'2025-09-28',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('cf164d0b-8c74-3d3f-8fec-d8007362d57e','019adbd4-0b13-726e-9aa8-3f26e5acec1b',7,NULL,'EMP-ABJ004-0084','Chioma Aliyu','chioma.aliyu.84@sweettooth.com','+234-820-355-2940','9743 Clement Hills, Abuja, FCT State, Nigeria','1998-06-21','female','Nigerian','Chukwuemeka Okoro','+234-815-163-2185','2024-04-27',NULL,'active',NULL,'morning',286189.08,NULL,'TIN-92457461','3810589138',NULL,NULL,'2025-11-28',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('d51c30ab-f129-3996-8a17-cbb0b988cbb4','019adbd4-0b1e-7356-8544-47bf94c8ffed',5,NULL,'EMP-ENU005-0101','Amina Eze','amina.eze.101@sweettooth.com','+234-801-107-8648','72158 Bednar Loaf Suite 055, Enugu, Enugu State, Nigeria','1989-01-08','female','Nigerian','Chukwuemeka Chukwu','+234-814-889-3526','2023-04-15',NULL,'active',NULL,'flexible',222200.68,NULL,'TIN-63390009','1776879997',NULL,NULL,'2025-07-18',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('d57850dc-6796-3457-b1fe-1f3a155f85b1','019adbd4-0b1e-7356-8544-47bf94c8ffed',2,NULL,'EMP-ENU005-0091','Kemi Chukwu','kemi.chukwu.91@sweettooth.com','+234-884-696-4312','163 Boyd Freeway, Enugu, Enugu State, Nigeria','1991-11-16','female','Nigerian','Kunle Mohammed','+234-826-426-1609','2023-04-06',NULL,'active',NULL,'flexible',196480.01,NULL,'TIN-63085054','3727017000',NULL,NULL,'2025-07-14',4.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('d6a25248-ca6a-3851-a2ed-7ee9eda0ca73','019adbd4-0b13-726e-9aa8-3f26e5acec1b',1,NULL,'EMP-ABJ004-0064','Ada Adebayo','ada.adebayo.64@sweettooth.com','+234-810-525-4279','732 Kristopher Plain Suite 216, Abuja, FCT State, Nigeria','1989-09-20','female','Nigerian','Obinna Adebayo','+234-854-387-3154','2024-03-29',NULL,'on_probation','2026-01-09','morning',242321.43,NULL,'TIN-73525419','0829457704',NULL,NULL,'2025-09-23',5.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('d6ec0a6c-8bc7-3db2-b089-d71802ba324e','019adbd4-0b13-726e-9aa8-3f26e5acec1b',6,NULL,'EMP-ABJ004-0082','Kemi Aliyu','kemi.aliyu.82@sweettooth.com','+234-882-964-5813','67311 Lynch Center Suite 195, Abuja, FCT State, Nigeria','1984-01-14','female','Nigerian','Emeka Chukwu','+234-803-727-2878','2023-04-01',NULL,'active',NULL,'rotating',115345.60,NULL,'TIN-10213702','4374671827',NULL,NULL,'2025-06-08',4.3,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('da111e3f-7d76-383b-bb59-e86ac991db22','019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,NULL,'EMP-CAL001-0004','Chukwuemeka Ogunleye','chukwuemeka.ogunleye.4@sweettooth.com','+234-865-782-8602','203 Bridgette Street Apt. 115, Calabar, Cross River State, Nigeria','1998-01-31','male','Nigerian','Nneka Williams','+234-825-894-7169','2023-02-27',NULL,'active',NULL,'afternoon',238675.46,NULL,'TIN-74162150','1259721290',NULL,NULL,'2025-09-15',4.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('dd374b8f-7e19-3a51-a924-3ab566da1d99','019adbd4-0aec-707f-b4e0-ea5ba04b7612',7,NULL,'EMP-PHC002-0041','Amina Okoro','amina.okoro.41@sweettooth.com','+234-803-338-8284','1233 Brennan Road Suite 919, Port Harcourt, Rivers State, Nigeria','1988-10-24','female','Nigerian','Ibrahim Mohammed','+234-823-571-3312','2024-05-21',NULL,'on_probation','2026-02-07','afternoon',316968.59,NULL,'TIN-30111916','6678483604',NULL,NULL,'2025-11-22',4.4,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('dff7b89c-14ac-3b16-ae34-da8068950c5b','019adbd4-0b1e-7356-8544-47bf94c8ffed',3,NULL,'EMP-ENU005-0092','Nneka Nwankwo','nneka.nwankwo.92@sweettooth.com','+234-862-979-1416','7741 Ericka Shore, Enugu, Enugu State, Nigeria','1983-04-15','female','Nigerian','Chigozie Chukwu','+234-906-466-8475','2023-12-22',NULL,'active',NULL,'afternoon',275876.40,NULL,'TIN-94562826','0556391317',NULL,NULL,'2025-08-17',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('e1d670da-31d0-34ab-a4b3-60920c21f52c','019adbd4-0b1e-7356-8544-47bf94c8ffed',4,NULL,'EMP-ENU005-0096','Hauwa Adebayo','hauwa.adebayo.96@sweettooth.com','+234-909-882-3020','7872 Jacques Lock, Enugu, Enugu State, Nigeria','2000-11-20','female','Nigerian','Tunde Okoro','+234-883-224-3840','2023-12-13',NULL,'on_probation','2025-12-15','rotating',86323.37,NULL,'TIN-55512795','0076976521',NULL,NULL,'2025-08-15',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('e296ed9a-b71c-3fb8-9503-4af150c07c38','019adbd4-0b13-726e-9aa8-3f26e5acec1b',6,NULL,'EMP-ABJ004-0081','Kunle Okoro','kunle.okoro.81@sweettooth.com','+234-901-477-7541','98881 Rico Ways, Abuja, FCT State, Nigeria','1985-07-22','male','Nigerian','Blessing Bello','+234-851-869-2694','2023-02-17',NULL,'active',NULL,'rotating',157905.54,NULL,'TIN-66661815','8548011761',NULL,NULL,'2025-09-27',3.6,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('e5e0d9a3-5543-3692-be4c-b990a933ddca','019adbd4-0b07-7018-928c-e49d13068b9b',4,NULL,'EMP-LAG003-0054','Chigozie Johnson','chigozie.johnson.54@sweettooth.com','+234-837-917-8227','227 Bednar Path Apt. 041, Lagos, Lagos State, Nigeria','2003-10-20','male','Nigerian','Chioma Okoro','+234-818-765-5544','2023-03-03',NULL,'active',NULL,'afternoon',173725.80,NULL,'TIN-22396648','4717123851',NULL,NULL,'2025-11-24',3.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('e7a07b3b-d1e6-3eee-b103-738d8b0bf51e','019adbd4-0aec-707f-b4e0-ea5ba04b7612',1,NULL,'EMP-PHC002-0024','Kunle Adebayo','kunle.adebayo.24@sweettooth.com','+234-809-355-6286','60530 Wyman Turnpike Suite 861, Port Harcourt, Rivers State, Nigeria','1995-06-21','male','Nigerian','Hauwa Johnson','+234-804-938-2692','2022-12-20',NULL,'active',NULL,'afternoon',257349.34,NULL,'TIN-38081325','4345284401',NULL,NULL,'2025-10-17',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('e8d3a86e-aefc-333c-88a6-655c34660849','019adbd4-0b13-726e-9aa8-3f26e5acec1b',4,NULL,'EMP-ABJ004-0075','Ibrahim Okafor','ibrahim.okafor.75@sweettooth.com','+234-831-461-8535','293 Narciso Loaf, Abuja, FCT State, Nigeria','2002-04-25','male','Nigerian','Folake Okoro','+234-813-241-1664','2024-05-21',NULL,'active',NULL,'afternoon',139295.67,NULL,'TIN-69218151','3407577386',NULL,NULL,'2025-11-20',4.4,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('e8f4424d-ea16-30a5-9cdc-b18f4acde6be','019adbd4-0b07-7018-928c-e49d13068b9b',6,NULL,'EMP-LAG003-0061','Ngozi Okafor','ngozi.okafor.61@sweettooth.com','+234-893-845-8352','942 Rohan Streets Apt. 115, Lagos, Lagos State, Nigeria','2002-04-19','female','Nigerian','Yusuf Okafor','+234-876-384-9682','2022-12-10',NULL,'on_probation','2026-02-24','rotating',124286.13,NULL,'TIN-56665926','4836480999',NULL,NULL,'2025-06-10',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('e97a58f9-3630-3a82-98b2-b7d8d95f9501','019adbd4-0b07-7018-928c-e49d13068b9b',1,NULL,'EMP-LAG003-0044','Amina Chukwu','amina.chukwu.44@sweettooth.com','+234-907-657-5312','806 Brown Path, Lagos, Lagos State, Nigeria','1992-06-24','female','Nigerian','Chukwuemeka Nwankwo','+234-834-494-3356','2023-11-21',NULL,'active',NULL,'rotating',194113.98,NULL,'TIN-43754352','7645978911','None',NULL,'2025-11-12',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('ef5886ae-a84e-37fb-a86e-b1224f521e31','019adbd4-0ac6-73c1-aab2-48530e36a7fc',5,NULL,'EMP-CAL001-0017','Ibrahim Okoro','ibrahim.okoro.17@sweettooth.com','+234-840-518-5679','36013 Will Forks, Calabar, Cross River State, Nigeria','1982-05-29','male','Nigerian','Ngozi Ogunleye','+234-821-143-1369','2023-03-09',NULL,'active',NULL,'afternoon',224287.59,NULL,'TIN-50664774','1318638126',NULL,NULL,'2025-09-29',5.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('f1c7a8d2-8233-318b-b0c9-7a2b26fe498c','019adbd4-0b1e-7356-8544-47bf94c8ffed',4,NULL,'EMP-ENU005-0098','Obinna Okafor','obinna.okafor.98@sweettooth.com','+234-858-332-3748','6615 Emmett Valleys Apt. 691, Enugu, Enugu State, Nigeria','1982-02-10','male','Nigerian','Folake Okoro','+234-836-729-7392','2024-08-22',NULL,'active',NULL,'morning',178770.60,NULL,'TIN-36743361','2113947426',NULL,NULL,'2025-11-07',4.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('f2017ff9-ea6d-3834-96f2-b1024ca41d46','019adbd4-0b1e-7356-8544-47bf94c8ffed',7,NULL,'EMP-ENU005-0104','Oluwaseun Okafor','oluwaseun.okafor.104@sweettooth.com','+234-870-752-8816','194 Chaim Rapids, Enugu, Enugu State, Nigeria','1988-02-19','male','Nigerian','Ngozi Williams','+234-901-992-1345','2025-02-16',NULL,'on_probation','2026-02-03','morning',237087.64,NULL,'TIN-34476011','6877827738',NULL,NULL,'2025-07-19',4.0,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('f2a6a946-13d3-332f-97ac-422bd700b390','019adbd4-0b1e-7356-8544-47bf94c8ffed',2,NULL,'EMP-ENU005-0090','Chukwuemeka Eze','chukwuemeka.eze.90@sweettooth.com','+234-801-922-6258','6025 Naomie Hills Suite 787, Enugu, Enugu State, Nigeria','1982-04-29','male','Nigerian','Chioma Eze','+234-834-817-9890','2025-09-04',NULL,'active',NULL,'morning',125075.34,NULL,'TIN-10348747','0480306687',NULL,NULL,'2025-07-03',4.9,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('f342e8ac-ffa8-333c-b7ab-0fac5f3fc931','019adbd4-0ac6-73c1-aab2-48530e36a7fc',6,NULL,'EMP-CAL001-0019','Ada Bello','ada.bello.19@sweettooth.com','+234-819-373-3746','483 Gaylord Extension, Calabar, Cross River State, Nigeria','1983-08-01','female','Nigerian','Oluwaseun Okafor','+234-846-326-8828','2025-08-06',NULL,'active',NULL,'flexible',80542.45,NULL,'TIN-62443999','5832628001',NULL,NULL,'2025-11-06',4.5,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('f60db747-2170-3fce-bb6d-35ca52242c9b','019adbd4-0aec-707f-b4e0-ea5ba04b7612',4,NULL,'EMP-PHC002-0034','Kemi Mohammed','kemi.mohammed.34@sweettooth.com','+234-880-132-8004','9397 Kshlerin Hill, Port Harcourt, Rivers State, Nigeria','1986-12-27','female','Nigerian','Emeka Aliyu','+234-890-954-3025','2022-12-06',NULL,'active',NULL,'flexible',203768.05,NULL,'TIN-70764659','9488959448',NULL,NULL,'2025-11-26',4.1,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('f6916761-d714-3e01-bde4-0b26ef7f84de','019adbd4-0b13-726e-9aa8-3f26e5acec1b',3,NULL,'EMP-ABJ004-0071','Ibrahim Nwankwo','ibrahim.nwankwo.71@sweettooth.com','+234-890-218-6198','174 Kuhic Flats Apt. 941, Abuja, FCT State, Nigeria','1989-11-05','male','Nigerian','Kemi Aliyu','+234-828-316-7561','2023-09-21',NULL,'active',NULL,'flexible',184726.14,NULL,'TIN-60795020','9228902892',NULL,NULL,'2025-08-16',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('f8108511-e438-3120-8f4b-d1f674cdabc1','019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,NULL,'EMP-CAL001-0020','Nneka Chukwu','nneka.chukwu.20@sweettooth.com','+234-841-382-5701','971 Ted Crossing, Calabar, Cross River State, Nigeria','1987-04-12','female','Nigerian','Yusuf Chukwu','+234-825-163-2630','2023-01-12',NULL,'active',NULL,'morning',254779.82,NULL,'TIN-84023276','7330072360',NULL,NULL,'2025-11-08',4.7,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0),
('fcaafa44-3617-3732-aa9e-e8ac2c488be8','019adbd4-0aec-707f-b4e0-ea5ba04b7612',5,NULL,'EMP-PHC002-0038','Fatima Adebayo','fatima.adebayo.38@sweettooth.com','+234-860-234-1152','9627 Hermann Glens, Port Harcourt, Rivers State, Nigeria','1991-02-20','female','Nigerian','Chukwuemeka Eze','+234-853-121-2259','2023-11-16',NULL,'active',NULL,'afternoon',215687.20,NULL,'TIN-81760955','1783802036',NULL,NULL,'2025-06-17',3.8,'2025-12-01 20:31:40','2025-12-01 20:31:40',NULL,'$2y$12$3NwpX6YOt9t/3GevtxVuzOsngk.dbnwtgi72WdKM1hdYCQ0Tdr8dy','2025-12-01 20:31:40',NULL,0);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
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
  `approved_by_type` varchar(255) NOT NULL,
  `approved_by_id` char(36) NOT NULL,
  `cancelled_by_type` varchar(255) NOT NULL,
  `cancelled_by_id` char(36) NOT NULL,
  `dispatched_by_type` varchar(255) NOT NULL,
  `dispatched_by_id` char(36) NOT NULL,
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
set autocommit=0;
INSERT INTO `items` VALUES
(1,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Sugar - White Granulated','CAL-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-01 20:31:48','2025-12-01 20:31:48'),
(2,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Flour - All Purpose','CAL-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-01 20:31:48','2025-12-01 20:31:48'),
(3,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Cocoa Powder - Premium Dark','CAL-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-01 20:31:48','2025-12-01 20:31:48'),
(4,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Butter - Salted','CAL-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-01 20:31:48','2025-12-01 20:31:48'),
(5,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Eggs - Large Grade A','CAL-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-01 20:31:48','2025-12-01 20:31:48'),
(6,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Vanilla Extract - Pure','CAL-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-01 20:31:48','2025-12-01 20:31:48'),
(7,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Chocolate Chips - Dark','CAL-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-01 20:31:48','2025-12-01 20:31:48'),
(8,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Milk - Fresh Whole','CAL-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(9,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Cream - Heavy Whipping','CAL-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(10,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Yeast - Active Dry','CAL-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(11,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Vegetable Oil - Cooking','CAL-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(12,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Salt - Table Salt','CAL-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(13,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Cake Boxes - 10 inch','CAL-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(14,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Pastry Boxes - Small','CAL-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(15,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Paper Bags - Brown','CAL-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(16,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Plastic Food Containers','CAL-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(17,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Dishwashing Liquid - Industrial','CAL-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(18,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Paper Towels - Kitchen Roll','CAL-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(19,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Garbage Bags - Large','CAL-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(20,'019adbd4-0ac6-73c1-aab2-48530e36a7fc','Mixing Bowls - Stainless Steel','CAL-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(21,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Sugar - White Granulated','PHC-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(22,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Flour - All Purpose','PHC-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(23,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Cocoa Powder - Premium Dark','PHC-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(24,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Butter - Salted','PHC-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(25,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Eggs - Large Grade A','PHC-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(26,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Vanilla Extract - Pure','PHC-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(27,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Chocolate Chips - Dark','PHC-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(28,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Milk - Fresh Whole','PHC-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(29,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Cream - Heavy Whipping','PHC-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(30,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Yeast - Active Dry','PHC-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(31,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Vegetable Oil - Cooking','PHC-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(32,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Salt - Table Salt','PHC-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(33,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Cake Boxes - 10 inch','PHC-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(34,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Pastry Boxes - Small','PHC-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(35,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Paper Bags - Brown','PHC-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(36,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Plastic Food Containers','PHC-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(37,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Dishwashing Liquid - Industrial','PHC-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(38,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Paper Towels - Kitchen Roll','PHC-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(39,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Garbage Bags - Large','PHC-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(40,'019adbd4-0aec-707f-b4e0-ea5ba04b7612','Mixing Bowls - Stainless Steel','PHC-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(41,'019adbd4-0b07-7018-928c-e49d13068b9b','Sugar - White Granulated','LAG-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(42,'019adbd4-0b07-7018-928c-e49d13068b9b','Flour - All Purpose','LAG-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(43,'019adbd4-0b07-7018-928c-e49d13068b9b','Cocoa Powder - Premium Dark','LAG-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(44,'019adbd4-0b07-7018-928c-e49d13068b9b','Butter - Salted','LAG-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(45,'019adbd4-0b07-7018-928c-e49d13068b9b','Eggs - Large Grade A','LAG-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(46,'019adbd4-0b07-7018-928c-e49d13068b9b','Vanilla Extract - Pure','LAG-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(47,'019adbd4-0b07-7018-928c-e49d13068b9b','Chocolate Chips - Dark','LAG-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(48,'019adbd4-0b07-7018-928c-e49d13068b9b','Milk - Fresh Whole','LAG-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(49,'019adbd4-0b07-7018-928c-e49d13068b9b','Cream - Heavy Whipping','LAG-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(50,'019adbd4-0b07-7018-928c-e49d13068b9b','Yeast - Active Dry','LAG-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(51,'019adbd4-0b07-7018-928c-e49d13068b9b','Vegetable Oil - Cooking','LAG-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(52,'019adbd4-0b07-7018-928c-e49d13068b9b','Salt - Table Salt','LAG-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(53,'019adbd4-0b07-7018-928c-e49d13068b9b','Cake Boxes - 10 inch','LAG-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(54,'019adbd4-0b07-7018-928c-e49d13068b9b','Pastry Boxes - Small','LAG-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(55,'019adbd4-0b07-7018-928c-e49d13068b9b','Paper Bags - Brown','LAG-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(56,'019adbd4-0b07-7018-928c-e49d13068b9b','Plastic Food Containers','LAG-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(57,'019adbd4-0b07-7018-928c-e49d13068b9b','Dishwashing Liquid - Industrial','LAG-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(58,'019adbd4-0b07-7018-928c-e49d13068b9b','Paper Towels - Kitchen Roll','LAG-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(59,'019adbd4-0b07-7018-928c-e49d13068b9b','Garbage Bags - Large','LAG-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(60,'019adbd4-0b07-7018-928c-e49d13068b9b','Mixing Bowls - Stainless Steel','LAG-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(61,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Sugar - White Granulated','ABJ-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(62,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Flour - All Purpose','ABJ-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(63,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Cocoa Powder - Premium Dark','ABJ-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(64,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Butter - Salted','ABJ-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(65,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Eggs - Large Grade A','ABJ-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(66,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Vanilla Extract - Pure','ABJ-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(67,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Chocolate Chips - Dark','ABJ-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(68,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Milk - Fresh Whole','ABJ-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(69,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Cream - Heavy Whipping','ABJ-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(70,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Yeast - Active Dry','ABJ-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(71,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Vegetable Oil - Cooking','ABJ-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(72,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Salt - Table Salt','ABJ-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(73,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Cake Boxes - 10 inch','ABJ-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(74,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Pastry Boxes - Small','ABJ-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(75,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Paper Bags - Brown','ABJ-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(76,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Plastic Food Containers','ABJ-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(77,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Dishwashing Liquid - Industrial','ABJ-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(78,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Paper Towels - Kitchen Roll','ABJ-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(79,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Garbage Bags - Large','ABJ-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(80,'019adbd4-0b13-726e-9aa8-3f26e5acec1b','Mixing Bowls - Stainless Steel','ABJ-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(81,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Sugar - White Granulated','ENU-ITM-00001','raw_material','kg','Premium white granulated sugar for baking and cooking',50.00,500.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(82,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Flour - All Purpose','ENU-ITM-00002','raw_material','kg','High-quality all-purpose flour for general baking',100.00,1000.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(83,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Cocoa Powder - Premium Dark','ENU-ITM-00003','raw_material','kg','Premium Dutch-processed cocoa powder',20.00,200.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(84,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Butter - Salted','ENU-ITM-00004','raw_material','kg','Fresh salted butter for baking and cooking',30.00,300.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(85,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Eggs - Large Grade A','ENU-ITM-00005','raw_material','cartons','Fresh large Grade A eggs (30 pieces per carton)',10.00,50.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(86,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Vanilla Extract - Pure','ENU-ITM-00006','raw_material','liters','Pure vanilla extract for flavoring',5.00,30.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(87,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Chocolate Chips - Dark','ENU-ITM-00007','raw_material','kg','70% dark chocolate chips for baking',15.00,150.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(88,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Milk - Fresh Whole','ENU-ITM-00008','raw_material','liters','Fresh whole milk, refrigerated',20.00,100.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(89,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Cream - Heavy Whipping','ENU-ITM-00009','raw_material','liters','Heavy whipping cream, 35% fat content',10.00,50.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(90,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Yeast - Active Dry','ENU-ITM-00010','raw_material','kg','Active dry yeast for bread making',5.00,25.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(91,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Vegetable Oil - Cooking','ENU-ITM-00011','raw_material','liters','Refined vegetable oil for cooking and frying',25.00,200.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(92,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Salt - Table Salt','ENU-ITM-00012','raw_material','kg','Iodized table salt for cooking and seasoning',10.00,100.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(93,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Cake Boxes - 10 inch','ENU-ITM-00013','packaging','pcs','White cardboard boxes for 10-inch cakes',100.00,1000.00,'active','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(94,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Pastry Boxes - Small','ENU-ITM-00014','packaging','pcs','Small boxes for pastries and individual servings',200.00,2000.00,'active','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(95,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Paper Bags - Brown','ENU-ITM-00015','packaging','pcs','Eco-friendly brown paper bags',500.00,5000.00,'active','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(96,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Plastic Food Containers','ENU-ITM-00016','packaging','pcs','Clear plastic containers with lids for takeaway',150.00,1500.00,'active','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(97,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Dishwashing Liquid - Industrial','ENU-ITM-00017','consumable','liters','Heavy-duty dishwashing liquid for commercial use',20.00,100.00,'active','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(98,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Paper Towels - Kitchen Roll','ENU-ITM-00018','consumable','units','Absorbent paper towels for kitchen use',50.00,200.00,'active','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(99,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Garbage Bags - Large','ENU-ITM-00019','consumable','pcs','Heavy-duty large garbage bags',100.00,500.00,'active','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(100,'019adbd4-0b1e-7356-8544-47bf94c8ffed','Mixing Bowls - Stainless Steel','ENU-ITM-00020','equipment','pcs','Professional stainless steel mixing bowls, various sizes',5.00,30.00,'active','2025-12-01 20:31:53','2025-12-01 20:31:53');
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
  CONSTRAINT `leave_applications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_applications_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_applications`
--

LOCK TABLES `leave_applications` WRITE;
/*!40000 ALTER TABLE `leave_applications` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `leave_applications` VALUES
(1,'LA-2025-001','0357d01c-7205-374e-b9e8-7c72d8f158ae',1,'2025-12-05','2025-12-07',1.00,'Reason for Leave *Reason for Leave *Reason for Leave *Reason for Leave *','98764849494',NULL,'approved','0357d01c-7205-374e-b9e8-7c72d8f158ae','App\\Models\\Employee','2025-12-02 11:32:14','Approve Leave Application\nApproval Notes (Optional)',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-12-02 11:31:42','2025-12-02 11:32:14',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `leave_types` VALUES
(1,'Annual','ANNA','\nCreate Leave Type\nConfigure leave type details and policies\nCreate Leave Type\nConfigure leave type details and policies\nCreate Leave Type\nConfigure leave type details and policies\nCreate Leave Type\nConfigure leave type details and policies\nCreate Leave Type\nConfigure leave type details and policies\nCreate Leave Type\nConfigure leave type details and policies',5,1,0,5,3,1,1,'#3b82f6','2025-12-02 11:30:48','2025-12-02 11:31:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(24,'2025_10_13_050139_create_clock_ins_table',1),
(25,'2025_10_15_053301_create_recipes_table',1),
(26,'2025_10_15_053356_create_recipe_ingredients_table',1),
(27,'2025_10_15_053520_create_shifts_table',1),
(28,'2025_10_15_053537_create_daily_produces_table',1),
(29,'2025_10_15_054442_create_production_records_table',1),
(30,'2025_10_15_055019_create_production_requests_table',1),
(31,'2025_10_15_055201_create_call_backs_table',1),
(32,'2025_10_15_055221_create_raw_material_utilizations_table',1),
(33,'2025_10_16_074716_create_product_types_table',1),
(34,'2025_10_16_074717_create_products_table',1),
(35,'2025_10_18_075757_add_branch_id_to_products_table',1),
(36,'2025_10_18_194413_add_cost_and_waste_fields_to_recipe_ingredients_table',1),
(37,'2025_10_19_030711_add_status_to_daily_produces_table',1),
(38,'2025_10_19_083910_create_employee_shifts_table',1),
(39,'2025_10_20_063309_create_sales_hifts_table',1),
(40,'2025_10_20_063310_create_product_stocks_table',1),
(41,'2025_10_20_063352_create_sales_table',1),
(42,'2025_10_20_063409_create_sale_items_table',1),
(43,'2025_10_20_063429_create_payments_table',1),
(44,'2025_10_21_073703_create_product_callbacks_table',1),
(45,'2025_10_22_094919_create_approved_items_table',1),
(46,'2025_10_23_100000_create_expiry_confirmations_table',1),
(47,'2025_10_23_120000_create_product_dispatches_table',1),
(48,'2025_10_24_200648_add_batch_tracking_to_production_records_table',1),
(49,'2025_10_25_045338_make_sales_shift_id_nullable_in_product_stocks_table',1),
(50,'2025_10_25_073857_create_receipts_table',1),
(51,'2025_10_27_000814_create_global_business_configurations_table',1),
(52,'2025_10_27_000816_create_global_currency_localizations_table',1),
(53,'2025_10_27_000818_create_global_branch_management_table',1),
(54,'2025_10_27_000820_create_global_inventory_management_table',1),
(55,'2025_10_27_000821_create_branch_inventory_management_table',1),
(56,'2025_10_27_000823_create_global_employee_management_table',1),
(57,'2025_10_27_000824_create_global_pos_configurations_table',1),
(58,'2025_10_27_000827_create_global_accounting_cashes_table',1),
(59,'2025_10_27_000829_create_global_customer_supplier_management_table',1),
(60,'2025_10_27_000831_create_global_reports_analytics_table',1),
(61,'2025_10_27_000833_create_global_security_accesses_table',1),
(62,'2025_10_27_000835_create_global_notifications_alerts_table',1),
(63,'2025_10_27_000837_create_audit_logs_table',1),
(64,'2025_10_27_000838_create_approval_requests_table',1),
(65,'2025_10_27_042254_remove_business_type_from_global_business_configurations',1),
(66,'2025_10_27_042747_remove_columns_from_global_business_configurations',1),
(67,'2025_11_01_082233_create_department_pages_table',1),
(68,'2025_11_01_082403_add_slug_to_departments_table',1),
(69,'2025_11_02_021715_create_product_dispatch_callbacks_table',1),
(70,'2025_11_02_104432_create_production_callbacks_table',1),
(71,'2025_11_02_110718_add_sales_shift_and_received_quantity_to_product_dispatches_table',1),
(72,'2025_11_06_055946_create_tables_table',1),
(73,'2025_11_06_055949_add_table_id_to_sales_table',1),
(74,'2025_11_06_055953_add_table_management_settings_to_branches_table',1),
(75,'2025_11_06_063045_add_table_management_to_departments_table',1),
(76,'2025_11_06_063047_add_department_id_to_sale_items_table',1),
(77,'2025_11_06_063247_add_department_id_to_tables_table',1),
(78,'2025_11_06_100524_create_department_product_table',1),
(79,'2025_11_06_102006_add_product_id_to_recipes_table',1),
(80,'2025_11_06_175546_make_product_dispatch_id_nullable_in_product_dispatch_callbacks_table',1),
(81,'2025_11_07_175717_add_sales_department_id_to_product_dispatches_table',1),
(82,'2025_11_07_183133_remove_redundant_yield_fields_from_products_table',1),
(83,'2025_11_09_053144_create_leave_types_table',1),
(84,'2025_11_09_053147_create_employee_leave_balances_table',1),
(85,'2025_11_09_053149_create_leave_applications_table',1),
(86,'2025_11_09_053151_create_probation_reviews_table',1),
(87,'2025_11_09_053154_create_salary_history_table',1),
(88,'2025_11_09_053507_create_employee_stepouts_table',1),
(89,'2025_11_09_103341_create_employee_leave_allocations_table',1),
(90,'2025_11_11_055910_add_branch_id_to_leave_application_table',1),
(91,'2025_11_14_000001_create_department_reports_table',1),
(92,'2025_11_14_000002_create_compiled_reports_table',1),
(93,'2025_11_14_000003_create_report_schedules_table',1),
(94,'2025_11_14_000004_create_report_distributions_table',1),
(95,'2025_11_14_000005_create_report_templates_table',1),
(96,'2025_11_14_000006_create_compiled_report_department_report_table',1),
(97,'2025_11_14_225458_create_product_transfers_table',1),
(98,'2025_11_15_061641_add_last_accessed_branch_id_to_users_table',1),
(99,'2025_11_15_170737_add_department_id_to_products_table',1),
(100,'2025_11_15_173556_add_is_siper_admin_request_to_production_requests_table',1),
(101,'2025_11_15_174249_add_is_super_admin_to_employees_table',1),
(102,'2025_11_24_032703_update_audit_logs_table_add_missing_columns',1),
(103,'2025_11_24_032823_update_audit_logs_setting_type_nullable',1),
(104,'2025_11_24_033049_alter_audit_logs_details_nullable',1),
(105,'2025_11_24_034645_create_approval_audit_requests_table',1),
(106,'2025_11_24_035053_add_branch_id_to_approval_audit_requests_table',1),
(107,'2025_12_02_000001_fix_audit_logs_auditable_id_column',2),
(108,'2025_12_02_000002_increase_audit_logs_action_column_size',3),
(109,'2025_12_02_100000_recreate_approval_requests_table',4);
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
(22,'user','019adbd4-0a1e-70af-adba-4dfe45affa1a'),
(15,'employee','019adf13-924f-714f-8352-5cd70791074e'),
(8,'employee','01acda54-c639-3075-9150-37f5eed06ddb'),
(5,'employee','0357d01c-7205-374e-b9e8-7c72d8f158ae'),
(15,'employee','0357d01c-7205-374e-b9e8-7c72d8f158ae'),
(13,'employee','0f46b4ad-2658-34b4-aa9a-1cdc6112d84a'),
(12,'employee','100752ac-d21c-3dcc-907f-4b478a3b9733'),
(6,'employee','15b9b0d3-b46a-3793-92c3-a31bdee6db5d'),
(17,'employee','1745cbb7-10cd-3f25-a238-94529ae5ca7d'),
(15,'employee','1f58d5d0-c31f-375c-a6b2-03e4a5f599ff'),
(16,'employee','220d8237-39a2-3537-8c57-7b56280eda39'),
(15,'employee','2702b844-5912-32e3-aff2-44ab1c3b417b'),
(19,'employee','27c3e77a-72c8-30a1-98c5-380d8a039bd3'),
(14,'employee','2af46333-91e5-36d9-83da-c41ac07171cd'),
(17,'employee','2c8e6f2a-0526-3fc7-9496-ad7741db226d'),
(16,'employee','2d142047-7ddd-35f6-b9a6-3fd3727460c8'),
(14,'employee','31d3f5ea-87b2-364b-8e75-ed5452953a3a'),
(18,'employee','35eac18f-0c5e-3aa8-8c30-4a160a42dfae'),
(6,'employee','3607fa20-0222-33a6-a1f9-da8a386152a0'),
(18,'employee','3607fa20-0222-33a6-a1f9-da8a386152a0'),
(14,'employee','37b32b0e-e9fa-3436-924b-9b59eca605d6'),
(13,'employee','3bf1f838-e106-30bb-ac75-c9c2b8b0cc0c'),
(13,'employee','3d989e41-b8ba-314e-98da-4dd0e0c5b7aa'),
(14,'employee','3e1289ba-e385-3b84-8243-58093af0fe80'),
(13,'employee','415e62d9-1aa2-3a62-a0c1-3b0f55ef5c15'),
(18,'employee','41cfcb11-97d0-37ed-850a-94fc992a8c49'),
(13,'employee','42503b0d-68c3-374a-8f73-3dcc1b392ab8'),
(13,'employee','439937b1-48ac-3b35-84d8-bf17cd5a21cf'),
(17,'employee','441b94c6-ef2e-3a2e-b81c-37c42eb5383c'),
(15,'employee','44639c67-51ca-32d8-8f0f-3c75863a3ffa'),
(16,'employee','45e4db87-284b-3870-8979-04e1fa9aaae8'),
(19,'employee','4acd63a3-cac5-39ca-abb3-0920a9cd0b4c'),
(16,'employee','4e224a26-9875-373a-b82d-b82e3fd758c9'),
(15,'employee','4ea977c8-735d-3936-b040-b643393378b6'),
(10,'employee','4f2efabd-14d8-36de-96ec-94cd48751e85'),
(13,'employee','518ea6dc-8459-3c3f-93a2-95bb67d9e61c'),
(12,'employee','51af9f3b-bf3d-3fb1-bf04-4e39a1a059fa'),
(16,'employee','522b8454-e175-3cea-aeab-c6c8717a1728'),
(17,'employee','52a5f02d-2ef6-360b-a1b8-27615f406647'),
(18,'employee','53af11df-f62e-3a07-b8cc-e05368efa7a5'),
(8,'employee','549e3510-564d-3f1f-b1cb-e93a49800b21'),
(15,'employee','553313ad-62b0-375d-8fed-fced4be7cc4a'),
(11,'employee','57e06f9f-0b19-3c60-9101-43464cebf324'),
(16,'employee','5880f31f-b4b3-3b42-b4fc-39343bbc55f7'),
(17,'employee','59cf7d06-4cc7-380e-9b17-ef929d9e6474'),
(14,'employee','5d034879-b9fc-3245-a6c7-4c579871b873'),
(8,'employee','61312a1f-20ea-34dc-b8af-61c351557968'),
(9,'employee','6201481e-e27c-360e-9362-78c6405dc52a'),
(9,'employee','66991f66-cd04-388e-8ced-1688f9acdbd0'),
(13,'employee','694b5484-3aa1-304d-ae57-0eed2a57fd74'),
(15,'employee','6d0f0772-072f-32f1-9f03-127c61a8829f'),
(18,'employee','6f7047f4-f24a-3808-b8f5-c292942dccac'),
(13,'employee','72812433-e623-3d39-b488-214e34f99e23'),
(9,'employee','73265c6a-6c4b-3660-8de9-8b1aa4c80979'),
(9,'employee','7f967123-0a10-3715-9539-e14a7e3caf61'),
(20,'employee','7ff6be45-1b65-3813-98fe-a38f7bc4fbbd'),
(17,'employee','83925e11-9245-30a5-823a-ae02b23c52cf'),
(17,'employee','8efe9b66-7c30-388b-867e-8d4342e53b21'),
(13,'employee','900cdc0b-ee25-3cef-a560-91fe3c7a002e'),
(11,'employee','90e042f1-2913-3af7-9fbe-ca7958bfcc40'),
(9,'employee','9b663fb5-a2c1-3142-8d3b-4e9ea7865dd2'),
(15,'employee','9e498b7f-a6f1-3264-9776-467b905e9bc1'),
(11,'employee','a0582faf-9586-3ecf-b5d0-f6cd07058e64'),
(20,'employee','a3f73790-38ea-396c-b9e2-66df082296b1'),
(19,'employee','a481af38-64a4-3f86-beea-256ebfec10ac'),
(16,'employee','a5f9a6d5-edb4-36ea-9d81-87042220de40'),
(13,'employee','a809e16d-e423-36c2-bb00-68f41bb4a5b6'),
(10,'employee','a96b155e-227b-3e99-ab66-aabd7df7dab2'),
(16,'employee','a9f468ba-2865-3277-a5fb-9d4f46034317'),
(8,'employee','abab6f75-74ad-3d01-aa32-06517e0232a7'),
(13,'employee','adcf9dae-510a-372d-bbc4-df91b7d182b2'),
(14,'employee','aeb7f0df-3bea-3513-ad12-98239232ec2c'),
(15,'employee','af0cfc8f-c0fa-3a32-980e-438ce0457dbd'),
(14,'employee','b00cadcd-c208-3f1b-8032-253755f9794f'),
(12,'employee','b0e45146-6a68-34ef-8ab6-66a310f0d637'),
(12,'employee','b3d72d23-3da8-3342-9296-7d8e7f8f52d1'),
(18,'employee','b6b33dfc-d65f-3faa-9f3b-e9d785780d28'),
(15,'employee','be6c2086-10ea-3c84-b636-c2d9af010b33'),
(11,'employee','beedeb93-582a-3ed2-aa22-19960d76ac72'),
(12,'employee','bf967152-e312-3f86-af62-6a1517646941'),
(16,'employee','c226ddf5-5fc5-3b3a-b891-4cc9f284552e'),
(19,'employee','c48854eb-13e4-3af0-a232-b074780d79a7'),
(11,'employee','c4f75bc9-d800-3b3a-beba-add1c08c27e2'),
(14,'employee','c743c078-08ad-3df2-9df0-44bcef654c13'),
(19,'employee','cf164d0b-8c74-3d3f-8fec-d8007362d57e'),
(17,'employee','d51c30ab-f129-3996-8a17-cbb0b988cbb4'),
(14,'employee','d57850dc-6796-3457-b1fe-1f3a155f85b1'),
(8,'employee','d6a25248-ca6a-3851-a2ed-7ee9eda0ca73'),
(18,'employee','d6ec0a6c-8bc7-3db2-b089-d71802ba324e'),
(13,'employee','da111e3f-7d76-383b-bb59-e86ac991db22'),
(20,'employee','dd374b8f-7e19-3a51-a924-3ab566da1d99'),
(10,'employee','dff7b89c-14ac-3b16-ae34-da8068950c5b'),
(16,'employee','e1d670da-31d0-34ab-a4b3-60920c21f52c'),
(18,'employee','e296ed9a-b71c-3fb8-9503-4af150c07c38'),
(16,'employee','e5e0d9a3-5543-3692-be4c-b990a933ddca'),
(13,'employee','e7a07b3b-d1e6-3eee-b103-738d8b0bf51e'),
(16,'employee','e8d3a86e-aefc-333c-88a6-655c34660849'),
(18,'employee','e8f4424d-ea16-30a5-9cdc-b18f4acde6be'),
(13,'employee','e97a58f9-3630-3a82-98b2-b7d8d95f9501'),
(17,'employee','ef5886ae-a84e-37fb-a86e-b1224f521e31'),
(16,'employee','f1c7a8d2-8233-318b-b0c9-7a2b26fe498c'),
(20,'employee','f2017ff9-ea6d-3834-96f2-b1024ca41d46'),
(14,'employee','f2a6a946-13d3-332f-97ac-422bd700b390'),
(18,'employee','f342e8ac-ffa8-333c-b7ab-0fac5f3fc931'),
(16,'employee','f60db747-2170-3fce-bb6d-35ca52242c9b'),
(10,'employee','f6916761-d714-3e01-bde4-0b26ef7f84de'),
(20,'employee','f8108511-e438-3120-8f4b-d1f674cdabc1'),
(17,'employee','fcaafa44-3617-3732-aa9e-e8ac2c488be8');
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
  `reference_number` varchar(255) DEFAULT NULL,
  `payment_time` timestamp NOT NULL,
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
set autocommit=0;
INSERT INTO `permissions` VALUES
(1,'view-employee-dashboard','employees','2025-12-01 20:31:18','2025-12-01 20:31:18'),
(2,'view-analytics','employees','2025-12-01 20:31:18','2025-12-01 20:31:18'),
(3,'view-profile','employees','2025-12-01 20:31:18','2025-12-01 20:31:18'),
(4,'edit-profile','employees','2025-12-01 20:31:18','2025-12-01 20:31:18'),
(5,'view-production-queue','employees','2025-12-01 20:31:18','2025-12-01 20:31:18'),
(6,'start-production','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(7,'complete-production','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(8,'approve-production','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(9,'manage-recipes','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(10,'process-sale','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(11,'issue-refund','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(12,'view-daily-sales','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(13,'close-register','employees','2025-12-01 20:31:19','2025-12-01 20:31:19'),
(14,'receive-stock','employees','2025-12-01 20:31:20','2025-12-01 20:31:20'),
(15,'transfer-stock','employees','2025-12-01 20:31:20','2025-12-01 20:31:20'),
(16,'adjust-inventory','employees','2025-12-01 20:31:20','2025-12-01 20:31:20'),
(17,'view-stock-levels','employees','2025-12-01 20:31:20','2025-12-01 20:31:20'),
(18,'view-employees','employees','2025-12-01 20:31:20','2025-12-01 20:31:20'),
(19,'create-employees','employees','2025-12-01 20:31:20','2025-12-01 20:31:20'),
(20,'edit-employees','employees','2025-12-01 20:31:21','2025-12-01 20:31:21'),
(21,'delete-employees','employees','2025-12-01 20:31:21','2025-12-01 20:31:21'),
(22,'assign-roles','employees','2025-12-01 20:31:21','2025-12-01 20:31:21'),
(23,'view-departments','employees','2025-12-01 20:31:21','2025-12-01 20:31:21'),
(24,'view-branches','employees','2025-12-01 20:31:21','2025-12-01 20:31:21'),
(25,'view-roles','employees','2025-12-01 20:31:21','2025-12-01 20:31:21'),
(26,'view-department-reports','employees','2025-12-01 20:31:21','2025-12-01 20:31:21'),
(27,'manage-staff-schedule','employees','2025-12-01 20:31:22','2025-12-01 20:31:22'),
(28,'view-orders','employees','2025-12-01 20:31:22','2025-12-01 20:31:22'),
(29,'create-orders','employees','2025-12-01 20:31:22','2025-12-01 20:31:22'),
(30,'edit-orders','employees','2025-12-01 20:31:22','2025-12-01 20:31:22'),
(31,'delete-orders','employees','2025-12-01 20:31:22','2025-12-01 20:31:22'),
(32,'process-orders','employees','2025-12-01 20:31:22','2025-12-01 20:31:22'),
(33,'cancel-orders','employees','2025-12-01 20:31:23','2025-12-01 20:31:23'),
(34,'view-products','employees','2025-12-01 20:31:23','2025-12-01 20:31:23'),
(35,'create-products','employees','2025-12-01 20:31:23','2025-12-01 20:31:23'),
(36,'edit-products','employees','2025-12-01 20:31:23','2025-12-01 20:31:23'),
(37,'delete-products','employees','2025-12-01 20:31:23','2025-12-01 20:31:23'),
(38,'manage-inventory','employees','2025-12-01 20:31:23','2025-12-01 20:31:23'),
(39,'view-customers','employees','2025-12-01 20:31:24','2025-12-01 20:31:24'),
(40,'create-customers','employees','2025-12-01 20:31:24','2025-12-01 20:31:24'),
(41,'edit-customers','employees','2025-12-01 20:31:24','2025-12-01 20:31:24'),
(42,'delete-customers','employees','2025-12-01 20:31:24','2025-12-01 20:31:24'),
(43,'view-reports','employees','2025-12-01 20:31:24','2025-12-01 20:31:24'),
(44,'generate-reports','employees','2025-12-01 20:31:24','2025-12-01 20:31:24'),
(45,'export-reports','employees','2025-12-01 20:31:25','2025-12-01 20:31:25'),
(46,'view-settings','employees','2025-12-01 20:31:25','2025-12-01 20:31:25'),
(47,'edit-settings','employees','2025-12-01 20:31:25','2025-12-01 20:31:25'),
(48,'view-employees','web','2025-12-01 20:31:25','2025-12-01 20:31:25'),
(49,'create-employees','web','2025-12-01 20:31:25','2025-12-01 20:31:25'),
(50,'edit-employees','web','2025-12-01 20:31:26','2025-12-01 20:31:26'),
(51,'delete-employees','web','2025-12-01 20:31:26','2025-12-01 20:31:26'),
(52,'view-roles','web','2025-12-01 20:31:26','2025-12-01 20:31:26'),
(53,'create-roles','web','2025-12-01 20:31:26','2025-12-01 20:31:26'),
(54,'edit-roles','web','2025-12-01 20:31:26','2025-12-01 20:31:26'),
(55,'delete-roles','web','2025-12-01 20:31:26','2025-12-01 20:31:26'),
(56,'view-permissions','web','2025-12-01 20:31:26','2025-12-01 20:31:26'),
(57,'create-permissions','web','2025-12-01 20:31:27','2025-12-01 20:31:27'),
(58,'edit-permissions','web','2025-12-01 20:31:27','2025-12-01 20:31:27'),
(59,'delete-permissions','web','2025-12-01 20:31:27','2025-12-01 20:31:27'),
(60,'view-branches','web','2025-12-01 20:31:27','2025-12-01 20:31:27'),
(61,'create-branches','web','2025-12-01 20:31:27','2025-12-01 20:31:27'),
(62,'edit-branches','web','2025-12-01 20:31:28','2025-12-01 20:31:28'),
(63,'delete-branches','web','2025-12-01 20:31:28','2025-12-01 20:31:28'),
(64,'view-system-settings','web','2025-12-01 20:31:28','2025-12-01 20:31:28'),
(65,'edit-system-settings','web','2025-12-01 20:31:28','2025-12-01 20:31:28'),
(66,'view-audit-logs','web','2025-12-01 20:31:28','2025-12-01 20:31:28'),
(67,'view employees','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(68,'create employees','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(69,'edit employees','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(70,'delete employees','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(71,'approve employee leave','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(72,'view employee attendance','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(73,'manage payroll','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(74,'view hr reports','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(75,'assign departments','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(76,'approve recruitment','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(77,'view employee profile','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(78,'generate employee report','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(79,'terminate employee','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(80,'view salary structure','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(81,'update employee role','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(82,'view performance reviews','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(83,'view production batches','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(84,'create production batch','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(85,'edit production batch','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(86,'delete production batch','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(87,'approve production plan','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(88,'view production schedule','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(89,'manage production line','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(90,'monitor production status','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(91,'record production output','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(92,'update production cost','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(93,'view quality control report','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(94,'approve quality control','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(95,'view wastage reports','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(96,'update machinery status','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(97,'log equipment maintenance','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(98,'view inventory','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(99,'create inventory item','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(100,'edit inventory item','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(101,'delete inventory item','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(102,'adjust stock levels','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(103,'view stock history','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(104,'transfer stock','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(105,'receive stock','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(106,'issue raw materials','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(107,'view warehouse report','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(108,'manage warehouse location','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(109,'view expiry tracking','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(110,'mark damaged goods','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(111,'approve restock','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(112,'monitor inventory alerts','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(113,'view suppliers','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(114,'create supplier','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(115,'edit supplier','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(116,'delete supplier','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(117,'approve purchase order','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(118,'create purchase order','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(119,'edit purchase order','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(120,'view purchase history','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(121,'receive goods','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(122,'approve vendor payment','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(123,'view logistics status','web','2025-12-01 20:31:34','2025-12-01 20:31:34'),
(124,'schedule delivery','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(125,'approve transportation','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(126,'track shipment','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(127,'view import/export records','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(128,'manage procurement report','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(129,'view sales records','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(130,'create sales order','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(131,'edit sales order','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(132,'delete sales order','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(133,'approve sales invoice','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(134,'process refund','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(135,'view financial dashboard','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(136,'approve expense','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(137,'view profit and loss','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(138,'manage tax settings','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(139,'generate sales reports','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(140,'approve discounts','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(141,'manage pricing structure','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(142,'view payment history','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(143,'view cash flow','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(144,'update financial policy','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(145,'view audit logs','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(146,'manage users','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(147,'manage roles','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(148,'manage permissions','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(149,'view system settings','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(150,'update company info','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(151,'backup database','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(152,'restore backup','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(153,'view activity logs','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(154,'manage notifications','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(155,'view dashboard','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(156,'access API tokens','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(157,'view analytics','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(158,'view KPI metrics','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(159,'manage departments','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(160,'configure approval workflow','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(161,'access admin panel','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(162,'view change history','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(163,'enable maintenance mode','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(164,'disable maintenance mode','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(165,'generate compliance report','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(166,'view supplier compliance','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(167,'approve compliance status','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(168,'view environmental reports','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(169,'manage food safety records','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(170,'view traceability logs','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(171,'approve export documents','web','2025-12-01 20:31:35','2025-12-01 20:31:35'),
(172,'review regulatory submissions','web','2025-12-01 20:31:36','2025-12-01 20:31:36'),
(173,'view recall reports','web','2025-12-01 20:31:36','2025-12-01 20:31:36');
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
  CONSTRAINT `probation_reviews_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `probation_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
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
  `branch_id` char(36) NOT NULL,
  `daily_produce_id` bigint(20) unsigned DEFAULT NULL,
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
  `status` enum('dispatched','received','rejected') NOT NULL DEFAULT 'dispatched',
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
  CONSTRAINT `product_dispatches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_dispatches_daily_produce_id_foreign` FOREIGN KEY (`daily_produce_id`) REFERENCES `daily_produces` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_dispatches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_stock` (`sales_shift_id`,`product_id`,`stock_date`,`shift_type`),
  KEY `product_stocks_product_id_foreign` (`product_id`),
  CONSTRAINT `product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stocks_sales_shift_id_foreign` FOREIGN KEY (`sales_shift_id`) REFERENCES `sales_shifts` (`id`) ON DELETE SET NULL
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
(1,1,'Pastries','PT','Baked pastries including croissants, danishes, and puff pastries','active',1,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(2,1,'Breads','BR','Fresh baked breads, loaves, and rolls','active',2,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(3,1,'Cakes','CK','Layer cakes, sponge cakes, and celebration cakes','active',3,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(4,1,'Cookies','CO','Baked cookies and biscuits','active',4,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(5,1,'Muffins','MF','Sweet and savory muffins','active',5,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(6,1,'Pies & Tarts','PIT','Fruit pies, cream pies, and tarts','active',6,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(7,1,'Sandwiches','SW','Fresh sandwiches and wraps','active',7,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(8,1,'Hot Kitchen','HK','Cooked meals and hot food items','active',8,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(9,2,'Gelato Base','GB','Base gelato mixtures before flavoring','active',1,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(10,2,'Gelato Flavors','GF','Finished gelato in various flavors','active',2,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(11,2,'Sorbet','SB','Fruit-based frozen desserts without dairy','active',3,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(12,2,'Ice Cream','IC','Traditional ice cream products','active',4,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(13,2,'Frozen Yogurt','FY','Frozen yogurt in various flavors','active',5,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(14,2,'Gelato Toppings','GT','House-made toppings and mix-ins for gelato','active',6,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(15,3,'Chocolates','CH','Handcrafted chocolates and truffles','active',1,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(16,3,'Candies','CD','Hard and soft candies','active',2,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(17,3,'Fudge','FG','Traditional and flavored fudge','active',3,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(18,3,'Caramels','CR','Soft and hard caramels','active',4,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(19,3,'Marshmallows','MM','Gourmet marshmallows in various flavors','active',5,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
(20,3,'Nougat','NG','Traditional nougat confections','active',6,'2025-12-01 20:31:54','2025-12-01 20:31:54',NULL);
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
  `quantity_rejected` decimal(12,2) NOT NULL DEFAULT 0.00,
  `production_time` timestamp NOT NULL,
  `quality_status` enum('excellent','good','acceptable','rejected') NOT NULL DEFAULT 'good',
  `dispatch_status` enum('available','partial','fully_dispatched') NOT NULL DEFAULT 'available',
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
  `uom` enum('grams','kg','liters','ml','pcs','units') NOT NULL DEFAULT 'pcs',
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
  CONSTRAINT `products_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_product_type_id_foreign` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `products` VALUES
('019adbd4-4c76-7278-a5e2-5c8a441adf88','Butter Croissant','PT-BUT-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,NULL,'Classic French butter croissant, flaky and golden',3.50,1.20,2,'pcs',100.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"breakfast\",\"french\",\"popular\"]','2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
('019adbd4-4ce9-7308-a696-1fe039e78981','Almond Danish','PT-ALM-002','019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,NULL,'Sweet Danish pastry topped with sliced almonds',4.00,1.50,2,'pcs',150.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\",\"nuts\"]','[\"breakfast\",\"pastry\"]','2025-12-01 20:31:54','2025-12-01 20:31:54',NULL),
('019adbd4-4cf9-70d6-9f6c-a9fb27078b31','Sourdough Loaf','BR-SOU-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',2,NULL,'Artisan sourdough bread with tangy flavor',6.50,2.00,4,'pcs',800.00,1,1,NULL,'[\"gluten\"]','[\"artisan\",\"sourdough\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4daa-7214-afe9-03776588ca95','Banana Bread','BR-BAN-002','019adbd4-0ac6-73c1-aab2-48530e36a7fc',2,NULL,'Moist banana bread with walnuts',5.99,2.50,7,'pcs',900.00,1,1,NULL,'[\"gluten\",\"eggs\",\"nuts\"]','[\"sweet\",\"popular\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4dc3-7382-b92e-5605467ba98a','Chocolate Cake Slice','CK-CHO-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',3,NULL,'Rich chocolate layer cake with chocolate ganache',7.50,2.80,5,'pcs',200.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"chocolate\",\"dessert\",\"popular\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4dce-7299-89d1-f4448ff4d0ca','Chocolate Chip Cookie','CO-CHI-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',4,NULL,'Classic chocolate chip cookies',2.50,0.80,10,'pcs',50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"popular\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4e2d-7309-915b-901ffa859a11','Oatmeal Raisin Cookie','CO-OAT-002','019adbd4-0ac6-73c1-aab2-48530e36a7fc',4,NULL,'Wholesome oatmeal cookies with plump raisins',2.50,0.75,10,'pcs',50.00,1,1,NULL,'[\"gluten\",\"dairy\",\"eggs\"]','[\"cookie\",\"healthy\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4e3e-7018-9dce-b0babd3e563c','Vanilla Gelato Base','GB-VAN-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',9,NULL,'Premium vanilla gelato base mixture',0.00,8.50,3,'kg',1000.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"base\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4e69-73ce-a6f1-66d2413dbd6d','Chocolate Gelato','GF-CHO-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',10,NULL,'Rich dark chocolate gelato',4.50,1.80,30,'grams',100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"chocolate\",\"popular\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4ec9-7212-9283-70157bce003e','Strawberry Gelato','GF-STR-002','019adbd4-0ac6-73c1-aab2-48530e36a7fc',10,NULL,'Fresh strawberry gelato',4.50,1.90,30,'grams',100.00,1,1,NULL,'[\"dairy\"]','[\"gelato\",\"fruit\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4ee5-72bf-bb4c-bf3db6824547','Pistachio Gelato','GF-PIS-003','019adbd4-0ac6-73c1-aab2-48530e36a7fc',10,NULL,'Authentic pistachio gelato from Sicily',5.50,2.50,30,'grams',100.00,1,1,NULL,'[\"dairy\",\"nuts\"]','[\"gelato\",\"premium\",\"nuts\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4efb-7075-8306-8554887d95e0','Dark Chocolate Truffle','CH-DAR-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',15,NULL,'Hand-rolled dark chocolate truffle',2.50,0.90,21,'pcs',20.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"premium\",\"truffle\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4f11-70a4-b4cb-38457db08736','Salted Caramel Chocolate','CH-SAL-002','019adbd4-0ac6-73c1-aab2-48530e36a7fc',15,NULL,'Milk chocolate with salted caramel filling',2.75,1.00,21,'pcs',25.00,1,1,NULL,'[\"dairy\"]','[\"chocolate\",\"caramel\",\"popular\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL),
('019adbd4-4fa1-7097-94e0-223b12e5b3ee','Fruit Gummies','CD-FRU-001','019adbd4-0ac6-73c1-aab2-48530e36a7fc',16,NULL,'Assorted fruit-flavored gummy candies',1.50,0.40,180,'grams',100.00,1,1,NULL,'[]','[\"candy\",\"fruit\",\"kids\"]','2025-12-01 20:31:55','2025-12-01 20:31:55',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
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
set autocommit=0;
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
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
  `currency` varchar(3) NOT NULL DEFAULT 'NGN',
  `exchange_rate` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `payment_status` enum('paid','partial','pending') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_purchase_number_unique` (`purchase_number`),
  KEY `purchases_branch_id_foreign` (`branch_id`),
  CONSTRAINT `purchases_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
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
) ENGINE=InnoDB AUTO_INCREMENT=465 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_ingredients`
--

LOCK TABLES `recipe_ingredients` WRITE;
/*!40000 ALTER TABLE `recipe_ingredients` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `recipe_ingredients` VALUES
(1,1,2,500.0000,'grams',0.0020,5.00,0,'Required for Butter Croissant','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(2,1,4,300.0000,'grams',0.0080,2.00,1,'Required for Butter Croissant','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(3,1,1,50.0000,'grams',0.0010,3.00,2,'Required for Butter Croissant','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(4,1,8,150.0000,'ml',0.0030,1.00,3,'Required for Butter Croissant','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(5,1,5,0.5000,'units',15.0000,0.00,4,'Required for Butter Croissant','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(6,1,10,15.0000,'grams',0.0200,0.00,5,'Required for Butter Croissant','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(7,1,12,10.0000,'grams',0.0010,0.00,6,'Required for Butter Croissant','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(8,2,2,280.0000,'grams',0.0020,5.00,0,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(9,2,4,225.0000,'grams',0.0080,2.00,1,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(10,2,1,200.0000,'grams',0.0010,3.00,2,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(11,2,5,0.5000,'units',15.0000,0.00,3,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(12,2,6,0.0100,'liters',50.0000,0.00,4,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(13,2,7,300.0000,'grams',0.0150,1.00,5,'Required for Chocolate Chip Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(14,3,2,250.0000,'grams',0.0020,5.00,0,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(15,3,3,75.0000,'grams',0.0250,2.00,1,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(16,3,1,400.0000,'grams',0.0010,3.00,2,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(17,3,5,0.6700,'units',15.0000,0.00,3,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(18,3,11,125.0000,'ml',0.0050,1.00,4,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(19,3,6,0.0100,'liters',50.0000,0.00,5,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(20,3,9,200.0000,'ml',0.0100,1.00,6,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(21,3,7,200.0000,'grams',0.0150,1.00,7,'Required for Chocolate Cake Slice','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(22,4,8,2.0000,'liters',3.0000,2.00,0,'Required for Chocolate Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(23,4,9,1.5000,'liters',10.0000,2.00,1,'Required for Chocolate Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(24,4,1,600.0000,'grams',0.0010,3.00,2,'Required for Chocolate Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(25,4,3,200.0000,'grams',0.0250,2.00,3,'Required for Chocolate Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(26,4,5,1.0000,'units',15.0000,0.00,4,'Required for Chocolate Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(27,5,8,1.8000,'liters',3.0000,2.00,0,'Required for Strawberry Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(28,5,9,1.2000,'liters',10.0000,2.00,1,'Required for Strawberry Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(29,5,1,550.0000,'grams',0.0010,3.00,2,'Required for Strawberry Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(30,5,5,0.8000,'units',15.0000,0.00,3,'Required for Strawberry Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(31,6,8,1.8000,'liters',3.0000,2.00,0,'Required for Pistachio Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(32,6,9,1.3000,'liters',10.0000,2.00,1,'Required for Pistachio Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(33,6,1,580.0000,'grams',0.0010,3.00,2,'Required for Pistachio Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(34,6,5,0.9000,'units',15.0000,0.00,3,'Required for Pistachio Gelato','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(35,7,2,450.0000,'grams',0.0020,5.00,0,'Required for Almond Danish','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(36,7,4,280.0000,'grams',0.0080,2.00,1,'Required for Almond Danish','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(37,7,1,120.0000,'grams',0.0010,3.00,2,'Required for Almond Danish','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(38,7,5,0.6000,'units',15.0000,0.00,3,'Required for Almond Danish','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(39,7,8,100.0000,'ml',0.0030,1.00,4,'Required for Almond Danish','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(40,7,6,0.0050,'liters',50.0000,0.00,5,'Required for Almond Danish','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(41,8,2,220.0000,'grams',0.0020,5.00,0,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(42,8,4,200.0000,'grams',0.0080,2.00,1,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(43,8,1,180.0000,'grams',0.0010,3.00,2,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(44,8,5,0.5000,'units',15.0000,0.00,3,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(45,8,6,0.0080,'liters',50.0000,0.00,4,'Required for Oatmeal Raisin Cookie','Standard preparation','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(46,9,2,1000.0000,'grams',0.0020,5.00,0,'Required for Sourdough Loaf','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(47,9,12,20.0000,'grams',0.0010,0.00,1,'Required for Sourdough Loaf','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(48,9,10,10.0000,'grams',0.0200,0.00,2,'Required for Sourdough Loaf','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(49,10,2,280.0000,'grams',0.0020,5.00,0,'Required for Banana Bread','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(50,10,1,200.0000,'grams',0.0010,3.00,1,'Required for Banana Bread','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(51,10,4,120.0000,'grams',0.0080,2.00,2,'Required for Banana Bread','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(52,10,5,0.5000,'units',15.0000,0.00,3,'Required for Banana Bread','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(53,10,11,80.0000,'ml',0.0050,1.00,4,'Required for Banana Bread','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(54,10,6,0.0050,'liters',50.0000,0.00,5,'Required for Banana Bread','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(55,11,7,500.0000,'grams',0.0150,2.00,0,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(56,11,9,300.0000,'ml',0.0100,1.00,1,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(57,11,4,50.0000,'grams',0.0080,1.00,2,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(58,11,3,100.0000,'grams',0.0250,2.00,3,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(59,11,6,0.0050,'liters',50.0000,0.00,4,'Required for Dark Chocolate Truffle','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(60,12,7,600.0000,'grams',0.0150,2.00,0,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(61,12,1,200.0000,'grams',0.0010,3.00,1,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(62,12,9,200.0000,'ml',0.0100,1.00,2,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(63,12,4,80.0000,'grams',0.0080,1.00,3,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(64,12,12,5.0000,'grams',0.0010,0.00,4,'Required for Salted Caramel Chocolate','Standard preparation','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(65,13,85,4.4210,'units',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(66,13,78,1.1977,'units',0.0000,0.00,1,'Nobis voluptatem eligendi porro et fugit voluptatem.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(67,13,62,6.0761,'grams',0.0000,0.00,2,'Iste et commodi blanditiis reprehenderit animi alias autem.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(68,13,67,4.6261,'ml',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(69,13,86,2.2555,'grams',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(70,13,26,7.2072,'grams',0.0000,0.00,5,'Molestiae nemo occaecati eos deserunt sed.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(71,13,86,9.2918,'units',0.0000,0.00,6,'Ullam enim fugiat non quae laborum.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(72,13,74,5.6170,'kg',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(73,13,14,4.3317,'grams',0.0000,0.00,8,'Error adipisci qui ea non odit rem.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(74,13,16,7.9492,'ml',0.0000,0.00,9,'Occaecati odit et nostrum maxime dignissimos molestias eaque.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(75,13,67,3.7638,'grams',0.0000,0.00,10,'Maiores architecto dolorem fugiat quasi mollitia.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(76,13,23,8.6882,'kg',0.0000,0.00,11,'Placeat alias excepturi in quos non dignissimos.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(77,13,14,8.2413,'grams',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(78,13,22,3.2486,'units',0.0000,0.00,13,'Illo repudiandae animi fugit.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(79,13,31,8.5373,'units',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(80,13,80,9.6102,'units',0.0000,0.00,15,'Dolorem amet minima consectetur unde perspiciatis.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(81,13,45,9.8240,'grams',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(82,13,98,8.2840,'ml',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(83,13,35,5.0502,'grams',0.0000,0.00,18,'Explicabo qui eaque minima culpa ipsum.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(84,13,21,9.5063,'grams',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(85,14,23,7.5881,'pcs',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(86,14,66,2.4196,'pcs',0.0000,0.00,1,'Consequatur iste similique fugiat architecto nemo.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(87,14,84,1.3332,'liters',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(88,14,8,9.4424,'kg',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(89,14,93,5.3324,'units',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(90,14,97,1.1871,'units',0.0000,0.00,5,'Sed voluptate odit quae porro sint.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(91,14,30,5.8278,'liters',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(92,14,93,1.8917,'units',0.0000,0.00,7,'Id rem quod dolor quia.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(93,14,22,9.7757,'ml',0.0000,0.00,8,'Eum sint ex quia dolorum eos nisi.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(94,14,87,2.2532,'liters',0.0000,0.00,9,'Laudantium quia nihil quibusdam et adipisci placeat inventore facere.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(95,14,20,5.3677,'liters',0.0000,0.00,10,'Ut corrupti sequi soluta modi quod veritatis porro.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(96,14,86,6.8133,'liters',0.0000,0.00,11,'Eum atque accusantium consequatur est consequatur.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(97,14,47,8.6107,'pcs',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(98,14,81,3.6557,'pcs',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(99,14,20,6.3783,'ml',0.0000,0.00,14,'Qui asperiores placeat qui eaque sint commodi sequi.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(100,14,69,8.1417,'liters',0.0000,0.00,15,'Quisquam quo aspernatur ullam ad voluptatem sit deserunt.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(101,14,40,7.3826,'pcs',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(102,14,50,9.8825,'units',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(103,14,57,8.7728,'kg',0.0000,0.00,18,'Totam est mollitia dolor ea et quisquam.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(104,14,13,5.7565,'pcs',0.0000,0.00,19,'Itaque asperiores aut deleniti illo voluptas distinctio debitis.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(105,15,34,7.1515,'grams',0.0000,0.00,0,'Sunt sint animi nisi repellat est maiores.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(106,15,4,3.1204,'liters',0.0000,0.00,1,'Incidunt dolorem provident voluptatem fuga nobis voluptatem.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(107,15,32,2.2903,'pcs',0.0000,0.00,2,'Quia animi error commodi aut aut et odio aut.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(108,15,98,5.6126,'pcs',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(109,15,58,0.6122,'grams',0.0000,0.00,4,'Et porro sequi ad illum debitis ratione unde.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(110,15,69,8.3217,'liters',0.0000,0.00,5,'Odio quo sequi incidunt molestiae accusamus.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(111,15,27,6.7015,'units',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(112,15,86,8.4724,'grams',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(113,15,27,2.1202,'kg',0.0000,0.00,8,'Distinctio iste aut sed animi et ut.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(114,15,6,6.1217,'pcs',0.0000,0.00,9,'Occaecati dolores quas modi tenetur et hic enim.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(115,15,45,5.5301,'pcs',0.0000,0.00,10,'Voluptas eum sunt ad et.',NULL,'2025-12-01 20:31:59','2025-12-01 20:31:59'),
(116,15,69,8.5046,'liters',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(117,15,57,9.4829,'liters',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(118,15,79,7.7163,'liters',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(119,15,9,2.7628,'kg',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(120,15,46,5.2137,'grams',0.0000,0.00,15,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(121,15,36,5.0254,'units',0.0000,0.00,16,'Similique commodi officiis eaque cum.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(122,15,57,2.6867,'pcs',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(123,15,15,5.3577,'kg',0.0000,0.00,18,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(124,15,91,0.7572,'grams',0.0000,0.00,19,'Atque vel aliquam officiis.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(125,16,17,8.2374,'pcs',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(126,16,18,3.5672,'kg',0.0000,0.00,1,'Aut labore dolor repellat et error consequatur corrupti et.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(127,16,59,7.2698,'units',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(128,16,30,7.4018,'ml',0.0000,0.00,3,'Cumque atque ex expedita debitis praesentium veniam nihil.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(129,16,81,3.7743,'pcs',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(130,16,62,6.7239,'grams',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(131,16,25,0.7389,'grams',0.0000,0.00,6,'Voluptates dignissimos officiis eligendi ut praesentium nam.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(132,16,83,1.4094,'liters',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(133,16,79,1.9076,'kg',0.0000,0.00,8,'Ratione dolores qui consequatur laborum et velit.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(134,16,44,4.6649,'grams',0.0000,0.00,9,'Consequatur sapiente quo omnis et corporis voluptatem facere.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(135,16,36,1.2817,'ml',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(136,16,42,8.8974,'units',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(137,16,23,4.8308,'ml',0.0000,0.00,12,'Et nemo quia qui vel optio.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(138,16,34,8.9802,'grams',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(139,16,31,1.1487,'units',0.0000,0.00,14,'Iusto amet rerum tenetur.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(140,16,77,7.7498,'units',0.0000,0.00,15,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(141,16,2,2.1069,'pcs',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(142,16,6,3.4809,'units',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(143,16,20,3.6795,'kg',0.0000,0.00,18,'Ratione fugit et asperiores quia quisquam qui.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(144,16,26,1.3259,'units',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(145,17,37,2.2735,'grams',0.0000,0.00,0,'Quisquam eum cum commodi error.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(146,17,81,9.5942,'liters',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(147,17,14,3.9806,'liters',0.0000,0.00,2,'Consequuntur pariatur autem temporibus.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(148,17,25,1.1384,'units',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(149,17,61,2.9619,'units',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(150,17,55,9.0543,'pcs',0.0000,0.00,5,'Accusantium optio omnis maxime tenetur ex mollitia et.',NULL,'2025-12-01 20:32:00','2025-12-01 20:32:00'),
(151,17,92,7.8999,'units',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(152,17,29,4.2910,'liters',0.0000,0.00,7,'Quos exercitationem est et neque quidem et.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(153,17,51,0.6072,'units',0.0000,0.00,8,'Autem natus perferendis et eum blanditiis nesciunt.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(154,17,54,2.6626,'grams',0.0000,0.00,9,'In est occaecati sint cum.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(155,17,61,8.1471,'pcs',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(156,17,69,9.8382,'kg',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(157,17,27,5.2446,'pcs',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(158,17,89,7.0380,'kg',0.0000,0.00,13,'Et soluta quod aspernatur.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(159,17,88,4.4536,'units',0.0000,0.00,14,'Iusto voluptas qui suscipit occaecati illum sint et.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(160,17,73,7.8277,'units',0.0000,0.00,15,'A placeat quo qui atque.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(161,17,29,8.8784,'ml',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(162,17,28,3.9059,'pcs',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(163,17,45,0.5152,'units',0.0000,0.00,18,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(164,17,15,0.4386,'kg',0.0000,0.00,19,'Qui ea accusamus ea officia voluptatem ea omnis incidunt.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(165,18,41,1.4794,'liters',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(166,18,31,2.4525,'ml',0.0000,0.00,1,'Sed inventore at et asperiores maiores a sed quia.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(167,18,39,1.7493,'pcs',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(168,18,19,8.8671,'units',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(169,18,39,6.0346,'liters',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(170,18,66,0.3420,'ml',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(171,18,81,3.5027,'grams',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(172,18,40,8.5086,'liters',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(173,18,38,8.4837,'units',0.0000,0.00,8,'Temporibus voluptatibus doloribus debitis aliquam perspiciatis.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(174,18,1,8.4126,'ml',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(175,18,7,6.8994,'grams',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(176,18,26,0.5081,'ml',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(177,18,21,6.2751,'grams',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(178,18,33,9.4474,'grams',0.0000,0.00,13,'Autem explicabo nam veniam ea est aspernatur.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(179,18,68,8.7923,'liters',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(180,18,88,9.2184,'grams',0.0000,0.00,15,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(181,18,84,8.9617,'pcs',0.0000,0.00,16,'Mollitia nostrum ut nobis non maxime.',NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(182,18,42,7.7553,'kg',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(183,18,84,9.8936,'ml',0.0000,0.00,18,NULL,NULL,'2025-12-01 20:32:01','2025-12-01 20:32:01'),
(184,18,5,9.0128,'liters',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(185,19,99,5.5555,'liters',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(186,19,32,3.3171,'kg',0.0000,0.00,1,'Aut in doloribus tempore dignissimos reiciendis quam ut.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(187,19,27,2.0064,'liters',0.0000,0.00,2,'Omnis aliquid aliquam consequuntur distinctio blanditiis tenetur iste.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(188,19,77,7.5578,'ml',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(189,19,94,7.7516,'pcs',0.0000,0.00,4,'Qui temporibus facilis molestias molestiae labore et id.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(190,19,46,4.1017,'liters',0.0000,0.00,5,'Aut dolores est qui labore modi atque aut.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(191,19,88,5.5369,'grams',0.0000,0.00,6,'Vero culpa provident aut ipsam animi possimus provident.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(192,19,36,2.5380,'liters',0.0000,0.00,7,'Autem quasi aut in nesciunt possimus officia facilis aut.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(193,19,90,9.4124,'grams',0.0000,0.00,8,'Vel ratione quia numquam odio voluptatem ipsam iusto laboriosam.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(194,19,23,0.9409,'units',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(195,19,5,4.0481,'units',0.0000,0.00,10,'Asperiores fuga quo eum non praesentium.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(196,19,61,5.7680,'kg',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(197,19,13,9.4968,'units',0.0000,0.00,12,'Quod alias recusandae eos minima eum magnam.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(198,19,27,8.6019,'pcs',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(199,19,59,8.0525,'ml',0.0000,0.00,14,'Quas autem nostrum praesentium optio.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(200,19,40,7.1445,'units',0.0000,0.00,15,'Fugit quo ut et ut magni inventore libero.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(201,19,44,6.0156,'pcs',0.0000,0.00,16,'Et animi nihil laudantium cum eaque qui itaque.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(202,19,85,5.4282,'grams',0.0000,0.00,17,'Et pariatur est repellendus tempore.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(203,19,60,9.4264,'kg',0.0000,0.00,18,'Eius et debitis eius ab sed sit modi.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(204,19,13,3.7546,'ml',0.0000,0.00,19,'Adipisci quas eos impedit sit quo sed.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(205,20,34,9.9652,'grams',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(206,20,1,5.9114,'liters',0.0000,0.00,1,'Quis qui esse rem in placeat minima optio.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(207,20,31,6.8374,'liters',0.0000,0.00,2,'Ut ratione aut voluptatem libero nisi.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(208,20,6,4.7977,'grams',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(209,20,85,4.6969,'ml',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(210,20,18,2.2543,'grams',0.0000,0.00,5,'Ut eligendi quas facere sapiente laboriosam repudiandae itaque.',NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(211,20,24,2.2811,'pcs',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:02','2025-12-01 20:32:02'),
(212,20,84,6.7725,'kg',0.0000,0.00,7,'Nihil et voluptatem aut et.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(213,20,60,0.7868,'liters',0.0000,0.00,8,'Eveniet corrupti eaque deserunt commodi quod quaerat.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(214,20,85,9.2152,'pcs',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(215,20,37,7.4108,'pcs',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(216,20,12,5.8649,'ml',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(217,20,37,9.0843,'pcs',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(218,20,64,6.8496,'pcs',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(219,20,34,7.9160,'liters',0.0000,0.00,14,'Atque veritatis consequatur voluptatem odit vitae ullam nobis.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(220,20,64,7.9851,'grams',0.0000,0.00,15,'Est expedita dolor quisquam reiciendis.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(221,20,56,5.0651,'grams',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(222,20,66,5.7117,'kg',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(223,20,50,0.6906,'liters',0.0000,0.00,18,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(224,20,88,2.0923,'pcs',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(225,21,75,5.8169,'kg',0.0000,0.00,0,'Sed et quis magni deserunt.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(226,21,67,9.2460,'pcs',0.0000,0.00,1,'Ut commodi reiciendis sunt rerum quibusdam a deserunt.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(227,21,18,9.6654,'liters',0.0000,0.00,2,'Velit non maxime non.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(228,21,54,5.9366,'units',0.0000,0.00,3,'Voluptates veniam magnam incidunt iusto totam.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(229,21,35,6.2795,'liters',0.0000,0.00,4,'Eaque in dignissimos qui itaque sed et.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(230,21,66,3.6313,'liters',0.0000,0.00,5,'Dolores non nostrum dolor officiis reiciendis.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(231,21,16,4.7475,'liters',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(232,21,67,3.9301,'grams',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(233,21,62,3.6146,'kg',0.0000,0.00,8,'Vitae libero rerum molestiae eos voluptatum aliquam.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(234,21,60,8.5047,'kg',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(235,21,41,6.2460,'liters',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(236,21,9,6.1871,'units',0.0000,0.00,11,'Porro accusamus reiciendis ut voluptatibus minus rerum.',NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(237,21,61,5.7651,'units',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(238,21,23,3.8519,'grams',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(239,21,84,1.6745,'pcs',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(240,21,81,9.4613,'grams',0.0000,0.00,15,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(241,21,75,9.5779,'units',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:03','2025-12-01 20:32:03'),
(242,21,38,3.2532,'liters',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(243,21,62,1.2883,'units',0.0000,0.00,18,'Voluptates ut enim quae mollitia quod.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(244,21,65,6.5294,'units',0.0000,0.00,19,'Atque nesciunt ullam et quaerat.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(245,22,69,4.3493,'grams',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(246,22,90,3.8016,'grams',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(247,22,64,5.2863,'units',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(248,22,6,2.6329,'grams',0.0000,0.00,3,'Magnam sapiente et dolor quia.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(249,22,20,5.3894,'units',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(250,22,7,4.5338,'kg',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(251,22,65,7.1540,'pcs',0.0000,0.00,6,'Corrupti mollitia sapiente aut natus eos in sint.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(252,22,42,9.8651,'units',0.0000,0.00,7,'Quos quo iure minima beatae.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(253,22,99,7.0440,'pcs',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(254,22,12,5.0618,'liters',0.0000,0.00,9,'Occaecati laboriosam sit cum ullam beatae.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(255,22,73,5.2503,'kg',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(256,22,86,2.9867,'liters',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(257,22,95,7.2527,'pcs',0.0000,0.00,12,'Quis vel commodi distinctio ratione ab delectus.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(258,22,72,9.8479,'ml',0.0000,0.00,13,'Tempore qui quod rerum similique aut vel.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(259,22,78,6.5875,'grams',0.0000,0.00,14,'Mollitia beatae nihil cumque corporis provident et.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(260,22,42,9.6441,'grams',0.0000,0.00,15,'Eveniet qui quia dolorum molestiae quia.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(261,22,8,0.2888,'pcs',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(262,22,29,6.6915,'ml',0.0000,0.00,17,'Nam in qui sed veritatis sint molestias.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(263,22,90,0.3800,'units',0.0000,0.00,18,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(264,22,32,5.3844,'units',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(265,23,97,6.1531,'units',0.0000,0.00,0,'Autem qui cupiditate beatae nihil minus et in.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(266,23,81,6.5632,'pcs',0.0000,0.00,1,'Porro quibusdam vero tempore enim aut.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(267,23,23,3.7162,'pcs',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(268,23,12,0.5705,'liters',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(269,23,38,5.8155,'grams',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(270,23,73,9.7331,'liters',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(271,23,94,9.4767,'kg',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(272,23,85,0.5883,'liters',0.0000,0.00,7,'Neque et ut nobis error.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(273,23,91,1.6364,'kg',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(274,23,43,5.2174,'kg',0.0000,0.00,9,'Quia dolore illum vel qui voluptatem odit veniam.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(275,23,29,6.8594,'kg',0.0000,0.00,10,'Iusto quis ratione non qui autem.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(276,23,100,9.2966,'units',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(277,23,81,3.1399,'units',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(278,23,88,3.1136,'kg',0.0000,0.00,13,'Similique velit illo et et quae cupiditate animi.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(279,23,39,2.6205,'pcs',0.0000,0.00,14,'Similique odio sit voluptas incidunt.',NULL,'2025-12-01 20:32:04','2025-12-01 20:32:04'),
(280,23,37,8.3777,'liters',0.0000,0.00,15,'Occaecati aut sit eos veniam aperiam eveniet.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(281,23,7,7.6383,'liters',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(282,23,54,5.2586,'grams',0.0000,0.00,17,'Et nihil porro omnis harum sit aliquam.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(283,23,31,1.6213,'kg',0.0000,0.00,18,'Aut dolorem dolor quos autem enim voluptatem.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(284,23,87,3.0664,'grams',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(285,24,40,9.6082,'kg',0.0000,0.00,0,'Quidem in maxime repudiandae iure voluptas ratione.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(286,24,7,8.7737,'units',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(287,24,29,6.6795,'grams',0.0000,0.00,2,'Velit ea officia non est enim sed qui.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(288,24,83,6.4820,'pcs',0.0000,0.00,3,'Sunt itaque optio consectetur repudiandae vel.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(289,24,65,7.7722,'pcs',0.0000,0.00,4,'Dignissimos impedit velit blanditiis dolores et labore.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(290,24,60,4.5632,'grams',0.0000,0.00,5,'Mollitia qui repellat tempora sed vel ab.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(291,24,65,9.2706,'units',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(292,24,42,3.9882,'units',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(293,24,54,1.4014,'pcs',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(294,24,20,5.2025,'kg',0.0000,0.00,9,'Asperiores ipsum molestias ipsa nam expedita qui non.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(295,24,68,9.8960,'kg',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(296,24,50,8.0660,'ml',0.0000,0.00,11,'Quasi quia dolorem omnis autem ut maxime optio id.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(297,24,60,9.6918,'grams',0.0000,0.00,12,'Molestiae molestias quis aut natus.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(298,24,100,8.3050,'units',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(299,24,11,9.5942,'pcs',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(300,24,49,9.5339,'pcs',0.0000,0.00,15,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(301,24,94,3.0215,'liters',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(302,24,93,1.0275,'kg',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(303,24,79,4.8327,'units',0.0000,0.00,18,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(304,24,47,4.3821,'units',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(305,25,52,8.3738,'liters',0.0000,0.00,0,'Ipsum est quod fugit illo.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(306,25,28,6.5171,'units',0.0000,0.00,1,'Quaerat velit omnis recusandae veritatis neque.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(307,25,100,5.1439,'pcs',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(308,25,78,5.1912,'grams',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(309,25,45,2.5522,'units',0.0000,0.00,4,'Explicabo libero harum et recusandae id.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(310,25,64,4.1782,'grams',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(311,25,63,5.8517,'units',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(312,25,65,5.8647,'kg',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(313,25,67,5.6606,'liters',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(314,25,7,6.2775,'units',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(315,25,75,7.4563,'grams',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(316,25,100,4.2606,'grams',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(317,25,29,3.6214,'pcs',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(318,25,63,0.8082,'kg',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(319,25,61,4.5814,'liters',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(320,25,53,8.0072,'ml',0.0000,0.00,15,'Qui qui est debitis quia soluta.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(321,25,47,1.9875,'pcs',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(322,25,83,7.0182,'liters',0.0000,0.00,17,'Ex quia voluptatem natus deserunt libero alias cumque.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(323,25,96,4.2150,'kg',0.0000,0.00,18,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(324,25,77,7.2831,'kg',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(325,26,33,6.7829,'kg',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(326,26,27,9.1923,'units',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(327,26,80,9.7097,'units',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(328,26,68,0.6864,'grams',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(329,26,74,9.3256,'grams',0.0000,0.00,4,'Provident et quod soluta omnis molestiae et.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(330,26,6,2.8187,'liters',0.0000,0.00,5,'Eius quidem ut cumque expedita dolorum.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(331,26,46,6.2917,'kg',0.0000,0.00,6,'Recusandae cum minima dolor amet consequatur libero unde.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(332,26,49,9.1201,'units',0.0000,0.00,7,'Autem provident in autem suscipit est.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(333,26,75,5.9504,'units',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(334,26,81,0.1225,'units',0.0000,0.00,9,'Veniam quo odit qui facere distinctio.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(335,26,86,0.1159,'units',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(336,26,87,3.2477,'ml',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(337,26,32,6.7274,'kg',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(338,26,15,8.6407,'units',0.0000,0.00,13,'Pariatur consectetur non autem quia ut aut.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(339,26,31,8.9596,'liters',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(340,26,38,9.6798,'ml',0.0000,0.00,15,'Sed est provident earum eos aspernatur nesciunt quam.',NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(341,26,63,4.8532,'kg',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:05','2025-12-01 20:32:05'),
(342,26,75,5.1096,'units',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(343,26,26,2.7712,'grams',0.0000,0.00,18,'Dolores eos qui fugiat.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(344,26,59,6.4720,'kg',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(345,27,37,4.9084,'kg',0.0000,0.00,0,'Qui repudiandae doloribus unde provident.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(346,27,45,9.8627,'ml',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(347,27,91,7.6337,'ml',0.0000,0.00,2,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(348,27,53,9.7057,'kg',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(349,27,96,8.9067,'kg',0.0000,0.00,4,'Quo at qui architecto qui porro et.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(350,27,84,0.3216,'liters',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(351,27,53,8.5243,'units',0.0000,0.00,6,'Tenetur mollitia laborum tempore dolorem.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(352,27,87,7.4531,'ml',0.0000,0.00,7,'Doloremque aliquid vel voluptatem quod sed eos et.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(353,27,35,3.8886,'units',0.0000,0.00,8,'Minima ut perspiciatis porro reiciendis voluptates laudantium corrupti.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(354,27,66,1.7946,'liters',0.0000,0.00,9,'Blanditiis doloremque tenetur reprehenderit voluptates eligendi necessitatibus rerum.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(355,27,60,1.1911,'kg',0.0000,0.00,10,'Velit praesentium nihil eum labore eos quis.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(356,27,29,1.1729,'grams',0.0000,0.00,11,'Qui rem incidunt quod ullam.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(357,27,70,6.3458,'liters',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(358,27,32,8.9883,'liters',0.0000,0.00,13,'Nesciunt voluptatum vero optio molestiae id.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(359,27,32,6.5045,'ml',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(360,27,81,0.1111,'kg',0.0000,0.00,15,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(361,27,52,3.1011,'liters',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(362,27,62,8.7990,'kg',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(363,27,91,9.8617,'grams',0.0000,0.00,18,'Rerum voluptates voluptates quis voluptatem qui cum aliquid.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(364,27,40,8.8301,'units',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(365,28,55,5.8443,'liters',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(366,28,96,5.5649,'ml',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(367,28,5,7.6755,'ml',0.0000,0.00,2,'Suscipit ut ut officia odio sint.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(368,28,86,9.7692,'units',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(369,28,74,7.8114,'liters',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(370,28,15,2.1980,'grams',0.0000,0.00,5,'Ratione sunt ut inventore ab.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(371,28,83,5.1501,'units',0.0000,0.00,6,'Rerum modi optio architecto et.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(372,28,73,7.0762,'liters',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(373,28,12,8.6894,'grams',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(374,28,34,9.4674,'ml',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(375,28,48,0.3148,'units',0.0000,0.00,10,'Ullam neque vel in aut architecto rerum.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(376,28,1,5.8366,'grams',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(377,28,98,7.0278,'liters',0.0000,0.00,12,'Minima doloribus eum similique sunt.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(378,28,23,9.3860,'kg',0.0000,0.00,13,'Rerum ut totam qui iusto nihil.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(379,28,55,7.1613,'pcs',0.0000,0.00,14,'Dolore rerum est illo officia id.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(380,28,81,3.5465,'ml',0.0000,0.00,15,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(381,28,84,6.9421,'ml',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(382,28,46,1.7347,'pcs',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(383,28,57,3.3254,'ml',0.0000,0.00,18,'Quas quaerat commodi in aut non aut id.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(384,28,73,8.3964,'kg',0.0000,0.00,19,'Molestiae mollitia accusamus nihil reiciendis.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(385,29,72,8.7294,'ml',0.0000,0.00,0,'Magnam reprehenderit accusamus quos beatae minus.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(386,29,85,1.4583,'liters',0.0000,0.00,1,'Sed ut nihil et optio repellendus et et cumque.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(387,29,27,7.5025,'ml',0.0000,0.00,2,'Non nobis cupiditate nihil occaecati sed.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(388,29,92,6.8721,'grams',0.0000,0.00,3,'Tempora voluptatum nostrum eveniet fugiat explicabo fugiat commodi.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(389,29,43,8.4048,'grams',0.0000,0.00,4,'Modi officia ut autem at blanditiis.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(390,29,88,4.1133,'pcs',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(391,29,36,3.3938,'liters',0.0000,0.00,6,'Sed incidunt placeat eos quo minus ratione est doloribus.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(392,29,66,0.3063,'kg',0.0000,0.00,7,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(393,29,59,2.2788,'units',0.0000,0.00,8,'Et accusantium iure velit similique et consectetur repellat.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(394,29,55,2.8432,'grams',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(395,29,19,7.1919,'ml',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(396,29,33,5.1328,'liters',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(397,29,64,7.9178,'kg',0.0000,0.00,12,'Consectetur facere a harum officiis deserunt in et.',NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(398,29,45,7.8142,'liters',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(399,29,38,5.8782,'pcs',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:06','2025-12-01 20:32:06'),
(400,29,94,8.0637,'units',0.0000,0.00,15,'Ea cum illo omnis eos corporis possimus.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(401,29,14,5.0576,'units',0.0000,0.00,16,'Aut numquam voluptatem rerum dolorem consequatur.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(402,29,42,4.7480,'units',0.0000,0.00,17,'Quam ullam est soluta ad repellendus quos illo.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(403,29,25,1.5682,'grams',0.0000,0.00,18,'At ut dolores corrupti hic nihil aspernatur facilis.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(404,29,60,2.8986,'kg',0.0000,0.00,19,'Quis quis soluta quia atque.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(405,30,56,6.6552,'ml',0.0000,0.00,0,'Aut quod possimus deleniti voluptatem amet dolores fugiat dolor.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(406,30,39,8.2780,'pcs',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(407,30,77,8.2861,'grams',0.0000,0.00,2,'Magni natus qui esse voluptas et.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(408,30,100,5.5002,'ml',0.0000,0.00,3,'Illo sint cum rem impedit dolorum itaque laborum.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(409,30,94,9.2220,'liters',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(410,30,14,6.8018,'units',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(411,30,66,3.3472,'grams',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(412,30,89,7.5426,'kg',0.0000,0.00,7,'Tenetur fugit distinctio nihil aut neque.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(413,30,88,6.3089,'liters',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(414,30,30,0.9306,'ml',0.0000,0.00,9,'Tempora exercitationem at quas nesciunt quaerat omnis.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(415,30,34,6.5153,'units',0.0000,0.00,10,'Vitae quis dolorem est.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(416,30,86,9.6141,'liters',0.0000,0.00,11,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(417,30,15,6.4141,'pcs',0.0000,0.00,12,'Explicabo consequatur similique in libero facere atque tempora.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(418,30,89,3.9078,'liters',0.0000,0.00,13,'Necessitatibus aut odit tempora expedita possimus error quibusdam.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(419,30,38,9.9824,'grams',0.0000,0.00,14,'Eum distinctio ducimus ducimus tenetur ut voluptatem quas.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(420,30,50,1.4438,'kg',0.0000,0.00,15,'Architecto quam nisi reiciendis ut dolores molestiae.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(421,30,24,2.2473,'kg',0.0000,0.00,16,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(422,30,61,6.6099,'pcs',0.0000,0.00,17,NULL,NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(423,30,68,5.5952,'kg',0.0000,0.00,18,'Corporis odit qui placeat libero.',NULL,'2025-12-01 20:32:07','2025-12-01 20:32:07'),
(424,30,2,8.6349,'liters',0.0000,0.00,19,'Deleniti consequatur est nisi.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(425,31,76,2.5341,'liters',0.0000,0.00,0,'Corporis molestias molestias quisquam et.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(426,31,18,3.5054,'grams',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(427,31,36,7.7175,'liters',0.0000,0.00,2,'Et sunt et dignissimos non.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(428,31,83,2.4496,'pcs',0.0000,0.00,3,'Eum minus reprehenderit at excepturi.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(429,31,12,4.3892,'ml',0.0000,0.00,4,'Rerum ab distinctio consequatur aut quod id.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(430,31,75,0.3217,'grams',0.0000,0.00,5,'Inventore ipsum possimus cum vitae fugiat praesentium ipsum.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(431,31,33,1.0961,'pcs',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(432,31,42,5.7336,'kg',0.0000,0.00,7,'Omnis qui consequatur autem perferendis voluptas ut.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(433,31,88,3.9847,'kg',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(434,31,52,0.3847,'pcs',0.0000,0.00,9,NULL,NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(435,31,88,0.1460,'liters',0.0000,0.00,10,'Sunt velit autem quasi voluptatem sed ipsa totam.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(436,31,18,7.7844,'liters',0.0000,0.00,11,'Ex est adipisci et consequuntur qui aut commodi.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(437,31,14,8.2283,'pcs',0.0000,0.00,12,NULL,NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(438,31,70,5.7015,'units',0.0000,0.00,13,'Ea quia dolores quibusdam et repellendus et.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(439,31,78,0.9915,'kg',0.0000,0.00,14,NULL,NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(440,31,43,3.5135,'ml',0.0000,0.00,15,'Aperiam aliquid excepturi qui.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(441,31,9,4.3080,'units',0.0000,0.00,16,'Voluptates est alias labore et dolores.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(442,31,56,3.4546,'grams',0.0000,0.00,17,'Et voluptatem ipsum corrupti placeat exercitationem ea.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(443,31,64,3.4132,'units',0.0000,0.00,18,'Ut iure ratione animi dolores non in.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(444,31,8,6.3444,'kg',0.0000,0.00,19,'Dolore consequatur commodi eum sunt in molestiae sit.',NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(445,32,23,4.5529,'kg',0.0000,0.00,0,NULL,NULL,'2025-12-01 20:32:08','2025-12-01 20:32:08'),
(446,32,74,8.0757,'kg',0.0000,0.00,1,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(447,32,64,0.3905,'grams',0.0000,0.00,2,'Veniam minus aut pariatur et cum.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(448,32,52,2.0873,'pcs',0.0000,0.00,3,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(449,32,13,6.3757,'liters',0.0000,0.00,4,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(450,32,72,1.4525,'units',0.0000,0.00,5,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(451,32,91,7.7959,'units',0.0000,0.00,6,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(452,32,33,5.6722,'grams',0.0000,0.00,7,'Quia tempora nemo et vel.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(453,32,4,1.1774,'grams',0.0000,0.00,8,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(454,32,68,7.4435,'grams',0.0000,0.00,9,'Nisi a dolor perspiciatis non.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(455,32,41,3.1462,'ml',0.0000,0.00,10,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(456,32,59,7.5908,'kg',0.0000,0.00,11,'Molestiae perspiciatis ipsa laboriosam rerum sint sunt et minima.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(457,32,69,8.4618,'kg',0.0000,0.00,12,'Enim id iure et.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(458,32,42,8.9843,'units',0.0000,0.00,13,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(459,32,92,9.2925,'pcs',0.0000,0.00,14,'Distinctio labore veritatis non saepe commodi.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(460,32,35,1.0535,'ml',0.0000,0.00,15,'Ut sit dolorem quia aliquam voluptatem.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(461,32,30,1.6644,'grams',0.0000,0.00,16,'Ab voluptatum aut delectus vero facilis et.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(462,32,91,7.4267,'pcs',0.0000,0.00,17,'Asperiores ut eos dolor fugit ea ex.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(463,32,67,6.1164,'units',0.0000,0.00,18,'Est dolor nesciunt in.',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(464,32,78,7.1019,'grams',0.0000,0.00,19,NULL,NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09');
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
  `product_type` enum('gelato_base','gelato_flavor','pastry','hot_kitchen','beverage') NOT NULL,
  `cost_per_unit` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `uom` enum('grams','kg','liters','ml','pcs','units') NOT NULL DEFAULT 'pcs',
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
  CONSTRAINT `recipes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `recipes` VALUES
(1,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4c76-7278-a5e2-5c8a441adf88','Butter Croissant','PT-BUT-001-RCP','pastry',0.4923,'pcs',24.00,240,'[\"Mix flour, sugar, salt, and yeast in a large bowl\",\"Add cold butter pieces and work into the dough\",\"Knead until smooth and elastic\",\"Rest dough in refrigerator for 2 hours\",\"Roll out and fold dough multiple times (lamination)\",\"Cut into triangles and roll into croissant shape\",\"Proof for 1-2 hours until doubled\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes until golden\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:56','2025-12-01 20:31:56'),
(2,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4dce-7299-89d1-f4448ff4d0ca','Chocolate Chip Cookie','CO-CHI-001-RCP','pastry',0.4215,'pcs',36.00,45,'[\"Cream together butter and sugars\",\"Beat in eggs and vanilla extract\",\"Mix in flour, baking soda, and salt\",\"Fold in chocolate chips\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 10-12 minutes\",\"Cool on baking sheet for 5 minutes before transferring\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:56','2025-12-01 20:31:57'),
(3,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4dc3-7382-b92e-5605467ba98a','Chocolate Cake Slice','CK-CHO-001-RCP','pastry',1.5901,'pcs',12.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mix dry ingredients: flour, cocoa powder, baking soda, salt\",\"Beat together eggs, sugar, oil, and vanilla\",\"Add hot water and mix until smooth\",\"Pour into greased cake pans\",\"Bake for 30-35 minutes\",\"Cool completely before frosting\",\"Prepare chocolate ganache and frost the cake\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(4,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4e69-73ce-a6f1-66d2413dbd6d','Chocolate Gelato','GF-CHO-001-RCP','gelato_flavor',0.8428,'grams',50.00,60,'[\"Heat milk and cream to 85\\u00b0C\",\"Whisk cocoa powder with some warm milk\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add cocoa mixture and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(5,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4ec9-7212-9283-70157bce003e','Strawberry Gelato','GF-STR-002-RCP','gelato_flavor',0.6063,'grams',50.00,60,'[\"Blend fresh strawberries to puree\",\"Heat milk, cream, and sugar to 85\\u00b0C\",\"Mix with egg yolks\",\"Cool completely\",\"Add strawberry puree\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(6,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4ee5-72bf-bb4c-bf3db6824547','Pistachio Gelato','GF-PIS-003-RCP','gelato_flavor',0.6573,'grams',50.00,70,'[\"Grind pistachios into fine paste\",\"Heat milk and cream to 85\\u00b0C\",\"Mix sugar with egg yolks\",\"Combine hot milk with egg mixture\",\"Add pistachio paste and mix well\",\"Cool to 4\\u00b0C\",\"Process in gelato machine for 25 minutes\",\"Store at -18\\u00b0C\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(7,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4ce9-7308-a696-1fe039e78981','Almond Danish','PT-ALM-002-RCP','pastry',0.7170,'pcs',18.00,180,'[\"Prepare puff pastry dough\",\"Roll and fold dough multiple times\",\"Cut into squares\",\"Add almond cream filling\",\"Top with sliced almonds\",\"Proof for 1 hour\",\"Brush with egg wash\",\"Bake at 200\\u00b0C for 15-18 minutes\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:57','2025-12-01 20:31:57'),
(8,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4e2d-7309-915b-901ffa859a11','Oatmeal Raisin Cookie','CO-OAT-002-RCP','pastry',0.2545,'pcs',40.00,40,'[\"Cream butter and sugars together\",\"Beat in eggs and vanilla\",\"Mix in flour, oats, and spices\",\"Fold in raisins\",\"Scoop dough onto baking sheets\",\"Bake at 180\\u00b0C for 12-14 minutes\",\"Cool before serving\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:57','2025-12-01 20:31:58'),
(9,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4cf9-70d6-9f6c-a9fb27078b31','Sourdough Loaf','BR-SOU-001-RCP','hot_kitchen',0.5800,'pcs',4.00,1440,'[\"Feed sourdough starter 12 hours before\",\"Mix flour, water, salt, and starter\",\"Autolyse for 30 minutes\",\"Stretch and fold every 30 minutes (4 times)\",\"Bulk ferment for 4-6 hours\",\"Shape into loaves\",\"Cold ferment overnight (12 hours)\",\"Score and bake at 230\\u00b0C for 35-40 minutes\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(10,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4daa-7214-afe9-03776588ca95','Banana Bread','BR-BAN-002-RCP','hot_kitchen',4.9636,'pcs',2.00,90,'[\"Preheat oven to 175\\u00b0C\",\"Mash ripe bananas\",\"Mix butter, sugar, and eggs\",\"Add mashed bananas\",\"Mix in flour, baking soda, and salt\",\"Pour into greased loaf pans\",\"Bake for 55-60 minutes\",\"Cool before slicing\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(11,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4efb-7075-8306-8554887d95e0','Dark Chocolate Truffle','CH-DAR-001-RCP','hot_kitchen',0.2777,'pcs',50.00,120,'[\"Chop dark chocolate finely\",\"Heat cream to simmering\",\"Pour over chocolate and let sit\",\"Stir until smooth ganache forms\",\"Cool and refrigerate for 2 hours\",\"Roll into balls\",\"Coat with cocoa powder\",\"Store in refrigerator\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(12,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,'019adbd4-4f11-70a4-b4cb-38457db08736','Salted Caramel Chocolate','CH-SAL-002-RCP','hot_kitchen',0.2512,'pcs',48.00,150,'[\"Make caramel with sugar and cream\",\"Add salt to caramel and cool\",\"Temper milk chocolate\",\"Fill molds halfway with chocolate\",\"Add caramel filling\",\"Top with more chocolate\",\"Cool and unmold\",\"Store in cool place\"]','active','01acda54-c639-3075-9150-37f5eed06ddb','App\\Models\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(13,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',3,NULL,'dignissimos Beverage','SKU-ZKVxwh2p','gelato_flavor',41.4204,'kg',81.54,60,'Nihil distinctio illo suscipit dolor eos asperiores vel. Quia commodi sunt sint et expedita dolores aut. Impedit aspernatur ut velit et sed aliquid voluptatem. Rem quis ratione facilis aut ipsum iste.','active','5880f31f-b4b3-3b42-b4fc-39343bbc55f7','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(14,'019adbd4-0b1e-7356-8544-47bf94c8ffed',4,NULL,'eum Gelato','SKU-PnzJ4Jy1','pastry',39.1209,'units',94.37,83,'Hic voluptatum voluptate qui quas illo et suscipit. Vitae ab consequatur vel dolor omnis. Occaecati natus quia aut. Consequuntur laborum maiores at minima nemo. Nam quia officiis et nihil quis rerum ut mollitia.','inactive','d6ec0a6c-8bc7-3db2-b089-d71802ba324e','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(15,'019adbd4-0b1e-7356-8544-47bf94c8ffed',4,NULL,'et Pastry','SKU-5v6uEq7k','pastry',42.8272,'liters',76.03,37,'Odit ut iste iure provident est non aut. Voluptas accusantium illum eius non facere deleniti minima. Consequatur aliquam natus assumenda facere numquam saepe ut. Est consequatur blanditiis aperiam quia saepe inventore doloribus.','active','a809e16d-e423-36c2-bb00-68f41bb4a5b6','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(16,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',1,NULL,'enim Gelato','SKU-aGLA7hsG','beverage',14.1181,'grams',83.01,18,'Ut quia vero consequatur incidunt qui. Nihil possimus nam delectus libero magni maxime praesentium cum. Qui consequatur commodi mollitia eaque nisi aut.','testing','e1d670da-31d0-34ab-a4b3-60920c21f52c','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(17,'019adbd4-0b1e-7356-8544-47bf94c8ffed',2,NULL,'consectetur Pastry','SKU-aS9YICm1','hot_kitchen',47.2279,'pcs',54.86,119,'Excepturi neque itaque recusandae in minima quisquam tempora. Eum et aut qui eius accusantium dolorum. Non ipsa quaerat nesciunt. Magnam ut dignissimos voluptas repellendus consectetur repellat. Sit repellat aspernatur quod nobis laborum ut.','testing','35eac18f-0c5e-3aa8-8c30-4a160a42dfae','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(18,'019adbd4-0b1e-7356-8544-47bf94c8ffed',1,NULL,'corrupti Gelato','SKU-SIFO5tLP','gelato_flavor',11.8122,'pcs',25.44,116,'Ut accusantium modi at ea praesentium id est. Aut sit sed et. Sunt explicabo at eum odit.','testing','a96b155e-227b-3e99-ab66-aabd7df7dab2','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(19,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',7,NULL,'et Pastry','SKU-HsbuEiPm','gelato_base',37.2399,'liters',26.34,103,'Ad et voluptates voluptatem a. Tenetur quod ea neque eius blanditiis dolore itaque aut. Amet placeat similique quia culpa et. Et ea omnis iure est pariatur vero dolorum. Distinctio dolor minima maiores maiores expedita voluptatum odio.','inactive','b00cadcd-c208-3f1b-8032-253755f9794f','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(20,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',8,NULL,'voluptate Beverage','SKU-5WqdO1IU','hot_kitchen',18.3711,'units',60.76,73,'Culpa cumque voluptate id cum dolorem sed voluptatum. Nulla sed explicabo odio dolores qui. Numquam dolorem voluptatem et et. Quis accusamus dolorem ut eligendi deleniti illum.','active','83925e11-9245-30a5-823a-ae02b23c52cf','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(21,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',7,NULL,'vel Pastry','SKU-VffwU3n7','pastry',19.4256,'liters',92.16,102,'Voluptas voluptate nihil cumque et sint architecto illo. Reprehenderit ipsum est ab corrupti sed cumque. Officiis officiis blanditiis eos ratione. Enim impedit corrupti fugiat unde temporibus omnis.','inactive','e97a58f9-3630-3a82-98b2-b7d8d95f9501','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(22,'019adbd4-0b07-7018-928c-e49d13068b9b',6,NULL,'delectus Beverage','SKU-EWPaNMaI','hot_kitchen',5.9118,'units',74.58,72,'Quae ut facere aut autem quis. Et sit sed voluptatem quisquam consequatur aliquam. Consectetur minus assumenda sed ut commodi.','active','cf164d0b-8c74-3d3f-8fec-d8007362d57e','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(23,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',6,NULL,'blanditiis Gelato','SKU-yQRlrDvr','beverage',1.0487,'grams',74.62,103,'Veniam soluta cupiditate a omnis sequi optio aliquid. Asperiores qui dolores id dignissimos atque quaerat ea. Dolore sapiente consequatur aspernatur excepturi et eligendi recusandae.','testing','e8f4424d-ea16-30a5-9cdc-b18f4acde6be','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(24,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,NULL,'ut Pastry','SKU-cMcdfEJC','beverage',27.3645,'pcs',13.38,91,'A quisquam aperiam beatae unde doloribus consequatur sunt. Beatae aut culpa nesciunt recusandae enim expedita aspernatur. Sint doloremque quod fugit fugit. Porro nesciunt dolor voluptate esse incidunt veritatis quia aperiam.','testing','42503b0d-68c3-374a-8f73-3dcc1b392ab8','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(25,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,NULL,'exercitationem Pastry','SKU-wwRjq20d','gelato_flavor',47.0675,'units',92.47,61,'Odit sunt minus quas vel. Pariatur optio ullam quos dolores blanditiis et dolore. Nulla est error dignissimos.','testing','4acd63a3-cac5-39ca-abb3-0920a9cd0b4c','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(26,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',6,NULL,'beatae Pastry','SKU-MnotAqzI','gelato_base',45.5207,'liters',45.29,52,'Id qui incidunt et iste ullam dolorem. Doloribus possimus molestiae aperiam rerum occaecati. Laborum corrupti ut facilis consequatur blanditiis dolorem placeat.','testing','da111e3f-7d76-383b-bb59-e86ac991db22','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(27,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',2,NULL,'autem Pastry','SKU-3Ry7Ow95','pastry',13.5265,'kg',84.71,110,'Placeat dolorem totam quo eius explicabo. Nobis harum cumque perspiciatis qui deleniti. Nesciunt nemo voluptas fuga pariatur sint minima et. Temporibus adipisci iure quidem commodi odio enim.','inactive','549e3510-564d-3f1f-b1cb-e93a49800b21','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(28,'019adbd4-0b07-7018-928c-e49d13068b9b',2,NULL,'enim Gelato','SKU-wRnvUPAt','hot_kitchen',35.6754,'units',83.33,98,'Reiciendis quos id porro unde aperiam iste. Eveniet culpa molestias est id quas aut doloremque. Rerum voluptas quam delectus.','inactive','b00cadcd-c208-3f1b-8032-253755f9794f','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(29,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',4,NULL,'doloremque Gelato','SKU-EyE0hhQz','gelato_flavor',26.1766,'ml',61.12,37,'Eos aspernatur blanditiis reiciendis aut amet. Aut fugiat libero rerum aliquam et minus harum et. Omnis placeat perferendis similique.','active','f6916761-d714-3e01-bde4-0b26ef7f84de','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(30,'019adbd4-0b1e-7356-8544-47bf94c8ffed',7,NULL,'et Pastry','SKU-8XtaoMCU','pastry',22.2982,'liters',92.50,68,'Provident porro et saepe ipsam illo. Excepturi quisquam porro culpa velit saepe eos assumenda. Sapiente voluptate fugiat consequatur iste eaque hic omnis quos.','active','2d142047-7ddd-35f6-b9a6-3fd3727460c8','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(31,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',1,NULL,'quia Gelato','SKU-LaUHPZQU','pastry',16.9771,'kg',39.70,120,'Ullam expedita et est quia accusantium. Qui ipsam similique et incidunt dolore sunt vel. Omnis qui recusandae sit odio praesentium sapiente.','testing','adcf9dae-510a-372d-bbc4-df91b7d182b2','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58'),
(32,'019adbd4-0b07-7018-928c-e49d13068b9b',3,NULL,'harum Beverage','SKU-LOPjYKS1','hot_kitchen',31.6620,'ml',11.55,41,'Numquam asperiores esse autem dolorem odit reprehenderit. Qui officiis excepturi quo non. Expedita aliquid ut sunt explicabo aut ut harum. Aut facere labore non sunt sed.','inactive','4acd63a3-cac5-39ca-abb3-0920a9cd0b4c','Database\\Seeders\\Employee','2025-12-01 20:31:58','2025-12-01 20:31:58');
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
(48,1),
(49,1),
(50,1),
(52,1),
(56,1),
(60,1),
(61,1),
(62,1),
(48,2),
(49,2),
(50,2),
(51,2),
(52,2),
(53,2),
(54,2),
(55,2),
(56,2),
(57,2),
(58,2),
(59,2),
(60,2),
(61,2),
(62,2),
(63,2),
(64,2),
(65,2),
(66,2),
(1,3),
(2,3),
(3,3),
(4,3),
(5,3),
(6,3),
(7,3),
(8,3),
(9,3),
(10,3),
(11,3),
(12,3),
(13,3),
(14,3),
(15,3),
(16,3),
(17,3),
(18,3),
(19,3),
(20,3),
(21,3),
(22,3),
(23,3),
(24,3),
(25,3),
(26,3),
(27,3),
(28,3),
(29,3),
(30,3),
(31,3),
(32,3),
(33,3),
(34,3),
(35,3),
(36,3),
(37,3),
(38,3),
(39,3),
(40,3),
(41,3),
(42,3),
(43,3),
(44,3),
(45,3),
(46,3),
(47,3),
(2,4),
(5,4),
(6,4),
(7,4),
(8,4),
(9,4),
(17,4),
(18,4),
(23,4),
(26,4),
(27,4),
(2,5),
(10,5),
(11,5),
(12,5),
(13,5),
(17,5),
(18,5),
(23,5),
(26,5),
(27,5),
(2,6),
(18,6),
(19,6),
(20,6),
(21,6),
(22,6),
(23,6),
(24,6),
(25,6),
(26,6),
(27,6),
(2,7),
(14,7),
(15,7),
(16,7),
(17,7),
(18,7),
(23,7),
(26,7),
(5,8),
(6,8),
(7,8),
(9,8),
(12,8),
(17,8),
(18,8),
(5,9),
(6,9),
(7,9),
(9,9),
(17,9),
(18,9),
(5,10),
(6,10),
(7,10),
(9,10),
(10,10),
(12,10),
(17,10),
(18,10),
(10,11),
(11,11),
(12,11),
(13,11),
(17,11),
(18,11),
(10,12),
(11,12),
(12,12),
(13,12),
(17,12),
(18,12),
(5,13),
(6,13),
(7,13),
(17,13),
(5,14),
(6,14),
(7,14),
(17,14),
(5,15),
(6,15),
(7,15),
(17,15),
(10,16),
(12,16),
(17,16),
(10,17),
(12,17),
(17,17),
(10,18),
(12,18),
(17,18),
(14,19),
(15,19),
(16,19),
(17,19),
(14,20),
(17,20),
(18,21),
(19,21),
(20,21),
(23,21),
(24,21),
(67,22),
(68,22),
(69,22),
(70,22),
(71,22),
(72,22),
(73,22),
(74,22),
(75,22),
(76,22),
(77,22),
(78,22),
(79,22),
(80,22),
(81,22),
(82,22),
(83,22),
(84,22),
(85,22),
(86,22),
(87,22),
(88,22),
(89,22),
(90,22),
(91,22),
(92,22),
(93,22),
(94,22),
(95,22),
(96,22),
(97,22),
(98,22),
(99,22),
(100,22),
(101,22),
(102,22),
(103,22),
(104,22),
(105,22),
(106,22),
(107,22),
(108,22),
(109,22),
(110,22),
(111,22),
(112,22),
(113,22),
(114,22),
(115,22),
(116,22),
(117,22),
(118,22),
(119,22),
(120,22),
(121,22),
(122,22),
(123,22),
(124,22),
(125,22),
(126,22),
(127,22),
(128,22),
(129,22),
(130,22),
(131,22),
(132,22),
(133,22),
(134,22),
(135,22),
(136,22),
(137,22),
(138,22),
(139,22),
(140,22),
(141,22),
(142,22),
(143,22),
(144,22),
(145,22),
(146,22),
(147,22),
(148,22),
(149,22),
(150,22),
(151,22),
(152,22),
(153,22),
(154,22),
(155,22),
(156,22),
(157,22),
(158,22),
(159,22),
(160,22),
(161,22),
(162,22),
(163,22),
(164,22),
(165,22),
(166,22),
(167,22),
(168,22),
(169,22),
(170,22),
(171,22),
(172,22),
(173,22);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles` VALUES
(1,'Admin','web','2025-12-01 20:31:28','2025-12-01 20:31:28'),
(2,'Super Admin','web','2025-12-01 20:31:29','2025-12-01 20:31:29'),
(3,'Managing Director','employees','2025-12-01 20:31:29','2025-12-01 20:31:29'),
(4,'Head of Production','employees','2025-12-01 20:31:29','2025-12-01 20:31:29'),
(5,'Sales Manager','employees','2025-12-01 20:31:29','2025-12-01 20:31:29'),
(6,'HR Manager','employees','2025-12-01 20:31:30','2025-12-01 20:31:30'),
(7,'Inventory Manager','employees','2025-12-01 20:31:30','2025-12-01 20:31:30'),
(8,'Chef','employees','2025-12-01 20:31:30','2025-12-01 20:31:30'),
(9,'Head of Gelato','employees','2025-12-01 20:31:30','2025-12-01 20:31:30'),
(10,'Confectionaries Manager','employees','2025-12-01 20:31:30','2025-12-01 20:31:30'),
(11,'Till Supervisor','employees','2025-12-01 20:31:31','2025-12-01 20:31:31'),
(12,'Corner Store Manager','employees','2025-12-01 20:31:31','2025-12-01 20:31:31'),
(13,'Kitchen Staff','employees','2025-12-01 20:31:31','2025-12-01 20:31:31'),
(14,'Gelato Production Staff','employees','2025-12-01 20:31:31','2025-12-01 20:31:31'),
(15,'Confectionaries Production Staff','employees','2025-12-01 20:31:32','2025-12-01 20:31:32'),
(16,'Cashier','employees','2025-12-01 20:31:32','2025-12-01 20:31:32'),
(17,'Corner Store Staff','employees','2025-12-01 20:31:32','2025-12-01 20:31:32'),
(18,'Confectionaries Sales Staff','employees','2025-12-01 20:31:32','2025-12-01 20:31:32'),
(19,'Stock Controller','employees','2025-12-01 20:31:32','2025-12-01 20:31:32'),
(20,'Store Keeper','employees','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(21,'HR Officer','employees','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(22,'MD','web','2025-12-01 20:31:33','2025-12-01 20:31:33'),
(23,'test_role_1764634084','web','2025-12-01 23:08:04','2025-12-01 23:08:04'),
(24,'test_role2_1764634084','employee','2025-12-01 23:08:04','2025-12-01 23:08:04');
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
  CONSTRAINT `salary_history_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
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
  `status` enum('pending','completed','cancelled','refunded','hold') NOT NULL DEFAULT 'completed',
  `order_type` enum('dine-in','takeaway','delivery','dine_in','glovo','transfer') NOT NULL DEFAULT 'dine_in',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_sale_number_unique` (`sale_number`),
  KEY `sales_sales_shift_id_foreign` (`sales_shift_id`),
  KEY `sales_department_id_foreign` (`department_id`),
  KEY `sales_branch_id_department_id_sale_time_index` (`branch_id`,`department_id`,`sale_time`),
  KEY `sales_table_id_foreign` (`table_id`),
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
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
  CONSTRAINT `sales_shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
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
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` char(36) NOT NULL,
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
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shifts_shift_number_unique` (`shift_number`),
  KEY `shifts_department_id_foreign` (`department_id`),
  KEY `shifts_branch_id_department_id_shift_date_index` (`branch_id`,`department_id`,`shift_date`),
  KEY `shifts_employee_id_index` (`employee_id`),
  CONSTRAINT `shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shifts_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `shifts` VALUES
(1,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',7,'220d8237-39a2-3537-8c57-7b56280eda39','SHIFT-8VcuxC','2025-11-30','morning','2025-12-01 11:23:11',NULL,'closed','Dolorem saepe impedit iusto itaque nihil eum harum natus.','2025-12-01 20:32:09','2025-12-01 20:32:09'),
(2,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',5,'aeb7f0df-3bea-3513-ad12-98239232ec2c','SHIFT-eXqTla','2025-11-06','night','2025-12-01 00:03:20','2025-12-02 16:44:07','closed','Sint consequatur qui velit molestiae quaerat qui facilis.','2025-12-01 20:32:09','2025-12-01 20:32:09'),
(3,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,'beedeb93-582a-3ed2-aa22-19960d76ac72','SHIFT-7ag2lz','2025-11-06','afternoon','2025-12-01 01:02:44',NULL,'submitted','Est aliquam saepe voluptas libero aut quidem deleniti.','2025-12-01 20:32:09','2025-12-01 20:32:09'),
(4,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',3,'15b9b0d3-b46a-3793-92c3-a31bdee6db5d','SHIFT-elGG9y','2025-11-02','afternoon','2025-11-30 20:56:00','2025-12-01 22:07:14','active','Quae voluptates et ut rerum nemo.','2025-12-01 20:32:09','2025-12-01 20:32:09'),
(5,'019adbd4-0b1e-7356-8544-47bf94c8ffed',3,'83925e11-9245-30a5-823a-ae02b23c52cf','SHIFT-K4riEu','2025-11-30','night','2025-12-01 10:56:12',NULL,'closed',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(6,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,'1f58d5d0-c31f-375c-a6b2-03e4a5f599ff','SHIFT-xgRncV','2025-11-16','morning','2025-12-01 15:55:23',NULL,'closed','Aliquam dignissimos quia excepturi et quae ut.','2025-12-01 20:32:09','2025-12-01 20:32:09'),
(7,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',1,'83925e11-9245-30a5-823a-ae02b23c52cf','SHIFT-2FBbBb','2025-11-15','night','2025-12-01 17:13:20','2025-12-02 15:14:06','submitted','Et provident cupiditate pariatur aut quaerat explicabo.','2025-12-01 20:32:09','2025-12-01 20:32:09'),
(8,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',3,'3e1289ba-e385-3b84-8243-58093af0fe80','SHIFT-FIKtYh','2025-11-09','morning','2025-12-01 13:20:55','2025-12-02 02:30:55','submitted',NULL,'2025-12-01 20:32:09','2025-12-01 20:32:09'),
(9,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,'a3f73790-38ea-396c-b9e2-66df082296b1','SHIFT-p4w9jx','2025-11-22','night','2025-12-01 10:28:21',NULL,'active',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(10,'019adbd4-0b1e-7356-8544-47bf94c8ffed',1,'f342e8ac-ffa8-333c-b7ab-0fac5f3fc931','SHIFT-tTcMyl','2025-11-10','night','2025-12-01 18:58:45','2025-12-02 13:38:39','active','Temporibus expedita blanditiis et doloremque adipisci.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(11,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',8,'72812433-e623-3d39-b488-214e34f99e23','SHIFT-YHDIpv','2025-11-13','afternoon','2025-12-01 04:01:32','2025-12-02 09:10:41','active','Distinctio iure sint unde.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(12,'019adbd4-0b07-7018-928c-e49d13068b9b',7,'518ea6dc-8459-3c3f-93a2-95bb67d9e61c','SHIFT-RJ0sYt','2025-11-14','night','2025-12-01 19:28:44','2025-12-02 09:43:08','active',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(13,'019adbd4-0b07-7018-928c-e49d13068b9b',6,'45e4db87-284b-3870-8979-04e1fa9aaae8','SHIFT-oys77j','2025-11-04','morning','2025-12-01 01:06:01','2025-12-02 09:25:25','closed','Aliquid quia consequatur fugit sit maxime pariatur repellat.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(14,'019adbd4-0b07-7018-928c-e49d13068b9b',5,'8efe9b66-7c30-388b-867e-8d4342e53b21','SHIFT-ZL2Uer','2025-11-28','morning','2025-12-01 04:22:03',NULL,'closed',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(15,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',4,'53af11df-f62e-3a07-b8cc-e05368efa7a5','SHIFT-8ARABr','2025-11-12','night','2025-12-01 08:14:36',NULL,'closed','Eos officia praesentium in voluptatum ut rerum unde.','2025-12-01 20:32:10','2025-12-01 20:32:10'),
(16,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',4,'a9f468ba-2865-3277-a5fb-9d4f46034317','SHIFT-sr1Ogu','2025-11-11','morning','2025-12-01 06:03:14','2025-12-02 06:02:51','submitted',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(17,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',5,'e1d670da-31d0-34ab-a4b3-60920c21f52c','SHIFT-zY0goJ','2025-11-22','night','2025-12-01 12:55:06','2025-12-02 17:25:01','active',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(18,'019adbd4-0b07-7018-928c-e49d13068b9b',8,'dff7b89c-14ac-3b16-ae34-da8068950c5b','SHIFT-x5wQBW','2025-11-16','night','2025-12-01 02:15:16','2025-12-02 15:17:14','closed',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(19,'019adbd4-0b1e-7356-8544-47bf94c8ffed',5,'a3f73790-38ea-396c-b9e2-66df082296b1','SHIFT-NnbhVy','2025-11-21','morning','2025-11-30 22:39:34',NULL,'active',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(20,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,'42503b0d-68c3-374a-8f73-3dcc1b392ab8','SHIFT-4OKbq8','2025-11-09','morning','2025-12-01 01:15:04','2025-12-02 18:11:58','closed',NULL,'2025-12-01 20:32:10','2025-12-01 20:32:10'),
(21,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',2,'3e1289ba-e385-3b84-8243-58093af0fe80','SHFT-20251201-0001','2025-12-01','afternoon','2025-12-01 21:23:52',NULL,'active',NULL,'2025-12-01 21:23:52','2025-12-01 21:23:52'),
(22,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',3,'0357d01c-7205-374e-b9e8-7c72d8f158ae','SHFT-20251202-0001','2025-12-02','morning','2025-12-02 08:14:41',NULL,'active',NULL,'2025-12-02 08:14:41','2025-12-02 08:14:41');
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
  `notes` text DEFAULT NULL,
  `movement_date` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_stock_id_foreign` (`stock_id`),
  KEY `stock_movements_moved_by_type_moved_by_id_index` (`moved_by_type`,`moved_by_id`),
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
(1,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',1,197.00,13.00,6.00,375.8000,'2025-11-05','good','2026-09-15','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(2,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',2,278.00,20.00,9.00,400.6000,'2025-11-28','warning','2026-02-09','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(3,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',3,360.00,36.00,12.00,424.7000,'2025-11-10','good','2026-05-21','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(4,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',4,252.00,20.00,6.00,140.2000,'2025-11-04','warning','2026-01-07','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(5,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',5,344.00,15.00,0.00,164.7000,'2025-11-28','warning','2026-01-13','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(6,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',6,55.00,2.00,1.00,339.1000,'2025-11-12','good','2026-06-10','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(7,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',7,178.00,17.00,4.00,98.8000,'2025-11-16','critical','2025-12-26','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(8,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',8,85.00,2.00,3.00,180.4000,'2025-11-12','good','2026-06-13','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(9,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',9,316.00,25.00,15.00,487.5000,'2025-11-11','good','2026-11-05','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(10,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',10,183.00,16.00,9.00,482.1000,'2025-11-11','warning','2026-02-12','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(11,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',11,105.00,7.00,1.00,437.6000,'2025-11-28','good','2026-04-13','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(12,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',12,122.00,1.00,1.00,75.9000,'2025-11-11','good','2026-09-05','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(13,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',13,593.00,18.00,9.00,49.9000,'2025-11-09','good',NULL,'2025-12-01 20:31:49','2025-12-01 20:31:49'),
(14,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',14,256.00,21.00,8.00,15.6000,'2025-11-28','good',NULL,'2025-12-01 20:31:49','2025-12-01 20:31:49'),
(15,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',15,336.00,1.00,12.00,23.2000,'2025-11-10','good',NULL,'2025-12-01 20:31:49','2025-12-01 20:31:49'),
(16,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',16,102.00,2.00,5.00,39.2000,'2025-11-14','good',NULL,'2025-12-01 20:31:49','2025-12-01 20:31:49'),
(17,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',17,42.00,1.00,2.00,206.5000,'2025-11-17','warning','2026-01-04','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(18,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',18,50.00,1.00,1.00,290.3000,'2025-11-27','warning','2026-02-08','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(19,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',19,55.00,5.00,0.00,89.3000,'2025-11-21','warning','2026-02-05','2025-12-01 20:31:49','2025-12-01 20:31:49'),
(20,'019adbd4-0ac6-73c1-aab2-48530e36a7fc',20,13.00,1.00,0.00,492.0000,'2025-11-14','good',NULL,'2025-12-01 20:31:49','2025-12-01 20:31:49'),
(21,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',21,356.00,10.00,1.00,419.2000,'2025-11-23','good','2026-07-18','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(22,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',22,152.00,11.00,4.00,123.5000,'2025-11-06','warning','2026-01-31','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(23,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',23,226.00,9.00,2.00,323.0000,'2025-11-20','warning','2026-01-23','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(24,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',24,252.00,25.00,11.00,196.3000,'2025-11-10','critical','2025-12-30','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(25,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',25,132.00,0.00,0.00,402.6000,'2025-11-13','warning','2026-01-13','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(26,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',26,347.00,21.00,8.00,241.1000,'2025-11-07','critical','2025-12-27','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(27,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',27,278.00,12.00,0.00,359.3000,'2025-11-30','critical','2025-12-16','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(28,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',28,138.00,10.00,1.00,174.6000,'2025-11-19','critical','2025-12-23','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(29,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',29,239.00,5.00,3.00,274.6000,'2025-11-16','good','2026-03-04','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(30,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',30,381.00,13.00,10.00,116.9000,'2025-11-10','critical','2025-12-11','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(31,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',31,330.00,7.00,0.00,345.7000,'2025-11-25','good','2026-05-10','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(32,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',32,353.00,14.00,13.00,464.8000,'2025-11-10','warning','2026-01-26','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(33,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',33,573.00,33.00,23.00,19.3000,'2025-11-27','critical',NULL,'2025-12-01 20:31:50','2025-12-01 20:31:50'),
(34,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',34,379.00,8.00,7.00,24.5000,'2025-11-03','warning',NULL,'2025-12-01 20:31:50','2025-12-01 20:31:50'),
(35,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',35,341.00,30.00,2.00,32.8000,'2025-11-03','good',NULL,'2025-12-01 20:31:50','2025-12-01 20:31:50'),
(36,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',36,773.00,52.00,1.00,47.6000,'2025-11-16','good',NULL,'2025-12-01 20:31:50','2025-12-01 20:31:50'),
(37,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',37,52.00,2.00,0.00,58.2000,'2025-11-06','critical','2025-12-13','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(38,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',38,106.00,7.00,2.00,139.1000,'2025-11-28','good','2026-11-03','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(39,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',39,72.00,4.00,1.00,184.6000,'2025-11-23','good','2026-05-23','2025-12-01 20:31:50','2025-12-01 20:31:50'),
(40,'019adbd4-0aec-707f-b4e0-ea5ba04b7612',40,16.00,0.00,0.00,202.6000,'2025-11-15','warning',NULL,'2025-12-01 20:31:50','2025-12-01 20:31:50'),
(41,'019adbd4-0b07-7018-928c-e49d13068b9b',41,239.00,10.00,5.00,377.5000,'2025-11-06','warning','2026-02-22','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(42,'019adbd4-0b07-7018-928c-e49d13068b9b',42,377.00,32.00,5.00,55.4000,'2025-11-30','warning','2026-01-07','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(43,'019adbd4-0b07-7018-928c-e49d13068b9b',43,385.00,3.00,14.00,333.5000,'2025-11-11','warning','2026-02-01','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(44,'019adbd4-0b07-7018-928c-e49d13068b9b',44,76.00,1.00,2.00,72.1000,'2025-11-05','critical','2025-12-21','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(45,'019adbd4-0b07-7018-928c-e49d13068b9b',45,118.00,1.00,4.00,82.7000,'2025-11-22','good','2026-04-25','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(46,'019adbd4-0b07-7018-928c-e49d13068b9b',46,92.00,9.00,2.00,423.0000,'2025-11-24','good','2026-10-14','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(47,'019adbd4-0b07-7018-928c-e49d13068b9b',47,295.00,20.00,0.00,359.6000,'2025-11-10','critical','2025-12-17','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(48,'019adbd4-0b07-7018-928c-e49d13068b9b',48,212.00,20.00,9.00,442.2000,'2025-11-15','good','2026-09-28','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(49,'019adbd4-0b07-7018-928c-e49d13068b9b',49,322.00,14.00,2.00,349.2000,'2025-11-04','critical','2025-12-10','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(50,'019adbd4-0b07-7018-928c-e49d13068b9b',50,209.00,13.00,7.00,186.0000,'2025-11-01','critical','2025-12-14','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(51,'019adbd4-0b07-7018-928c-e49d13068b9b',51,88.00,8.00,3.00,465.8000,'2025-11-17','good','2026-04-16','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(52,'019adbd4-0b07-7018-928c-e49d13068b9b',52,366.00,1.00,0.00,306.7000,'2025-11-24','good','2026-11-11','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(53,'019adbd4-0b07-7018-928c-e49d13068b9b',53,284.00,15.00,14.00,43.4000,'2025-11-28','good',NULL,'2025-12-01 20:31:51','2025-12-01 20:31:51'),
(54,'019adbd4-0b07-7018-928c-e49d13068b9b',54,730.00,41.00,30.00,34.5000,'2025-11-13','good',NULL,'2025-12-01 20:31:51','2025-12-01 20:31:51'),
(55,'019adbd4-0b07-7018-928c-e49d13068b9b',55,712.00,13.00,33.00,33.8000,'2025-11-26','good',NULL,'2025-12-01 20:31:51','2025-12-01 20:31:51'),
(56,'019adbd4-0b07-7018-928c-e49d13068b9b',56,330.00,25.00,11.00,5.4000,'2025-11-20','critical',NULL,'2025-12-01 20:31:51','2025-12-01 20:31:51'),
(57,'019adbd4-0b07-7018-928c-e49d13068b9b',57,43.00,0.00,1.00,194.4000,'2025-11-16','critical','2025-12-27','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(58,'019adbd4-0b07-7018-928c-e49d13068b9b',58,141.00,3.00,3.00,92.1000,'2025-11-18','warning','2026-02-07','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(59,'019adbd4-0b07-7018-928c-e49d13068b9b',59,40.00,4.00,1.00,39.7000,'2025-11-29','good','2026-03-11','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(60,'019adbd4-0b07-7018-928c-e49d13068b9b',60,7.00,0.00,0.00,660.0000,'2025-11-24','good',NULL,'2025-12-01 20:31:51','2025-12-01 20:31:51'),
(61,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',61,287.00,6.00,14.00,128.8000,'2025-11-29','warning','2026-01-06','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(62,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',62,333.00,33.00,10.00,107.5000,'2025-11-09','warning','2026-02-08','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(63,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',63,362.00,4.00,0.00,396.9000,'2025-11-06','critical','2025-12-11','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(64,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',64,362.00,31.00,5.00,438.6000,'2025-11-27','warning','2026-01-16','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(65,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',65,158.00,2.00,0.00,447.8000,'2025-11-17','warning','2026-01-23','2025-12-01 20:31:51','2025-12-01 20:31:51'),
(66,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',66,361.00,7.00,8.00,168.7000,'2025-11-02','good','2026-11-18','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(67,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',67,192.00,6.00,3.00,128.8000,'2025-11-22','good','2026-05-02','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(68,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',68,61.00,1.00,1.00,461.4000,'2025-11-17','good','2026-06-18','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(69,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',69,320.00,24.00,12.00,293.5000,'2025-11-18','critical','2025-12-27','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(70,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',70,190.00,1.00,1.00,152.4000,'2025-11-08','warning','2026-01-24','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(71,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',71,151.00,4.00,6.00,107.4000,'2025-11-16','critical','2025-12-16','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(72,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',72,343.00,5.00,14.00,357.9000,'2025-11-03','good','2026-09-28','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(73,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',73,693.00,24.00,11.00,35.3000,'2025-11-28','warning',NULL,'2025-12-01 20:31:52','2025-12-01 20:31:52'),
(74,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',74,337.00,24.00,7.00,40.9000,'2025-11-12','warning',NULL,'2025-12-01 20:31:52','2025-12-01 20:31:52'),
(75,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',75,524.00,31.00,11.00,34.4000,'2025-11-05','critical',NULL,'2025-12-01 20:31:52','2025-12-01 20:31:52'),
(76,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',76,258.00,24.00,2.00,43.4000,'2025-11-04','warning',NULL,'2025-12-01 20:31:52','2025-12-01 20:31:52'),
(77,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',77,110.00,11.00,2.00,229.4000,'2025-11-23','good','2026-08-03','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(78,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',78,94.00,2.00,4.00,213.9000,'2025-11-15','good','2026-11-23','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(79,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',79,29.00,2.00,1.00,207.8000,'2025-11-26','good','2026-03-09','2025-12-01 20:31:52','2025-12-01 20:31:52'),
(80,'019adbd4-0b13-726e-9aa8-3f26e5acec1b',80,17.00,1.00,0.00,1143.6000,'2025-11-01','good',NULL,'2025-12-01 20:31:52','2025-12-01 20:31:52'),
(81,'019adbd4-0b1e-7356-8544-47bf94c8ffed',81,198.00,3.00,3.00,418.3000,'2025-11-09','warning','2026-01-03','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(82,'019adbd4-0b1e-7356-8544-47bf94c8ffed',82,134.00,3.00,3.00,363.8000,'2025-11-29','good','2026-10-07','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(83,'019adbd4-0b1e-7356-8544-47bf94c8ffed',83,69.00,2.00,0.00,271.2000,'2025-11-28','good','2026-03-20','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(84,'019adbd4-0b1e-7356-8544-47bf94c8ffed',84,90.00,1.00,1.00,231.4000,'2025-11-28','good','2026-06-26','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(85,'019adbd4-0b1e-7356-8544-47bf94c8ffed',85,363.00,3.00,14.00,123.7000,'2025-11-27','critical','2025-12-20','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(86,'019adbd4-0b1e-7356-8544-47bf94c8ffed',86,319.00,10.00,11.00,165.1000,'2025-11-04','critical','2025-12-26','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(87,'019adbd4-0b1e-7356-8544-47bf94c8ffed',87,61.00,5.00,2.00,389.6000,'2025-11-09','good','2026-06-07','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(88,'019adbd4-0b1e-7356-8544-47bf94c8ffed',88,266.00,10.00,10.00,301.6000,'2025-11-27','good','2026-08-11','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(89,'019adbd4-0b1e-7356-8544-47bf94c8ffed',89,319.00,7.00,9.00,410.4000,'2025-11-24','good','2026-07-11','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(90,'019adbd4-0b1e-7356-8544-47bf94c8ffed',90,226.00,21.00,10.00,101.1000,'2025-11-24','critical','2025-12-16','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(91,'019adbd4-0b1e-7356-8544-47bf94c8ffed',91,167.00,15.00,3.00,366.5000,'2025-11-10','good','2026-10-25','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(92,'019adbd4-0b1e-7356-8544-47bf94c8ffed',92,145.00,7.00,4.00,212.0000,'2025-11-05','good','2026-07-02','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(93,'019adbd4-0b1e-7356-8544-47bf94c8ffed',93,206.00,4.00,1.00,7.6000,'2025-11-08','good',NULL,'2025-12-01 20:31:53','2025-12-01 20:31:53'),
(94,'019adbd4-0b1e-7356-8544-47bf94c8ffed',94,791.00,55.00,39.00,28.4000,'2025-11-09','good',NULL,'2025-12-01 20:31:53','2025-12-01 20:31:53'),
(95,'019adbd4-0b1e-7356-8544-47bf94c8ffed',95,343.00,27.00,2.00,19.0000,'2025-11-02','warning',NULL,'2025-12-01 20:31:53','2025-12-01 20:31:53'),
(96,'019adbd4-0b1e-7356-8544-47bf94c8ffed',96,480.00,17.00,18.00,20.2000,'2025-11-11','critical',NULL,'2025-12-01 20:31:53','2025-12-01 20:31:53'),
(97,'019adbd4-0b1e-7356-8544-47bf94c8ffed',97,67.00,4.00,0.00,140.5000,'2025-11-30','good','2026-09-10','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(98,'019adbd4-0b1e-7356-8544-47bf94c8ffed',98,120.00,0.00,0.00,68.0000,'2025-11-21','good','2026-09-18','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(99,'019adbd4-0b1e-7356-8544-47bf94c8ffed',99,35.00,3.00,1.00,254.3000,'2025-11-14','good','2026-11-26','2025-12-01 20:31:53','2025-12-01 20:31:53'),
(100,'019adbd4-0b1e-7356-8544-47bf94c8ffed',100,20.00,0.00,0.00,977.5000,'2025-11-21','good',NULL,'2025-12-01 20:31:53','2025-12-01 20:31:53');
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
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
  KEY `users_last_accessed_branch_id_foreign` (`last_accessed_branch_id`),
  CONSTRAINT `users_last_accessed_branch_id_foreign` FOREIGN KEY (`last_accessed_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
('019adbd4-0a1e-70af-adba-4dfe45affa1a',NULL,'Managing Director','md@sweettooth.com','$2y$12$J1ZwCjuWgwWfwSoEKmjQPubE6TRDQJolRnwMngPw0a1AlajmgnHg6',NULL,NULL,NULL,NULL,'JoIqXfTBwDxzPhbw1Li2hWiiQ2r98WZIffd8eEMngFS5R9OxLH6RXEeok50n','2025-12-01 20:31:37','2025-12-01 20:31:37');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
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

-- Dump completed on 2025-12-02 15:25:11
