INSERT INTO mails (subject, content, created_at, status)
SELECT
    'Mock Subject ' || i,
    'Mock content for mail #' || i,
    NOW(),
    1
FROM generate_series(1, 100) AS s(i);
