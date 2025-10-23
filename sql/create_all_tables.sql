BEGIN;


CREATE TABLE IF NOT EXISTS public."User"
(
    "UserID" uuid NOT NULL,
    "Username" character varying(255) NOT NULL,
    "Password" text NOT NULL,
    "Role" character varying(20) NOT NULL,
    "LastLogin" date,
    PRIMARY KEY ("UserID"),
    CONSTRAINT "Unique Username" UNIQUE ("Username")
        INCLUDE("UserID")
);

CREATE TABLE IF NOT EXISTS public."Session"
(
    "SessionID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "IP" character varying(20) NOT NULL,
    "UserAgent" character varying(2000) NOT NULL,
    "Payload" text NOT NULL,
    "LastActivity" date NOT NULL,
    PRIMARY KEY ("SessionID"),
    UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."LoginHistory"
(
    "RecordID" uuid NOT NULL,
    "SessionID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "LastLogged" date NOT NULL,
    PRIMARY KEY ("RecordID"),
    UNIQUE ("SessionID"),
    UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."Staff"
(
    "StaffID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "DisplayName" character varying(255) NOT NULL,
    "DOB" date NOT NULL,
    "Contact" integer NOT NULL,
    "Email" character varying(255) NOT NULL,
    PRIMARY KEY ("StaffID"),
    UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."Account"
(
    "AccountID" bigint,
    "CustomerID" uuid,
    "AccountType" character varying(255),
    "Balance" money,
    CONSTRAINT "AccountID_Uniq" UNIQUE ("AccountID")
);

CREATE TABLE IF NOT EXISTS public."Customer"
(
    "CustomerID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "DisplayName" character varying(255) NOT NULL,
    "FirstName" character varying(255) NOT NULL,
    "LastName" character varying(255) NOT NULL,
    "DOB" date NOT NULL,
    "ContactNo" integer,
    "Email" character varying(255) NOT NULL,
    PRIMARY KEY ("CustomerID"),
    UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."Transaction"
(
    "TransactionID" uuid NOT NULL,
    "ToAccount" bigint NOT NULL,
    "FromAccount" bigint NOT NULL,
    "ReferenceNumber" bigint NOT NULL,
    "TransactionDate" date NOT NULL,
    "Amount" money NOT NULL,
    PRIMARY KEY ("TransactionID")
);

CREATE TABLE IF NOT EXISTS public."Claims"
(
    "ClaimID" uuid NOT NULL,
    "CustomerID" uuid NOT NULL,
    "ManagedBy" uuid NOT NULL,
    "ImagePath" character varying(1000) NOT NULL,
    "Description" character varying(1000) NOT NULL,
    "CreatedAt" date NOT NULL,
    "UpdatedAt" date NOT NULL,
    PRIMARY KEY ("ClaimID"),
    UNIQUE ("ManagedBy")
);

ALTER TABLE IF EXISTS public."Session"
    ADD FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."LoginHistory"
    ADD FOREIGN KEY ("SessionID")
    REFERENCES public."Session" ("SessionID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."LoginHistory"
    ADD FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Staff"
    ADD FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Account"
    ADD FOREIGN KEY ("CustomerID")
    REFERENCES public."Customer" ("CustomerID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Customer"
    ADD FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Transaction"
    ADD FOREIGN KEY ("ToAccount")
    REFERENCES public."Account" ("AccountID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Transaction"
    ADD FOREIGN KEY ("FromAccount")
    REFERENCES public."Account" ("AccountID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Claims"
    ADD FOREIGN KEY ("ManagedBy")
    REFERENCES public."Staff" ("StaffID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Claims"
    ADD FOREIGN KEY ("CustomerID")
    REFERENCES public."Customer" ("CustomerID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;

END;