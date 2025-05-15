# How to make it work locally

## Running the environment

- Build the app image with the following command:

```bash
docker-compose up -d --build
```

## Create database tables

- Connect to the database (you can find db credentials in `Core/Configs/db.php`) and run all SQL commands from `tables.sql` file.

## Upload CSV file

- Upload CSV file to the server using curl or Postman.

```bash
curl --location 'http://localhost:8080/v1/files/upload' \
    --form 'file=@"/home/xxx/Downloads/Данные для тестового.csv"'
```

## Import subscribers from uploaded CSV file

- Open docker bash

```bash
 docker exec -it docker exec -it reimagined-winner_app_1 bash
```

- Run import command

```bash
php console.php command:import-subscribers-from-uploaded-files
```
## Sent the next mail to all subscribers

- Add fake mails into the database

```sql
INSERT INTO 
    mails (subject, content, created_at, status)
SELECT
    'Mock Subject ' || i,
    'Mock content for mail #' || i,
    NOW(),
    1
FROM generate_series(1, 100) AS s(i);
```
- Run command to send the next mail to all subscribers

```bash
php console.php command:send-next-mail-to-all-subscribers
```

Done.
