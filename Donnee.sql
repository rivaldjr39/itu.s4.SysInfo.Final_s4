-- ============================================================
-- DONNÉES DE TEST - SYSTÈME DE BARÈMES PAR OPÉRÉRATEUR
-- ============================================================

-- ============================================================
-- 1. OPÉRATEURS
-- ============================================================

-- MVola est NOTRE opérateur (notre_operateur = 1)
INSERT INTO operateurs (id, nom, notre_operateur) VALUES (1, 'MVola', 1);

-- Les autres opérateurs (notre_operateur = 0)
INSERT INTO operateurs (id, nom, notre_operateur) VALUES (2, 'Orange Money', 0);
INSERT INTO operateurs (id, nom, notre_operateur) VALUES (3, 'Airtel Money', 0);



-- MVola (opérateur 1) - NOTRE opérateur
INSERT INTO prefixes (id, prefixe, id_operateur) VALUES (1, '038', 1);
INSERT INTO prefixes (id, prefixe, id_operateur) VALUES (2, '039', 1);

-- Orange Money (opérateur 2)
INSERT INTO prefixes (id, prefixe, id_operateur) VALUES (3, '032', 2);
INSERT INTO prefixes (id, prefixe, id_operateur) VALUES (4, '033', 2);
INSERT INTO prefixes (id, prefixe, id_operateur) VALUES (5, '034', 2);

-- Airtel Money (opérateur 3)
INSERT INTO prefixes (id, prefixe, id_operateur) VALUES (6, '031', 3);
INSERT INTO prefixes (id, prefixe, id_operateur) VALUES (7, '037', 3);

-- ============================================================
-- 3. TYPES D'OPÉRATIONS
-- ============================================================

INSERT INTO types_operations (id, code, libelle) VALUES (1, 'DEPOT', 'Dépôt');
INSERT INTO types_operations (id, code, libelle) VALUES (2, 'RETRAIT', 'Retrait');
INSERT INTO types_operations (id, code, libelle) VALUES (3, 'TRANSFERT', 'Transfert');

-- ============================================================
-- 4. STATUTS
-- ============================================================
INSERT INTO statut(libelle) VALUES
('REUSSI'),
('ECHEC');

-- ============================================================
-- 5. CLIENTS
-- ============================================================

-- Clients MVola (NOTRE opérateur - peuvent utiliser le système)
INSERT INTO client (id, numero_telephone, nom, role, prefixe_id) 
VALUES (1, '0381234567', 'Jean Rakoto', 'CLIENT', 1);

INSERT INTO client (id, numero_telephone, nom, role, prefixe_id) 
VALUES (2, '0399876543', 'Marie Andria', 'CLIENT', 2);

INSERT INTO client (id, numero_telephone, nom, role, prefixe_id) 
VALUES (5, '0380000001', 'Admin System', 'ADMIN', 1);

-- Clients d'autres opérateurs (peuvent se connecter mais pas de frais de barème)
-- Client Orange Money
INSERT INTO client (id, numero_telephone, nom, role, prefixe_id) 
VALUES (3, '0327654321', 'Pierre Ben', 'CLIENT', 3);

-- Client Airtel Money
INSERT INTO client (id, numero_telephone, nom, role, prefixe_id) 
VALUES (4, '0312345678', 'Sophie Lal', 'CLIENT', 6);

-- ============================================================
-- 6. COMPTES
-- ============================================================

INSERT INTO comptes (id, client_id, solde) VALUES (1, 1, 500000);
INSERT INTO comptes (id, client_id, solde) VALUES (2, 2, 250000);
INSERT INTO comptes (id, client_id, solde) VALUES (3, 3, 100000);
INSERT INTO comptes (id, client_id, solde) VALUES (4, 4, 75000);
INSERT INTO comptes (id, client_id, solde) VALUES (5, 5, 500000);


INSERT INTO baremes_frais (id, type_operation_id, operateur_id, montant_min, montant_max, frais_fixe, frais_pourcentage) 
VALUES (1, 3, 1, 1000, 10000, 500, 1.5);

INSERT INTO baremes_frais (id, type_operation_id, operateur_id, montant_min, montant_max, frais_fixe, frais_pourcentage) 
VALUES (2, 3, 1, 10001, 100000, 1000, 2.0);

INSERT INTO baremes_frais (id, type_operation_id, operateur_id, montant_min, montant_max, frais_fixe, frais_pourcentage) 
VALUES (3, 3, 1, 100001, 999999999, 2000, 2.5);

-- Retraits MVola
INSERT INTO baremes_frais (id, type_operation_id, operateur_id, montant_min, montant_max, frais_fixe, frais_pourcentage) 
VALUES (4, 2, 1, 1000, 20000, 300, 1.0);

INSERT INTO baremes_frais (id, type_operation_id, operateur_id, montant_min, montant_max, frais_fixe, frais_pourcentage) 
VALUES (5, 2, 1, 20001, 100000, 800, 1.5);

INSERT INTO baremes_frais (id, type_operation_id, operateur_id, montant_min, montant_max, frais_fixe, frais_pourcentage) 
VALUES (6, 2, 1, 100001, 999999999, 1500, 2.0);




