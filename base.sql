CREATE DATABASE mobile_money;

CREATE TABLE operateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    prefixe VARCHAR(6) NOT NULL UNIQUE
);

CREATE TABLE administrateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL
);

CREATE TABLE administration (
    id INT PRIMARY KEY AUTO_INCREMENT,
    administrateur_id INT,
    operateur_id INT,
    FOREIGN KEY (administrateur_id) REFERENCES administrateur(id),
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE operation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE client (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    operateur_id INT,
    telephone VARCHAR(15) NOT NULL UNIQUE,
    solde DECIMAL(20, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE frais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    montant DECIMAL(20, 2) NOT NULL,
    operation_id INT,
    FOREIGN KEY (operation_id) REFERENCES operation(id)
);

CREATE TABLE transaction (
    id INT PRIMARY KEY AUTO_INCREMENT,
    montant DECIMAL(20, 2) NOT NULL,
    frais DECIMAL(20, 2) NOT NULL,
    date DATETIME NOT NULL,
    operation_id INT,
    client_hote INT,
    client_cible INT,
    FOREIGN KEY (client_hote) REFERENCES client(id),
    FOREIGN KEY (client_cible) REFERENCES client(id),
    FOREIGN KEY (operation_id) REFERENCES operation(id)
);