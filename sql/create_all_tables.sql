BEGIN;


CREATE TABLE IF NOT EXISTS public."Account"
(
    "AccountID" bigint,
    "CustomerID" uuid,
    "AccountType" character varying(255) COLLATE pg_catalog."default",
    "Balance" money,
    CONSTRAINT "AccountID_Uniq" UNIQUE ("AccountID")
);

CREATE TABLE IF NOT EXISTS public."Claims"
(
    "ClaimID" uuid NOT NULL,
    "CustomerID" uuid NOT NULL,
    "ManagedBy" uuid NOT NULL,
    "ImagePath" character varying(1000) COLLATE pg_catalog."default" NOT NULL,
    "Description" character varying(1000) COLLATE pg_catalog."default" NOT NULL,
    "CreatedAt" timestamp with time zone NOT NULL,
    "UpdatedAt" timestamp with time zone NOT NULL,
    CONSTRAINT "Claims_pkey" PRIMARY KEY ("ClaimID"),
    CONSTRAINT "Claims_ManagedBy_key" UNIQUE ("ManagedBy")
);

CREATE TABLE IF NOT EXISTS public."Customer"
(
    "CustomerID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "DisplayName" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    "FirstName" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    "LastName" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    "DOB" date NOT NULL,
    "ContactNo" integer,
    "Email" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT "Customer_pkey" PRIMARY KEY ("CustomerID"),
    CONSTRAINT "Customer_UserID_key" UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."LoginHistory"
(
    "RecordID" uuid NOT NULL,
    "SessionID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "LastLogged" timestamp with time zone NOT NULL,
    CONSTRAINT "LoginHistory_pkey" PRIMARY KEY ("RecordID"),
    CONSTRAINT "LoginHistory_SessionID_key" UNIQUE ("SessionID"),
    CONSTRAINT "LoginHistory_UserID_key" UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."Session"
(
    "SessionID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "IP" character varying(20) COLLATE pg_catalog."default" NOT NULL,
    "UserAgent" character varying(2000) COLLATE pg_catalog."default" NOT NULL,
    "Payload" text COLLATE pg_catalog."default" NOT NULL,
    "LastActivity" timestamp with time zone NOT NULL,
    CONSTRAINT "Session_pkey" PRIMARY KEY ("SessionID"),
    CONSTRAINT "Session_UserID_key" UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."Staff"
(
    "StaffID" uuid NOT NULL,
    "UserID" uuid NOT NULL,
    "DisplayName" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    "DOB" date NOT NULL,
    "Contact" integer NOT NULL,
    "Email" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT "Staff_pkey" PRIMARY KEY ("StaffID"),
    CONSTRAINT "Staff_UserID_key" UNIQUE ("UserID")
);

CREATE TABLE IF NOT EXISTS public."Transaction"
(
    "TransactionID" uuid NOT NULL,
    "ToAccount" bigint NOT NULL,
    "FromAccount" bigint NOT NULL,
    "ReferenceNumber" bigint NOT NULL,
    "TransactionDate" timestamp with time zone NOT NULL,
    "Amount" money NOT NULL,
    CONSTRAINT "Transaction_pkey" PRIMARY KEY ("TransactionID")
);

CREATE TABLE IF NOT EXISTS public."User"
(
    "UserID" uuid NOT NULL,
    "Username" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    "Password" text COLLATE pg_catalog."default" NOT NULL,
    "Role" character varying(20) COLLATE pg_catalog."default" NOT NULL,
    "LastLogin" timestamp with time zone,
    CONSTRAINT "User_pkey" PRIMARY KEY ("UserID"),
    CONSTRAINT "Unique Username" UNIQUE ("Username")
        INCLUDE("UserID")
);

ALTER TABLE IF EXISTS public."Account"
    ADD CONSTRAINT "Account_CustomerID_fkey" FOREIGN KEY ("CustomerID")
    REFERENCES public."Customer" ("CustomerID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Claims"
    ADD CONSTRAINT "Claims_CustomerID_fkey" FOREIGN KEY ("CustomerID")
    REFERENCES public."Customer" ("CustomerID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Claims"
    ADD CONSTRAINT "Claims_ManagedBy_fkey" FOREIGN KEY ("ManagedBy")
    REFERENCES public."Staff" ("StaffID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;
CREATE INDEX IF NOT EXISTS "Claims_ManagedBy_key"
    ON public."Claims"("ManagedBy");


ALTER TABLE IF EXISTS public."Customer"
    ADD CONSTRAINT "Customer_UserID_fkey" FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;
CREATE INDEX IF NOT EXISTS "Customer_UserID_key"
    ON public."Customer"("UserID");


ALTER TABLE IF EXISTS public."LoginHistory"
    ADD CONSTRAINT "LoginHistory_SessionID_fkey" FOREIGN KEY ("SessionID")
    REFERENCES public."Session" ("SessionID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;
CREATE INDEX IF NOT EXISTS "LoginHistory_SessionID_key"
    ON public."LoginHistory"("SessionID");


ALTER TABLE IF EXISTS public."LoginHistory"
    ADD CONSTRAINT "LoginHistory_UserID_fkey" FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;
CREATE INDEX IF NOT EXISTS "LoginHistory_UserID_key"
    ON public."LoginHistory"("UserID");


ALTER TABLE IF EXISTS public."Session"
    ADD CONSTRAINT "Session_UserID_fkey" FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;
CREATE INDEX IF NOT EXISTS "Session_UserID_key"
    ON public."Session"("UserID");


ALTER TABLE IF EXISTS public."Staff"
    ADD CONSTRAINT "Staff_UserID_fkey" FOREIGN KEY ("UserID")
    REFERENCES public."User" ("UserID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;
CREATE INDEX IF NOT EXISTS "Staff_UserID_key"
    ON public."Staff"("UserID");


ALTER TABLE IF EXISTS public."Transaction"
    ADD CONSTRAINT "Transaction_FromAccount_fkey" FOREIGN KEY ("FromAccount")
    REFERENCES public."Account" ("AccountID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;


ALTER TABLE IF EXISTS public."Transaction"
    ADD CONSTRAINT "Transaction_ToAccount_fkey" FOREIGN KEY ("ToAccount")
    REFERENCES public."Account" ("AccountID") MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION
    NOT VALID;

END;