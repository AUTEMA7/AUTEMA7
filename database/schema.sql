-- ================================================================
-- IlaraNet Bénin - Base de données MySQL
-- Plateforme Numérique de Gestion des Centres de Santé
-- République du Bénin — 2026
-- Conformité ANIP & OHADA
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00"; -- WAT (West Africa Time)

-- ================================================================
-- BASE DE DONNÉES
-- ================================================================

CREATE DATABASE IF NOT EXISTS `ilaranet_benin` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `ilaranet_benin`;

-- ================================================================
-- TABLES SYSTÈME
-- ================================================================

-- Table: Établissements de santé (Multi-tenant)
CREATE TABLE `ilara_facilities` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) UNIQUE NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` ENUM('hopital', 'clinique_privee', 'cabinet_medical', 'centre_de_sante', 'polyclinique') NOT NULL,
  `rccm` VARCHAR(50),
  `authorization_number` VARCHAR(50), -- Autorisation sanitaire
  `address` TEXT,
  `city` VARCHAR(100),
  `phone` VARCHAR(20),
  `email` VARCHAR(255),
  `logo_path` VARCHAR(255),
  `is_active` BOOLEAN DEFAULT FALSE,
  `is_validated` BOOLEAN DEFAULT FALSE, -- Validé par Super Admin
  `subscription_plan` ENUM('starter', 'clinique', 'hopital') DEFAULT 'starter',
  `subscription_expires_at` DATETIME,
  `settings` JSON, -- Configuration spécifique
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type (`type`),
  INDEX idx_active (`is_active`),
  INDEX idx_validated (`is_validated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Super Admins (Plateforme)
CREATE TABLE `ilara_super_admins` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) UNIQUE NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `is_active` BOOLEAN DEFAULT TRUE,
  `last_login_at` DATETIME,
  `last_login_ip` VARCHAR(45),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Services par établissement
