CREATE DATABASE IF NOT EXISTS ilaranet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ilaranet;

CREATE TABLE IF NOT EXISTS tenants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    access_code VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NULL,
    full_name VARCHAR(120) NOT NULL,
    role ENUM('director','chief','staff') NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE IF NOT EXISTS otp_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    otp_code VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS patients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    npi VARCHAR(40) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    patient_code VARCHAR(8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_tenant_npi (tenant_id, npi),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE IF NOT EXISTS emergency_cases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    npi VARCHAR(40) NOT NULL,
    esi_level TINYINT UNSIGNED NOT NULL,
    priority_color VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE IF NOT EXISTS admissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    npi VARCHAR(40) NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    bed_number VARCHAR(20) NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE IF NOT EXISTS lab_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    npi VARCHAR(40) NOT NULL,
    test_name VARCHAR(120) NOT NULL,
    result_status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE IF NOT EXISTS pharmacy_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    npi VARCHAR(40) NOT NULL,
    medication VARCHAR(120) NOT NULL,
    pharmacy_name VARCHAR(120) NOT NULL,
    token VARCHAR(64) NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    channel ENUM('momo','flooz','ccash') NOT NULL,
    reference VARCHAR(40) NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    entity_type VARCHAR(60) NOT NULL,
    entity_id VARCHAR(60) NULL,
    metadata JSON NULL,
    event_time_wat DATETIME NOT NULL,
    hash_chain CHAR(64) GENERATED ALWAYS AS (SHA2(CONCAT(id, '|', action, '|', entity_type, '|', IFNULL(entity_id,''), '|', event_time_wat), 256)) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tenant_time (tenant_id, event_time_wat),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO tenants (id, name, type)
VALUES (1, 'IlaraNet Centre Démo', 'hopital')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO services (id, tenant_id, name, access_code)
VALUES
    (1, 1, 'Urgences', '$2y$12$qU/MUUvhZLdbIb42vFBHYe/OEuEONccwEU70ig08buc886AH70deq')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (tenant_id, service_id, full_name, role, email, phone, password_hash)
SELECT 1, NULL, 'Directeur Démo', 'director', 'directeur@ilaranet.bj', '+2290100000000', '$2y$12$82TjFJynWwqVysRYXUdOW.PoZfzaPJqTyOfEdAcr9EPReg0WzU3PO'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='directeur@ilaranet.bj');

INSERT INTO users (tenant_id, service_id, full_name, role, email, phone, password_hash)
SELECT 1, 1, 'Chef Urgences Démo', 'chief', 'chef@ilaranet.bj', '+2290100000001', '$2y$12$82TjFJynWwqVysRYXUdOW.PoZfzaPJqTyOfEdAcr9EPReg0WzU3PO'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='chef@ilaranet.bj');

INSERT INTO users (tenant_id, service_id, full_name, role, email, phone, password_hash)
SELECT 1, 1, 'Infirmier Démo', 'staff', 'staff@ilaranet.bj', '+2290100000002', '$2y$12$82TjFJynWwqVysRYXUdOW.PoZfzaPJqTyOfEdAcr9EPReg0WzU3PO'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='staff@ilaranet.bj');

-- password hash corresponds to: password
-- service access code hash corresponds to: 123456
