INSERT INTO prefixes (prefixe) VALUES ('033'), ('037');

INSERT INTO types_operations (code, libelle) VALUES
('DEPOT', 'Dépôt'),
('RETRAIT', 'Retrait'),
('TRANSFERT', 'Transfert');

INSERT INTO statut (libelle) VALUES
('REUSSI'),
('ECHEC');

-- Barèmes de frais pour le dépôt (id type_operation = 1)
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais_fixe, frais_pourcentage) VALUES
(1, 0,      5000,     100,  0),
(1, 5001,   20000,    200,  0),
(1, 20001,  100000,   0,    1.0),
(1, 100001, 999999999, 0,   0.75);

-- Exemple de barème par tranche pour le transfert (id type_operation = 3)
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais_fixe, frais_pourcentage) VALUES
(3, 0,      5000,    200,  0),
(3, 5001,   20000,   500,  0),
(3, 20001,  100000,  0,    2.0),
(3, 100001, 999999999, 0,  1.5);


INSERT INTO client (numero_telephone, nom, Role, prefixe_id
) VALUES
('0331234567', 'Alice', 'CLIENT', 1),
('0379876543', 'Bob', 'CLIENT', 2),
('0330000001', 'Charlie', 'ADMIN', 1);

INSERT INTO comptes (client_id, solde) VALUES
(1, 100000),
(2, 50000);


/*
-- ============================================================
-- VUES UTILES POUR LE CÔTÉ OPÉRATEUR
-- ============================================================

-- Situation des gains (frais perçus sur retrait/transfert)
CREATE VIEW vue_gains_operateur AS
SELECT
    t.libelle           AS type_operation,
    DATE(o.date_operation) AS jour,
    SUM(o.frais)         AS total_frais,
    COUNT(*)             AS nombre_operations
FROM operations o
JOIN types_operations t ON t.id = o.type_operation_id
WHERE o.statut = 'REUSSI' AND t.code IN ('RETRAIT', 'TRANSFERT')
GROUP BY t.libelle, DATE(o.date_operation);


*/
-- Situation des comptes clients
CREATE VIEW vue_situation_comptes AS
SELECT
    cl.numero_telephone,
    c.solde,
    c.date_creation,
    (SELECT COUNT(*) FROM operations o
     WHERE o.compte_source_id = c.id OR o.compte_destination_id = c.id) AS nombre_operations
FROM comptes c
JOIN client cl ON cl.id = c.client_id;