CREATE TABLE `ilara_services` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `uuid` CHAR(36) UNIQUE NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `access_code` CHAR(6) NOT NULL, -- Code 6 chiffres
  `code_expires_at` DATETIME, -- Renouvellement tous les 90 jours
  `is_active` BOOLEAN DEFAULT TRUE,
  `config` JSON, -- Horaires, capacité, équipements
  `chef_service_id` BIGINT UNSIGNED, -- Référence utilisateur
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  INDEX idx_facility (`facility_id`),
  INDEX idx_active (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- UTILISATEURS ET AUTHENTIFICATION
-- ================================================================

-- Table: Utilisateurs (Personnel + Patients)
CREATE TABLE `ilara_users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `uuid` CHAR(36) UNIQUE NOT NULL,
  `user_type` ENUM('personnel', 'patient') NOT NULL,
  
  -- Champs communs
  `npi` VARCHAR(50), -- NPI ANIP (obligatoire pour patients)
  `is_temporary_npi` BOOLEAN DEFAULT FALSE, -- NPI temporaire pour inconnus
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255),
  `phone` VARCHAR(20),
  `photo_path` VARCHAR(255),
  `access_code` CHAR(8), -- Code patient (6-8) ou code directeur (8)
  `access_code_expires_at` DATETIME,
  
  -- Champs personnel
  `role` ENUM('super_admin', 'directeur', 'chef_service', 'medecin', 'infirmier', 'sage_femme', 'biologiste', 'radiologue', 'pharmacien', 'comptable', 'accueil'),
  `specialty` VARCHAR(100),
  `professional_number` VARCHAR(50), -- Numéro ordre professionnel
  `signature_path` VARCHAR(255), -- Signature électronique
  `password_hash` VARCHAR(255), -- Pour personnel uniquement
  `is_active` BOOLEAN DEFAULT TRUE,
  
  -- Champs patients
  `gender` ENUM('M', 'F'),
  `date_of_birth` DATE,
  `age_approximate` TINYINT, -- Pour patients inconnus
  `physical_description` TEXT, -- Pour patients inconnus
  `blood_type` VARCHAR(5),
  `allergies` JSON,
  `medical_history` JSON,
  `emergency_contacts` JSON,
  `treating_physician_id` BIGINT UNSIGNED, -- Médecin traitant désigné
  
  -- Sécurité
  `two_factor_secret` VARCHAR(255), -- Secret TOTP
  `two_factor_enabled` BOOLEAN DEFAULT FALSE,
  `last_login_at` DATETIME,
  `last_login_ip` VARCHAR(45),
  `failed_login_attempts` TINYINT DEFAULT 0,
  `locked_until` DATETIME,
  
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`treating_physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  INDEX idx_npi (`npi`),
  INDEX idx_facility (`facility_id`),
  INDEX idx_user_type (`user_type`),
  INDEX idx_role (`role`),
  INDEX idx_active (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Assignation Personnel aux Services
CREATE TABLE `ilara_service_staff` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `is_primary` BOOLEAN DEFAULT FALSE, -- Service principal
  `assigned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` BIGINT UNSIGNED,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_by`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  UNIQUE KEY unique_service_staff (`service_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Sessions utilisateurs
CREATE TABLE `ilara_sessions` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED, -- Session liée à un service
  `session_token` VARCHAR(255) UNIQUE NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE CASCADE,
  INDEX idx_token (`session_token`),
  INDEX idx_expires (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: OTP (2FA)
CREATE TABLE `ilara_otps` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `otp_code` CHAR(6) NOT NULL,
  `purpose` ENUM('login', 'password_reset', 'code_validation') NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `is_used` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_otp (`otp_code`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- DOSSIERS MÉDICAUX
-- ================================================================

-- Table: Dossiers patients (par facility)
CREATE TABLE `ilara_patient_files` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_id` BIGINT UNSIGNED NOT NULL,
  `internal_file_number` VARCHAR(50) UNIQUE, -- Numéro dossier interne
  `qr_code_data` TEXT, -- Données QR code urgence
  `is_active` BOOLEAN DEFAULT TRUE,
  `merged_with_file_id` BIGINT UNSIGNED, -- Fusion avec vrai NPI
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`merged_with_file_id`) REFERENCES `ilara_patient_files`(`id`) ON DELETE SET NULL,
  INDEX idx_patient (`patient_id`),
  INDEX idx_facility (`facility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Consultations
CREATE TABLE `ilara_consultations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_file_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED,
  `physician_id` BIGINT UNSIGNED NOT NULL,
  `consultation_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `reason` TEXT, -- Motif de consultation
  `vitals` JSON, -- Température, tension, SpO2, poids, glycémie...
  `clinical_notes` TEXT, -- Notes cliniques privées structurées
  `diagnosis_codes` JSON, -- Codes CIM-11
  `diagnosis_text` TEXT, -- Diagnostic texte
  `prescription` JSON, -- Ordonnance
  `certificates` JSON, -- Certificats générés
  `voice_transcription` BOOLEAN DEFAULT FALSE, -- Dictée vocale
  `ai_assisted` BOOLEAN DEFAULT FALSE, -- Assistant IA utilisé
  `status` ENUM('ongoing', 'completed', 'cancelled') DEFAULT 'ongoing',
  `duration_minutes` INT,
  `follow_up_date` DATETIME,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_file_id`) REFERENCES `ilara_patient_files`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_patient (`patient_file_id`),
  INDEX idx_physician (`physician_id`),
  INDEX idx_date (`consultation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Rendez-vous
CREATE TABLE `ilara_appointments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_id` BIGINT UNSIGNED NOT NULL,
  `physician_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED,
  `appointment_date` DATETIME NOT NULL,
  `duration_minutes` INT DEFAULT 30,
  `status` ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
  `reminder_sent` BOOLEAN DEFAULT FALSE,
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE SET NULL,
  INDEX idx_date (`appointment_date`),
  INDEX idx_status (`status`),
  INDEX idx_patient (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- LABORATOIRE & IMAGERIE
-- ================================================================

-- Table: Demandes d'analyses
CREATE TABLE `ilara_lab_requests` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_file_id` BIGINT UNSIGNED NOT NULL,
  `requesting_physician_id` BIGINT UNSIGNED NOT NULL,
  `biologist_id` BIGINT UNSIGNED,
  `service_id` BIGINT UNSIGNED,
  `tests_requested` JSON NOT NULL, -- Liste des analyses demandées
  `priority` ENUM('routine', 'urgent', 'stat') DEFAULT 'routine',
  `sample_collected_at` DATETIME,
  `results` JSON, -- Résultats saisis
  `is_critical` BOOLEAN DEFAULT FALSE, -- Résultat critique
  `status` ENUM('pending', 'in_progress', 'completed', 'validated') DEFAULT 'pending',
  `validated_at` DATETIME,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_file_id`) REFERENCES `ilara_patient_files`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`requesting_physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`biologist_id`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  INDEX idx_status (`status`),
  INDEX idx_patient (`patient_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Demandes d'imagerie
CREATE TABLE `ilara_imaging_requests` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_file_id` BIGINT UNSIGNED NOT NULL,
  `requesting_physician_id` BIGINT UNSIGNED NOT NULL,
  `radiologist_id` BIGINT UNSIGNED,
  `service_id` BIGINT UNSIGNED,
  `modality` ENUM('radio', 'echographie', 'scanner', 'irm', 'autre') NOT NULL,
  `body_part` VARCHAR(100),
  `clinical_indication` TEXT,
  `images_path` JSON, -- Chemins vers les fichiers DICOM/images
  `report` TEXT, -- Rapport radiologique
  `status` ENUM('pending', 'in_progress', 'completed', 'validated') DEFAULT 'pending',
  `validated_at` DATETIME,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_file_id`) REFERENCES `ilara_patient_files`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`requesting_physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`radiologist_id`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  INDEX idx_status (`status`),
  INDEX idx_patient (`patient_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- HOSPITALISATION & URGENCES
-- ================================================================

-- Table: Lits et chambres
CREATE TABLE `ilara_beds` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `room_number` VARCHAR(20),
  `bed_number` VARCHAR(20),
  `bed_type` ENUM('standard', 'reanimation', 'soins_intensifs', 'pediatrie', 'maternite') DEFAULT 'standard',
  `is_available` BOOLEAN DEFAULT TRUE,
  `status` ENUM('available', 'occupied', 'cleaning', 'maintenance') DEFAULT 'available',
  `equipment` JSON, -- Équipements disponibles
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE CASCADE,
  INDEX idx_facility (`facility_id`),
  INDEX idx_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Hospitalisations
CREATE TABLE `ilara_hospitalizations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_file_id` BIGINT UNSIGNED NOT NULL,
  `admission_bed_id` BIGINT UNSIGNED NOT NULL,
  `admitting_physician_id` BIGINT UNSIGNED NOT NULL,
  `admission_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `admission_reason` TEXT,
  `admission_type` ENUM('programmee', 'urgence', 'transfert') DEFAULT 'programmee',
  `esi_level` TINYINT, -- Niveau ESI si urgence
  `current_bed_id` BIGINT UNSIGNED,
  `discharge_date` DATETIME,
  `discharge_reason` TEXT,
  `discharge_condition` ENUM('gueri', 'améliore', 'stable', 'transfere', 'deces'),
  `discharge_summary` TEXT,
  `checkout_checklist` JSON, -- Checklist pré-sortie
  `total_cost` DECIMAL(15,2) DEFAULT 0.00,
  `advance_paid` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('admitted', 'transferred', 'discharged', 'deceased') DEFAULT 'admitted',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_file_id`) REFERENCES `ilara_patient_files`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`admission_bed_id`) REFERENCES `ilara_beds`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`current_bed_id`) REFERENCES `ilara_beds`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`admitting_physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_status (`status`),
  INDEX idx_patient (`patient_file_id`),
  INDEX idx_admission (`admission_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Surveillance hospitalière (constantes journalières)
CREATE TABLE `ilara_patient_monitoring` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `hospitalization_id` BIGINT UNSIGNED NOT NULL,
  `recorded_by` BIGINT UNSIGNED NOT NULL,
  `recorded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `vitals` JSON NOT NULL, -- Constantes vitales
  `treatments_administered` JSON, -- Traitements donnés
  `observations` TEXT,
  `pain_level` TINYINT, -- Échelle douleur 0-10
  FOREIGN KEY (`hospitalization_id`) REFERENCES `ilara_hospitalizations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_hospitalization (`hospitalization_id`),
  INDEX idx_recorded (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Urgences (triage)
CREATE TABLE `ilara_emergency_cases` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_file_id` BIGINT UNSIGNED NOT NULL,
  `triage_nurse_id` BIGINT UNSIGNED NOT NULL,
  `arrival_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `esi_level` TINYINT NOT NULL, -- 1-5
  `esi_color` ENUM('red', 'orange', 'yellow', 'green', 'blue') NOT NULL,
  `chief_complaint` TEXT,
  `initial_vitals` JSON,
  `assigned_box` VARCHAR(20),
  `assigned_physician_id` BIGINT UNSIGNED,
  `code_blue_triggered` BOOLEAN DEFAULT FALSE,
  `code_blue_time` DATETIME,
  `transfer_requested` BOOLEAN DEFAULT FALSE,
  `transfer_destination_id` BIGINT UNSIGNED, -- Facility destination
  `disposition` ENUM('libere', 'hospitalise', 'transfere', 'deces', 'part contre avis'),
  `disposition_time` DATETIME,
  `status` ENUM('waiting', 'in_care', 'hospitalized', 'transferred', 'discharged', 'deceased') DEFAULT 'waiting',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_file_id`) REFERENCES `ilara_patient_files`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`triage_nurse_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`transfer_destination_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE SET NULL,
  INDEX idx_status (`status`),
  INDEX idx_esi (`esi_level`),
  INDEX idx_arrival (`arrival_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- PHARMACIE & STOCKS
-- ================================================================

-- Table: Médicaments (base VIDAL Afrique)
CREATE TABLE `ilara_medications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED, -- NULL = base globale
  `name` VARCHAR(255) NOT NULL,
  `generic_name` VARCHAR(255), -- DCI
  `form` VARCHAR(100), -- Comprimé, sirop, injectable...
  `strength` VARCHAR(50), -- Dosage
  `manufacturer` VARCHAR(255),
  `atc_code` VARCHAR(20), -- Classification ATC
  `requires_prescription` BOOLEAN DEFAULT TRUE,
  `is_stupefiant` BOOLEAN DEFAULT FALSE,
  `is_psychotrope` BOOLEAN DEFAULT FALSE,
  `interactions` JSON, -- Interactions médicamenteuses
  `contraindications` JSON,
  `side_effects` JSON,
  `dosage_guidelines` JSON,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_name (`name`),
  INDEX idx_generic (`generic_name`),
  INDEX idx_active (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Stocks pharmacie
CREATE TABLE `ilara_stock` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED,
  `medication_id` BIGINT UNSIGNED NOT NULL,
  `batch_number` VARCHAR(50),
  `quantity` INT NOT NULL DEFAULT 0,
  `unit_price` DECIMAL(15,2) NOT NULL,
  `selling_price` DECIMAL(15,2) NOT NULL,
  `expiry_date` DATE NOT NULL,
  `min_stock_alert` INT DEFAULT 10,
  `location` VARCHAR(100), -- Emplacement dans la pharmacie
  `last_inventory_at` DATETIME,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`medication_id`) REFERENCES `ilara_medications`(`id`) ON DELETE CASCADE,
  INDEX idx_facility (`facility_id`),
  INDEX idx_expiry (`expiry_date`),
  INDEX idx_quantity (`quantity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Mouvements de stock
CREATE TABLE `ilara_stock_movements` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `stock_id` BIGINT UNSIGNED NOT NULL,
  `movement_type` ENUM('entry', 'exit', 'adjustment', 'expired', 'returned') NOT NULL,
  `quantity` INT NOT NULL,
  `reference_type` VARCHAR(50), -- consultation, hospitalization, purchase...
  `reference_id` BIGINT UNSIGNED,
  `performed_by` BIGINT UNSIGNED NOT NULL,
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`stock_id`) REFERENCES `ilara_stock`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`performed_by`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_stock (`stock_id`),
  INDEX idx_type (`movement_type`),
  INDEX idx_date (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Pharmacies externes (réseau national)
CREATE TABLE `ilara_pharmacies` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) UNIQUE NOT NULL,
  `onpb_number` VARCHAR(50) UNIQUE NOT NULL, -- Ordre National Pharmaciens Bénin
  `name` VARCHAR(255) NOT NULL,
  `address` TEXT,
  `city` VARCHAR(100),
  `latitude` DECIMAL(10,8),
  `longitude` DECIMAL(11,8),
  `phone` VARCHAR(20),
  `email` VARCHAR(255),
  `is_on_duty` BOOLEAN DEFAULT FALSE, -- Pharmacie de garde
  `opening_hours` JSON,
  `is_partner` BOOLEAN DEFAULT FALSE,
  `api_token` VARCHAR(255) UNIQUE, -- Token API pour interconnexion
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_city (`city`),
  INDEX idx_duty (`is_on_duty`),
  INDEX idx_active (`is_active`),
  SPATIAL INDEX idx_location (`latitude`, `longitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Ordonnances électroniques
CREATE TABLE `ilara_prescriptions` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `consultation_id` BIGINT UNSIGNED,
  `patient_id` BIGINT UNSIGNED NOT NULL,
  `physician_id` BIGINT UNSIGNED NOT NULL,
  `prescription_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `items` JSON NOT NULL, -- Médicaments prescrits
  `is_dispensed` BOOLEAN DEFAULT FALSE,
  `dispensed_at` DATETIME,
  `dispensing_pharmacy_id` BIGINT UNSIGNED,
  `token` CHAR(64) UNIQUE, -- Token unique pour validation
  `qr_code` TEXT,
  `pdf_path` VARCHAR(255),
  `is_signed` BOOLEAN DEFAULT FALSE,
  `signature_timestamp` DATETIME,
  `validity_days` INT DEFAULT 7,
  `expires_at` DATETIME,
  `status` ENUM('active', 'dispensed', 'expired', 'cancelled') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`consultation_id`) REFERENCES `ilara_consultations`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`patient_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dispensing_pharmacy_id`) REFERENCES `ilara_pharmacies`(`id`) ON DELETE SET NULL,
  INDEX idx_patient (`patient_id`),
  INDEX idx_token (`token`),
  INDEX idx_status (`status`),
  INDEX idx_date (`prescription_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- FACTURATION & COMPTABILITÉ (OHADA)
-- ================================================================

-- Table: Grille tarifaire
CREATE TABLE `ilara_pricing` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED,
  `act_code` VARCHAR(50) NOT NULL,
  `act_name` VARCHAR(255) NOT NULL,
  `act_category` VARCHAR(100), -- consultation, acte, chambre, medicament...
  `base_price` DECIMAL(15,2) NOT NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE SET NULL,
  UNIQUE KEY unique_pricing (`facility_id`, `act_code`),
  INDEX idx_facility (`facility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Factures
CREATE TABLE `ilara_invoices` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_file_id` BIGINT UNSIGNED NOT NULL,
  `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
  `invoice_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `due_date` DATETIME,
  `items` JSON NOT NULL, -- Détail des prestations
  `subtotal` DECIMAL(15,2) NOT NULL,
  `discount` DECIMAL(15,2) DEFAULT 0.00,
  `tax` DECIMAL(15,2) DEFAULT 0.00,
  `total` DECIMAL(15,2) NOT NULL,
  `amount_paid` DECIMAL(15,2) DEFAULT 0.00,
  `balance` DECIMAL(15,2) NOT NULL,
  `payment_method` ENUM('especes', 'c_cash', 'mtn_momo', 'flooz', 'cheque', 'virement', 'assurance'),
  `insurance_info` JSON, -- Informations assurance
  `is_tiers_payant` BOOLEAN DEFAULT FALSE,
  `status` ENUM('draft', 'issued', 'paid', 'partial', 'overdue', 'cancelled') DEFAULT 'draft',
  `pdf_path` VARCHAR(255),
  `notes` TEXT,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_file_id`) REFERENCES `ilara_patient_files`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_patient (`patient_file_id`),
  INDEX idx_status (`status`),
  INDEX idx_date (`invoice_date`),
  INDEX idx_balance (`balance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Paiements
CREATE TABLE `ilara_payments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `invoice_id` BIGINT UNSIGNED NOT NULL,
  `payment_number` VARCHAR(50) UNIQUE NOT NULL,
  `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `amount` DECIMAL(15,2) NOT NULL,
  `payment_method` ENUM('especes', 'c_cash', 'mtn_momo', 'flooz', 'cheque', 'virement', 'assurance') NOT NULL,
  `transaction_reference` VARCHAR(255), -- Référence transaction mobile money
  `received_by` BIGINT UNSIGNED NOT NULL,
  `notes` TEXT,
  `receipt_pdf_path` VARCHAR(255),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`invoice_id`) REFERENCES `ilara_invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`received_by`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_invoice (`invoice_id`),
  INDEX idx_date (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Clôture de caisse
CREATE TABLE `ilara_cash_closing` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED,
  `cashier_id` BIGINT UNSIGNED NOT NULL,
  `closing_date` DATE NOT NULL,
  `opening_balance` DECIMAL(15,2) DEFAULT 0.00,
  `total_cash_in` DECIMAL(15,2) DEFAULT 0.00,
  `total_cash_out` DECIMAL(15,2) DEFAULT 0.00,
  `expected_balance` DECIMAL(15,2) NOT NULL,
  `actual_balance` DECIMAL(15,2) NOT NULL,
  `discrepancy` DECIMAL(15,2) DEFAULT 0.00,
  `discrepancy_reason` TEXT,
  `is_validated` BOOLEAN DEFAULT FALSE,
  `validated_by` BIGINT UNSIGNED,
  `validated_at` DATETIME,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`cashier_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`validated_by`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  UNIQUE KEY unique_closing (`facility_id`, `service_id`, `closing_date`, `cashier_id`),
  INDEX idx_date (`closing_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Comptabilité OHADA (plan comptable)
CREATE TABLE `ilara_accounting_entries` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `entry_date` DATE NOT NULL,
  `account_number` VARCHAR(20) NOT NULL, -- Plan comptable OHADA
  `account_name` VARCHAR(255) NOT NULL,
  `debit` DECIMAL(15,2) DEFAULT 0.00,
  `credit` DECIMAL(15,2) DEFAULT 0.00,
  `reference_type` VARCHAR(50),
  `reference_id` BIGINT UNSIGNED,
  `description` TEXT,
  `posted_by` BIGINT UNSIGNED NOT NULL,
  `is_posted` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`posted_by`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_account (`account_number`),
  INDEX idx_date (`entry_date`),
  INDEX idx_facility (`facility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- RESSOURCES HUMAINES
-- ================================================================

-- Table: Contrats de travail
CREATE TABLE `ilara_contracts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `contract_type` ENUM('cdi', 'cdd', 'stage', 'interim') NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE,
  `salary_base` DECIMAL(15,2) NOT NULL,
  `cnss_number` VARCHAR(50),
  `position` VARCHAR(100),
  `department` VARCHAR(100),
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_employee (`employee_id`),
  INDEX idx_active (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Planning et présences
CREATE TABLE `ilara_schedules` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `shift_date` DATE NOT NULL,
  `shift_start` TIME,
  `shift_end` TIME,
  `shift_type` ENUM('matin', 'apres_midi', 'nuit', 'garde') DEFAULT 'matin',
  `is_actual` BOOLEAN DEFAULT FALSE, -- Planning réel vs prévu
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY unique_schedule (`facility_id`, `service_id`, `user_id`, `shift_date`),
  INDEX idx_date (`shift_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Congés et absences
CREATE TABLE `ilara_leaves` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `leave_type` ENUM('conges_payes', 'maladie', 'maternite', 'paternite', 'sans_solde', 'autre') NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `approved_by` BIGINT UNSIGNED,
  `approved_at` DATETIME,
  `reason` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  INDEX idx_employee (`employee_id`),
  INDEX idx_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- AUDIT TRAIL & SÉCURITÉ
-- ================================================================

-- Table: Audit Trail (immuable)
CREATE TABLE `ilara_audit_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED,
  `service_id` BIGINT UNSIGNED,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50), -- patient, consultation, invoice...
  `entity_id` BIGINT UNSIGNED,
  `old_values` JSON, -- Valeurs avant modification
  `new_values` JSON, -- Valeurs après modification
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `wat_timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP, -- West Africa Time
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_user (`user_id`),
  INDEX idx_action (`action`),
  INDEX idx_timestamp (`wat_timestamp`),
  INDEX idx_entity (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Alertes et notifications
CREATE TABLE `ilara_notifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `recipient_id` BIGINT UNSIGNED NOT NULL,
  `notification_type` ENUM('sms', 'push', 'email', 'in_app') NOT NULL,
  `category` VARCHAR(50), -- rdv_reminder, result_ready, stock_alert...
  `title` VARCHAR(255),
  `message` TEXT NOT NULL,
  `data` JSON, -- Données additionnelles
  `is_read` BOOLEAN DEFAULT FALSE,
  `sent_at` DATETIME,
  `delivered_at` DATETIME,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipient_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  INDEX idx_recipient (`recipient_id`),
  INDEX idx_read (`is_read`),
  INDEX idx_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Alertes critiques (Code Bleu, stock bas...)
CREATE TABLE `ilara_critical_alerts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED,
  `alert_type` ENUM('code_blue', 'stock_low', 'stock_expired', 'cash_discrepancy', 'critical_result', 'mdo_declaration') NOT NULL,
  `severity` ENUM('low', 'medium', 'high', 'critical') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `context` JSON, -- Contexte de l'alerte
  `acknowledged_by` BIGINT UNSIGNED,
  `acknowledged_at` DATETIME,
  `resolved_at` DATETIME,
  `is_resolved` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `ilara_services`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`acknowledged_by`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  INDEX idx_severity (`severity`),
  INDEX idx_resolved (`is_resolved`),
  INDEX idx_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TÉLÉCONSULTATION
-- ================================================================

-- Table: Téléconsultations
CREATE TABLE `ilara_teleconsultations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `patient_id` BIGINT UNSIGNED NOT NULL,
  `physician_id` BIGINT UNSIGNED NOT NULL,
  `scheduled_at` DATETIME NOT NULL,
  `started_at` DATETIME,
  `ended_at` DATETIME,
  `duration_minutes` INT,
  `video_session_id` VARCHAR(255), -- WebRTC session ID
  `consultation_notes` TEXT,
  `prescription_id` BIGINT UNSIGNED,
  `invoice_id` BIGINT UNSIGNED,
  `status` ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`physician_id`) REFERENCES `ilara_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`prescription_id`) REFERENCES `ilara_prescriptions`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`invoice_id`) REFERENCES `ilara_invoices`(`id`) ON DELETE SET NULL,
  INDEX idx_status (`status`),
  INDEX idx_scheduled (`scheduled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- EXPORT & RAPPORTS
-- ================================================================

-- Table: Exports DHIS2/SNIGS
CREATE TABLE `ilara_dhis2_exports` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `facility_id` BIGINT UNSIGNED NOT NULL,
  `export_period_start` DATE NOT NULL,
  `export_period_end` DATE NOT NULL,
  `data_elements` JSON NOT NULL,
  `export_status` ENUM('pending', 'success', 'failed') DEFAULT 'pending',
  `dhis2_response` JSON,
  `exported_at` DATETIME,
  `exported_by` BIGINT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`facility_id`) REFERENCES `ilara_facilities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`exported_by`) REFERENCES `ilara_users`(`id`) ON DELETE SET NULL,
  INDEX idx_period (`export_period_start`, `export_period_end`),
  INDEX idx_status (`export_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- DONNÉES DE DÉMONSTRATION
-- ================================================================

-- Super Admin par défaut
INSERT INTO `ilara_super_admins` (`uuid`, `full_name`, `email`, `password_hash`, `phone`) VALUES
(UUID(), 'Administrateur Système', 'admin@ilaranet.bj', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYzS3MebAJu', '+22900000000');
-- Mot de passe par défaut: admin123 (À CHANGER IMPÉRATIVEMENT EN PRODUCTION)

COMMIT;
