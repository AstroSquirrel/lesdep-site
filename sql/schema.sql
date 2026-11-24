-- Схема базы данных сайта «Департамент лесного хозяйства Заречной области»
-- СУБД: MySQL 5.7+ / MariaDB 10.3+, кодировка utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS appeal_messages;
DROP TABLE IF EXISTS appeals;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS sections;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- Пользователи сайта. Роли: admin – администратор, employee – сотрудник департамента,
-- citizen – гражданин (зарегистрированный посетитель)
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login         VARCHAR(40)  NOT NULL UNIQUE,
    email         VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name     VARCHAR(150) NOT NULL,
    phone         VARCHAR(30)  NULL,
    role          ENUM('admin','employee','citizen') NOT NULL DEFAULT 'citizen',
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_by    INT UNSIGNED NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME     NULL,
    CONSTRAINT fk_users_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Разделы сайта (пункты главного меню, содержащие статьи)
CREATE TABLE sections (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(40)  NOT NULL UNIQUE,
    title       VARCHAR(100) NOT NULL,
    description VARCHAR(400) NOT NULL DEFAULT '',
    sort        INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Статьи (материалы разделов, новости, документы)
CREATE TABLE articles (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section_id   INT UNSIGNED NOT NULL,
    slug         VARCHAR(120) NOT NULL,
    title        VARCHAR(255) NOT NULL,
    summary      VARCHAR(600) NOT NULL DEFAULT '',
    body         MEDIUMTEXT   NOT NULL,
    image        VARCHAR(120) NULL,
    doc_meta     VARCHAR(255) NULL,          -- реквизиты документа (для раздела «Документы»)
    author_id    INT UNSIGNED NULL,
    is_published TINYINT(1)   NOT NULL DEFAULT 1,
    views        INT UNSIGNED NOT NULL DEFAULT 0,
    published_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME     NULL,
    UNIQUE KEY uq_section_slug (section_id, slug),
    KEY ix_published (is_published, published_at),
    CONSTRAINT fk_articles_section FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    CONSTRAINT fk_articles_author  FOREIGN KEY (author_id)  REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Баннеры главной страницы
CREATE TABLE banners (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title     VARCHAR(150) NOT NULL,
    text      VARCHAR(300) NOT NULL DEFAULT '',
    link      VARCHAR(255) NOT NULL DEFAULT '',
    image     VARCHAR(120) NULL,
    place     ENUM('slider','side') NOT NULL DEFAULT 'slider',
    sort      INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Обращения граждан (интернет-приёмная)
CREATE TABLE appeals (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    category    VARCHAR(80)  NOT NULL,
    subject     VARCHAR(200) NOT NULL,
    status      ENUM('new','in_work','answered','closed') NOT NULL DEFAULT 'new',
    assigned_to INT UNSIGNED NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY ix_user (user_id),
    CONSTRAINT fk_appeals_user     FOREIGN KEY (user_id)     REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_appeals_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Сообщения в переписке по обращению
CREATE TABLE appeal_messages (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appeal_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    body       TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY ix_appeal (appeal_id),
    CONSTRAINT fk_msg_appeal FOREIGN KEY (appeal_id) REFERENCES appeals(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_user   FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
