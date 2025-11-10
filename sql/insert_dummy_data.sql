
INSERT INTO public."User" VALUES ('bfbc8057-a47f-4aea-8d24-0676416e426c', 'CharLee', '$argon2id$v=19$m=131072,t=4,p=2$WW0xbWFWRXhiaTU5TDlaVw$2Sb0xKzs8U5SG6mrUYfPwSA/elnXC6ZrjcvEPSlb0o0', 'USER', '2025-11-05 13:27:29+00');
INSERT INTO public."User" VALUES ('18baec59-b3ee-4235-a229-cad327b1be13', 'staff2', '$argon2id$v=19$m=65536,t=4,p=1$ZW0yTDVhRjdSWG9GSnlDeg$wLrFf8w0wlV4+8+3b73gPlgscBzvVx5TItBgf5+hCVA', 'ADMIN', '2025-11-06 15:43:37.598043+00');
INSERT INTO public."User" VALUES ('f701197d-1c6c-4787-a24a-6d91ac4ba439', 'test1', '$argon2id$v=19$m=65536,t=4,p=1$bjgwb3VORjRTa01hUGF5aw$2AZMB2Uttlu6YKwYp2pD9Sy6zhfxfSnn6hSz+fJFRoc', 'DELETED', '2025-11-10 03:25:25+00');
INSERT INTO public."User" VALUES ('dee11f9c-55c5-48e5-a918-38bd0a6ef2fc', 'BobbyTee', '$argon2id$v=19$m=131072,t=4,p=2$RWxOUHlEeldpZktHeVVkRQ$7+77Mb3ZqMEttEX0Cg8K9Y3lQRORnhShz+yanHTnfCY', 'USER', '2025-10-27 14:21:38+00');
INSERT INTO public."User" VALUES ('1c0a9577-24a6-40f9-9151-c1b98fe16721', 'staff1', '$argon2id$v=19$m=131072,t=4,p=2$ZFNNSklydTc1ZTN6bXgueg$Sx2QGGtAUzt+YZBrJjTQ33mjuRUTf/J/HZs0dxIxEa4', 'STAFF', '2025-11-10 08:00:15+00');
INSERT INTO public."User" VALUES ('1a015fb0-706f-454b-bd3c-ec4efe065a15', 'user1', '$argon2id$v=19$m=65536,t=4,p=1$aGZLOFltMThOanAzZ0o3Nw$WQTAk+u1eFhgbg1ctIVTBqjKQJ4dup0tRT5rKcM6dN8', 'DELETED', NULL);
INSERT INTO public."User" VALUES ('419c72a8-be13-4003-a10f-07ceb4f2c6b7', 'AlicWil', '$argon2id$v=19$m=131072,t=4,p=2$Y0k0WWQ1YlhSYnB1SGU3TA$06hAtWmgcLlug00mE3NaYbT7+Qy+tOILMuKJ2nw4HeQ', 'USER', '2025-11-10 09:46:23+00');
INSERT INTO public."User" VALUES ('b836bc46-ac7e-4066-b954-3b43f412c07b', 'staff3', '$argon2id$v=19$m=131072,t=4,p=2$T2lNNE5iR3ZoV3p0VFZzQw$x7e2x2b5qWj1NsfYeOhyh+W3ZDyikxa5klCL0jj+nkY', 'STAFF', '2025-11-10 09:54:18.265196+00');
INSERT INTO public."User" VALUES ('7c20ddd0-ea0d-49ce-9aef-5f637cadf0b5', 'admin1', '$argon2id$v=19$m=131072,t=4,p=2$UXlPYmNpRU5LU0ZPdHdQMA$BGYn9DRlX6683cqOoFcRAYdeYODjgxCM2q5EJzishGI', 'ADMIN', '2025-11-10 12:40:37+00');


