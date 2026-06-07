-- =====================================================
-- ClinicDesk Database Schema
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS clinicdesk_db;
USE clinicdesk_db;

-- =====================================================
-- Table 1: users
-- =====================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','doctor','patient') NOT NULL DEFAULT 'patient',
    phone VARCHAR(20) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    first_login TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table 2: specializations
-- =====================================================
CREATE TABLE specializations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table 3: doctors
-- =====================================================
CREATE TABLE doctors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    specialization_id INT UNSIGNED NOT NULL,
    bio TEXT DEFAULT NULL,
    consultation_fee DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    available_days VARCHAR(50) NOT NULL DEFAULT 'Sun,Mon,Tue,Wed,Thu',
    photo VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table 4: appointments
-- =====================================================
CREATE TABLE appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id INT UNSIGNED NOT NULL,
    doctor_id INT UNSIGNED NOT NULL,
    appt_date DATE NOT NULL,
    appt_time TIME NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    reason VARCHAR(255) DEFAULT NULL,
    doctor_notes TEXT DEFAULT NULL,
    cancellation_reason TEXT DEFAULT NULL,
    cancelled_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY no_double_booking (doctor_id, appt_date, appt_time),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table 5: prescriptions
-- =====================================================
CREATE TABLE prescriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT UNSIGNED NOT NULL UNIQUE,
    diagnosis TEXT NOT NULL,
    medications TEXT NOT NULL,
    notes TEXT DEFAULT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table 6: appointment_logs (Advanced Feature)
-- =====================================================
CREATE TABLE appointment_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT UNSIGNED NOT NULL,
    changed_by_user_id INT UNSIGNED NOT NULL,
    old_status VARCHAR(20) DEFAULT NULL,
    new_status VARCHAR(20) NOT NULL,
    note TEXT DEFAULT NULL,
    changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table 7: pending_registrations (Advanced Feature)
-- =====================================================
CREATE TABLE pending_registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    reason TEXT DEFAULT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    rejection_reason TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Insert Specializations
-- =====================================================
INSERT INTO specializations (name) VALUES 
('General Practice'),
('Cardiology'),
('Dermatology'),
('Pediatrics'),
('Orthopedics'),
('Neurology'),
('Ophthalmology'),
('ENT'),
('Psychiatry'),
('Dentistry'),
('Gynecology'),
('Urology');

-- =====================================================
-- Insert Admin User
-- Password: Admin@1234
-- =====================================================
INSERT INTO users (name, email, password, role, is_active, first_login) VALUES 
('Admin', 'admin@clinic.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 0);

-- =====================================================
-- Insert Sample Doctor User
-- Password: Admin@1234
-- =====================================================
INSERT INTO users (name, email, password, role, phone, is_active, first_login) VALUES 
('Dr. Ahmed Hassan', 'ahmed@clinic.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '0501234567', 1, 0);

INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days, photo) VALUES 
(2, 1, 'Experienced general practitioner with over 10 years of experience in family medicine. Specialized in preventive care and chronic disease management.', 150.00, 'Sun,Mon,Tue,Wed,Thu', NULL);

-- =====================================================
-- Insert Second Sample Doctor (Cardiology)
-- Password: Admin@1234
-- =====================================================
INSERT INTO users (name, email, password, role, phone, is_active, first_login) VALUES 
('Dr. Samira Khalil', 'samira@clinic.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '0502345678', 1, 0);

INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days, photo) VALUES 
(3, 2, 'Cardiology specialist with expertise in heart conditions, hypertension, and preventive cardiovascular care.', 250.00, 'Sun,Mon,Tue,Wed', NULL);

-- =====================================================
-- Insert Sample Patient User
-- Password: Admin@1234
-- =====================================================
INSERT INTO users (name, email, password, role, phone, is_active, first_login) VALUES 
('Sara Ali', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '0509876543', 1, 1);

-- =====================================================
-- Insert Sample Patient 2
-- Password: Admin@1234
-- =====================================================
INSERT INTO users (name, email, password, role, phone, is_active, first_login) VALUES 
('Omar Mansour', 'omar@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '0508765432', 1, 1);

-- =====================================================
-- Insert Sample Appointments
-- =====================================================

-- Appointment 1: Tomorrow with Dr. Ahmed
INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason) VALUES 
(4, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'pending', 'Annual checkup and general health consultation. Feeling tired lately.');

-- Appointment 2: Day after tomorrow with Dr. Ahmed
INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason) VALUES 
(5, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:30:00', 'confirmed', 'Follow-up for blood pressure medication');

-- Appointment 3: Today with Dr. Samira (Completed)
INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason) VALUES 
(4, 2, CURDATE(), '11:00:00', 'completed', 'Chest pain and shortness of breath');

-- Appointment 4: Yesterday (Cancelled)
INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason, cancellation_reason, cancelled_at) VALUES 
(5, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:30:00', 'cancelled', 'Routine checkup', 'Patient requested cancellation', NOW());

-- Appointment 5: Next week with Dr. Ahmed (Confirmed)
INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason) VALUES 
(4, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '13:00:00', 'confirmed', 'Vaccination and travel health advice');

-- =====================================================
-- Insert Sample Prescription for Completed Appointment
-- =====================================================
INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path) VALUES 
(3, 'Mild hypertension and anxiety symptoms', 
 '1. Lisinopril 10mg - once daily\n2. Propranolol 40mg - twice daily as needed', 
 'Follow up in 2 weeks. Monitor blood pressure daily. Avoid caffeine and stress.', 
 NULL);

-- =====================================================
-- Insert Appointment Logs (Example)
-- =====================================================
INSERT INTO appointment_logs (appointment_id, changed_by_user_id, old_status, new_status, note) VALUES 
(2, 1, 'pending', 'confirmed', 'Admin confirmed the appointment'),
(4, 1, 'pending', 'cancelled', 'Patient requested cancellation');

-- =====================================================
-- Display Summary
-- =====================================================
SELECT 'Database setup completed!' AS message;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_doctors FROM doctors;
SELECT COUNT(*) AS total_appointments FROM appointments;
SELECT COUNT(*) AS total_prescriptions FROM prescriptions;