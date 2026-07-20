-- ============================================================
-- BASE DE DONNÉES : Simulateur d'opérateur Mobile Money
-- Version 1
-- ============================================================


CREATE TABLE prefixes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    prefixe         VARCHAR(5) NOT NULL UNIQUE, 
    actif           BOOLEAN DEFAULT TRUE,
    date_creation   DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE types_operations (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    code    VARCHAR(20) NOT NULL UNIQUE, 
    libelle VARCHAR(50) NOT NULL,
    actif   BOOLEAN DEFAULT TRUE
);

CREATE TABLE baremes_frais (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    type_operation_id   INT NOT NULL,
    montant_min         DECIMAL(15,2) NOT NULL,
    montant_max         DECIMAL(15,2) NOT NULL,   -- mettre une très grande valeur si "et plus"
    frais_fixe          DECIMAL(15,2) DEFAULT 0,
    frais_pourcentage   DECIMAL(5,2) DEFAULT 0,   -- en pourcentage
    date_debut          DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_fin            DATETIME NULL,     
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id)
);



CREATE TABLE client (
    id                        INT AUTO_INCREMENT PRIMARY KEY,
    numero_telephone          VARCHAR(15) NOT NULL UNIQUE,
    nom VARCHAR(50) NOT NULL,
    role TEXT NOT NULL DEFAULT 'CLIENT' CHECK (role IN ('CLIENT', 'ADMIN')),
    prefixe_id                INT NOT NULL,
    date_premiere_connexion   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prefixe_id) REFERENCES prefixes(id)
);


CREATE TABLE comptes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    client_id       INT NOT NULL UNIQUE,
    solde           DECIMAL(15,2) DEFAULT 0,
    date_creation   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);


CREATE TABLE operations (
    id                      INT AUTO_INCREMENT PRIMARY KEY,
    reference               VARCHAR(30) NOT NULL UNIQUE,
    type_operation_id       INT NOT NULL,
    compte_source_id        INT NULL,
    compte_destination_id   INT NULL,
    montant                 DECIMAL(15,2) NOT NULL,
    frais                   DECIMAL(15,2) DEFAULT 0,
    montant_total           DECIMAL(15,2) NOT NULL,   -- montant +/- frais selon le cas
    bareme_frais_id         INT NULL,                 -- traçabilité du barème appliqué
    statut                  INT NOT NULL DEFAULT 1,   -- Référence vers la table statut
    date_operation          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id),
    FOREIGN KEY (compte_source_id) REFERENCES comptes(id),
    FOREIGN KEY (compte_destination_id) REFERENCES comptes(id),
    FOREIGN KEY (bareme_frais_id) REFERENCES baremes_frais(id),
    FOREIGN KEY (statut) REFERENCES statut(id)
);

CREATE TABLE statut (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);





