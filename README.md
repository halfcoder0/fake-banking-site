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


## FOR OTP
Create the UserOTP table:

CREATE TABLE "UserOTP" (
  "UserID" UUID PRIMARY KEY REFERENCES "User"("UserID") ON DELETE CASCADE,
  "Code"   VARCHAR(6) NOT NULL,
  "ExpiresAt" TIMESTAMP WITH TIME ZONE NOT NULL
);

2. composer require phpmailer/phpmailer
(Add this on the docker container)
