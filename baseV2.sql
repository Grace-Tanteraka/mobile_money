
CREATE TABLE comission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    Nom VARCHAR(50) NOT NULL, -- Ex: 'Telma -> Orange'
    operateur_source_id INTEGER NOT NULL,
    operateur_cible_id INTEGER NOT NULL,
    taux_pourcentage DECIMAL(5, 2) NOT NULL, -- Ex: 10.00 (pour 10%)
    FOREIGN KEY (operateur_source_id) REFERENCES operateur(id),
    FOREIGN KEY (operateur_cible_id) REFERENCES operateur(id)
);

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    montant DECIMAL(20, 2) NOT NULL,
    frais DECIMAL(20, 2) NOT NULL,
    date DATETIME NOT NULL,
    operation_id INTEGER,
    client_hote INTEGER,
    client_cible INTEGER,
    valeur_commision DECIMAL(20, 2) NOT NULL,
    FOREIGN KEY (client_hote) REFERENCES client(id),
    FOREIGN KEY (client_cible) REFERENCES client(id),
    FOREIGN KEY (operation_id) REFERENCES operation(id)
);

-- Nettoyage préalable (optionnel)
DELETE FROM comission;

-- Insertion des règles de commissions inter-opérateurs
-- Taux : Yas (1) <-> Airtel (2) = 8%
INSERT INTO comission (Nom, operateur_source_id, operateur_cible_id, taux_pourcentage) 
VALUES ('Yas -> Airtel', 1, 2, 8.00);

INSERT INTO comission (Nom, operateur_source_id, operateur_cible_id, taux_pourcentage) 
VALUES ('Airtel -> Yas', 2, 1, 8.00);


-- Taux : Yas (1) <-> Orange (3) = 10%
INSERT INTO comission (Nom, operateur_source_id, operateur_cible_id, taux_pourcentage) 
VALUES ('Yas -> Orange', 1, 3, 10.00);

INSERT INTO comission (Nom, operateur_source_id, operateur_cible_id, taux_pourcentage) 
VALUES ('Orange -> Yas', 3, 1, 10.00);


-- Taux : Airtel (2) <-> Orange (3) = 5%
INSERT INTO comission (Nom, operateur_source_id, operateur_cible_id, taux_pourcentage) 
VALUES ('Airtel -> Orange', 2, 3, 5.00);

INSERT INTO comission (Nom, operateur_source_id, operateur_cible_id, taux_pourcentage) 
VALUES ('Orange -> Airtel', 3, 2, 5.00);