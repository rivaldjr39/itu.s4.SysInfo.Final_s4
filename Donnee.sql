

-- Préfixes
INSERT INTO prefixes (prefixe, actif) VALUES
('033', 1),
('037', 1);

-- Types d'opérations
INSERT INTO types_operations (code, libelle, actif) VALUES
('DEPOT', 'Dépôt', 1),
('RETRAIT', 'Retrait', 1),
('TRANSFERT', 'Transfert', 1);

-- Statuts
INSERT INTO statut (libelle) VALUES
('REUSSI'),
('ECHEC');


-- DEPOT
INSERT INTO baremes_frais
(type_operation_id, montant_min, montant_max, frais_fixe)
VALUES
(1,0,999999999,0);

-- RETRAIT
INSERT INTO baremes_frais
(type_operation_id,montant_min,montant_max,frais_fixe)
VALUES
(2,100,1000,50),
(2,1001,5000,50),
(2,5001,10000,100),
(2,10001,25000,200),
(2,25001,50000,400),
(2,50001,100000,800),
(2,100001,250000,1500),
(2,250001,500000,1500),
(2,500001,1000000,2500),
(2,1000001,2000000,3000);

-- TRANSFERT (exemple)
INSERT INTO baremes_frais
(type_operation_id,montant_min,montant_max,frais_fixe)
VALUES
(3,0,10000,200),
(3,10001,50000,500),
(3,50001,100000,1000),
(3,100001,999999999,2000);

-- ============================================================
-- CLIENTS
-- ============================================================

INSERT INTO client
(numero_telephone,nom,role,prefixe_id)
VALUES
('0331234567','Administrateur','ADMIN',1),
('0331111111','Jean', 'CLIENT',1),
('0332222222','Paul', 'CLIENT',1),
('0373333333','Marie','CLIENT',2),
('0374444444','Luc',  'CLIENT',2);

-- ============================================================
-- COMPTES
-- ============================================================

INSERT INTO comptes (client_id,solde)
VALUES
(1,0),
(2,500000),
(3,120000),
(4,75000),
(5,20000);