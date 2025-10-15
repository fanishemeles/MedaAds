CREATE DATABASE IF NOT EXISTS medaads CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medaads;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS placements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_placements_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    placement_id INT UNSIGNED NULL,
    title VARCHAR(160) NOT NULL,
    body TEXT NOT NULL,
    target_url VARCHAR(255) NULL,
    image_url VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    views INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ads_placement FOREIGN KEY (placement_id) REFERENCES placements(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (email, password_hash, role) VALUES
    ('admin@medaads.test', '$2y$12$PfBF5N3glJtZ58lvjcYKxOxq0qbCl3jpdCDggbC.7sDMtrzCMTayC', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email), role = VALUES(role);

INSERT INTO placements (name, description) VALUES
    ('Homepage Hero', 'Large banner displayed on the homepage hero area'),
    ('Sidebar Widget', 'Smaller ad shown in the sidebar'),
    ('Checkout Upsell', 'Placement shown on the checkout page')
ON DUPLICATE KEY UPDATE name = VALUES(name);
