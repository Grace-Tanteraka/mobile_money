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
    valeur_commision DECIMAL(20, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (client_hote) REFERENCES client(id),
    FOREIGN KEY (client_cible) REFERENCES client(id),
    FOREIGN KEY (operation_id) REFERENCES operation(id)
);

CREATE TABLE comission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    Nom VARCHAR(50) NOT NULL, -- Ex: 'Telma -> Orange'
    operateur_source_id INTEGER NOT NULL,
    operateur_cible_id INTEGER NOT NULL,
    taux_pourcentage DECIMAL(5, 2) NOT NULL, -- Ex: 10.00 (pour 10%)
    FOREIGN KEY (operateur_source_id) REFERENCES operateur(id),
    FOREIGN KEY (operateur_cible_id) REFERENCES operateur(id)
);

CREATE TABLE promotion_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    valeur DECIMAL(5, 2) NOT NULL
);