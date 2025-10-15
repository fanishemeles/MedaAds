CREATE DATABASE medaads;
USE medaads;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    advertiser_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    target_url VARCHAR(255) NOT NULL,
    status ENUM('draft', 'active', 'paused') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ads_advertiser (advertiser_id),
    CONSTRAINT fk_ads_advertiser FOREIGN KEY (advertiser_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE placements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ad_id INT UNSIGNED NOT NULL,
    publisher_id INT UNSIGNED NOT NULL,
    slot_key VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_placements_slot (slot_key, publisher_id),
    INDEX idx_placements_ad (ad_id),
    INDEX idx_placements_publisher (publisher_id),
    CONSTRAINT fk_placements_ad FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    CONSTRAINT fk_placements_publisher FOREIGN KEY (publisher_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
