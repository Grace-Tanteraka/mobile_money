-- CREATE DATABASE mobile_money;

CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL
);

CREATE TABLE prefixe (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(6) NOT NULL UNIQUE,
    operateur_id INTEGER,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE administrateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL
);

CREATE TABLE administration (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    administrateur_id INTEGER,
    operateur_id INTEGER,
    FOREIGN KEY (administrateur_id) REFERENCES administrateur(id),
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    operateur_id INTEGER,
    telephone VARCHAR(15) NOT NULL UNIQUE,
    solde DECIMAL(20, 2) NOT NULL DEFAULT 0.00,
    mdp VARCHAR(255) NOT NULL,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    montant_inf DECIMAL(20, 2) NOT NULL,
    montant_sup DECIMAL(20, 2) NOT NULL,
    montant_frais DECIMAL(20, 2) NOT NULL,
    operation_id INTEGER,
    FOREIGN KEY (operation_id) REFERENCES operation(id)
);

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    montant DECIMAL(20, 2) NOT NULL,
    frais DECIMAL(20, 2) NOT NULL,
    date DATETIME NOT NULL,
    operation_id INTEGER,
    client_hote INTEGER,
    client_cible INTEGER,
    FOREIGN KEY (client_hote) REFERENCES client(id),
    FOREIGN KEY (client_cible) REFERENCES client(id),
    FOREIGN KEY (operation_id) REFERENCES operation(id)
);

-- Datas
/* Donnee de test */
INSERT INTO operateur (nom, prefixe) VALUES ('Orange', '032'), ('Yas', '038');
/*mdp : 123*/
INSERT INTO administrateur (nom, prenom, email, mdp) VALUES ('Admin', 'Super', 'admin@gmail.com', '$2y$10$Q7sZoxFxdfTL9gdLYgOn4O8zs6YUILjiINCEHy3A0nNUXlZ/jg9Nq');
INSERT INTO client (nom, prenom, operateur_id, telephone) VALUES ('Client', 'Test', 1, '0321234567');
