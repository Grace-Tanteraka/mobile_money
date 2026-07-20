PRAGMA foreign_keys = ON;

-- =========================
-- TABLE : departments
-- =========================
CREATE TABLE departments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE,
    description TEXT
);

-- =========================
-- TABLE : types_conge
-- =========================
CREATE TABLE types_conge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL UNIQUE,
    jours_annuels INTEGER NOT NULL,
    deductible INTEGER NOT NULL DEFAULT 1
);

-- =========================
-- TABLE : employes
-- =========================
CREATE TABLE employes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('employe', 'rh', 'admin')),
    
    departement_id INTEGER,
    
    date_embauche DATE NOT NULL,
    actif INTEGER NOT NULL DEFAULT 1,

    FOREIGN KEY (departement_id)
        REFERENCES departments(id)
        ON DELETE SET NULL
);

-- =========================
-- TABLE : soldes
-- =========================
CREATE TABLE soldes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,

    annee INTEGER NOT NULL,

    jours_attribues INTEGER NOT NULL DEFAULT 0,
    jours_pris INTEGER NOT NULL DEFAULT 0,

    FOREIGN KEY (employe_id)
        REFERENCES employes(id)
        ON DELETE CASCADE,

    FOREIGN KEY (type_conge_id)
        REFERENCES types_conge(id)
        ON DELETE CASCADE,

    UNIQUE(employe_id, type_conge_id, annee)
);

-- =========================
-- TABLE : conges
-- =========================
CREATE TABLE conges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,

    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,

    nb_jours INTEGER NOT NULL,

    motif TEXT,

    statut TEXT NOT NULL DEFAULT 'en_attente'
        CHECK(statut IN (
            'en_attente',
            'approuvee',
            'refusee',
            'annulee'
        )),

    commentaire_rh TEXT,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    traite_par INTEGER,

    FOREIGN KEY (employe_id)
        REFERENCES employes(id)
        ON DELETE CASCADE,

    FOREIGN KEY (type_conge_id)
        REFERENCES types_conge(id)
        ON DELETE CASCADE,

    FOREIGN KEY (traite_par)
        REFERENCES employes(id)
        ON DELETE SET NULL
);

-- =========================
-- DONNEES DE TEST
-- =========================

-- Departments
INSERT INTO departments (nom, description) VALUES
('Informatique', 'Département IT'),
('RH', 'Ressources humaines'),
('Finance', 'Gestion financière');

-- Types de congé
INSERT INTO types_conge (libelle, jours_annuels, deductible) VALUES
('Congé annuel', 30, 1),
('Congé maladie', 15, 0),
('Congé exceptionnel', 5, 1);

-- Employés
INSERT INTO employes (
    nom,
    prenom,
    email,
    password,
    role,
    departement_id,
    date_embauche,
    actif
) VALUES
(
    'Admin',
    'System',
    'admin@techmada.mg',
    '$2y$10$PKs9VsMKFuylz.IzIcSJkeSq5Pp8.4Jr277voe92JgDKBjNinNnsC',
    'admin',
    1,
    '2024-01-01',
    1
),
(
    'Rakoto',
    'Jean',
    'employe@techmada.mg',
    '$2y$10$xCELSzhnz/3Ik9KJ/WvKz.8rHSW0RJFweqs0mxCvwxLU8kBfxCF9G',
    'employe',
    1,
    '2024-02-10',
    1
),
(
    'Rabe',
    'Sarah',
    'rh@techmada.mg',
    '$2y$10$QH58JaLwdkTj8KvGk2w8weCWEKkj.ANSj8Pc10Kml3DkKc1rvbF9C',
    'rh',
    2,
    '2023-06-15',
    1
);

-- Soldes
INSERT INTO soldes (
    employe_id,
    type_conge_id,
    annee,
    jours_attribues,
    jours_pris
) VALUES
(2, 1, 2026, 30, 5),
(2, 2, 2026, 15, 2),
(3, 1, 2026, 30, 3);

-- Congés
INSERT INTO conges (
    employe_id,
    type_conge_id,
    date_debut,
    date_fin,
    nb_jours,
    motif,
    statut,
    commentaire_rh,
    traite_par
) VALUES
(
    2,
    1,
    '2026-05-20',
    '2026-05-25',
    5,
    'Vacances',
    'approuvee',
    'Demande validée',
    3
),
(
    2,
    2,
    '2026-06-01',
    '2026-06-02',
    2,
    'Maladie',
    'en_attente',
    NULL,
    NULL
);