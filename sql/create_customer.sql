CREATE TABLE tblecustomer (
id SERIAL PRIMARY KEY,
	fullname VARCHAR(255),
	mobilenumber VARCHAR(255),
	email VARCHAR(255),
	password VARCHAR(255),
	RegDate TIMESTAMP DEFAULT NOW()
);
