SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'cars' AND column_name = 'year') = 0,
    'ALTER TABLE cars ADD COLUMN `year` SMALLINT UNSIGNED NOT NULL DEFAULT 2020',
    'SELECT 1'
);
PREPARE migration FROM @ddl;
EXECUTE migration;
DEALLOCATE PREPARE migration;

SET @ddl = IF(
    (SELECT data_type FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'cars' AND column_name = 'price') <> 'decimal',
    'ALTER TABLE cars MODIFY COLUMN price DECIMAL(10,2) NOT NULL',
    'SELECT 1'
);
PREPARE migration FROM @ddl;
EXECUTE migration;
DEALLOCATE PREPARE migration;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'cars' AND column_name = 'transmission') = 0,
    'ALTER TABLE cars ADD COLUMN transmission VARCHAR(40) NOT NULL DEFAULT ''Manuaal''',
    'SELECT 1'
);
PREPARE migration FROM @ddl;
EXECUTE migration;
DEALLOCATE PREPARE migration;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'cars' AND column_name = 'seats') = 0,
    'ALTER TABLE cars ADD COLUMN seats TINYINT UNSIGNED NOT NULL DEFAULT 5',
    'SELECT 1'
);
PREPARE migration FROM @ddl;
EXECUTE migration;
DEALLOCATE PREPARE migration;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'cars' AND column_name = 'description') = 0,
    'ALTER TABLE cars ADD COLUMN description TEXT NOT NULL',
    'SELECT 1'
);
PREPARE migration FROM @ddl;
EXECUTE migration;
DEALLOCATE PREPARE migration;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'cars' AND column_name = 'status') = 0,
    'ALTER TABLE cars ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT ''vaba''',
    'SELECT 1'
);
PREPARE migration FROM @ddl;
EXECUTE migration;
DEALLOCATE PREPARE migration;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(40) NOT NULL DEFAULT '',
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reservations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY reservations_overlap_lookup (car_id, status, start_date, end_date),
    KEY reservations_user_lookup (user_id, created_at),
    CONSTRAINT reservations_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT reservations_car_fk FOREIGN KEY (car_id) REFERENCES cars (id) ON DELETE RESTRICT,
    CONSTRAINT reservations_dates_check CHECK (start_date <= end_date),
    CONSTRAINT reservations_price_check CHECK (total_price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
