--
-- TOC entry 3492 (class 0 OID 16924)
-- Dependencies: 223
-- Data for Name: User; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."User" VALUES ('419c72a8-be13-4003-a10f-07ceb4f2c6b7', 'CharLee', '$argon2id$v=19$m=131072,t=4,p=2$Y0k0WWQ1YlhSYnB1SGU3TA$06hAtWmgcLlug00mE3NaYbT7+Qy+tOILMuKJ2nw4HeQ', 'USER', '2025-10-27 14:21:02+00');
INSERT INTO public."User" VALUES ('bfbc8057-a47f-4aea-8d24-0676416e426c', 'AlicWil', '$argon2id$v=19$m=131072,t=4,p=2$WW0xbWFWRXhiaTU5TDlaVw$2Sb0xKzs8U5SG6mrUYfPwSA/elnXC6ZrjcvEPSlb0o0', 'USER', '2025-10-27 14:21:26+00');
INSERT INTO public."User" VALUES ('dee11f9c-55c5-48e5-a918-38bd0a6ef2fc', 'BobbyTee', '$argon2id$v=19$m=131072,t=4,p=2$cXdnWVRGN1ZaNG40ZDRxNw$+YpG4977Y9TzYC7RDB/aMv/XQ7JYlgnpXsYjScXjyiQ', 'USER', '2025-10-27 14:21:38+00');
INSERT INTO public."User" VALUES ('1c0a9577-24a6-40f9-9151-c1b98fe16721', 'staff1', '$argon2id$v=19$m=131072,t=4,p=2$ZFNNSklydTc1ZTN6bXgueg$Sx2QGGtAUzt+YZBrJjTQ33mjuRUTf/J/HZs0dxIxEa4', 'STAFF', '2025-10-27 14:21:50+00');
INSERT INTO public."User" VALUES ('7c20ddd0-ea0d-49ce-9aef-5f637cadf0b5', 'admin1', '$argon2id$v=19$m=131072,t=4,p=2$UXlPYmNpRU5LU0ZPdHdQMA$BGYn9DRlX6683cqOoFcRAYdeYODjgxCM2q5EJzishGI', 'ADMIN', '2025-10-27 14:22:41+00');


--
-- TOC entry 3487 (class 0 OID 16883)
-- Dependencies: 218
-- Data for Name: Customer; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Customer" VALUES ('45028a18-a7e9-4f63-9e5c-5d0cc0b4c447', '419c72a8-be13-4003-a10f-07ceb4f2c6b7', 'Alice W', 'Alice', 'Williams', '1992-03-15', 91234567, 'alicew@example.com');
INSERT INTO public."Customer" VALUES ('f925eb89-e135-4e82-a0ad-abe2bc17f317', 'dee11f9c-55c5-48e5-a918-38bd0a6ef2fc', 'Bob T', 'Bob', 'Taylor', '1988-11-20', 98765432, 'bobt@example.com');
INSERT INTO public."Customer" VALUES ('e307c470-fd78-40d9-bc0c-b4ea95d21b98', 'bfbc8057-a47f-4aea-8d24-0676416e426c', 'Charlie L', 'Charlie', 'Lee', '1995-07-10', 92345678, 'charliel@example.com');


--
-- TOC entry 3485 (class 0 OID 16869)
-- Dependencies: 216
-- Data for Name: Account; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Account" VALUES (100000001, '45028a18-a7e9-4f63-9e5c-5d0cc0b4c447', 'Savings', '$1,500.00');
INSERT INTO public."Account" VALUES (100000002, '45028a18-a7e9-4f63-9e5c-5d0cc0b4c447', 'Checking', '$250.75');
INSERT INTO public."Account" VALUES (100000003, 'f925eb89-e135-4e82-a0ad-abe2bc17f317', 'Savings', '$8,200.00');
INSERT INTO public."Account" VALUES (100000004, 'f925eb89-e135-4e82-a0ad-abe2bc17f317', 'Investment', '$50,000.00');
INSERT INTO public."Account" VALUES (100000005, 'e307c470-fd78-40d9-bc0c-b4ea95d21b98', 'Savings', '$999.99');
INSERT INTO public."Account" VALUES (100000006, 'e307c470-fd78-40d9-bc0c-b4ea95d21b98', 'Checking', '$120.00');


