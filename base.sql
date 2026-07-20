

-- ============================================================
-- OPERATEURS
-- ============================================================

CREATE TABLE operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- PREFIXES
-- ============================================================

CREATE TABLE prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    id_operateur INTEGER NOT NULL,
    actif BOOLEAN DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_operateur) REFERENCES operateurs(id)
);

-- ============================================================
-- TYPES D'OPERATIONS
-- ============================================================

CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    libelle TEXT NOT NULL,
    actif BOOLEAN DEFAULT 1
);

-- ============================================================
-- BAREMES DES FRAIS
-- ============================================================

CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min DECIMAL(15,2) NOT NULL,
    montant_max DECIMAL(15,2) NOT NULL,
    frais_fixe DECIMAL(15,2) DEFAULT 0,
    frais_pourcentage DECIMAL(5,2) DEFAULT 0,
    date_debut DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_fin DATETIME,
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id)
);

-- ============================================================
-- COMMISSIONS
-- ============================================================

CREATE TABLE configurations_commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,
    type_operation_id INTEGER NOT NULL,
    autre_operateur BOOLEAN DEFAULT 0,
    commission_pourcentage DECIMAL(5,2) NOT NULL DEFAULT 0,
    date_debut DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_fin DATETIME,
    FOREIGN KEY (operateur_id) REFERENCES operateurs(id),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id)
);

-- ============================================================
-- CLIENTS
-- ============================================================

CREATE TABLE client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    nom TEXT NOT NULL,
    role TEXT NOT NULL
        DEFAULT 'CLIENT'
        CHECK(role IN ('CLIENT','ADMIN')),
    prefixe_id INTEGER NOT NULL,
    date_premiere_connexion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(prefixe_id)
        REFERENCES prefixes(id)
);

-- ============================================================
-- COMPTES
-- ============================================================

CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL UNIQUE,
    solde DECIMAL(15,2) DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(client_id) REFERENCES client(id)
);

-- ============================================================
-- STATUT
-- ============================================================

CREATE TABLE statut (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL
);

-- ============================================================
-- OPERATIONS
-- ============================================================

CREATE TABLE operations (

    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reference TEXT NOT NULL UNIQUE,
    type_operation_id INTEGER NOT NULL,
    compte_source_id INTEGER,
    compte_destination_id INTEGER,
    montant DECIMAL(15,2) NOT NULL,
    frais DECIMAL(15,2) DEFAULT 0,
    montant_total DECIMAL(15,2) NOT NULL,
    bareme_frais_id INTEGER,
    frais_inclus BOOLEAN DEFAULT 0,
    statut INTEGER DEFAULT 1,
    date_operation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(type_operation_id)
        REFERENCES types_operations(id),
    FOREIGN KEY(compte_source_id)
        REFERENCES comptes(id),
    FOREIGN KEY(compte_destination_id)
        REFERENCES comptes(id),
    FOREIGN KEY(bareme_frais_id)
        REFERENCES baremes_frais(id),
    FOREIGN KEY(statut)
        REFERENCES statut(id)
);


CREATE TABLE operation_destinations (

    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operation_id INTEGER NOT NULL,
    compte_destination_id INTEGER NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    FOREIGN KEY(operation_id)
        REFERENCES operations(id),
    FOREIGN KEY(compte_destination_id)
        REFERENCES comptes(id)
);