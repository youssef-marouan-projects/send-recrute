-- ============================================================
-- send-recrute database schema
-- Users with roles (admin/user) + subscription plans (free/basic/pro)
-- limiting CV uploads and generated emails, with CV upload logging.
-- Run this once on a fresh database (or diff it against what
-- already exists) before deploying the updated app.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- plans: free / basic / pro
-- NULL in max_cv_uploads / max_emails means "unlimited"
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS plans (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(30)  NOT NULL UNIQUE,   -- free | basic | pro
    name            VARCHAR(50)  NOT NULL,
    max_cv_uploads  INT          NULL,               -- NULL = unlimited
    max_emails      INT          NULL,               -- NULL = unlimited
    price           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO plans (slug, name, max_cv_uploads, max_emails, price) VALUES
    ('free',  'Free',  3,   3,   0.00),
    ('basic', 'Basic', 25,  25,  9.99),
    ('pro',   'Pro',   NULL, NULL, 29.99)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    max_cv_uploads = VALUES(max_cv_uploads),
    max_emails = VALUES(max_emails),
    price = VALUES(price);

-- ------------------------------------------------------------
-- users: role defaults to 'user', plan defaults to Free (id 1)
-- cv_uploads_count / emails_generated_count are running totals
-- used for fast limit checks (kept in sync by the app).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id                      INT AUTO_INCREMENT PRIMARY KEY,
    name                    VARCHAR(100) NOT NULL,
    email                   VARCHAR(150) NOT NULL UNIQUE,
    password                VARCHAR(255) NOT NULL,
    role                    ENUM('admin','user') NOT NULL DEFAULT 'user',
    plan_id                 INT NOT NULL DEFAULT 1,
    cv_uploads_count        INT NOT NULL DEFAULT 0,
    emails_generated_count  INT NOT NULL DEFAULT 0,
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_plan FOREIGN KEY (plan_id) REFERENCES plans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- cv_uploads: one row per uploaded CV file, path stored on disk
-- under htdocs/uploads/cv/{user_id}/{stored_name}
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cv_uploads (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    original_name  VARCHAR(255) NOT NULL,
    stored_name    VARCHAR(255) NOT NULL,
    path           VARCHAR(500) NOT NULL,   -- relative path saved for lookup/serving
    extension      VARCHAR(10)  NOT NULL,
    size_bytes     INT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cvuploads_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- email_generations: one row per AI email generated, linked
-- back to the CV upload that produced it (if any)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_generations (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    cv_upload_id   INT NULL,
    job_post       TEXT NOT NULL,
    language       VARCHAR(50) NOT NULL DEFAULT 'English',
    result         LONGTEXT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_emailgen_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_emailgen_cv FOREIGN KEY (cv_upload_id) REFERENCES cv_uploads(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- OPTIONAL: create your first admin account manually after
-- registering normally through /auth/register, e.g.:
-- UPDATE users SET role = 'admin' WHERE email = 'you@example.com';
-- ------------------------------------------------------------
