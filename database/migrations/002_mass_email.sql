-- Migration: mass-email feature (ported from send_multi_email_to_recruters)
-- Run this against your existing database after pulling this branch.

-- 1. Profile / sending identity fields on users
ALTER TABLE users
    ADD COLUMN sender_name        VARCHAR(255) NULL AFTER email,
    ADD COLUMN sender_email       VARCHAR(255) NULL AFTER sender_name,
    ADD COLUMN gmail_app_password TEXT         NULL AFTER sender_email;
    -- gmail_app_password is stored ENCRYPTED (see app/Helpers/CryptoHelper.php),
    -- never in plain text.

-- 2. Reusable email signatures (one user can have several)
CREATE TABLE IF NOT EXISTS signatures (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    name           VARCHAR(255) NOT NULL,
    title          VARCHAR(255) NULL,
    email          VARCHAR(255) NULL,
    phone          VARCHAR(100) NULL,
    linkedin       VARCHAR(255) NULL,
    github         VARCHAR(255) NULL,
    portfolio      VARCHAR(255) NULL,
    custom_text    TEXT NULL,
    image_shape    VARCHAR(20) DEFAULT 'circle',
    image_size     INT DEFAULT 80,
    layout         VARCHAR(20) DEFAULT 'horizontal',
    accent_color   VARCHAR(20) DEFAULT '#3b82f6',
    show_icons     TINYINT(1) DEFAULT 1,
    font_family    VARCHAR(255) DEFAULT 'Arial, Helvetica, sans-serif',
    links_columns  INT DEFAULT 1,
    image_base64   LONGTEXT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Mass-send campaigns (one Excel upload = one campaign)
CREATE TABLE IF NOT EXISTS campaigns (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    cv_upload_id   INT NULL,
    signature_id   INT NULL,
    subject        VARCHAR(500) NULL,
    message        TEXT NULL,
    status         ENUM('pending','sending','finished') DEFAULT 'pending',
    total          INT DEFAULT 0,
    sent_count     INT DEFAULT 0,
    failed_count   INT DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cv_upload_id) REFERENCES cv_uploads(id) ON DELETE SET NULL,
    FOREIGN KEY (signature_id) REFERENCES signatures(id) ON DELETE SET NULL
);

-- 4. Recipients per campaign (rows loaded from the Excel file)
CREATE TABLE IF NOT EXISTS campaign_recipients (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id    INT NOT NULL,
    email          VARCHAR(255) NOT NULL,
    post           TEXT NULL,
    body           LONGTEXT NULL,
    status         ENUM('pending','sent','failed') DEFAULT 'pending',
    error          VARCHAR(500) NULL,
    sent_at        TIMESTAMP NULL,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);
