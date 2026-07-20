INSERT INTO operateur (nom) VALUES
('Yas'),
('Airtel'),
('Orange');

INSERT INTO prefixe (code, operateur_id) VALUES
('034', 1),
('038', 1),
('033', 2),
('032', 3),
('037', 3);

INSERT INTO client (nom, prenom, telephone, operateur_id) VALUES
('Rakoto', 'Jean', '0341234567', 1),
('Rabe', 'Marie', '0389876543', 1),
('Andrianina', 'Hery', '0334567890', 2),
('Rasoa', 'Lala', '0321234567', 3);

INSERT INTO operation (libelle) VALUES
('Depot'),
('Retrait'),
('Transfert');

-- ============================================================
-- INSERTION DES FRAIS PAR TRANCHE (OPÉRATIONS 2 ET 3)
-- ============================================================

-- Pour l'opération ID 2 (ex: Retrait)
INSERT INTO frais (montant_inf, montant_sup, montant_frais, operation_id) VALUES
(100, 1000, 50, 2),
(1001, 5000, 50, 2),
(5001, 10000, 100, 2),
(10001, 25000, 200, 2),
(25001, 50000, 400, 2),
(50001, 100000, 800, 2),
(100001, 250000, 1500, 2),
(250001, 500000, 1500, 2),
(500001, 1000000, 2500, 2),
(1000001, 2000000, 3000, 2);

-- Pour l'opération ID 3 (ex: Transfert)
INSERT INTO frais (montant_inf, montant_sup, montant_frais, operation_id) VALUES
(100, 1000, 50, 3),
(1001, 5000, 50, 3),
(5001, 10000, 100, 3),
(10001, 25000, 200, 3),
(25001, 50000, 400, 3),
(50001, 100000, 800, 3),
(100001, 250000, 1500, 3),
(250001, 500000, 1500, 3),
(500001, 1000000, 2500, 3),
(1000001, 2000000, 3000, 3);

-- depot
INSERT INTO transactions (montant, frais, date, operation_id, client_hote) VALUES
(500000, 0, '2023-10-01 10:00:00', 1, 1),
(20000, 0, '2023-10-02 14:30:00', 1, 2),
(150000, 0, '2023-10-03 09:15:00', 1, 3),
(300000, 0, '2023-10-04 16:45:00', 1, 4);

-- retrait
INSERT INTO transactions (montant, frais, date, operation_id, client_hote) VALUES
(100000, 50, '2023-10-05 11:20:00', 2, 1),
(50000, 50, '2023-10-06 15:10:00', 2, 2),
(200000, 100, '2023-10-07 13:40:00', 2, 3),
(80000, 50, '2023-10-08 17:30:00', 2, 4);

-- transfert
INSERT INTO transactions (montant, frais, date, operation_id, client_hote, client_cible) VALUES
(50000, 50, '2023-10-09 12:00:00', 3, 1, 2),
(100000, 100, '2023-10-10 14:30:00', 3, 2, 3),
(150000, 200, '2023-10-11 09:15:00', 3, 3, 4),
(200000, 400, '2023-10-12 16:45:00', 3, 4, 1);