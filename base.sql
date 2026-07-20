-- CREATE DATABASE mobile_money;

CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL,
    prefixe VARCHAR(6) NOT NULL UNIQUE
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
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    montant DECIMAL(20, 2) NOT NULL,
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