--
-- TOC entry 3488 (class 0 OID 16883)
-- Dependencies: 218
-- Data for Name: Customer; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Customer" VALUES ('f925eb89-e135-4e82-a0ad-abe2bc17f317', 'dee11f9c-55c5-48e5-a918-38bd0a6ef2fc', 'Bob T', 'Bob', 'Taylor', '1988-11-20', 98765432, 'bobt@example.com');
INSERT INTO public."Customer" VALUES ('e307c470-fd78-40d9-bc0c-b4ea95d21b98', 'bfbc8057-a47f-4aea-8d24-0676416e426c', 'Charlie L', 'Charlie', 'Lee', '1995-07-10', 92345678, 'charliel@example.com');
INSERT INTO public."Customer" VALUES ('45028a18-a7e9-4f63-9e5c-5d0cc0b4c447', '419c72a8-be13-4003-a10f-07ceb4f2c6b7', 'Alice F', 'Alice F', 'Williams', '1992-03-15', 91234567, 'alicew@example.com');
INSERT INTO public."Customer" VALUES ('a62ae297-f775-46fc-8f03-ea96acb126a1', 'f701197d-1c6c-4787-a24a-6d91ac4ba439', 'Deleted User', 'deleted', 'deleted', '0001-01-01', 0, 'deleted');
INSERT INTO public."Customer" VALUES ('d2d23cb2-3d05-4f26-bb7d-a832f6280bf0', '1a015fb0-706f-454b-bd3c-ec4efe065a15', 'Deleted User', 'deleted', 'deleted', '0001-01-01', 0, 'deleted');


--
-- TOC entry 3486 (class 0 OID 16869)
-- Dependencies: 216
-- Data for Name: Account; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Account" VALUES (100000006, 'e307c470-fd78-40d9-bc0c-b4ea95d21b98', 'Checking', '$190.99');
INSERT INTO public."Account" VALUES (100000004, 'f925eb89-e135-4e82-a0ad-abe2bc17f317', 'Investment', '$50,000.00');
INSERT INTO public."Account" VALUES (100000001, '45028a18-a7e9-4f63-9e5c-5d0cc0b4c447', 'Savings', '$990.00');
INSERT INTO public."Account" VALUES (100000002, '45028a18-a7e9-4f63-9e5c-5d0cc0b4c447', 'Checking', '$0.65');
INSERT INTO public."Account" VALUES (100000003, 'f925eb89-e135-4e82-a0ad-abe2bc17f317', 'Savings', '$7,899.80');
INSERT INTO public."Account" VALUES (100000005, 'e307c470-fd78-40d9-bc0c-b4ea95d21b98', 'Savings', '$1,089.00');


--
-- TOC entry 3487 (class 0 OID 16874)
-- Dependencies: 217
-- Data for Name: Claims; Type: TABLE DATA; Schema: public; Owner: admin
--



--
-- TOC entry 3489 (class 0 OID 16892)
-- Dependencies: 219
-- Data for Name: LoginHistory; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."LoginHistory" VALUES ('a1d8099c-197a-4673-8479-62bee73eb65a', '7c20ddd0-ea0d-49ce-9aef-5f637cadf0b5', '2025-11-10 12:40:37+00', '1R9J5RCCqXRlaUOz-3acbNmII7U-TVXbIA-GQuLF69Y-II9F');


--
-- TOC entry 3490 (class 0 OID 16901)
-- Dependencies: 220
-- Data for Name: Session; Type: TABLE DATA; Schema: public; Owner: admin
--



--
-- TOC entry 3491 (class 0 OID 16910)
-- Dependencies: 221
-- Data for Name: Staff; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Staff" VALUES ('afb21cba-29c3-481f-bf8f-9633007a08db', '7c20ddd0-ea0d-49ce-9aef-5f637cadf0b5', 'Admin One', '1997-05-28', 93456777, 'admin1@nexabank.com');
INSERT INTO public."Staff" VALUES ('07696573-87a2-4c52-975f-e305249a0201', '18baec59-b3ee-4235-a229-cad327b1be13', 'staff2', '1995-07-15', 91234567, 'staff2@nexabank.com');
INSERT INTO public."Staff" VALUES ('7bc30bce-bb08-4e92-b994-2a17f9795c23', '1c0a9577-24a6-40f9-9151-c1b98fe16721', 'Staff One', '1999-03-12', 98765432, 'staff@nexabank.com');
INSERT INTO public."Staff" VALUES ('6633b62e-3593-4d5a-801b-43349ec38ed7', 'b836bc46-ac7e-4066-b954-3b43f412c07b', 'Staff Three', '1995-07-15', 91234567, 'staff2@nexabank.com');


--
-- TOC entry 3492 (class 0 OID 16919)
-- Dependencies: 222
-- Data for Name: Transaction; Type: TABLE DATA; Schema: public; Owner: admin
--

