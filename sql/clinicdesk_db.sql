

CREATE DATABASE IF NOT EXISTS clinicdesk_db
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE clinicdesk_db;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM("admin","doctor","patient") NOT NULL DEFAULT "patient",
    phone VARCHAR(20) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    first_login TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE specializations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE doctors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    specialization_id INT UNSIGNED NOT NULL,
    bio TEXT DEFAULT NULL,
    consultation_fee DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    available_days VARCHAR(50) NOT NULL DEFAULT "Sun,Mon,Tue,Wed,Thu",
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id INT UNSIGNED NOT NULL,
    doctor_id INT UNSIGNED NOT NULL,
    appt_date DATE NOT NULL,
    appt_time TIME NOT NULL,
    status ENUM("pending","confirmed","completed","cancelled") NOT NULL DEFAULT "pending",
    reason VARCHAR(255) DEFAULT NULL,
    doctor_notes TEXT DEFAULT NULL,
    cancelled_by INT UNSIGNED DEFAULT NULL,
    cancellation_reason VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY no_double_booking (doctor_id, appt_date, appt_time),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE appointment_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT UNSIGNED NOT NULL,
    changed_by_user_id INT UNSIGNED NOT NULL,
    old_status VARCHAR(20) NOT NULL,
    new_status VARCHAR(20) NOT NULL,
    note VARCHAR(500) DEFAULT NULL,
    changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email, password, role, first_login) VALUES
("Admin", "admin@clinic.local", "$2y$12$DOIQ2SQDJVdsj0PudvyjDOyGuAmUTp62DTrf5dH6OgAMaOfkUkOU.", "admin", 0);

INSERT INTO specializations (name) VALUES
("General Practice"),
("Cardiology"),
("Dermatology"),
("Pediatrics"),
("Orthopedics"),
("Neurology"),
("Ophthalmology"),
("ENT"),
("Psychiatry");

INSERT INTO users (name, email, password, role, phone, first_login) VALUES
("Dr. Sara Khalil", "sara@clinic.local", "$2y$12$Jj71/AHDELN4JolT8Rr3eOfJM5duKncYcJn6YP6Ma3ew1J3EFwlMe", "doctor", "0599123456", 0);

INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days) VALUES
(2, 2, "Cardiologist with 8 years of experience.", 75.00, "Sun,Mon,Tue,Wed,Thu");

INSERT INTO users (name, email, password, role, phone, first_login) VALUES
("Ahmed Salah", "ahmed@clinic.local", "$2y$12$5KZ.vc92Osp.5HZcomTb7O/49CtP3A1OoRm.AMkh4YFfeLVyjqk4C", "patient", "0599876543", 0);
