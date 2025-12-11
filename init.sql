DROP DATABASE IF EXISTS socialapp;
CREATE DATABASE socialapp;
USE socialapp;

CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       blocked BOOL DEFAULT 0,
                       name VARCHAR(50) NOT NULL UNIQUE,
                       passwd_hashed VARCHAR(255) NOT NULL,
                       created_at DATE NOT NULL
);

CREATE TABLE friends (
                         user_id INT,
                         friend_id INT,
                         PRIMARY KEY (user_id, friend_id),
                         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                         FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE groups (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE texts (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       group_id INT NOT NULL,
                       user_id INT NOT NULL,
                       content TEXT NOT NULL,
                       sent_at DATETIME NOT NULL,
                       blocked BOOL NOT NULL DEFAULT 0,
                       FOREIGN KEY (group_id) REFERENCES groups(id),
                       FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Users
INSERT INTO users (name, passwd_hashed, created_at) VALUES
                                                        ('Adam', 'heslo1', '2024-01-10'),
                                                        ('Boris', 'heslo2', '2024-01-11'),
                                                        ('Cyril', 'heslo3', '2024-01-09'),
                                                        ('David', 'heslo4', '2024-01-20'),
                                                        ('Eva', 'heslo5', '2024-01-18'),
                                                        ('Filip', 'heslo6', '2024-01-05'),
                                                        ('Gabriela', 'heslo7', '2024-01-15'),
                                                        ('Honza', 'heslo8', '2024-01-03'),
                                                        ('Igor', 'heslo9', '2024-01-21'),
                                                        ('Jana', 'heslo10', '2024-01-19');

-- Groups (NEEDED!)
INSERT INTO groups (name) VALUES
                              ('PHP chat'),
                              ('School'),
                              ('Gaming'),
                              ('Family'),
                              ('Work');

-- Messages
INSERT INTO texts (group_id, user_id, content, sent_at) VALUES
                                                            (1, 1, 'Ahoj, resi nekdo PHP?', '2024-02-01 10:00:00'),
                                                            (1, 2, 'Jo, ja tu jsem.', '2024-02-01 10:05:00'),
                                                            (1, 3, 'Muzu taky pomoct.', '2024-02-01 10:06:00'),

                                                            (2, 4, 'Mame ukol do skoly?', '2024-02-02 08:15:00'),
                                                            (2, 5, 'Jo, posilam zadani.', '2024-02-02 08:16:00'),
                                                            (2, 6, 'Diky!', '2024-02-02 08:17:00'),

                                                            (3, 7, 'Kdo dnes hraje CS?', '2024-02-03 19:00:00'),
                                                            (3, 8, 'Ja muzu.', '2024-02-03 19:01:00'),
                                                            (3, 9, 'Jdu taky.', '2024-02-03 19:05:00'),

                                                            (4, 10, 'Kdy bude rodinny obed?', '2024-02-04 11:30:00'),
                                                            (4, 1, 'V nedeli!', '2024-02-04 11:32:00'),

                                                            (5, 2, 'Kdo jde zitra do prace?', '2024-02-05 07:00:00'),
                                                            (5, 3, 'Jdu ja.', '2024-02-05 07:03:00');


INSERT INTO friends (user_id, friend_id) VALUES
                                             (1, 2), (2, 1),
                                             (1, 3), (3, 1),
                                             (2, 3), (3, 2),
                                             (4, 5), (5, 4),
                                             (6, 7), (7, 6),
                                             (8, 9), (9, 8),
                                             (10, 1), (1, 10);

-- Example queries:
SELECT * FROM users ORDER BY created_at DESC;

SELECT * FROM users WHERE created_at = '2024-01-10';

SELECT
    u.name AS UserName,
    f2.name AS FriendName
FROM friends f
         JOIN users u  ON u.id = f.user_id
         JOIN users f2 ON f2.id = f.friend_id;

SELECT
    texts.id,
    texts.content,
    texts.sent_at,
    users.name AS author,
    groups.name AS group_name
FROM texts
         JOIN users ON texts.user_id = users.id
         JOIN groups ON texts.group_id = groups.id;

SELECT
    users.id,
    users.name,
    COUNT(friends.friend_id) AS friend_count
FROM users
         LEFT JOIN friends ON users.id = friends.user_id
GROUP BY users.id;

SELECT
    groups.name AS group_name,
    COUNT(texts.id) AS messages_count
FROM groups
         LEFT JOIN texts ON groups.id = texts.group_id
GROUP BY groups.id;

SELECT
    texts.content,
    texts.sent_at,
    groups.name AS group_name
FROM texts
         JOIN groups ON texts.group_id = groups.id
WHERE texts.user_id = 1;