INSERT INTO public."Transaction" VALUES ('323968d2-feac-4c17-af68-cf461d5656b1', 100000001, 100000002, 700001, '2025-10-18 14:30:21.673114+00', '$200.00', NULL);
INSERT INTO public."Transaction" VALUES ('468902c2-c378-4cf6-b630-f92115b65135', 100000003, 100000001, 700002, '2025-10-19 14:30:21.673114+00', '$50.00', NULL);
INSERT INTO public."Transaction" VALUES ('eabbfc0e-c97a-477d-8a6e-b2273b87dbbb', 100000004, 100000003, 700003, '2025-10-20 14:30:21.673114+00', '$500.00', NULL);
INSERT INTO public."Transaction" VALUES ('a877fc89-77cf-4836-95a2-f6de92272392', 100000005, 100000004, 700004, '2025-10-21 14:30:21.673114+00', '$1,000.00', NULL);
INSERT INTO public."Transaction" VALUES ('f7292b01-b704-4dc8-ac20-a44e13065e87', 100000006, 100000005, 700005, '2025-10-22 14:30:21.673114+00', '$75.50', NULL);
INSERT INTO public."Transaction" VALUES ('d5bdd89b-e53b-4027-af2e-bc334a9ed40d', 100000002, 100000006, 700006, '2025-10-23 14:30:21.673114+00', '$20.00', NULL);
INSERT INTO public."Transaction" VALUES ('4e5e7b66-5c7c-421d-b14a-d97f64663549', 100000004, 100000003, 700007, '2025-10-24 14:30:21.673114+00', '$125.00', NULL);
INSERT INTO public."Transaction" VALUES ('66d072a2-06cc-40ca-9b65-206403a1219e', 100000005, 100000004, 700008, '2025-10-25 14:30:21.673114+00', '$90.00', NULL);
INSERT INTO public."Transaction" VALUES ('d186b359-dbfd-4383-a25a-305f118071e2', 100000003, 100000001, 700009, '2025-10-26 14:30:21.673114+00', '$2,500.00', NULL);
INSERT INTO public."Transaction" VALUES ('bba0badc-31fc-4c70-8b8e-11463071960d', 100000006, 100000004, 700010, '2025-10-27 14:30:21.673114+00', '$42.00', NULL);
INSERT INTO public."Transaction" VALUES ('8a8943a1-32da-4a70-8f8f-6da580cc7adf', 100000006, 100000005, 500276736189585160, '2025-10-31 15:09:36+00', '$100.00', NULL);
INSERT INTO public."Transaction" VALUES ('7ee8dada-9009-4ac2-85df-03f2072a3ecd', 100000006, 100000005, 3429335067552430068, '2025-10-31 15:14:55+00', '$110.00', NULL);
INSERT INTO public."Transaction" VALUES ('0934a184-7130-4868-a4fe-76a50d0ac577', 100000005, 0, 5418746870545206279, '2025-11-01 17:09:10+00', '$100.00', 'DEPOSIT');
INSERT INTO public."Transaction" VALUES ('f6e14d41-6beb-45df-a9e5-d8ec7d21fc3c', 100000005, 0, 7259697378789034280, '2025-11-01 17:10:50+00', '$200.00', 'DEPOSIT');
INSERT INTO public."Transaction" VALUES ('037553c8-ccfa-4806-bbee-001937198709', 0, 100000006, 5555188381480117957, '2025-11-05 07:58:17+00', '$10.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('4189f9ad-a436-4cb3-b509-f19dbd637dcd', 0, 100000006, 1168354915823963265, '2025-11-05 08:01:14+00', '$10.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('5c1ebb21-46a0-477f-b4ed-5328da0be7bd', 0, 100000006, 5376954614680182719, '2025-11-05 08:01:42+00', '$10.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('2034322c-6e52-487b-83d4-69103922cbad', 0, 100000006, 7526943755682732653, '2025-11-05 11:59:49+00', '$10.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('ac12aaac-e65f-41b0-9249-0a7cae5eee03', 0, 100000001, 1912398391340486755, '2025-11-05 13:29:22+00', '$200.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('56219786-531c-41b6-9983-06d31859bc89', 0, 100000001, 8868629529918547748, '2025-11-08 08:39:41+00', '$10.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('7467a4dd-4513-48e9-9db6-a6069a1f6455', 0, 100000002, 7402949639743993605, '2025-11-08 08:43:11+00', '$50.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('8ee06b38-ad00-4c20-93bb-9977650161a1', 0, 100000001, 3995343665854654303, '2025-11-09 09:46:41+00', '$200.00', 'WITHDRAW');
INSERT INTO public."Transaction" VALUES ('501305e6-c070-4c5f-8a49-8332c269fcae', 100000003, 100000002, 6073765641359399359, '2025-11-10 09:47:00+00', '$100.00', 'TRANSFER');


--
-- TOC entry 3494 (class 0 OID 17028)
-- Dependencies: 224
-- Data for Name: UserOTP; Type: TABLE DATA; Schema: public; Owner: admin
--
