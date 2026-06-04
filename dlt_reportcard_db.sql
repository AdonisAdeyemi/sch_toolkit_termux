/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnueabihf (armv8l)
--
-- Host: localhost    Database: reportcard_db
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-0+deb13u1 from Debian

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
-- Table structure for table `class_levels`
--

DROP TABLE IF EXISTS `class_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `slug` varchar(20) NOT NULL,
  `level_order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pricing_tiers`
--

DROP TABLE IF EXISTS `pricing_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pricing_tiers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `tokens` int(10) unsigned NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `per_print` decimal(10,2) NOT NULL,
  `highlight` tinyint(1) DEFAULT 0,
  `discount_per_print` decimal(10,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_academic_periods`
--

DROP TABLE IF EXISTS `report_academic_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_academic_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session` varchar(20) NOT NULL,
  `term` tinyint(4) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_attendance`
--

DROP TABLE IF EXISTS `report_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_attendance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `days_present` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_period` (`student_id`,`period_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_period_id` (`period_id`),
  CONSTRAINT `fk_attendance_period` FOREIGN KEY (`period_id`) REFERENCES `report_academic_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attendance_student` FOREIGN KEY (`student_id`) REFERENCES `report_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_card_preferences`
--

DROP TABLE IF EXISTS `report_card_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_card_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `primary_color_accent` varchar(20) DEFAULT NULL,
  `secondary_color_accent` varchar(20) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `logo_position` varchar(50) DEFAULT NULL,
  `printed_name` varchar(150) DEFAULT NULL,
  `logo_watermark` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_settings_school` (`school_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_class_subjects`
--

DROP TABLE IF EXISTS `report_class_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_class_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned NOT NULL,
  `report_subject_id` bigint(20) unsigned NOT NULL,
  `alias_name` varchar(150) DEFAULT NULL,
  `subject_order` int(11) DEFAULT 0,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_class_subject` (`class_id`,`report_subject_id`),
  KEY `idx_class` (`class_id`),
  KEY `idx_subject` (`report_subject_id`),
  KEY `idx_department` (`department_id`),
  CONSTRAINT `fk_cs_class` FOREIGN KEY (`class_id`) REFERENCES `report_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cs_department` FOREIGN KEY (`department_id`) REFERENCES `report_departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cs_report_subject` FOREIGN KEY (`report_subject_id`) REFERENCES `report_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_classes`
--

DROP TABLE IF EXISTS `report_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_school` (`school_id`),
  CONSTRAINT `fk_classes_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_comments`
--

DROP TABLE IF EXISTS `report_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `comment_type` enum('principal','class_teacher') NOT NULL,
  `assessment_type` enum('ca','exam') DEFAULT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_student` (`student_id`),
  KEY `idx_period` (`period_id`),
  CONSTRAINT `fk_comment_period` FOREIGN KEY (`period_id`) REFERENCES `report_academic_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comment_student` FOREIGN KEY (`student_id`) REFERENCES `report_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_course_teacher`
--

DROP TABLE IF EXISTS `report_course_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_course_teacher` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_subject_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subject` (`class_subject_id`),
  KEY `fk_ct_teacher` (`teacher_id`),
  CONSTRAINT `fk_ct_class_subject` FOREIGN KEY (`class_subject_id`) REFERENCES `report_class_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_departments`
--

DROP TABLE IF EXISTS `report_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_domain_scores`
--

DROP TABLE IF EXISTS `report_domain_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_domain_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `domain_id` bigint(20) unsigned NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `teacher_comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_domain_student` (`student_id`),
  KEY `fk_domain_period` (`period_id`),
  KEY `fk_domain_definition` (`domain_id`),
  CONSTRAINT `fk_domain_definition` FOREIGN KEY (`domain_id`) REFERENCES `report_domains` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_domain_period` FOREIGN KEY (`period_id`) REFERENCES `report_academic_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_domain_student` FOREIGN KEY (`student_id`) REFERENCES `report_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_domains`
--

DROP TABLE IF EXISTS `report_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_domains` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(100) NOT NULL,
  `domain_type` enum('affective','psychomotor') NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_exam_types`
--

DROP TABLE IF EXISTS `report_exam_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_exam_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `max_score` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_generated_pdfs`
--

DROP TABLE IF EXISTS `report_generated_pdfs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_generated_pdfs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `hash` varchar(64) NOT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `generated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_class_period` (`class_id`,`period_id`),
  KEY `idx_hash` (`hash`),
  KEY `fk_pdf_school` (`school_id`),
  KEY `fk_pdf_period` (`period_id`),
  KEY `fk_pdf_teacher` (`generated_by`),
  CONSTRAINT `fk_pdf_period` FOREIGN KEY (`period_id`) REFERENCES `report_academic_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pdf_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_results`
--

DROP TABLE IF EXISTS `report_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `class_subject_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `ca1_score` int(11) DEFAULT NULL,
  `ca2_score` int(11) DEFAULT NULL,
  `exam_score` int(11) DEFAULT NULL,
  `total_score` int(11) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_result` (`student_id`,`class_subject_id`,`period_id`),
  KEY `idx_student` (`student_id`),
  KEY `idx_subject` (`class_subject_id`),
  KEY `idx_period` (`period_id`),
  CONSTRAINT `fk_rr_class_subject` FOREIGN KEY (`class_subject_id`) REFERENCES `report_class_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rr_period` FOREIGN KEY (`period_id`) REFERENCES `report_academic_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rr_student` FOREIGN KEY (`student_id`) REFERENCES `report_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_school_period_settings`
--

DROP TABLE IF EXISTS `report_school_period_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_school_period_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `days_open` int(10) unsigned NOT NULL DEFAULT 124,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_of_vacation` date DEFAULT NULL,
  `date_of_resumption` date DEFAULT NULL,
  `term_start_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_school_period_settings` (`school_id`,`period_id`),
  KEY `period_id` (`period_id`),
  KEY `school_id` (`school_id`),
  CONSTRAINT `fk_period_settings_period` FOREIGN KEY (`period_id`) REFERENCES `report_academic_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_period_settings_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_student_departments`
--

DROP TABLE IF EXISTS `report_student_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_student_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_student_period` (`student_id`,`period_id`),
  KEY `idx_student` (`student_id`),
  KEY `idx_period` (`period_id`),
  KEY `fk_sd_department` (`department_id`),
  KEY `fk_sd_class` (`class_id`),
  CONSTRAINT `fk_sd_class` FOREIGN KEY (`class_id`) REFERENCES `report_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sd_department` FOREIGN KEY (`department_id`) REFERENCES `report_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sd_period` FOREIGN KEY (`period_id`) REFERENCES `report_academic_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sd_student` FOREIGN KEY (`student_id`) REFERENCES `report_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_students`
--

DROP TABLE IF EXISTS `report_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `student_name` varchar(150) NOT NULL,
  `religion` enum('CRS','IRS') DEFAULT NULL,
  `passport_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`class_id`),
  KEY `idx_school` (`school_id`),
  CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `report_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_students_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_subjects`
--

DROP TABLE IF EXISTS `report_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `base_subject_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `is_custom` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_base_subject` (`base_subject_id`),
  CONSTRAINT `fk_rs_base_subject` FOREIGN KEY (`base_subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schools` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `premium_until` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token_balance` int(11) NOT NULL DEFAULT 0,
  `last_free_token_date` date DEFAULT NULL,
  `daily_free_quota` int(11) NOT NULL DEFAULT 3,
  `daily_free_used` int(11) NOT NULL DEFAULT 0,
  `daily_free_last_reset` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `class_arm` enum('jss','sss') NOT NULL DEFAULT 'sss',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_arm_name` (`class_arm`,`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `token_usage_audit`
--

DROP TABLE IF EXISTS `token_usage_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `token_usage_audit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `token_type` enum('free','paid') NOT NULL,
  `tokens_used` int(10) unsigned NOT NULL DEFAULT 1,
  `action` varchar(50) NOT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  KEY `school_id` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `paid_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `reference` varchar(255) NOT NULL,
  `gateway` enum('paystack') NOT NULL DEFAULT 'paystack',
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'NGN',
  `tokens_purchased` int(11) NOT NULL,
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `raw_payload` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_reference` (`reference`),
  KEY `idx_status` (`status`),
  KEY `school_id` (`school_id`) USING BTREE,
  CONSTRAINT `fk_transactions_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `username` varchar(150) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('creator','admin','staff') NOT NULL DEFAULT 'staff',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `can_purchase_tokens` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_school_username` (`school_id`,`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `created_by` (`created_by`),
  KEY `school_id` (`school_id`) USING BTREE,
  CONSTRAINT `fk_users_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-06-01  3:40:21
