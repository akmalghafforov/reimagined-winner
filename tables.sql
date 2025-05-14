-- Table: files
CREATE TABLE files (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    path VARCHAR(300) NOT NULL,
    status INTEGER NOT NULL,
    uploaded_at TIMESTAMP NOT NULL DEFAULT NOW(),
    hash_md5 CHAR(32),
    hash_sha256 CHAR(64)
);

-- Table: subscribers
CREATE TABLE subscribers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    number BIGINT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Table: mail
CREATE TABLE mail (
    id SERIAL PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Table: sent_mail
CREATE TABLE sent_mail (
    id SERIAL PRIMARY KEY,
    mail_id INTEGER NOT NULL REFERENCES mail(id) ON DELETE CASCADE,
    subscriber_id INTEGER NOT NULL REFERENCES subscribers(id) ON DELETE CASCADE,
    sent_at TIMESTAMP NOT NULL DEFAULT NOW()
);

ALTER TABLE files ADD COLUMN last_processed_at TIMESTAMP;

ALTER TABLE subscribers ADD CONSTRAINT unique_number UNIQUE (number);

CREATE INDEX idx_subscribers_number ON subscribers (number);