--
-- TOC entry 3490 (class 0 OID 16910)
-- Dependencies: 221
-- Data for Name: Staff; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Staff" VALUES ('7bc30bce-bb08-4e92-b994-2a17f9795c23', '1c0a9577-24a6-40f9-9151-c1b98fe16721', 'Staff One', '1999-03-12', 98765432, 'staff@nexabank.com');
INSERT INTO public."Staff" VALUES ('afb21cba-29c3-481f-bf8f-9633007a08db', '7c20ddd0-ea0d-49ce-9aef-5f637cadf0b5', 'Admin One', '1997-05-28', 93456777, 'admin1@nexabank.com');


--
-- TOC entry 3486 (class 0 OID 16874)
-- Dependencies: 217
-- Data for Name: Claims; Type: TABLE DATA; Schema: public; Owner: admin
--



--
-- TOC entry 3489 (class 0 OID 16901)
-- Dependencies: 220
-- Data for Name: Session; Type: TABLE DATA; Schema: public; Owner: admin
--



--
-- TOC entry 3488 (class 0 OID 16892)
-- Dependencies: 219
-- Data for Name: LoginHistory; Type: TABLE DATA; Schema: public; Owner: admin
--



--
-- TOC entry 3491 (class 0 OID 16919)
-- Dependencies: 222
-- Data for Name: Transaction; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Transaction" VALUES ('323968d2-feac-4c17-af68-cf461d5656b1', 100000001, 100000002, 700001, '2025-10-18 14:30:21.673114+00', '$200.00');
INSERT INTO public."Transaction" VALUES ('468902c2-c378-4cf6-b630-f92115b65135', 100000003, 100000001, 700002, '2025-10-19 14:30:21.673114+00', '$50.00');
INSERT INTO public."Transaction" VALUES ('eabbfc0e-c97a-477d-8a6e-b2273b87dbbb', 100000004, 100000003, 700003, '2025-10-20 14:30:21.673114+00', '$500.00');
INSERT INTO public."Transaction" VALUES ('a877fc89-77cf-4836-95a2-f6de92272392', 100000005, 100000004, 700004, '2025-10-21 14:30:21.673114+00', '$1,000.00');
INSERT INTO public."Transaction" VALUES ('f7292b01-b704-4dc8-ac20-a44e13065e87', 100000006, 100000005, 700005, '2025-10-22 14:30:21.673114+00', '$75.50');
INSERT INTO public."Transaction" VALUES ('d5bdd89b-e53b-4027-af2e-bc334a9ed40d', 100000002, 100000006, 700006, '2025-10-23 14:30:21.673114+00', '$20.00');
INSERT INTO public."Transaction" VALUES ('4e5e7b66-5c7c-421d-b14a-d97f64663549', 100000004, 100000003, 700007, '2025-10-24 14:30:21.673114+00', '$125.00');
INSERT INTO public."Transaction" VALUES ('66d072a2-06cc-40ca-9b65-206403a1219e', 100000005, 100000004, 700008, '2025-10-25 14:30:21.673114+00', '$90.00');
INSERT INTO public."Transaction" VALUES ('d186b359-dbfd-4383-a25a-305f118071e2', 100000003, 100000001, 700009, '2025-10-26 14:30:21.673114+00', '$2,500.00');
INSERT INTO public."Transaction" VALUES ('bba0badc-31fc-4c70-8b8e-11463071960d', 100000006, 100000004, 700010, '2025-10-27 14:30:21.673114+00', '$42.00');

