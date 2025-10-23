INSERT INTO Customer (CustomerID,displayName, firstname, lastname, username, dob, contact, email)
VALUES
  ('01', 'Ambrose Sus', 'Ambrose', 'Sus', 'susambrose', '2001-09-29', '+6588221714', 'ambrosesus@yes.com');


INSERT INTO ACCOUNT (AccountID, CustomerID, balance)
VALUES
    ('1', '01', 10000.00);
