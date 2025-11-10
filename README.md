# fake-banking-site | Nexabank

Nexabank - A fake banking site for SIT - ICT2216

## Setup

1. Build container \
    `docker-compose build`
2. Run container \
    `docker-compose up`
3. Run command

```bash
    docker-compose exec laravel_php_app bash
    composer install
```

## Setup Database

1. Login to PGAdmin on port 8383

2. Paste and execute the queries in `sql` folder

    - `create_all_tables.sql`
    - `insert_dummy_data.sql`
