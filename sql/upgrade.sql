

USE clinicdesk_db;

ALTER TABLE users
    ADD COLUMN first_login TINYINT(1) NOT NULL DEFAULT 1 AFTER is_active;

UPDATE users SET first_login = 0;

ALTER TABLE appointments
    ADD COLUMN cancelled_by INT UNSIGNED DEFAULT NULL AFTER doctor_notes,
    ADD COLUMN cancellation_reason VARCHAR(500) DEFAULT NULL AFTER cancelled_by;

CREATE TABLE IF NOT EXISTS appointment_logs (
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
