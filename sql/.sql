CREATE TABLE Customer (
    CustomerID SERIAL PRIMARY KEY,
    displayName TEXT NOT NULL,
    firstname TEXT NOT NULL,
    lastname TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    dob DATE,
    contact VARCHAR(15),
    email TEXT UNIQUE NOT NULL
);

CREATE TABLE Account (
    AccountID SERIAL PRIMARY KEY,
    CustomerID INT NOT NULL REFERENCES Customer(CustomerID) ON DELETE CASCADE,
    balance NUMERIC(14,2) DEFAULT 0.00
);